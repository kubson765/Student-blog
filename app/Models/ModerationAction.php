<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'moderator_id',
        'moderatable_type',
        'moderatable_id',
        'action',
        'reason',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function moderatable()
    {
        return $this->morphTo();
    }
}
