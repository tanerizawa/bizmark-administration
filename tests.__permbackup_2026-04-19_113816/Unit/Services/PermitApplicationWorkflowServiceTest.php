<?php

namespace Tests\Unit\Services;

use App\Models\PermitApplication;
use App\Services\PermitApplicationWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PermitApplicationWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_assert_can_transition_allows_valid_transition(): void
    {
        $service = app(PermitApplicationWorkflowService::class);
        $service->assertCanTransition('draft', 'submitted');

        $this->assertTrue(true);
    }

    public function test_assert_can_transition_rejects_invalid_transition(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Invalid status transition');

        $service = app(PermitApplicationWorkflowService::class);
        $service->assertCanTransition('draft', 'completed');
    }

    public function test_transition_updates_status_and_creates_status_log(): void
    {
        $application = PermitApplication::factory()->create([
            'status' => 'draft',
        ]);

        $service = app(PermitApplicationWorkflowService::class);
        $service->transition($application, 'submitted', 'Submitted for review', 'client', 123);

        $application->refresh();
        $this->assertSame('submitted', $application->status);

        $this->assertDatabaseHas('application_status_logs', [
            'application_id' => $application->id,
            'from_status' => 'draft',
            'to_status' => 'submitted',
            'changed_by_type' => 'client',
            'changed_by_id' => 123,
        ]);
    }
}
