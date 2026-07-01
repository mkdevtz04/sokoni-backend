<?php

namespace App\Providers;

use App\Services\KilakonaService;
use App\Services\OtpService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KilakonaService::class);

        $this->app->singleton(OtpService::class, function ($app) {
            return new OtpService($app->make(KilakonaService::class));
        });
    }

    public function boot(): void
    {
        // 5 attempts per minute per IP — login, register, reset password
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // 3 attempts per minute per IP — OTP resend & forgot-password (prevents SMS spam)
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // 60 requests per minute — authenticated API (keyed by user ID, falls back to IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
