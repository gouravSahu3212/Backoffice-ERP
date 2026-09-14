@extends('layouts.dashboard')

@section('page-title', 'Hotel Bookings')

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hotel Bookings</h1>
            <p class="text-sm text-gray-500 mt-1">Track and manage room bookings you have confirmed.</p>
        </div>

        <form method="GET" action="{{ route('agent.hotel-bookings.index') }}" class="w-full lg:w-80">
            <label for="booking-search" class="sr-only">Search bookings</label>
            <input id="booking-search" name="search" value="{{ $search ?? '' }}" type="search"
                placeholder="Search reference, guest, or hotel…"
                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Booking Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Hotel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Room Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Check-in / Out</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rooms & Guests</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            {{-- Status --}}
                            <td class="px-4 py-3 text-xs">
                                @php
                                    $statusClasses = match ($booking->status) {
                                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusClasses }} capitalize">
                                    {{ $booking->status }}
                                </span>
                            </td>

                            {{-- Booking Reference --}}
                            <td class="px-4 py-3 text-xs font-semibold text-gray-900 whitespace-nowrap">
                                {{ $booking->booking_reference }}
                            </td>

                            {{-- Hotel & Location --}}
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <div class="font-bold text-gray-900">{{ $booking->hotel?->name ?? '—' }}</div>
                                <div class="text-[11px] text-gray-400 font-medium">
                                    {{ $booking->hotel?->location?->name ?? '' }}
                                </div>
                            </td>

                            {{-- Room Type --}}
                            <td class="px-4 py-3 text-xs font-medium text-gray-800">
                                {{ $booking->roomSlot?->name ?? 'Standard Room' }}
                            </td>

                            {{-- Customer Information --}}
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <div class="font-semibold text-gray-900">{{ $booking->customer_name }}</div>
                                <div class="text-[11px] text-gray-400">{{ $booking->customer_email }}</div>
                                @if($booking->customer_phone)
                                    <div class="text-[11px] text-gray-400">{{ $booking->customer_phone }}</div>
                                @endif
                            </td>

                            {{-- Check-in / Check-out --}}
                            <td class="px-4 py-3 text-xs text-gray-700 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $booking->check_in?->format('M d, Y') ?? '—' }} → {{ $booking->check_out?->format('M d, Y') ?? '—' }}
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    @php
                                        $nights = ($booking->check_in && $booking->check_out) 
                                            ? max(1, $booking->check_in->diffInDays($booking->check_out)) 
                                            : 1;
                                    @endphp
                                    {{ $nights }} night{{ $nights > 1 ? 's' : '' }}
                                </div>
                            </td>

                            {{-- Rooms & Guests --}}
                            <td class="px-4 py-3 text-xs text-gray-700 whitespace-nowrap">
                                <span class="font-semibold">{{ $booking->rooms_count ?? 1 }} Room(s)</span>
                                <span class="text-gray-400 text-[11px]">• {{ $booking->guests ?? 1 }} Guest(s)</span>
                            </td>

                            {{-- Total Price --}}
                            <td class="px-4 py-3 text-xs font-bold text-gray-900 whitespace-nowrap">
                                {{ $booking->currency ?? 'AED' }} {{ number_format((float) $booking->total_price, 2) }}
                            </td>

                            {{-- Booking Date --}}
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                {{ $booking->created_at?->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center text-xs text-gray-400 italic">
                                No hotel bookings confirmed yet. Go to 
                                <a href="{{ route('agent.hotels.index') }}" class="text-[#0B1527] font-semibold hover:underline">Hotels</a> 
                                and click <strong>View Availability</strong> to book a room.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
