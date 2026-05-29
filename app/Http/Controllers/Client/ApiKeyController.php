<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * P8 — Client-side API key management portal.
 */
class ApiKeyController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $keys = ApiKey::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->get();

        return view('client.api-keys.index', compact('keys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:80',
            'plan' => 'required|in:free,starter,pro,enterprise',
        ]);

        $client = Auth::guard('client')->user();

        // Free plan: max 1 key. Paid: max 5.
        $limit = $request->plan === 'free' ? 1 : 5;
        if (ApiKey::where('client_id', $client->id)->count() >= $limit) {
            return back()->withErrors(['name' => "Maksimum $limit API key untuk plan {$request->plan}."]);
        }

        ApiKey::generate($client->id, $request->name, $request->plan);

        return back()->with('success', 'API key berhasil dibuat.');
    }

    public function destroy(ApiKey $apiKey)
    {
        $client = Auth::guard('client')->user();

        if ($apiKey->client_id !== $client->id) {
            abort(403);
        }

        $apiKey->delete();

        return back()->with('success', 'API key dihapus.');
    }

    public function toggleActive(ApiKey $apiKey)
    {
        $client = Auth::guard('client')->user();

        if ($apiKey->client_id !== $client->id) {
            abort(403);
        }

        $apiKey->update(['is_active' => ! $apiKey->is_active]);

        return back()->with('success', 'Status API key diperbarui.');
    }
}
