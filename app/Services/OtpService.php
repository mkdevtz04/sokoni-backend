<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(private KilakonaService $sms) {}

    public function generate(User $user, string $type = 'phone'): OtpCode
    {
        // Invalidate any existing unused OTPs of the same type
        OtpCode::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $otp = OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'type'       => $type,
            'expires_at' => Carbon::now()->addMinutes((int) env('OTP_EXPIRES_MINUTES', 10)),
        ]);

        $this->sendOtp($user, $code, $type);

        return $otp;
    }

    public function verify(User $user, string $code, string $type = 'phone'): bool
    {
        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otp || $otp->isExpired()) {
            return false;
        }

        $otp->update(['verified_at' => now()]);

        match ($type) {
            'phone'          => $user->update(['phone_verified_at' => now()]),
            'email'          => $user->update(['email_verified_at' => now()]),
            'password_reset' => null,
        };

        return true;
    }

    private function sendOtp(User $user, string $code, string $type): void
    {
        $minutes = env('OTP_EXPIRES_MINUTES', 10);

        $message = match ($type) {
            'phone'          => "SOKONI: Your verification code is {$code}. Valid for {$minutes} minutes. Do not share this code.",
            'email'          => "SOKONI: Your email verification code is {$code}. Valid for {$minutes} minutes.",
            'password_reset' => "SOKONI: Your password reset code is {$code}. Valid for {$minutes} minutes. Ignore if you did not request this.",
            default          => "SOKONI: Your OTP code is {$code}. Valid for {$minutes} minutes.",
        };

        $sent = $this->sms->send($user->phone, $message);

        if (! $sent) {
            // Log failure but don't throw — OTP is still saved in DB
            Log::warning('OTP SMS delivery failed', [
                'user_id' => $user->id,
                'phone'   => $user->phone,
                'type'    => $type,
            ]);
        }
    }
}
