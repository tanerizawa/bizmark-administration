<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NotificationChannels\WebPush\PushSubscription;

class PushNotificationController extends Controller
{
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

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $client->updatePushSubscription(
                $validated['endpoint'],
                $validated['keys']['p256dh'],
                $validated['keys']['auth']
            );

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to push notifications'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe: ' . $e->getMessage()
            ], 500);
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

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $client->deletePushSubscription($validated['endpoint']);

            return response()->json([
                'success' => true,
                'message' => 'Successfully unsubscribed from push notifications'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unsubscribe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's push subscription status
     */
    public function status(Request $request)
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $subscriptions = $client->pushSubscriptions()->count();

        return response()->json([
            'success' => true,
            'subscribed' => $subscriptions > 0,
            'subscription_count' => $subscriptions
        ]);
    }

    /**
     * Send test notification
     */
    public function test(Request $request)
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            // Check if client has any push subscriptions
            $subscriptionCount = $client->pushSubscriptions()->count();
            
            if ($subscriptionCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No push subscriptions found. Please enable notifications first.'
                ], 400);
            }

            // Send test notification to all client's devices
            $client->notify(new \App\Notifications\TestNotification());

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully!',
                'devices' => $subscriptionCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send test notification', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
