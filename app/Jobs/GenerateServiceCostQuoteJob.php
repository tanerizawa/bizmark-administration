<?php

namespace App\Jobs;

use App\Models\ServiceCostRequest;
use App\Services\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateServiceCostQuoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public array $backoff = [30, 90, 300];

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        protected string $requestNumber,
        protected array $validated,
        protected int $reviewedBy
    ) {}

    public function handle(OpenRouterService $openRouter): void
    {
        $serviceRequest = ServiceCostRequest::where('request_number', $this->requestNumber)->first();

        if (! $serviceRequest) {
            Log::warning('GenerateServiceCostQuoteJob: request not found', [
                'request_number' => $this->requestNumber,
            ]);

            return;
        }

        $serviceCategories = ServiceCostRequest::getServiceCategories();
        $servicesByCategory = ServiceCostRequest::getServicesByCategory();
        $categoryKey = $serviceRequest->service_category;
        $categoryLabel = $serviceCategories[$categoryKey] ?? $categoryKey;
        $requestedServices = is_array($serviceRequest->services_requested) ? $serviceRequest->services_requested : [];

        $serviceLabels = [];
        foreach ($requestedServices as $serviceKey) {
            $serviceLabels[] = $servicesByCategory[$categoryKey][$serviceKey] ?? $serviceKey;
        }

        $systemPrompt = "Anda adalah konsultan legal bisnis Indonesia senior. Tugas Anda membuat konten penawaran jasa yang profesional, formal, sopan, jelas, dan siap kirim. Jangan gunakan placeholder seperti [Nama], [Perusahaan], atau teks dummy apa pun. Penutup WAJIB menggunakan penanda pengirim 'Tim Konsultan' dan email kontak info@bizmark.id.\n\nOutput WAJIB JSON valid dengan struktur:\n{\n  \"offer_text\": \"isi penawaran formal untuk customer (plain text)\",\n  \"email_subject\": \"subjek email formal\",\n  \"email_body\": \"isi email formal lengkap (plain text)\",\n  \"email_html_body\": \"isi email HTML formal dengan <p>, <strong>, <em>, <ul>, <li>, <table>, <tr>, <td>\"\n}";

        $userPrompt = "Buatkan konten penawaran resmi untuk data berikut:\n".
            "- Nomor Permohonan: {$serviceRequest->request_number}\n".
            "- Nama Pemohon: {$serviceRequest->display_name}\n".
            "- Email Pemohon: {$serviceRequest->email}\n".
            '- Tipe Pemohon: '.($serviceRequest->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan')."\n".
            "- Kategori Layanan: {$categoryLabel}\n".
            '- Layanan yang Diminta: '.(empty($serviceLabels) ? '-' : implode('; ', $serviceLabels))."\n".
            '- Deskripsi Kebutuhan: '.($serviceRequest->project_description ?: '-')."\n".
            '- Lokasi Proyek: '.($serviceRequest->project_location ?: '-')."\n".
            '- Estimasi Biaya Penawaran: Rp '.number_format((float) $this->validated['quoted_price'], 0, ',', '.')."\n".
            '- Timeline Penawaran: '.(($this->validated['quoted_timeline'] ?? '') !== '' ? $this->validated['quoted_timeline'] : '-')."\n".
            '- Catatan Admin Tambahan: '.(($this->validated['quote_notes'] ?? '') !== '' ? $this->validated['quote_notes'] : '-')."\n\n".
            "Aturan:\n".
            "1. Gunakan bahasa Indonesia formal dan profesional.\n".
            "2. Penawaran harus mencakup ringkasan kebutuhan, nilai penawaran, timeline, dan ajakan tindak lanjut.\n".
            "3. Email body harus siap kirim manual oleh admin.\n".
            "4. Jangan mengarang data di luar input.\n".
            "5. Jangan gunakan placeholder.\n".
            "6. Penutup wajib: Tim Konsultan (info@bizmark.id).\n".
            "7. Field email_html_body wajib berisi HTML bersih.\n".
            '8. Kembalikan JSON valid saja sesuai format.';

        $aiResponse = $openRouter->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], [
            'model' => config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash'),
            'temperature' => 0.35,
            'max_tokens' => 1800,
        ]);

        if (! ($aiResponse['success'] ?? false)) {
            Log::warning('GenerateServiceCostQuoteJob: AI generation failed', [
                'request_number' => $this->requestNumber,
                'error' => $aiResponse['error'] ?? 'unknown',
            ]);
            $this->fail(new \RuntimeException('AI response failed: '.($aiResponse['error'] ?? 'unknown')));

            return;
        }

        $content = trim((string) ($aiResponse['content'] ?? ''));
        $content = preg_replace('/^```json\s*/i', '', $content);
        $content = preg_replace('/```$/', '', $content);
        $decoded = json_decode(trim($content), true);

        if (! is_array($decoded) && preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
        }

        if (! is_array($decoded)) {
            Log::warning('GenerateServiceCostQuoteJob: AI returned non-JSON', [
                'request_number' => $this->requestNumber,
            ]);
            $this->fail(new \RuntimeException('AI returned invalid JSON'));

            return;
        }

        $quoteContent = [
            'generated_by_ai' => true,
            'ai_model' => $aiResponse['model'] ?? null,
            'generated_at' => now()->toDateTimeString(),
            'offer_text' => trim((string) ($decoded['offer_text'] ?? '')),
            'email_subject' => trim((string) ($decoded['email_subject'] ?? '')),
            'email_body' => trim((string) ($decoded['email_body'] ?? '')),
            'email_html_body' => trim((string) ($decoded['email_html_body'] ?? '')),
        ];

        $serviceRequest->update([
            'status' => 'quoted',
            'quoted_price' => $this->validated['quoted_price'],
            'quoted_timeline' => $this->validated['quoted_timeline'] ?? null,
            'quote_details' => $quoteContent,
            'quoted_at' => now(),
            'reviewed_by' => $this->reviewedBy,
            'reviewed_at' => now(),
            'ai_quote_status' => 'completed',
        ]);

        Log::info('GenerateServiceCostQuoteJob: completed', [
            'request_number' => $this->requestNumber,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateServiceCostQuoteJob: permanently failed', [
            'request_number' => $this->requestNumber,
            'error' => $exception->getMessage(),
        ]);

        ServiceCostRequest::where('request_number', $this->requestNumber)
            ->update(['ai_quote_status' => 'failed']);
    }
}
