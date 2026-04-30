<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Client;
use App\Models\Permission;
use App\Models\PermitApplication;
use App\Models\PermitType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminPermitApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $unauthorized;

    private Client $client;

    private PermitType $permitType;

    private PermitApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permManage = Permission::firstOrCreate(
            ['name' => 'permits.manage'],
            ['display_name' => 'Manage Permits', 'group' => 'permits']
        );
        $roleAdmin->permissions()->syncWithoutDetaching([$permManage->id]);

        $this->admin = User::factory()->create(['role_id' => $roleAdmin->id]);
        $this->unauthorized = User::factory()->create(['role_id' => $roleViewer->id]);

        $this->client = Client::create([
            'name' => 'PT Maju Jaya',
            'company_name' => 'PT Maju Jaya',
            'email' => 'maju@example.com',
            'password' => bcrypt('password'),
            'phone' => '0217654321',
            'client_type' => 'company',
            'status' => 'active',
        ]);

        $this->permitType = PermitType::create([
            'name' => 'Izin Lingkungan',
            'code' => 'IL-001',
            'category' => 'environmental',
            'is_active' => true,
        ]);

        $this->application = PermitApplication::create([
            'client_id' => $this->client->id,
            'permit_type_id' => $this->permitType->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function test_admin_can_list_permit_applications(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.permit-applications.index'))
            ->assertOk();
    }

    public function test_unauthorized_cannot_access_permit_applications(): void
    {
        $this->actingAs($this->unauthorized)
            ->get(route('admin.permit-applications.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('admin.permit-applications.index'))
            ->assertRedirect();
    }

    public function test_admin_can_view_application_detail(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.permit-applications.show', $this->application->id))
            ->assertOk();
    }

    public function test_show_returns_404_for_missing_application(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.permit-applications.show', 99999))
            ->assertNotFound();
    }

    public function test_admin_can_start_review_on_submitted_application(): void
    {
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.permit-applications.start-review', $this->application->id))
            ->assertRedirect(route('admin.permit-applications.show', $this->application->id));

        $this->application->refresh();
        $this->assertEquals('under_review', $this->application->status);
        $this->assertEquals($this->admin->id, $this->application->reviewed_by);
        $this->assertNotNull($this->application->reviewed_at);
    }

    public function test_start_review_rejects_non_submitted_application(): void
    {
        $this->application->update(['status' => 'draft']);

        $this->actingAs($this->admin)
            ->post(route('admin.permit-applications.start-review', $this->application->id))
            ->assertRedirect();

        $this->assertEquals('draft', $this->application->fresh()->status);
    }

    public function test_admin_can_update_status_to_document_incomplete(): void
    {
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.permit-applications.update-status', $this->application->id), [
                'status' => 'document_incomplete',
                'notes' => 'Dokumen NPWP belum dilampirkan.',
            ])
            ->assertRedirect();

        $this->assertEquals('document_incomplete', $this->application->fresh()->status);
    }

    public function test_admin_can_update_status_to_cancelled(): void
    {
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.permit-applications.update-status', $this->application->id), [
                'status' => 'cancelled',
                'notes' => 'Dibatalkan atas permintaan client.',
            ])
            ->assertRedirect();

        $this->assertEquals('cancelled', $this->application->fresh()->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.permit-applications.update-status', $this->application->id), [
                'status' => 'invalid-status',
            ])
            ->assertSessionHasErrors(['status']);
    }

    public function test_admin_can_add_notes_to_application(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.permit-applications.add-notes', $this->application->id), [
                'notes' => 'Perlu verifikasi dokumen tambahan.',
            ])
            ->assertRedirect();

        $this->assertStringContainsString(
            'Perlu verifikasi dokumen tambahan.',
            $this->application->fresh()->admin_notes ?? ''
        );
    }
}
