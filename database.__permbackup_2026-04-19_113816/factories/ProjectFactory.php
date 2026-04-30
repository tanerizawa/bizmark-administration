<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $statusId = ProjectStatus::query()->value('id') ?? ProjectStatus::factory()->create()->id;

        return [
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'client_id' => null,
            'permit_application_id' => null,
            'client_name' => fake()->name(),
            'client_contact' => fake()->phoneNumber(),
            'client_address' => fake()->optional()->address(),
            'status_id' => $statusId,
            'institution_id' => null,
            'start_date' => fake()->optional()->date(),
            'deadline' => fake()->optional()->date(),
            'actual_completion_date' => null,
            'completed_at' => null,
            'completion_notes' => null,
            'progress_percentage' => 0,
            'budget' => fake()->optional()->randomFloat(2, 1000000, 100000000),
            'actual_cost' => 0,
            'notes' => fake()->optional()->sentence(),
            'contract_value' => 0,
            'down_payment' => 0,
            'payment_received' => 0,
            'total_expenses' => 0,
            'profit_margin' => 0,
            'payment_terms' => null,
            'payment_status' => 'unpaid',
        ];
    }
}
