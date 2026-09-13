@extends('layouts.dashboard')

@section('page-title', 'Hotels & Inventory')

@section('content')

<div x-data="agentHotels()" x-init="init()">
    
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hotels & Inventory</h1>
        </div>
        <button 
            type="button" 
            @click="openAddModal()"
            class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition-colors inline-flex items-center gap-2 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Hotel</span>
        </button>
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

    {{-- Search & Filter Bar --}}
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm mb-6">
        <form @submit.prevent="filterHotels()" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Search Hotel</label>
                <input 
                    type="text" 
                    x-model="filters.search" 
                    placeholder="Search name or address..."
                    class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Location</label>
                <select x-model="filters.location_id" @change="filterHotels()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="">All Locations</option>
                    <template x-for="loc in locationsList" :key="loc.id">
                        <option :value="loc.id" x-text="loc.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-900 mb-1.5">Star Rating</label>
                <select x-model="filters.star_rating" @change="filterHotels()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="filterHotels()" class="flex-1 bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Filter</span>
                </button>
                <button type="button" @click="resetFilters()" class="px-3 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg text-xs font-medium transition-colors">
                    Reset
                </button>
            </div>
        </form>
    </div>

    {{-- Hotels List Section --}}
    <div class="relative min-h-[300px] space-y-3.5">
        
        {{-- Loading Skeleton --}}
        <div x-show="loading" class="space-y-3">
            <template x-for="i in [1,2,3,4]" :key="i">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm animate-pulse flex items-center justify-between">
                    <div class="space-y-2 w-1/3">
                        <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                    </div>
                    <div class="h-6 bg-gray-200 rounded-full w-24"></div>
                </div>
            </template>
        </div>

        {{-- Hotel Cards List --}}
        <div x-show="!loading && hotelsList.length > 0" class="space-y-3.5">
            <template x-for="hotel in hotelsList" :key="hotel.id">
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all overflow-hidden">
                    
                    {{-- Main Row Header --}}
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        
                        {{-- Left side: Title, Stars, Badges --}}
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="font-bold text-gray-900 text-lg" x-text="hotel.name"></h3>
                            
                            {{-- Star Rating Icons --}}
                            <div class="flex items-center text-gray-900 text-xs">
                                <template x-for="star in getStarArray(hotel.star_rating)" :key="star">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-gray-900" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </template>
                            </div>

                            {{-- Active Badge --}}
                            <button 
                                type="button"
                                @click="toggleStatus(hotel)"
                                :class="hotel.is_active ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-600'"
                                class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full lowercase tracking-wide transition-colors">
                                <span x-text="hotel.is_active ? 'active' : 'inactive'"></span>
                            </button>

                            {{-- Featured Badge --}}
                            <template x-if="hotel.is_featured">
                                <span class="bg-gray-100 text-gray-600 text-[11px] font-medium px-2.5 py-0.5 rounded-full border border-gray-200">
                                    Featured
                                </span>
                            </template>
                        </div>

                        {{-- Right side: Location, Edit, View, Expand Chevron --}}
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-gray-500 font-medium text-xs sm:text-sm" x-text="hotel.location_name"></span>

                            {{-- Edit Pencil Button --}}
                            <button 
                                type="button" 
                                @click="openEditModal(hotel)" 
                                class="text-gray-500 hover:text-gray-900 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Edit Hotel"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                            </button>

                            {{-- View Toggle Eye Button --}}
                            <button 
                                type="button" 
                                @click="toggleExpand(hotel.id)" 
                                class="text-gray-500 hover:text-gray-900 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                                title="View Details"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>

                            {{-- Chevron Expand Button --}}
                            <button 
                                type="button" 
                                @click="toggleExpand(hotel.id)" 
                                class="text-gray-400 hover:text-gray-700 p-1 transition-transform duration-200"
                                :class="expandedHotels.includes(hotel.id) ? 'rotate-180' : ''"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Expanded Content --}}
                    <div 
                        x-show="expandedHotels.includes(hotel.id)" 
                        x-collapse
                        class="px-5 pb-5 pt-2 border-t border-gray-100 bg-gray-50/50 space-y-4"
                    >
                        {{-- Address --}}
                        <template x-if="hotel.address">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Address</span>
                                <p class="text-xs text-gray-700" x-text="hotel.address"></p>
                            </div>
                        </template>

                        {{-- Description --}}
                        <template x-if="hotel.description">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Description</span>
                                <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line" x-text="hotel.description"></p>
                            </div>
                        </template>

                        {{-- About terms and conditions --}}
                        <template x-if="hotel.terms_and_conditions">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">About Terms and Conditions</span>
                                <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line" x-text="hotel.terms_and_conditions"></p>
                            </div>
                        </template>

                        {{-- Amenities Capsules --}}
                        <template x-if="hotel.amenities && hotel.amenities.length > 0">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Amenities</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="item in hotel.amenities" :key="item">
                                        <span class="bg-white border border-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full shadow-xs font-medium" x-text="item"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Slots / Rooms Section --}}
                        <div class="pt-3 border-t border-gray-200/80">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-gray-900">Slots / Rooms</h4>
                                <button 
                                    type="button"
                                    @click="openAddSlotModal(hotel)"
                                    class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-xs px-3.5 py-2 rounded-lg transition-colors inline-flex items-center gap-1.5 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>Add Slot</span>
                                </button>
                            </div>

                            {{-- Slots Table --}}
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-2xs">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50/80">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Slot Name</th>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Capacity</th>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Price/Night</th>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Currency</th>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Available</th>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <template x-for="slot in (hotel.slots || [])" :key="slot.id">
                                            <tr class="hover:bg-gray-50/60 transition-colors">
                                                <td class="px-4 py-3 text-xs font-semibold text-gray-900" x-text="slot.name"></td>
                                                <td class="px-4 py-3 text-xs text-gray-600" x-text="slot.capacity"></td>
                                                <td class="px-4 py-3 text-xs font-medium text-gray-900" x-text="slot.price_per_night"></td>
                                                <td class="px-4 py-3 text-xs text-gray-600 uppercase" x-text="slot.currency"></td>
                                                <td class="px-4 py-3 text-xs text-gray-600" x-text="slot.available_qty"></td>
                                                <td class="px-4 py-3 text-xs">
                                                    <button 
                                                        type="button" 
                                                        @click="toggleSlotStatus(slot, hotel)"
                                                        :class="slot.is_active ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-600'"
                                                        class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full lowercase tracking-wide transition-colors">
                                                        <span x-text="slot.is_active ? 'active' : 'inactive'"></span>
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-xs text-right">
                                                    <button 
                                                        type="button" 
                                                        @click="openEditSlotModal(slot, hotel)"
                                                        class="text-gray-500 hover:text-gray-900 p-1 rounded-md hover:bg-gray-100 transition-colors"
                                                        title="Edit Slot">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="!hotel.slots || hotel.slots.length === 0">
                                            <tr>
                                                <td colspan="7" class="px-4 py-6 text-center text-xs text-gray-400 italic">
                                                    No slots/rooms added yet. Click "+ Add Slot" above.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && hotelsList.length === 0" class="bg-white border border-gray-100 rounded-xl p-12 text-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5s0 0 0 0M13.5 6.75h1.5s0 0 0 0M9 10.5h1.5s0 0 0 0M13.5 10.5h1.5s0 0 0 0M9 14.25h1.5s0 0 0 0M13.5 14.25h1.5s0 0 0 0M9 18h1.5s0 0 0 0M13.5 18h1.5s0 0 0 0" />
            </svg>
            <h4 class="text-base font-semibold text-gray-900 mb-1">No hotels found</h4>
            <p class="text-xs text-gray-500 mb-4">Add a new hotel or try adjusting your search filters.</p>
            <button 
                type="button" 
                @click="openAddModal()" 
                class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-xs px-4 py-2 rounded-lg transition-colors">
                + Add Hotel
            </button>
        </div>

    </div>

    {{-- Add / Edit Hotel Modal Popup --}}
    <div 
        x-show="modalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto"
        style="display: none;"
    >
        <div 
            @click.away="closeModal()"
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative my-8"
        >
            {{-- Modal Close Button x --}}
            <button 
                type="button" 
                @click="closeModal()"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition-colors"
                title="Close"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Modal Header --}}
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900" x-text="isEditing ? 'Edit Hotel' : 'Add Hotel'"></h2>
            </div>

            {{-- Modal Form --}}
            <form @submit.prevent="submitHotelForm()" class="space-y-4 max-h-[80vh] overflow-y-auto pr-1">
                
                {{-- Hotel Name * --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Hotel Name *</label>
                    <input 
                        type="text" 
                        x-model="form.name" 
                        required
                        placeholder="Enter hotel name"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                    />
                    <template x-if="errors.name">
                        <p class="text-xs text-red-500 mt-1" x-text="errors.name[0]"></p>
                    </template>
                </div>

                {{-- Location * (Dropdown + Add New Button) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-900">Location *</label>
                        <button 
                            type="button" 
                            @click="showAddLocationInput = !showAddLocationInput"
                            class="text-xs font-semibold text-gray-700 hover:text-gray-900 hover:underline inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Add Location</span>
                        </button>
                    </div>

                    {{-- Dynamic Inline Add Location Field --}}
                    <div x-show="showAddLocationInput" x-transition class="mb-2 p-2 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                        <input 
                            type="text" 
                            x-model="newLocationName" 
                            placeholder="New location name..."
                            class="flex-1 bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-gray-900"
                            @keydown.enter.prevent="saveNewLocation()"
                        />
                        <button 
                            type="button" 
                            @click="saveNewLocation()"
                            :disabled="savingLocation || !newLocationName.trim()"
                            class="bg-[#0B1527] hover:bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition-colors disabled:opacity-50">
                            <span x-show="!savingLocation">Save</span>
                            <span x-show="savingLocation">...</span>
                        </button>
                        <button 
                            type="button" 
                            @click="showAddLocationInput = false; newLocationName = '';"
                            class="text-gray-400 hover:text-gray-600 p-1 text-xs">
                            ✕
                        </button>
                    </div>

                    {{-- Location Dropdown --}}
                    <select 
                        x-model="form.location_id" 
                        required
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select location</option>
                        <template x-for="loc in locationsList" :key="loc.id">
                            <option :value="loc.id" x-text="loc.name"></option>
                        </template>
                    </select>
                    <template x-if="errors.location_id">
                        <p class="text-xs text-red-500 mt-1" x-text="errors.location_id[0]"></p>
                    </template>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Address</label>
                    <input 
                        type="text" 
                        x-model="form.address" 
                        placeholder="Enter full address"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                    />
                    <template x-if="errors.address">
                        <p class="text-xs text-red-500 mt-1" x-text="errors.address[0]"></p>
                    </template>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Description</label>
                    <textarea 
                        x-model="form.description" 
                        rows="3"
                        placeholder="Enter hotel description"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 resize-y"
                    ></textarea>
                    <template x-if="errors.description">
                        <p class="text-xs text-red-500 mt-1" x-text="errors.description[0]"></p>
                    </template>
                </div>

                {{-- About terms and conditions (After Description) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">About terms and conditions</label>
                    <textarea 
                        x-model="form.terms_and_conditions" 
                        rows="3"
                        placeholder="Enter terms and conditions"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 resize-y"
                    ></textarea>
                    <template x-if="errors.terms_and_conditions">
                        <p class="text-xs text-red-500 mt-1" x-text="errors.terms_and_conditions[0]"></p>
                    </template>
                </div>

                {{-- Star Rating --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Star Rating</label>
                    <select 
                        x-model="form.star_rating" 
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <template x-if="errors.star_rating">
                        <p class="text-xs text-red-500 mt-1" x-text="errors.star_rating[0]"></p>
                    </template>
                </div>

                {{-- Amenities Section (Capsules with Cross Icon + Add New Button & Dropdown) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-900">Amenities</label>
                        <button 
                            type="button" 
                            @click="showAmenityDropdown = !showAmenityDropdown"
                            class="text-xs font-semibold text-gray-700 hover:text-gray-900 hover:underline inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Add Amenity</span>
                        </button>
                    </div>

                    {{-- Amenities Capsules List with Cross Icons --}}
                    <div class="flex flex-wrap gap-2 mb-2 p-2.5 bg-gray-50/70 border border-gray-200 rounded-xl min-h-[44px] items-center">
                        <template x-for="(amenity, idx) in form.amenities" :key="amenity">
                            <span class="bg-white border border-gray-300 text-gray-800 text-xs font-medium px-3 py-1 rounded-full inline-flex items-center gap-1.5 shadow-2xs">
                                <span x-text="amenity"></span>
                                <button 
                                    type="button" 
                                    @click="removeAmenity(idx)"
                                    class="text-gray-400 hover:text-gray-700 rounded-full transition-colors p-0.5"
                                    title="Remove amenity"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template x-if="form.amenities.length === 0">
                            <span class="text-xs text-gray-400 italic">No amenities added yet. Click "+ Add Amenity" below.</span>
                        </template>
                    </div>

                    {{-- Add Amenity Dropdown & Custom Amenity Input --}}
                    <div x-show="showAmenityDropdown" x-transition class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-2 mb-2">
                        <div class="flex items-center gap-2">
                            <select 
                                x-model="selectedAmenityToSelect" 
                                @change="addSelectedAmenityFromDropdown()"
                                class="flex-1 bg-white border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <option value="">Select an amenity to add...</option>
                                <template x-for="item in availableAmenitiesList" :key="item.id">
                                    <option :value="item.name" x-text="item.name" :disabled="form.amenities.includes(item.name)"></option>
                                </template>
                            </select>
                        </div>
                        
                        {{-- Custom New Amenity Input --}}
                        <div class="flex items-center gap-2 pt-1 border-t border-gray-200">
                            <input 
                                type="text" 
                                x-model="newAmenityName" 
                                placeholder="Or type a new custom amenity..."
                                class="flex-1 bg-white border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-gray-900"
                                @keydown.enter.prevent="saveNewAmenity()"
                            />
                            <button 
                                type="button" 
                                @click="saveNewAmenity()"
                                :disabled="savingAmenity || !newAmenityName.trim()"
                                class="bg-[#0B1527] hover:bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors disabled:opacity-50">
                                <span x-show="!savingAmenity">Add</span>
                                <span x-show="savingAmenity">...</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Status & Featured --}}
                <div class="grid grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Status</label>
                        <select 
                            x-model="form.is_active" 
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option :value="true">Active</option>
                            <option :value="false">Inactive</option>
                        </select>
                    </div>
                    <div class="pt-5">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-900 cursor-pointer">
                            <input 
                                type="checkbox" 
                                x-model="form.is_featured" 
                                class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                            />
                            <span>Featured</span>
                        </label>
                    </div>
                </div>

                {{-- Form Actions (Cancel & Save) --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="closeModal()"
                        class="px-4 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        :disabled="submittingForm"
                        class="px-5 py-2.5 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50 inline-flex items-center gap-2"
                    >
                        <span x-show="!submittingForm" x-text="isEditing ? 'Save Changes' : 'Save'"></span>
                        <span x-show="submittingForm">Saving...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Add / Edit Room Slot Modal Popup --}}
    <div 
        x-show="slotModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto"
        style="display: none;"
    >
        <div 
            @click.away="closeSlotModal()"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative my-8"
        >
            {{-- Modal Close Button x --}}
            <button 
                type="button" 
                @click="closeSlotModal()"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition-colors"
                title="Close"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Modal Header --}}
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900" x-text="isEditingSlot ? 'Edit Slot' : 'Add Slot'"></h2>
            </div>

            {{-- Modal Form --}}
            <form @submit.prevent="submitSlotForm()" class="space-y-4">
                
                {{-- Slot Name * --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Slot Name *</label>
                    <input 
                        type="text" 
                        x-model="slotForm.name" 
                        required
                        placeholder="Standard Room"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                    />
                    <template x-if="slotErrors.name">
                        <p class="text-xs text-red-500 mt-1" x-text="slotErrors.name[0]"></p>
                    </template>
                </div>

                {{-- Capacity & Available Qty --}}
                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Capacity</label>
                        <input 
                            type="number" 
                            x-model="slotForm.capacity" 
                            min="1"
                            required
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                        <template x-if="slotErrors.capacity">
                            <p class="text-xs text-red-500 mt-1" x-text="slotErrors.capacity[0]"></p>
                        </template>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Available Qty</label>
                        <input 
                            type="number" 
                            x-model="slotForm.available_qty" 
                            min="0"
                            required
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                        <template x-if="slotErrors.available_qty">
                            <p class="text-xs text-red-500 mt-1" x-text="slotErrors.available_qty[0]"></p>
                        </template>
                    </div>
                </div>

                {{-- Price/Night * & Currency --}}
                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Price/Night *</label>
                        <input 
                            type="number" 
                            step="0.01"
                            x-model="slotForm.price_per_night" 
                            min="0"
                            required
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                        <template x-if="slotErrors.price_per_night">
                            <p class="text-xs text-red-500 mt-1" x-text="slotErrors.price_per_night[0]"></p>
                        </template>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Currency</label>
                        <select 
                            x-model="slotForm.currency" 
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="AED">AED</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="SAR">SAR</option>
                        </select>
                        <template x-if="slotErrors.currency">
                            <p class="text-xs text-red-500 mt-1" x-text="slotErrors.currency[0]"></p>
                        </template>
                    </div>
                </div>

                {{-- Form Actions (Cancel & Save) --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="closeSlotModal()"
                        class="px-4 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        :disabled="submittingSlotForm"
                        class="px-5 py-2.5 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50 inline-flex items-center gap-2"
                    >
                        <span x-show="!submittingSlotForm">Save</span>
                        <span x-show="submittingSlotForm">Saving...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function agentHotels() {
    return {
        loading: false,
        hotelsList: @json($hotels->items()),
        locationsList: @json($locations),
        availableAmenitiesList: @json($amenities),
        expandedHotels: [],
        successMessage: '',
        modalOpen: false,
        isEditing: false,
        editingHotelId: null,
        submittingForm: false,
        errors: {},
        
        // Dynamic location addition state
        showAddLocationInput: false,
        newLocationName: '',
        savingLocation: false,

        // Dynamic amenity addition state
        showAmenityDropdown: false,
        selectedAmenityToSelect: '',
        newAmenityName: '',
        savingAmenity: false,

        // Slot management state
        slotModalOpen: false,
        isEditingSlot: false,
        targetHotelForSlot: null,
        editingSlotId: null,
        submittingSlotForm: false,
        slotErrors: {},
        slotForm: {
            name: '',
            capacity: 2,
            available_qty: 1,
            price_per_night: 0,
            currency: 'AED',
            is_active: true
        },

        filters: {
            search: @json($search ?? ''),
            location_id: @json($locationId ?? ''),
            star_rating: @json($starRating ?? '')
        },

        form: {
            name: '',
            location_id: '',
            address: '',
            description: '',
            terms_and_conditions: '',
            star_rating: 4,
            amenities: [],
            is_active: true,
            is_featured: false
        },

        init() {
            // Page ready
        },

        getStarArray(count) {
            const num = parseInt(count) || 1;
            return Array.from({ length: Math.min(Math.max(num, 1), 5) }, (_, i) => i + 1);
        },

        toggleExpand(id) {
            if (this.expandedHotels.includes(id)) {
                this.expandedHotels = this.expandedHotels.filter(item => item !== id);
            } else {
                this.expandedHotels.push(id);
            }
        },

        async filterHotels() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.location_id) params.append('location_id', this.filters.location_id);
                if (this.filters.star_rating) params.append('star_rating', this.filters.star_rating);

                const response = await fetch(`{{ route('agent.hotels.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success && data.hotels) {
                    this.hotelsList = data.hotels.data || data.hotels;
                }
            } catch (err) {
                console.error('Failed to filter hotels', err);
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters.search = '';
            this.filters.location_id = '';
            this.filters.star_rating = '';
            this.filterHotels();
        },

        openAddModal() {
            this.isEditing = false;
            this.editingHotelId = null;
            this.errors = {};
            this.showAddLocationInput = false;
            this.showAmenityDropdown = false;
            this.newLocationName = '';
            this.newAmenityName = '';
            this.form = {
                name: '',
                location_id: this.locationsList.length > 0 ? this.locationsList[0].id : '',
                address: '',
                description: '',
                terms_and_conditions: '',
                star_rating: 4,
                amenities: ['WiFi', 'Pool', 'Spa', 'Gym', 'Restaurant'],
                is_active: true,
                is_featured: false
            };
            this.modalOpen = true;
        },

        openEditModal(hotel) {
            this.isEditing = true;
            this.editingHotelId = hotel.id;
            this.errors = {};
            this.showAddLocationInput = false;
            this.showAmenityDropdown = false;
            this.newLocationName = '';
            this.newAmenityName = '';
            this.form = {
                name: hotel.name,
                location_id: hotel.location_id,
                address: hotel.address || '',
                description: hotel.description || '',
                terms_and_conditions: hotel.terms_and_conditions || '',
                star_rating: hotel.star_rating || 4,
                amenities: Array.isArray(hotel.amenities) ? [...hotel.amenities] : [],
                is_active: Boolean(hotel.is_active),
                is_featured: Boolean(hotel.is_featured)
            };
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
            this.errors = {};
        },

        removeAmenity(index) {
            this.form.amenities.splice(index, 1);
        },

        addSelectedAmenityFromDropdown() {
            if (this.selectedAmenityToSelect && !this.form.amenities.includes(this.selectedAmenityToSelect)) {
                this.form.amenities.push(this.selectedAmenityToSelect);
            }
            this.selectedAmenityToSelect = '';
        },

        async saveNewLocation() {
            if (!this.newLocationName.trim()) return;
            this.savingLocation = true;

            try {
                const response = await fetch('{{ route('agent.hotels.locations.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.newLocationName.trim() })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    this.locationsList.push(data.location);
                    this.form.location_id = data.location.id;
                    this.newLocationName = '';
                    this.showAddLocationInput = false;
                } else {
                    alert(data.message || 'Failed to add location.');
                }
            } catch (err) {
                console.error('Failed to save location', err);
            } finally {
                this.savingLocation = false;
            }
        },

        async saveNewAmenity() {
            const name = this.newAmenityName.trim();
            if (!name) return;
            this.savingAmenity = true;

            try {
                const response = await fetch('{{ route('agent.hotels.amenities.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    if (!this.availableAmenitiesList.some(item => item.name.toLowerCase() === name.toLowerCase())) {
                        this.availableAmenitiesList.push(data.amenity);
                    }
                    if (!this.form.amenities.includes(data.amenity.name)) {
                        this.form.amenities.push(data.amenity.name);
                    }
                    this.newAmenityName = '';
                } else {
                    alert(data.message || 'Failed to add amenity.');
                }
            } catch (err) {
                console.error('Failed to save amenity', err);
            } finally {
                this.savingAmenity = false;
            }
        },

        async submitHotelForm() {
            this.submittingForm = true;
            this.errors = {};

            const url = this.isEditing 
                ? `{{ url('agent/hotels') }}/${this.editingHotelId}`
                : '{{ route('agent.hotels.store') }}';

            const method = this.isEditing ? 'PUT' : 'POST';

            const payload = {
                _token: '{{ csrf_token() }}',
                ...this.form
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.closeModal();
                    this.successMessage = data.message || 'Hotel saved successfully!';
                    
                    if (this.isEditing) {
                        const index = this.hotelsList.findIndex(h => h.id === this.editingHotelId);
                        if (index !== -1) {
                            this.hotelsList[index] = data.hotel;
                        }
                    } else {
                        this.hotelsList.unshift(data.hotel);
                    }
                } else if (data.errors) {
                    this.errors = data.errors;
                } else {
                    alert(data.message || 'Error saving hotel.');
                }
            } catch (err) {
                console.error('Submit hotel failed', err);
                alert('Submission failed. Please try again.');
            } finally {
                this.submittingForm = false;
            }
        },

        async toggleStatus(hotel) {
            try {
                const response = await fetch(`{{ url('agent/hotels') }}/${hotel.id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    hotel.is_active = data.is_active;
                    this.successMessage = `Hotel status updated to ${data.is_active ? 'active' : 'inactive'}.`;
                }
            } catch (err) {
                console.error('Status toggle failed', err);
            }
        },

        // --- Slot Modal Handlers ---
        openAddSlotModal(hotel) {
            this.targetHotelForSlot = hotel;
            this.isEditingSlot = false;
            this.editingSlotId = null;
            this.slotErrors = {};
            this.slotForm = {
                name: '',
                capacity: 2,
                available_qty: 1,
                price_per_night: 0,
                currency: 'AED',
                is_active: true
            };
            this.slotModalOpen = true;
        },

        openEditSlotModal(slot, hotel) {
            this.targetHotelForSlot = hotel;
            this.isEditingSlot = true;
            this.editingSlotId = slot.id;
            this.slotErrors = {};
            this.slotForm = {
                name: slot.name,
                capacity: slot.capacity,
                available_qty: slot.available_qty,
                price_per_night: slot.price_per_night,
                currency: slot.currency || 'AED',
                is_active: Boolean(slot.is_active)
            };
            this.slotModalOpen = true;
        },

        closeSlotModal() {
            this.slotModalOpen = false;
            this.slotErrors = {};
        },

        async submitSlotForm() {
            if (!this.targetHotelForSlot) return;
            this.submittingSlotForm = true;
            this.slotErrors = {};

            const url = this.isEditingSlot
                ? `{{ url('agent/hotels/slots') }}/${this.editingSlotId}`
                : `{{ url('agent/hotels') }}/${this.targetHotelForSlot.id}/slots`;

            const method = this.isEditingSlot ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.slotForm)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.closeSlotModal();
                    this.successMessage = data.message || 'Room slot saved successfully!';
                    
                    if (!this.targetHotelForSlot.slots) {
                        this.targetHotelForSlot.slots = [];
                    }

                    if (this.isEditingSlot) {
                        const idx = this.targetHotelForSlot.slots.findIndex(s => s.id === this.editingSlotId);
                        if (idx !== -1) {
                            this.targetHotelForSlot.slots[idx] = data.slot;
                        }
                    } else {
                        this.targetHotelForSlot.slots.push(data.slot);
                    }
                } else if (data.errors) {
                    this.slotErrors = data.errors;
                } else {
                    alert(data.message || 'Error saving room slot.');
                }
            } catch (err) {
                console.error('Submit slot failed', err);
                alert('Submission failed. Please try again.');
            } finally {
                this.submittingSlotForm = false;
            }
        },

        async toggleSlotStatus(slot, hotel) {
            try {
                const response = await fetch(`{{ url('agent/hotels/slots') }}/${slot.id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    slot.is_active = data.is_active;
                    this.successMessage = `Slot status updated to ${data.is_active ? 'active' : 'inactive'}.`;
                }
            } catch (err) {
                console.error('Slot status toggle failed', err);
            }
        }
    };
}
</script>
@endpush

@endsection
