<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            static $globalSettings = null;

            if ($globalSettings === null) {
                try {
                    // Cache the settings for the lifecycle of the request to avoid multiple queries
                    $globalSettings = \App\Models\Setting::whereIn('key', ['currency', 'currency_code', 'currency_symbol', 'favicon'])
                        ->pluck('value', 'key')
                        ->toArray();
                }
                catch (\Exception $e) {
                    $globalSettings = [];
                }
            }

            $currencyCode = strtoupper((string) ($globalSettings['currency'] ?? $globalSettings['currency_code'] ?? 'USD'));
            $currencySymbol = trim((string) ($globalSettings['currency_symbol'] ?? ''));
            if ($currencySymbol === '') {
                $currencySymbol = match ($currencyCode) {
                    'INR' => '₹',
                    'EUR' => '€',
                    'GBP' => '£',
                    'JPY' => '¥',
                    'AUD' => 'A$',
                    'CAD' => 'C$',
                    'SGD' => 'S$',
                    'AED' => 'AED ',
                    default => '$',
                };
            }

            $view->with('global_currency', $currencySymbol);
            $view->with('global_currency_code', $currencyCode);
            $view->with('global_favicon', $globalSettings['favicon'] ?? null);
        });
    }
}
