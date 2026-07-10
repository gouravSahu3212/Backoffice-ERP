<div id="tour-card-{{ $tour->id }}"
    class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 flex flex-col gap-3 hover:shadow-md transition-shadow">

    {{-- Header: title + status badge --}}
    <div class="flex items-start justify-between gap-3">
        <h3 class="text-sm font-semibold text-gray-900 leading-snug">{{ $tour->title }}</h3>
        <span
            class="tour-status-badge shrink-0 inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full
            {{ $tour->is_active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500' }}">
            {{ $tour->is_active ? 'active' : 'inactive' }}
        </span>
    </div>

    {{-- Location · Days · Rating --}}
    <div class="flex items-center gap-2 text-xs text-gray-500">
        <span class="text-gray-400 font-medium">{{ $tour->location }}</span>
        <span class="text-gray-300">·</span>
        <span>{{ $tour->days }} {{ Str::plural('day', $tour->days) }}</span>
        @if ($tour->hotel_rating)
            <span class="text-gray-300">·</span>
            <span class="flex items-center gap-0.5">
                @for ($s = 1; $s <= $tour->hotel_rating; $s++)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-amber-400" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                @endfor
            </span>
        @endif
    </div>

    {{-- Pricing --}}
    <div class="text-xs text-gray-600 space-y-0.5">
        <p><span class="text-gray-400">Retail:</span> <span class="font-semibold">{{ $tour->currency }}
                {{ number_format($tour->retail_price) }}</span></p>
        <p><span class="text-gray-400">Agent:</span> <span class="font-semibold text-gray-700">{{ $tour->currency }}
                {{ number_format($tour->agent_price) }}</span> <span class="text-gray-400">per person</span></p>
    </div>

    {{-- Departure dates with slots --}}
    @if (!empty($tour->departure_months))
        <div class="grid grid-cols-1 gap-2 text-xs text-gray-600">
            @foreach ($tour->departure_months as $departure)
                @php
                    $date = data_get($departure, 'date', $departure);
                    $slots = data_get($departure, 'slots', 1);
                @endphp
                <div class="flex items-center justify-between">
                    <span class="truncate">{{ $date }}</span>
                    <span class="text-gray-400">{{ $slots }} {{ Str::plural('slot', $slots) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-1 border-t border-gray-50">

        @php
            $tourImageFiles = collect($tour->image_urls ?? [])->map(function ($path) {
                return [
                    'path' => $path,
                    'url' => \Illuminate\Support\Facades\Storage::url($path),
                ];
            })->all();

            $itineraryPdfFile = $tour->itinerary_pdf ? [
                'path' => $tour->itinerary_pdf,
                'url' => \Illuminate\Support\Facades\Storage::url($tour->itinerary_pdf),
                'name' => basename($tour->itinerary_pdf),
            ] : null;

            $tourData = [
                'id' => $tour->id,
                'title' => $tour->title,
                'location' => $tour->location,
                'days' => $tour->days,
                'hotel_rating' => $tour->hotel_rating,
                'currency' => $tour->currency,
                'retail_price' => $tour->retail_price,
                'agent_price' => $tour->agent_price,
                'summary' => $tour->summary,
                'description' => $tour->description,
                'itinerary' => $tour->itinerary,
                'itinerary_pdf' => $itineraryPdfFile,
                'highlights' => $tour->highlights,
                'whats_included' => $tour->whats_included,
                'departure_months' => $tour->departure_months,
                'max_capacity' => $tour->max_capacity,
                'is_active' => $tour->is_active ? '1' : '0',
                'image_urls' => $tourImageFiles,
            ];
        @endphp

        {{-- Edit (opens modal) --}}
        <button type="button" title="Edit tour"
            class="open-edit-modal p-1.5 text-gray-400 hover:text-gray-700 transition-colors"
            data-tour='{{ json_encode($tourData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}'
            data-update-url="{{ route('admin.tours.update', $tour) }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-pencil h-4 w-4">
                <path
                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z">
                </path>
                <path d="m15 5 4 4"></path>
            </svg>
        </button>

        {{-- Toggle Status (AJAX) --}}
        <button type="button" title="{{ $tour->is_active ? 'Deactivate' : 'Activate' }}"
            class="toggle-status-btn p-1.5 text-gray-400 hover:text-gray-700 transition-colors"
            data-id="{{ $tour->id }}" data-is-active="{{ $tour->is_active ? '1' : '0' }}"
            data-toggle-url="{{ route('admin.tours.toggle-status', $tour) }}">
            @if ($tour->is_active)
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-toggle-right h-4 w-4">
                    <rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect>
                    <circle cx="16" cy="12" r="2"></circle>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-toggle-left h-4 w-4">
                    <rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect>
                    <circle cx="8" cy="12" r="2"></circle>
                </svg>
            @endif
        </button>

        {{-- Delete --}}
        <button type="button" title="Delete tour"
            class="open-delete-modal p-1.5 text-gray-400 hover:text-red-600 transition-colors"
            data-id="{{ $tour->id }}" data-title="{{ $tour->title }}"
            data-delete-url="{{ route('admin.tours.destroy', $tour) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
            </svg>
        </button>

    </div>

</div>
