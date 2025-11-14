<?php

namespace App\Services;

use App\Models\DocumentDraft;
use App\Models\ComplianceCheck;
use Illuminate\Support\Facades\Log;

class UKLUPLComplianceService
{
    private array $issues = [];
    private array $scores = [
        'structure' => 0,
        'compliance' => 0,
        'formatting' => 0,
        'completeness' => 0,
    ];

    /**
     * Main validation method - orchestrates all checks
     */
    public function validate(DocumentDraft $draft): ComplianceCheck
    {
        $this->issues = [];
        $this->scores = [
            'structure' => 0,
            'compliance' => 0,
            'formatting' => 0,
            'completeness' => 0,
        ];

        try {
            // Run all validation checks
            $this->validateStructure($draft->content);
            $this->validateFormulirUKLUPL($draft->content);
            $this->validateIdentitasPemrakarsa($draft->content);
            $this->validateFormatting($draft->content);
            $this->validateCompleteness($draft);

            // Calculate overall score
            $overallScore = $this->calculateOverallScore();

            // Count issues by severity
            $issueStats = $this->countIssuesBySeverity();

            // Create or update compliance check record
            $complianceCheck = ComplianceCheck::updateOrCreate(
                ['draft_id' => $draft->id],
                [
                    'document_type' => 'UKL-UPL',
                    'overall_score' => $overallScore,
                    'structure_score' => $this->scores['structure'],
                    'compliance_score' => $this->scores['compliance'],
                    'formatting_score' => $this->scores['formatting'],
                    'completeness_score' => $this->scores['completeness'],
                    'issues' => $this->issues,
                    'status' => 'completed',
                    'total_issues' => count($this->issues),
                    'critical_issues' => $issueStats['critical'],
                    'warning_issues' => $issueStats['warning'],
                    'info_issues' => $issueStats['info'],
                    'checked_at' => now(),
                ]
            );

            return $complianceCheck;

        } catch (\Exception $e) {
            Log::error('Compliance validation failed', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
            ]);

            return ComplianceCheck::updateOrCreate(
                ['draft_id' => $draft->id],
                [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Validate document structure (BAB I, II, III, IV)
     */
    protected function validateStructure(string $content): void
    {
        $score = 100;
        $requiredBabs = ['BAB I', 'BAB II', 'BAB III', 'BAB IV'];
        $foundBabs = [];

        foreach ($requiredBabs as $bab) {
            if (preg_match('/\b' . preg_quote($bab, '/') . '\b/i', $content)) {
                $foundBabs[] = $bab;
            } else {
                $this->addIssue(
                    'structure',
                    'critical',
                    "$bab tidak ditemukan dalam dokumen",
                    'Tambahkan ' . $bab . ' sesuai struktur UKL-UPL standar',
                    $bab
                );
                $score -= 25;
            }
        }

        // Check BAB I sub-sections
        if (in_array('BAB I', $foundBabs)) {
            $bab1Sections = ['1.1', '1.2', '1.3'];
            foreach ($bab1Sections as $section) {
                if (!preg_match('/\b' . preg_quote($section, '/') . '\b/', $content)) {
                    $this->addIssue(
                        'structure',
                        'warning',
                        "Sub-bab $section (BAB I) tidak ditemukan",
                        'Tambahkan sub-bab: 1.1 Latar Belakang, 1.2 Tujuan dan Manfaat, 1.3 Peraturan Terkait',
                        "BAB I - Section $section"
                    );
                    $score -= 5;
                }
            }
        }

        // Check BAB III has Formulir UKL-UPL
        if (in_array('BAB III', $foundBabs)) {
            if (!preg_match('/FORMULIR UKL[\-\s]*UPL|Tabel.*UKL[\-\s]*UPL/i', $content)) {
                $this->addIssue(
                    'structure',
                    'critical',
                    "Formulir UKL-UPL tidak ditemukan di BAB III",
                    'Tambahkan tabel Formulir UKL-UPL dengan 12 kolom sesuai Permen LHK',
                    'BAB III - Section 3.5'
                );
                $score -= 20;
            }
        }

        $this->scores['structure'] = max(0, $score);
    }

    /**
     * Validate Formulir UKL-UPL format (12 kolom mandatory)
     */
    protected function validateFormulirUKLUPL(string $content): void
    {
        $score = 100;

        // Check for required columns in Formulir UKL-UPL
        $requiredColumns = [
            'Dampak Lingkungan',
            'Sumber Dampak',
            'Indikator Dampak',
            'Bentuk Pengelolaan',
            'Lokasi Pengelolaan',
            'Periode Pengelolaan',
            'Institusi Pengelolaan',
            'Bentuk Pemantauan',
            'Lokasi Pemantauan',
            'Periode Pemantauan',
            'Institusi Pemantauan',
        ];

        $foundColumns = 0;
        foreach ($requiredColumns as $column) {
            if (stripos($content, $column) !== false) {
                $foundColumns++;
            }
        }

        if ($foundColumns < 8) {
            $this->addIssue(
                'compliance',
                'critical',
                "Formulir UKL-UPL tidak lengkap (hanya $foundColumns dari 11+ kolom ditemukan)",
                'Gunakan format tabel UKL-UPL standar dengan 12 kolom: Dampak, Sumber, Indikator, Pengelolaan (Bentuk/Lokasi/Periode/Institusi), Pemantauan (Bentuk/Lokasi/Periode/Institusi)',
                'BAB III.5 - Formulir UKL-UPL'
            );
            $score -= 50;
        } elseif ($foundColumns < 11) {
            $this->addIssue(
                'compliance',
                'warning',
                "Formulir UKL-UPL mungkin tidak lengkap ($foundColumns dari 11+ kolom ditemukan)",
                'Periksa kembali apakah semua kolom wajib ada: Pelaksana & Pengawas untuk Pengelolaan dan Pemantauan',
                'BAB III.5 - Formulir UKL-UPL'
            );
            $score -= 20;
        }

        // Check for required impact categories
        $requiredImpacts = [
            'kualitas udara' => 'Dampak terhadap kualitas udara (debu, emisi)',
            'kebisingan' => 'Dampak kebisingan dari konstruksi/operasional',
            'air limbah' => 'Pengelolaan air limbah',
            'sampah' => 'Pengelolaan sampah/limbah padat',
        ];

        foreach ($requiredImpacts as $keyword => $description) {
            if (stripos($content, $keyword) === false) {
                $this->addIssue(
                    'compliance',
                    'warning',
                    "$description tidak tercantum dalam Formulir UKL-UPL",
                    "Tambahkan dampak '$keyword' beserta upaya pengelolaan dan pemantauannya",
                    'BAB III.5 - Formulir UKL-UPL'
                );
                $score -= 10;
            }
        }

        $this->scores['compliance'] = max(0, $score);
    }

    /**
     * Validate Identitas Pemrakarsa completeness
     */
    protected function validateIdentitasPemrakarsa(string $content): void
    {
        $score = 100;

        // Extract section containing identitas pemrakarsa
        $identitasSection = $this->extractSection($content, '2.1', '2.2');

        if (!$identitasSection) {
            $this->addIssue(
                'completeness',
                'critical',
                'Bagian Identitas Pemrakarsa (2.1) tidak ditemukan',
                'Tambahkan sub-bab 2.1 dengan identitas lengkap pemrakarsa',
                'BAB II.1'
            );
            $this->scores['completeness'] = 0;
            return;
        }

        // Check required fields
        $requiredFields = [
            'nama' => ['pattern' => '/nama\s*pemrakarsa\s*:?\s*(.+)/i', 'label' => 'Nama Pemrakarsa'],
            'alamat' => ['pattern' => '/alamat\s*:?\s*(.+)/i', 'label' => 'Alamat'],
            'telepon' => ['pattern' => '/telepon|telp|hp|no\.\s*hp\s*:?\s*(.+)/i', 'label' => 'No. Telepon'],
            'email' => ['pattern' => '/email|e-mail\s*:?\s*(.+)/i', 'label' => 'Email'],
        ];

        foreach ($requiredFields as $field => $config) {
            if (!preg_match($config['pattern'], $identitasSection)) {
                $this->addIssue(
                    'completeness',
                    'warning',
                    $config['label'] . ' tidak ditemukan atau tidak jelas',
                    'Tambahkan ' . $config['label'] . ' lengkap di bagian Identitas Pemrakarsa',
                    'BAB II.1'
                );
                $score -= 15;
            }
        }

        // Check for NIK/KTP
        if (!preg_match('/NIK|KTP|Nomor Identitas/i', $identitasSection)) {
            $this->addIssue(
                'completeness',
                'info',
                'NIK/No. KTP tidak tercantum',
                'Sebaiknya tambahkan NIK/No. KTP untuk kelengkapan data',
                'BAB II.1'
            );
            $score -= 5;
        }

        // Check for NPWP
        if (!preg_match('/NPWP/i', $identitasSection)) {
            $this->addIssue(
                'completeness',
                'info',
                'NPWP tidak tercantum',
                'Jika pemrakarsa memiliki NPWP, sebaiknya dicantumkan',
                'BAB II.1'
            );
            $score -= 5;
        }

        $this->scores['completeness'] = max(0, $score);
    }

    /**
     * Validate document formatting
     */
    protected function validateFormatting(string $content): void
    {
        $score = 100;

        // Check numbering consistency
        if (!$this->hasConsistentNumbering($content)) {
            $this->addIssue(
                'formatting',
                'warning',
                'Penomoran BAB atau sub-bab tidak konsisten',
                'Gunakan format: BAB I, BAB II (romawi) dan 1.1, 1.2, 2.1 (desimal) untuk sub-bab',
                'Document-wide'
            );
            $score -= 15;
        }

        // Check for proper spacing (minimal line breaks between sections)
        $babCount = preg_match_all('/BAB\s+[IVX]+/i', $content);
        $lineBreakCount = preg_match_all('/\n\s*\n/', $content);
        
        if ($lineBreakCount < $babCount * 2) {
            $this->addIssue(
                'formatting',
                'info',
                'Spasi antar bagian mungkin kurang',
                'Pastikan ada line break yang cukup antar BAB dan sub-bab untuk readability',
                'Document-wide'
            );
            $score -= 10;
        }

        // Check if date format is consistent (Indonesian format)
        if (preg_match_all('/\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}/', $content) > 0) {
            $this->addIssue(
                'formatting',
                'warning',
                'Format tanggal menggunakan angka (DD/MM/YYYY)',
                'Sebaiknya gunakan format: DD Bulan YYYY (contoh: 15 Januari 2025)',
                'Document-wide'
            );
            $score -= 10;
        }

        $this->scores['formatting'] = max(0, $score);
    }

    /**
     * Validate overall completeness
     */
    protected function validateCompleteness(DocumentDraft $draft): void
    {
        // Already calculated in validateIdentitasPemrakarsa
        // This method can be extended for additional completeness checks
        
        // Check minimum content length
        $wordCount = str_word_count(strip_tags($draft->content));
        
        if ($wordCount < 1000) {
            $this->addIssue(
                'completeness',
                'critical',
                "Dokumen terlalu pendek ($wordCount kata, minimum 1000 kata)",
                'UKL-UPL standar minimal 10-15 halaman. Lengkapi semua bagian dengan detail yang memadai',
                'Document-wide'
            );
            $this->scores['completeness'] -= 30;
        } elseif ($wordCount < 2000) {
            $this->addIssue(
                'completeness',
                'warning',
                "Dokumen mungkin kurang detail ($wordCount kata)",
                'Pastikan semua bagian dijelaskan dengan lengkap dan detail',
                'Document-wide'
            );
            $this->scores['completeness'] -= 10;
        }
    }

    /**
     * Add issue to the list
     */
    protected function addIssue(string $category, string $severity, string $message, string $suggestion, string $location): void
    {
        $this->issues[] = [
            'category' => $category,
            'severity' => $severity,
            'message' => $message,
            'suggestion' => $suggestion,
            'location' => $location,
        ];
    }

    /**
     * Calculate overall compliance score
     */
    protected function calculateOverallScore(): float
    {
        // Weighted average
        $weights = [
            'structure' => 0.25,
            'compliance' => 0.35,
            'formatting' => 0.15,
            'completeness' => 0.25,
        ];

        $overallScore = 0;
        foreach ($this->scores as $category => $score) {
            $overallScore += $score * $weights[$category];
        }

        return round($overallScore, 2);
    }

    /**
     * Count issues by severity
     */
    protected function countIssuesBySeverity(): array
    {
        $counts = [
            'critical' => 0,
            'warning' => 0,
            'info' => 0,
        ];

        foreach ($this->issues as $issue) {
            $severity = $issue['severity'] ?? 'info';
            if (isset($counts[$severity])) {
                $counts[$severity]++;
            }
        }

        return $counts;
    }

    /**
     * Extract specific section from content
     */
    protected function extractSection(string $content, string $startSection, ?string $endSection = null): ?string
    {
        $pattern = '/(' . preg_quote($startSection, '/') . '.*?)';
        if ($endSection) {
            $pattern .= '(?=' . preg_quote($endSection, '/') . '|$)';
        } else {
            $pattern .= '$/s';
        }

        if (preg_match($pattern . '/s', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check if numbering is consistent
     */
    protected function hasConsistentNumbering(string $content): bool
    {
        // Check for BAB with Roman numerals
        $babRoman = preg_match_all('/BAB\s+[IVX]+/i', $content);
        $babArabic = preg_match_all('/BAB\s+\d+/i', $content);

        // Should use Roman (not Arabic) for BAB
        if ($babArabic > $babRoman) {
            return false;
        }

        // Check for consistent sub-numbering (1.1, 1.2 format)
        $decimalNumbering = preg_match_all('/\d+\.\d+/', $content);
        
        // Should have decimal numbering for sub-sections
        if ($decimalNumbering < 5) {
            return false;
        }

        return true;
    }

    /**
     * Get human-readable compliance summary
     */
    public function getComplianceSummary(ComplianceCheck $check): string
    {
        $score = $check->overall_score;
        $critical = $check->critical_issues;

        if ($score >= 90 && $critical == 0) {
            return "✅ Dokumen SANGAT BAIK dan siap untuk diajukan. Compliance rate {$score}%.";
        } elseif ($score >= 80 && $critical == 0) {
            return "✅ Dokumen BAIK dan memenuhi standar minimum. Ada {$check->warning_issues} poin yang bisa diperbaiki. Compliance rate {$score}%.";
        } elseif ($score >= 70) {
            return "⚠️ Dokumen CUKUP namun perlu perbaikan. Ada {$critical} isu kritis dan {$check->warning_issues} warning. Compliance rate {$score}%.";
        } elseif ($score >= 60) {
            return "❌ Dokumen KURANG memenuhi standar. Ada {$critical} isu kritis yang harus diperbaiki. Compliance rate {$score}%.";
        } else {
            return "❌ Dokumen TIDAK MEMENUHI standar UKL-UPL. Banyak komponen penting yang hilang atau tidak sesuai. Compliance rate {$score}%.";
        }
    }
}
