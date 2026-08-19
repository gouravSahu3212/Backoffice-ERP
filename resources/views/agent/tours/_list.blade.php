@forelse($tours as $tour)
    @php
        $firstImage = collect($tour->image_urls)->first();
        $imageUrl = null;
        if ($firstImage) {
            $imageUrl = (str_starts_with($firstImage, 'http://') || str_starts_with($firstImage, 'https://'))
                ? $firstImage
                : Storage::url($firstImage);
        }
    @endphp

    <div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow">
        {{-- Image --}}
        <div class="w-full md:w-80 h-56 md:h-auto shrink-0 bg-gray-100 relative overflow-hidden">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $tour->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2"><a href="{{ route('agent.tours.show', $tour) }}" class="hover:text-slate-700 transition-colors">{{ $tour->title }}</a></h3>
                
                {{-- Rating & Reviews --}}
                <div class="flex items-center gap-2 mb-3 text-sm">
                    <span class="font-bold text-gray-900">{{ $tour->hotel_rating ? $tour->hotel_rating . '.0' : '4.7' }}</span>
                    <div class="flex items-center text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 inline-block" fill="{{ $i <= ($tour->hotel_rating ?? 5) ? '#000000' : '#E5E7EB' }}" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-gray-400">•</span>
                    <a href="#" class="text-gray-500 hover:text-gray-700 underline">38 traveler reviews</a>
                </div>

                {{-- Tour Meta Info --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $tour->days }} days
                    </span>

                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        From {{ $tour->location }}
                    </span>

                    @if($tour->hotel_rating)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded bg-gray-100 text-xs font-semibold text-gray-700 border border-gray-200">
                            {{ $tour->hotel_rating }}★ Hotels
                        </span>
                    @endif
                </div>

                {{-- Summary --}}
                @if($tour->summary)
                    <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">{{ $tour->summary }}</p>
                @endif
            </div>
        </div>

        {{-- Price & Action --}}
        <div class="p-6 border-t md:border-t-0 md:border-l border-gray-100 flex flex-col items-end justify-between shrink-0 bg-gray-50/30">
            <div class="text-right w-full">
                <span class="text-xs text-gray-400 block mb-1">From</span>
                <div class="text-2xl font-bold text-gray-900 tracking-tight">
                    {{ $tour->currency === 'SAR' ? 'SAR ' : 'US$ ' }}{{ number_format($tour->agent_price, 0) }}
                </div>
                <span class="text-xs text-gray-400 block mt-0.5">per person</span>
            </div>

            <a href="{{ route('agent.tours.show', $tour) }}" class="w-full md:w-auto mt-6 bg-[#0B1527] hover:bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-md transition-colors inline-flex items-center justify-center gap-2 shadow-sm group">
                <span>View Details</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
@empty
    <div class="bg-white border border-gray-100 rounded-lg p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center mx-auto mb-4 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No tours found</h3>
        <p class="text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
    </div>
@endforelse
