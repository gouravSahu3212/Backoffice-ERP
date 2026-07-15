@extends('layouts.dashboard')

@section('page-title', 'Tour Booking Details')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
            <p class="text-sm text-gray-500 mt-1">Read-only view for admin users.</p>
        </div>
        <a href="{{ route('admin.tours.bookings.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            Back to bookings
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Booking reference</p>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $booking->booking_reference }}</h2>
                    </div>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize
                        {{ $booking->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700' : ($booking->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                        {{ $booking->status }}
                    </span>
                </div>
            </div>
            <div class="p-6 grid gap-5 md:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Guest</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $booking->guest_name }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->guest_email }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->guest_phone }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Vehicle</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $booking->vehicle_type }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->passengers }} passenger(s)</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Pickup</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $booking->pickup_location }}</p>
                    <p class="text-sm text-gray-600">{{ optional($booking->tour_date)->format('d M Y') ?? 'TBD' }} at {{ $booking->pickup_time }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dropoff</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $booking->dropoff_location }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
            <p class="mt-3 text-sm text-gray-600 whitespace-pre-line">
                {{ $booking->notes ?: 'No notes were provided for this booking.' }}
            </p>
        </div>
    </div>
@endsection
