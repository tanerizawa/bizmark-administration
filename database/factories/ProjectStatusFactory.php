<?php

namespace Database\Factories;

use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectStatusFactory extends Factory
{
    protected $model = ProjectStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->slug(2),
            'description' => fake()->optional()->sentence(),
            'color' => '#3B82F6',
            'sort_order' => 0,
            'is_active' => true,
            'is_final' => false,
        ];
    }
}
