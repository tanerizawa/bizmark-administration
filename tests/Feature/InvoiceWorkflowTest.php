<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\CashAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Permission;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InvoiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Project $project;

    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentMethodSeeder::class);
        \App\Models\PaymentMethod::clearCache();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        foreach (['invoices.create', 'invoices.view', 'invoices.edit', 'invoices.delete', 'finances.manage_payments'] as $perm) {
            Permission::create(['name' => $perm, 'display_name' => $perm, 'group' => 'invoices']);
        }
        $role->permissions()->attach(Permission::pluck('id'));

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
        $client = Client::factory()->create();
        $status = ProjectStatus::factory()->create();
        $this->project = Project::factory()->create([
            'client_id' => $client->id,
            'status_id' => $status->id,
        ]);

        $this->invoice = Invoice::create([
            'project_id' => $this->project->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 5000000,
            'tax_rate' => 11,
            'tax_amount' => 550000,
            'total_amount' => 5550000,
            'paid_amount' => 0,
            'remaining_amount' => 5550000,
            'client_name' => $client->name,
            'client_address' => $client->company ?? '',
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_update_invoice_status(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->patchJson("/admin/invoices/{$this->invoice->id}/status", [
                'status' => 'sent',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'sent',
        ]);
    }

    public function test_update_status_rejects_invalid_status(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->patchJson("/admin/invoices/{$this->invoice->id}/status", [
                'status' => 'invalid_status',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_admin_can_send_draft_invoice(): void
    {
        Notification::fake();

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson("/admin/invoices/{$this->invoice->id}/send");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'sent',
        ]);
    }

    public function test_send_fails_for_non_draft_invoice(): void
    {
        $this->invoice->update(['status' => 'sent']);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson("/admin/invoices/{$this->invoice->id}/send");

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_admin_can_delete_draft_invoice(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->deleteJson("/admin/invoices/{$this->invoice->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('invoices', ['id' => $this->invoice->id]);
    }

    public function test_cannot_delete_non_draft_invoice(): void
    {
        $this->invoice->update(['status' => 'sent']);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->deleteJson("/admin/invoices/{$this->invoice->id}");

        $response->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('invoices', ['id' => $this->invoice->id]);
    }

    public function test_record_payment_updates_invoice_balance(): void
    {
        $cashAccount = CashAccount::create([
            'account_name' => 'Bank BCA',
            'account_type' => 'bank',
            'bank_name' => 'BCA',
            'current_balance' => 0,
            'is_active' => true,
        ]);

        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson("/admin/invoices/{$this->invoice->id}/payment", [
                'amount' => 5550000,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TF-2026-001',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'paid',
        ]);

        $this->assertEquals(5550000, $cashAccount->fresh()->current_balance);
    }

    public function test_record_payment_fails_without_cash_account(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson("/admin/invoices/{$this->invoice->id}/payment", [
                'amount' => 1000000,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    }

    public function test_record_payment_requires_positive_amount(): void
    {
        $response = $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson("/admin/invoices/{$this->invoice->id}/payment", [
                'amount' => 0,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_unauthenticated_cannot_update_invoice_status(): void
    {
        $this->patchJson("/admin/invoices/{$this->invoice->id}/status", ['status' => 'sent'])
            ->assertStatus(401);
    }

    public function test_invoice_partial_payment_sets_partial_status(): void
    {
        CashAccount::create([
            'account_name' => 'Bank Mandiri',
            'account_type' => 'bank',
            'bank_name' => 'Mandiri',
            'current_balance' => 0,
            'is_active' => true,
        ]);

        $this->withoutMiddleware([CheckPermission::class, EnsureTwoFactorVerified::class])
            ->actingAs($this->adminUser)
            ->postJson("/admin/invoices/{$this->invoice->id}/payment", [
                'amount' => 2000000,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'bank_transfer',
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'partial',
        ]);
    }
}
