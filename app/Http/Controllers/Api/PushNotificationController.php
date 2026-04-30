<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    use ApiResponse;

    /**
     * Subscribe to push notifications
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $client = Auth::guard('client')->user();

        if (! $client) {
            return $this->unauthorized();
        }

        try {
            $client->updatePushSubscription(
                $validated['endpoint'],
                $validated['keys']['p256dh'],
                $validated['keys']['auth']
            );

            return $this->success(null, 'Berhasil berlangganan notifikasi push');
        } catch (\Exception $e) {
            return $this->serverError('Gagal berlangganan: '.$e->getMessage());
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url|max:500',
        ]);

        $client = Auth::guard('client')->user();

        if (! $client) {
            return $this->unauthorized();
        }

        try {
            $client->deletePushSubscription($validated['endpoint']);

            return $this->success(null, 'Berhasil berhenti berlangganan notifikasi push');
        } catch (\Exception $e) {
            return $this->serverError('Gagal berhenti berlangganan: '.$e->getMessage());
        }
    }

    /**
     * Get user's push subscription status
     */
    public function status(Request $request)
    {
        $client = Auth::guard('client')->user();

        if (! $client) {
            return $this->unauthorized();
        }

        $subscriptions = $client->pushSubscriptions()->count();

        return $this->success([
            'subscribed' => $subscriptions > 0,
            'subscription_count' => $subscriptions,
        ]);
    }

    /**
     * Send test notification
     */
    public function test(Request $request)
    {
        $client = Auth::guard('client')->user();

        if (! $client) {
            return $this->unauthorized();
        }

        try {
            // Check if client has any push subscriptions
            $subscriptionCount = $client->pushSubscriptions()->count();

            if ($subscriptionCount === 0) {
                return $this->error('Belum ada langganan notifikasi. Aktifkan notifikasi terlebih dahulu.');
            }

            // Send test notification to all client's devices
            $client->notify(new TestNotification);

            return $this->success(['devices' => $subscriptionCount], 'Notifikasi uji berhasil dikirim!');

        } catch (\Exception $e) {
            Log::error('Failed to send test notification', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverError('Gagal mengirim notifikasi uji: '.$e->getMessage());
        }
    }
}
