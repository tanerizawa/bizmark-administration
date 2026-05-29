<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppMessageJob;
use App\Services\WhatsAppApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private readonly WhatsAppApiService $waApi) {}

    /**
     * GET — Meta webhook verification challenge.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        Log::warning('[WhatsApp] Webhook verification failed', ['token' => $token]);

        return response('Forbidden', 403);
    }

    /**
     * POST — incoming messages from Meta.
     */
    public function handle(Request $request): Response
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('X-Hub-Signature-256');

        if (! $this->waApi->validateSignature($rawPayload, $signature)) {
            Log::warning('[WhatsApp] Invalid webhook signature');

            return response('Unauthorized', 401);
        }

        $payload = json_decode($rawPayload, true);

        // Only process whatsapp messages (not status updates, etc.)
        $object = $payload['object'] ?? '';
        if ($object !== 'whatsapp_business_account') {
            return response('OK', 200);
        }

        // Dispatch async — webhook must return 200 quickly
        ProcessWhatsAppMessageJob::dispatch($payload)->onQueue('whatsapp');

        return response('OK', 200);
    }
}
