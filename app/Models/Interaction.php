<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interactable_type',
        'interactable_id',
        'type',
        'value',
    ];

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

    // ============================================
    // SCOPES
    // ============================================

    public function scopeUpvotes($query)
    {
        return $query->where('type', 'upvote');
    }

    public function scopeDownvotes($query)
    {
        return $query->where('type', 'downvote');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
