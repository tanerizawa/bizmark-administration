<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Permission;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ExpenseCategorySeeder;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectExpenseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $adminWithPermission;

    private User $userWithoutPermission;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);
        $this->seed(PaymentMethodSeeder::class);
        $this->seed(ExpenseCategorySeeder::class);

        $roleAdmin = Role::create(['name' => 'admin_finance', 'display_name' => 'Admin Finance']);
        $roleViewer = Role::create(['name' => 'viewer', 'display_name' => 'Viewer']);

        Permission::create(['name' => 'finances.manage_expenses', 'display_name' => 'Manage Expenses', 'group' => 'finances']);
        $roleAdmin->permissions()->attach(Permission::where('name', 'finances.manage_expenses')->first());

        $this->adminWithPermission = User::factory()->create(['role_id' => $roleAdmin->id]);
        $this->userWithoutPermission = User::factory()->create(['role_id' => $roleViewer->id]);

        $status = ProjectStatus::factory()->create();
        $this->project = Project::factory()->create(['status_id' => $status->id]);
    }

    public function test_user_with_permission_can_create_expense(): void
    {
        $response = $this->actingAs($this->adminWithPermission)
            ->post("/admin/projects/{$this->project->id}/expenses", [
                'expense_date' => now()->toDateString(),
                'category' => 'survey',
                'amount' => 500000,
                'payment_method' => 'bank_transfer',
                'description' => 'Biaya survey lapangan',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_expenses', [
            'project_id' => $this->project->id,
            'amount' => 500000,
            'category' => 'survey',
        ]);
    }

    public function test_user_without_permission_cannot_create_expense(): void
    {
        $response = $this->actingAs($this->userWithoutPermission)
            ->postJson("/admin/projects/{$this->project->id}/expenses", [
                'expense_date' => now()->toDateString(),
                'category' => 'survey',
                'amount' => 500000,
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('project_expenses', [
            'project_id' => $this->project->id,
        ]);
    }

    public function test_unauthenticated_cannot_create_expense(): void
    {
        $this->postJson("/admin/projects/{$this->project->id}/expenses", [
            'expense_date' => now()->toDateString(),
            'category' => 'survey',
            'amount' => 500000,
            'payment_method' => 'bank_transfer',
        ])->assertStatus(401);
    }

    public function test_user_with_permission_can_delete_expense(): void
    {
        $expense = ProjectExpense::create([
            'project_id' => $this->project->id,
            'expense_date' => now()->toDateString(),
            'category' => 'survey',
            'amount' => 200000,
            'payment_method' => 'cash',
            'created_by' => $this->adminWithPermission->id,
        ]);

        $response = $this->actingAs($this->adminWithPermission)
            ->delete("/admin/expenses/{$expense->id}");

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('project_expenses', ['id' => $expense->id]);
    }

    public function test_user_without_permission_cannot_delete_expense(): void
    {
        $expense = ProjectExpense::create([
            'project_id' => $this->project->id,
            'expense_date' => now()->toDateString(),
            'category' => 'survey',
            'amount' => 200000,
            'payment_method' => 'cash',
            'created_by' => $this->adminWithPermission->id,
        ]);

        $response = $this->actingAs($this->userWithoutPermission)
            ->deleteJson("/admin/expenses/{$expense->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('project_expenses', ['id' => $expense->id]);
    }

    public function test_expense_requires_amount(): void
    {
        $response = $this->actingAs($this->adminWithPermission)
            ->postJson("/admin/projects/{$this->project->id}/expenses", [
                'expense_date' => now()->toDateString(),
                'category' => 'survey',
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_expense_amount_must_be_positive(): void
    {
        $response = $this->actingAs($this->adminWithPermission)
            ->postJson("/admin/projects/{$this->project->id}/expenses", [
                'expense_date' => now()->toDateString(),
                'category' => 'survey',
                'amount' => -100,
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }
}
