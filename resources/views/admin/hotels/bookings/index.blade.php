@extends('layouts.dashboard')

@section('page-title', 'Hotel Bookings')

@section('content')

<div x-data="adminHotelBookings()" x-init="init()">

    {{-- Top Section Title & Search --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hotel Bookings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and update room booking requests submitted by agents.</p>
        </div>

        {{-- Search & Filters --}}
        <form method="GET" action="{{ route('admin.hotels.bookings.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input 
                    type="search" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Search by ref, customer, hotel..." 
                    class="w-64 bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900"
                />
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg pl-3.5 pr-8 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 cursor-pointer">
                <option value="">All Statuses</option>
                <option value="new" {{ ($status ?? '') === 'new' ? 'selected' : '' }}>New</option>
                <option value="confirmed" {{ ($status ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="rejected" {{ ($status ?? '') === 'rejected' || ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
    </div>

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

    {{-- Bookings Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-4">ID</th>
                        <th class="px-5 py-4">Customer</th>
                        <th class="px-5 py-4">Hotel</th>
                        <th class="px-5 py-4">Dates</th>
                        <th class="px-5 py-4">Amount</th>
                        <th class="px-5 py-4">Payment</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bookings as $b)
                        @php
                            $checkInDate = \Carbon\Carbon::parse($b->check_in);
                            $checkOutDate = \Carbon\Carbon::parse($b->check_out);
                            $nightsCount = max(1, $checkInDate->diffInDays($checkOutDate));
                            $isPaid = $b->status === 'confirmed';
                            $displayStatus = ($b->status === 'cancelled') ? 'rejected' : $b->status;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Booking Ref --}}
                            <td class="px-5 py-4 font-bold text-gray-900 whitespace-nowrap">
                                {{ $b->booking_reference }}
                            </td>

                            {{-- Customer --}}
                            <td class="px-5 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $b->customer_name }}
                            </td>

                            {{-- Hotel --}}
                            <td class="px-5 py-4 text-gray-800 whitespace-nowrap">
                                {{ $b->hotel->name ?? 'N/A' }}
                            </td>

                            {{-- Dates --}}
                            <td class="px-5 py-4 text-xs font-mono text-gray-600 whitespace-nowrap">
                                {{ $b->check_in?->format('Y-m-d') }} &rarr; {{ $b->check_out?->format('Y-m-d') }}
                            </td>

                            {{-- Amount --}}
                            <td class="px-5 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ number_format($b->total_price, 0) }} {{ $b->currency ?? 'AED' }}
                            </td>

                            {{-- Payment Badge --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($isPaid)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#0F172A] text-white">
                                        paid
                                    </span>
                                @else
                                    <span class="text-gray-400 font-semibold font-mono text-sm ml-2">-</span>
                                @endif
                            </td>

                            {{-- Editable Status Dropdown --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <select 
                                    @change="onStatusSelectChange({{ $b->id }}, '{{ $b->booking_reference }}', '{{ $displayStatus }}', $event)" 
                                    class="text-xs font-bold rounded-full pl-3.5 pr-7 py-1 border-0 focus:ring-2 focus:ring-gray-900 cursor-pointer shadow-xs transition-all"
                                    :style="{
                                        width: '{{ $displayStatus }}' === 'new' ? '70px' : ('{{ $displayStatus }}' === 'confirmed' ? '105px' : '90px')
                                    }"
                                    :class="{
                                        'bg-amber-100 text-amber-800': '{{ $displayStatus }}' === 'new',
                                        'bg-[#0F172A] text-white': '{{ $displayStatus }}' === 'confirmed',
                                        'bg-rose-100 text-rose-700': '{{ $displayStatus }}' === 'rejected' || '{{ $displayStatus }}' === 'cancelled'
                                    }"
                                >
                                    <option value="new" {{ $displayStatus === 'new' ? 'selected' : '' }}>new</option>
                                    <option value="confirmed" {{ $displayStatus === 'confirmed' ? 'selected' : '' }}>confirmed</option>
                                    <option value="rejected" {{ $displayStatus === 'rejected' || $displayStatus === 'cancelled' ? 'selected' : '' }}>rejected</option>
                                </select>
                            </td>

                            {{-- Action Icons --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Eye Icon (Details Modal) --}}
                                    <button 
                                        type="button" 
                                        @click="openDetailsModal({
                                            id: {{ $b->id }},
                                            reference: '{{ $b->booking_reference }}',
                                            customer: '{{ addslashes($b->customer_name) }}',
                                            email: '{{ addslashes($b->customer_email) }}',
                                            phone: '{{ addslashes($b->customer_phone ?? '+971551234567') }}',
                                            hotel: '{{ addslashes($b->hotel->name ?? 'N/A') }}',
                                            room: '{{ addslashes($b->roomSlot->room_type_name ?? 'Room') }}',
                                            check_in: '{{ $b->check_in?->format('Y-m-d') }}',
                                            check_out: '{{ $b->check_out?->format('Y-m-d') }}',
                                            nights: {{ $nightsCount }},
                                            total: '{{ number_format($b->total_price, 0) }} {{ $b->currency ?? 'AED' }}',
                                            status: '{{ $displayStatus }}',
                                            notes: '{{ addslashes($b->special_requests ?? 'Late check-in') }}',
                                            created_at: '{{ $b->created_at?->format('Y-m-d') }}',
                                            payment: '{{ $isPaid ? 'card (paid)' : '-' }}'
                                        })"
                                        class="p-1.5 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors"
                                        title="View Details"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    {{-- Reject Icon --}}
                                    @if($displayStatus !== 'rejected' && $displayStatus !== 'cancelled')
                                        <button 
                                            type="button" 
                                            @click="openCancelModal({{ $b->id }}, '{{ $b->booking_reference }}', '{{ $displayStatus }}', 'rejected')"
                                            class="p-1.5 text-gray-500 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors"
                                            title="Reject Booking"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Print / PDF Icon --}}
                                    <a 
                                        href="{{ route('admin.hotels.bookings.print', $b) }}" 
                                        target="_blank"
                                        class="p-1.5 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors"
                                        title="Print / Export PDF"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-500">
                                No hotel bookings found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($bookings->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    {{-- BOOKING DETAILS MODAL --}}
    <div 
        x-show="modalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="min-h-screen px-4 flex items-center justify-center text-center sm:block sm:p-0">
            {{-- Backdrop --}}
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="modalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Box --}}
            <div 
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100"
                @click.away="modalOpen = false"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900">
                        Booking Details – <span x-text="activeBooking.reference"></span>
                    </h2>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body Grid --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        
                        {{-- Left Column --}}
                        <div class="space-y-4">
                            <div>
                                <span class="text-gray-500 font-medium">Customer:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.customer"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Email:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.email"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Room:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.room"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Check-out:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.check_out"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Total:</span>
                                <span class="text-gray-900 font-semibold ml-1.5" x-text="activeBooking.total"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Status:</span>
                                <span class="text-gray-900 font-semibold ml-1.5" x-text="activeBooking.status"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Notes:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.notes"></span>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-4">
                            <div>
                                <span class="text-gray-500 font-medium">Phone:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.phone"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Hotel:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.hotel"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Check-in:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.check_in"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Nights:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.nights"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Payment:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.payment"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Created:</span>
                                <span class="text-gray-900 font-medium ml-1.5" x-text="activeBooking.created_at"></span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                    <a 
                        :href="`/admin/hotels/bookings/${activeBooking.id}/print`" 
                        target="_blank"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg inline-flex items-center gap-1.5 shadow-xs transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print PDF
                    </a>
                    <button 
                        type="button" 
                        @click="modalOpen = false" 
                        class="px-5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-100 rounded-lg shadow-xs transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATUS CHANGE CONFIRMATION MODAL --}}
    <div 
        x-show="confirmModalOpen" 
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
            {{-- Backdrop --}}
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="cancelStatusChange()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Box --}}
            <div 
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 border border-gray-100"
                @click.away="cancelStatusChange()"
            >
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-xl font-bold text-gray-900" x-text="(statusChangeData.newStatus === 'rejected' || statusChangeData.newStatus === 'cancelled') ? 'Reject Booking' : (statusChangeData.newStatus === 'confirmed' ? 'Confirm Booking' : 'Update Booking Status')"></h3>
                    <button type="button" @click="cancelStatusChange()" class="text-gray-400 hover:text-gray-600 p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <p class="text-gray-500 text-sm leading-relaxed mb-6">
                    <template x-if="statusChangeData.newStatus === 'rejected' || statusChangeData.newStatus === 'cancelled'">
                        <span>Are you sure you want to reject booking <strong class="text-gray-800" x-text="statusChangeData.reference"></strong>? This will release the booked rooms back to availability.</span>
                    </template>
                    <template x-if="statusChangeData.newStatus === 'confirmed'">
                        <span>Are you sure you want to confirm booking <strong class="text-gray-800" x-text="statusChangeData.reference"></strong>? This will mark payment as paid.</span>
                    </template>
                    <template x-if="statusChangeData.newStatus === 'new'">
                        <span>Are you sure you want to change the status of booking <strong class="text-gray-800" x-text="statusChangeData.reference"></strong> to "new"?</span>
                    </template>
                </p>

                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="cancelStatusChange()" 
                        class="px-5 py-2.5 bg-white border-2 border-gray-900 text-gray-900 font-semibold rounded-xl hover:bg-gray-50 text-sm transition-colors"
                    >
                        No, Keep
                    </button>
                    <button 
                        type="button" 
                        @click="confirmStatusChange()" 
                        class="px-5 py-2.5 font-semibold rounded-xl text-sm shadow-sm transition-colors text-white"
                        :class="{
                            'bg-[#EF4444] hover:bg-red-600': statusChangeData.newStatus === 'rejected' || statusChangeData.newStatus === 'cancelled',
                            'bg-[#0F172A] hover:bg-slate-800': statusChangeData.newStatus === 'confirmed',
                            'bg-amber-600 hover:bg-amber-700': statusChangeData.newStatus === 'new'
                        }"
                        x-text="(statusChangeData.newStatus === 'rejected' || statusChangeData.newStatus === 'cancelled') ? 'Yes, Reject' : (statusChangeData.newStatus === 'confirmed' ? 'Yes, Confirm' : 'Yes, Update')"
                    >
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function adminHotelBookings() {
    return {
        modalOpen: false,
        confirmModalOpen: false,
        statusChangeData: {
            bookingId: null,
            reference: '',
            oldStatus: '',
            newStatus: '',
            selectElement: null
        },
        activeBooking: {},
        toastMessage: '',

        init() {},

        openDetailsModal(booking) {
            this.activeBooking = booking;
            this.modalOpen = true;
        },

        openCancelModal(id, reference, currentStatus = 'new', targetStatus = 'rejected') {
            this.statusChangeData = {
                bookingId: id,
                reference: reference,
                oldStatus: currentStatus,
                newStatus: targetStatus,
                selectElement: null
            };
            this.confirmModalOpen = true;
        },

        onStatusSelectChange(bookingId, reference, currentStatus, event) {
            const newStatus = event.target.value;
            if (newStatus === currentStatus) return;

            this.statusChangeData = {
                bookingId: bookingId,
                reference: reference,
                oldStatus: currentStatus,
                newStatus: newStatus,
                selectElement: event.target
            };
            this.confirmModalOpen = true;
        },

        cancelStatusChange() {
            if (this.statusChangeData.selectElement) {
                this.statusChangeData.selectElement.value = this.statusChangeData.oldStatus;
            }
            this.confirmModalOpen = false;
        },

        async confirmStatusChange() {
            if (!this.statusChangeData.bookingId || !this.statusChangeData.newStatus) return;
            const { bookingId, newStatus } = this.statusChangeData;
            this.confirmModalOpen = false;
            await this.updateStatus(bookingId, newStatus);
        },

        async updateStatus(bookingId, newStatus) {
            try {
                const res = await fetch(`/admin/hotels/bookings/${bookingId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.toastMessage = data.message || `Booking status updated to ${newStatus}.`;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert(data.message || 'Failed to update booking status.');
                }
            } catch (err) {
                console.error('Status update failed', err);
                alert('Failed to update status. Please try again.');
            }
        }
    };
}
</script>
@endpush

@endsection
