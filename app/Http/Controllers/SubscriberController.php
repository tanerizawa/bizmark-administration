<?php

namespace App\Http\Controllers;

use App\Models\EmailSubscriber;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriberController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:email_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        $validated['source'] = 'landing_page';
        $validated['status'] = 'active';

        EmailSubscriber::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Anda telah berhasil berlangganan newsletter kami.'
        ]);
    }

    public function unsubscribe($email, $token)
    {
        $subscriber = EmailSubscriber::where('email', $email)->firstOrFail();
        
        // Simple token validation (you can enhance this)
        $expectedToken = md5($email . config('app.key'));
        
        if ($token !== $expectedToken) {
            abort(403, 'Invalid unsubscribe link');
        }

        $subscriber->unsubscribe('User requested unsubscribe');

        return view('emails.unsubscribed', compact('subscriber'));
    }

    public function trackOpen($trackingId)
    {
        $log = EmailLog::where('tracking_id', $trackingId)->first();
        
        if ($log && !$log->opened_at) {
            $log->markAsOpened(
                request()->ip(),
                request()->header('User-Agent')
            );
        }

        // Return 1x1 transparent pixel
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
            ->header('Content-Type', 'image/gif');
    }
}
