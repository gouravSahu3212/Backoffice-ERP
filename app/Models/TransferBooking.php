<?php

namespace App\Models;

use Database\Factories\TransferBookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferBooking extends Model
{
    /** @use HasFactory<TransferBookingFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'guest_name',
        'guest_email',
        'guest_phone',
        'pickup_location',
        'dropoff_location',
        'vehicle_type',
        'transfer_date',
        'pickup_time',
        'passengers',
        'status',
        'notes',
        'total_amount',
        'currency',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'total_amount' => 'decimal:2',
        'passengers' => 'integer',
    ];
}
