<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShapefileProject extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'geojson',
        'area_m2',
        'area_ha',
        'perimeter_m',
        'metadata',
        'rtrw_zona',
        'rtrw_perda',
        'rtrw_remark',
        'rtrw_raw',
        'file_path',
        'session_token',
        'agreed_terms_at',
        'service_inquiry_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'geojson' => 'array',
        'metadata' => 'array',
        'rtrw_raw' => 'array',
        'area_m2' => 'decimal:2',
        'area_ha' => 'decimal:6',
        'perimeter_m' => 'decimal:2',
        'agreed_terms_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceInquiry(): BelongsTo
    {
        return $this->belongsTo(ServiceInquiry::class);
    }
}
