<tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors" data-rate-id="{{ $rate->id }}">
    <td class="py-3.5 px-4 text-sm font-medium">
        {{ $rate->fromLocation->name }}
    </td>
    <td class="py-3.5 px-4 text-sm font-medium">
        {{ $rate->toLocation->name }}
    </td>
    <td class="py-3.5 px-4 text-sm text-gray-700">
        {{ $rate->vehicleType->name }}
    </td>
    <td class="py-3.5 px-4 text-sm text-gray-500">
        {{ $rate->fare_type }}
    </td>
    <td class="py-3.5 px-4 text-sm font-medium">
        {{ number_format($rate->price, 0) }}
    </td>
    <td class="py-3.5 px-4 text-sm text-gray-500">
        {{ $rate->currency }}
    </td>
    <td class="py-3.5 px-4">
        <span class="rate-status-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
            {{ $rate->is_active ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-500' }}">
            {{ $rate->is_active ? 'active' : 'inactive' }}
        </span>
    </td>
    <td class="py-3.5 px-4">
        <div class="flex items-center justify-end gap-3">
            {{-- Edit --}}
            <button type="button"
                class="edit-rate-btn text-gray-400 hover:text-gray-700 transition-colors"
                data-id="{{ $rate->id }}"
                data-from="{{ $rate->from_location_id }}"
                data-to="{{ $rate->to_location_id }}"
                data-vehicle="{{ $rate->vehicle_type_id }}"
                data-fare="{{ $rate->fare_type }}"
                data-price="{{ $rate->price }}"
                data-currency="{{ $rate->currency }}"
                data-notes="{{ $rate->notes }}"
                data-is-active="{{ $rate->is_active ? '1' : '0' }}"
                title="Edit rate">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil h-4 w-4">
                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path>
                    <path d="m15 5 4 4"></path>
                </svg>
            </button>

            {{-- Toggle Status --}}
            <button type="button"
                class="toggle-status-btn relative text-gray-400 hover:text-gray-700 transition-colors"
                data-id="{{ $rate->id }}"
                data-active="{{ $rate->is_active ? '1' : '0' }}"
                title="{{ $rate->is_active ? 'Deactivate' : 'Activate' }}">
                 @if($rate->is_active)
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="toggle-icon lucide lucide-toggle-right h-4 w-4"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect><circle cx="16" cy="12" r="2"></circle></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="toggle-icon lucide lucide-toggle-left h-4 w-4"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect><circle cx="8" cy="12" r="2"></circle></svg>
                @endif
                {{-- Spinner (hidden by default) --}}
                <svg class="toggle-spinner hidden animate-spin w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><radialGradient id="a18" cx=".66" fx=".66" cy=".3125" fy=".3125" gradientTransform="scale(1.5)"><stop offset="0" stop-color="currentColor"></stop><stop offset=".3" stop-color="currentColor" stop-opacity=".9"></stop><stop offset=".6" stop-color="currentColor" stop-opacity=".6"></stop><stop offset=".8" stop-color="currentColor" stop-opacity=".3"></stop><stop offset="1" stop-color="currentColor" stop-opacity="0"></stop></radialGradient><circle transform-origin="center" fill="none" stroke="url(#a18)" stroke-width="15" stroke-linecap="round" stroke-dasharray="200 1000" stroke-dashoffset="0" cx="100" cy="100" r="70"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2" values="360;0" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></circle><circle transform-origin="center" fill="none" opacity=".2" stroke="currentColor" stroke-width="15" stroke-linecap="round" cx="100" cy="100" r="70"></circle></svg>
            </button>
        </div>
    </td>
</tr>
