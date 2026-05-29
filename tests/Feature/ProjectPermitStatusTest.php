<?php

namespace Tests\Feature;

use App\Models\PermitType;
use App\Models\Project;
use App\Models\ProjectPermit;
use App\Models\ProjectPermitDependency;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Modules\Perizinan\Controllers\Public\ProjectPermitController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Tests\TestCase;

class ProjectPermitStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear route cache so dynamic test routes can be registered
        if (app()->routesAreCached()) {
            \Illuminate\Support\Facades\Artisan::call('route:clear');
        }

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->app['router']->post('testing/projects/{project}/permits', [ProjectPermitController::class, 'store'])
            ->middleware(SubstituteBindings::class)
            ->name('testing.projects.permits.store');

        $this->app['router']->patch('testing/permits/{permit}/status', [ProjectPermitController::class, 'updateStatus'])
            ->middleware(SubstituteBindings::class)
            ->name('testing.permits.update-status');

        $routes = $this->app['router']->getRoutes();
        $routes->refreshNameLookups();
        $routes->refreshActionLookups();
    }

    public function test_store_persists_uppercase_status(): void
    {
        [$project, $permitType, $user] = $this->prepareProjectContext();

        $response = $this->actingAs($user)->post(route('testing.projects.permits.store', $project), [
            'permit_type_id' => $permitType->id,
            'is_goal_permit' => true,
            'notes' => 'Catatan pengujian',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('project_permits', [
            'project_id' => $project->id,
            'status' => ProjectPermit::STATUS_NOT_STARTED,
        ]);
    }

    public function test_can_start_helper_respects_uppercase_statuses(): void
    {
        [$project, $permitType, $user] = $this->prepareProjectContext();

        $parentPermit = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitType->id,
            'sequence_order' => 1,
            'is_goal_permit' => false,
            'status' => ProjectPermit::STATUS_NOT_STARTED,
        ]);

        $childPermit = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitType->id,
            'sequence_order' => 2,
            'is_goal_permit' => false,
            'status' => ProjectPermit::STATUS_NOT_STARTED,
        ]);

        ProjectPermitDependency::create([
            'project_permit_id' => $childPermit->id,
            'depends_on_permit_id' => $parentPermit->id,
            'dependency_type' => ProjectPermitDependency::TYPE_MANDATORY,
            'can_proceed_without' => false,
        ]);

        $this->assertFalse($childPermit->fresh()->canStart());

        $this->actingAs($user)->patch(route('testing.permits.update-status', $parentPermit), [
            'status' => ProjectPermit::STATUS_APPROVED,
        ])->assertRedirect();

        $this->assertDatabaseHas('project_permits', [
            'id' => $parentPermit->id,
            'status' => ProjectPermit::STATUS_APPROVED,
        ]);

        $this->assertTrue($childPermit->fresh()->canStart());

        $this->actingAs($user)->patch(route('testing.permits.update-status', $childPermit), [
            'status' => ProjectPermit::STATUS_IN_PROGRESS,
        ])->assertRedirect();

        $this->assertDatabaseHas('project_permits', [
            'id' => $childPermit->id,
            'status' => ProjectPermit::STATUS_IN_PROGRESS,
        ]);

        $this->assertFalse($childPermit->fresh()->canStart());
    }

    public function test_override_records_metadata_and_persists_values(): void
    {
        [$project, $permitType, $user] = $this->prepareProjectContext();

        $parentPermit = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitType->id,
            'sequence_order' => 1,
            'is_goal_permit' => false,
            'status' => ProjectPermit::STATUS_NOT_STARTED,
        ]);

        $childPermit = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitType->id,
            'sequence_order' => 2,
            'is_goal_permit' => false,
            'status' => ProjectPermit::STATUS_NOT_STARTED,
        ]);

        ProjectPermitDependency::create([
            'project_permit_id' => $childPermit->id,
            'depends_on_permit_id' => $parentPermit->id,
            'dependency_type' => ProjectPermitDependency::TYPE_MANDATORY,
            'can_proceed_without' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('testing.permits.update-status', $childPermit), [
            'status' => ProjectPermit::STATUS_IN_PROGRESS,
            'override_reason' => 'Melanjutkan meski belum lengkap',
        ]);

        $response->assertRedirect();

        $updatedPermit = $childPermit->fresh();

        $this->assertSame(ProjectPermit::STATUS_IN_PROGRESS, $updatedPermit->status);
        $this->assertTrue($updatedPermit->override_dependencies);
        $this->assertSame('Melanjutkan meski belum lengkap', $updatedPermit->override_reason);
        $this->assertSame($user->id, $updatedPermit->override_by_user_id);
        $this->assertNotNull($updatedPermit->override_at);
    }

    private function prepareProjectContext(): array
    {
        $user = User::factory()->create();

        $projectStatus = ProjectStatus::create([
            'name' => 'In Progress',
            'code' => 'IN_PROGRESS',
        ]);

        $project = Project::forceCreate([
            'name' => 'Proyek Pengujian',
            'client_name' => 'Klien Uji',
            'client_contact' => '081234567890',
            'status_id' => $projectStatus->id,
        ]);

        $permitType = PermitType::create([
            'name' => 'Izin Lingkungan',
            'code' => 'ENV-001',
            'category' => 'environmental',
            'is_active' => true,
        ]);

        return [$project, $permitType, $user];
    }
}
