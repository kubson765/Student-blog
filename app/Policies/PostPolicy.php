<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Widok listy – wszyscy
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Widok pojedynczego posta
     */
    public function view(?User $user, Post $post): bool
    {
        // Zawsze widoczne, jeśli approved i opublikowane
        if ($post->isVisible() && $post->status === 'published') {
            return true;
        }

        // Właściciel widzi swoje
        if ($user && $user->id === $post->user_id) {
            return true;
        }

        // Moderator widzi WSZYSTKO (do moderacji)
        if ($user && $user->isModerator()) {
            return true;
        }

        return false;
    }

    /**
     * Tworzenie posta
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail()
            && !$user->isBanned();
    }

    /**
     * Edycja posta – TYLKO autor
     * Moderator NIE MOŻE edytować!
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id
            && !$user->isBanned();
    }

    /**
     * Usuwanie posta – TYLKO autor (lub admin)
     * Moderator NIE MOŻE usuwać!
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $post->user_id;
    }

// ============================================
// AKCJE MODERACYJNE – TYLKO MODERATOR I ADMIN
// ============================================

    /**
     * Czy może moderować (widzieć kolejkę)?
     */
    public function moderate(User $user): bool
    {
        return $user->isModerator() && !$user->isBanned();
    }

    /**
     * Czy może zaakceptować post?
     */
    public function approve(User $user, Post $post): bool
    {
        // Moderator/admin, nie autor (nie może zatwierdzić własnego posta!)
        if (!$user->isModerator()) {
            return false;
        }

        // Zasada four-eyes: nie możesz moderować własnych treści
        if ($post->user_id === $user->id) {
            return false;
        }

        // Można zatwierdzić tylko posty oczekujące
        return in_array($post->moderation_status, ['pending', 'rejected']);
    }

    /**
     * Czy może odrzucić post?
     */
    public function reject(User $user, Post $post): bool
    {
        if (!$user->isModerator()) {
            return false;
        }

        if ($post->user_id === $user->id) {
            return false;
        }

        return $post->moderation_status === 'pending';
    }

    /**
     * Czy może oznaczyć jako spam?
     */
    public function markAsSpam(User $user, Post $post): bool
    {
        return $this->reject($user, $post);
    }

    /**
     * Czy może eskalować do admina?
     */
    public function escalate(User $user, Post $post): bool
    {
        return $user->isModerator() && $post->user_id !== $user->id;
    }

    /**
     * Czy może trwale usunąć (ukryć) post?
     * TYLKO ADMIN – moderator nie ma tego uprawnienia!
     */
    public function forceHide(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
