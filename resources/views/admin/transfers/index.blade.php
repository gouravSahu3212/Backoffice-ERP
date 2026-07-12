@extends('layouts.dashboard')

@section('page-title', 'Transfer Rates')

@section('content')

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transfer Rates</h1>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-0 mb-5 border border-gray-200 rounded-lg w-fit overflow-hidden">
        <a href="{{ route('admin.transfers.index', ['tab' => 'city']) }}"
            id="tab-city"
            class="tab-btn px-5 py-2 text-sm font-semibold transition-colors
                {{ $tab === 'city' ? 'bg-white text-gray-900 shadow-sm' : 'bg-gray-50 text-gray-400 hover:text-gray-700' }}">
            City-to-City Rates
        </a>
        <a href="{{ route('admin.transfers.index', ['tab' => 'airport']) }}"
            id="tab-airport"
            class="tab-btn px-5 py-2 text-sm font-semibold transition-colors
                {{ $tab === 'airport' ? 'bg-white text-gray-900 shadow-sm' : 'bg-gray-50 text-gray-400 hover:text-gray-700' }}">
            Airport Rates
        </a>
    </div>

    {{-- ======================================================
         CITY-TO-CITY TAB
         ====================================================== --}}
    <div id="panel-city" class="{{ $tab === 'city' ? '' : 'hidden' }}">

        {{-- Toolbar --}}
        <div class="flex justify-end mb-4">
            <button id="open-rate-modal" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Rate
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">From</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">To</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Vehicle</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Fare</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Price</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Currency</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Status</th>
                        <th class="text-right py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody id="city-rates-tbody">
                    @forelse ($rates as $rate)
                        @include('admin.transfers._city_rate_row', ['rate' => $rate])
                    @empty
                        <tr id="empty-state-row">
                            <td colspan="8" class="py-16 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <p class="text-gray-400 text-sm">No rates yet. Click <strong>Add Rate</strong> to create your first one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($rates->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $rates->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ======================================================
         AIRPORT TAB
         ====================================================== --}}
    <div id="panel-airport" class="{{ $tab === 'airport' ? '' : 'hidden' }}">

        {{-- Toolbar --}}
        <div class="flex justify-end mb-4">
            <button id="open-airport-rate-modal" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Rate
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Airport</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Type</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Zone</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Vehicle</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Price</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Currency</th>
                        <th class="text-left py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Status</th>
                        <th class="text-right py-3.5 px-4 text-xs font-semibold uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody id="airport-rates-tbody">
                    @forelse ($airportRates as $rate)
                        @include('admin.transfers._airport_rate_row', ['rate' => $rate])
                    @empty
                        <tr id="airport-empty-state-row">
                            <td colspan="8" class="py-16 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <p class="text-gray-400 text-sm">No airport rates yet. Click <strong>Add Rate</strong> to create your first one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($airportRates->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $airportRates->links() }}
                </div>
            @endif
        </div>

    </div>


    {{-- ======================================================
         ADD / EDIT RATE MODAL
         ====================================================== --}}
    <div id="rate-modal" class="fixed inset-0 z-50 hidden items-center justify-center" aria-modal="true" role="dialog">

        {{-- Backdrop --}}
        <div id="rate-modal-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div class="flex items-center justify-between px-7 pt-7 pb-4 border-b border-gray-100 flex-shrink-0">
                <h2 id="rate-modal-title" class="text-xl font-bold text-gray-900">Add City-to-City Rate</h2>
                <button id="close-rate-modal" type="button"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Scrollable body --}}
            <div class="overflow-y-auto flex-1 px-7 py-5">
                <form id="rate-form" class="space-y-4">
                    @csrf
                    <input type="hidden" id="rate-id" name="rate_id" value="">

                    {{-- From City --}}
                    <div>
                        <label for="from-location" class="block text-sm font-medium text-gray-700 mb-1.5">
                            From City <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="from-location" name="from_location_id"
                                class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">Select</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" data-target="from-location" data-type="city"
                                class="add-inline-location flex-shrink-0 px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                title="Add new city">+ New</button>
                        </div>
                        <p class="field-error text-red-500 text-xs mt-1 hidden" data-field="from_location_id"></p>
                    </div>

                    {{-- To City --}}
                    <div>
                        <label for="to-location" class="block text-sm font-medium text-gray-700 mb-1.5">
                            To City <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="to-location" name="to_location_id"
                                class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">Select</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" data-target="to-location" data-type="city"
                                class="add-inline-location flex-shrink-0 px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                title="Add new city">+ New</button>
                        </div>
                        <p class="field-error text-red-500 text-xs mt-1 hidden" data-field="to_location_id"></p>
                    </div>

                    {{-- Vehicle Type --}}
                    <div>
                        <label for="vehicle-type" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Vehicle Type <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="vehicle-type" name="vehicle_type_id"
                                class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">Select</option>
                                @foreach ($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="add-vehicle-type-btn"
                                class="flex-shrink-0 px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                title="Add new vehicle type">+ New</button>
                        </div>
                        <p class="field-error text-red-500 text-xs mt-1 hidden" data-field="vehicle_type_id"></p>
                    </div>

                    {{-- Fare Type + Price + Currency --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label for="fare-type" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Fare Type <span class="text-red-500">*</span>
                            </label>
                            <select id="fare-type" name="fare_type"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="fixed">fixed</option>
                                <option value="per_km">per km</option>
                                <option value="per_hr">per hr</option>
                            </select>
                            <p class="field-error text-red-500 text-xs mt-1 hidden" data-field="fare_type"></p>
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <input id="price" type="number" name="price" value="0" min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                            <p class="field-error text-red-500 text-xs mt-1 hidden" data-field="price"></p>
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Currency
                            </label>
                            <select id="currency" name="currency"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="AED">AED</option>
                                <option value="USD">USD</option>
                                <option value="SAR">SAR</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="rate-status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="rate-status" name="is_active"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                        <textarea id="notes" name="notes" rows="2"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y"></textarea>
                    </div>

                    {{-- Global error --}}
                    <p id="form-error" class="text-red-500 text-sm hidden"></p>

                </form>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-7 py-4 border-t border-gray-100 flex-shrink-0">
                <button type="button" id="cancel-rate-modal"
                    class="px-5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" id="save-rate-btn"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="save-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Save
                </button>
            </div>

        </div>
    </div>

    {{-- ======================================================
         ADD NEW LOCATION MINI MODAL
         ====================================================== --}}
    <div id="new-location-modal" class="fixed inset-0 z-[60] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Location</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input id="new-location-name" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition"
                        placeholder="e.g. Dubai Marina" />
                </div>
                <input type="hidden" id="new-location-type" value="city" />
                <input type="hidden" id="new-location-target" value="" />
                <p id="new-location-error" class="text-red-500 text-xs hidden"></p>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" id="cancel-new-location"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" id="save-new-location"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="new-location-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Add
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================
         ADD NEW VEHICLE TYPE MINI MODAL
         ====================================================== --}}
    <div id="new-vehicle-modal" class="fixed inset-0 z-[60] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Vehicle Type</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input id="new-vehicle-name" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition"
                        placeholder="e.g. Limousine" />
                </div>
                <p id="new-vehicle-error" class="text-red-500 text-xs hidden"></p>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" id="cancel-new-vehicle"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" id="save-new-vehicle"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="new-vehicle-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Add
                </button>
            </div>
        </div>
    </div>


    {{-- ======================================================
         ADD / EDIT AIRPORT RATE MODAL
         ====================================================== --}}
    <div id="airport-rate-modal" class="fixed inset-0 z-50 hidden items-center justify-center" aria-modal="true" role="dialog">

        {{-- Backdrop --}}
        <div id="airport-rate-modal-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div class="flex items-center justify-between px-7 pt-7 pb-4 border-b border-gray-100 flex-shrink-0">
                <h2 id="airport-rate-modal-title" class="text-xl font-bold text-gray-900">Add Airport Rate</h2>
                <button id="close-airport-rate-modal" type="button"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Scrollable body --}}
            <div class="overflow-y-auto flex-1 px-7 py-5">
                <form id="airport-rate-form" class="space-y-4">
                    @csrf
                    <input type="hidden" id="airport-rate-id" name="rate_id" value="">

                    {{-- Airport --}}
                    <div>
                        <label for="airport-select" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Airport <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="airport-select" name="airport_id"
                                class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">Select</option>
                                @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}">{{ $airport->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="add-airport-btn"
                                class="flex-shrink-0 px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                title="Add new airport">+ New</button>
                        </div>
                        <p class="field-error-airport text-red-500 text-xs mt-1 hidden" data-field="airport_id"></p>
                    </div>


                    {{-- Transfer Type + Zone --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="transfer-type-select" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Transfer Type <span class="text-red-500">*</span>
                            </label>
                            <select id="transfer-type-select" name="transfer_type"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="pickup">Pickup</option>
                                <option value="drop">Drop</option>
                            </select>
                            <p class="field-error-airport text-red-500 text-xs mt-1 hidden" data-field="transfer_type"></p>
                        </div>
                        <div>
                            <label for="zone-select" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Zone <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select id="zone-select" name="zone_id"
                                    class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                    <option value="">Select</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="add-zone-btn"
                                    class="flex-shrink-0 px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                    title="Add new zone">+ New</button>
                            </div>
                            <p class="field-error-airport text-red-500 text-xs mt-1 hidden" data-field="zone_id"></p>
                        </div>
                    </div>

                    {{-- Vehicle Type --}}
                    <div>
                        <label for="airport-vehicle-type" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Vehicle Type <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select id="airport-vehicle-type" name="vehicle_type_id"
                                class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">Select</option>
                                @foreach ($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="add-airport-vehicle-type-btn"
                                class="flex-shrink-0 px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                                title="Add new vehicle type">+ New</button>
                        </div>
                        <p class="field-error-airport text-red-500 text-xs mt-1 hidden" data-field="vehicle_type_id"></p>
                    </div>

                    {{-- Fare Type + Price + Currency --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label for="airport-fare-type" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Fare Type <span class="text-red-500">*</span>
                            </label>
                            <select id="airport-fare-type" name="fare_type"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="fixed">fixed</option>
                                <option value="per_km">per km</option>
                                <option value="per_hr">per hr</option>
                            </select>
                            <p class="field-error-airport text-red-500 text-xs mt-1 hidden" data-field="fare_type"></p>
                        </div>
                        <div>
                            <label for="airport-price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <input id="airport-price" type="number" name="price" value="0" min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                            <p class="field-error-airport text-red-500 text-xs mt-1 hidden" data-field="price"></p>
                        </div>
                        <div>
                            <label for="airport-currency" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Currency
                            </label>
                            <select id="airport-currency" name="currency"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="AED">AED</option>
                                <option value="USD">USD</option>
                                <option value="SAR">SAR</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="airport-notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                        <textarea id="airport-notes" name="notes" rows="2"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y"></textarea>
                    </div>

                    {{-- Global error --}}
                    <p id="airport-form-error" class="text-red-500 text-sm hidden"></p>

                </form>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-7 py-4 border-t border-gray-100 flex-shrink-0">
                <button type="button" id="cancel-airport-rate-modal"
                    class="px-5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" id="save-airport-rate-btn"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="airport-save-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Save
                </button>
            </div>

        </div>
    </div>

    {{-- ======================================================
         ADD NEW ZONE MINI MODAL
         ====================================================== --}}
    <div id="new-zone-modal" class="fixed inset-0 z-[60] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Zone</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input id="new-zone-name" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition"
                        placeholder="e.g. Palm Jumeirah" />
                </div>
                <p id="new-zone-error" class="text-red-500 text-xs hidden"></p>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" id="cancel-new-zone"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" id="save-new-zone"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="new-zone-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Add
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================
         ADD NEW AIRPORT VEHICLE TYPE MINI MODAL
         ====================================================== --}}
    <div id="new-airport-vehicle-modal" class="fixed inset-0 z-[60] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Vehicle Type</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input id="new-airport-vehicle-name" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition"
                        placeholder="e.g. Limousine" />
                </div>
                <p id="new-airport-vehicle-error" class="text-red-500 text-xs hidden"></p>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" id="cancel-new-airport-vehicle"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" id="save-new-airport-vehicle"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="new-airport-vehicle-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Add
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================
         ADD NEW AIRPORT MINI MODAL
         ====================================================== --}}
    <div id="new-airport-modal" class="fixed inset-0 z-[60] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Airport</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input id="new-airport-name" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition"
                        placeholder="e.g. Al Maktoum International (DWC)" />
                </div>
                <p id="new-airport-error" class="text-red-500 text-xs hidden"></p>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" id="cancel-new-airport"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" id="save-new-airport"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg id="new-airport-spinner" class="hidden animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                    </svg>
                    Add
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Routes ──────────────────────────────────────────────────────────────
    const ROUTES = {
        store:        '{{ route('admin.transfers.city-rates.store') }}',
        update:       (id) => `{{ url('admin/transfers/city-rates') }}/${id}`,
        toggle:       (id) => `{{ url('admin/transfers/city-rates') }}/${id}/toggle-status`,
        destroy:      (id) => `{{ url('admin/transfers/city-rates') }}/${id}`,
        storeLocation: '{{ route('admin.transfers.locations.store') }}',
        storeVehicle:  '{{ route('admin.transfers.vehicle-types.store') }}',
    };

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Helpers ──────────────────────────────────────────────────────────────
    function showModal(el) {
        el.classList.remove('hidden');
        el.classList.add('flex');
    }
    function hideModal(el) {
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    async function apiFetch(url, method, body = null) {
        const opts = {
            method,
            headers: {
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
        };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        const res = await fetch(url, opts);
        return res.json();
    }

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        const ge = document.getElementById('form-error');
        if (ge) { ge.textContent = ''; ge.classList.add('hidden'); }
    }

    function showErrors(errors) {
        clearErrors();
        if (typeof errors === 'object') {
            Object.entries(errors).forEach(([field, msgs]) => {
                const el = document.querySelector(`.field-error[data-field="${field}"]`);
                if (el) {
                    el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    el.classList.remove('hidden');
                }
            });
        } else {
            const ge = document.getElementById('form-error');
            if (ge) { ge.textContent = errors; ge.classList.remove('hidden'); }
        }
    }

    function setSelectBorder(selectEl, hasError) {
        selectEl.classList.toggle('border-red-400', hasError);
        selectEl.classList.toggle('border-gray-200', !hasError);
    }

    // ── Main Rate Modal ───────────────────────────────────────────────────────
    const rateModal     = document.getElementById('rate-modal');
    const modalTitle    = document.getElementById('rate-modal-title');
    const rateIdInput   = document.getElementById('rate-id');
    const saveBtn       = document.getElementById('save-rate-btn');
    const saveSpinner   = document.getElementById('save-spinner');

    // Form fields
    const fromSel    = document.getElementById('from-location');
    const toSel      = document.getElementById('to-location');
    const vehicleSel = document.getElementById('vehicle-type');
    const fareSel    = document.getElementById('fare-type');
    const priceIn    = document.getElementById('price');
    const currSel    = document.getElementById('currency');
    const statusSel  = document.getElementById('rate-status');
    const notesIn    = document.getElementById('notes');

    function resetModal() {
        rateIdInput.value = '';
        fromSel.value    = '';
        toSel.value      = '';
        vehicleSel.value = '';
        fareSel.value    = 'fixed';
        priceIn.value    = '0';
        currSel.value    = 'AED';
        statusSel.value  = '1';
        notesIn.value    = '';
        clearErrors();
    }

    function openAddModal() {
        resetModal();
        modalTitle.textContent = 'Add City-to-City Rate';
        showModal(rateModal);
        fromSel.focus();
    }

    function openEditModal(btn) {
        resetModal();
        modalTitle.textContent    = 'Edit City-to-City Rate';
        rateIdInput.value         = btn.dataset.id;
        fromSel.value             = btn.dataset.from;
        toSel.value               = btn.dataset.to;
        vehicleSel.value          = btn.dataset.vehicle;
        fareSel.value             = btn.dataset.fare;
        priceIn.value             = btn.dataset.price;
        currSel.value             = btn.dataset.currency;
        statusSel.value           = btn.dataset.isActive;
        notesIn.value             = btn.dataset.notes ?? '';
        showModal(rateModal);
    }

    document.getElementById('open-rate-modal').addEventListener('click', openAddModal);
    document.getElementById('close-rate-modal').addEventListener('click', () => hideModal(rateModal));
    document.getElementById('cancel-rate-modal').addEventListener('click', () => hideModal(rateModal));
    document.getElementById('rate-modal-backdrop').addEventListener('click', () => hideModal(rateModal));

    // Delegated edit button handler
    document.getElementById('city-rates-tbody').addEventListener('click', (e) => {
        const btn = e.target.closest('.edit-rate-btn');
        if (btn) { openEditModal(btn); }
    });

    // Save (store or update)
    saveBtn.addEventListener('click', async () => {
        clearErrors();
        const id = rateIdInput.value;
        const payload = {
            from_location_id: fromSel.value,
            to_location_id:   toSel.value,
            vehicle_type_id:  vehicleSel.value,
            fare_type:        fareSel.value,
            price:            priceIn.value,
            currency:         currSel.value,
            is_active:        statusSel.value,
            notes:            notesIn.value,
        };

        saveBtn.disabled = true;
        saveSpinner.classList.remove('hidden');

        try {
            let data;
            if (id) {
                data = await apiFetch(ROUTES.update(id), 'PUT', payload);
            } else {
                data = await apiFetch(ROUTES.store, 'POST', payload);
            }

            if (data.success) {
                const tbody = document.getElementById('city-rates-tbody');

                if (id) {
                    // Replace existing row
                    const existingRow = tbody.querySelector(`tr[data-rate-id="${id}"]`);
                    if (existingRow) {
                        existingRow.outerHTML = data.row_html;
                    }
                } else {
                    // Remove empty state row if present
                    const emptyRow = tbody.querySelector('#empty-state-row');
                    if (emptyRow) { emptyRow.remove(); }
                    // Prepend new row
                    tbody.insertAdjacentHTML('afterbegin', data.row_html);
                }

                hideModal(rateModal);
            } else if (data.errors) {
                showErrors(data.errors);
            } else {
                showErrors(data.message ?? 'An error occurred.');
            }
        } catch (err) {
            showErrors('Network error. Please try again.');
        } finally {
            saveBtn.disabled = false;
            saveSpinner.classList.add('hidden');
        }
    });

    // ── Toggle Status ─────────────────────────────────────────────────────────
    document.getElementById('city-rates-tbody').addEventListener('click', async (e) => {
        const btn = e.target.closest('.toggle-status-btn');
        if (!btn) { return; }

        const icon    = btn.querySelector('.toggle-icon');
        const spinner = btn.querySelector('.toggle-spinner');

        btn.disabled = true;
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');

        try {
            const data = await apiFetch(ROUTES.toggle(btn.dataset.id), 'PATCH');
            if (data.success) {
                const row   = btn.closest('tr');
                const badge = row.querySelector('.rate-status-badge');

                if (data.is_active) {
                    badge.textContent = 'active';
                    badge.className   = 'rate-status-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-900 text-white';
                } else {
                    badge.textContent = 'inactive';
                    badge.className   = 'rate-status-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-500';
                }

                btn.dataset.active = data.is_active ? '1' : '0';
                btn.title = data.is_active ? 'Deactivate' : 'Activate';
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
            icon.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    });

    // ── Add New Location Mini Modal ───────────────────────────────────────────
    const newLocModal   = document.getElementById('new-location-modal');
    const newLocName    = document.getElementById('new-location-name');
    const newLocType    = document.getElementById('new-location-type');
    const newLocTarget  = document.getElementById('new-location-target');
    const newLocError   = document.getElementById('new-location-error');
    const newLocSpinner = document.getElementById('new-location-spinner');

    document.querySelectorAll('.add-inline-location').forEach(btn => {
        btn.addEventListener('click', () => {
            newLocName.value   = '';
            newLocType.value   = btn.dataset.type;
            newLocTarget.value = btn.dataset.target;
            newLocError.classList.add('hidden');
            showModal(newLocModal);
            newLocName.focus();
        });
    });

    document.getElementById('cancel-new-location').addEventListener('click', () => hideModal(newLocModal));

    document.getElementById('save-new-location').addEventListener('click', async () => {
        const name   = newLocName.value.trim();
        const type   = newLocType.value;
        const target = newLocTarget.value;

        if (!name) {
            newLocError.textContent = 'Name is required.';
            newLocError.classList.remove('hidden');
            return;
        }

        newLocSpinner.classList.remove('hidden');

        try {
            const data = await apiFetch(ROUTES.storeLocation, 'POST', { name, type });
            if (data.success) {
                const loc = data.location;
                const option = new Option(loc.name, loc.id);

                // Add to both from and to selects
                [fromSel, toSel].forEach(sel => {
                    sel.add(option.cloneNode(true));
                });

                // Select in the targeted select
                const targetSel = document.getElementById(target);
                if (targetSel) { targetSel.value = loc.id; }

                hideModal(newLocModal);
            } else {
                newLocError.textContent = data.message ?? 'Error saving location.';
                newLocError.classList.remove('hidden');
            }
        } catch (err) {
            newLocError.textContent = 'Network error.';
            newLocError.classList.remove('hidden');
        } finally {
            newLocSpinner.classList.add('hidden');
        }
    });

    // ── Add New Vehicle Type Mini Modal ───────────────────────────────────────
    const newVehicleModal   = document.getElementById('new-vehicle-modal');
    const newVehicleName    = document.getElementById('new-vehicle-name');
    const newVehicleError   = document.getElementById('new-vehicle-error');
    const newVehicleSpinner = document.getElementById('new-vehicle-spinner');

    document.getElementById('add-vehicle-type-btn').addEventListener('click', () => {
        newVehicleName.value = '';
        newVehicleError.classList.add('hidden');
        showModal(newVehicleModal);
        newVehicleName.focus();
    });

    document.getElementById('cancel-new-vehicle').addEventListener('click', () => hideModal(newVehicleModal));

    document.getElementById('save-new-vehicle').addEventListener('click', async () => {
        const name = newVehicleName.value.trim();
        if (!name) {
            newVehicleError.textContent = 'Name is required.';
            newVehicleError.classList.remove('hidden');
            return;
        }

        newVehicleSpinner.classList.remove('hidden');

        try {
            const data = await apiFetch(ROUTES.storeVehicle, 'POST', { name });
            if (data.success) {
                const vt = data.vehicle_type;
                const option = new Option(vt.name, vt.id);
                vehicleSel.add(option);
                vehicleSel.value = vt.id;
                hideModal(newVehicleModal);
            } else {
                newVehicleError.textContent = data.message ?? 'Error saving vehicle type.';
                newVehicleError.classList.remove('hidden');
            }
        } catch (err) {
            newVehicleError.textContent = 'Network error.';
            newVehicleError.classList.remove('hidden');
        } finally {
            newVehicleSpinner.classList.add('hidden');
        }
    });

    // Close mini modals on backdrop click
    newLocModal.querySelector('.absolute').addEventListener('click', () => hideModal(newLocModal));
    newVehicleModal.querySelector('.absolute').addEventListener('click', () => hideModal(newVehicleModal));

    // Enter key in mini modals
    newLocName.addEventListener('keydown', (e) => { if (e.key === 'Enter') { document.getElementById('save-new-location').click(); } });
    newVehicleName.addEventListener('keydown', (e) => { if (e.key === 'Enter') { document.getElementById('save-new-vehicle').click(); } });

})();
</script>

<script>
(function () {
    'use strict';

    // ── Routes ──────────────────────────────────────────────────────────────
    const AIRPORT_ROUTES = {
        store:        '{{ route('admin.transfers.airport-rates.store') }}',
        update:       (id) => `{{ url('admin/transfers/airport-rates') }}/${id}`,
        toggle:       (id) => `{{ url('admin/transfers/airport-rates') }}/${id}/toggle-status`,
        destroy:      (id) => `{{ url('admin/transfers/airport-rates') }}/${id}`,
        storeZone:    '{{ route('admin.transfers.zones.store') }}',
        storeVehicle: '{{ route('admin.transfers.airport-vehicle-types.store') }}',
    };

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Helpers ──────────────────────────────────────────────────────────────
    function showModal(el) { el.classList.remove('hidden'); el.classList.add('flex'); }
    function hideModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); }

    async function apiFetch(url, method, body = null) {
        const opts = {
            method,
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        const res = await fetch(url, opts);
        return res.json();
    }

    function clearAirportErrors() {
        document.querySelectorAll('.field-error-airport').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        const ge = document.getElementById('airport-form-error');
        if (ge) { ge.textContent = ''; ge.classList.add('hidden'); }
    }

    function showAirportErrors(errors) {
        clearAirportErrors();
        if (typeof errors === 'object') {
            Object.entries(errors).forEach(([field, msgs]) => {
                const el = document.querySelector(`.field-error-airport[data-field="${field}"]`);
                if (el) {
                    el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    el.classList.remove('hidden');
                }
            });
        } else {
            const ge = document.getElementById('airport-form-error');
            if (ge) { ge.textContent = errors; ge.classList.remove('hidden'); }
        }
    }

    // ── Airport Rate Modal ────────────────────────────────────────────────────
    const airportRateModal   = document.getElementById('airport-rate-modal');
    const airportModalTitle  = document.getElementById('airport-rate-modal-title');
    const airportRateIdInput = document.getElementById('airport-rate-id');
    const airportSaveBtn     = document.getElementById('save-airport-rate-btn');
    const airportSaveSpinner = document.getElementById('airport-save-spinner');

    const airportSel      = document.getElementById('airport-select');
    const transferTypeSel = document.getElementById('transfer-type-select');
    const zoneSel         = document.getElementById('zone-select');
    const airportVehicle  = document.getElementById('airport-vehicle-type');
    const airportFareSel  = document.getElementById('airport-fare-type');
    const airportPriceIn  = document.getElementById('airport-price');
    const airportCurrSel  = document.getElementById('airport-currency');
    const airportNotesIn  = document.getElementById('airport-notes');

    function resetAirportModal() {
        airportRateIdInput.value = '';
        airportSel.value         = '';
        transferTypeSel.value    = 'pickup';
        zoneSel.value            = '';
        airportVehicle.value     = '';
        airportFareSel.value     = 'fixed';
        airportPriceIn.value     = '0';
        airportCurrSel.value     = 'AED';
        airportNotesIn.value     = '';
        clearAirportErrors();
    }

    function openAirportAddModal() {
        resetAirportModal();
        airportModalTitle.textContent = 'Add Airport Rate';
        showModal(airportRateModal);
        airportSel.focus();
    }

    function openAirportEditModal(btn) {
        resetAirportModal();
        airportModalTitle.textContent = 'Edit Airport Rate';
        airportRateIdInput.value      = btn.dataset.id;
        airportSel.value              = btn.dataset.airport;
        transferTypeSel.value         = btn.dataset.transferType;
        zoneSel.value                 = btn.dataset.zone;
        airportVehicle.value          = btn.dataset.vehicle;
        airportFareSel.value          = btn.dataset.fare;
        airportPriceIn.value          = btn.dataset.price;
        airportCurrSel.value          = btn.dataset.currency;
        airportNotesIn.value          = btn.dataset.notes ?? '';
        showModal(airportRateModal);
    }

    document.getElementById('open-airport-rate-modal').addEventListener('click', openAirportAddModal);
    document.getElementById('close-airport-rate-modal').addEventListener('click', () => hideModal(airportRateModal));
    document.getElementById('cancel-airport-rate-modal').addEventListener('click', () => hideModal(airportRateModal));
    document.getElementById('airport-rate-modal-backdrop').addEventListener('click', () => hideModal(airportRateModal));

    // Delegated edit button handler
    document.getElementById('airport-rates-tbody').addEventListener('click', (e) => {
        const btn = e.target.closest('.edit-airport-rate-btn');
        if (btn) { openAirportEditModal(btn); }
    });

    // Save (store or update)
    airportSaveBtn.addEventListener('click', async () => {
        clearAirportErrors();
        const id = airportRateIdInput.value;
        const payload = {
            airport_id:      airportSel.value,
            transfer_type:   transferTypeSel.value,
            zone_id:         zoneSel.value,
            vehicle_type_id: airportVehicle.value,
            fare_type:       airportFareSel.value,
            price:           airportPriceIn.value,
            currency:        airportCurrSel.value,
            notes:           airportNotesIn.value,
        };

        airportSaveBtn.disabled = true;
        airportSaveSpinner.classList.remove('hidden');

        try {
            let data;
            if (id) {
                data = await apiFetch(AIRPORT_ROUTES.update(id), 'PUT', payload);
            } else {
                data = await apiFetch(AIRPORT_ROUTES.store, 'POST', payload);
            }

            if (data.success) {
                const tbody = document.getElementById('airport-rates-tbody');

                if (id) {
                    const existingRow = tbody.querySelector(`tr[data-rate-id="${id}"]`);
                    if (existingRow) { existingRow.outerHTML = data.row_html; }
                } else {
                    const emptyRow = tbody.querySelector('#airport-empty-state-row');
                    if (emptyRow) { emptyRow.remove(); }
                    tbody.insertAdjacentHTML('afterbegin', data.row_html);
                }

                hideModal(airportRateModal);
            } else if (data.errors) {
                showAirportErrors(data.errors);
            } else {
                showAirportErrors(data.message ?? 'An error occurred.');
            }
        } catch (err) {
            showAirportErrors('Network error. Please try again.');
        } finally {
            airportSaveBtn.disabled = false;
            airportSaveSpinner.classList.add('hidden');
        }
    });

    // ── Toggle Status ─────────────────────────────────────────────────────────
    document.getElementById('airport-rates-tbody').addEventListener('click', async (e) => {
        const btn = e.target.closest('.airport-toggle-status-btn');
        if (!btn) { return; }

        const icon    = btn.querySelector('.toggle-icon');
        const spinner = btn.querySelector('.toggle-spinner');

        btn.disabled = true;
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');

        try {
            const data = await apiFetch(AIRPORT_ROUTES.toggle(btn.dataset.id), 'PATCH');
            if (data.success) {
                const row   = btn.closest('tr');
                const badge = row.querySelector('.airport-rate-status-badge');

                if (data.is_active) {
                    badge.textContent = 'active';
                    badge.className   = 'airport-rate-status-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-900 text-white';
                } else {
                    badge.textContent = 'inactive';
                    badge.className   = 'airport-rate-status-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-500';
                }

                btn.dataset.active = data.is_active ? '1' : '0';
                btn.title = data.is_active ? 'Deactivate' : 'Activate';
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
            icon.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    });

    // ── Add New Zone Mini Modal ───────────────────────────────────────────────
    const newZoneModal   = document.getElementById('new-zone-modal');
    const newZoneName    = document.getElementById('new-zone-name');
    const newZoneError   = document.getElementById('new-zone-error');
    const newZoneSpinner = document.getElementById('new-zone-spinner');

    document.getElementById('add-zone-btn').addEventListener('click', () => {
        newZoneName.value = '';
        newZoneError.classList.add('hidden');
        showModal(newZoneModal);
        newZoneName.focus();
    });

    document.getElementById('cancel-new-zone').addEventListener('click', () => hideModal(newZoneModal));
    newZoneModal.querySelector('.absolute').addEventListener('click', () => hideModal(newZoneModal));

    document.getElementById('save-new-zone').addEventListener('click', async () => {
        const name = newZoneName.value.trim();
        if (!name) {
            newZoneError.textContent = 'Name is required.';
            newZoneError.classList.remove('hidden');
            return;
        }

        newZoneSpinner.classList.remove('hidden');

        try {
            const data = await apiFetch(AIRPORT_ROUTES.storeZone, 'POST', { name });
            if (data.success) {
                const zone   = data.zone;
                const option = new Option(zone.name, zone.id);
                zoneSel.add(option);
                zoneSel.value = zone.id;
                hideModal(newZoneModal);
            } else {
                newZoneError.textContent = data.message ?? 'Error saving zone.';
                newZoneError.classList.remove('hidden');
            }
        } catch (err) {
            newZoneError.textContent = 'Network error.';
            newZoneError.classList.remove('hidden');
        } finally {
            newZoneSpinner.classList.add('hidden');
        }
    });

    newZoneName.addEventListener('keydown', (e) => { if (e.key === 'Enter') { document.getElementById('save-new-zone').click(); } });

    // ── Add New Airport Vehicle Type Mini Modal ───────────────────────────────
    const newAirportVehicleModal   = document.getElementById('new-airport-vehicle-modal');
    const newAirportVehicleName    = document.getElementById('new-airport-vehicle-name');
    const newAirportVehicleError   = document.getElementById('new-airport-vehicle-error');
    const newAirportVehicleSpinner = document.getElementById('new-airport-vehicle-spinner');

    document.getElementById('add-airport-vehicle-type-btn').addEventListener('click', () => {
        newAirportVehicleName.value = '';
        newAirportVehicleError.classList.add('hidden');
        showModal(newAirportVehicleModal);
        newAirportVehicleName.focus();
    });

    document.getElementById('cancel-new-airport-vehicle').addEventListener('click', () => hideModal(newAirportVehicleModal));
    newAirportVehicleModal.querySelector('.absolute').addEventListener('click', () => hideModal(newAirportVehicleModal));

    document.getElementById('save-new-airport-vehicle').addEventListener('click', async () => {
        const name = newAirportVehicleName.value.trim();
        if (!name) {
            newAirportVehicleError.textContent = 'Name is required.';
            newAirportVehicleError.classList.remove('hidden');
            return;
        }

        newAirportVehicleSpinner.classList.remove('hidden');

        try {
            const data = await apiFetch(AIRPORT_ROUTES.storeVehicle, 'POST', { name });
            if (data.success) {
                const vt     = data.vehicle_type;
                const option = new Option(vt.name, vt.id);
                airportVehicle.add(option);
                airportVehicle.value = vt.id;
                hideModal(newAirportVehicleModal);
            } else {
                newAirportVehicleError.textContent = data.message ?? 'Error saving vehicle type.';
                newAirportVehicleError.classList.remove('hidden');
            }
        } catch (err) {
            newAirportVehicleError.textContent = 'Network error.';
            newAirportVehicleError.classList.remove('hidden');
        } finally {
            newAirportVehicleSpinner.classList.add('hidden');
        }
    });

    newAirportVehicleName.addEventListener('keydown', (e) => { if (e.key === 'Enter') { document.getElementById('save-new-airport-vehicle').click(); } });

    // ── Add New Airport Mini Modal ────────────────────────────────────────────
    const newAirportModal   = document.getElementById('new-airport-modal');
    const newAirportName    = document.getElementById('new-airport-name');
    const newAirportError   = document.getElementById('new-airport-error');
    const newAirportSpinner = document.getElementById('new-airport-spinner');
    const storeLocationUrl  = '{{ route('admin.transfers.locations.store') }}';

    document.getElementById('add-airport-btn').addEventListener('click', () => {
        newAirportName.value = '';
        newAirportError.classList.add('hidden');
        showModal(newAirportModal);
        newAirportName.focus();
    });

    document.getElementById('cancel-new-airport').addEventListener('click', () => hideModal(newAirportModal));
    newAirportModal.querySelector('.absolute').addEventListener('click', () => hideModal(newAirportModal));

    document.getElementById('save-new-airport').addEventListener('click', async () => {
        const name = newAirportName.value.trim();
        if (!name) {
            newAirportError.textContent = 'Name is required.';
            newAirportError.classList.remove('hidden');
            return;
        }

        newAirportSpinner.classList.remove('hidden');

        try {
            const data = await apiFetch(storeLocationUrl, 'POST', { name, type: 'airport' });
            if (data.success) {
                const loc    = data.location;
                const option = new Option(loc.name, loc.id);
                airportSel.add(option);
                airportSel.value = loc.id;
                hideModal(newAirportModal);
            } else {
                newAirportError.textContent = data.message ?? 'Error saving airport.';
                newAirportError.classList.remove('hidden');
            }
        } catch (err) {
            newAirportError.textContent = 'Network error.';
            newAirportError.classList.remove('hidden');
        } finally {
            newAirportSpinner.classList.add('hidden');
        }
    });

    newAirportName.addEventListener('keydown', (e) => { if (e.key === 'Enter') { document.getElementById('save-new-airport').click(); } });

})();
</script>
@endpush
