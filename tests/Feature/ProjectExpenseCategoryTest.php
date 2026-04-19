<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckPermission::class);
        $this->withoutMiddleware(EnsureTwoFactorVerified::class);
    }

    public function test_new_category_can_be_stored_via_project_expense_controller(): void
    {
        $user = User::factory()->create();

        $status = ProjectStatus::create([
            'name' => 'In Progress',
            'code' => 'IN_PROGRESS',
        ]);

        $project = Project::forceCreate([
            'name' => 'Project Pengujian',
            'client_name' => 'Klien Uji',
            'client_contact' => '081234567890',
            'status_id' => $status->id,
        ]);

        ExpenseCategory::create([
            'slug' => 'communication',
            'name' => 'Komunikasi & Internet',
            'group' => 'Operasional',
            'icon' => '📞',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 100,
        ]);

        PaymentMethod::create([
            'code' => 'transfer',
            'name' => 'Transfer Bank',
            'description' => 'Transfer via rekening bank',
            'requires_cash_account' => false,
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 10,
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
