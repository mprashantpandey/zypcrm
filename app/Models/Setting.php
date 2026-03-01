<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected static function booted(): void
    {
        static::saved(function (Setting $setting): void {
            Cache::forget("setting:{$setting->key}");
        });

        static::deleted(function (Setting $setting): void {
            Cache::forget("setting:{$setting->key}");
        });
    }

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting:{$key}", now()->addMinutes(10), function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function getCurrencySymbol(?string $default = null): ?string
    {
        $configured = trim((string) static::getValue('currency_symbol', ''));
        if ($configured !== '') {
            return $configured;
        }

        $code = static::getCurrencyCode($default ? null : 'USD');
        $map = [
            'USD' => '$',
            'INR' => '₹',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'SGD' => 'S$',
            'AED' => 'AED ',
        ];

        if ($code && isset($map[$code])) {
            return $map[$code];
        }

        return $default ?? '$';
    }

    public static function getCurrencyCode(?string $default = null): string
    {
        $code = static::getValue('currency', static::getValue('currency_code', $default ?? 'USD'));

        return strtoupper((string) ($code ?: ($default ?? 'USD')));
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        return filter_var(static::getValue($key, $default ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN);
    }
}
