@extends('layouts.dashboard')

@section('page-title', 'Transfers')

@section('content')

<div x-data="agentTransfers()" x-init="init()">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transfers</h1>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1.5 mb-5 bg-gray-100/80 p-1 rounded-lg w-fit">
        <button 
            type="button"
            @click="switchTab('city')"
            :class="activeTab === 'city' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'"
            class="px-4 py-2 text-sm rounded-md transition-all">
            City-to-City Rates
        </button>
        <button 
            type="button"
            @click="switchTab('airport')"
            :class="activeTab === 'airport' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'"
            class="px-4 py-2 text-sm rounded-md transition-all">
            Airport Rates
        </button>
        <button 
            type="button"
            @click="switchTab('fullday')"
            :class="activeTab === 'fullday' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'"
            class="px-4 py-2 text-sm rounded-md transition-all">
            Full-day Booking
        </button>
    </div>

    {{-- Success Toast Notification --}}
    <div 
        x-show="successMessage" 
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
            <span x-text="successMessage" class="leading-snug"></span>
        </div>
        <button type="button" @click="successMessage = ''" class="text-emerald-200 hover:text-white p-1 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm mb-6">
        <form @submit.prevent="fetchTransfers()" class="space-y-4">
            
            {{-- City-to-City Filters --}}
            <div x-show="activeTab === 'city'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">From City *</label>
                    <select x-model="filters.city.from_location_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">To City *</label>
                    <select x-model="filters.city.to_location_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Vehicle Type</label>
                    <select x-model="filters.city.vehicle_type_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($vehicleTypes as $vt)
                            <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Fare Type</label>
                    <select x-model="filters.city.fare_type" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">All</option>
                        <option value="fixed">Fixed</option>
                        <option value="per_km">Per KM</option>
                        <option value="per_hr">Per Hour</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Date</label>
                    <input type="date" x-model="filters.city.date" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="dd-mm-yyyy" />
                </div>
                <div>
                    <button type="button" @click="fetchTransfers()" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Search</span>
                    </button>
                </div>
            </div>

            {{-- Airport Rates Filters --}}
            <div x-show="activeTab === 'airport'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3.5 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Airport *</label>
                    <select x-model="filters.airport.airport_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select Airport</option>
                        @foreach($airports as $ap)
                            <option value="{{ $ap->id }}">{{ $ap->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Zone</label>
                    <select x-model="filters.airport.zone_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select Zone</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}">{{ $z->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Transfer Type</label>
                    <select x-model="filters.airport.transfer_type" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">All Types</option>
                        <option value="pickup">Pickup</option>
                        <option value="drop">Drop</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Vehicle Type</label>
                    <select x-model="filters.airport.vehicle_type_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($vehicleTypes as $vt)
                            <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Fare Type</label>
                    <select x-model="filters.airport.fare_type" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">All</option>
                        <option value="fixed">Fixed</option>
                        <option value="per_km">Per KM</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Max Price</label>
                    <input type="number" x-model="filters.airport.max_price" placeholder="e.g. 500" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900" />
                </div>
                <div>
                    <button type="button" @click="fetchTransfers()" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Search</span>
                    </button>
                </div>
            </div>

            {{-- Full-day Booking Filters --}}
            <div x-show="activeTab === 'fullday'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">From City *</label>
                    <select x-model="filters.fullday.from_location_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">To City *</label>
                    <select x-model="filters.fullday.to_location_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Vehicle Type</label>
                    <select x-model="filters.fullday.vehicle_type_id" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">Select</option>
                        @foreach($vehicleTypes as $vt)
                            <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Fare Type</label>
                    <select x-model="filters.fullday.fare_type" @change="fetchTransfers()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">All (Half / Full)</option>
                        <option value="half_day">Half Day</option>
                        <option value="full_day">Full Day</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Max Price</label>
                    <input type="number" x-model="filters.fullday.max_price" placeholder="e.g. 1000" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900" />
                </div>
                <div>
                    <button type="button" @click="fetchTransfers()" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Search</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Listing Section --}}
    <div class="relative min-h-[250px]">
        
        {{-- Skeleton Loaders --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="i in [1,2,3,4]" :key="i">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm animate-pulse flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-5 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-6 bg-gray-200 rounded-full w-16"></div>
                        </div>
                        <div class="h-3 bg-gray-100 rounded w-3/4 mb-6"></div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                        <div class="h-9 bg-gray-200 rounded-md w-28"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Transfer Cards Grid --}}
        <div x-show="!loading && transfers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="item in transfers" :key="item.id">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        {{-- Top title & badge --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="font-bold text-gray-900 text-base" x-text="item.title"></h3>
                            <span class="shrink-0 bg-[#0B1527] text-white text-[11px] font-semibold px-3 py-1 rounded-full uppercase tracking-wider" x-text="item.badge"></span>
                        </div>

                        {{-- Notes --}}
                        <p class="text-xs text-gray-500 mb-5" x-text="item.notes"></p>
                    </div>

                    {{-- Bottom price & action --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-lg font-bold text-gray-900" x-text="item.price + ' ' + item.currency"></span>
                        </div>
                        <button 
                            type="button" 
                            @click="openSummary(item)"
                            class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-800 rounded-lg text-xs font-semibold transition-colors">
                            View Summary
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && transfers.length === 0" class="bg-white border border-gray-100 rounded-xl p-12 text-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v11.135m12 0a2.25 2.25 0 01-4.5 0" />
            </svg>
            <h4 class="text-base font-semibold text-gray-900 mb-1">No transfers found</h4>
            <p class="text-xs text-gray-500">Try adjusting your filters to find available transfer rates.</p>
        </div>

    </div>

    {{-- Bottom-Up Summary Sheet Drawer --}}
    <div 
        x-show="drawerOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 z-50 flex items-end justify-center"
        style="display: none;"
    >
        {{-- Drawer Card --}}
        <div 
            @click.away="drawerOpen = false"
            x-show="drawerOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="bg-white rounded-t-2xl shadow-2xl w-full max-w-3xl p-6 relative border-t border-gray-100"
        >
            {{-- Top Drag Handle --}}
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-4"></div>

            {{-- Close Button in Top-Right Corner --}}
            <button 
                type="button" 
                @click="drawerOpen = false"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-colors"
                title="Close"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Header --}}
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900">Cost Summary</h2>
                <p class="text-xs text-gray-400 mt-0.5">Transfer rate details</p>
            </div>

            {{-- Selected Transfer Details --}}
            <template x-if="selectedItem">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-xs py-3 border-y border-gray-100">
                        <div>
                            <span class="text-gray-400 block mb-0.5">Route:</span>
                            <span class="font-semibold text-gray-900" x-text="selectedItem.route_label"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">Vehicle:</span>
                            <span class="font-semibold text-gray-900" x-text="selectedItem.vehicle"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">Fare Type:</span>
                            <span class="font-semibold text-gray-900 capitalize" x-text="selectedItem.fare_type"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">Base Price:</span>
                            <span class="font-semibold text-gray-900" x-text="selectedItem.price + ' ' + selectedItem.currency"></span>
                        </div>
                    </div>

                    {{-- Total Section --}}
                    <div class="flex items-center justify-between py-2">
                        <span class="text-base font-bold text-gray-900">Total</span>
                        <span class="text-xl font-extrabold text-gray-900" x-text="selectedItem.price + ' ' + selectedItem.currency"></span>
                    </div>

                    {{-- Send Enquiry Action Button --}}
                    <div class="pt-2">
                        <button 
                            type="button"
                            @click="openEnquiryModal()"
                            class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm py-3 rounded-lg transition-colors shadow-sm inline-flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3 21l18-9L3 3l3 9zm0 0h7" />
                            </svg>
                            <span>Send Enquiry</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Transfer Enquiry Form Modal --}}
    <div 
        x-show="enquiryModalOpen" 
        x-transition
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.away="enquiryModalOpen = false"
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative"
        >
            <button 
                type="button" 
                @click="enquiryModalOpen = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="mb-5">
                <h3 class="text-xl font-bold text-gray-900">Transfer Booking Enquiry</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Provide customer details to send enquiry for 
                    <strong class="text-gray-900" x-text="selectedItem ? selectedItem.title : ''"></strong> 
                    (<span x-text="selectedItem ? selectedItem.route_label : ''"></span>).
                </p>
            </div>

            {{-- Total Summary Box --}}
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3.5 mb-5 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-400 block">Total Price:</span>
                    <span class="text-lg font-bold text-gray-900" x-text="selectedItem ? (selectedItem.price + ' ' + selectedItem.currency) : ''"></span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400 block">Vehicle:</span>
                    <span class="text-xs font-semibold text-gray-800" x-text="selectedItem ? selectedItem.vehicle : ''"></span>
                </div>
            </div>

            <form @submit.prevent="submitEnquiry()" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Customer Name *</label>
                    <input 
                        type="text" 
                        x-model="enquiryForm.customer_name" 
                        required
                        placeholder="John Doe"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                    />
                    <template x-if="enquiryErrors.customer_name">
                        <p class="text-xs text-red-500 mt-1" x-text="enquiryErrors.customer_name[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Date of Birth *</label>
                    <input 
                        type="date" 
                        x-model="enquiryForm.date_of_birth" 
                        max="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                        :max="maxDob"
                        required
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                    />
                    <template x-if="enquiryErrors.date_of_birth">
                        <p class="text-xs text-red-500 mt-1" x-text="enquiryErrors.date_of_birth[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-900 mb-1.5">Passport Number *</label>
                    <input 
                        type="text" 
                        x-model="enquiryForm.passport_number" 
                        required
                        placeholder="A1234567"
                        class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 uppercase"
                    />
                    <template x-if="enquiryErrors.passport_number">
                        <p class="text-xs text-red-500 mt-1" x-text="enquiryErrors.passport_number[0]"></p>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Pickup Date</label>
                        <input 
                            type="date" 
                            x-model="enquiryForm.pickup_date" 
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                        <template x-if="enquiryErrors.pickup_date">
                            <p class="text-xs text-red-500 mt-1" x-text="enquiryErrors.pickup_date[0]"></p>
                        </template>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1.5">Pickup Time</label>
                        <input 
                            type="time" 
                            x-model="enquiryForm.pickup_time" 
                            class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        />
                        <template x-if="enquiryErrors.pickup_time">
                            <p class="text-xs text-red-500 mt-1" x-text="enquiryErrors.pickup_time[0]"></p>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="enquiryModalOpen = false"
                        class="px-4 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        :disabled="submittingEnquiry"
                        class="px-5 py-2.5 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50 inline-flex items-center gap-2"
                    >
                        <span x-show="!submittingEnquiry">Submit Enquiry</span>
                        <span x-show="submittingEnquiry">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function agentTransfers() {
    return {
        activeTab: @json($tab),
        loading: false,
        transfers: @json($rates->values()),
        drawerOpen: false,
        enquiryModalOpen: false,
        selectedItem: null,
        maxDob: (function() {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            return yesterday.toISOString().split('T')[0];
        })(),
        submittingEnquiry: false,
        successMessage: '',
        enquiryErrors: {},
        enquiryForm: {
            customer_name: '',
            date_of_birth: '',
            passport_number: '',
            pickup_date: '',
            pickup_time: ''
        },
        filters: {
            city: {
                from_location_id: '',
                to_location_id: '',
                vehicle_type_id: '',
                fare_type: '',
                date: ''
            },
            airport: {
                airport_id: '',
                zone_id: '',
                transfer_type: '',
                vehicle_type_id: '',
                fare_type: '',
                max_price: ''
            },
            fullday: {
                from_location_id: '',
                to_location_id: '',
                vehicle_type_id: '',
                fare_type: '',
                max_price: ''
            }
        },

        init() {
            // initial load
        },

        switchTab(tabName) {
            this.activeTab = tabName;
            this.fetchTransfers();
        },

        async fetchTransfers() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                params.append('tab', this.activeTab);

                const currentFilters = this.filters[this.activeTab] || {};
                Object.keys(currentFilters).forEach(key => {
                    if (currentFilters[key]) {
                        params.append(key, currentFilters[key]);
                    }
                });

                const response = await fetch(`{{ route('agent.transfers.search') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.transfers = data.transfers;
                }
            } catch (err) {
                console.error('Failed to load transfers', err);
            } finally {
                this.loading = false;
            }
        },

        openSummary(item) {
            this.selectedItem = item;
            this.drawerOpen = true;
        },

        openEnquiryModal() {
            this.enquiryErrors = {};
            const currentFilter = this.filters[this.activeTab] || {};
            if (currentFilter.date) {
                this.enquiryForm.pickup_date = currentFilter.date;
            }
            if (currentFilter.time) {
                this.enquiryForm.pickup_time = currentFilter.time;
            }
            this.enquiryModalOpen = true;
        },

        async submitEnquiry() {
            if (!this.selectedItem) return;
            this.submittingEnquiry = true;
            this.enquiryErrors = {};

            const todayStr = new Date().toISOString().split('T')[0];
            if (this.enquiryForm.date_of_birth && this.enquiryForm.date_of_birth >= todayStr) {
                this.enquiryErrors.date_of_birth = ['Date of birth must be a past date.'];
                this.submittingEnquiry = false;
                return;
            }

            try {
                const payload = {
                    _token: '{{ csrf_token() }}',
                    customer_name: this.enquiryForm.customer_name,
                    date_of_birth: this.enquiryForm.date_of_birth,
                    passport_number: this.enquiryForm.passport_number,
                    title: this.selectedItem.title,
                    route_label: this.selectedItem.route_label,
                    vehicle: this.selectedItem.vehicle,
                    transfer_type_category: this.selectedItem.tab,
                    rate_id: this.selectedItem.id,
                    total_price: this.selectedItem.price,
                    currency: this.selectedItem.currency,
                    pickup_date: this.enquiryForm.pickup_date || null,
                    pickup_time: this.enquiryForm.pickup_time || null
                };

                const response = await fetch('{{ route('agent.transfers.enquire') }}', {
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
                    this.enquiryModalOpen = false;
                    this.drawerOpen = false;
                    this.successMessage = `Enquiry ${data.reference} submitted successfully! You can view it under Booking Enquiries.`;
                    setTimeout(() => { this.successMessage = ''; }, 5000);
                    this.enquiryForm.customer_name = '';
                    this.enquiryForm.date_of_birth = '';
                    this.enquiryForm.passport_number = '';
                } else if (data.errors) {
                    this.enquiryErrors = data.errors;
                } else {
                    alert(data.message || 'Something went wrong. Please try again.');
                }
            } catch (err) {
                console.error('Enquiry submission failed', err);
                alert('Submission failed. Please try again.');
            } finally {
                this.submittingEnquiry = false;
            }
        }
    };
}
</script>
@endpush

@endsection
