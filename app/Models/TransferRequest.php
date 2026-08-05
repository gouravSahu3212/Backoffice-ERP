<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_reference',
        'agent_id',
        'transfer_type_category',
        'city_rate_id',
        'airport_rate_id',
        'full_day_rate_id',
        'title',
        'route_label',
        'vehicle',
        'customer_name',
        'date_of_birth',
        'passport_number',
        'pickup_date',
        'pickup_time',
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
            'pickup_date' => 'date',
            'total_price' => 'decimal:2',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function cityRate(): BelongsTo
    {
        return $this->belongsTo(CityTransferRate::class, 'city_rate_id');
    }

    public function airportRate(): BelongsTo
    {
        return $this->belongsTo(AirportTransferRate::class, 'airport_rate_id');
    }

    public function fullDayRate(): BelongsTo
    {
        return $this->belongsTo(FullDayTransferRate::class, 'full_day_rate_id');
    }
}
