<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirportTransferRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_id',
        'transfer_type',
        'zone_id',
        'vehicle_type_id',
        'fare_type',
        'price',
        'currency',
        'notes',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function airport(): BelongsTo
    {
        return $this->belongsTo(TransferLocation::class, 'airport_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(AirportTransferZone::class, 'zone_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
