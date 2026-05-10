<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:2', 'max:100'],
            'email'     => ['nullable', 'email', 'unique:users,email', 'max:255'],
            'phone'     => ['nullable', 'string', 'unique:users,phone', 'max:20'],
            'password'  => ['required', Password::min(8)->letters()->numbers()],
            'user_type' => ['sometimes', 'string', 'in:customer,driver'],
            'currency'  => ['sometimes', 'string', 'size:3'],
            'referral_code' => ['sometimes', 'string', 'size:6'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->email && !$this->phone) {
                $validator->errors()->add('contact', 'Either email or phone number is required.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'An account with this email already exists.',
            'phone.unique' => 'An account with this phone number already exists.',
        ];
    }
}
