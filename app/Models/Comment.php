<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\Moderatable;

class Comment extends Model
{
    use HasFactory, Moderatable;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'moderation_status',
        'moderation_reason',
        'moderated_at',
        'moderated_by',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

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
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Usunięty użytkownik',
        ]);
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
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    /**
     * Polimorficzne interakcje (np. polubienia komentarza).
     */
    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactable');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Tylko komentarze główne (bez parent_id)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Tylko zaakceptowane
     */
    public function scopeApproved($query)
    {
        return $query->where('moderation_status', 'approved');
    }

    /**
     * Z odpowiedziami (eager loading)
     */
    public function scopeWithReplies($query)
    {
        return $query->with(['replies.user', 'user']);
    }

    // ============================================
    // METODY POMOCNICZE
    // ============================================

    /**
     * Czy komentarz jest odpowiedzią?
     */
    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Czy komentarz może być edytowany przez użytkownika?
     * (w ciągu 15 minut od utworzenia)
     */
    public function isEditableBy(User $user): bool
    {
        return $this->user_id === $user->id
            && $this->created_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Czy komentarz jest widoczny publicznie?
     */
    public function isVisible(): bool
    {
        return $this->moderation_status === 'approved';
    }
}
