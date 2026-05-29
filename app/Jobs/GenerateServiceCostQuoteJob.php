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

        $systemPrompt = "Anda adalah konsultan legal bisnis Indonesia senior. Tugas Anda membuat konten penawaran jasa yang profesional, formal, sopan, jelas, dan siap kirim. Jangan gunakan placeholder seperti [Nama], [Perusahaan], atau teks dummy apa pun. Penutup WAJIB menggunakan penanda pengirim 'Tim Konsultan' dan email kontak info@bizmark.id.\n\nOUTPUT WAJIB: kembalikan HANYA JSON object murni sesuai struktur berikut. JANGAN gunakan markdown code fence (```), JANGAN tambahkan teks atau penjelasan apapun sebelum maupun sesudah JSON.\n{\n  \"offer_text\": \"isi penawaran formal untuk customer (plain text)\",\n  \"email_subject\": \"subjek email formal\",\n  \"email_body\": \"isi email formal lengkap (plain text)\",\n  \"email_html_body\": \"isi email HTML formal dengan <p>, <strong>, <em>, <ul>, <li>, <table>, <tr>, <td>\"\n}";

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
            '8. Kembalikan JSON object murni saja, tanpa markdown, tanpa teks tambahan.';

        $aiResponse = $openRouter->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], [
            'model' => config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash'),
            'temperature' => 0.35,
            'max_tokens' => 1800,
            'response_format' => ['type' => 'json_object'],
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

        // Multi-strategy JSON extraction — centralised in OpenRouterService
        $decoded = OpenRouterService::extractJson($content);

        if (! is_array($decoded)) {
            Log::warning('GenerateServiceCostQuoteJob: AI returned non-JSON', [
                'request_number' => $this->requestNumber,
                'content_preview' => substr($content, 0, 300),
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

        $serviceRequest = ServiceCostRequest::where('request_number', $this->requestNumber)->first();
        if ($serviceRequest) {
            // Save a deterministic fallback so the admin can still send the quote email
            $fallback = $this->buildFallbackContent($serviceRequest);
            $serviceRequest->update([
                'ai_quote_status' => 'failed',
                'status' => 'quoted',
                'quoted_price' => $this->validated['quoted_price'],
                'quoted_timeline' => $this->validated['quoted_timeline'] ?? null,
                'quote_details' => $fallback,
                'quoted_at' => now(),
                'reviewed_by' => $this->reviewedBy,
                'reviewed_at' => now(),
            ]);
        }
    }

    /**
     * Build a deterministic (non-AI) fallback quote so the record is never left
     * in a broken state when the AI job permanently fails.
     */
    private function buildFallbackContent(ServiceCostRequest $serviceRequest): array
    {
        $timeline = ($this->validated['quoted_timeline'] ?? '') !== ''
            ? $this->validated['quoted_timeline']
            : 'Sesuai kesepakatan';
        $quotePrice = 'Rp '.number_format((float) $this->validated['quoted_price'], 0, ',', '.');
        $name = $serviceRequest->display_name ?? 'Bapak/Ibu';
        $reqNum = $serviceRequest->request_number;

        $offerText = "Menindaklanjuti permohonan {$reqNum}, bersama ini kami sampaikan penawaran jasa secara resmi sebesar {$quotePrice} dengan estimasi pelaksanaan {$timeline}.\n\n".
            "Penawaran ini disusun berdasarkan informasi kebutuhan yang telah Bapak/Ibu sampaikan. Jika diperlukan penyesuaian ruang lingkup layanan, rincian biaya dan timeline dapat kami revisi melalui konfirmasi lanjutan.\n\n".
            'Kami siap mendampingi proses hingga tahap implementasi. Mohon konfirmasi persetujuan agar tim kami dapat menindaklanjuti tahap berikutnya.';

        $emailSubject = "Penawaran Jasa - {$reqNum} - Bizmark.ID";
        $emailBody = "Yth. {$name},\n\n".
            "Terima kasih atas permohonan yang telah disampaikan kepada Bizmark.ID. Berdasarkan kebutuhan yang Bapak/Ibu ajukan, kami menyampaikan penawaran jasa sebagai berikut:\n\n".
            "- Nomor Permohonan: {$reqNum}\n".
            "- Nilai Penawaran: {$quotePrice}\n".
            "- Estimasi Timeline: {$timeline}\n\n".
            "Apabila Bapak/Ibu berkenan, kami siap melanjutkan ke tahap berikutnya. Silakan membalas email ini untuk konfirmasi atau kebutuhan penyesuaian.\n\n".
            "Hormat kami,\nTim Konsultan\ninfo@bizmark.id";

        $paragraphs = array_filter(explode("\n\n", trim($emailBody)));
        $htmlParagraphs = array_map(
            fn ($p) => '<p style="margin:0 0 12px 0;line-height:1.75;color:#1f2937;font-size:14px;">'.nl2br(e(trim($p))).'</p>',
            $paragraphs
        );
        $summaryTable = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:10px 0 16px 0;border-collapse:collapse;border:1px solid #dbe2ef;">'
            .'<tr><td style="padding:10px 12px;background:#f8fafc;color:#475569;font-size:12px;">Nomor Permohonan</td><td style="padding:10px 12px;background:#fff;color:#0f172a;font-size:12px;font-weight:600;text-align:right;">'.e($reqNum).'</td></tr>'
            .'<tr><td style="padding:10px 12px;background:#f8fafc;color:#475569;font-size:12px;border-top:1px solid #e2e8f0;">Nilai Penawaran</td><td style="padding:10px 12px;background:#fff;color:#0f172a;font-size:12px;font-weight:700;text-align:right;border-top:1px solid #e2e8f0;">'.$quotePrice.'</td></tr>'
            .'<tr><td style="padding:10px 12px;background:#f8fafc;color:#475569;font-size:12px;border-top:1px solid #e2e8f0;">Estimasi Timeline</td><td style="padding:10px 12px;background:#fff;color:#0f172a;font-size:12px;font-weight:600;text-align:right;border-top:1px solid #e2e8f0;">'.e($timeline).'</td></tr>'
            .'</table>';
        $emailHtmlBody = $summaryTable.implode('', $htmlParagraphs);

        return [
            'generated_by_ai' => false,
            'ai_model' => null,
            'generated_at' => now()->toDateTimeString(),
            'fallback_reason' => 'AI returned invalid JSON after retries',
            'offer_text' => $offerText,
            'email_subject' => $emailSubject,
            'email_body' => $emailBody,
            'email_html_body' => $emailHtmlBody,
        ];
    }
}
