<?php

namespace App\Jobs;

use App\Models\WhatsAppConversation;
use App\Services\WhatsAppBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(private readonly array $payload) {}

    public function handle(WhatsAppBotService $botService, \App\Services\WhatsAppApiService $waApi): void
    {
        $inbound = $waApi->extractInboundMessage($this->payload);
        if (! $inbound) {
            return;
        }

        ['phone' => $phone, 'name' => $name, 'message' => $message, 'message_id' => $messageId] = $inbound;

        if (empty(trim($message))) {
            return;
        }

        // Find or create conversation
        $conv = WhatsAppConversation::firstOrCreate(
            ['wa_phone' => $phone],
            ['wa_name' => $name, 'status' => 'bot', 'last_message_at' => now()]
        );

        // Update name if first time
        if ($name && ! $conv->wa_name) {
            $conv->update(['wa_name' => $name]);
        }

        Log::info('[WhatsApp] Processing message', [
            'phone' => $phone,
            'conv_id' => $conv->id,
            'status' => $conv->status,
        ]);

        $botService->processMessage($conv, $message, $messageId);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('[WhatsApp] Job failed: '.$e->getMessage(), [
            'payload' => $this->payload,
        ]);
    }
}
