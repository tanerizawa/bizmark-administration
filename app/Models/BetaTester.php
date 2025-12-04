<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class BetaTester extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'registration_number',
        'access_token',
        'access_token_expires_at',
        'full_name',
        'place_of_birth',
        'date_of_birth',
        'address',
        'identity_number',
        'identity_type',
        'university',
        'faculty',
        'major',
        'student_id',
        'semester',
        'email',
        'phone',
        'whatsapp',
        'motivation',
        'status',
        'program_start_date',
        'program_end_date',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'gitlab_username',
        'gitlab_user_id',
        'gitlab_access_granted_at',
        'certificate_issued',
        'certificate_issued_at',
        'compensation_amount',
        'compensation_paid_at',
        'registration_ip',
        'registration_user_agent',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'program_start_date' => 'date',
        'program_end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'access_token_expires_at' => 'datetime',
        'gitlab_access_granted_at' => 'datetime',
        'certificate_issued' => 'boolean',
        'certificate_issued_at' => 'datetime',
        'compensation_paid_at' => 'datetime',
        'compensation_amount' => 'decimal:2',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'document_progress',
    ];

    /**
     * Generate unique registration number
     */
    public static function generateRegistrationNumber(): string
    {
        $year = date('Y');
        $lastNumber = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastNumber ? (int) substr($lastNumber->registration_number, -3) + 1 : 1;
        
        return sprintf('BT-%s-%03d', $year, $nextNumber);
    }

    /**
     * Generate secure access token
     */
    public function generateAccessToken(): string
    {
        $token = bin2hex(random_bytes(32));
        
        $this->update([
            'access_token' => $token,
            'access_token_expires_at' => now()->addMonths(6), // Token valid for 6 months
        ]);
        
        return $token;
    }

    /**
     * Check if access token is valid
     */
    public function hasValidAccessToken(): bool
    {
        if (!$this->access_token) {
            return false;
        }
        
        if ($this->access_token_expires_at && $this->access_token_expires_at->isPast()) {
            return false;
        }
        
        return true;
    }

    /**
     * Boot method untuk auto-generate registration number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($betaTester) {
            if (empty($betaTester->registration_number)) {
                $betaTester->registration_number = self::generateRegistrationNumber();
            }
            
            // Auto-generate access token on creation
            if (empty($betaTester->access_token)) {
                $betaTester->access_token = bin2hex(random_bytes(32));
                $betaTester->access_token_expires_at = now()->addMonths(6);
            }
        });
    }

    /**
     * Relationship dengan documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(BetaTesterDocument::class);
    }

    /**
     * Relationship dengan activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(BetaTesterActivity::class);
    }

    /**
     * Get pakta integritas document
     */
    public function paktaIntegritas()
    {
        return $this->documents()->where('document_type', 'pakta_integritas')->first();
    }

    /**
     * Get NDA document
     */
    public function ndaDocument()
    {
        return $this->documents()->where('document_type', 'nda')->first();
    }

    /**
     * Check if pakta integritas sudah ditandatangani
     */
    public function getHasSignedPaktaAttribute(): bool
    {
        return $this->documents()
            ->where('document_type', 'pakta_integritas')
            ->where('is_signed', true)
            ->exists();
    }

    /**
     * Check if NDA sudah ditandatangani
     */
    public function getHasSignedNdaAttribute(): bool
    {
        return $this->documents()
            ->where('document_type', 'nda')
            ->where('is_signed', true)
            ->exists();
    }

    /**
     * Check if semua dokumen sudah ditandatangani
     */
    public function getHasSignedAllDocumentsAttribute(): bool
    {
        return $this->has_signed_pakta && $this->has_signed_nda;
    }

    /**
     * Get document status percentage
     */
    public function getDocumentProgressAttribute(): int
    {
        $total = 2; // pakta_integritas + nda
        $signed = 0;
        
        if ($this->has_signed_pakta) $signed++;
        if ($this->has_signed_nda) $signed++;
        
        return round(($signed / $total) * 100);
    }

    /**
     * Get formatted birth date for documents
     */
    public function getFormattedBirthDateAttribute(): string
    {
        if (!$this->date_of_birth) return '';
        
        return $this->date_of_birth->isoFormat('DD MMMM YYYY');
    }

    /**
     * Get formatted birth place and date
     */
    public function getBirthInfoAttribute(): string
    {
        return $this->place_of_birth . ', ' . $this->formatted_birth_date;
    }

    /**
     * Get current date in Indonesian format
     */
    public function getCurrentDateFormattedAttribute(): string
    {
        return now()->isoFormat('DD MMMM YYYY');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'registered' => 'gray',
            'documents_pending' => 'yellow',
            'documents_signed' => 'blue',
            'active' => 'green',
            'completed' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'registered' => 'Terdaftar',
            'documents_pending' => 'Menunggu Tanda Tangan',
            'documents_signed' => 'Dokumen Lengkap',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get identity type label
     */
    public function getIdentityTypeLabelAttribute(): string
    {
        return $this->identity_type === 'ktp' ? 'KTP' : 'Kartu Tanda Mahasiswa';
    }

    /**
     * Log activity
     */
    public function logActivity(string $type, string $description, ?array $data = null): void
    {
        $this->activities()->create([
            'activity_type' => $type,
            'activity_description' => $description,
            'activity_data' => $data ? json_encode($data) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->headers->get('referer'),
        ]);
    }

    /**
     * Update status to documents_pending after registration
     */
    public function markAsDocumentsPending(): void
    {
        $this->update(['status' => 'documents_pending']);
        $this->logActivity('status_change', 'Status berubah menjadi: Menunggu Tanda Tangan');
    }

    /**
     * Update status to documents_signed after all signed
     */
    public function markAsDocumentsSigned(): void
    {
        if ($this->has_signed_all_documents) {
            $this->update(['status' => 'documents_signed']);
            $this->logActivity('status_change', 'Status berubah menjadi: Dokumen Lengkap');
        }
    }

    /**
     * Approve beta tester
     */
    public function approve(?string $startDate = null, ?string $endDate = null): void
    {
        $this->update([
            'status' => 'active',
            'approved_at' => now(),
            'program_start_date' => $startDate ?? now(),
            'program_end_date' => $endDate ?? now()->addMonth(),
        ]);
        
        $this->logActivity('approved', 'Beta tester disetujui dan diaktifkan');
    }

    /**
     * Reject beta tester
     */
    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
        
        $this->logActivity('rejected', 'Beta tester ditolak: ' . $reason);
    }

    /**
     * Scope: Active beta testers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Documents signed
     */
    public function scopeDocumentsSigned($query)
    {
        return $query->where('status', 'documents_signed')
            ->orWhere('status', 'active')
            ->orWhere('status', 'completed');
    }

    /**
     * Scope: Current program period
     */
    public function scopeCurrentPeriod($query)
    {
        return $query->whereNotNull('program_start_date')
            ->where('program_start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('program_end_date')
                    ->orWhere('program_end_date', '>=', now());
            });
    }

    /**
     * Scope: Pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'documents_signed');
    }
}
