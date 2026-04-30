<?php

namespace Database\Seeders;

use App\Models\AISetting;
use Illuminate\Database\Seeder;

class AISettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Pricing: Business Size Multipliers
            [
                'category' => 'pricing',
                'key' => 'pricing.size_multiplier.micro',
                'value' => '1.0',
                'data_type' => 'number',
                'description' => 'Multiplier for micro businesses (< 10 employees)',
                'default_value' => '1.0',
                'display_order' => 1,
                'group_name' => 'Business Size Multipliers',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.size_multiplier.small',
                'value' => '1.3',
                'data_type' => 'number',
                'description' => 'Multiplier for small businesses (10-50 employees)',
                'default_value' => '1.3',
                'display_order' => 2,
                'group_name' => 'Business Size Multipliers',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.size_multiplier.medium',
                'value' => '1.8',
                'data_type' => 'number',
                'description' => 'Multiplier for medium businesses (50-100 employees)',
                'default_value' => '1.8',
                'display_order' => 3,
                'group_name' => 'Business Size Multipliers',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.size_multiplier.large',
                'value' => '2.5',
                'data_type' => 'number',
                'description' => 'Multiplier for large businesses (> 100 employees)',
                'default_value' => '2.5',
                'display_order' => 4,
                'group_name' => 'Business Size Multipliers',
            ],

            // Pricing: Location Multipliers
            [
                'category' => 'pricing',
                'key' => 'pricing.location_multiplier.industrial',
                'value' => '1.2',
                'data_type' => 'number',
                'description' => 'Multiplier for industrial zones',
                'default_value' => '1.2',
                'display_order' => 11,
                'group_name' => 'Location Multipliers',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.location_multiplier.commercial',
                'value' => '1.0',
                'data_type' => 'number',
                'description' => 'Multiplier for commercial areas',
                'default_value' => '1.0',
                'display_order' => 12,
                'group_name' => 'Location Multipliers',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.location_multiplier.residential',
                'value' => '0.9',
                'data_type' => 'number',
                'description' => 'Multiplier for residential areas',
                'default_value' => '0.9',
                'display_order' => 13,
                'group_name' => 'Location Multipliers',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.location_multiplier.rural',
                'value' => '0.8',
                'data_type' => 'number',
                'description' => 'Multiplier for rural areas',
                'default_value' => '0.8',
                'display_order' => 14,
                'group_name' => 'Location Multipliers',
            ],

            // Pricing: Calculation Rules
            [
                'category' => 'pricing',
                'key' => 'pricing.overhead_percentage',
                'value' => '10',
                'data_type' => 'number',
                'description' => 'Overhead percentage added to costs',
                'default_value' => '10',
                'display_order' => 21,
                'group_name' => 'Calculation Rules',
                'validation_rules' => ['min:0', 'max:50'],
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.minimum_grand_total',
                'value' => '3000000',
                'data_type' => 'integer',
                'description' => 'Minimum grand total in IDR',
                'default_value' => '3000000',
                'display_order' => 22,
                'group_name' => 'Calculation Rules',
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.cost_range_variance',
                'value' => '15',
                'data_type' => 'number',
                'description' => 'Cost range variance percentage (±)',
                'default_value' => '15',
                'display_order' => 23,
                'group_name' => 'Calculation Rules',
                'validation_rules' => ['min:5', 'max:30'],
            ],
            [
                'category' => 'pricing',
                'key' => 'pricing.confidence_threshold',
                'value' => '0.5',
                'data_type' => 'number',
                'description' => 'Minimum confidence score threshold',
                'default_value' => '0.5',
                'display_order' => 24,
                'group_name' => 'Calculation Rules',
                'validation_rules' => ['min:0.3', 'max:1.0'],
            ],

            // Global: AI Provider
            [
                'category' => 'global',
                'key' => 'global.ai_enabled',
                'value' => '1',
                'data_type' => 'boolean',
                'description' => 'Enable/disable all AI features',
                'default_value' => '1',
                'display_order' => 1,
                'group_name' => 'General Settings',
                'is_public' => true,
            ],
            [
                'category' => 'global',
                'key' => 'global.ai_timeout',
                'value' => '30',
                'data_type' => 'integer',
                'description' => 'AI request timeout in seconds',
                'default_value' => '30',
                'display_order' => 2,
                'group_name' => 'General Settings',
            ],
        ];

        foreach ($settings as $setting) {
            AISetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('AI Settings seeded successfully!');
    }
}
