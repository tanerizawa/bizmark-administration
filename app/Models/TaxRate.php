<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TaxRate extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'description',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function clearCache(): void
    {
        Cache::forget('tax_rates.options');
    }

    public static function options(): Collection
    {
        return Cache::rememberForever('tax_rates.options', function () {
            return static::query()
                ->orderBy('sort_order')
                ->orderBy('rate')
                ->get();
        });
    }

    public static function active(): Collection
    {
        return static::options()->where('is_active', true)->values();
    }

    public static function defaultRate(): ?self
    {
        return static::options()->firstWhere('is_default', true)
            ?? static::options()->first();
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
