@extends('layouts.dashboard')

@section('page-title', 'Hotels')

@section('content')

<div x-data="agentHotels()" x-init="init()">

    {{-- Top Section Title --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Hotels</h1>
    </div>

    {{-- Success Toast Notification --}}
    <div 
        x-show="successMessage" 
        x-transition
        class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center justify-between shadow-sm"
        style="display: none;"
    >
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-text="successMessage"></span>
        </div>
        <button type="button" @click="successMessage = ''" class="text-emerald-500 hover:text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Top Filter Bar (Always visible in both grid & detail view) --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs mb-6">
        <form @submit.prevent="filterHotels()" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3.5 items-end">
            
            {{-- City/Location Dropdown --}}
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">City/Location</label>
                <select x-model="filters.location_id" @change="filterHotels()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="">All Locations</option>
                    <template x-for="loc in locationsList" :key="loc.id">
                        <option :value="loc.id" x-text="loc.name"></option>
                    </template>
                </select>
            </div>

            {{-- Check-in --}}
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Check-in *</label>
                <input 
                    type="date" 
                    x-model="filters.check_in" 
                    @change="filterHotels()"
                    class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900"
                />
            </div>

            {{-- Check-out --}}
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Check-out *</label>
                <input 
                    type="date" 
                    x-model="filters.check_out" 
                    @change="filterHotels()"
                    class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900"
                />
            </div>

            {{-- Guests --}}
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Guests</label>
                <input 
                    type="number" 
                    min="1" 
                    x-model.number="filters.guests" 
                    placeholder="2"
                    class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900"
                />
            </div>

            {{-- Min Stars --}}
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Min Stars</label>
                <select x-model="filters.star_rating" @change="filterHotels()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="">Any</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>

            {{-- Search Button --}}
            <div>
                <button type="submit" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition-colors inline-flex items-center justify-center gap-2 shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                </button>
            </div>

        </form>
    </div>

    {{-- VIEW 1: HOTELS GRID LISTING --}}
    <div x-show="!viewingHotel">
        
        {{-- Loading Skeleton --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <template x-for="i in [1,2,3]" :key="i">
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-xs animate-pulse">
                    <div class="h-48 bg-gray-200 w-full"></div>
                    <div class="p-5 space-y-3">
                        <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-100 rounded w-1/2"></div>
                        <div class="h-10 bg-gray-200 rounded w-full mt-4"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Hotel Cards Grid (3 Columns matching Expected UI Screenshot) --}}
        <div x-show="!loading && hotelsList.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <template x-for="hotel in hotelsList" :key="hotel.id">
                <div class="bg-white border border-gray-200/80 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between transition-all duration-200 hover:shadow-md hover:border-gray-300">
                    
                    <div>
                        {{-- Hotel Image --}}
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            <img 
                                :src="getHotelImage(hotel)" 
                                :alt="hotel.name"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                            />
                        </div>

                        {{-- Card Header & Info --}}
                        <div class="p-5 space-y-3">
                            <h3 class="font-bold text-gray-900 text-lg leading-snug" x-text="hotel.name"></h3>
                            
                            {{-- Location & Star Rating (Exact line match to Expected UI) --}}
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span class="text-xs font-medium text-gray-600" x-text="getLocationName(hotel)"></span>
                                </div>
                                <div class="flex items-center text-gray-900 text-xs gap-0.5 font-bold">
                                    <template x-for="star in getStarArray(hotel.star_rating)" :key="star">
                                        <span>★</span>
                                    </template>
                                </div>
                            </div>

                            {{-- Amenities Pills (Small, clean capsule pills matching Expected UI) --}}
                            <template x-if="hotel.amenities && hotel.amenities.length > 0">
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <template x-for="(item, idx) in hotel.amenities.slice(0, 4)" :key="idx">
                                        <span class="bg-gray-50 border border-gray-200 text-gray-700 text-[11px] px-2.5 py-0.5 rounded-full font-normal" x-text="item"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- View Availability Button --}}
                    <div class="p-5 pt-0">
                        <button 
                            type="button" 
                            @click="selectHotel(hotel)"
                            class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors text-center shadow-xs"
                        >
                            View Availability
                        </button>
                    </div>

                </div>
            </template>
        </div>

        {{-- No Results State --}}
        <div x-show="!loading && hotelsList.length === 0" class="bg-white border border-gray-100 rounded-2xl p-12 text-center shadow-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0H9m4 0h2" />
            </svg>
            <h4 class="text-base font-semibold text-gray-900 mb-1">No available hotels match your search criteria</h4>
            <p class="text-xs text-gray-500">Try adjusting your location, dates, or guest count filters above.</p>
        </div>

    </div>

    {{-- VIEW 2: HOTEL ROOM AVAILABILITY DETAIL VIEW --}}
    <div x-show="viewingHotel && selectedHotel" class="space-y-6" style="display: none;">
        
        {{-- Back to results button --}}
        <div>
            <button 
                type="button" 
                @click="viewingHotel = false; selectedHotel = null;"
                class="bg-white border border-gray-200 text-gray-800 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition-colors shadow-2xs"
            >
                <span>← Back to results</span>
            </button>
        </div>

        {{-- Hotel Hero Header Section --}}
        <template x-if="selectedHotel">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                {{-- Left: Main Resort Image & Thumbnails --}}
                <div class="md:col-span-6 space-y-3">
                    <div class="h-72 rounded-2xl overflow-hidden shadow-xs bg-gray-100 border border-gray-200">
                        <img 
                            :src="getHotelImage(selectedHotel)" 
                            :alt="selectedHotel.name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    {{-- Thumbnail grid --}}
                    <div class="flex gap-2">
                        <div class="w-24 h-16 rounded-lg border border-gray-200 overflow-hidden bg-gray-100">
                            <img :src="getHotelImage(selectedHotel)" class="w-full h-full object-cover opacity-90 hover:opacity-100 cursor-pointer" />
                        </div>
                    </div>
                </div>

                {{-- Right: Hotel Details & Amenities --}}
                <div class="md:col-span-6 space-y-3">
                    <h2 class="text-3xl font-bold text-gray-900" x-text="selectedHotel.name"></h2>
                    
                    {{-- Location & Stars --}}
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="font-medium text-gray-700 text-sm" x-text="getLocationName(selectedHotel)"></span>
                        </div>
                        <div class="flex items-center text-gray-900 text-sm gap-0.5 font-bold">
                            <template x-for="star in getStarArray(selectedHotel.star_rating)" :key="star">
                                <span>★</span>
                            </template>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p class="text-sm text-gray-600 leading-relaxed" x-text="selectedHotel.description || 'Luxury hotel offering premium accommodation and world-class amenities.'"></p>

                    {{-- Full Amenities Capsules --}}
                    <template x-if="selectedHotel.amenities && selectedHotel.amenities.length > 0">
                        <div class="flex flex-wrap gap-2 pt-2">
                            <template x-for="item in selectedHotel.amenities" :key="item">
                                <span class="bg-gray-50 border border-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full font-medium" x-text="item"></span>
                            </template>
                        </div>
                    </template>
                </div>

            </div>
        </template>

        {{-- Available Rooms Section Header --}}
        <div class="pt-4 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-900" x-text="`Available Rooms (${nights} night${nights > 1 ? 's' : ''})`"></h3>
        </div>

        {{-- Room Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template x-for="slot in getAvailableSlotsForSelectedHotel()" :key="slot.id">
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex items-end justify-between hover:border-gray-300 transition-colors">
                    
                    {{-- Left Side: Room Title, Capacity, Availability & Pricing --}}
                    <div class="space-y-2">
                        <h4 class="font-bold text-gray-900 text-base" x-text="slot.name"></h4>
                        
                        {{-- Capacity & Real Availability Count --}}
                        <p class="text-xs text-gray-500 font-medium">
                            <span x-text="`Capacity: ${slot.capacity} guests`"></span>
                            <span>•</span>
                            <span class="text-emerald-700 font-semibold" x-text="`${slot.real_available_qty !== undefined ? slot.real_available_qty : slot.available_qty} available`"></span>
                        </p>

                        {{-- Total Price & Per Night Subtitle --}}
                        <div class="pt-1 flex items-baseline gap-1.5">
                            <span class="font-bold text-gray-900 text-lg" x-text="`${(slot.price_per_night * nights).toFixed(0)} ${slot.currency || 'AED'}`"></span>
                            <span class="text-xs text-gray-400 font-normal" x-text="`(${slot.price_per_night}/night)`"></span>
                        </div>
                    </div>

                    {{-- Right Side: Book Button --}}
                    <div>
                        <button 
                            type="button" 
                            @click="openBookingModal(slot)"
                            class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-6 py-2 rounded-lg transition-colors shadow-2xs"
                        >
                            Book
                        </button>
                    </div>

                </div>
            </template>
        </div>

        {{-- No Available Rooms Message --}}
        <template x-if="getAvailableSlotsForSelectedHotel().length === 0">
            <div class="bg-white border border-gray-100 rounded-xl p-8 text-center text-gray-500 text-sm">
                No rooms available matching your selected guest capacity or dates.
            </div>
        </template>

    </div>

    {{-- BOOKING CONFIRMATION MODAL --}}
    <div 
        x-show="bookingModalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="bookingModalOpen = false"></div>

            <div class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-10">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Complete Room Booking</h2>
                        <p class="text-xs text-gray-500" x-text="selectedSlot ? `${selectedSlot.name} at ${selectedHotel.name}` : ''"></p>
                    </div>
                    <button type="button" @click="bookingModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Stay Summary Box --}}
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200/80 mb-4 text-xs space-y-1.5 text-gray-700">
                    <div class="flex justify-between">
                        <span class="font-medium">Check-in / Check-out:</span>
                        <span class="font-semibold text-gray-900" x-text="`${filters.check_in || 'N/A'} to ${filters.check_out || 'N/A'}`"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Stay Duration:</span>
                        <span class="font-semibold text-gray-900" x-text="`${nights} night(s)`"></span>
                    </div>
                    <div class="flex justify-between" x-show="selectedSlot">
                        <span class="font-medium">Price / Night:</span>
                        <span class="font-semibold text-gray-900" x-text="selectedSlot ? `${selectedSlot.price_per_night} ${selectedSlot.currency || 'AED'}/night` : ''"></span>
                    </div>
                    <div class="flex justify-between" x-show="selectedSlot">
                        <span class="font-medium">Rooms Selected:</span>
                        <span class="font-semibold text-gray-900" x-text="`${bookingForm.rooms_count || 1} room(s)`"></span>
                    </div>
                    <div class="flex justify-between pt-1.5 border-t border-gray-200/60" x-show="selectedSlot">
                        <span class="font-medium">Total Price:</span>
                        <span class="font-bold text-gray-900 text-sm" x-text="selectedSlot ? `${(selectedSlot.price_per_night * nights * (bookingForm.rooms_count || 1)).toFixed(0)} ${selectedSlot.currency || 'AED'}` : ''"></span>
                    </div>
                </div>

                <form @submit.prevent="submitBooking()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Customer Name *</label>
                        <input 
                            type="text" 
                            x-model="bookingForm.customer_name" 
                            required
                            placeholder="Enter primary guest full name"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Customer Email *</label>
                            <input 
                                type="email" 
                                x-model="bookingForm.customer_email" 
                                required
                                placeholder="guest@example.com"
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Customer Phone</label>
                            <input 
                                type="tel" 
                                x-model="bookingForm.customer_phone" 
                                placeholder="+971 50 123 4567"
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Number of Rooms *</label>
                        <input 
                            type="number" 
                            min="1" 
                            :max="selectedSlot ? (selectedSlot.real_available_qty !== undefined ? selectedSlot.real_available_qty : selectedSlot.available_qty) : 10" 
                            x-model.number="bookingForm.rooms_count" 
                            required
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Special Requests / Notes</label>
                        <textarea 
                            x-model="bookingForm.special_requests" 
                            rows="2"
                            placeholder="Optional bed preference, late check-in request..."
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="bookingModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button 
                            type="submit" 
                            :disabled="submittingBooking"
                            class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-6 py-2.5 rounded-lg shadow-sm"
                        >
                            <span x-text="submittingBooking ? 'Processing...' : 'Confirm & Book'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function agentHotels() {
    return {
        baseUrl: '{{ url("agent/hotels") }}',
        loading: false,
        hotelsList: @json(isset($hotelsPaginator) ? $hotelsPaginator->items() : (isset($hotels) ? $hotels->items() : [])),
        locationsList: @json($locations),
        availableAmenitiesList: @json($amenities),
        successMessage: '',
        
        viewingHotel: false,
        selectedHotel: null,

        nights: @json($nights ?? 1),

        filters: {
            search: @json($search ?? ''),
            location_id: @json($locationId ?? ''),
            star_rating: @json($starRating ?? ''),
            guests: @json($guests ?? 2),
            check_in: @json($checkIn ?? date('Y-m-d')),
            check_out: @json($checkOut ?? date('Y-m-d', strtotime('+1 day')))
        },

        bookingModalOpen: false,
        selectedSlot: null,
        submittingBooking: false,
        bookingForm: {
            customer_name: '',
            customer_email: '',
            customer_phone: '',
            rooms_count: 1,
            special_requests: ''
        },

        hotelImages: [
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80'
        ],

        init() {
            this.updateNights();
        },

        getHotelImage(hotel) {
            if (!hotel || !hotel.id) return this.hotelImages[0];
            return this.hotelImages[hotel.id % this.hotelImages.length];
        },

        getLocationName(hotel) {
            if (!hotel) return '';
            if (hotel.location_name) return hotel.location_name;
            if (hotel.location && hotel.location.name) return hotel.location.name;
            if (hotel.location_id && this.locationsList && this.locationsList.length > 0) {
                const found = this.locationsList.find(l => l.id == hotel.location_id);
                if (found) return found.name;
            }
            return '';
        },

        getStarArray(count) {
            const num = parseInt(count) || 1;
            return Array.from({ length: Math.min(Math.max(num, 1), 5) }, (_, i) => i + 1);
        },

        updateNights() {
            if (this.filters.check_in && this.filters.check_out) {
                const d1 = new Date(this.filters.check_in);
                const d2 = new Date(this.filters.check_out);
                if (d2 > d1) {
                    const diffTime = Math.abs(d2 - d1);
                    this.nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
                    return;
                }
            }
            this.nights = 1;
        },

        async filterHotels() {
            this.loading = true;
            this.updateNights();
            try {
                const params = new URLSearchParams();
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.location_id) params.append('location_id', this.filters.location_id);
                if (this.filters.star_rating) params.append('star_rating', this.filters.star_rating);
                if (this.filters.guests) params.append('guests', this.filters.guests);
                if (this.filters.check_in) params.append('check_in', this.filters.check_in);
                if (this.filters.check_out) params.append('check_out', this.filters.check_out);

                const response = await fetch(`${this.baseUrl}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success && data.hotels) {
                    this.hotelsList = data.hotels.data || data.hotels;
                    if (data.nights) this.nights = data.nights;

                    if (this.selectedHotel) {
                        const updated = this.hotelsList.find(h => h.id === this.selectedHotel.id);
                        if (updated) {
                            this.selectedHotel = updated;
                        }
                    }
                }
            } catch (err) {
                console.error('Failed to filter hotels', err);
            } finally {
                this.loading = false;
            }
        },

        selectHotel(hotel) {
            this.selectedHotel = hotel;
            this.viewingHotel = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        getAvailableSlotsForSelectedHotel() {
            if (!this.selectedHotel || !this.selectedHotel.slots) return [];
            const reqGuests = parseInt(this.filters.guests) || 1;
            return this.selectedHotel.slots.filter(slot => {
                const avail = slot.real_available_qty !== undefined ? slot.real_available_qty : slot.available_qty;
                return slot.is_active && slot.capacity >= reqGuests && avail > 0;
            });
        },

        openBookingModal(slot) {
            this.selectedSlot = slot;
            this.bookingForm = {
                customer_name: '',
                customer_email: '{{ auth()->user()->email ?? "" }}',
                customer_phone: '{{ auth()->user()->phone ?? "" }}',
                rooms_count: 1,
                special_requests: ''
            };
            this.bookingModalOpen = true;
        },

        async submitBooking() {
            if (!this.selectedSlot || !this.selectedHotel) return;
            this.submittingBooking = true;

            const payload = {
                _token: '{{ csrf_token() }}',
                hotel_room_slot_id: this.selectedSlot.id,
                customer_name: this.bookingForm.customer_name,
                customer_email: this.bookingForm.customer_email,
                customer_phone: this.bookingForm.customer_phone,
                check_in: this.filters.check_in,
                check_out: this.filters.check_out,
                guests: this.filters.guests || 2,
                rooms_count: this.bookingForm.rooms_count || 1,
                special_requests: this.bookingForm.special_requests
            };

            try {
                const response = await fetch('{{ route("agent.hotels.book") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.bookingModalOpen = false;
                    this.successMessage = data.message || 'Room booking confirmed successfully!';
                    this.filterHotels();
                } else {
                    alert(data.message || 'Booking failed. Please check availability.');
                }
            } catch (err) {
                console.error('Booking submission failed', err);
                alert('Submission failed. Please try again.');
            } finally {
                this.submittingBooking = false;
            }
        }
    };
}
</script>
@endpush

@endsection
