<?php

namespace App\Http\Controllers;

use App\Models\AnonymousPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'not-banned']);
        $this->middleware('can:moderate,App\Models\Post');
        $this->middleware('throttle:moderation');
    }

    /**
     * Kolejka moderacji
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 'anonymous');

        $items = match ($type) {
            'posts' => Post::where('moderation_status', 'pending')
                ->with('user')
                ->oldest()
                ->paginate(20),
            default => AnonymousPost::where('moderation_status', 'pending')
                ->oldest()
                ->paginate(20),
        };

        $stats = [
            'pending_anonymous' => AnonymousPost::where('moderation_status', 'pending')->count(),
            'pending_posts' => Post::where('moderation_status', 'pending')->count(),
            'pending_reports' => \App\Models\Report::where('status', 'pending')->count(),
        ];

        return view('moderation.index', compact('items', 'type', 'stats'));
    }

    /**
     * Szczegóły
     */
    public function show(string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);

        return view('moderation.show', compact('post', 'type'));
    }

    /**
     * Akceptacja
     */
    public function approve(Request $request, string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);
        $this->authorize('approve', $post);

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        DB::transaction(function () use ($post, $request) {
            $post->update([
                'moderation_status' => 'approved',
                'moderation_reason' => $request->reason,
                'moderated_at' => now(),
                'moderated_by' => auth()->id(),
                'published_at' => $post->published_at ?? now(),
            ]);

            $post->logModerationAction(auth()->user(), 'approve', $request->reason);
        });

        return redirect()
            ->route('moderation.index', ['type' => $type])
            ->with('success', 'Post zaakceptowany.');
    }

    /**
     * Odrzucenie
     */
    public function reject(Request $request, string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);
        $this->authorize('reject', $post);

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        DB::transaction(function () use ($post, $request) {
            $post->update([
                'moderation_status' => 'rejected',
                'moderation_reason' => $request->reason,
                'moderated_at' => now(),
                'moderated_by' => auth()->id(),
            ]);

            $post->logModerationAction(auth()->user(), 'reject', $request->reason);
        });

        return redirect()
            ->route('moderation.index', ['type' => $type])
            ->with('success', 'Post odrzucony.');
    }

    private function resolvePost(string $type, int $id)
    {
        return match ($type) {
            'anonymous' => AnonymousPost::findOrFail($id),
            'posts' => Post::findOrFail($id),
            default => abort(404),
        };
    }

    public function history(string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);

        $actions = $post->moderationActions()
            ->with('moderator')
            ->latest()
            ->get();

        return view('moderation.history', compact('post', 'actions', 'type'));
    }
}
