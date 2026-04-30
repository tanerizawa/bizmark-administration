<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Permission;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectIncomeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $financeUser;

    private User $viewerUser;

    private Project $project;

    private int $paymentMethodId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);
        $this->seed(PaymentMethodSeeder::class);

        $roleFinance = Role::create(['name' => 'finance', 'display_name' => 'Finance']);
        $roleViewer = Role::create(['name' => 'viewer', 'display_name' => 'Viewer']);

        Permission::create(['name' => 'finances.manage_payments', 'display_name' => 'Manage Payments', 'group' => 'finances']);
        $roleFinance->permissions()->attach(Permission::where('name', 'finances.manage_payments')->first());

        $this->financeUser = User::factory()->create(['role_id' => $roleFinance->id]);
        $this->viewerUser = User::factory()->create(['role_id' => $roleViewer->id]);

        $status = ProjectStatus::factory()->create();
        $this->project = Project::factory()->create(['status_id' => $status->id]);

        $this->paymentMethodId = \App\Models\PaymentMethod::where('code', 'other')->first()->id;
    }

    private function validPayload(): array
    {
        return [
            'payment_date' => now()->toDateString(),
            'amount' => 1500000,
            'payment_method_id' => $this->paymentMethodId,
            'description' => 'Uang muka proyek perizinan',
        ];
    }

    public function test_finance_user_can_record_direct_income(): void
    {
        $response = $this->actingAs($this->financeUser)
            ->postJson("/admin/projects/{$this->project->id}/direct-income", $this->validPayload());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('project_payments', [
            'project_id' => $this->project->id,
            'amount' => 1500000,
        ]);
    }

    public function test_viewer_cannot_record_direct_income(): void
    {
        $response = $this->actingAs($this->viewerUser)
            ->postJson("/admin/projects/{$this->project->id}/direct-income", $this->validPayload());

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_record_direct_income(): void
    {
        $this->postJson("/admin/projects/{$this->project->id}/direct-income", $this->validPayload())
            ->assertStatus(401);
    }

    public function test_direct_income_requires_amount(): void
    {
        $payload = $this->validPayload();
        unset($payload['amount']);

        $this->actingAs($this->financeUser)
            ->postJson("/admin/projects/{$this->project->id}/direct-income", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_direct_income_amount_must_be_positive(): void
    {
        $payload = array_merge($this->validPayload(), ['amount' => 0]);

        $this->actingAs($this->financeUser)
            ->postJson("/admin/projects/{$this->project->id}/direct-income", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_direct_income_requires_valid_payment_method(): void
    {
        $payload = array_merge($this->validPayload(), ['payment_method_id' => 99999]);

        $this->actingAs($this->financeUser)
            ->postJson("/admin/projects/{$this->project->id}/direct-income", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('payment_method_id');
    }

    public function test_finance_user_can_delete_direct_income(): void
    {
        // Create a payment first
        $this->actingAs($this->financeUser)
            ->postJson("/admin/projects/{$this->project->id}/direct-income", $this->validPayload());

        $payment = \App\Models\ProjectPayment::where('project_id', $this->project->id)->first();
        $this->assertNotNull($payment);

        $response = $this->actingAs($this->financeUser)
            ->deleteJson("/admin/projects/{$this->project->id}/direct-income/{$payment->id}");

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('project_payments', ['id' => $payment->id]);
    }
}
