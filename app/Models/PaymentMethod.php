<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'requires_cash_account',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_cash_account' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function clearCache(): void
    {
        Cache::forget('payment_methods.options');
    }

    public static function options(): Collection
    {
        return Cache::rememberForever('payment_methods.options', function () {
            return static::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    public static function activeCodes(): array
    {
        return static::options()
            ->where('is_active', true)
            ->pluck('code')
            ->all();
    }

    public static function activeCodesRequiringAccount(): array
    {
        return static::options()
            ->where('is_active', true)
            ->where('requires_cash_account', true)
            ->pluck('code')
            ->all();
    }

    public static function findByCode(string $code): ?self
    {
        return static::options()->firstWhere('code', $code);
    }

    public static function findActiveByCode(string $code): ?self
    {
        return static::options()->first(function ($method) use ($code) {
            return $method->code === $code && $method->is_active;
        });
    }

    public static function accountTypeFor(string $code): string
    {
        $method = static::findActiveByCode($code);

        return match ($method?->code) {
            'cash' => 'cash',
            default => 'bank',
        };
    }

    protected static function booted(): void
    {
        $flush = function () {
            static::clearCache();
        };

        static::saved($flush);
        static::deleted($flush);
    }
}
