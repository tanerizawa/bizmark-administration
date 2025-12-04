<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BetaTesterDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'beta_tester_id',
        'document_type',
        'document_title',
        'document_content',
        'is_signed',
        'signed_at',
        'signature_ip',
        'signature_user_agent',
        'signature_hash',
        'signature_data',
        'pdf_path',
        'pdf_filename',
        'pdf_filesize',
        'pdf_generated_at',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
        'pdf_generated_at' => 'datetime',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'signature_data' => 'array',
        'pdf_filesize' => 'integer',
    ];

    /**
     * Relationship dengan beta tester
     */
    public function betaTester(): BelongsTo
    {
        return $this->belongsTo(BetaTester::class);
    }

    /**
     * Relationship dengan verified user
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    /**
     * Get document title
     */
    public function getDocumentTitleAttribute(): string
    {
        return match($this->document_type) {
            'pakta_integritas' => 'Pakta Integritas',
            'nda' => 'Perjanjian Kerahasiaan (NDA)',
            default => 'Dokumen'
        };
    }

    /**
     * Get signed status badge
     */
    public function getSignedStatusAttribute(): array
    {
        if ($this->is_verified) {
            return [
                'label' => 'Terverifikasi',
                'color' => 'green',
                'icon' => 'check-circle'
            ];
        }
        
        if ($this->is_signed) {
            return [
                'label' => 'Tertandatangan',
                'color' => 'blue',
                'icon' => 'check'
            ];
        }
        
        return [
            'label' => 'Belum Ditandatangani',
            'color' => 'yellow',
            'icon' => 'clock'
        ];
    }

    /**
     * Get filled document content with beta tester data
     */
    public function getFilledContentAttribute(): string
    {
        if (!$this->betaTester) {
            return $this->document_content;
        }

        $replacements = [
            '[Nama Lengkap]' => $this->betaTester->full_name,
            '[Tempat Lahir]' => $this->betaTester->place_of_birth,
            '[Tanggal Lahir]' => $this->betaTester->formatted_birth_date,
            '[Tempat, Tanggal Lahir]' => $this->betaTester->birth_info,
            '[Alamat]' => $this->betaTester->address,
            '[Nomor Identitas]' => $this->betaTester->identity_number,
            '[Jenis Identitas]' => $this->betaTester->identity_type_label,
            '[Universitas]' => $this->betaTester->university,
            '[Fakultas]' => $this->betaTester->faculty,
            '[Program Studi]' => $this->betaTester->major,
            '[NIM]' => $this->betaTester->student_id,
            '[Semester]' => $this->betaTester->semester,
            '[Email]' => $this->betaTester->email,
            '[Nomor Telepon]' => $this->betaTester->phone,
            '[Nomor WhatsApp]' => $this->betaTester->whatsapp ?? $this->betaTester->phone,
            '[Tanggal Hari Ini]' => now()->isoFormat('DD MMMM YYYY'),
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $this->document_content
        );
    }

    /**
     * Generate signature hash
     */
    public function generateSignatureHash(): string
    {
        $data = $this->filled_content . 
                $this->betaTester->email . 
                $this->betaTester->full_name . 
                now()->toDateTimeString();
        
        return hash('sha256', $data);
    }

    /**
     * Sign document digitally
     */
    public function signDocument(): bool
    {
        $signatureData = [
            'beta_tester_id' => $this->beta_tester_id,
            'beta_tester_name' => $this->betaTester->full_name,
            'beta_tester_email' => $this->betaTester->email,
            'document_type' => $this->document_type,
            'signed_at' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'browser' => $this->parseBrowser(request()->userAgent()),
        ];

        $this->update([
            'is_signed' => true,
            'signed_at' => now(),
            'signature_ip' => request()->ip(),
            'signature_user_agent' => request()->userAgent(),
            'signature_hash' => $this->generateSignatureHash(),
            'signature_data' => $signatureData,
        ]);

        // Log activity
        $this->betaTester->logActivity(
            'document_signed',
            'Menandatangani dokumen: ' . $this->document_title,
            ['document_id' => $this->id]
        );

        // Generate PDF after signing
        $this->generatePdf();

        // Check if all documents signed
        $this->betaTester->markAsDocumentsSigned();

        return true;
    }

    /**
     * Generate PDF document
     */
    public function generatePdf(): bool
    {
        try {
            $pdf = Pdf::loadView('beta-tester.documents.pdf', [
                'document' => $this,
                'betaTester' => $this->betaTester,
                'content' => $this->filled_content,
            ]);

            $filename = sprintf(
                '%s_%s_%s.pdf',
                $this->document_type,
                $this->betaTester->registration_number,
                now()->format('YmdHis')
            );

            $path = 'beta-tester/documents/' . $this->betaTester->registration_number . '/' . $filename;
            
            Storage::disk('local')->put($path, $pdf->output());

            $this->update([
                'pdf_path' => $path,
                'pdf_filename' => $filename,
                'pdf_filesize' => Storage::disk('local')->size($path),
                'pdf_generated_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF for document ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify signature integrity
     */
    public function verifySignature(): bool
    {
        if (!$this->is_signed) {
            return false;
        }

        $currentHash = $this->generateSignatureHash();
        return $currentHash === $this->signature_hash;
    }

    /**
     * Verify document by admin
     */
    public function verify(int $userId): void
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);

        $this->betaTester->logActivity(
            'document_verified',
            'Dokumen diverifikasi: ' . $this->document_title,
            ['document_id' => $this->id, 'verified_by' => $userId]
        );
    }

    /**
     * Download PDF
     */
    public function downloadPdf()
    {
        if (!$this->pdf_path || !Storage::disk('local')->exists($this->pdf_path)) {
            $this->generatePdf();
        }

        return Storage::disk('local')->download($this->pdf_path, $this->pdf_filename);
    }

    /**
     * Parse browser from user agent
     */
    private function parseBrowser(string $userAgent): string
    {
        if (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Microsoft Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            return 'Google Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Mozilla Firefox';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            return 'Opera';
        }
        
        return 'Unknown Browser';
    }

    /**
     * Scope: Signed documents only
     */
    public function scopeSigned($query)
    {
        return $query->where('is_signed', true);
    }

    /**
     * Scope: Verified documents only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Pending verification
     */
    public function scopePendingVerification($query)
    {
        return $query->where('is_signed', true)
                     ->where('is_verified', false);
    }
}
