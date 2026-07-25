@extends('layouts.dashboard')

@section('page-title', $tour->title)

@section('content')

{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('agent.tours.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
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
        <span class="text-gray-300">•</span>
        <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ $tour->days }} days
        </span>
        <span class="text-gray-300">•</span>
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
            <div class="w-full h-[400px] md:h-[450px] bg-gray-100 rounded-2xl overflow-hidden shadow-sm relative mb-4">
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
                        <button type="button" class="gallery-thumb w-24 h-18 rounded-xl overflow-hidden border-2 {{ $index === 0 ? 'border-gray-900 opacity-100' : 'border-transparent opacity-70 hover:opacity-100' }} transition-all shrink-0" data-src="{{ $img }}">
                            <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Tour Description Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Tour Description</h2>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $tour->description ?: ($tour->summary ?: 'Experience the very best of this escort tour. Explore breathtaking landmarks, enjoy thrilling local safaris, and relax at world-class resorts.') }}
            </p>
        </div>

        {{-- Highlights & What's Included Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Highlights Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Highlights</h2>
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
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">What's Included</h2>
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
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Itinerary</h2>
            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                {{ $tour->itinerary ?: 'Day-by-day itinerary will be provided. A detailed PDF itinerary can be downloaded below. Each day includes guided sightseeing, comfortable transfers and selected meals.' }}
            </p>

            @if($tour->itinerary_pdf)
                @php
                    $pdfUrl = (str_starts_with($tour->itinerary_pdf, 'http://') || str_starts_with($tour->itinerary_pdf, 'https://'))
                        ? $tour->itinerary_pdf
                        : Storage::url($tour->itinerary_pdf);
                @endphp
                <div class="pt-4 border-t border-gray-100">
                    <a href="{{ $pdfUrl }}" target="_blank" download class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
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
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
            
            {{-- Price Summary --}}
            <div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                    <span>Retail</span>
                    <span class="line-through">{{ $tour->currency === 'SAR' ? 'SAR ' : 'US$ ' }}{{ number_format($tour->retail_price ?: ($tour->agent_price * 1.25), 0) }}</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-sm font-semibold text-gray-900">Agent</span>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-gray-900 tracking-tight">
                            {{ $tour->currency === 'SAR' ? 'SAR ' : 'US$ ' }}{{ number_format($tour->agent_price, 0) }}
                        </span>
                        <span class="text-xs text-gray-400 block">per person</span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Check Availability Form --}}
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Check Availability</span>
                </h3>

                <div class="space-y-4">
                    {{-- Month --}}
                    <div>
                        <label for="side-month" class="block text-xs font-semibold text-gray-700 mb-1.5">Month</label>
                        <select id="side-month" class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                            <option value="">Any month</option>
                            @foreach($months as $m)
                                <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Travellers --}}
                    <div>
                        <label for="side-travellers" class="block text-xs font-semibold text-gray-700 mb-1.5">Travellers</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <input type="number" id="side-travellers" min="1" value="2" class="w-full border border-gray-200 rounded-xl pl-9 pr-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button id="side-check-btn" type="button" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white font-semibold text-sm py-3 rounded-xl transition-colors shadow-sm inline-flex items-center justify-center gap-2">
                        <span>Check Availability</span>
                    </button>
                </div>
            </div>

            {{-- Availability Results List (Shown under button as requested) --}}
            <div id="side-availability-results" class="space-y-4 pt-2">
                {{-- Loaded via JS on clicking Check Availability --}}
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
(function() {
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

            if (mainHero) {
                mainHero.src = this.dataset.src;
            }
        });
    });

    // Availability Checker
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
            const res = await fetch(`${availUrl}?month=${encodeURIComponent(monthVal)}&travellers=${encodeURIComponent(travVal)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();

            if (res.ok && data.success) {
                if (!data.departures || data.departures.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl text-center text-xs text-gray-500">
                            No departures found matching your criteria.
                        </div>
                    `;
                    return;
                }

                let html = `<p class="text-xs text-gray-500 mb-3 font-medium">${data.count} departure${data.count > 1 ? 's' : ''} found</p>`;

                html += data.departures.map(dep => `
                    <div class="border border-gray-200 rounded-xl p-4 bg-white shadow-2xs space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">${dep.month_name}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">${dep.subtitle}</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold ${dep.badge_class}">
                                ${dep.status_badge}
                            </span>
                        </div>

                        <p class="text-xs ${dep.slots > 0 ? 'text-gray-500' : 'text-red-500 font-medium'}">
                            ${dep.seats_text}
                        </p>

                        <hr class="border-gray-100">

                        <div class="space-y-1 text-xs">
                            <div class="flex items-center justify-between text-gray-400">
                                <span>Retail price</span>
                                <span class="line-through">${formatPrice(dep.currency, dep.retail_price || (dep.agent_price * 1.25))}</span>
                            </div>
                            <div class="flex items-center justify-between font-bold text-gray-900 text-sm">
                                <span>Agent price</span>
                                <span>${formatPrice(dep.currency, dep.agent_price)}</span>
                            </div>
                        </div>

                        <button type="button" class="w-full mt-2 bg-[#0B1527] hover:bg-slate-800 text-white text-xs font-semibold py-2.5 rounded-lg transition-colors shadow-2xs cursor-pointer">
                            Enquire
                        </button>
                    </div>
                `).join('');

                resultsContainer.innerHTML = html;
            }
        } catch (err) {
            console.error('Availability check failed', err);
            resultsContainer.innerHTML = `
                <div class="p-3 bg-red-50 text-red-600 text-xs rounded-xl border border-red-100">
                    Failed to fetch availability. Please try again.
                </div>
            `;
        } finally {
            sideCheckBtn.disabled = false;
            sideCheckBtn.innerHTML = '<span>Check Availability</span>';
        }
    }

    sideCheckBtn.addEventListener('click', loadAvailability);
})();
</script>
@endpush

@endsection
