<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\PermitApplication;
use App\Models\Role;
use App\Models\User;
use App\Policies\DocumentPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PermitApplicationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $userWithFinance;

    private User $userWithoutFinance;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $roleStaff = Role::create(['name' => 'staff', 'display_name' => 'Staff']);

        Permission::create(['name' => 'finances.view', 'display_name' => 'View Finances', 'group' => 'finances']);
        Permission::create(['name' => 'finances.create', 'display_name' => 'Create Finances', 'group' => 'finances']);
        Permission::create(['name' => 'finances.edit', 'display_name' => 'Edit Finances', 'group' => 'finances']);
        Permission::create(['name' => 'finances.delete', 'display_name' => 'Delete Finances', 'group' => 'finances']);

        $roleAdmin->permissions()->attach(
            Permission::whereIn('name', ['finances.view', 'finances.create', 'finances.edit', 'finances.delete'])->pluck('id')
        );

        $this->userWithFinance = User::factory()->create(['role_id' => $roleAdmin->id]);
        $this->userWithoutFinance = User::factory()->create(['role_id' => $roleStaff->id]);
    }

    public function test_payment_policy_allows_user_with_finances_view(): void
    {
        $policy = new PaymentPolicy;
        $payment = new Payment;

        $this->assertTrue($policy->viewAny($this->userWithFinance));
        $this->assertTrue($policy->view($this->userWithFinance, $payment));
    }

    public function test_payment_policy_denies_user_without_permission(): void
    {
        $policy = new PaymentPolicy;
        $payment = new Payment;

        $this->assertFalse($policy->viewAny($this->userWithoutFinance));
        $this->assertFalse($policy->view($this->userWithoutFinance, $payment));
        $this->assertFalse($policy->create($this->userWithoutFinance));
        $this->assertFalse($policy->delete($this->userWithoutFinance, $payment));
    }

    public function test_payment_policy_allows_create_with_permission(): void
    {
        $policy = new PaymentPolicy;

        $this->assertTrue($policy->create($this->userWithFinance));
    }

    public function test_document_policy_not_stub(): void
    {
        Permission::create(['name' => 'documents.view', 'display_name' => 'View Documents', 'group' => 'documents']);
        Permission::create(['name' => 'documents.delete', 'display_name' => 'Delete Documents', 'group' => 'documents']);

        $role = Role::where('name', 'admin')->first();
        $role->permissions()->attach(
            Permission::whereIn('name', ['documents.view', 'documents.delete'])->pluck('id')
        );

        $policy = new DocumentPolicy;
        $doc = new Document;

        $this->assertTrue($policy->viewAny($this->userWithFinance));
        $this->assertTrue($policy->view($this->userWithFinance, $doc));
        $this->assertTrue($policy->delete($this->userWithFinance, $doc));
        $this->assertFalse($policy->viewAny($this->userWithoutFinance));
    }

    public function test_permit_application_policy_client_can_view_own(): void
    {
        $client = Client::factory()->create();
        $otherClient = Client::factory()->create();

        $application = PermitApplication::factory()->create(['client_id' => $client->id]);

        $policy = new PermitApplicationPolicy;

        $this->assertTrue($policy->viewAsClient($client, $application));
        $this->assertFalse($policy->viewAsClient($otherClient, $application));
        $this->assertFalse($policy->viewAsClient(null, $application));
    }

    public function test_permit_application_policy_client_cancel_only_draft(): void
    {
        $client = Client::factory()->create();

        $draftApp = PermitApplication::factory()->create([
            'client_id' => $client->id,
            'status' => 'draft',
        ]);
        $approvedApp = PermitApplication::factory()->create([
            'client_id' => $client->id,
            'status' => 'approved',
        ]);

        $policy = new PermitApplicationPolicy;

        $this->assertTrue($policy->cancelAsClient($client, $draftApp));
        $this->assertFalse($policy->cancelAsClient($client, $approvedApp));
    }
}
