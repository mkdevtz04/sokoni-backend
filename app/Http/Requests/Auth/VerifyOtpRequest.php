<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'exists:users,phone'],
            'code'  => ['required', 'string', 'size:6'],
            'type'  => ['nullable', 'in:phone,email,password_reset'],
        ];
    }
}
