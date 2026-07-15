<?php

namespace App\Models;

use Database\Factories\TourBookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourBooking extends Model
{
    /** @use HasFactory<TourBookingFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'guest_name',
        'guest_email',
        'guest_phone',
        'pickup_location',
        'dropoff_location',
        'vehicle_type',
        'tour_date',
        'pickup_time',
        'passengers',
        'status',
        'notes',
        'total_amount',
        'currency',
    ];

    protected $casts = [
        'tour_date' => 'date',
        'total_amount' => 'decimal:2',
        'passengers' => 'integer',
    ];
}