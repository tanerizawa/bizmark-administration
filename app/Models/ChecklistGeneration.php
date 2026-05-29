<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistGeneration extends Model
{
    protected $fillable = [
        'kbli_code',
        'permit_type',
        'city',
        'business_scale',
        'checklist_data',
        'pdf_path',
        'requester_email',
        'ip_address',
    ];

    protected $casts = [
        'checklist_data' => 'array',
    ];
}
