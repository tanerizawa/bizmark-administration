<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::query()->value('id') ?? Project::factory()->create()->id,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'sop_notes' => null,
            'assigned_user_id' => User::query()->value('id'),
            'status' => fake()->randomElement(['todo', 'in_progress', 'done', 'blocked']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'due_date' => fake()->optional()->date(),
            'started_at' => null,
            'completed_at' => null,
            'completion_notes' => null,
            'institution_id' => null,
            'depends_on_task_id' => null,
            'project_permit_id' => null,
            'estimated_hours' => null,
            'actual_hours' => null,
            'sort_order' => 0,
        ];
    }
}
