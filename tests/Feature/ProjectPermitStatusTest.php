<?php

namespace Tests\Feature;

use App\Http\Controllers\ProjectPermitController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\PermitType;
use App\Models\Project;
use App\Models\ProjectPermit;
use App\Models\ProjectPermitDependency;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProjectPermitStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);

        Route::post('testing/projects/{project}/permits', [ProjectPermitController::class, 'store'])
            ->name('testing.projects.permits.store');

        Route::patch('testing/permits/{permit}/status', [ProjectPermitController::class, 'updateStatus'])
            ->name('testing.permits.update-status');
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

    private function prepareProjectContext(): array
    {
        $user = User::factory()->create();

        $projectStatus = ProjectStatus::create([
            'name' => 'In Progress',
            'code' => 'IN_PROGRESS',
        ]);

        $project = Project::forceCreate([
            'code' => 'PRJ-001',
            'name' => 'Proyek Pengujian',
            'client_name' => 'Klien Uji',
            'permit_type' => 'environmental',
            'current_status_id' => $projectStatus->id,
            'is_urgent' => false,
            'is_archived' => false,
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
