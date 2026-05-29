<?php

namespace App\Services;

use App\Models\ChecklistGeneration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChecklistGeneratorService
{
    public function __construct(protected OpenRouterService $ai) {}

    /**
     * Generate checklist dokumen via AI. Hasil di-cache 7 hari.
     * Returns ChecklistGeneration model dengan checklist_data terisi.
     */
    public function generate(
        string $kbliCode,
        string $permitType,
        string $city,
        string $businessScale,
        ?string $requesterEmail,
        ?string $ipAddress
    ): ChecklistGeneration {
        $cacheKey = 'checklist:'.md5("{$kbliCode}:{$permitType}:{$city}:{$businessScale}");

        $checklistData = Cache::remember($cacheKey, now()->addDays(7), function () use ($kbliCode, $permitType, $city, $businessScale) {
            return $this->callAI($kbliCode, $permitType, $city, $businessScale);
        });

        $record = ChecklistGeneration::create([
            'kbli_code' => $kbliCode,
            'permit_type' => $permitType,
            'city' => $city,
            'business_scale' => $businessScale,
            'checklist_data' => $checklistData,
            'requester_email' => $requesterEmail,
            'ip_address' => $ipAddress,
        ]);

        return $record;
    }

    /**
     * Generate PDF dan simpan ke storage, update pdf_path di record.
     */
    public function generatePdf(ChecklistGeneration $record): string
    {
        $filename = 'checklist-'.$record->id.'-'.Str::slug($record->permit_type).'.pdf';
        $relativePath = 'checklists/'.$filename;
        $absolutePath = storage_path('app/public/'.$relativePath);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        $pdf = Pdf::loadView('tools.checklist-generator.pdf', [
            'record' => $record,
        ])->setPaper('a4', 'portrait');

        $pdf->save($absolutePath);

        $record->update(['pdf_path' => $relativePath]);

        return $relativePath;
    }

    // ──────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────

    private function callAI(string $kbliCode, string $permitType, string $city, string $businessScale): array
    {
        $prompt = $this->buildPrompt($kbliCode, $permitType, $city, $businessScale);

        $result = $this->ai->chat(
            messages: [
                ['role' => 'system', 'content' => 'Kamu adalah konsultan perizinan usaha Indonesia berpengalaman. Berikan jawaban HANYA dalam format JSON valid.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            options: [
                'model' => config('services.openrouter.default_model', 'openrouter/free'),
                'temperature' => 0.3,
                'max_tokens' => 2000,
                'response_format' => ['type' => 'json_object'],
            ]
        );

        if (! $result['success']) {
            Log::error('ChecklistGeneratorService: AI failed', ['kbli' => $kbliCode, 'permit' => $permitType]);

            return $this->fallbackChecklist($kbliCode, $permitType);
        }

        $parsed = json_decode($result['content'], true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($parsed['categories'])) {
            Log::warning('ChecklistGeneratorService: invalid JSON from AI', ['raw' => substr($result['content'], 0, 500)]);

            return $this->fallbackChecklist($kbliCode, $permitType);
        }

        return $parsed;
    }

    private function buildPrompt(string $kbliCode, string $permitType, string $city, string $businessScale): string
    {
        return <<<PROMPT
Buatkan checklist dokumen lengkap untuk pengajuan {$permitType} di {$city} untuk:
- Kode KBLI: {$kbliCode}
- Skala usaha: {$businessScale}

Kembalikan JSON dengan struktur TEPAT sebagai berikut:
{
  "summary": "Ringkasan singkat checklist ini",
  "estimated_days": 14,
  "categories": [
    {
      "name": "Nama Kategori",
      "documents": [
        {
          "name": "Nama Dokumen",
          "required": true,
          "notes": "Catatan atau keterangan tambahan",
          "copies": 2
        }
      ]
    }
  ],
  "tips": ["tip 1", "tip 2"]
}

Sertakan dokumen untuk: identitas pemohon, legalitas usaha, lokasi/bangunan, teknis operasional, dan persetujuan lingkungan jika relevan.
PROMPT;
    }

    private function fallbackChecklist(string $kbliCode, string $permitType): array
    {
        return [
            'summary' => "Checklist dasar untuk {$permitType} (KBLI: {$kbliCode}). Verifikasi ke dinas setempat untuk kelengkapan.",
            'estimated_days' => 30,
            'categories' => [
                [
                    'name' => 'Identitas Pemohon',
                    'documents' => [
                        ['name' => 'KTP Penanggung Jawab', 'required' => true, 'notes' => 'Asli dan fotokopi', 'copies' => 2],
                        ['name' => 'NPWP Pribadi', 'required' => true, 'notes' => 'Asli dan fotokopi', 'copies' => 1],
                        ['name' => 'Pas foto 3x4', 'required' => true, 'notes' => 'Latar belakang merah', 'copies' => 4],
                    ],
                ],
                [
                    'name' => 'Legalitas Usaha',
                    'documents' => [
                        ['name' => 'Akta Pendirian Perusahaan', 'required' => true, 'notes' => 'Beserta SK Kemenkumham', 'copies' => 2],
                        ['name' => 'NPWP Badan Usaha', 'required' => true, 'notes' => 'Asli dan fotokopi', 'copies' => 1],
                        ['name' => 'NIB (Nomor Induk Berusaha)', 'required' => true, 'notes' => 'Dari OSS-RBA', 'copies' => 1],
                    ],
                ],
                [
                    'name' => 'Lokasi & Bangunan',
                    'documents' => [
                        ['name' => 'Sertifikat/Bukti Kepemilikan Tanah', 'required' => true, 'notes' => 'Atau perjanjian sewa', 'copies' => 2],
                        ['name' => 'IMB / PBG', 'required' => true, 'notes' => 'Izin Mendirikan Bangunan', 'copies' => 1],
                        ['name' => 'Denah Lokasi dan Bangunan', 'required' => true, 'notes' => 'Skala 1:100 atau 1:200', 'copies' => 2],
                    ],
                ],
            ],
            'tips' => [
                'Pastikan semua fotokopi dokumen dilegalisir.',
                'Daftarkan usaha di OSS-RBA (oss.go.id) terlebih dahulu untuk mendapatkan NIB.',
                'Konsultasikan dengan dinas terkait di '.($kbliCode ? 'kota/kabupaten setempat' : 'kota Anda').' untuk persyaratan khusus.',
            ],
        ];
    }
}
