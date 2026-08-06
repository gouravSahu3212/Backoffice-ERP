<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'passport_number' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'route_label' => ['required', 'string', 'max:255'],
            'vehicle' => ['nullable', 'string', 'max:255'],
            'transfer_type_category' => ['nullable', 'string', 'in:city,airport,fullday'],
            'rate_id' => ['nullable', 'integer'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'pickup_date' => ['nullable', 'date'],
            'pickup_time' => ['nullable', 'string', 'max:50'],
        ];
    }
}
