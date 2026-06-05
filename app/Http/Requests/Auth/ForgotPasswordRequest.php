<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone'  => ['required_without:email', 'nullable', 'exists:users,phone'],
            'email'  => ['required_without:phone', 'nullable', 'email', 'exists:users,email'],
            'method' => ['nullable', 'in:sms,email'],
        ];
    }
}
