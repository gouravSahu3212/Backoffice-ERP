<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRoomSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'capacity',
        'available_qty',
        'price_per_night',
        'currency',
        'month',
        'season_type',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'available_qty' => 'integer',
            'price_per_night' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
