<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'kbli_code',
        'business_size',
        'location',
        'location_type',
        'investment_level',
        'employee_count',
        'project_description',
        'deliverables_requested',
        'estimate_status',
        'auto_estimate',
        'final_quote',
        'confidence_score',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'contacted',
        'contacted_at',
        'converted_to_client',
        'client_id',
        'ip_address',
        'user_agent',
        'referrer_url',
        'utm_params',
    ];

    protected $casts = [
        'deliverables_requested' => 'array',
        'auto_estimate' => 'array',
        'final_quote' => 'array',
        'utm_params' => 'array',
        'confidence_score' => 'decimal:2',
        'employee_count' => 'integer',
        'contacted' => 'boolean',
        'converted_to_client' => 'boolean',
        'reviewed_at' => 'datetime',
        'contacted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the KBLI associated with this request
     */
    public function kbli()
    {
        return $this->belongsTo(Kbli::class, 'kbli_code', 'code');
    }

    /**
     * Get the admin who reviewed this request
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the client if converted
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope: Pending review
     */
    public function scopePending($query)
    {
        return $query->where('estimate_status', 'auto_estimated')
                    ->whereNull('reviewed_at');
    }

    /**
     * Scope: Not contacted yet
     */
    public function scopeNotContacted($query)
    {
        return $query->where('contacted', false);
    }

    /**
     * Scope: High potential (high confidence + large business)
     */
    public function scopeHighPotential($query)
    {
        return $query->where('confidence_score', '>=', 0.7)
                    ->whereIn('business_size', ['medium', 'large']);
    }

    /**
     * Get estimate_data as alias for auto_estimate (for view compatibility)
     */
    public function getEstimateDataAttribute()
    {
        return $this->auto_estimate;
    }

    /**
     * Get total estimated cost from auto_estimate
     */
    public function getTotalEstimatedCostAttribute()
    {
        if (!$this->auto_estimate || !isset($this->auto_estimate['cost_summary']['grand_total'])) {
            return 0;
        }
        
        return $this->auto_estimate['cost_summary']['grand_total'];
    }

    /**
     * Get formatted cost range
     */
    public function getFormattedCostRangeAttribute()
    {
        if (!$this->auto_estimate || !isset($this->auto_estimate['cost_summary']['formatted']['range'])) {
            return 'Not estimated';
        }
        
        return $this->auto_estimate['cost_summary']['formatted']['range'];
    }

    /**
     * Get business size label
     */
    public function getBusinessSizeLabelAttribute()
    {
        return match($this->business_size) {
            'micro' => 'Mikro (< 10 karyawan)',
            'small' => 'Kecil (10-50 karyawan)',
            'medium' => 'Menengah (50-100 karyawan)',
            'large' => 'Besar (> 100 karyawan)',
            default => 'Unknown',
        };
    }

    /**
     * Get investment level label
     */
    public function getInvestmentLevelLabelAttribute()
    {
        return match($this->investment_level) {
            'under_100m' => '< Rp 100 juta',
            '100m_500m' => 'Rp 100 - 500 juta',
            '500m_2b' => 'Rp 500 juta - 2 miliar',
            'over_2b' => '> Rp 2 miliar',
            default => 'Unknown',
        };
    }

    /**
     * Mark as contacted
     */
    public function markAsContacted()
    {
        $this->update([
            'contacted' => true,
            'contacted_at' => now(),
        ]);
    }

    /**
     * Convert to client
     */
    public function convertToClient(int $clientId)
    {
        $this->update([
            'converted_to_client' => true,
            'client_id' => $clientId,
        ]);
    }

    /**
     * Update estimate status
     */
    public function updateEstimateStatus(string $status, ?int $reviewerId = null)
    {
        $data = ['estimate_status' => $status];
        
        if (in_array($status, ['reviewed', 'approved'])) {
            $data['reviewed_by'] = $reviewerId;
            $data['reviewed_at'] = now();
        }
        
        $this->update($data);
    }
}
