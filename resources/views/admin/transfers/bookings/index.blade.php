@extends('layouts.dashboard')

@section('page-title', 'Transfer Bookings')

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transfer Bookings</h1>
            <p class="text-sm text-gray-500 mt-1">Review transfer bookings created by agents. Admin users can only view and inspect them.</p>
        </div>

        <form method="GET" action="{{ route('admin.transfers.bookings.index') }}" class="w-full lg:w-80">
            <label for="booking-search" class="sr-only">Search bookings</label>
            <input id="booking-search" name="search" value="{{ $search ?? '' }}" type="search"
                placeholder="Search reference, guest, or status"
                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Reference</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Guest</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Route</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Transfer Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $booking->booking_reference }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <div class="font-medium">{{ $booking->guest_name }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->guest_email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <div>{{ $booking->pickup_location }}</div>
                            <div class="text-xs text-gray-500">to {{ $booking->dropoff_location }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($booking->transfer_date)->format('d M Y') ?? 'TBD' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize
                                {{ $booking->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700' : ($booking->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->currency }} {{ number_format((float) $booking->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.transfers.bookings.show', $booking) }}" class="font-medium text-gray-900 hover:text-gray-600">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center text-sm text-gray-500">
                            No transfer bookings found yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($bookings->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
