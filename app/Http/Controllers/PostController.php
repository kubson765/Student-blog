<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $query = Post::with(['user', 'tags']);

        // ===== VISIBILITY =====
        if ($request->user()) {
            $query->where(function ($q) use ($request) {
                $q->where('status', Post::STATUS_PUBLISHED)
                    ->orWhere('user_id', $request->user()->id);
            });
        } else {
            $query->published();
        }

        // ===== SEARCH =====
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{search}%")
                    ->orWhere('content', 'LIKE', "%{search}%");
            });
        }

        // ===== CATEGORY FILTER =====
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // ===== TAG FILTER =====
        if ($tagSlug = $request->input('tagSlug')) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        // ===== AUTHOR FILTER =====
        if ($authorID = $request->input('author')) {
            $query->where('user_id', $authorID);
        }

        // ===== STATUS FILTER (LOGGED IN) =====
        if ($request->user() && $status = $request->input('status')) {
            $query->where(function ($q) use ($status, $request) {
                if ($status === 'draft') {
                    $q->where('status', 'draft')
                        ->where('user_id', $request->user()->id);
                } else {
                    $q->where('status', $status);
                }
            });
        }

        // ===== SORTING =====
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'ASC'),
            'title' => $query->orderBy('title', 'ASC'),
            'popular' => $query->withCount('interactions')
                ->orderByDesc('interactions_count'),
            default => $query->orderByDesc('published_at')
                ->orderByDesc('created_at'),
        };

        $posts = $query->paginate(9)->withQueryString();

        // ============================================
        // DANE POMOCNICZE DLA WIDOKU
        // ============================================
        $categories = $this->categories;
        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->get();
        $activeFilters = $request->only(['search', 'category', 'tag', 'author', 'status', 'sort']);

        return view('posts.index', compact(
            'posts',
            'categories',
            'tags',
            'activeFilters'
        ));
    }

    /**
     * Formularz tworzenia nowego posta
     */
    public function create(): View
    {
        $this->authorize('create', Post::class);

        $categories = $this->categories;
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Zapisz nowy post
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // Ustaw published_at jeśli publikujemy
        if ($data['status'] === Post::STATUS_PUBLISHED) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        try {
            $post = DB::transaction(function () use ($data) {

                $post = Post::create($data);

                // tags (if exist)
                if (!empty($data['tags'])) {
                    $post->tags()->sync($data['tags']);
                }

                return $post;
            });

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Post został utworzony pomyślnie!');
        } catch (\Exception $e) {
            Log::error("Post creation failed", [
                'user_id' => $request->user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => "Could not create post. Try again"]);
        }
    }

    /**
     * Wyświetl pojedynczy post
     */
    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        $post->load(['user', 'tags', 'comments.user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Formularz edycji posta
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $categories = $this->categories;
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Zapisz zmiany w poście
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === Post::STATUS_PUBLISHED && !$post->published_at) {
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
