<?php

namespace App\Policies;

use App\Models\FilterPreset;
use App\Models\User;

class FilterPresetPolicy
{
    /**
     * Determine whether the user can delete the filter preset.
     */
    public function delete(User $user, FilterPreset $filterPreset): bool
    {
        return $filterPreset->user_id === $user->id;
    }
}
