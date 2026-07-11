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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>

            {{-- Toggle Status --}}
            <button type="button"
                class="toggle-status-btn relative text-gray-400 hover:text-gray-700 transition-colors"
                data-id="{{ $rate->id }}"
                data-active="{{ $rate->is_active ? '1' : '0' }}"
                title="{{ $rate->is_active ? 'Deactivate' : 'Activate' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 toggle-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{-- Spinner (hidden by default) --}}
                <svg class="toggle-spinner hidden animate-spin w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4v8z"></path>
                </svg>
            </button>
        </div>
    </td>
</tr>
