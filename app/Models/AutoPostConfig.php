<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoPostConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'posts_per_day',
        'post_times',
        'timezone',
        'ai_model',
        'min_word_count',
        'max_word_count',
        'min_reading_time',
        'max_reading_time',
        'min_headings',
        'max_headings',
        'min_paragraphs',
        'internal_links_count',
        'include_featured_image',
        'auto_publish',
        'duplicate_threshold',
        'cooldown_days',
        'excluded_keywords',
        'category_weights',
        'language_distribution',
        'market_focus',
        'daily_limit',
        'retry_attempts',
        'timeout_seconds',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'posts_per_day' => 'integer',
        'post_times' => 'array',
        'min_word_count' => 'integer',
        'max_word_count' => 'integer',
        'min_reading_time' => 'integer',
        'max_reading_time' => 'integer',
        'min_headings' => 'integer',
        'max_headings' => 'integer',
        'min_paragraphs' => 'integer',
        'internal_links_count' => 'integer',
        'include_featured_image' => 'boolean',
        'auto_publish' => 'boolean',
        'duplicate_threshold' => 'float',
        'cooldown_days' => 'integer',
        'excluded_keywords' => 'array',
        'category_weights' => 'array',
        'language_distribution' => 'array',
        'market_focus' => 'array',
        'daily_limit' => 'integer',
        'retry_attempts' => 'integer',
        'timeout_seconds' => 'integer',
    ];

    /**
     * Get the singleton config instance
     */
    public static function current()
    {
        return static::firstOrCreate([], [
            'is_enabled' => false,
            'posts_per_day' => 3,
            'post_times' => ['08:00', '13:00', '19:00'],
            'timezone' => 'Asia/Jakarta',
            'ai_model' => 'anthropic/claude-3.5-sonnet',
            'min_word_count' => 800,
            'max_word_count' => 1500,
            'min_reading_time' => 4,
            'max_reading_time' => 8,
            'min_headings' => 3,
            'max_headings' => 6,
            'min_paragraphs' => 5,
            'internal_links_count' => 3,
            'include_featured_image' => true,
            'auto_publish' => false,
            'duplicate_threshold' => 0.75,
            'cooldown_days' => 30,
            'excluded_keywords' => [],
            'category_weights' => [
                'general' => 20,
                'tips' => 40,
                'regulation' => 25,
                'case-study' => 10,
                'news' => 5,
            ],
            'daily_limit' => 5,
            'retry_attempts' => 3,
            'timeout_seconds' => 120,
        ]);
    }

    /**
     * Check if auto-posting is enabled
     */
    public function isEnabled()
    {
        return $this->is_enabled;
    }

    /**
     * Get post times for today in Carbon instances
     */
    public function getPostTimesForDate($date)
    {
        return collect($this->post_times)->map(function ($time) use ($date) {
            return \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $time, $this->timezone);
        });
    }

    /**
     * Get category for next article based on weights
     */
    public function getNextCategory()
    {
        // Simple weighted random selection
        $weights = $this->category_weights;
        $total = array_sum($weights);
        $rand = rand(1, $total);
        
        $current = 0;
        foreach ($weights as $category => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $category;
            }
        }
        
        return 'general'; // Fallback
    }

    /**
     * Get language for next article based on distribution
     */
    public function getNextLanguage()
    {
        if (!$this->language_distribution || empty($this->language_distribution)) {
            return 'id'; // Default to Indonesian
        }

        $weights = $this->language_distribution;
        $total = array_sum($weights);
        
        if ($total === 0) {
            return 'id';
        }
        
        $rand = rand(1, $total);
        
        $current = 0;
        foreach ($weights as $lang => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $lang;
            }
        }
        
        return 'id'; // Fallback
    }

    /**
     * Get target market for next article
     */
    public function getTargetMarket($language)
    {
        if (!$this->market_focus || empty($this->market_focus)) {
            return $language === 'en' ? 'pma' : 'local';
        }

        // If both markets enabled, return based on language
        if (($this->market_focus['local'] ?? false) && ($this->market_focus['pma'] ?? false)) {
            return 'both';
        }

        // Return specific market
        if ($this->market_focus['pma'] ?? false) {
            return 'pma';
        }

        return 'local';
    }
}
