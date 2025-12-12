<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostConfig;
use Illuminate\Http\Request;

class AutoPostConfigController extends Controller
{
    public function index()
    {
        $config = AutoPostConfig::first();
        
        if (!$config) {
            $config = AutoPostConfig::create([
                'is_enabled' => false,
                'posts_per_day' => 3,
                'post_times' => ['08:00', '13:00', '19:00'],
                'ai_model' => 'anthropic/claude-3.5-sonnet',
                'min_word_count' => 800,
                'max_word_count' => 1500,
                'similarity_threshold' => 0.75,
                'quality_threshold' => 70,
                'internal_links_count' => 3,
                'auto_publish' => true,
                'auto_add_tags' => true,
                'auto_schedule_days' => 7,
            ]);
        }
        
        return view('admin.auto-post.config', compact('config'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'is_enabled' => 'boolean',
            'posts_per_day' => 'required|integer|min:1|max:10',
            'post_times' => 'required|array|min:1',
            'post_times.*' => 'required|date_format:H:i',
            'ai_model' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:2',
            'min_word_count' => 'required|integer|min:300',
            'max_word_count' => 'required|integer|min:500',
            'similarity_threshold' => 'required|numeric|min:0|max:1',
            'quality_threshold' => 'required|integer|min:0|max:100',
            'internal_links_count' => 'required|integer|min:0|max:10',
            'auto_publish' => 'boolean',
            'auto_add_tags' => 'boolean',
            'auto_schedule_days' => 'required|integer|min:1|max:30',
        ]);
        
        $config = AutoPostConfig::first();
        $config->update($validated);
        
        return redirect()
            ->route('admin.auto-post.config')
            ->with('success', 'Konfigurasi berhasil diperbarui');
    }
    
    public function toggle(Request $request)
    {
        $config = AutoPostConfig::first();
        $config->update(['is_enabled' => !$config->is_enabled]);
        
        $status = $config->is_enabled ? 'diaktifkan' : 'dinonaktifkan';
        
        return response()->json([
            'success' => true,
            'message' => "Auto-posting berhasil {$status}",
            'is_enabled' => $config->is_enabled,
        ]);
    }
}
