<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCityTransferRateRequest extends FormRequest
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
            'from_location_id' => ['required', 'integer', 'exists:transfer_locations,id'],
            'to_location_id'   => ['required', 'integer', 'exists:transfer_locations,id', 'different:from_location_id'],
            'vehicle_type_id'  => ['required', 'integer', 'exists:vehicle_types,id'],
            'fare_type'        => ['required', 'in:fixed,per_km,per_hr'],
            'price'            => ['required', 'numeric', 'min:0'],
            'currency'         => ['required', 'string', 'max:10'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'is_active'        => ['nullable', 'boolean'],
        ];
    }
}
