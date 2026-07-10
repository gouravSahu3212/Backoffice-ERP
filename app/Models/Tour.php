<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'days',
        'hotel_rating',
        'currency',
        'retail_price',
        'agent_price',
        'summary',
        'description',
        'itinerary',
        'itinerary_pdf',
        'highlights',
        'whats_included',
        'image_urls',
        'departure_months',
        'max_capacity',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'whats_included' => 'array',
            'image_urls' => 'array',
            'departure_months' => 'array',
            'retail_price' => 'decimal:2',
            'agent_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to only active tours.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
