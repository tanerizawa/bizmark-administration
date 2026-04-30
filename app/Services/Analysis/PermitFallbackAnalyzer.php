<?php

namespace App\Services\Analysis;

/**
 * Deterministic fallback permit analyzer.
 *
 * Dipakai oleh FreeAIAnalysisService ketika panggilan ke LLM gagal (timeout,
 * rate limit, invalid JSON response). Menghasilkan rekomendasi izin berbasis
 * heuristik keyword-matching + skala investasi, sehingga user tetap mendapat
 * hasil analisis meskipun AI provider sedang down.
 */
class PermitFallbackAnalyzer
{
    /**
     * Analyze form data and return fallback permit recommendation.
     *
     * @param  array<string, mixed>  $formData
     * @return array<string, mixed>
     */
    public function analyze(array $formData): array
    {
        $scale = $formData['business_scale'] ?? 'small';
        $investment = $formData['estimated_investment'] ?? 'under_100m';
        $locationCategory = $formData['location_category'] ?? 'commercial';
        $businessActivity = $formData['business_activity'] ?? '';

        // Determine scale multiplier for costs
        $scaleMultiplier = match ($scale) {
            'micro' => 1.0,
            'small' => 1.3,
            'medium' => 1.8,
            'large' => 2.5,
            default => 1.0,
        };

        // Determine if environmental permits are likely needed
        $needsEnvironmental = $this->likelyNeedsEnvironmental($businessActivity, $investment);
        $needsPBG = $this->likelyNeedsPBG($businessActivity, $locationCategory);
        $needsAMDAL = $this->likelyNeedsAMDAL($businessActivity, $investment, $scale);
        $needsB3 = $this->likelyNeedsB3($businessActivity);

        // ====== TAHAP 1: LEGALITAS DASAR (Foundational) ======
        $permits = [
            [
                'code' => 'OSS_NIB',
                'name' => 'Nomor Induk Berusaha (NIB) via OSS RBA',
                'priority' => 'critical',
                'category' => 'foundational',
                'type' => 'mandatory',
                'estimated_timeline' => '1-3 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 0,
                    'note' => 'Gratis (pendaftaran online via OSS)',
                ],
                'consultant_fee' => [
                    'min' => (int) (1500000 * $scaleMultiplier),
                    'max' => (int) (3000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pendaftaran dan verifikasi data',
                ],
                'total_cost_range' => CostFormatter::range(1500000 * $scaleMultiplier, 3000000 * $scaleMultiplier),
                'description' => 'Identitas tunggal pelaku usaha yang diterbitkan melalui OSS RBA. Wajib untuk semua jenis dan skala usaha di Indonesia sesuai UU Cipta Kerja.',
                'issuing_authority' => 'Kementerian Investasi/BKPM via OSS',
                'legal_basis' => 'UU 6/2023 (Cipta Kerja), PP 5/2021 tentang Perizinan Berusaha Berbasis Risiko',
                'prerequisites' => [],
                'triggers_next' => ['NPWP Badan Usaha', 'PKKPR/KKPR'],
            ],
            [
                'code' => 'NPWP_BADAN',
                'name' => 'NPWP Badan Usaha',
                'priority' => 'critical',
                'category' => 'foundational',
                'type' => 'mandatory',
                'estimated_timeline' => '1-3 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 0,
                    'note' => 'Gratis (pendaftaran online via Coretax/e-Registration)',
                ],
                'consultant_fee' => [
                    'min' => (int) (1000000 * $scaleMultiplier),
                    'max' => (int) (1500000 * $scaleMultiplier),
                    'note' => 'Pendampingan registrasi dan pengaturan pajak',
                ],
                'total_cost_range' => CostFormatter::range(1000000 * $scaleMultiplier, 1500000 * $scaleMultiplier),
                'description' => 'Nomor Pokok Wajib Pajak untuk badan usaha. Diperlukan untuk kewajiban perpajakan dan transaksi bisnis.',
                'issuing_authority' => 'Direktorat Jenderal Pajak (Coretax)',
                'legal_basis' => 'UU 6/2023, UU 7/2021 tentang Harmonisasi Peraturan Perpajakan',
                'prerequisites' => ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['PKKPR/KKPR', 'Sertifikat Standar'],
            ],
        ];

