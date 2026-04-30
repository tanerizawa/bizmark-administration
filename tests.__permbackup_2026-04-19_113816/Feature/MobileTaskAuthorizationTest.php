<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class MobileTaskAuthorizationTest extends TestCase
{
    public function test_user_cannot_view_task_assigned_to_other_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $task = Task::factory()->create([
            'assigned_user_id' => $userB->id,
        ]);

        $this->actingAs($userA)
            ->withHeader('User-Agent', 'iPhone')
            ->get("/m/tasks/{$task->id}")
            ->assertStatus(403);
    }
}
