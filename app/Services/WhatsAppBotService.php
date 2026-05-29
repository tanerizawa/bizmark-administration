<?php

namespace App\Services;

use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WhatsAppBotService
{
    private const INTENT_HANDLERS = [
        'cek_kbli' => 'handleKbliLookup',
        'estimasi_biaya' => 'handleCostEstimate',
        'info_layanan' => 'handleServiceInfo',
        'status_proyek' => 'handleProjectStatus',
        'hubungi_admin' => 'handleHumanHandoff',
        'salam' => 'handleGreeting',
        'unknown' => 'handleUnknown',
    ];

    public function __construct(
        private readonly OpenRouterService $openRouter,
        private readonly WhatsAppApiService $waApi
    ) {}

    /**
     * Proses pesan masuk: deteksi intent → kirim balasan → log.
     */
    public function processMessage(WhatsAppConversation $conv, string $message, ?string $waMessageId = null): void
    {
        // Jika sudah handoff, admin yang handle — bot tidak ikut campur
        if ($conv->isHandedOff()) {
            return;
        }

        // Log inbound
        $conv->messages()->create([
            'direction' => 'inbound',
            'content' => $message,
            'wa_message_id' => $waMessageId,
        ]);

        // Deteksi intent
        $intent = $this->detectIntent($message, $conv->context ?? []);
        $handler = self::INTENT_HANDLERS[$intent['name'] ?? 'unknown'] ?? 'handleUnknown';
        $reply = $this->$handler($intent, $message, $conv);

        // Log outbound
        $conv->messages()->create([
            'direction' => 'outbound',
            'content' => $reply,
        ]);

        // Update context + last_message_at
        $conv->update([
            'context' => array_merge($conv->context ?? [], $intent['extracted_data'] ?? []),
            'last_message_at' => now(),
        ]);

        // Kirim balasan
        $this->waApi->sendText($conv->wa_phone, $reply);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Intent Handlers
    // ────────────────────────────────────────────────────────────────────────

    private function handleGreeting(array $intent, string $message, WhatsAppConversation $conv): string
    {
        $name = $conv->wa_name ? ", {$conv->wa_name}" : '';

        return "Halo{$name}! 👋 Selamat datang di *Bizmark — Konsultan Perizinan Usaha*.\n\n"
            ."Saya bisa membantu Anda dengan:\n"
            ."1️⃣ Cek KBLI / jenis usaha\n"
            ."2️⃣ Estimasi biaya perizinan\n"
            ."3️⃣ Info layanan Bizmark\n"
            ."4️⃣ Status proyek perizinan Anda\n"
            ."5️⃣ Bicara dengan konsultan kami\n\n"
            .'Ketik angka atau sampaikan kebutuhan Anda 😊';
    }

    private function handleKbliLookup(array $intent, string $message, WhatsAppConversation $conv): string
    {
        $keyword = $intent['extracted_data']['kbli_keyword'] ?? $message;

        return "🔍 *Pencarian KBLI: \"{$keyword}\"*\n\n"
            ."Untuk pencarian KBLI lengkap, kunjungi:\n"
            .'🌐 '.config('app.url').'/kbli?q='.urlencode($keyword)."\n\n"
            .'Atau sampaikan deskripsi usaha Anda secara lengkap, dan kami akan bantu identifikasi KBLI yang tepat. '
            .'Untuk konsultasi mendalam, ketik *hubungi konsultan*.';
    }

    private function handleCostEstimate(array $intent, string $message, WhatsAppConversation $conv): string
    {
        return "💰 *Estimasi Biaya Perizinan*\n\n"
            ."Biaya perizinan sangat bergantung pada:\n"
            ."• Jenis usaha & KBLI\n"
            ."• Skala usaha (mikro/kecil/menengah/besar)\n"
            ."• Lokasi usaha\n"
            ."• Jenis izin yang dibutuhkan (NIB, SIUP, IMB, dll)\n\n"
            ."Untuk estimasi akurat, hubungi konsultan kami:\n"
            .'📞 '.config('services.whatsapp.admin_phone', config('app.phone_number', ''))."\n"
            .'Atau ketik *hubungi konsultan* untuk disambungkan langsung.';
    }

    private function handleServiceInfo(array $intent, string $message, WhatsAppConversation $conv): string
    {
        return "📋 *Layanan Bizmark*\n\n"
            ."✅ Pengurusan NIB (Nomor Induk Berusaha)\n"
            ."✅ Izin Usaha OSS-RBA\n"
            ."✅ IMB / PBG\n"
            ."✅ SIUP & TDP\n"
            ."✅ Izin Lingkungan (AMDAL, UKL-UPL)\n"
            ."✅ Sertifikasi Halal\n"
            ."✅ Izin edar BPOM\n"
            ."✅ Perizinan sektor khusus\n\n"
            .'🌐 Info lengkap: '.config('app.url')."\n\n"
            .'Ingin konsultasi gratis? Ketik *hubungi konsultan*.';
    }

    private function handleProjectStatus(array $intent, string $message, WhatsAppConversation $conv): string
    {
        return "📂 *Status Proyek*\n\n"
            ."Untuk melihat status proyek perizinan Anda secara real-time, login ke portal klien:\n"
            .'🔗 '.config('app.url')."/client\n\n"
            ."Belum punya akun? Tim kami akan buatkan saat Anda onboarding.\n"
            .'Ada pertanyaan spesifik? Ketik *hubungi konsultan*.';
    }

    private function handleHumanHandoff(array $intent, string $message, WhatsAppConversation $conv): string
    {
        $conv->update(['status' => 'handoff']);

        // Notifikasi admin via email
        try {
            Mail::raw(
                "WhatsApp Handoff Request\n\n"
                .'Nama: '.($conv->wa_name ?? '-')."\n"
                ."No HP: {$conv->wa_phone}\n"
                ."Pesan terakhir: {$message}\n"
                .'Waktu: '.now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB',
                function ($mail) use ($conv) {
                    $mail->to(config('mail.admin_address', config('mail.from.address')))
                        ->subject('[WA Bot] Handoff Request — '.($conv->wa_name ?? $conv->wa_phone));
                }
            );
        } catch (\Throwable $e) {
            Log::warning('[WhatsApp] Handoff email failed: '.$e->getMessage());
        }

        return "✅ Permintaan Anda telah diteruskan ke konsultan kami.\n\n"
            ."Tim Bizmark akan membalas dalam *30 menit kerja* (Senin–Jumat, 08.00–17.00 WIB).\n\n"
            ."Untuk keperluan mendesak, hubungi langsung:\n"
            .'📞 '.config('services.whatsapp.admin_phone', '')."\n"
            .'🌐 '.config('app.url');
    }

    private function handleUnknown(array $intent, string $message, WhatsAppConversation $conv): string
    {
        return "Maaf, saya belum memahami pertanyaan tersebut. 🙏\n\n"
            ."Anda bisa:\n"
            ."• Ketik *cek KBLI* untuk pencarian jenis usaha\n"
            ."• Ketik *estimasi biaya* untuk info biaya perizinan\n"
            ."• Ketik *layanan* untuk daftar layanan kami\n"
            .'• Ketik *hubungi konsultan* untuk disambungkan ke tim kami';
    }

    // ────────────────────────────────────────────────────────────────────────
    // AI Intent Detection
    // ────────────────────────────────────────────────────────────────────────

    private function detectIntent(string $message, array $context): array
    {
        $systemPrompt = <<<'PROMPT'
Anda adalah asisten AI Bizmark, perusahaan konsultan perizinan usaha di Indonesia.
Deteksi intent dari pesan pengguna dan ekstrak data relevan.

Intents yang tersedia:
- cek_kbli: Pengguna ingin cek kode KBLI atau jenis usaha
- estimasi_biaya: Pengguna ingin tahu estimasi biaya perizinan
- info_layanan: Pengguna bertanya tentang layanan Bizmark
- status_proyek: Pengguna ingin tahu status proyek/izin mereka
- hubungi_admin: Pengguna ingin bicara dengan manusia atau ada pertanyaan kompleks
- salam: Salam biasa (halo, selamat pagi, dsb)
- unknown: Tidak teridentifikasi

Balas HANYA dalam JSON (tanpa teks lain):
{"name": "intent_name", "confidence": 0.95, "extracted_data": {"kbli_keyword": "..."}}
PROMPT;

        try {
            $result = $this->openRouter->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => 'Context: '.json_encode($context)."\nMessage: ".$message],
            ], ['max_tokens' => 200]);

            if ($result['success'] && ! empty($result['content'])) {
                $decoded = json_decode(trim($result['content']), true);
                if (is_array($decoded) && isset($decoded['name'])) {
                    return $decoded;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[WhatsApp] Intent detection failed: '.$e->getMessage());
        }

        // Fallback: keyword matching
        return $this->keywordFallback($message);
    }

    private function keywordFallback(string $message): array
    {
        $lower = mb_strtolower($message);

        if (preg_match('/kbli|jenis usaha|kode usaha/', $lower)) {
            return ['name' => 'cek_kbli', 'confidence' => 0.7, 'extracted_data' => []];
        }
        if (preg_match('/harga|biaya|tarif|cost|estimasi/', $lower)) {
            return ['name' => 'estimasi_biaya', 'confidence' => 0.7, 'extracted_data' => []];
        }
        if (preg_match('/layanan|service|jasa|apa saja/', $lower)) {
            return ['name' => 'info_layanan', 'confidence' => 0.7, 'extracted_data' => []];
        }
        if (preg_match('/status|proyek|izin saya|progress/', $lower)) {
            return ['name' => 'status_proyek', 'confidence' => 0.7, 'extracted_data' => []];
        }
        if (preg_match('/konsultan|admin|manusia|orang|hubungi|telepon|cs/', $lower)) {
            return ['name' => 'hubungi_admin', 'confidence' => 0.7, 'extracted_data' => []];
        }
        if (preg_match('/halo|hai|hello|selamat|pagi|siang|sore|malam/', $lower)) {
            return ['name' => 'salam', 'confidence' => 0.8, 'extracted_data' => []];
        }

        return ['name' => 'unknown', 'confidence' => 0.5, 'extracted_data' => []];
    }
}
