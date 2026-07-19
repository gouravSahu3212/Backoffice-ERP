<?php

namespace App\Http\Requests\Admin;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
            ],
            'username' => [
                'required',
                'max:50',
                Rule::unique('users', 'username')->ignore($this->agent),
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'email' => [
                'required',
                'email:rfc,filter',
                Rule::unique('users')->ignore($this->agent),
            ],
            'phone' => [
                'nullable',
                'max:20',
                new Phone,
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Username may only contain letters, numbers, and underscores.',
        ];
    }
}
