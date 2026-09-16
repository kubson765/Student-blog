<?php

namespace App\Policies;

use App\Models\User;

// app/Policies/AnonymousPostPolicy.php
class AnonymousPostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, AnonymousPost $post): bool
    {
        // Publicznie widoczne tylko approved
        if ($post->isVisible()) {
            return true;
        }

        // Moderator/admin widzi wszystko
        return $user && $user->isModerator();
    }

    public function create(?User $user): bool
    {
        // Każdy może utworzyć anonimowy post (nawet niezalogowany)
        // ale jeśli zalogowany i zbanowany – nie
        if ($user && $user->isBanned()) {
            return false;
        }

        return true;
    }

    public function update(User $user, AnonymousPost $post): bool
    {
        // Anonimowe posty nie mogą być edytowane (nie ma autora)
        return false;
    }

    public function delete(User $user, AnonymousPost $post): bool
    {
        // Tylko admin może usunąć anonimowy post
        return $user->isAdmin();
    }

    public function approve(User $user, AnonymousPost $post): bool
    {
        return $user->isModerator() && $post->moderation_status === 'pending';
    }

    public function reject(User $user, AnonymousPost $post): bool
    {
        return $user->isModerator() && $post->moderation_status === 'pending';
    }

    public function markAsSpam(User $user, AnonymousPost $post): bool
    {
        return $user->isModerator() && $post->moderation_status === 'pending';
    }
}
