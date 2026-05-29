<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceReport extends Model
{
    protected $fillable = [
        'project_id',
        'template_id',
        'generated_by',
        'input_data',
        'pdf_path',
        'status',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'input_data' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'generated_by');
    }

    public static array $statusLabels = [
        'draft' => 'Draft',
        'generating' => 'Sedang Dibuat',
        'ready' => 'Siap Diunduh',
        'submitted' => 'Telah Disubmit',
        'approved' => 'Disetujui DINAS',
    ];

    public static array $statusColors = [
        'draft' => '#94a3b8',
        'generating' => '#f59e0b',
        'ready' => '#10b981',
        'submitted' => '#6366f1',
        'approved' => '#059669',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? '#94a3b8';
    }
}
