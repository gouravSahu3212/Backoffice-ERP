<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'related_agent_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * The user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subject (polymorphic) of the action.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The agent this activity is relevant to.
     */
    public function relatedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_agent_id');
    }

    /**
     * Scope: order by newest first.
     */
    public function scopeLatest($query, string $column = 'created_at')
    {
        return $query->orderByDesc($column);
    }
}