        // ====== TAHAP 2: TATA RUANG (Spatial Planning) ======
        // PKKPR for commercial/industrial locations or larger scale
        $needsPKKPR = in_array($locationCategory, ['industrial', 'commercial']) || in_array($scale, ['medium', 'large']);
        if ($needsPKKPR) {
            $permits[] = [
                'code' => 'PKKPR',
                'name' => 'Persetujuan Kesesuaian Kegiatan Pemanfaatan Ruang (PKKPR)',
                'priority' => 'critical',
                'category' => 'foundational',
                'type' => 'mandatory',
                'estimated_timeline' => '5-14 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 500000,
                    'note' => 'Biaya administrasi (gratis jika via OSS untuk risiko rendah-menengah)',
                ],
                'consultant_fee' => [
                    'min' => (int) (2000000 * $scaleMultiplier),
                    'max' => (int) (5000000 * $scaleMultiplier),
                    'note' => 'Pendampingan analisis kesesuaian tata ruang',
                ],
                'total_cost_range' => CostFormatter::range(2000000 * $scaleMultiplier, 5500000 * $scaleMultiplier),
                'description' => 'Persetujuan kesesuaian lokasi usaha dengan Rencana Tata Ruang Wilayah (RTRW/RDTR). Pengganti Izin Lokasi lama. Wajib sebelum mengurus izin lingkungan dan bangunan.',
                'issuing_authority' => 'Pemerintah Daerah / ATR-BPN via OSS',
                'legal_basis' => 'PP 21/2021 tentang Penyelenggaraan Penataan Ruang, UU 6/2023',
                'prerequisites' => ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['AMDAL/UKL-UPL/SPPL', 'PBG'],
            ];
        }

        // ====== TAHAP 3: LINGKUNGAN HIDUP (Environmental) ======
        if ($needsAMDAL) {
            $permits[] = [
                'code' => 'AMDAL',
                'name' => 'Analisis Mengenai Dampak Lingkungan (AMDAL)',
                'priority' => 'critical',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '60-120 hari kerja',
                'government_fee' => [
                    'min' => 10000000,
                    'max' => (int) (100000000 * ($scale === 'large' ? 1.0 : 0.5)),
                    'note' => 'Biaya evaluasi, sidang komisi AMDAL, dan PNBP KLHK',
                ],
                'consultant_fee' => [
                    'min' => (int) (50000000 * $scaleMultiplier),
                    'max' => (int) (150000000 * $scaleMultiplier),
                    'note' => 'Studi AMDAL lengkap: ANDAL, RKL-RPL, sidang komisi penilai',
                ],
                'total_cost_range' => CostFormatter::range(60000000 * $scaleMultiplier, 250000000 * $scaleMultiplier),
                'description' => 'Dokumen kajian dampak lingkungan wajib untuk usaha berisiko tinggi. Mencakup studi ANDAL, RKL-RPL, dan sidang Komisi Penilai AMDAL.',
                'issuing_authority' => 'Kementerian LHK / Dinas Lingkungan Hidup',
                'legal_basis' => 'UU 6/2023, PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup',
                'prerequisites' => $needsPKKPR ? ['Nomor Induk Berusaha (NIB)', 'PKKPR'] : ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['PBG', 'Sertifikat Standar'],
            ];
        } elseif ($needsEnvironmental) {
            $permits[] = [
                'code' => 'UKL_UPL',
                'name' => 'Upaya Pengelolaan Lingkungan Hidup (UKL-UPL)',
                'priority' => 'high',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '14-30 hari kerja',
                'government_fee' => [
                    'min' => 1000000,
                    'max' => 5000000,
                    'note' => 'Biaya pemeriksaan dan evaluasi dokumen DLH',
                ],
                'consultant_fee' => [
                    'min' => (int) (10000000 * $scaleMultiplier),
                    'max' => (int) (25000000 * $scaleMultiplier),
                    'note' => 'Penyusunan dokumen UKL-UPL dan pendampingan',
                ],
                'total_cost_range' => CostFormatter::range(11000000 * $scaleMultiplier, 30000000 * $scaleMultiplier),
                'description' => 'Dokumen pengelolaan lingkungan untuk usaha risiko menengah (dampak lingkungan sedang). Wajib sebelum operasional.',
                'issuing_authority' => 'Dinas Lingkungan Hidup',
                'legal_basis' => 'UU 6/2023, PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup',
                'prerequisites' => $needsPKKPR ? ['Nomor Induk Berusaha (NIB)', 'PKKPR'] : ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['Sertifikat Standar', 'PBG'],
            ];
        } else {
            // Low-risk: SPPL (Surat Pernyataan Pengelolaan Lingkungan)
            $permits[] = [
                'code' => 'SPPL',
                'name' => 'Surat Pernyataan Pengelolaan Lingkungan (SPPL)',
                'priority' => 'high',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '1-3 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 0,
                    'note' => 'Gratis (terintegrasi dalam OSS untuk risiko rendah)',
                ],
                'consultant_fee' => [
                    'min' => (int) (500000 * $scaleMultiplier),
                    'max' => (int) (1500000 * $scaleMultiplier),
                    'note' => 'Pendampingan penyusunan SPPL',
                ],
                'total_cost_range' => CostFormatter::range(500000 * $scaleMultiplier, 1500000 * $scaleMultiplier),
                'description' => 'Dokumen pernyataan pengelolaan lingkungan untuk usaha risiko rendah. Diterbitkan otomatis melalui OSS bersamaan dengan NIB.',
                'issuing_authority' => 'OSS / Dinas Lingkungan Hidup',
                'legal_basis' => 'UU 6/2023, PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup',
                'prerequisites' => ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['Sertifikat Standar'],
            ];
        }

        // Add B3 (hazardous waste) permits if needed
        if ($needsB3) {
            $permits[] = [
                'code' => 'TPS_LB3',
                'name' => 'Izin Tempat Penyimpanan Sementara Limbah B3 (TPS-LB3)',
                'priority' => 'high',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '14-30 hari kerja',
                'government_fee' => [
                    'min' => 500000,
                    'max' => 1000000,
                    'note' => 'Biaya verifikasi teknis fasilitas TPS',
                ],
                'consultant_fee' => [
                    'min' => (int) (7000000 * $scaleMultiplier),
                    'max' => (int) (12000000 * $scaleMultiplier),
                    'note' => 'Penyusunan SOP, desain TPS, verifikasi fasilitas',
                ],
                'total_cost_range' => CostFormatter::range(7500000 * $scaleMultiplier, 13000000 * $scaleMultiplier),
                'description' => 'Izin penyimpanan sementara limbah Bahan Berbahaya dan Beracun (B3) sebelum diangkut ke fasilitas pengolahan.',
                'issuing_authority' => 'Dinas Lingkungan Hidup / KemenLHK',
                'legal_basis' => 'UU 6/2023, PP 22/2021, Permen LHK 6/2021',
                'prerequisites' => [$needsAMDAL ? 'AMDAL' : 'UKL-UPL'],
                'triggers_next' => ['Izin Pengelolaan Limbah B3'],
            ];
            $permits[] = [
                'code' => 'IZIN_KELOLA_B3',
                'name' => 'Izin Pengelolaan Limbah B3',
                'priority' => 'high',
                'category' => 'sectoral',
                'type' => 'mandatory',
                'estimated_timeline' => '30-60 hari kerja',
                'government_fee' => [
                    'min' => 1000000,
                    'max' => 5000000,
                    'note' => 'PNBP KemenLHK untuk evaluasi pengelolaan B3',
                ],
                'consultant_fee' => [
                    'min' => (int) (15000000 * $scaleMultiplier),
                    'max' => (int) (50000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pengurusan izin pengelolaan B3 lengkap',
                ],
                'total_cost_range' => CostFormatter::range(16000000 * $scaleMultiplier, 55000000 * $scaleMultiplier),
                'description' => 'Izin untuk pengumpulan, penyimpanan, dan pengolahan limbah B3. Wajib untuk semua kegiatan yang menghasilkan atau mengelola limbah B3.',
                'issuing_authority' => 'Kementerian Lingkungan Hidup dan Kehutanan',
                'legal_basis' => 'UU 6/2023, PP 22/2021, Permen LHK 6/2021',
                'prerequisites' => ['TPS-LB3', $needsAMDAL ? 'AMDAL' : 'UKL-UPL'],
                'triggers_next' => [],
            ];
        }

        // ====== TAHAP 4: TEKNIS & BANGUNAN ======
        if ($needsPBG) {
            $permits[] = [
                'code' => 'PBG',
                'name' => 'Persetujuan Bangunan Gedung (PBG)',
                'priority' => 'high',
                'category' => 'technical',
                'type' => 'mandatory',
                'estimated_timeline' => '14-28 hari kerja',
                'government_fee' => [
                    'min' => 1000000,
                    'max' => 2000000,
                    'note' => 'Retribusi PBG sesuai Perda (tergantung luas & zona)',
                ],
                'consultant_fee' => [
                    'min' => (int) (5000000 * $scaleMultiplier),
                    'max' => (int) (8000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pengajuan PBG via SIMBG dan kelengkapan teknis',
                ],
                'total_cost_range' => CostFormatter::range(6000000 * $scaleMultiplier, 10000000 * $scaleMultiplier),
                'description' => 'Persetujuan dari pemerintah daerah untuk mendirikan atau merenovasi bangunan. Pengganti IMB sesuai UU Cipta Kerja. Diajukan melalui SIMBG.',
                'issuing_authority' => 'Dinas PU / SIMBG',
                'legal_basis' => 'UU 6/2023, PP 16/2021 tentang Bangunan Gedung',
                'prerequisites' => $needsPKKPR
                    ? ['Nomor Induk Berusaha (NIB)', 'PKKPR', $needsAMDAL ? 'AMDAL' : ($needsEnvironmental ? 'UKL-UPL' : 'SPPL')]
                    : ['Nomor Induk Berusaha (NIB)', $needsAMDAL ? 'AMDAL' : ($needsEnvironmental ? 'UKL-UPL' : 'SPPL')],
                'triggers_next' => ['Sertifikat Laik Fungsi (SLF)'],
            ];

            // SLF after PBG
            $permits[] = [
                'code' => 'SLF',
                'name' => 'Sertifikat Laik Fungsi (SLF)',
                'priority' => 'high',
                'category' => 'technical',
                'type' => 'mandatory',
                'estimated_timeline' => '14-30 hari kerja',
                'government_fee' => [
                    'min' => 500000,
                    'max' => 2000000,
                    'note' => 'Biaya pemeriksaan kelaikan fungsi bangunan',
                ],
                'consultant_fee' => [
                    'min' => (int) (3000000 * $scaleMultiplier),
                    'max' => (int) (7000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pengujian kelaikan dan penerbitan SLF',
                ],
                'total_cost_range' => CostFormatter::range(3500000 * $scaleMultiplier, 9000000 * $scaleMultiplier),
                'description' => 'Sertifikat yang menyatakan bangunan gedung telah laik fungsi untuk digunakan. Wajib dimiliki setelah PBG sebelum bangunan dioperasikan.',
                'issuing_authority' => 'Dinas PU / SIMBG',
                'legal_basis' => 'UU 6/2023, PP 16/2021 tentang Bangunan Gedung',
                'prerequisites' => ['Persetujuan Bangunan Gedung (PBG)'],
                'triggers_next' => ['Sertifikat Standar'],
            ];
        }

        // ====== TAHAP 5: OPERASIONAL ======
        $permits[] = [
            'code' => 'SERTIFIKAT_STANDAR',
            'name' => 'Sertifikat Standar / Izin Usaha Berbasis Risiko',
            'priority' => 'high',
            'category' => 'operational',
            'type' => 'mandatory',
            'estimated_timeline' => '7-14 hari kerja',
            'government_fee' => [
                'min' => 0,
                'max' => 500000,
                'note' => 'Biaya verifikasi (jika diperlukan)',
            ],
            'consultant_fee' => [
                'min' => (int) (3000000 * $scaleMultiplier),
                'max' => (int) (7000000 * $scaleMultiplier),
                'note' => 'Penyusunan dokumen dan pendampingan perizinan',
            ],
            'total_cost_range' => CostFormatter::range(3000000 * $scaleMultiplier, 7500000 * $scaleMultiplier),
            'description' => 'Izin operasional sesuai klasifikasi risiko usaha melalui OSS RBA. Untuk risiko menengah-tinggi, memerlukan verifikasi lapangan.',
            'issuing_authority' => 'Pemerintah Daerah / K/L Terkait via OSS',
            'legal_basis' => 'UU 6/2023, PP 5/2021, Perpres 10/2021',
            'prerequisites' => array_filter([
                'Nomor Induk Berusaha (NIB)',
                'NPWP Badan Usaha',
                $needsAMDAL ? 'AMDAL' : ($needsEnvironmental ? 'UKL-UPL' : 'SPPL'),
                $needsPBG ? 'Sertifikat Laik Fungsi (SLF)' : null,
            ]),
            'triggers_next' => [],
        ];

        // Calculate totals
        $govMin = $govMax = $conMin = $conMax = 0;
        $totalMinDays = $totalMaxDays = 0;
        foreach ($permits as $permit) {
            $govMin += $permit['government_fee']['min'];
            $govMax += $permit['government_fee']['max'];
            $conMin += $permit['consultant_fee']['min'];
            $conMax += $permit['consultant_fee']['max'];
            // Parse timeline days
            if (preg_match('/(\d+)\s*[-–]\s*(\d+)/', $permit['estimated_timeline'], $m)) {
                $totalMinDays += (int) $m[1];
                $totalMaxDays += (int) $m[2];
            }
        }

        $complexity = count($permits) <= 3 ? 4.0 : (count($permits) <= 5 ? 6.0 : (count($permits) <= 7 ? 7.5 : 8.5));
        $riskClass = $needsAMDAL ? 'tinggi' : ($needsEnvironmental || $needsB3 ? 'menengah_tinggi' : 'menengah_rendah');
        $riskLevel = match ($riskClass) {
            'rendah' => 'low',
            'menengah_rendah' => 'medium',
            'menengah_tinggi' => 'high',
            'tinggi' => 'high',
            default => 'medium',
        };

        $timelineSummary = $totalMinDays > 0
            ? "{$totalMinDays}-{$totalMaxDays} hari kerja"
            : ($needsAMDAL ? '90-180 hari kerja' : ($needsEnvironmental ? '30-60 hari kerja' : '14-30 hari kerja'));

        // Build critical path from critical/high priority permits
        $criticalPath = [];
        foreach ($permits as $p) {
            if (in_array($p['priority'], ['critical', 'high'])) {
                $criticalPath[] = $p['name'].' ('.$p['estimated_timeline'].')';
            }
        }

        return [
            'recommended_permits' => $permits,
            'risk_classification' => $riskClass,
            'kbli_suggestion' => [
                'code' => null,
                'description' => 'Tidak dapat menentukan KBLI secara otomatis. Konsultasi lebih lanjut diperlukan.',
                'confidence' => 'low',
            ],
            'total_estimated_cost' => [
                'government_fees' => ['min' => $govMin, 'max' => $govMax],
                'consultant_fees' => ['min' => $conMin, 'max' => $conMax],
                'grand_total' => ['min' => $govMin + $conMin, 'max' => $govMax + $conMax],
                'currency' => 'IDR',
            ],
            'total_estimated_timeline' => $timelineSummary,
            'estimated_timeline' => [
                'summary' => $timelineSummary,
                'minimum_days' => $totalMinDays ?: null,
                'maximum_days' => $totalMaxDays ?: null,
                'critical_path' => $criticalPath,
            ],
            'complexity_score' => $complexity,
            'risk_assessment' => [
                'level' => $riskLevel,
                'factors' => [
                    'Analisis ini menggunakan estimasi umum berdasarkan regulasi 2026 - konsultasi langsung diperlukan untuk akurasi',
                    'Persyaratan spesifik dapat bervariasi berdasarkan peraturan daerah (Perda) setempat',
                    'Timeline dapat berubah tergantung kelengkapan dokumen dan antrian instansi',
                ],
                'mitigation' => [
                    'Konsultasikan dengan konsultan perizinan bersertifikat sebelum memulai proses',
                    'Siapkan seluruh dokumen persyaratan secara lengkap sebelum pengajuan',
                    'Pastikan kepatuhan terhadap RTRW/RDTR daerah setempat',
                    'Monitor perubahan regulasi terkait UU Cipta Kerja dan turunannya',
                ],
                'common_pitfalls' => [
                    'Dokumen tidak lengkap saat pengajuan sehingga terjadi penolakan/revisi',
                    'Tidak memperhatikan urutan perolehan izin (dependency chain)',
                    'Menggunakan format izin lama (SIUP/TDP/IMB/HO) yang sudah tidak berlaku',
                    'Tidak mengurus PKKPR sebelum izin lingkungan dan PBG',
                    'Tidak memperbarui NIB setelah ada perubahan data usaha',
                ],
            ],
            'risk_factors' => [
                'Analisis ini menggunakan estimasi umum berdasarkan regulasi 2026 - konsultasi langsung diperlukan untuk akurasi',
                'Persyaratan spesifik dapat bervariasi berdasarkan peraturan daerah (Perda) setempat',
                'Timeline dapat berubah tergantung kelengkapan dokumen dan antrian instansi',
            ],
            'required_documents' => [
                'KTP Pengurus/Pemilik (e-KTP yang masih berlaku)',
                'Akta Pendirian dan perubahannya (jika badan usaha)',
                'SK Kemenkumham / AHU Online (untuk PT/CV)',
                'NPWP Pribadi Pengurus/Pemilik',
                'Bukti kepemilikan/sewa tempat usaha (SHM/SHGB/Sewa)',
                'Denah dan foto lokasi usaha',
                'Surat kuasa (jika diwakilkan)',
                'Peta lokasi dan koordinat GPS',
            ],
            'next_steps' => [
                'Siapkan dokumen legalitas perusahaan (KTP, Akta, SK Kemenkumham)',
                'Tentukan kode KBLI 5 digit yang sesuai dengan konsultan BizMark',
                'Periksa kesesuaian lokasi usaha dengan RTRW/RDTR setempat',
                'Daftar NIB melalui OSS RBA dengan pendampingan konsultan',
                'Urus dokumen lingkungan sesuai klasifikasi risiko usaha',
                'Daftar ke portal BizMark.ID untuk analisis detail dan pendampingan lengkap',
            ],
            'limitations' => 'Ini adalah analisis estimasi otomatis berdasarkan regulasi 2026 (UU 6/2023, PP 5/2021). AI tidak dapat menganalisis saat ini, sehingga hasil berdasarkan template umum. Untuk analisis detail, timeline breakdown, dan pendampingan konsultan bersertifikat, silakan daftar ke portal BizMark.ID.',
            'ai_model_used' => 'fallback-v3',
            'ai_tokens_used' => 0,
            'ai_processing_time' => 0,
            'generated_at' => now()->toIso8601String(),
            'version' => '3.0-fallback-2026',
            'cached' => false,
        ];
    }

    /**
     * Check if business likely needs AMDAL (high-impact environmental assessment)
     * AMDAL required for: large-scale industry, B3/hazardous, mining, major construction, investment >2B
     */
    private function likelyNeedsAMDAL(string $activity, string $investment, string $scale): bool
    {
        // Large investment almost always needs AMDAL
        if ($investment === 'over_2b' && $scale === 'large') {
            return true;
        }

        $amdalKeywords = ['tambang', 'pertambangan', 'mining', 'smelter', 'kilang', 'refinery',
            'limbah b3', 'b3', 'hazardous', 'chemical', 'kimia berat', 'pupuk', 'pestisida',
            'petrokimia', 'pelabuhan', 'bandara', 'tol', 'bendungan', 'pltu', 'pltn',
            'nuklir', 'sawit besar', 'perkebunan besar', 'hutan'];

        foreach ($amdalKeywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if business likely deals with B3 (hazardous materials/waste)
     */
    private function likelyNeedsB3(string $activity): bool
    {
        $b3Keywords = ['limbah', 'b3', 'hazardous', 'toxic', 'beracun', 'berbahaya',
            'chemical', 'kimia', 'pestisida', 'pupuk kimia', 'electroplating',
            'galvanis', 'aki', 'baterai', 'oli bekas', 'smelter', 'pertambangan'];

        foreach ($b3Keywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if business likely needs environmental permits
     */
    private function likelyNeedsEnvironmental(string $activity, string $investment): bool
    {
        $envKeywords = ['pabrik', 'manufaktur', 'produksi', 'industri', 'pertambangan', 'mining',
            'konstruksi', 'pembangunan', 'chemical', 'kimia', 'limbah', 'pengolahan',
            'factory', 'manufacturing', 'tambang', 'sawit', 'kelapa sawit', 'perkebunan'];

        foreach ($envKeywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }

        // Large investments typically need environmental assessment
        return in_array($investment, ['500m_2b', 'over_2b']);
    }

    /**
     * Check if business likely needs PBG (building permit)
     */
    private function likelyNeedsPBG(string $activity, string $locationCategory): bool
    {
        $pbgKeywords = ['restoran', 'cafe', 'kafe', 'hotel', 'penginapan', 'gudang', 'warehouse',
            'pabrik', 'factory', 'toko', 'ruko', 'showroom', 'bengkel', 'workshop',
            'klinik', 'rumah sakit', 'hospital', 'gedung', 'building', 'mall'];

        foreach ($pbgKeywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }

        return in_array($locationCategory, ['industrial', 'commercial']);
    }
}
