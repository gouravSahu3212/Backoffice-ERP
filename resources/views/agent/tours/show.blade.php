@extends('layouts.dashboard')

@section('page-title', $tour->title)

@section('content')

{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('agent.tours.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Back to tours</span>
    </a>
</div>

{{-- Tour Header --}}
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $tour->title }}</h1>
    
    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
        <span class="font-bold text-gray-900">{{ $tour->hotel_rating ? $tour->hotel_rating . '.0' : '4.7' }}</span>
        <div class="flex items-center text-amber-400">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-4 h-4 inline-block" fill="{{ $i <= ($tour->hotel_rating ?? 5) ? '#000000' : '#E5E7EB' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
        <a href="#" class="text-gray-500 hover:text-gray-700 underline">38 traveler reviews</a>
        <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ $tour->days }} days
        </span>
        <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            From {{ $tour->location }}
        </span>
    </div>
</div>

@php
    $images = collect($tour->image_urls)->map(function($img) {
        if (!$img) return null;
        return (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) ? $img : Storage::url($img);
    })->filter()->values();
    $mainImage = $images->first();
@endphp

{{-- Main Grid Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">
    
    {{-- Left Column: Gallery + Details --}}
    <div class="lg:col-span-8 space-y-6">
        
        {{-- Hero Gallery --}}
        <div>
            <div class="w-full h-[400px] md:h-[450px] bg-gray-100 rounded-lg overflow-hidden shadow-sm relative mb-4">
                @if($mainImage)
                    <img id="main-hero-image" src="{{ $mainImage }}" alt="{{ $tour->title }}" class="w-full h-full object-cover transition-opacity duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($images->count() > 1)
                <div class="flex gap-3 overflow-x-auto pb-2">
                    @foreach($images as $index => $img)
                        <button type="button" class="gallery-thumb w-24 h-18 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-gray-900 opacity-100' : 'border-transparent opacity-70 hover:opacity-100' }} transition-all shrink-0" data-src="{{ $img }}">
                            <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Tour Description Card --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <h2 class="font-bold text-gray-900 mb-3">Tour Description</h2>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $tour->description ?: ($tour->summary ?: 'Experience the very best of this escort tour. Explore breathtaking landmarks, enjoy thrilling local safaris, and relax at world-class resorts.') }}
            </p>
        </div>

        {{-- Highlights & What's Included Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Highlights Card --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h2 class="font-bold text-gray-900 mb-4">Highlights</h2>
                @php
                    $highlightsList = is_array($tour->highlights) ? $tour->highlights : (trim($tour->highlights) ? explode("\n", $tour->highlights) : []);
                @endphp
                @if(count($highlightsList) > 0)
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        @foreach($highlightsList as $item)
                            @if(trim($item))
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-gray-800 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ trim($item) }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">No highlights specified.</p>
                @endif
            </div>

            {{-- What's Included Card --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h2 class="font-bold text-gray-900 mb-4">What's Included</h2>
                @php
                    $includedList = is_array($tour->whats_included) ? $tour->whats_included : (trim($tour->whats_included) ? explode("\n", $tour->whats_included) : []);
                @endphp
                @if(count($includedList) > 0)
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        @foreach($includedList as $item)
                            @if(trim($item))
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-gray-800 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ trim($item) }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">No inclusions specified.</p>
                @endif
            </div>

        </div>

        {{-- Itinerary Card --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <h2 class="font-bold text-gray-900 mb-3">Itinerary</h2>
            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                {{ $tour->itinerary ?: 'Day-by-day itinerary will be provided. A detailed PDF itinerary can be downloaded below. Each day includes guided sightseeing, comfortable transfers and selected meals.' }}
            </p>

            @if($tour->itinerary_pdf)
                @php
                    $pdfUrl = (str_starts_with($tour->itinerary_pdf, 'http://') || str_starts_with($tour->itinerary_pdf, 'https://'))
                        ? $tour->itinerary_pdf
                        : Storage::url($tour->itinerary_pdf);
                @endphp
                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ $pdfUrl }}" target="_blank" download class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Download Itinerary (PDF)</span>
                    </a>
                </div>
            @endif
        </div>

    </div>

    {{-- Right Column: Availability Sidebar --}}
    <div class="lg:col-span-4 sticky top-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-6">
            
            {{-- Price Summary --}}
            <div>
                <div class="flex items-center text-xs text-gray-500 mb-1 gap-2">
                    <span>Retail</span>
                    <span class="line-through">{{ $tour->currency === 'SAR' ? 'SAR ' : 'US$ ' }}{{ number_format($tour->retail_price ?: ($tour->agent_price * 1.25), 0) }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-sm font-semibold text-gray-900">Agent</span>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">
                        {{ $tour->currency === 'SAR' ? 'SAR ' : 'US$ ' }}{{ number_format($tour->agent_price, 0) }}
                    </span>
                    <span class="text-xs text-gray-400 block">per person</span>
                </div>
            </div>

            <hr class="border-gray-200">

            {{-- Check Availability Form --}}
            <div class="!mt-3">
                <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Check Availability</span>
                </h3>

                <div class="space-y-4">
                    {{-- Departure Date Picker --}}
                    <div>
                        <label for="side-date" class="block text-sm font-semibold mb-1.5">Departure Date</label>
                        <div class="relative">
                            <input type="text" id="side-date" placeholder="Select Departure Date" readonly class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition cursor-pointer pr-10">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Month --}}
                    <div>
                        <label for="side-month" class="block text-sm font-semibold mb-1.5">Month</label>
                        <select id="side-month" class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                            <option value="">Any month</option>
                            @foreach($months as $m)
                                <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Travellers --}}
                    <div>
                        <label for="side-travellers" class="block text-sm font-semibold mb-1.5">Travellers</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <input type="number" id="side-travellers" min="1" value="2" class="w-full border border-gray-200 rounded-lg pl-9 pr-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button id="side-check-btn" type="button" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm py-3 rounded-lg transition-colors shadow-sm inline-flex items-center justify-center gap-2">
                        <span>Check Availability</span>
                    </button>
                </div>
            </div>

            {{-- Availability Results List --}}
            <div id="side-availability-results" class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                {{-- Loaded via JS on clicking Check Availability --}}
            </div>

        </div>
    </div>

</div>

{{-- ============================================================
     TOUR ENQUIRY MODAL
     ============================================================ --}}
<div id="enquiry-modal" class="fixed inset-0 z-50 hidden items-center justify-center" aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div id="enquiry-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 pt-6 pb-4 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Tour Enquiry</h2>
                    <p id="enquiry-subtitle" class="text-sm text-gray-500 mt-0.5"></p>
                </div>
                <button id="enquiry-close" type="button" class="text-gray-400 hover:text-gray-600 transition-colors ml-4 mt-0.5 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

            {{-- Customer Name --}}
            <div>
                <label for="enq-customer-name" class="block text-sm font-semibold text-gray-700 mb-1.5">Customer Name <span class="text-red-500">*</span></label>
                <input type="text" id="enq-customer-name" placeholder="Enter customer name"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p id="err-customer-name" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            {{-- Date of Birth --}}
            <div>
                <label for="enq-dob" class="block text-sm font-semibold text-gray-700 mb-1.5">Date of Birth <span class="text-red-500">*</span></label>
                <input type="date" id="enq-dob" max="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p id="err-dob" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            {{-- Passport Number --}}
            <div>
                <label for="enq-passport" class="block text-sm font-semibold text-gray-700 mb-1.5">Passport Number <span class="text-red-500">*</span></label>
                <input type="text" id="enq-passport" placeholder="Enter passport number"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p id="err-passport" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            {{-- Number of People --}}
            <div>
                <label for="enq-pax" class="block text-sm font-semibold text-gray-700 mb-1.5">Number of People <span class="text-red-500">*</span></label>
                <input type="number" id="enq-pax" min="1"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p id="enq-capacity-note" class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="enq-capacity-text"></span>
                </p>
                <p id="err-pax" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            {{-- Price Summary --}}
            <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Enquiry Total</span>
                    <span id="enq-price-display" class="text-base font-bold text-gray-900"></span>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    <span id="enq-price-breakdown"></span>
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <button id="enquiry-cancel" type="button"
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button id="enquiry-submit" type="button"
                class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B1527] hover:bg-slate-800 rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                <span id="enquiry-submit-label">Submit</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
