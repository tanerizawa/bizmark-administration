<?php

namespace Database\Factories;

use App\Models\PermitType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermitTypeFactory extends Factory
{
    protected $model = PermitType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'code' => fake()->unique()->slug(2),
            'category' => fake()->randomElement(['environmental', 'land', 'building', 'transportation', 'business', 'other']),
            'institution_id' => null,
            'avg_processing_days' => 14,
            'description' => fake()->optional()->sentence(),
            'required_documents' => [],
            'estimated_cost_min' => 1000000,
            'estimated_cost_max' => 5000000,
            'is_active' => true,
        ];
    }
}
