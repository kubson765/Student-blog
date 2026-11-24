<?php

namespace App\Models;

use Database\Factories\FilterPresetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilterPreset extends Model
{
    /** @use HasFactory<FilterPresetFactory> */
    use HasFactory;

    /**
     * Query parameters that a saved preset may contain.
     *
     * @var list<string>
     */
    public const FILTER_KEYS = ['search', 'category', 'tags', 'author', 'status', 'sort'];

    protected $fillable = ['user_id', 'name', 'filters'];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
