@extends('layouts.dashboard')

@section('page-title', 'Hotels & Inventory Management')

@section('content')

<div x-data="adminHotels()" x-init="init()">
    
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
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm animate-pulse flex items-center justify-between">
                    <div class="space-y-2 w-1/3">
                        <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                    </div>
                    <div class="h-8 bg-gray-200 rounded w-24"></div>
                </div>
            </template>
        </div>

        {{-- Main Hotels Items --}}
        <div x-show="!loading && hotelsList.length > 0" class="space-y-3.5">
            <template x-for="hotel in hotelsList" :key="hotel.id">
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-xs overflow-hidden transition-all duration-200 hover:border-gray-300">
                    
                    {{-- Header Bar --}}
                    <div class="px-6 py-5 flex items-center justify-between gap-4">
                        
                        {{-- Left side: Name, Stars, Badges --}}
                        <div class="flex items-center gap-3.5 flex-wrap">
                            <h3 class="font-bold text-gray-900 text-lg" x-text="hotel.name"></h3>
                            
                            {{-- Star Rating (Black Stars matching SS) --}}
                            <div class="flex items-center text-gray-900 text-sm gap-0.5">
                                <template x-for="star in getStarArray(hotel.star_rating)" :key="star">
                                    <span class="font-bold">★</span>
                                </template>
                            </div>

                            {{-- Status Badge (Dark capsule matching SS) --}}
                            <button 
                                type="button" 
                                @click="toggleStatus(hotel)"
                                :class="hotel.is_active ? 'bg-[#0B1527] text-white' : 'bg-gray-200 text-gray-600'"
                                class="text-xs font-medium px-3 py-0.5 rounded-full lowercase tracking-wide transition-colors"
                            >
                                <span x-text="hotel.is_active ? 'active' : 'inactive'"></span>
                            </button>

                            {{-- Featured Badge (Light capsule matching SS) --}}
                            <template x-if="hotel.is_featured">
                                <span class="bg-white border border-gray-200 text-gray-700 text-xs px-3 py-0.5 rounded-full font-normal shadow-2xs">
                                    Featured
                                </span>
                            </template>
                        </div>

                        {{-- Right side: Location, Edit, Toggle, Expand Chevron --}}
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-gray-400 font-normal text-sm sm:text-base mr-1" x-text="hotel.location ? hotel.location.name : (hotel.location_name || '')"></span>

                            {{-- Edit Pencil Button --}}
                            <button 
                                type="button" 
                                @click="openEditModal(hotel)" 
                                class="text-gray-600 hover:text-gray-900 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Edit Hotel"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                            </button>

                            {{-- Toggle Status Button --}}
                            <button 
                                type="button" 
                                @click="toggleStatus(hotel)"
                                class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                                :title="hotel.is_active ? 'Deactivate Hotel' : 'Activate Hotel'"
                            >
                                <template x-if="hotel.is_active">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect>
                                        <circle cx="16" cy="12" r="2"></circle>
                                    </svg>
                                </template>
                                <template x-if="!hotel.is_active">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect>
                                        <circle cx="8" cy="12" r="2"></circle>
                                    </svg>
                                </template>
                            </button>

                            {{-- Delete Hotel Button --}}
                            <button 
                                type="button" 
                                @click="confirmDeleteHotel(hotel)"
                                class="text-gray-600 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors"
                                title="Delete Hotel"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>

                            {{-- Expand Chevron Button --}}
                            <button 
                                type="button" 
                                @click="toggleExpand(hotel.id)" 
                                class="text-gray-600 hover:text-gray-900 p-1 transition-transform duration-200"
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
                                <h4 class="text-sm font-bold text-gray-900">Slots / Rooms Inventory</h4>
                                <button 
                                    type="button"
                                    @click="openAddSlotModal(hotel)"
                                    class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-xs px-3.5 py-2 rounded-lg transition-colors inline-flex items-center gap-1.5 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>Add Room Slot</span>
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
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Stock Qty</th>
                                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <template x-for="slot in (hotel.slots || [])" :key="slot.id">
                                            <tr class="hover:bg-gray-50/60 transition-colors">
                                                <td class="px-4 py-3 text-xs font-semibold text-gray-900" x-text="slot.name"></td>
                                                <td class="px-4 py-3 text-xs text-gray-600" x-text="slot.capacity + ' guests'"></td>
                                                <td class="px-4 py-3 text-xs font-medium text-gray-900" x-text="slot.price_per_night"></td>
                                                <td class="px-4 py-3 text-xs text-gray-600 uppercase" x-text="slot.currency"></td>
                                                <td class="px-4 py-3 text-xs text-gray-600 font-semibold" x-text="slot.available_qty"></td>
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
                                                    No slots/rooms added yet. Click "+ Add Room Slot" above.
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

        {{-- No Hotels Empty State --}}
        <div x-show="!loading && hotelsList.length === 0" class="bg-white border border-gray-100 rounded-xl p-12 text-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0H9m4 0h2" />
            </svg>
            <h4 class="text-base font-semibold text-gray-900 mb-1">No hotels found</h4>
            <p class="text-xs text-gray-500 mb-4">Add a new hotel property to populate the inventory catalog.</p>
            <button 
                type="button" 
                @click="openAddModal()"
                class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-xs px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-1.5">
                + Add Hotel
            </button>
        </div>

    </div>

    {{-- Add/Edit Hotel Modal --}}
    <div 
        x-show="modalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="closeModal()"></div>

            <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-10">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900" x-text="isEditing ? 'Edit Hotel' : 'Add Hotel'"></h2>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitHotelForm()" class="space-y-4 max-h-[80vh] overflow-y-auto pr-1">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Hotel Name *</label>
                        <input 
                            type="text" 
                            x-model="form.name" 
                            required
                            placeholder="Enter hotel name"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                        <template x-if="errors.name">
                            <p class="text-xs text-rose-500 mt-1" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    {{-- Location Selection & Inline Creation --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-gray-900">Location *</label>
                            <button 
                                type="button" 
                                @click="showAddLocationInput = !showAddLocationInput"
                                class="text-xs text-slate-700 font-semibold hover:underline"
                            >
                                <span x-text="showAddLocationInput ? 'Cancel' : '+ Add New Location'"></span>
                            </button>
                        </div>

                        <div x-show="!showAddLocationInput">
                            <select 
                                x-model="form.location_id" 
                                required
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            >
                                <option value="" disabled>Select Location</option>
                                <template x-for="loc in locationsList" :key="loc.id">
                                    <option :value="loc.id" x-text="loc.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Inline Location Input --}}
                        <div x-show="showAddLocationInput" class="flex items-center gap-2">
                            <input 
                                type="text" 
                                x-model="newLocationName" 
                                placeholder="Enter new location name"
                                class="flex-1 bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />
                            <button 
                                type="button" 
                                @click="saveNewLocation()" 
                                :disabled="savingLocation"
                                class="bg-gray-900 hover:bg-slate-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg transition-colors"
                            >
                                <span x-text="savingLocation ? 'Saving...' : 'Save Location'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Star Rating --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Star Rating *</label>
                        <select x-model="form.star_rating" class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Address</label>
                        <input 
                            type="text" 
                            x-model="form.address" 
                            placeholder="Enter full address"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Description</label>
                        <textarea 
                            x-model="form.description" 
                            rows="3"
                            placeholder="Enter hotel description"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        ></textarea>
                    </div>

                    {{-- Terms & Conditions --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Terms & Conditions</label>
                        <textarea 
                            x-model="form.terms_and_conditions" 
                            rows="3"
                            placeholder="Enter terms and conditions"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        ></textarea>
                    </div>

                    {{-- Amenities --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Amenities</label>
                        
                        {{-- Selected Amenities Pills --}}
                        <div class="flex flex-wrap gap-1.5 mb-2.5">
                            <template x-for="(amenity, idx) in form.amenities" :key="idx">
                                <span class="bg-gray-100 border border-gray-200 text-gray-800 text-xs px-2.5 py-1 rounded-full inline-flex items-center gap-1.5 font-medium shadow-2xs">
                                    <span x-text="amenity"></span>
                                    <button type="button" @click="removeAmenity(idx)" class="text-gray-400 hover:text-rose-500 font-bold">×</button>
                                </span>
                            </template>
                        </div>

                        {{-- Custom Dropdown for Selecting / Adding Amenities --}}
                        <div class="relative mt-2" x-data="{ open: false }" @click.outside="open = false">
                            <input 
                                type="text" 
                                x-model="amenitySearchQuery" 
                                @focus="open = true"
                                @input="open = true"
                                placeholder="Click to select or search amenity..."
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />

                            {{-- Dropdown Menu --}}
                            <div 
                                x-show="open" 
                                x-transition
                                class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-30 max-h-56 overflow-y-auto divide-y divide-gray-100"
                                style="display: none;"
                            >
                                {{-- List of Unselected Master Amenities --}}
                                <div class="py-1">
                                    <template x-for="item in getUnselectedAmenities()" :key="item.id || item.name">
                                        <button 
                                            type="button" 
                                            @click="addAmenity(item.name); open = false; amenitySearchQuery = ''" 
                                            class="w-full text-left px-3.5 py-2 text-xs text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center justify-between transition-colors"
                                        >
                                            <span x-text="item.name"></span>
                                            <span class="text-[10px] text-gray-400 font-semibold">+ Add</span>
                                        </button>
                                    </template>

                                    <template x-if="getUnselectedAmenities().length === 0 && amenitySearchQuery.trim() === ''">
                                        <div class="px-3.5 py-2.5 text-xs text-gray-400 italic text-center">
                                            All available amenities added.
                                        </div>
                                    </template>
                                </div>

                                {{-- Custom Amenity Creation Option --}}
                                <div class="p-2 bg-gray-50/60" x-show="amenitySearchQuery.trim() !== ''">
                                    <button 
                                        type="button" 
                                        @click="saveNewAmenity(amenitySearchQuery.trim()); open = false"
                                        :disabled="savingAmenity"
                                        class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-medium text-xs px-3 py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-2xs"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        <span x-text="savingAmenity ? 'Saving...' : 'Add \x22' + amenitySearchQuery.trim() + '\x22 to Master List'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 pt-2">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900 w-4 h-4">
                            <span>Active</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" x-model="form.is_featured" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900 w-4 h-4">
                            <span>Featured</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button 
                            type="submit" 
                            :disabled="submittingForm"
                            class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow-sm"
                        >
                            <span x-text="submittingForm ? 'Saving...' : 'Save Hotel'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add/Edit Slot Modal --}}
    <div 
        x-show="slotModalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="closeSlotModal()"></div>

            <div class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-10">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900" x-text="isEditingSlot ? 'Edit Room Slot' : 'Add Room Slot'"></h2>
                    <button type="button" @click="closeSlotModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitSlotForm()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Slot / Room Name *</label>
                        <input 
                            type="text" 
                            x-model="slotForm.name" 
                            required
                            placeholder="e.g. Deluxe Room, Executive Suite"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Capacity (Guests) *</label>
                            <input 
                                type="number" 
                                min="1"
                                x-model.number="slotForm.capacity" 
                                required
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Stock Quantity *</label>
                            <input 
                                type="number" 
                                min="0"
                                x-model.number="slotForm.available_qty" 
                                required
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Price / Night *</label>
                            <input 
                                type="number" 
                                step="0.01"
                                min="0"
                                x-model.number="slotForm.price_per_night" 
                                required
                                class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Currency</label>
                            <select 
                                id="e-currency" 
                                name="currency"
                                x-model="slotForm.currency"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                @foreach (['SAR'] as $cur)
                                    <option value="{{ $cur }}">{{ $cur }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 pt-2">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" x-model="slotForm.is_active" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900 w-4 h-4">
                            <span>Active</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="closeSlotModal()" class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button 
                            type="submit" 
                            :disabled="submittingSlotForm"
                            class="bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow-sm"
                        >
                            <span x-text="submittingSlotForm ? 'Saving...' : 'Save Slot'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Hotel Confirmation Modal --}}
    <div 
        x-show="deleteModalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="cancelDeleteHotel()"></div>

            <div class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Delete Hotel?</h3>
                        <p class="text-xs text-gray-500 mt-0.5">This action cannot be undone.</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 mb-6 leading-relaxed">
                    Are you sure you want to delete <span class="font-bold text-gray-900" x-text="hotelToDelete ? ('\x22' + hotelToDelete.name + '\x22') : 'this hotel'"></span> and all of its associated room slots?
                </p>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="cancelDeleteHotel()" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        @click="deleteHotel()" 
                        :disabled="deletingHotel"
                        class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center gap-2 shadow-sm"
                    >
                        <span x-text="deletingHotel ? 'Deleting...' : 'Delete Hotel'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function adminHotels() {
    return {
        baseUrl: '{{ url("admin/hotels") }}',
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
        
        deleteModalOpen: false,
        hotelToDelete: null,
        deletingHotel: false,
        
        showAddLocationInput: false,
        newLocationName: '',
        savingLocation: false,

        showAmenityDropdown: false,
        amenitySearchQuery: '',
        newAmenityName: '',
        savingAmenity: false,

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

        init() {},

        getStarArray(count) {
            const num = parseInt(count) || 1;
            return Array.from({ length: Math.min(Math.max(num, 1), 5) }, (_, i) => i + 1);
        },

        getUnselectedAmenities() {
            const selected = (this.form.amenities || []).map(a => String(a).toLowerCase());
            const query = (this.amenitySearchQuery || '').trim().toLowerCase();
            return (this.availableAmenitiesList || []).filter(item => {
                const name = String(item.name || '').toLowerCase();
                const isNotSelected = !selected.includes(name);
                const matchesQuery = query === '' || name.includes(query);
                return isNotSelected && matchesQuery;
            });
        },

        addAmenity(name) {
            if (name && !this.form.amenities.includes(name)) {
                this.form.amenities.push(name);
            }
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

                const response = await fetch(`${this.baseUrl}?${params.toString()}`, {
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
            this.amenitySearchQuery = '';
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
            this.amenitySearchQuery = '';
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

        async saveNewLocation() {
            if (!this.newLocationName.trim()) return;
            this.savingLocation = true;

            try {
                const response = await fetch(`${this.baseUrl}/locations`, {
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

        async saveNewAmenity(customName = null) {
            const name = (customName || this.amenitySearchQuery || '').trim();
            if (!name) return;
            this.savingAmenity = true;

            try {
                const response = await fetch(`${this.baseUrl}/amenities`, {
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
                    this.amenitySearchQuery = '';
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
                ? `${this.baseUrl}/${this.editingHotelId}`
                : `${this.baseUrl}`;

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
                const response = await fetch(`${this.baseUrl}/${hotel.id}/toggle-status`, {
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

        confirmDeleteHotel(hotel) {
            this.hotelToDelete = hotel;
            this.deleteModalOpen = true;
        },

        cancelDeleteHotel() {
            this.deleteModalOpen = false;
            this.hotelToDelete = null;
            this.deletingHotel = false;
        },

        async deleteHotel() {
            if (!this.hotelToDelete) return;
            this.deletingHotel = true;

            try {
                const response = await fetch(`${this.baseUrl}/${this.hotelToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    const deletedId = this.hotelToDelete.id;
                    this.hotelsList = this.hotelsList.filter(h => h.id !== deletedId);
                    this.successMessage = data.message || 'Hotel deleted successfully.';
                    this.cancelDeleteHotel();
                } else {
                    alert(data.message || 'Failed to delete hotel.');
                }
            } catch (err) {
                console.error('Delete hotel failed', err);
                alert('Failed to delete hotel.');
            } finally {
                this.deletingHotel = false;
            }
        },

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
                ? `${this.baseUrl}/slots/${this.editingSlotId}`
                : `${this.baseUrl}/${this.targetHotelForSlot.id}/slots`;

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
                const response = await fetch(`${this.baseUrl}/slots/${slot.id}/toggle-status`, {
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
