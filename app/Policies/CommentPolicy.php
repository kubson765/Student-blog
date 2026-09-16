<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Comment $comment): bool
    {
        if ($comment->isVisible()) {
            return true;
        }

        if ($user && $user->id === $comment->user_id) {
            return true;
        }

        // Moderator sees all
        if ($user && $user->isModerator()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !$user->isBanned();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $comment->isEditableBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $comment->user_id;
    }

    // ===== MODERATION =====

    public function moderate(User $user): bool
    {
        return $user->isModerator();
    }

    public function approve(User $user, Comment $comment): bool
    {
        if (!$user->isModerator()) {
            return false;
        }

        // Four-eyes: nie moderuj własnych komentarzy
        if ($comment->user_id === $user->id) {
            return false;
        }

        return in_array($comment->moderation_status, ['pending', 'rejected']);
    }

    public function reject(User $user, Comment $comment): bool
    {
        if (!$user->isModerator()) {
            return false;
        }

        if ($comment->user_id === $user->id) {
            return false;
        }

        return $comment->moderation_status === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }
}
