<?php

namespace App\Policies;

use App\Models\ModerationAction;
use App\Models\User;

class ModerationActionPolicy
{
    public function viewAny(User $user): bool
    {
        return ($user->isAdmin() || $user->isModerator());
    }

    public function view(User $user, ModerationAction $action): bool
    {
        return ($user->isAdmin() || $user->isModerator());
    }
}
