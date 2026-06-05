<?php

namespace App\Providers;

use App\Services\KilakonaService;
use App\Services\OtpService;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
