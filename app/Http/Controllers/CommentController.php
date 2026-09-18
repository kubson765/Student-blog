<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Interaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Lista komentarzy dla posta (AJAX lub redirect)
     */
    public function index(Post $post, Request $request)
    {
        $user = $request->user();
        $comments = $post->comments()
            ->root()
            ->approved()
            ->with([
                'user',
                'replies' => function ($query) {
                    $query->where('moderation_status', 'approved')
                        ->with('user')
                        ->latest();
                },
                'replies.replies' => function ($query) {
                    $query->where('moderation_status', 'approved')
                        ->with('user')
                        ->latest();
                },
            ])
            ->latest()
            ->paginate(20);

        $allCommentIds = $comments->flatMap(function ($comment) {
            return collect([$comment->id])
                ->concat($comment->replies->pluck('id'))
                ->concat($comment->replies->flatMap->replies->pluck('id'));
        })->unique()->filter()->toArray();

        if (empty($allCommentIds)) {
            return view('comments.index', compact('post', 'comments'));
        }

        $voteCounts = Interaction::where('interactable_type', Comment::class)
            ->whereIn('interactable_id', $allCommentIds)
            ->whereIn('type', ['upvote', 'downvote'])
            ->selectRaw('interactable_id, type, COUNT(*) as count')
            ->groupBy('interactable_id', 'type')
            ->get()
            ->groupBy('interactable_id');

        $userVotes = [];
        if ($user) {
            $userVotes = Interaction::where('user_id', $user->id)
                ->where('interactable_type', Comment::class)
                ->whereIn('interactable_id', $allCommentIds)
                ->whereIn('type', ['upvote', 'downvote'])
                ->pluck('type', 'interactable_id')
                ->toArray();
        }

        $attachVotes = function ($comment) use (&$attachVotes, $voteCounts, $userVotes) {
            $upvotes = $voteCounts->get($comment->id)?->firstWhere('type', 'upvote')?->count ?? 0;
            $downvotes = $voteCounts->get($comment->id)?->firstWhere('type', 'downvote')?->count ?? 0;

            $comment->vote_score = $upvotes - $downvotes;
            $comment->user_vote = $userVotes[$comment->id] ?? null;
            if ($comment->relationLoaded('replies')) {
                $comment->replies->each($attachVotes);
            }
        };

        $comments->each($attachVotes);

        return view('comments.index', compact('post', 'comments'));
    }

    /**
     * Zapisz nowy komentarz
     */
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();
        $data['post_id'] = $post->id;
        $data['user_id'] = $request->user()->id;

        // Check parent
        if (!empty($data['parent_id'])) {
            $parent = Comment::find($data['parent_id']);
            if (!$parent || $parent->post_id !== $post->id) {
                return back()->withErrors([
                    'parent_id' => 'Nieprawidłowy komentarz nadrzędny.',
                ]);
            }
        }

        // Nowe komentarze domyślnie wymagają moderacji
        // (chyba że autor jest moderatorem/adminem)
        $data['moderation_status'] = $request->user()->isModerator()
            ? 'approved'
            : 'pending';

        try {
            DB::transaction(function () use ($data) {
                Comment::create($data);
            });

            $message = $data['moderation_status'] === 'approved'
                ? 'Komentarz został dodany.'
                : 'Komentarz został wysłany do moderacji.';

            return redirect()
                ->route('posts.show', $post)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Comment creation failed', [
                'post_id' => $post->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Nie udało się dodać komentarza.']);
        }
    }

    /**
     * Edytuj komentarz
     */
    public function edit(Comment $comment)
    {
        $this->authorize('update', $comment);

        return view('comments.edit', compact('comment'));
    }

    /**
     * Zaktualizuj komentarz
     */
    public function update(StoreCommentRequest $request, Comment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        try {
            $comment->update([
                'content' => $validated['content'],
                // Edytowany komentarz wraca do moderacji
                'moderation_status' => $request->user()->isModerator() ? 'approved' : 'pending',
            ]);

            return redirect()
                ->route('posts.show', $comment->post)
                ->with('success', 'Komentarz został zaktualizowany.');
        } catch (\Exception $e) {
            Log::error('Comment update failed', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => 'Nie udało się zaktualizować komentarza.']);
        }
    }

    /**
     * Usuń komentarz
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $post = $comment->post;

        try {
            DB::transaction(function () use ($comment) {
                // Usuń odpowiedzi rekurencyjnie
                $this->deleteReplies($comment);

                // Usuń interakcje
                $comment->interactions()->delete();

                // Usuń komentarz
                $comment->delete();
            });

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Komentarz został usunięty.');
        } catch (\Exception $e) {
            Log::error('Comment deletion failed', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => 'Nie udało się usunąć komentarza.']);
        }
    }

    /**
     * Recursively delete replies
     */
    private function deleteReplies(Comment $comment): void
    {
        foreach ($comment->replies as $reply) {
            $this->deleteReplies($reply);
            $reply->interactions()->delete();
            $reply->delete();
        }
    }
}
