<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ServiceCostRequestQuoteMail;
use App\Models\ServiceCostRequest;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServiceCostRequestController extends Controller
{
    public function __construct(private OpenRouterService $openRouterService) {}

    /**
     * Display the service cost request detail
     */
    public function show(string $requestNumber)
    {
        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        // Get status options
        $statusOptions = [
            'pending' => 'Pending',
            'reviewing' => 'Reviewing',
            'quoted' => 'Quoted',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];

        // Parse services requested if it's JSON
        if (is_string($serviceRequest->services_requested)) {
            $serviceRequest->services_requested = json_decode($serviceRequest->services_requested, true) ?? [];
        }

        // Parse documents if it's JSON
        if (is_string($serviceRequest->documents)) {
            $serviceRequest->documents = json_decode($serviceRequest->documents, true) ?? [];
        }

        // Parse quote details for AI-generated content display
        if (is_string($serviceRequest->quote_details)) {
            $serviceRequest->quote_details = json_decode($serviceRequest->quote_details, true) ?? [];
        }

        if (! is_array($serviceRequest->quote_details)) {
            $serviceRequest->quote_details = [];
        }

        return view('admin.service-cost-requests.show', compact('serviceRequest', 'statusOptions'));
    }

    /**
     * Update the service cost request status
     */
    public function updateStatus(Request $request, string $requestNumber)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,quoted,accepted,rejected,cancelled',
        ]);

        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();
        $serviceRequest->update([
            'status' => $validated['status'],
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Status berhasil diupdate');
    }

    /**
     * Add admin note to service cost request
     */
    public function addNote(Request $request, string $requestNumber)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        // Get current notes
        $notes = $serviceRequest->admin_notes ?? '';

        // Format new note with timestamp
        $timestamp = now()->format('d M Y H:i');
        $author = Auth::user()->name ?? 'Admin';
        $newNote = "[{$timestamp} - {$author}]\n{$validated['note']}";

        // Append to notes
        $notes = $notes ? "{$notes}\n\n{$newNote}" : $newNote;

        $serviceRequest->update([
            'admin_notes' => $notes,
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan');
    }

    /**
     * Generate and send quote to customer
     */
    public function generateQuote(Request $request, string $requestNumber)
    {
        $validated = $request->validate([
            'quoted_price' => 'required|numeric|min:0',
            'quoted_timeline' => 'nullable|string|max:255',
            'quote_notes' => 'nullable|string|max:2000',
            'generate_ai_content' => 'nullable|boolean',
        ]);

        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        $shouldGenerateAi = (bool) ($validated['generate_ai_content'] ?? false);
        $quoteContent = $shouldGenerateAi
            ? $this->generateAiQuoteContent($serviceRequest, $validated)
            : null;

        if (empty($quoteContent)) {
            $quoteContent = $this->generateFallbackQuoteContent($serviceRequest, $validated);
        }

        $quoteContent['digital_signature'] = $this->buildDigitalSignature($serviceRequest, (float) $validated['quoted_price']);

        $serviceRequest->update([
            'status' => 'quoted',
            'quoted_price' => $validated['quoted_price'],
            'quoted_timeline' => $validated['quoted_timeline'],
            'quote_details' => $quoteContent,
            'quoted_at' => now(),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Add note about quote generation
        if ($validated['quote_notes']) {
            $timestamp = now()->format('d M Y H:i');
            $author = Auth::user()->name ?? 'Admin';
            $note = "[{$timestamp} - {$author}]\nQuote Generated:\n{$validated['quote_notes']}";
            $notes = $serviceRequest->admin_notes ? "{$serviceRequest->admin_notes}\n\n{$note}" : $note;
            $serviceRequest->update(['admin_notes' => $notes]);
        }

        return back()->with('success', 'Quote berhasil dibuat dan status diupdate');
    }

    /**
     * Regenerate AI content for quoted request.
     */
    public function regenerateQuoteContent(Request $request, string $requestNumber)
    {
        $validated = $request->validate([
            'regen_notes' => 'nullable|string|max:2000',
        ]);

        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        if (! $serviceRequest->quoted_price) {
            return back()->with('error', 'Nilai quote belum tersedia. Silakan isi quote terlebih dahulu.');
        }

        $generationPayload = [
            'quoted_price' => (float) $serviceRequest->quoted_price,
            'quoted_timeline' => $serviceRequest->quoted_timeline,
            'quote_notes' => $validated['regen_notes'] ?? null,
        ];

        $quoteContent = $this->generateAiQuoteContent($serviceRequest, $generationPayload);
        if (empty($quoteContent)) {
            $quoteContent = $this->generateFallbackQuoteContent($serviceRequest, $generationPayload);
        }

        $quoteContent['digital_signature'] = $this->buildDigitalSignature($serviceRequest, (float) $serviceRequest->quoted_price);

        $serviceRequest->update([
            'quote_details' => $quoteContent,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $timestamp = now()->format('d M Y H:i');
        $author = Auth::user()->name ?? 'Admin';
        $note = "[{$timestamp} - {$author}]\nKonten quote berhasil diregenerate";
        $notes = $serviceRequest->admin_notes ? "{$serviceRequest->admin_notes}\n\n{$note}" : $note;
        $serviceRequest->update(['admin_notes' => $notes]);

        return back()->with('success', 'Konten quote berhasil diregenerate.');
    }

    /**
     * Mark service cost request as completed
     */
    public function complete(Request $request, string $requestNumber)
    {
        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        $serviceRequest->update([
            'status' => 'accepted',
            'completed_at' => now(),
            'completed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Permohonan ditandai sebagai selesai');
    }

    /**
     * Archive service cost request
     */
    public function archive(Request $request, string $requestNumber)
    {
        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        $serviceRequest->update([
            'archived_at' => now(),
        ]);

        return back()->with('success', 'Permohonan berhasil diarsipkan');
    }

    /**
     * Send generated quote email to requester via app mail system.
     */
    public function sendQuoteEmail(Request $request, string $requestNumber)
    {
        $validated = $request->validate([
            'email_subject' => 'nullable|string|max:255',
            'email_body' => 'nullable|string|max:20000',
        ]);

        $serviceRequest = ServiceCostRequest::where('request_number', $requestNumber)->firstOrFail();

        if (! $serviceRequest->quoted_at || ! $serviceRequest->quoted_price) {
            return back()->with('error', 'Quote belum tersedia. Silakan generate quote terlebih dahulu.');
        }

        $quoteDetails = is_array($serviceRequest->quote_details) ? $serviceRequest->quote_details : [];
        if (empty($quoteDetails)) {
            $quoteDetails = $this->generateFallbackQuoteContent($serviceRequest, [
                'quoted_price' => $serviceRequest->quoted_price,
                'quoted_timeline' => $serviceRequest->quoted_timeline,
                'quote_notes' => null,
            ]);

            $serviceRequest->update(['quote_details' => $quoteDetails]);
        }

        $subject = trim((string) ($validated['email_subject'] ?? ($quoteDetails['email_subject'] ?? '')));
        if ($subject === '') {
            $subject = 'Penawaran Jasa - '.$serviceRequest->request_number.' - Bizmark.ID';
        }
        $subject = $this->sanitizeQuoteText($subject, $serviceRequest, false);

        $body = trim((string) ($validated['email_body'] ?? ($quoteDetails['email_body'] ?? '')));
        if ($body === '') {
            $fallback = $this->generateFallbackQuoteContent($serviceRequest, [
                'quoted_price' => $serviceRequest->quoted_price,
                'quoted_timeline' => $serviceRequest->quoted_timeline,
                'quote_notes' => null,
            ]);
            $body = $fallback['email_body'];
            $quoteDetails = array_merge($quoteDetails, $fallback);
            $serviceRequest->update(['quote_details' => $quoteDetails]);
        }
        $body = $this->sanitizeQuoteText($body, $serviceRequest, true);

        $htmlBody = $this->composeHtmlBody(
            $serviceRequest,
            $body,
            (string) ($quoteDetails['email_html_body'] ?? '')
        );

        $digitalSignature = is_array($quoteDetails['digital_signature'] ?? null)
            ? $quoteDetails['digital_signature']
            : $this->buildDigitalSignature($serviceRequest, (float) $serviceRequest->quoted_price);

        // Persist any admin-adjusted email content for consistency
        if (($quoteDetails['email_subject'] ?? '') !== $subject
            || ($quoteDetails['email_body'] ?? '') !== $body
            || ($quoteDetails['email_html_body'] ?? '') !== $htmlBody
            || ($quoteDetails['digital_signature']['signature_id'] ?? '') !== ($digitalSignature['signature_id'] ?? '')) {
            $quoteDetails['email_subject'] = $subject;
            $quoteDetails['email_body'] = $body;
            $quoteDetails['email_html_body'] = $htmlBody;
            $quoteDetails['digital_signature'] = $digitalSignature;
            $serviceRequest->update(['quote_details' => $quoteDetails]);
        }

        try {
            Mail::to($serviceRequest->email)
                ->send(new ServiceCostRequestQuoteMail(
                    serviceRequest: $serviceRequest,
                    subjectLine: $subject,
                    bodyText: $body,
                    htmlBody: $htmlBody,
                    signature: $digitalSignature
                ));

            $timestamp = now()->format('d M Y H:i');
            $author = Auth::user()->name ?? 'Admin';
            $note = "[{$timestamp} - {$author}]\nEmail penawaran telah dikirim ke {$serviceRequest->email} melalui info@bizmark.id";
            $notes = $serviceRequest->admin_notes ? "{$serviceRequest->admin_notes}\n\n{$note}" : $note;

            $serviceRequest->update([
                'responded_at' => now(),
                'admin_notes' => $notes,
            ]);

            return back()->with('success', 'Email penawaran berhasil dikirim melalui info@bizmark.id');
        } catch (\Throwable $e) {
            Log::error('Failed sending service cost request quote email', [
                'request_number' => $serviceRequest->request_number,
                'email' => $serviceRequest->email,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal mengirim email penawaran. Silakan cek konfigurasi mail server.');
        }
    }

    /**
     * Generate professional quote and email template using AI.
     */
    private function generateAiQuoteContent(ServiceCostRequest $serviceRequest, array $validated): ?array
    {
        try {
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
                '- Estimasi Biaya Penawaran: Rp '.number_format((float) $validated['quoted_price'], 0, ',', '.')."\n".
                '- Timeline Penawaran: '.(($validated['quoted_timeline'] ?? '') !== '' ? $validated['quoted_timeline'] : '-')."\n".
                '- Catatan Admin Tambahan: '.(($validated['quote_notes'] ?? '') !== '' ? $validated['quote_notes'] : '-')."\n\n".
                "Aturan:\n".
                "1. Gunakan bahasa Indonesia formal dan profesional.\n".
                "2. Penawaran harus mencakup ringkasan kebutuhan, nilai penawaran, timeline, dan ajakan tindak lanjut.\n".
                "3. Email body harus siap kirim manual oleh admin (lengkap salam pembuka, isi, penutup, dan kontak lanjutan).\n".
                "4. Jangan mengarang data di luar input.\n".
                "5. Jangan gunakan placeholder.\n".
                "6. Penutup wajib: Tim Konsultan (info@bizmark.id).\n".
                "7. Field email_html_body wajib berisi HTML bersih dengan kombinasi bold/italic dan tabel ringkas penawaran.\n".
                '8. Kembalikan JSON valid saja sesuai format.';

            $aiResponse = $this->openRouterService->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ], [
                'model' => config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash'),
                'temperature' => 0.35,
                'max_tokens' => 1800,
            ]);

            if (! ($aiResponse['success'] ?? false)) {
                Log::warning('AI quote generation failed at request level', [
                    'request_number' => $serviceRequest->request_number,
                    'error' => $aiResponse['error'] ?? 'unknown',
                ]);

                return null;
            }

            $content = trim((string) ($aiResponse['content'] ?? ''));
            $content = preg_replace('/^```json\s*/i', '', $content);
            $content = preg_replace('/```$/', '', $content);
            $decoded = json_decode(trim($content), true);

            if (! is_array($decoded) && preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                $decoded = json_decode($matches[0], true);
            }

            if (! is_array($decoded)) {
                return null;
            }

            $offerText = trim((string) ($decoded['offer_text'] ?? ''));
            $emailSubject = trim((string) ($decoded['email_subject'] ?? ''));
            $emailBody = trim((string) ($decoded['email_body'] ?? ''));
            $emailHtmlBody = trim((string) ($decoded['email_html_body'] ?? ''));

            $offerText = $this->sanitizeQuoteText($offerText, $serviceRequest, false);
            $emailSubject = $this->sanitizeQuoteText($emailSubject, $serviceRequest, false);
            $emailBody = $this->sanitizeQuoteText($emailBody, $serviceRequest, true);
            $emailHtmlBody = $this->composeHtmlBody($serviceRequest, $emailBody, $emailHtmlBody);

            if ($offerText === '' || $emailSubject === '' || $emailBody === '' || $emailHtmlBody === '') {
                return null;
            }

            return [
                'generated_by_ai' => true,
                'ai_model' => $aiResponse['model'] ?? null,
                'generated_at' => now()->toDateTimeString(),
                'offer_text' => $offerText,
                'email_subject' => $emailSubject,
                'email_body' => $emailBody,
                'email_html_body' => $emailHtmlBody,
            ];
        } catch (\Throwable $e) {
            Log::error('AI quote generation exception', [
                'request_number' => $serviceRequest->request_number,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Deterministic fallback quote and email template when AI is unavailable.
     */
    private function generateFallbackQuoteContent(ServiceCostRequest $serviceRequest, array $validated): array
    {
        $timeline = ($validated['quoted_timeline'] ?? '') !== '' ? $validated['quoted_timeline'] : 'Sesuai kesepakatan';
        $quotePrice = 'Rp '.number_format((float) $validated['quoted_price'], 0, ',', '.');
        $quoteNotes = trim((string) ($validated['quote_notes'] ?? ''));

        $offerText = "Menindaklanjuti permohonan {$serviceRequest->request_number}, bersama ini kami sampaikan penawaran jasa secara resmi sebesar {$quotePrice} dengan estimasi pelaksanaan {$timeline}.\n\n".
            "Penawaran ini disusun berdasarkan informasi kebutuhan yang telah Bapak/Ibu sampaikan. Jika diperlukan penyesuaian ruang lingkup layanan, rincian biaya dan timeline dapat kami revisi melalui konfirmasi lanjutan.\n\n".
            'Kami siap mendampingi proses hingga tahap implementasi. Mohon konfirmasi persetujuan agar tim kami dapat menindaklanjuti tahap berikutnya.';

        if ($quoteNotes !== '') {
            $offerText .= "\n\nCatatan tambahan: {$quoteNotes}";
        }

        $emailSubject = "Penawaran Jasa - {$serviceRequest->request_number} - Bizmark.ID";
        $emailBody = "Yth. Bapak/Ibu {$serviceRequest->display_name},\n\n".
            "Terima kasih atas permohonan yang telah disampaikan kepada Bizmark.ID. Berdasarkan kebutuhan yang Bapak/Ibu ajukan, kami menyampaikan penawaran jasa sebagai berikut:\n\n".
            "- Nomor Permohonan: {$serviceRequest->request_number}\n".
            "- Nilai Penawaran: {$quotePrice}\n".
            "- Estimasi Timeline: {$timeline}\n\n".
            ($quoteNotes !== '' ? "Catatan: {$quoteNotes}\n\n" : '').
            "Apabila Bapak/Ibu berkenan, kami siap melanjutkan ke tahap berikutnya. Silakan membalas email ini untuk konfirmasi atau kebutuhan penyesuaian.\n\n".
            "Hormat kami,\nTim Konsultan\ninfo@bizmark.id";

        $offerText = $this->sanitizeQuoteText($offerText, $serviceRequest, false);
        $emailSubject = $this->sanitizeQuoteText($emailSubject, $serviceRequest, false);
        $emailBody = $this->sanitizeQuoteText($emailBody, $serviceRequest, true);
        $emailHtmlBody = $this->composeHtmlBody($serviceRequest, $emailBody, '');

        return [
            'generated_by_ai' => false,
            'ai_model' => null,
            'generated_at' => now()->toDateTimeString(),
            'offer_text' => $offerText,
            'email_subject' => $emailSubject,
            'email_body' => $emailBody,
            'email_html_body' => $emailHtmlBody,
        ];
    }

    /**
     * Clean placeholder artifacts and ensure formal signature.
     */
    private function sanitizeQuoteText(string $text, ServiceCostRequest $serviceRequest, bool $ensureSignature): string
    {
        $cleaned = str_replace(["\r\n", "\r"], "\n", trim($text));

        $replacements = [
            '[Nama Perusahaan/Konsultan Anda]' => 'Tim Konsultan',
            '[Nama Perusahaan Anda]' => 'Tim Konsultan',
            '[Jabatan Anda]' => 'Tim Konsultan',
            '[Nomor Telepon Anda]' => config('landing_metrics.contact.phone', '+62 838 7960 2855'),
            '[Alamat Email Anda]' => 'info@bizmark.id',
            '[Nama Anda]' => 'Tim Konsultan',
            '[Nama Penerima]' => $serviceRequest->display_name,
        ];

        $cleaned = str_ireplace(array_keys($replacements), array_values($replacements), $cleaned);
        $cleaned = preg_replace('/\[[^\]]+\]/', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\n{3,}/', "\n\n", $cleaned) ?? $cleaned;
        $cleaned = trim($cleaned);

        if ($ensureSignature) {
            if (stripos($cleaned, 'tim konsultan') === false) {
                $cleaned .= "\n\nHormat kami,\nTim Konsultan\ninfo@bizmark.id";
            } elseif (stripos($cleaned, 'info@bizmark.id') === false) {
                $cleaned .= "\ninfo@bizmark.id";
            }
        }

        return trim($cleaned);
    }

    /**
     * Build safe HTML body with rich typography and table summary.
     */
    private function composeHtmlBody(ServiceCostRequest $serviceRequest, string $plainBody, string $preferredHtml): string
    {
        $safePreferred = $this->sanitizeQuoteHtml($preferredHtml);
        if ($safePreferred !== '') {
            return $safePreferred;
        }

        $paragraphs = preg_split('/\n\n+/', trim($plainBody)) ?: [];
        $htmlParagraphs = [];
        foreach ($paragraphs as $paragraph) {
            $line = nl2br(e(trim($paragraph)));
            if ($line !== '') {
                $line = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $line) ?? $line;
                $line = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $line) ?? $line;
                $htmlParagraphs[] = '<p style="margin:0 0 12px 0;line-height:1.75;color:#1f2937;font-size:14px;">'.$line.'</p>';
            }
        }

        $timeline = $serviceRequest->quoted_timeline ?: 'Sesuai kesepakatan';
        $summaryTable = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:10px 0 16px 0;border-collapse:collapse;border:1px solid #dbe2ef;border-radius:10px;overflow:hidden;">'
            .'<tr><td style="padding:10px 12px;background:#f8fafc;color:#475569;font-size:12px;">Nomor Permohonan</td><td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:12px;font-weight:600;text-align:right;">'.e($serviceRequest->request_number).'</td></tr>'
            .'<tr><td style="padding:10px 12px;background:#f8fafc;color:#475569;font-size:12px;border-top:1px solid #e2e8f0;">Nilai Penawaran</td><td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:12px;font-weight:700;text-align:right;border-top:1px solid #e2e8f0;">Rp '.number_format((float) $serviceRequest->quoted_price, 0, ',', '.').'</td></tr>'
            .'<tr><td style="padding:10px 12px;background:#f8fafc;color:#475569;font-size:12px;border-top:1px solid #e2e8f0;">Estimasi Timeline</td><td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:12px;font-weight:600;text-align:right;border-top:1px solid #e2e8f0;">'.e($timeline).'</td></tr>'
            .'</table>';

        return $summaryTable.implode('', $htmlParagraphs);
    }

    /**
     * Allow only safe typography tags for email HTML body.
     */
    private function sanitizeQuoteHtml(string $html): string
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        $allowed = '<p><strong><em><ul><ol><li><br><table><thead><tbody><tr><td><th><a><span>';
        $cleaned = strip_tags($trimmed, $allowed);
        $cleaned = preg_replace('/\son[a-z]+="[^"]*"/i', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/javascript:/i', '', $cleaned) ?? $cleaned;

        return trim($cleaned);
    }

    /**
     * Create a digital signature payload for outgoing quote email.
     */
    private function buildDigitalSignature(ServiceCostRequest $serviceRequest, float $quotedPrice): array
    {
        $issuedAt = now();
        $signatureId = 'SIG-'.$serviceRequest->request_number.'-'.$issuedAt->format('YmdHis');
        $hash = strtoupper(substr(hash('sha256', $serviceRequest->request_number.'|'.$quotedPrice.'|'.$issuedAt->toIso8601String().'|'.config('app.key')), 0, 20));

        return [
            'signer_name' => 'Tim Konsultan',
            'signer_title' => 'Business Licensing Consultant',
            'signer_email' => 'info@bizmark.id',
            'issued_at' => $issuedAt->format('d M Y H:i'),
            'signature_id' => $signatureId,
            'signature_hash' => $hash,
        ];
    }
}
