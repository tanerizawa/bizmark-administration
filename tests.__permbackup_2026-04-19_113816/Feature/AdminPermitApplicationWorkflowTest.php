<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PermitApplication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermitApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_skip_status_transitions(): void
    {
        $permission = Permission::create([
            'name' => 'permits.manage',
            'display_name' => 'Manage Permit Applications',
            'group' => 'permits',
        ]);

        $role = Role::create([
            'name' => 'permit_manager',
            'display_name' => 'Permit Manager',
            'description' => null,
            'is_system' => false,
        ]);
        $role->grantPermission($permission);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $application = PermitApplication::factory()->create([
            'status' => 'submitted',
        ]);

        $this->actingAs($user)->post("/admin/permit-applications/{$application->id}/update-status", [
            'status' => 'completed',
        ])->assertStatus(422);
    }
}
