<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['post_id', 'user_id', 'parent_id', 'content', 'is_approved'])]
class Comment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Post, do którego przypisany jest komentarz.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Autor komentarza.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Komentarz nadrzędny (w przypadku odpowiedzi).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Odpowiedzi (komentarze podrzędne).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Polimorficzne interakcje (np. polubienia komentarza).
     */
    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactable');
    }
}