(function() {
    // Available Departure Dates configured by admin for this tour
    @php
        $availableDates = collect($tour->departure_months ?? [])->pluck('date')->filter()->values();
    @endphp
    const availableDates = @json($availableDates);
    const tourTitle = @json($tour->title);
    const enquireUrl = @json(route('agent.tours.enquire', $tour));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Initialize Flatpickr calendar
    const fp = flatpickr('#side-date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'M j, Y',
        altInputClass: 'w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition cursor-pointer',
        enable: availableDates.length > 0 ? availableDates : [],
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                const monthVal = dateStr.substring(0, 7);
                if (sideMonth) { sideMonth.value = monthVal; }
            }
        }
    });

    // Thumbnail Gallery Switcher
    const mainHero = document.getElementById('main-hero-image');
    const thumbs = document.querySelectorAll('.gallery-thumb');

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            thumbs.forEach(t => {
                t.classList.remove('border-gray-900', 'opacity-100');
                t.classList.add('border-transparent', 'opacity-70');
            });
            this.classList.remove('border-transparent', 'opacity-70');
            this.classList.add('border-gray-900', 'opacity-100');
            if (mainHero) { mainHero.src = this.dataset.src; }
        });
    });

    // Availability Checker
    const sideDate = document.getElementById('side-date');
    const sideMonth = document.getElementById('side-month');
    const sideTravellers = document.getElementById('side-travellers');
    const sideCheckBtn = document.getElementById('side-check-btn');
    const resultsContainer = document.getElementById('side-availability-results');
    const availUrl = @json(route('agent.tours.availability', $tour));

    function formatPrice(currency, amount) {
        const symbol = currency === 'SAR' ? 'SAR ' : 'US$ ';
        return symbol + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    async function loadAvailability() {
        const dateVal = sideDate.value;
        const monthVal = sideMonth.value;
        const travVal = sideTravellers.value || 1;

        sideCheckBtn.disabled = true;
        sideCheckBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Checking…</span>
        `;

        try {
            const queryParams = new URLSearchParams({ date: dateVal, month: monthVal, travellers: travVal });
            const res = await fetch(`${availUrl}?${queryParams.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                if (!data.departures || data.departures.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center text-xs text-gray-500">
                            No departures found matching your criteria.
                        </div>
                    `;
                    return;
                }

                let html = `<p class="text-xs text-gray-500 mb-3 font-medium">${data.count} departure${data.count > 1 ? 's' : ''} found</p>`;

                html += data.departures.map(dep => `
                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-2xs space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">${dep.date_label}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">${dep.subtitle}</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-lg font-semibold ${dep.badge_class}">
                                ${dep.status_badge}
                            </span>
                        </div>

                        <p class="text-xs ${dep.slots > 0 ? 'text-gray-500' : 'text-red-500 font-medium'}">
                            ${dep.seats_text}
                        </p>

                        <hr class="border-gray-200">

                        <div class="space-y-1 text-xs">
                            <div class="flex items-center justify-between text-gray-400">
                                <span>Retail price</span>
                                <span class="line-through">${formatPrice(dep.currency, dep.retail_price || (dep.agent_price * 1.25))}</span>
                            </div>
                            <div class="flex items-center justify-between font-bold text-gray-900 text-sm">
                                <span>Agent price</span>
                                <span>${formatPrice(dep.currency, dep.agent_price)}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600 font-semibold pt-1 border-t border-gray-100 mt-1">
                                <span>Total (${travVal} pax)</span>
                                <span>${formatPrice(dep.currency, dep.agent_price * parseInt(travVal))}</span>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="enquire-btn w-full mt-2 ${dep.slots > 0 ? 'bg-[#0B1527] hover:bg-slate-800 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'} text-white text-xs font-semibold py-2.5 rounded-lg transition-colors shadow-2xs"
                            ${dep.slots <= 0 ? 'disabled' : ''}
                            data-date="${dep.date}"
                            data-date-label="${dep.date_label}"
                            data-slots="${dep.slots}"
                            data-agent-price="${dep.agent_price}"
                            data-currency="${dep.currency}"
                            data-max-capacity="${dep.max_capacity}"
                        >
                            ${dep.slots > 0 ? 'Enquire' : 'Sold Out'}
                        </button>
                    </div>
                `).join('');

                resultsContainer.innerHTML = html;

                // Bind enquire buttons
                resultsContainer.querySelectorAll('.enquire-btn[data-date]').forEach(btn => {
                    btn.addEventListener('click', function() {
                        openEnquiryModal({
                            date: this.dataset.date,
                            dateLabel: this.dataset.dateLabel,
                            slots: parseInt(this.dataset.slots),
                            agentPrice: parseFloat(this.dataset.agentPrice),
                            currency: this.dataset.currency,
                            maxCapacity: parseInt(this.dataset.maxCapacity),
                        });
                    });
                });
            }
        } catch (err) {
            console.error('Availability check failed', err);
            resultsContainer.innerHTML = `
                <div class="p-3 bg-red-50 text-red-600 text-xs rounded-lg border border-red-100">
                    Failed to fetch availability. Please try again.
                </div>
            `;
        } finally {
            sideCheckBtn.disabled = false;
            sideCheckBtn.innerHTML = '<span>Check Availability</span>';
        }
    }

    sideCheckBtn.addEventListener('click', loadAvailability);

    // ============================================================
    // ENQUIRY MODAL
    // ============================================================
    const modal = document.getElementById('enquiry-modal');
    const backdrop = document.getElementById('enquiry-backdrop');
    const closeBtn = document.getElementById('enquiry-close');
    const cancelBtn = document.getElementById('enquiry-cancel');
    const submitBtn = document.getElementById('enquiry-submit');
    const submitLabel = document.getElementById('enquiry-submit-label');

    const fCustomerName = document.getElementById('enq-customer-name');
    const fDob = document.getElementById('enq-dob');
    const fPassport = document.getElementById('enq-passport');
    const fPax = document.getElementById('enq-pax');
    const capacityText = document.getElementById('enq-capacity-text');
    const priceDisplay = document.getElementById('enq-price-display');
    const priceBreakdown = document.getElementById('enq-price-breakdown');
    const subtitle = document.getElementById('enquiry-subtitle');

    // Hidden state for current enquiry
    let currentEnquiry = {};

    function openEnquiryModal({ date, dateLabel, slots, agentPrice, currency, maxCapacity }) {
        currentEnquiry = { date, dateLabel, slots, agentPrice, currency, maxCapacity };

        // Reset form
        fCustomerName.value = '';
        fDob.value = '';
        const yesterdayStr = new Date(Date.now() - 86400000).toISOString().split('T')[0];
        fDob.max = yesterdayStr;
        fPassport.value = '';

        // Default pax to current travellers value (clamped to allowed max)
        const travellersInput = parseInt(sideTravellers.value) || 1;
        const maxAllowed = Math.min(slots, maxCapacity);
        fPax.min = 1;
        fPax.max = maxAllowed;
        fPax.value = Math.min(travellersInput, maxAllowed);

        // Subtitle
        subtitle.textContent = `${tourTitle} — ${dateLabel} departure`;

        // Capacity note
        capacityText.textContent = `Max capacity: ${maxCapacity} seats. Available slots: ${slots}.`;

        // Hide all errors
        ['enq-customer-name', 'enq-dob', 'enq-passport', 'enq-pax'].forEach(id => {
            const el = document.getElementById('err-' + id.replace('enq-', ''));
            if (el) { el.classList.add('hidden'); el.textContent = ''; }
        });

        updatePriceDisplay();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEnquiryModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function updatePriceDisplay() {
        const pax = parseInt(fPax.value) || 1;
        const total = (currentEnquiry.agentPrice || 0) * pax;
        const symbol = currentEnquiry.currency === 'SAR' ? 'SAR ' : 'US$ ';
        priceDisplay.textContent = symbol + total.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        priceBreakdown.textContent = `${symbol}${Number(currentEnquiry.agentPrice || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 })} × ${pax} person${pax > 1 ? 's' : ''}`;
    }

    fPax.addEventListener('input', function() {
        const maxAllowed = Math.min(currentEnquiry.slots || 1, currentEnquiry.maxCapacity || 1);
        if (parseInt(this.value) > maxAllowed) {
            this.value = maxAllowed;
        }
        if (parseInt(this.value) < 1 || this.value === '') {
            this.value = 1;
        }
        updatePriceDisplay();
    });

    closeBtn.addEventListener('click', closeEnquiryModal);
    cancelBtn.addEventListener('click', closeEnquiryModal);
    backdrop.addEventListener('click', closeEnquiryModal);

    function showFieldError(fieldId, message) {
        const el = document.getElementById('err-' + fieldId);
        if (el) {
            el.textContent = message;
            el.classList.remove('hidden');
        }
    }

    function clearErrors() {
        document.querySelectorAll('[id^="err-"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    function validateForm() {
        clearErrors();
        let valid = true;

        if (!fCustomerName.value.trim()) {
            showFieldError('customer-name', 'Customer name is required.');
            valid = false;
        }
        if (!fDob.value) {
            showFieldError('dob', 'Date of birth is required.');
            valid = false;
        } else if (fDob.value >= new Date().toISOString().split('T')[0]) {
            showFieldError('dob', 'Date of birth must be a past date.');
            valid = false;
        }
        if (!fPassport.value.trim()) {
            showFieldError('passport', 'Passport number is required.');
            valid = false;
        }
        const paxVal = parseInt(fPax.value);
        if (!paxVal || paxVal < 1) {
            showFieldError('pax', 'At least 1 person is required.');
            valid = false;
        }
        const maxAllowed = Math.min(currentEnquiry.slots || 1, currentEnquiry.maxCapacity || 1);
        if (paxVal > maxAllowed) {
            showFieldError('pax', `Cannot exceed ${maxAllowed} people (slots available).`);
            valid = false;
        }

        return valid;
    }

    submitBtn.addEventListener('click', async function() {
        if (!validateForm()) { return; }

        submitBtn.disabled = true;
        submitLabel.textContent = 'Submitting…';

        const pax = parseInt(fPax.value);
        const totalPrice = (currentEnquiry.agentPrice || 0) * pax;

        try {
            const res = await fetch(enquireUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    customer_name: fCustomerName.value.trim(),
                    date_of_birth: fDob.value,
                    passport_number: fPassport.value.trim(),
                    departure_date: currentEnquiry.date,
                    pax: pax,
                    total_price: totalPrice,
                    currency: currentEnquiry.currency,
                }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                closeEnquiryModal();
                // Show a brief success banner
                const banner = document.createElement('div');
                banner.className = 'fixed top-5 right-5 z-[9999] bg-emerald-600 text-white text-sm font-medium px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 animate-fade-in';
                banner.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Enquiry submitted! Reference: <strong>${data.reference}</strong>
                `;
                document.body.appendChild(banner);
                setTimeout(() => banner.remove(), 5000);
            } else {
                // Show validation errors from server
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, msgs]) => {
                        const map = {
                            customer_name: 'customer-name',
                            date_of_birth: 'dob',
                            passport_number: 'passport',
                            pax: 'pax',
                        };
                        if (map[field]) {
                            showFieldError(map[field], Array.isArray(msgs) ? msgs[0] : msgs);
                        }
                    });
                }
            }
        } catch (err) {
            console.error('Enquiry submit failed', err);
        } finally {
            submitBtn.disabled = false;
            submitLabel.textContent = 'Submit';
        }
    });

})();
</script>
@endpush

@endsection
