<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'group',
        'icon',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Retrieve cached category options.
     */
    public static function options(): Collection
    {
        return Cache::rememberForever('expense_categories.options', function () {
            return static::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Bust the cache when categories change.
     */
    protected static function booted(): void
    {
        $flush = function () {
            Cache::forget('expense_categories.options');
            ProjectExpense::clearCategoryCache();
        };

        static::saved($flush);
        static::deleted($flush);
    }
}
