<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    /**
     * Subscribe user to push notifications
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $user->updatePushSubscription(
                $validated['endpoint'],
                $validated['keys']['p256dh'],
                $validated['keys']['auth']
            );

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengaktifkan push notification'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unsubscribe user from push notifications
     */
    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url|max:500',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $user->deletePushSubscription($validated['endpoint']);

            return response()->json([
                'success' => true,
                'message' => 'Push notification dinonaktifkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get push subscription status
     */
    public function status()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $subscriptions = $user->pushSubscriptions()->count();

        return response()->json([
            'success' => true,
            'subscribed' => $subscriptions > 0,
            'subscription_count' => $subscriptions
        ]);
    }

    /**
     * Send test push notification
     */
    public function test()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $user->notify(new \App\Notifications\TestPushNotification());

            return response()->json([
                'success' => true,
                'message' => 'Test notification terkirim'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim: ' . $e->getMessage()
            ], 500);
        }
    }
}
