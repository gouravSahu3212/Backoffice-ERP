@extends('layouts.dashboard')

@section('page-title', 'Hotel Bookings')

@section('content')
<div x-data="agentHotelBookings()">

    {{-- Notification Toast --}}
    <div 
        x-show="toastMessage" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-4 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-4 opacity-0 scale-95"
        class="fixed bottom-5 right-5 z-[9999] max-w-md bg-emerald-600 text-white px-4 py-3 rounded-xl shadow-2xl text-sm font-medium flex items-center justify-between gap-3 border border-emerald-500/30"
        style="display: none;"
    >
        <div class="flex items-center gap-2.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-text="toastMessage"></span>
        </div>
        <button type="button" @click="toastMessage = ''" class="text-emerald-200 hover:text-white p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hotel Bookings</h1>
            <p class="text-sm text-gray-500 mt-1">Track and manage room bookings you have submitted.</p>
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
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            {{-- Status --}}
                            <td class="px-4 py-3 text-xs">
                                @php
                                    $statusClasses = match ($booking->status) {
                                        'confirmed' => 'bg-slate-900 text-white border-slate-900 font-bold',
                                        'payment_received' => 'bg-emerald-600 text-white border-emerald-600 font-bold',
                                        'rejected', 'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusClasses }}">
                                    {{ str_replace('_', ' ', ucfirst($booking->status)) }}
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

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap text-xs">
                                @if($booking->status === 'confirmed')
                                    <div class="inline-flex items-center gap-1.5 text-xs text-amber-700 font-medium bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/70 cursor-help" title="To cancel a confirmed booking, please contact admin support.">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Contact Admin to cancel</span>
                                    </div>
                                @elseif($booking->status !== 'cancelled' && $booking->status !== 'rejected')
                                    <button 
                                        type="button" 
                                        @click="openCancelModal({{ $booking->id }}, '{{ $booking->booking_reference }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-lg transition-colors shadow-2xs"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancel Booking
                                    </button>
                                @else
                                    <span class="text-gray-400 font-medium text-[11px]">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-16 text-center text-xs text-gray-400 italic">
                                No hotel bookings found yet. Go to 
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

    {{-- AGENT CANCELLATION MODAL --}}
    <div 
        x-show="cancelModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="min-h-screen px-4 flex items-center justify-center text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="cancelModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 border border-gray-100"
                @click.away="cancelModalOpen = false"
            >
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-xl font-bold text-gray-900">Cancel Booking</h3>
                    <button type="button" @click="cancelModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    Are you sure you want to cancel booking <strong class="text-gray-900" x-text="targetBookingRef"></strong>? 
                    This action will cancel the booking request and release the reserved room availability.
                </p>

                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="cancelModalOpen = false" 
                        class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm transition-colors"
                    >
                        No, Keep Booking
                    </button>
                    <button 
                        type="button" 
                        @click="confirmCancelBooking()" 
                        :disabled="submitting"
                        class="px-5 py-2.5 font-semibold rounded-xl text-sm shadow-sm transition-colors text-white bg-rose-600 hover:bg-rose-700 disabled:opacity-50"
                    >
                        <span x-show="!submitting">Yes, Cancel Booking</span>
                        <span x-show="submitting" style="display: none;">Cancelling…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function agentHotelBookings() {
    return {
        cancelModalOpen: false,
        targetBookingId: null,
        targetBookingRef: '',
        submitting: false,
        toastMessage: '',

        openCancelModal(id, reference) {
            this.targetBookingId = id;
            this.targetBookingRef = reference;
            this.cancelModalOpen = true;
        },

        async confirmCancelBooking() {
            if (!this.targetBookingId || this.submitting) return;

            this.submitting = true;
            try {
                const res = await fetch(`/agent/hotel-bookings/${this.targetBookingId}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.cancelModalOpen = false;
                    this.toastMessage = data.message || 'Booking cancelled successfully.';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert(data.message || 'Failed to cancel booking.');
                }
            } catch (err) {
                console.error('Cancellation error', err);
                alert('An error occurred while cancelling booking. Please try again.');
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endpush
@endsection
