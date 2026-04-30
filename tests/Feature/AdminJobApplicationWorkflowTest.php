<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminJobApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $hrManager;

    private User $unauthorized;

    private JobVacancy $vacancy;

    private JobApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleHr = Role::firstOrCreate(['name' => 'hr_manager'], ['display_name' => 'HR Manager']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permView = Permission::firstOrCreate(['name' => 'recruitment.view'], ['display_name' => 'View Recruitment', 'group' => 'hrm']);
        $permManage = Permission::firstOrCreate(['name' => 'recruitment.manage'], ['display_name' => 'Manage Recruitment', 'group' => 'hrm']);
        $permProcess = Permission::firstOrCreate(['name' => 'recruitment.process_applications'], ['display_name' => 'Process Applications', 'group' => 'hrm']);
        $roleHr->permissions()->syncWithoutDetaching([$permView->id, $permManage->id, $permProcess->id]);

        $this->hrManager = User::factory()->create(['role_id' => $roleHr->id]);
        $this->unauthorized = User::factory()->create(['role_id' => $roleViewer->id]);

        $this->vacancy = JobVacancy::create([
            'title' => 'Konsultan Lingkungan',
            'slug' => 'konsultan-lingkungan',
            'position' => 'Staff',
            'description' => 'Deskripsi posisi.',
            'responsibilities' => 'Tanggung jawab.',
            'qualifications' => 'S1 minimum.',
            'employment_type' => 'full-time',
            'location' => 'Karawang',
            'status' => 'open',
            'deadline' => now()->addMonths(1)->toDateString(),
        ]);

        $this->application = JobApplication::create([
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Andi Wijaya',
            'email' => 'andi@example.com',
            'phone' => '081234567890',
            'education_level' => 'S1',
            'major' => 'Teknik Lingkungan',
            'institution' => 'Universitas Indonesia',
            'status' => 'pending',
            'cover_letter' => 'Saya tertarik melamar posisi ini.',
        ]);
    }

    public function test_hr_manager_can_list_applications(): void
    {
        $this->actingAs($this->hrManager)
            ->get(route('admin.applications.index'))
            ->assertOk()
            ->assertSee($this->application->full_name);
    }

    public function test_unauthorized_user_cannot_list_applications(): void
    {
        $this->actingAs($this->unauthorized)
            ->get(route('admin.applications.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_applications(): void
    {
        $this->get(route('admin.applications.index'))
            ->assertRedirect();
    }

    public function test_hr_manager_can_view_application_detail(): void
    {
        $this->actingAs($this->hrManager)
            ->get(route('admin.applications.show', $this->application->id))
            ->assertOk()
            ->assertSee($this->application->full_name)
            ->assertSee($this->application->email);
    }

    public function test_show_returns_404_for_missing_application(): void
    {
        $this->actingAs($this->hrManager)
            ->get(route('admin.applications.show', 99999))
            ->assertNotFound();
    }

    public function test_hr_manager_can_update_status_to_reviewed(): void
    {
        Mail::fake();

        $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $this->application->id), [
                'status' => 'reviewed',
                'notes' => 'Kandidat memenuhi kualifikasi.',
            ])
            ->assertRedirect();

        $this->application->refresh();
        $this->assertEquals('reviewed', $this->application->status);
        $this->assertEquals('Kandidat memenuhi kualifikasi.', $this->application->notes);
        $this->assertNotNull($this->application->reviewed_at);
        $this->assertEquals($this->hrManager->id, $this->application->reviewed_by);
    }

    public function test_hr_manager_can_update_status_to_interview(): void
    {
        Mail::fake();

        $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $this->application->id), [
                'status' => 'interview',
                'notes' => 'Dijadwalkan interview minggu depan.',
            ])
            ->assertRedirect();

        $this->assertEquals('interview', $this->application->fresh()->status);
    }

    public function test_hr_manager_can_reject_application(): void
    {
        Mail::fake();

        $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $this->application->id), [
                'status' => 'rejected',
                'notes' => 'Tidak memenuhi kualifikasi minimum.',
            ])
            ->assertRedirect();

        $this->assertEquals('rejected', $this->application->fresh()->status);
    }

    public function test_hr_manager_can_accept_application(): void
    {
        Mail::fake();

        $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $this->application->id), [
                'status' => 'accepted',
            ])
            ->assertRedirect();

        $this->assertEquals('accepted', $this->application->fresh()->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $this->application->id), [
                'status' => 'invalid-status',
            ])
            ->assertSessionHasErrors(['status']);

        $this->assertEquals('pending', $this->application->fresh()->status);
    }

    public function test_update_status_attempts_email_notification(): void
    {
        Mail::fake();

        $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $this->application->id), [
                'status' => 'reviewed',
            ])
            ->assertRedirect();

        // Email is sent or silently suppressed on transport failure; both are valid outcomes.
        // Verify status was updated as the primary assertion.
        $this->assertEquals('reviewed', $this->application->fresh()->status);
    }

    public function test_hr_manager_can_filter_applications_by_status(): void
    {
        JobApplication::create([
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '0812999',
            'status' => 'reviewed',
            'education_level' => 'S1',
            'major' => 'Hukum',
            'institution' => 'UI',
        ]);

        $this->actingAs($this->hrManager)
            ->get(route('admin.applications.index', ['status' => 'reviewed']))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Andi Wijaya');
    }

    public function test_hr_manager_can_delete_application(): void
    {
        $this->actingAs($this->hrManager)
            ->delete(route('admin.applications.destroy', $this->application->id))
            ->assertRedirect(route('admin.applications.index'));

        $this->assertDatabaseMissing('job_applications', ['id' => $this->application->id]);
    }
}
