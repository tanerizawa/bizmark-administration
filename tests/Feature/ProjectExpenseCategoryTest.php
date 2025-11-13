<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_category_can_be_stored_via_project_expense_controller(): void
    {
        $user = User::factory()->create();

        $status = ProjectStatus::create([
            'name' => 'In Progress',
            'code' => 'IN_PROGRESS',
        ]);

        $project = Project::forceCreate([
            'code' => 'PRJ-TEST-001',
            'name' => 'Project Pengujian',
            'client_name' => 'Klien Uji',
            'permit_type' => 'environmental',
            'current_status_id' => $status->id,
        ]);

        $payload = [
            'expense_date' => '2024-01-15',
            'category' => 'communication',
            'vendor_name' => 'PT Telekomunikasi',
            'amount' => 1500000,
            'payment_method' => 'transfer',
            'description' => 'Layanan internet proyek',
        ];

        $response = $this->actingAs($user)->post(route('projects.expenses.store', $project), $payload);

        $response->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('project_expenses', [
            'project_id' => $project->id,
            'category' => 'communication',
            'description' => 'Layanan internet proyek',
        ]);

        $expense = ProjectExpense::first();

        $this->assertNotNull($expense);
        $this->assertSame('Komunikasi & Internet', $expense->category_name);
        $this->assertSame('📞', $expense->category_icon);
    }
}
