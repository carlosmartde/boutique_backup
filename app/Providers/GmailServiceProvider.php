<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GmailService;

class GmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GmailService::class, function ($app) {
            return new GmailService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}