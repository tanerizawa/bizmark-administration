<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\CheckOssStatusJob;
use App\Models\OssCredential;
use App\Models\OssPermitStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class OssTrackerController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $statuses = OssPermitStatus::where('client_id', $client->id)
            ->with('project')
            ->orderByDesc('last_checked_at')
            ->get();

        $hasCredential = OssCredential::where('client_id', $client->id)
            ->where('is_active', true)
            ->exists();

        return view('client.oss-tracker.index', compact('statuses', 'hasCredential'));
    }

    /**
     * Admin atau klien dapat trigger manual re-check (throttled).
     */
    public function refreshStatus(OssPermitStatus $status)
    {
        $client = Auth::guard('client')->user();

        // Authorize: hanya punya client
        abort_unless($status->client_id === $client->id, 403);

        // Rate limit: 1x per jam per record
        $cacheKey = 'oss_manual_refresh:'.$status->id;
        if (cache()->has($cacheKey)) {
            return back()->with('warning', 'Status baru saja diperbarui. Coba lagi dalam 1 jam.');
        }

        CheckOssStatusJob::dispatch($status->client_id, $status->application_number, $status->permit_type)
            ->onQueue('oss-tracker');

        cache()->put($cacheKey, true, now()->addHour());

        return back()->with('success', 'Permintaan pengecekan status dikirim. Harap tunggu beberapa menit.');
    }

    /**
     * Simpan / update kredensial OSS klien (encrypted).
     */
    public function storeCredential(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'oss_username' => ['required', 'string', 'max:255'],
            'oss_password' => ['required', 'string', 'max:255'],
        ]);

        OssCredential::updateOrCreate(
            ['client_id' => $client->id],
            [
                'oss_username_encrypted' => Crypt::encryptString($validated['oss_username']),
                'oss_password_encrypted' => Crypt::encryptString($validated['oss_password']),
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Kredensial OSS berhasil disimpan.');
    }
}
