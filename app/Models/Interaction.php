<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'interactable_type', 'interactable_id', 'type', 'value'])]
class Interaction extends Model
{
    use HasFactory;

    /**
     * Użytkownik, który wszedł w interakcję.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Rodzic polimorficzny (Post lub Comment).
     */
    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }
}