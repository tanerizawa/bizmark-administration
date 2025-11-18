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
     * Only returns 5-digit KBLI codes
     */
    public static function search(string $keyword, int $limit = 20)
    {
        return self::where(function ($query) use ($keyword) {
                $query->where('code', 'ILIKE', "%{$keyword}%")
                      ->orWhere('description', 'ILIKE', "%{$keyword}%");
            })
            ->whereRaw('LENGTH(code) = 5')
            ->orderBy('code')
            ->limit($limit)
            ->get();
    }

    /**
     * Get KBLI by code
     * Only returns 5-digit KBLI codes
     */
    public static function findByCode(string $code)
    {
        return self::where('code', $code)
            ->whereRaw('LENGTH(code) = 5')
            ->first();
    }

    /**
     * Get KBLI by sector
     * Only returns 5-digit KBLI codes
     */
    public static function getBySector(string $sector)
    {
        return self::where('sector', $sector)
            ->whereRaw('LENGTH(code) = 5')
            ->orderBy('code')
            ->get();
    }
}
