<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $table = 'system_settings';
    
    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'maintenance_mode',
        'email_notifications',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'email_notifications' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::create();
    }
}
