<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'location' => ['required', 'string', 'max:255'],
            'days' => ['required', 'integer', 'min:1', 'max:365'],
            'hotel_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'currency' => ['required', 'string', 'max:10'],
            'retail_price' => ['required', 'numeric', 'min:0'],
            'agent_price' => ['required', 'numeric', 'min:0'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'itinerary_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'highlights' => ['nullable', 'string'],
            'whats_included' => ['nullable', 'string'],
            'image_urls' => ['nullable', 'array'],
            'image_urls.*' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'departure_months' => ['nullable', 'array'],
            'departure_months.*.date' => ['required_with:departure_months.*', 'date'],
            'departure_months.*.slots' => ['required_with:departure_months.*', 'integer', 'min:1'],
            'max_capacity' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
