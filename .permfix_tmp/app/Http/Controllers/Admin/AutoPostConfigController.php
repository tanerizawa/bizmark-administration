<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostConfig;
use Illuminate\Http\Request;

class AutoPostConfigController extends Controller
{
    public function index()
    {
        $config = AutoPostConfig::current();
        
        return view('admin.auto-post.config', compact('config'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'is_enabled' => 'boolean',
            'posts_per_day' => 'required|integer|min:1|max:24',
            'post_times' => 'required|array|min:1|max:24',
            'post_times.*' => 'required|date_format:H:i|distinct',
            'ai_model' => 'required|string',
            'min_word_count' => 'required|integer|min:300',
            'max_word_count' => 'required|integer|min:500|gte:min_word_count',
            'duplicate_threshold' => 'required|numeric|min:0|max:1',
            'cooldown_days' => 'nullable|integer|min:1|max:365',
            'internal_links_count' => 'required|integer|min:0|max:10',
            'auto_publish' => 'boolean',
            'language_distribution' => 'nullable|array',
            'language_distribution.*' => 'nullable|integer|min:0|max:100',
            'market_focus' => 'nullable|array',
        ]);
        
        // Ensure boolean fields default to false if not sent
        $validated['auto_publish'] = $request->boolean('auto_publish');
        $validated['is_enabled'] = $request->boolean('is_enabled');

        // Normalize post times to unique + sorted values for deterministic scheduling.
        $validated['post_times'] = collect($validated['post_times'] ?? [])
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (count($validated['post_times']) !== (int) $validated['posts_per_day']) {
            return back()
                ->withErrors([
                    'post_times' => 'Jumlah waktu posting harus sama dengan Post Per Hari.',
                ])
                ->withInput();
        }
        
        // Handle market_focus checkboxes
        if (isset($validated['market_focus'])) {
            $validated['market_focus'] = [
                'local' => isset($validated['market_focus']['local']),
                'pma' => isset($validated['market_focus']['pma']),
            ];
        }
        
        $config = AutoPostConfig::current();
        $config->update($validated);
        
        return redirect()
            ->route('auto-post.config')
            ->with('success', 'Konfigurasi berhasil diperbarui');
    }
    
    public function toggle(Request $request)
    {
        $config = AutoPostConfig::current();
        $config->update(['is_enabled' => !$config->is_enabled]);
        
        $status = $config->is_enabled ? 'diaktifkan' : 'dinonaktifkan';
        
        return response()->json([
            'success' => true,
            'message' => "Auto-posting berhasil {$status}",
            'is_enabled' => $config->is_enabled,
        ]);
    }
}
