<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kbli extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'kbli';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'description',
        'category',
        'sector',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Search KBLI by keyword (code or description)
     */
    public static function search(string $keyword, int $limit = 20)
    {
        return self::where('code', 'ILIKE', "%{$keyword}%")
            ->orWhere('description', 'ILIKE', "%{$keyword}%")
            ->limit($limit)
            ->get();
    }

    /**
     * Get KBLI by code
     */
    public static function findByCode(string $code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * Get KBLI by category
     */
    public static function getByCategory(string $category)
    {
        return self::where('category', $category)->get();
    }

    /**
     * Get KBLI by sector
     */
    public static function getBySector(string $sector)
    {
        return self::where('sector', $sector)->get();
    }
}
