<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\AnonymousPost;
use App\Models\FilterPreset;
use App\Models\Interaction;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PostController extends Controller
{
    protected array $categories = [
        'Życie studenckie',
        'Wykłady i ćwiczenia',
        'Egzaminy i sesja',
        'Praktyki i praca',
        'Stypendia i pomoc',
        'Kariera po studiach',
        'Kampus i wydarzenia',
        'Recenzje przedmiotów',
        'Poradniki',
        'Ogólne',
    ];

    /**
     * Wyświetl listę postów (publiczna lista + własne szkice)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ===== Zwyczajne posty =====
        $postsQuery = Post::with(['user', 'tags']);

        if ($user) {
            $postsQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($sub) {
                        $sub->where('moderation_status', 'approved')
                            ->where('status', Post::STATUS_PUBLISHED)
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());
                    });
            });
        } else {
            $postsQuery->where('moderation_status', 'approved')
                ->where('status', Post::STATUS_PUBLISHED)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        // ===== Anonimowe posty =====
        $anonymousQuery = AnonymousPost::query()
            ->where('moderation_status', 'approved')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // ===== SEARCH =====
        if ($search = $request->input('search')) {
            $postsQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });

            $anonymousQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        // ===== CATEGORY FILTER =====
        if ($category = $request->input('category')) {
            $postsQuery->where('category', $category);
            $anonymousQuery->where('category', $category);
        }

        // ===== TAG FILTER (AND: a post must have every selected tag) =====
        $tagSlugs = array_values(array_slice(array_unique(array_filter(
            (array) $request->input('tags', []),
            static fn (mixed $slug): bool => is_string($slug) && $slug !== '',
        )), 0, 20));

        if ($tagSlugs !== []) {
            foreach ($tagSlugs as $tagSlug) {
                $postsQuery->whereHas('tags', fn (Builder $query) => $query->where('slug', $tagSlug));
            }

            $anonymousQuery->whereRaw('1 = 0');
        }

        if ($authorID = $request->input('author')) {
            $postsQuery->where('user_id', $authorID);
            $anonymousQuery->whereRaw('1 = 0');
        }

        if ($user && $status = $request->input('status')) {
            $postsQuery->where(function ($q) use ($status, $user) {
                if ($status === 'draft') {
                    $q->where('status', 'draft')
                        ->where('user_id', $user->id);
                } else {
                    $q->where('status', $status);
                }
            });
            $anonymousQuery->whereRaw('1 = 0');
        }

        // ===== SQL sort for regular posts =====
        $sort = $request->input('sort', 'latest');

        $applySort = function ($query) use ($sort) {
            return match ($sort) {
                'oldest' => $query->orderBy('created_at', 'asc'),
                'title' => $query->orderBy('title', 'asc'),
                'popular' => $query->orderByRaw('
                (
                    SELECT COALESCE(SUM(CASE WHEN type = "upvote" THEN 1 ELSE -1 END), 0)
                    FROM interactions
                    WHERE interactions.interactable_type = ?
                      AND interactions.interactable_id = posts.id
                ) DESC
            ', [Post::class]),
                default => $query->orderByRaw('COALESCE(published_at, created_at) DESC'),
            };
        };

        $postsQuery = $applySort($postsQuery);
        // Anonymous: sort only by date, since no votes
        $anonymousQuery->orderByRaw('COALESCE(published_at, created_at) DESC');

        // ============================================
        // 6. POBRANIE + SCALENIE
        // ============================================
        $regularPosts = $postsQuery->get();
        $anonymousPosts = $anonymousQuery->get();

        $regularPosts->each(fn ($p) => $p->source = 'post');
        $anonymousPosts->each(fn ($p) => $p->source = 'anonymous');

        $allPosts = $regularPosts->concat($anonymousPosts);

        // ============================================
        // 7. PONOWNE SORTOWANIE W PHP (dla spójności)
        // ============================================
        // Bo concat() psuje porządek (najpierw regularne, potem anonimowe)
        $allPosts = match ($sort) {
            'oldest' => $allPosts->sortBy('created_at'),
            'title' => $allPosts->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE),
            'popular' => $allPosts->sortByDesc(fn ($p) => $p->vote_score ?? 0),
            default => $allPosts->sortByDesc(fn ($p) => $p->published_at ?? $p->created_at),
        };

        // ============================================
        // 8. OBLICZ SCORE dla postów (potrzebne do popular i widoku)
        // ============================================
        $postIds = $regularPosts->pluck('id')->toArray();

        $voteCounts = Interaction::where('interactable_type', Post::class)
            ->whereIn('interactable_id', $postIds)
            ->whereIn('type', ['upvote', 'downvote'])
            ->selectRaw('interactable_id, type, COUNT(*) as count')
            ->groupBy('interactable_id', 'type')
            ->get()
            ->groupBy('interactable_id');

        $userVotes = [];
        if ($user) {
            $userVotes = Interaction::where('user_id', $user->id)
                ->where('interactable_type', Post::class)
                ->whereIn('interactable_id', $postIds)
                ->whereIn('type', ['upvote', 'downvote'])
                ->pluck('type', 'interactable_id')
                ->toArray();
        }

        // Przypisz score i user_vote PRZED sortowaniem popular
        $allPosts->each(function ($post) use ($voteCounts, $userVotes) {
            if (($post->source ?? 'post') === 'post') {
                $upvotes = $voteCounts->get($post->id)?->firstWhere('type', 'upvote')?->count ?? 0;
                $downvotes = $voteCounts->get($post->id)?->firstWhere('type', 'downvote')?->count ?? 0;
                $post->vote_score = $upvotes - $downvotes;
                $post->user_vote = $userVotes[$post->id] ?? null;
            } else {
                $post->vote_score = 0;
                $post->user_vote = null;
            }
        });

        // ============================================
        // 9. PONOWNE SORTOWANIE (teraz z vote_score)
        // ============================================
        if ($sort === 'popular') {
            $allPosts = $allPosts->sortByDesc('vote_score');
        }

        // ============================================
        // 10. PAGINACJA (Collection)
        // ============================================
        $perPage = 9;
        $page = max(1, (int) $request->input('page', 1));
        $allPosts = $allPosts->values(); // reset kluczy

        $posts = new LengthAwarePaginator(
            $allPosts->forPage($page, $perPage)->values(),
            $allPosts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        // Optimalization: Count votes for all posts
        $postIds = $posts->pluck('id')->toArray();

        // Count upvotes/downvotes per post (jeden SQL)
        $voteCounts = Interaction::where('interactable_type', Post::class)
            ->whereIn('interactable_id', $postIds)
            ->whereIn('type', ['upvote', 'downvote'])
            ->selectRaw('interactable_id, type, COUNT(*) as count')
            ->groupBy('interactable_id', 'type')
            ->get()
            ->groupBy('interactable_id');

        // Users vote (jeden SQL)
        $userVotes = [];
        if ($user) {
            $userVotes = Interaction::where('user_id', $user->id)
                ->where('interactable_type', Post::class)
                ->whereIn('interactable_id', $postIds)
                ->whereIn('type', ['upvote', 'downvote'])
                ->pluck('type', 'interactable_id')
                ->toArray();
        }

        // Przypisz do każdego posta
        $posts->each(function ($post) use ($voteCounts, $userVotes) {
            $upvotes = $voteCounts->get($post->id)?->firstWhere('type', 'upvote')?->count ?? 0;
            $downvotes = $voteCounts->get($post->id)?->firstWhere('type', 'downvote')?->count ?? 0;

            $post->vote_score = $upvotes - $downvotes;
            $post->user_vote = $userVotes[$post->id] ?? null;
        });

        // ============================================
        // DANE POMOCNICZE DLA WIDOKU
        // ============================================
        $categories = $this->categories;
        $tags = Tag::withCount([
            'posts' => function (Builder $query) {
                $query->where('status', Post::STATUS_PUBLISHED)
                    ->where('moderation_status', 'approved')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            },
        ])
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->get();

        $activeFilters = array_filter(
            $request->only(FilterPreset::FILTER_KEYS),
            static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== [],
        );
        $activeFilters['tags'] = $tagSlugs;
        $filterPresets = $user?->filterPresets()->orderBy('name')->get() ?? collect();

        return view('posts.index', compact(
            'posts',
            'categories',
            'tags',
            'activeFilters',
            'filterPresets',
        ));
    }

    /**
     * Formularz tworzenia nowego posta
     */
    public function create(): View
    {
        $this->authorize('create', Post::class);

        $categories = $this->categories;
        $tags = Tag::orderBy('name')->get();

        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Zapisz nowy post
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $data['moderation_status'] = 'pending';

        // Ustaw published_at jeśli publikujemy
        if ($data['status'] === Post::STATUS_PUBLISHED) {
            // $data['published_at'] = $data['published_at'] ?? now();
        }

        try {
            $post = DB::transaction(function () use ($data) {

                $post = Post::create($data);

                // tags (if exist)
                if (! empty($data['tags'])) {
                    $post->tags()->sync($data['tags']);
                }

                return $post;
            });

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Post został utworzony pomyślnie! Wymaga teraz akceptacji przez moderację.');
        } catch (\Exception $e) {
            Log::error('Post creation failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Could not create post. Try again']);
        }
    }

    /**
     * Wyświetl pojedynczy post
     */
    public function show(Post $post, Request $request): View
    {
        $this->authorize('view', $post);

        $comments = $post->comments()
            ->root()
            ->approved()
            ->with([
                'user',
                'replies' => fn ($q) => $q->where('moderation_status', 'approved')->with('user')->latest(),
                'replies.replies' => fn ($q) => $q->where('moderation_status', 'approved')->with('user')->latest(),
            ])
            ->latest()
            ->paginate(20);

        $upvotes = Interaction::where('interactable_type', Post::class)
            ->where('interactable_id', $post->id)
            ->where('type', 'upvote')
            ->count();

        $downvotes = Interaction::where('interactable_type', Post::class)
            ->where('interactable_id', $post->id)
            ->where('type', 'downvote')
            ->count();

        $post->vote_score = $upvotes - $downvotes;

        $post->user_vote = null;
        if ($user = $request->user()) {
            $post->user_vote = Interaction::where('user_id', $user->id)
                ->where('interactable_type', Post::class)
                ->where('interactable_id', $post->id)
                ->whereIn('type', ['upvote', 'downvote'])
                ->value('type');
        }

        $post->load(['user', 'tags']);

        return view('posts.show', compact('post', 'comments'));
    }

    /**
     * Formularz edycji posta
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $categories = $this->categories;
        $tags = Tag::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Zapisz zmiany w poście
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === Post::STATUS_PUBLISHED && ! $post->published_at) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        try {
            DB::transaction(function () use ($post, $data) {
                // 1. Zaktualizuj post
                $post->update($data);

                // 2. Zaktualizuj tagi
                if (isset($data['tags'])) {
                    $post->tags()->sync($data['tags']);
                }
            });

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Post updated!');
        } catch (\Exception $e) {
            Log::error('Post update failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Could not update post']);
        }
    }

    /**
     * Usuń post
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post został usunięty.');
    }
}
