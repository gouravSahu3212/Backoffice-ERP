<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRoomSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'available_qty' => ['required', 'integer', 'min:0'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'month' => ['nullable', 'string', 'in:January,February,March,April,May,June,July,August,September,October,November,December'],
            'season_type' => ['nullable', 'string', 'in:High,Low,Shoulder'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
