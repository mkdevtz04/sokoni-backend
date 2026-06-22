<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'phone'         => ['required', 'string', 'unique:users,phone'],
            'email'         => ['nullable', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'role'          => ['nullable', 'in:customer,vendor,driver'],
            'business_name' => ['required_if:role,vendor', 'nullable', 'string', 'max:200'],
            'region'        => ['required_if:role,vendor', 'nullable', 'string', 'max:100'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
