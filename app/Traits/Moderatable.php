<?php

namespace App\Traits;

use App\Models\ModerationAction;
use App\Models\User;

trait Moderatable
{
    /**
     * Relacja do akcji moderacji
     */
    public function moderationActions()
    {
        return $this->morphMany(ModerationAction::class, 'moderatable');
    }

    /**
     * Relacja do zgłoszeń
     */
    public function reports()
    {
        return $this->morphMany(\App\Models\Report::class, 'reportable');
    }

    /**
     * Czy jest widoczny publicznie?
     */
    public function isVisible(): bool
    {
        return $this->moderation_status === 'approved'
            && ($this->status ?? 'published') !== 'archived';
    }

    /**
     * Zapisz akcję moderacji
     */
    public function logModerationAction(
        User $moderator,
        string $action,
        string $reason,
    ): ModerationAction {
        return $this->moderationActions()->create([
            'moderator_id' => $moderator->id,
            'action' => $action,
            'reason' => $reason,
        ]);
    }
}
