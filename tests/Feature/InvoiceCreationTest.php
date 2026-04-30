<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Permission;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceCreationTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Project $project;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        Permission::create(['name' => 'invoices.create', 'display_name' => 'Create Invoices', 'group' => 'invoices']);
        Permission::create(['name' => 'invoices.view', 'display_name' => 'View Invoices', 'group' => 'invoices']);
        $role->permissions()->attach(Permission::whereIn('name', ['invoices.create', 'invoices.view'])->pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
        $this->client = Client::factory()->create();
        $status = ProjectStatus::factory()->create();
        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'status_id' => $status->id,
        ]);
    }

    public function test_admin_can_create_invoice(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])->actingAs($this->adminUser)
            ->postJson("/admin/projects/{$this->project->id}/invoices", [
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'tax_rate' => 11,
                'notes' => 'Test invoice',
                'items' => [
                    [
                        'description' => 'Jasa Konsultasi Perizinan',
                        'quantity' => 1,
                        'unit_price' => 5000000,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['invoice' => ['invoice_number', 'items']]);

        $this->assertDatabaseHas('invoices', [
            'project_id' => $this->project->id,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'description' => 'Jasa Konsultasi Perizinan',
            'quantity' => 1,
        ]);
    }

    public function test_invoice_requires_items(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])->actingAs($this->adminUser)
            ->postJson("/admin/projects/{$this->project->id}/invoices", [
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'tax_rate' => 11,
                'items' => [],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('items');
    }

    public function test_invoice_requires_due_date_after_invoice_date(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])->actingAs($this->adminUser)
            ->postJson("/admin/projects/{$this->project->id}/invoices", [
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->subDay()->toDateString(),
                'tax_rate' => 11,
                'items' => [
                    ['description' => 'Item', 'quantity' => 1, 'unit_price' => 100000],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('due_date');
    }

    public function test_invoice_generates_unique_number(): void
    {
        $number1 = Invoice::generateInvoiceNumber();
        Invoice::create([
            'project_id' => $this->project->id,
            'invoice_number' => $number1,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 0,
            'tax_rate' => 11,
            'tax_amount' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'remaining_amount' => 0,
            'status' => 'draft',
        ]);

        $number2 = Invoice::generateInvoiceNumber();

        $this->assertNotEquals($number1, $number2);
        $this->assertStringStartsWith('INV-', $number1);
        $this->assertStringStartsWith('INV-', $number2);
    }

    public function test_invoice_fails_for_project_without_client(): void
    {
        $projectNoClient = Project::factory()->create([
            'client_id' => null,
            'status_id' => $this->project->status_id,
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])->actingAs($this->adminUser)
            ->postJson("/admin/projects/{$projectNoClient->id}/invoices", [
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'tax_rate' => 11,
                'items' => [
                    ['description' => 'Item', 'quantity' => 1, 'unit_price' => 100000],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_unauthenticated_cannot_create_invoice(): void
    {
        $response = $this->postJson("/admin/projects/{$this->project->id}/invoices", [
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'tax_rate' => 11,
            'items' => [
                ['description' => 'Item', 'quantity' => 1, 'unit_price' => 100000],
            ],
        ]);

        $response->assertStatus(401);
    }
}
