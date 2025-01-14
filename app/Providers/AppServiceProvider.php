<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Cashier::calculateTaxes();
        RateLimiter::for('donations', function (object $job) {
            Log::info('Rate limit for donations', ['user_id' => $job->donation->user_id]);

            return Limit::perSecond(1, 10)->by($job->donation->user_id);
        });
        LogViewer::auth(function ($request) {
            if (config('app.env') === 'local') {
                return true;
            }

            return $request->user()
                && $request->user()->id === User::first()->id;
        });
    }
}
