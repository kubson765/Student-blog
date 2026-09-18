<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Interaction;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'not-banned']);
    }

    /**
     * Post or comment Upvote
     */
    public function upvote(Request $request, string $type, int $id): RedirectResponse
    {
        return $this->vote($request, $type, $id, 'upvote');
    }

    /**
     * Post or comment Downvote
     */
    public function downvote(Request $request, string $type, int $id): RedirectResponse
    {
        return $this->vote($request, $type, $id, 'downvote');
    }

    /**
     * Voting logic (toggle)
     */
    private function vote(Request $request, string $type, int $id, string $voteType): RedirectResponse
    {
        $interactable = $this->resolveInteractable($type, $id);

        // Can't vote for your own content
        if ($this->isOwnContent($interactable, $request->user())) {
            return back()->with('info', 'Nie możesz głosować na własne treści.');
        }

        try {
            DB::transaction(function () use ($interactable, $request, $voteType) {
                $userId = $request->user()->id;

                // Check if there's a vote
                $existing = Interaction::where('user_id', $userId)
                    ->where('interactable_type', get_class($interactable))
                    ->where('interactable_id', $interactable->id)
                    ->whereIn('type', ['upvote', 'downvote'])
                    ->first();

                // If exist and is the same type -> delete (toggle)
                if ($existing && $existing->type === $voteType) {
                    $existing->delete();
                    return;
                }

                if ($existing) {
                    $existing->update(['type' => $voteType]);
                    return;
                }

                // None -> create
                Interaction::create([
                    'user_id' => $userId,
                    'interactable_type' => get_class($interactable),
                    'interactable_id' => $interactable->id,
                    'type' => $voteType,
                    'value' => $voteType === 'upvote' ? 1 : -1,
                ]);
            });

            return back();
        } catch (\Exception $e) {
            Log::error('Vote failed', [
                'type' => $type,
                'id' => $id,
                'vote_type' => $voteType,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => 'Nie udało się zapisać głosu.']);
        }
    }

    /**
     * Resolve model by type
     */
    private function resolveInteractable(string $type, int $id)
    {
        return match ($type) {
            'post' => Post::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            default => abort(404),
        };
    }

    /**
     * Does the content belong to user?
     */
    private function isOwnContent($interactable, $user): bool
    {
        // AnonymousPost nie ma user_id, ale to nie dotyczy tego kontrolera
        return isset($interactable->user_id) && $interactable->user_id === $user->id;
    }
}
