@extends('layouts.dashboard')

@section('page-title', 'Tours')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Tours</h1>
    <p class="text-sm text-gray-500 mt-1">Search escorted tours and view availability.</p>
</div>

{{-- Search & Filters Card --}}
<div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-8">
    <form id="tour-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" onsubmit="return false;">
        
        {{-- Location Filter --}}
        <div>
            <label for="filter-location" class="block text-sm font-semibold text-gray-900 mb-2">Location</label>
            <div class="relative">
                <select id="filter-location" name="location" class="w-full appearance-none bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition pr-10 cursor-pointer">
                    <option value="">Any destination</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400">
                </div>
            </div>
        </div>

        {{-- Month Filter --}}
        <div>
            <label for="filter-month" class="block text-sm font-semibold text-gray-900 mb-2">Month</label>
            <div class="relative">
                <select id="filter-month" name="month" class="w-full appearance-none bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition pr-10 cursor-pointer">
                    <option value="">Any month</option>
                    @foreach($months as $m)
                        <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400">
                </div>
            </div>
        </div>

        {{-- Travellers Filter --}}
        <div>
            <label for="filter-travellers" class="block text-sm font-semibold text-gray-900 mb-2">Travellers</label>
            <input type="number" id="filter-travellers" name="travellers" min="1" placeholder="2" value="2"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
        </div>

        {{-- Search Button --}}
        <div>
            <button id="search-tours-btn" type="button" class="w-full bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold px-5 py-3 rounded-xl transition-colors inline-flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Search Tours</span>
            </button>
        </div>

    </form>
</div>

{{-- Tours List Container --}}
<div id="tours-container" class="space-y-6">
    @include('agent.tours._list', ['tours' => $tours])
</div>

@push('scripts')
<script>
(function() {
    const filterLocation = document.getElementById('filter-location');
    const filterMonth = document.getElementById('filter-month');
    const filterTravellers = document.getElementById('filter-travellers');
    const searchBtn = document.getElementById('search-tours-btn');
    const container = document.getElementById('tours-container');

    const searchUrl = @json(route('agent.tours.search'));
    const showUrlTemplate = @json(route('agent.tours.show', 'TOUR_ID'));

    function renderStars(rating) {
        if (!rating) return '';
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            const fill = i <= rating ? '#000000' : '#E5E7EB';
            stars += `<svg class="w-4 h-4 inline-block" fill="${fill}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        }
        return stars;
    }

    function formatPrice(currency, amount) {
        const symbol = currency === 'SAR' ? 'SAR ' : (currency === 'USD' || currency === 'US$' ? 'US$ ' : currency + ' ');
        const formattedAmount = Number(amount).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        return symbol + formattedAmount;
    }

    async function performSearch() {
        const params = new URLSearchParams({
            location: filterLocation.value,
            month: filterMonth.value,
            travellers: filterTravellers.value,
        });

        // Skeleton loading state
        container.innerHTML = `
            <div class="space-y-6 animate-pulse">
                ${[1, 2].map(() => `
                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden flex flex-col md:flex-row h-64">
                        <div class="bg-gray-200 w-full md:w-80 h-full shrink-0"></div>
                        <div class="p-6 flex-1 space-y-4">
                            <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-12 bg-gray-200 rounded w-full"></div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        try {
            const res = await fetch(`${searchUrl}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();

            if (res.ok && data.success) {
                if (data.tours.length === 0) {
                    container.innerHTML = `
                        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">No tours found</h3>
                            <p class="text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = data.tours.map(tour => `
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow">
                        <!-- Image -->
                        <div class="w-full md:w-80 h-56 md:h-auto shrink-0 bg-gray-100 relative overflow-hidden">
                            ${tour.image_url ? `
                                <img src="${tour.image_url}" alt="${tour.title}" class="w-full h-full object-cover">
                            ` : `
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            `}
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">${tour.title}</h3>
                                
                                <!-- Rating & Reviews (Static mock matching layout design) -->
                                <div class="flex items-center gap-2 mb-3 text-sm">
                                    <span class="font-bold text-gray-900">${tour.hotel_rating ? (tour.hotel_rating + '.0') : '4.7'}</span>
                                    <div class="flex items-center text-amber-400">
                                        ${renderStars(tour.hotel_rating || 5)}
                                    </div>
                                    <span class="text-gray-400">•</span>
                                    <a href="#" class="text-gray-500 hover:text-gray-700 underline">38 traveler reviews</a>
                                </div>

                                <!-- Tour Meta Info -->
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        ${tour.days} days
                                    </span>

                                    <span class="flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        From ${tour.location}
                                    </span>

                                    ${tour.hotel_rating ? `
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded bg-gray-100 text-xs font-semibold text-gray-700 border border-gray-200">
                                            ${tour.hotel_rating}★ Hotels
                                        </span>
                                    ` : ''}
                                </div>

                                <!-- Summary -->
                                ${tour.summary ? `
                                    <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">${tour.summary}</p>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="p-6 md:w-64 border-t md:border-t-0 md:border-l border-gray-100 flex flex-col items-end justify-between shrink-0 bg-gray-50/30">
                            <div class="text-right w-full">
                                <span class="text-xs text-gray-400 block mb-1">From</span>
                                <div class="text-2xl font-bold text-gray-900 tracking-tight">
                                    ${formatPrice(tour.currency, tour.agent_price)}
                                </div>
                                <span class="text-xs text-gray-400 block mt-0.5">per person</span>
                            </div>

                            <a href="${showUrlTemplate.replace('TOUR_ID', tour.id)}" class="w-full md:w-auto mt-6 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors inline-flex items-center justify-center gap-2 shadow-sm group">
                                <span>View Details</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                `).join('');
            }
        } catch (err) {
            console.error('Filter request failed', err);
        }
    }

    // Trigger search on form submit / button click
    searchBtn.addEventListener('click', performSearch);
    filterLocation.addEventListener('change', performSearch);
    filterMonth.addEventListener('change', performSearch);
    filterTravellers.addEventListener('input', () => {
        clearTimeout(window._travellerDebounce);
        window._travellerDebounce = setTimeout(performSearch, 400);
    });
})();
</script>
@endpush

@endsection
