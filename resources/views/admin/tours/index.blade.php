@extends('layouts.dashboard')

@section('page-title', 'Tours')

@section('content')

    {{-- Page header --}}
    <div class="flex justify-between items-center mb-6">

        <h1 class="text-2xl font-bold text-gray-900">Tours</h1>

        <button id="open-create-tour-modal" type="button"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add Tour
        </button>

    </div>

    {{-- Tour cards grid --}}
    @if ($tours->isEmpty())
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-16 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <p class="text-gray-400 text-sm">No tours yet. Click <strong>Add Tour</strong> to create your first one.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5" id="tours-grid">
            @foreach ($tours as $tour)
                @include('admin.tours._card', ['tour' => $tour])
            @endforeach
        </div>

        @if ($tours->hasPages())
            <div class="mt-6">
                {{ $tours->links() }}
            </div>
        @endif
    @endif

    {{-- ============================================================
     ADD TOUR MODAL
     ============================================================ --}}
    <div id="create-tour-modal" class="fixed inset-0 z-50 hidden items-center justify-center" aria-modal="true"
        role="dialog">

        {{-- Backdrop --}}
        <div class="create-modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-7">

            {{-- Close --}}
            <button class="close-create-modal absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-bold text-gray-900 mb-6">Add Tour</h2>

            <div class="overflow-y-auto flex-1 px-7 py-5 max-h-[80vh]">
                <form id="create-tour-form" method="POST" action="{{ route('admin.tours.store') }}" class="space-y-4"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label for="c-title" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input id="c-title" type="text" name="title" value="{{ old('title') }}" required autofocus
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Location + Days --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="c-location" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <select id="c-location" name="location"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                @foreach (['Saudi Arabia', 'United Arab Emirates', 'Egypt', 'Jordon', 'Morocco', 'Turkey'] as $cur)
                                    <option value="{{ $cur }}"
                                        {{ old('location', 'SAR') === $cur ? 'selected' : '' }}>{{ $cur }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="c-days" class="block text-sm font-medium text-gray-700 mb-1.5">Days</label>
                            <input id="c-days" type="number" name="days" value="{{ old('days', 7) }}" min="1"
                                max="365"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Hotel Rating + Currency --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="c-hotel-rating" class="block text-sm font-medium text-gray-700 mb-1.5">Hotel
                                Rating</label>
                            <select id="c-hotel-rating" name="hotel_rating"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">-- Any --</option>
                                @foreach (range(1, 5) as $star)
                                    <option value="{{ $star }}"
                                        {{ old('hotel_rating', 4) == $star ? 'selected' : '' }}>
                                        {{ $star }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="c-currency" class="block text-sm font-medium text-gray-700 mb-1.5">Currency</label>
                            <select id="c-currency" name="currency"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                @foreach (['SAR'] as $cur)
                                    <option value="{{ $cur }}"
                                        {{ old('currency', 'SAR') === $cur ? 'selected' : '' }}>{{ $cur }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Retail Price + Agent Price --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="c-retail-price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Retail Price / Person <span class="text-red-500">*</span>
                            </label>
                            <input id="c-retail-price" type="number" name="retail_price"
                                value="{{ old('retail_price', 0) }}" min="0" step="0.01" required
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="c-agent-price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Agent Price / Person <span class="text-red-500">*</span>
                            </label>
                            <input id="c-agent-price" type="number" name="agent_price"
                                value="{{ old('agent_price', 0) }}" min="0" step="0.01" required
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Max Capacity --}}
                    <div>
                        <label for="c-max-capacity" class="block text-sm font-medium text-gray-700 mb-1.5">Max
                            Capacity</label>
                        <input id="c-max-capacity" type="number" name="max_capacity"
                            value="{{ old('max_capacity', 20) }}" min="1"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                    </div>

                    {{-- Summary --}}
                    <div>
                        <label for="c-summary" class="block text-sm font-medium text-gray-700 mb-1.5">Summary</label>
                        <textarea id="c-summary" name="summary" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y">{{ old('summary') }}</textarea>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="c-description"
                            class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea id="c-description" name="description" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y">{{ old('description') }}</textarea>
                    </div>

                    {{-- Itinerary --}}
                    <div>
                        <label for="c-itinerary" class="block text-sm font-medium text-gray-700 mb-1.5">Itinerary</label>
                        <textarea id="c-itinerary" name="itinerary" rows="5" placeholder="Day 1: ...&#10;Day 2: ..."
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y font-mono">{{ old('itinerary') }}</textarea>
                        {{-- PDF uploader --}}
                        <x-file-upload id="c-itinerary-uploader" name="itinerary_pdf" label="Itinerary PDF" accept=".pdf" max-size="10" />
                    </div>

                    {{-- Highlights --}}
                    <div>
                        <label for="c-highlights" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Highlights <span class="text-xs text-gray-400">(one per line)</span>
                        </label>
                        <textarea id="c-highlights" name="highlights" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y">{{ old('highlights') }}</textarea>
                    </div>

                    {{-- What's Included --}}
                    <div>
                        <label for="c-whats-included" class="block text-sm font-medium text-gray-700 mb-1.5">
                            What's Included <span class="text-xs text-gray-400">(one per line)</span>
                        </label>
                        <textarea id="c-whats-included" name="whats_included" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y">{{ old('whats_included') }}</textarea>
                    </div>

                    {{-- Image Files --}}
                    <div>
                        <x-file-upload name="image_urls[]" label="Image Files"
                            accept="image/*" :multiple="true" max-size="10" max-files="10"
                            description="You can upload multiple images at once. Max 10 MB per file and up to 10 images." />
                    </div>

                    {{-- Departure Dates & Slots --}}
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Departure Dates & Slots
                            </label>
                            <button type="button" id="add-departure-row-create"
                                class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
                                Add date
                            </button>
                        </div>
                        <div id="create-departure-items" class="space-y-3"></div>
                        <p class="mt-2 text-xs text-gray-500">
                            Add one departure date and the number of available slots for that date.
                        </p>
                        @error('departure_months')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('departure_months.*.date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('departure_months.*.slots')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <template id="departure-row-template">
                            <div class="departure-row grid gap-3 sm:grid-cols-[1fr_100px_auto] items-end">
                            <div>
                                <label class="sr-only">Departure date</label>
                                <input type="date" name="departure_months[][date]" data-role="departure-date"
                                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="sr-only">Available slots</label>
                                <input type="number" name="departure_months[][slots]" data-role="departure-slots" min="1" value="1"
                                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
                                    placeholder="Slots">
                            </div>
                            <button type="button" class="remove-departure-row rounded-full border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition">
                                Remove
                            </button>
                        </div>
                    </template>

                    {{-- Status --}}
                    <div>
                        <label for="c-status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="c-status" name="is_active"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Hidden submit for Enter key --}}
                    <button type="submit" class="hidden"></button>

                </form>

                {{-- Footer actions --}}
                <div
                    class="flex justify-end gap-3 px-7 py-4 border-t border-gray-100 bg-gray-50/60 rounded-b-2xl shrink-0">
                    <button type="button"
                        class="close-create-modal px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="submit" form="create-tour-form"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition">
                        Save Tour
                    </button>
                </div>
            </div>
        </div>

    </div>

    </div>

    {{-- ============================================================
     EDIT TOUR MODAL
     ============================================================ --}}
    <div id="edit-tour-modal" class="fixed inset-0 z-50 hidden items-center justify-center" aria-modal="true"
        role="dialog">

        <div class="edit-modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-7">
            <button class="close-edit-modal absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Tour</h2>

            <div class="overflow-y-auto flex-1 px-7 py-5 max-h-[80vh]">
                <form id="edit-tour-form" method="POST" action="" class="space-y-4"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="e-title" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input id="e-title" type="text" name="title" required
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="e-location" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Location</label>
                            <select id="e-location" name="location"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                @foreach (['Saudi Arabia', 'United Arab Emirates', 'Egypt', 'Jordon', 'Morocco', 'Turkey'] as $cur)
                                    <option value="{{ $cur }}">{{ $cur }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="e-days" class="block text-sm font-medium text-gray-700 mb-1.5">Days</label>
                            <input id="e-days" type="number" name="days" min="1" max="365"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="e-hotel-rating" class="block text-sm font-medium text-gray-700 mb-1.5">Hotel Rating</label>
                            <select id="e-hotel-rating" name="hotel_rating"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                <option value="">-- Any --</option>
                                @foreach (range(1, 5) as $star)
                                    <option value="{{ $star }}">{{ $star }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="e-currency" class="block text-sm font-medium text-gray-700 mb-1.5">Currency</label>
                            <select id="e-currency" name="currency"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                                @foreach (['SAR'] as $cur)
                                    <option value="{{ $cur }}">{{ $cur }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="e-retail-price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Retail Price / Person</label>
                            <input id="e-retail-price" type="number" name="retail_price" min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="e-agent-price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Agent Price / Person</label>
                            <input id="e-agent-price" type="number" name="agent_price" min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                    </div>

                    <div>
                        <label for="e-summary" class="block text-sm font-medium text-gray-700 mb-1.5">Summary</label>
                        <textarea id="e-summary" name="summary" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y"></textarea>
                    </div>

                    <div>
                        <label for="e-description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea id="e-description" name="description" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y"></textarea>
                    </div>

                    <div>
                        <label for="e-itinerary" class="block text-sm font-medium text-gray-700 mb-1.5">Itinerary</label>
                        <textarea id="e-itinerary" name="itinerary" rows="5"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y font-mono"></textarea>
                        <x-file-upload id="e-itinerary-uploader" name="itinerary_pdf" label="Itinerary PDF" accept=".pdf" max-size="10" />
                    </div>

                    <div>
                        <label for="e-highlights" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Highlights <span class="text-xs text-gray-400">(one per line)</span>
                        </label>
                        <textarea id="e-highlights" name="highlights" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y"></textarea>
                    </div>

                    <div>
                        <label for="e-whats-included" class="block text-sm font-medium text-gray-700 mb-1.5">
                            What&apos;s Included <span class="text-xs text-gray-400">(one per line)</span>
                        </label>
                        <textarea id="e-whats-included" name="whats_included" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-y"></textarea>
                    </div>

                    <div>
                        <x-file-upload id="e-image-uploader" name="image_urls[]" label="Image Files"
                            accept="image/*" :multiple="true" max-size="10" max-files="10"
                            description="You can upload multiple images at once. Max 10 MB per file and up to 10 images." />
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Departure Dates & Slots
                            </label>
                            <button type="button" id="add-departure-row-edit"
                                class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
                                Add date
                            </button>
                        </div>
                        <div id="edit-departure-items" class="space-y-3"></div>
                        <p class="mt-2 text-xs text-gray-500">
                            Add one departure date and the number of available slots for that date.
                        </p>
                        @error('departure_months')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('departure_months.*.date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('departure_months.*.slots')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="e-max-capacity" class="block text-sm font-medium text-gray-700 mb-1.5">Max Capacity</label>
                        <input id="e-max-capacity" type="number" name="max_capacity" min="1"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                    </div>

                    <div>
                        <label for="e-status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="e-status" name="is_active"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="hidden"></button>
                </form>

                <div
                    class="flex justify-end gap-3 px-7 py-4 border-t border-gray-100 bg-gray-50/60 rounded-b-2xl shrink-0">
                    <button type="button"
                        class="close-edit-modal px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="submit" form="edit-tour-form"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition">
                        Save Tour
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
     DELETE CONFIRMATION MODAL
     ============================================================ --}}
    <div id="delete-tour-modal" class="fixed inset-0 z-50 hidden items-center justify-center" aria-modal="true"
        role="dialog">
        <div class="delete-modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-7">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Delete Tour?</h2>
            <p class="text-sm text-gray-500 mb-6" id="delete-modal-msg">This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button"
                    class="close-delete-modal px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <form id="delete-tour-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                // ── Helpers ────────────────────────────────────────────────────────
                function openModal(el) {
                    el.classList.remove('hidden');
                    el.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeModal(el) {
                    el.classList.add('hidden');
                    el.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                // ── CREATE modal ───────────────────────────────────────────────────
                const createModal = document.getElementById('create-tour-modal');
                const createForm = document.getElementById('create-tour-form');
                const editForm = document.getElementById('edit-tour-form');
                const departureTemplate = document.getElementById('departure-row-template');
                const createDepartureContainer = document.getElementById('create-departure-items');
                const editDepartureContainer = document.getElementById('edit-departure-items');
                const addDepartureRowCreate = document.getElementById('add-departure-row-create');
                const addDepartureRowEdit = document.getElementById('add-departure-row-edit');
                let oldDepartureData = @json(old('departure_months', []));

                if (typeof oldDepartureData === 'string' && oldDepartureData.trim() !== '') {
                    oldDepartureData = oldDepartureData.split('\n')
                        .map(function (line) {
                            return line.trim();
                        })
                        .filter(function (line) {
                            return line !== '';
                        })
                        .map(function (line) {
                            return { date: line, slots: 1 };
                        });
                } else if (!Array.isArray(oldDepartureData)) {
                    oldDepartureData = [];
                }

                function refreshDepartureRowNames(container) {
                    if (!container) {
                        return;
                    }

                    Array.from(container.querySelectorAll('.departure-row')).forEach(function (row, index) {
                        const dateInput = row.querySelector('input[data-role="departure-date"]');
                        const slotsInput = row.querySelector('input[data-role="departure-slots"]');

                        if (dateInput) {
                            dateInput.name = `departure_months[${index}][date]`;
                        }
                        if (slotsInput) {
                            slotsInput.name = `departure_months[${index}][slots]`;
                        }
                    });
                }

                function addDepartureRow(container, data = { date: '', slots: 1 }) {
                    if (!departureTemplate || !container) {
                        return;
                    }

                    const clone = departureTemplate.content.firstElementChild.cloneNode(true);
                    const dateInput = clone.querySelector('input[name="departure_months[][date]"]');
                    const slotsInput = clone.querySelector('input[name="departure_months[][slots]"]');
                    const removeButton = clone.querySelector('.remove-departure-row');

                    if (dateInput) {
                        dateInput.value = data.date || '';
                        dateInput.min = new Date().toISOString().split('T')[0]; // Set minimum date to today
                        dateInput.dataset.role = 'departure-date';
                    }
                    if (slotsInput) {
                        slotsInput.value = data.slots ?? 1;
                        slotsInput.dataset.role = 'departure-slots';
                    }
                    if (removeButton) {
                        removeButton.addEventListener('click', function () {
                            clone.remove();
                            refreshDepartureRowNames(container);
                        });
                    }

                    container.appendChild(clone);
                    refreshDepartureRowNames(container);
                }

                function setDepartureRows(container, rows) {
                    if (!container) {
                        return;
                    }

                    container.innerHTML = '';

                    if (!Array.isArray(rows) || rows.length === 0) {
                        addDepartureRow(container);
                        return;
                    }

                    rows.forEach(function (item) {
                        addDepartureRow(container, {
                            date: item.date ?? '',
                            slots: item.slots ?? 1,
                        });
                    });

                    refreshDepartureRowNames(container);
                }

                document.getElementById('open-create-tour-modal').addEventListener('click', () => openModal(createModal));
                document.querySelectorAll('.close-create-modal, .create-modal-backdrop').forEach(el =>
                    el.addEventListener('click', () => closeModal(createModal))
                );

                if (createDepartureContainer && Array.isArray(oldDepartureData) && oldDepartureData.length) {
                    setDepartureRows(createDepartureContainer, oldDepartureData);
                }
                addDepartureRowCreate?.addEventListener('click', function () {
                    addDepartureRow(createDepartureContainer);
                });

                createForm.addEventListener('submit', function () {
                    if (!createDepartureContainer) {
                        return;
                    }

                    refreshDepartureRowNames(createDepartureContainer);

                    const rows = Array.from(createDepartureContainer.querySelectorAll('.departure-row'));
                    rows.forEach(function (row) {
                        const dateInput = row.querySelector('input[data-role="departure-date"]');
                        const slotsInput = row.querySelector('input[data-role="departure-slots"]');

                        if (dateInput && slotsInput && (!dateInput.value || !slotsInput.value)) {
                            row.remove();
                        }
                    });
                });

                editForm?.addEventListener('submit', function () {
                    if (!editDepartureContainer) {
                        return;
                    }

                    refreshDepartureRowNames(editDepartureContainer);

                    const rows = Array.from(editDepartureContainer.querySelectorAll('.departure-row'));
                    rows.forEach(function (row) {
                        const dateInput = row.querySelector('input[data-role="departure-date"]');
                        const slotsInput = row.querySelector('input[data-role="departure-slots"]');

                        if (dateInput && slotsInput && (!dateInput.value || !slotsInput.value)) {
                            row.remove();
                        }
                    });
                });

                addDepartureRowEdit?.addEventListener('click', function () {
                    addDepartureRow(editDepartureContainer);
                });

                const imageInput = document.getElementById('c-image-urls');
                const imageFilesList = document.getElementById('image-files-list');
                if (imageInput && imageFilesList) {
                    imageInput.addEventListener('change', function() {
                        const files = Array.from(this.files || []);
                        imageFilesList.textContent = files.length ? files.map(file => file.name).join(', ') : 'No files selected.';
                    });
                }

                @if ($errors->any())
                    openModal(createModal);
                @endif

                // ── DELETE modal ────────────────────────────────────────────────────
                const deleteModal = document.getElementById('delete-tour-modal');
                const deleteForm = document.getElementById('delete-tour-form');
                const deleteMsg = document.getElementById('delete-modal-msg');

                document.querySelectorAll('.open-delete-modal').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        deleteForm.action = this.dataset.deleteUrl;
                        deleteMsg.textContent = 'Delete "' + this.dataset.title +
                            '"? This cannot be undone.';
                        openModal(deleteModal);
                    });
                });

                document.querySelectorAll('.close-delete-modal, .delete-modal-backdrop').forEach(el =>
                    el.addEventListener('click', () => closeModal(deleteModal))
                );

                // ── EDIT modal ───────────────────────────────────────────────────────
                const editModal = document.getElementById('edit-tour-modal');

                document.querySelectorAll('.open-edit-modal').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const tourData = JSON.parse(this.dataset.tour);
                        editForm.action = this.dataset.updateUrl;

                        editForm.querySelector('#e-title').value = tourData.title || '';
                        editForm.querySelector('#e-location').value = tourData.location || '';
                        editForm.querySelector('#e-days').value = tourData.days || 1;
                        editForm.querySelector('#e-hotel-rating').value = tourData.hotel_rating ?? '';
                        editForm.querySelector('#e-currency').value = tourData.currency || 'SAR';
                        editForm.querySelector('#e-retail-price').value = tourData.retail_price || 0;
                        editForm.querySelector('#e-agent-price').value = tourData.agent_price || 0;
                        editForm.querySelector('#e-summary').value = tourData.summary || '';
                        editForm.querySelector('#e-description').value = tourData.description || '';
                        editForm.querySelector('#e-itinerary').value = tourData.itinerary || '';
                        editForm.querySelector('#e-highlights').value = (tourData.highlights || []).join('\n');
                        editForm.querySelector('#e-whats-included').value = (tourData.whats_included || []).join('\n');
                        editForm.querySelector('#e-max-capacity').value = tourData.max_capacity || 20;
                        editForm.querySelector('#e-status').value = tourData.is_active ? '1' : '0';

                        // Image uploader existing files
                        const imageUploader = window.fileUploaderComponents?.['e-image-uploader'];
                        if (imageUploader) {
                            const existingFiles = Array.isArray(tourData.image_urls)
                                ? tourData.image_urls.map(file => ({
                                    path: file.path ?? file,
                                    name: (file.path ?? file).split('/').pop(),
                                    url: file.url ?? file.path ?? file,
                                }))
                                : [];

                            imageUploader.setExistingFiles(existingFiles);
                        }

                        // Itinerary PDF uploader existing file
                        const pdfUploader = window.fileUploaderComponents?.['e-itinerary-uploader'];
                        if (pdfUploader && tourData.itinerary_pdf) {
                            const file = tourData.itinerary_pdf;
                            const existingPdf = {
                                path: file.path ?? file,
                                name: file.name ?? (file.path ?? file).split('/').pop(),
                                url: file.url ?? file.path ?? file,
                            };

                            pdfUploader.setExistingFiles([existingPdf]);
                        }

                        const departureRows = Array.isArray(tourData.departure_months)
                            ? tourData.departure_months.map(function (item) {
                                if (item && typeof item === 'object') {
                                    return {
                                        date: item.date ?? '',
                                        slots: item.slots ?? 1,
                                    };
                                }

                                const parsedDate = Date.parse(item);
                                if (!isNaN(parsedDate)) {
                                    const d = new Date(parsedDate);
                                    return {
                                        date: `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-01`,
                                        slots: 1,
                                    };
                                }

                                return {
                                    date: '',
                                    slots: 1,
                                };
                            })
                            : [];

                        if (editDepartureContainer) {
                            setDepartureRows(editDepartureContainer, departureRows);
                        }

                        openModal(editModal);
                    });
                });

                document.querySelectorAll('.close-edit-modal, .edit-modal-backdrop').forEach(el =>
                    el.addEventListener('click', () => closeModal(editModal))
                );

                // ── TOGGLE STATUS (AJAX) ───────────────────────────────────────────
                document.querySelectorAll('.tour-toggle-status-btn').forEach(function(btn) {
                    btn.addEventListener('click', async function() {
                        const url = btn.dataset.toggleUrl;
                        btn.disabled = true;

                        try {
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: '_method=PATCH',
                            });
                            const data = await res.json();

                            if (res.ok && data.success) {
                                const isActive = data.is_active;
                                btn.dataset.isActive = isActive ? '1' : '0';
                                btn.title = isActive ? 'Deactivate' : 'Activate';

                                const tourId = btn.dataset.id;
                                const card = document.getElementById('tour-card-' + tourId);
                                if (card) {
                                    const badge = card.querySelector('.tour-status-badge');
                                    if (badge) {
                                        badge.textContent = isActive ? 'active' : 'inactive';
                                        badge.className =
                                            'tour-status-badge inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full ' +
                                            (isActive ? 'bg-gray-900 text-white' :
                                                'bg-gray-100 text-gray-500');
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('Toggle failed', err);
                        } finally {
                            btn.disabled = false;
                        }
                    });
                });

                // ── KEYBOARD ESC ───────────────────────────────────────────────────
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeModal(createModal);
                        closeModal(deleteModal);
                    }
                });
            })();
        </script>
    @endpush

@endsection
