<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceCostRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_number',
        'applicant_type',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'nik',
        'occupation',
        'company_name',
        'npwp',
        'nib',
        'business_type',
        'business_sector',
        'pic_name',
        'pic_position',
        'service_category',
        'services_requested',
        'project_description',
        'ai_letter_body',
        'project_location',
        'estimated_budget',
        'timeline_expectation',
        'documents',
        'status',
        'admin_notes',
        'quoted_price',
        'quoted_timeline',
        'quote_details',
        'ai_quote_status',
        'quoted_at',
        'responded_at',
        'reviewed_by',
        'reviewed_at',
        'completed_at',
        'completed_by',
        'archived_at',
        'source',
        'referral_code',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'services_requested' => 'array',
        'documents' => 'array',
        'quote_details' => 'array',
        'estimated_budget' => 'decimal:2',
        'quoted_price' => 'decimal:2',
        'quoted_at' => 'datetime',
        'responded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate request number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }

    /**
     * Generate unique request number
     */
    public static function generateRequestNumber(): string
    {
        do {
            $number = 'SCR-'.strtoupper(Str::random(8));
        } while (self::where('request_number', $number)->exists());

        return $number;
    }

    /**
     * Get display name based on applicant type
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->applicant_type === 'badan') {
            return $this->company_name ?: $this->name;
        }

        return $this->name;
    }

    /**
     * Check if applicant is a company/entity
     */
    public function isCompany(): bool
    {
        return $this->applicant_type === 'badan';
    }

    /**
     * Check if applicant is an individual
     */
    public function isIndividual(): bool
    {
        return $this->applicant_type === 'perorangan';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'reviewing' => 'bg-blue-100 text-blue-800',
            'quoted' => 'bg-purple-100 text-purple-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get human readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Review',
            'reviewing' => 'Sedang Ditinjau',
            'quoted' => 'Penawaran Terkirim',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get business type label
     */
    public function getBusinessTypeLabelAttribute(): ?string
    {
        return match ($this->business_type) {
            'pt' => 'PT (Perseroan Terbatas)',
            'cv' => 'CV (Commanditaire Vennootschap)',
            'ud' => 'UD (Usaha Dagang)',
            'yayasan' => 'Yayasan',
            'koperasi' => 'Koperasi',
            'lainnya' => 'Lainnya',
            default => null,
        };
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for individual applicants
     */
    public function scopeIndividuals($query)
    {
        return $query->where('applicant_type', 'perorangan');
    }

    /**
     * Scope for company applicants
     */
    public function scopeCompanies($query)
    {
        return $query->where('applicant_type', 'badan');
    }

    /**
     * Get formatted estimated budget
     */
    public function getFormattedBudgetAttribute(): ?string
    {
        if (! $this->estimated_budget) {
            return null;
        }

        return 'Rp '.number_format($this->estimated_budget, 0, ',', '.');
    }

    /**
     * Get formatted quoted price
     */
    public function getFormattedQuotedPriceAttribute(): ?string
    {
        if (! $this->quoted_price) {
            return null;
        }

        return 'Rp '.number_format($this->quoted_price, 0, ',', '.');
    }

    /**
     * Get list of service categories
     */
    public static function getServiceCategories(): array
    {
        return [
            'perizinan' => 'Perizinan Usaha',
            'lingkungan' => 'Perizinan Lingkungan',
            'konstruksi' => 'Perizinan Konstruksi',
            'ketenagakerjaan' => 'Ketenagakerjaan',
            'perpajakan' => 'Perpajakan',
            'legalitas' => 'Legalitas Perusahaan',
            'sertifikasi' => 'Sertifikasi',
            'lainnya' => 'Lainnya',
        ];
    }

    /**
     * Get available services by category
     */
    public static function getServicesByCategory(): array
    {
        return [
            'perizinan' => [
                'nib' => 'NIB (Nomor Induk Berusaha)',
                'izin_usaha' => 'Izin Usaha OSS',
                'izin_lokasi' => 'Izin Lokasi',
                'izin_komersial' => 'Izin Komersial/Operasional',
                'siup' => 'SIUP',
                'tdp' => 'TDP',
            ],
            'lingkungan' => [
                'amdal' => 'AMDAL',
                'ukl_upl' => 'UKL-UPL',
                'sppl' => 'SPPL',
                'izin_lb3' => 'Izin Limbah B3',
                'izin_emisi' => 'Izin Emisi',
                'pertek_air' => 'Pertek Air',
            ],
            'konstruksi' => [
                'imb_pbg' => 'IMB/PBG',
                'slf' => 'SLF (Sertifikat Laik Fungsi)',
                'sbujk' => 'SBUJK',
                'sku' => 'SKU Konstruksi',
            ],
            'ketenagakerjaan' => [
                'wlkp' => 'WLKP',
                'pp_pkb' => 'PP/PKB',
                'izin_tenaga_asing' => 'Izin Tenaga Kerja Asing',
                'bpjs_tk' => 'BPJS Ketenagakerjaan',
            ],
            'perpajakan' => [
                'npwp_badan' => 'NPWP Badan',
                'pkp' => 'Pengusaha Kena Pajak',
                'konsultasi_pajak' => 'Konsultasi Perpajakan',
            ],
            'legalitas' => [
                'pendirian_pt' => 'Pendirian PT',
                'pendirian_cv' => 'Pendirian CV',
                'perubahan_akta' => 'Perubahan Akta',
                'pembubaran' => 'Pembubaran Perusahaan',
            ],
            'sertifikasi' => [
                'iso_9001' => 'ISO 9001',
                'iso_14001' => 'ISO 14001',
                'iso_45001' => 'ISO 45001',
                'halal' => 'Sertifikasi Halal',
                'sni' => 'SNI',
            ],
            'lainnya' => [
                'konsultasi' => 'Konsultasi Umum',
                'custom' => 'Layanan Kustom',
            ],
        ];
    }

    /**
     * Get the user who reviewed this request
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who completed this request
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
