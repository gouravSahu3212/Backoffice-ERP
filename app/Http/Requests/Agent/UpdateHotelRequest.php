<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location_id' => ['required', 'exists:transfer_locations,id'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
            'star_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }
}
