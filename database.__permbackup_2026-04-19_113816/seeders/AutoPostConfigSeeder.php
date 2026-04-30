<?php

namespace Database\Seeders;

use App\Models\AutoPostConfig;
use Illuminate\Database\Seeder;

class AutoPostConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Seeding Auto Post Configuration...');

        $config = AutoPostConfig::create([
            'is_enabled' => false, // Disabled by default - enable manually
            'posts_per_day' => 3,
            'post_times' => ['08:00', '13:00', '19:00'], // Prime times: Morning, Lunch, Evening
            'timezone' => 'Asia/Jakarta',

            // AI Settings
            'ai_model' => 'anthropic/claude-3.5-sonnet',
            'min_word_count' => 800,
            'max_word_count' => 1500,
            'min_reading_time' => 4,
            'max_reading_time' => 8,

            // Content Rules
            'min_headings' => 3,
            'max_headings' => 6,
            'min_paragraphs' => 5,
            'internal_links_count' => 3,
            'include_featured_image' => true,
            'auto_publish' => false, // Save as draft by default

            // Quality Control
            'duplicate_threshold' => 0.75,
            'cooldown_days' => 30,
            'excluded_keywords' => [],

            // Category Distribution (based on business focus)
            'category_weights' => [
                'tips' => 40,        // Most valuable for SEO & user engagement
                'regulation' => 25,  // Core expertise
                'general' => 20,     // General knowledge
                'case-study' => 10,  // Social proof
                'news' => 5,         // Trending topics
            ],

            // Monitoring
            'daily_limit' => 5,
            'retry_attempts' => 3,
            'timeout_seconds' => 120,
        ]);

        $this->command->info('✅ Auto Post Configuration created!');
        $this->command->line('');
        $this->command->info('📋 Configuration Summary:');
        $this->command->line('   Status: '.($config->is_enabled ? 'ENABLED' : 'DISABLED'));
        $this->command->line("   Posts per day: {$config->posts_per_day}");
        $this->command->line('   Prime times: '.implode(', ', $config->post_times));
        $this->command->line("   AI Model: {$config->ai_model}");
        $this->command->line('   Auto-publish: '.($config->auto_publish ? 'YES' : 'NO (save as draft)'));
        $this->command->line('');
        $this->command->warn('⚠️  Auto-posting is DISABLED by default. Enable from admin panel.');
    }
}
