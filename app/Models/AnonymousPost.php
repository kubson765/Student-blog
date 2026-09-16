<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Moderatable;

class AnonymousPost extends Model
{
    use HasFactory, Moderatable;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'status',
        'moderation_status',
        'moderation_reason',
        'moderated_at',
        'moderated_by',
        'fingerprint',
        'ip_hash',
        'user_agent_hash',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    public function moderatedBy()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
