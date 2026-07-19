<?php

namespace App\Http\Requests\Admin;

use App\Rules\Phone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgentRequest extends FormRequest
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
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'email' => [
                'required',
                'email:rfc,filter',
                'unique:users,email',
            ],
            'phone' => [
                'nullable',
                'max:20',
                new Phone,
            ],
            'password' => [
                'required',
                'min:8',
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
