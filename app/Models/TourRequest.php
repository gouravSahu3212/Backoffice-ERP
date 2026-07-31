<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_reference',
        'tour_id',
        'agent_id',
        'customer_name',
        'date_of_birth',
        'passport_number',
        'departure_date',
        'pax',
        'total_price',
        'currency',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'departure_date' => 'date',
            'total_price' => 'decimal:2',
            'pax' => 'integer',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
