<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AdminClientsAuthorizationTest extends TestCase
{
    public function test_admin_clients_routes_require_clients_permission(): void
    {
        $role = Role::create([
            'name' => 'no_clients',
            'display_name' => 'No Clients',
            'description' => null,
            'is_system' => false,
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $client = Client::factory()->create();

        $this->actingAs($user)->get('/admin/clients')->assertStatus(403);
        $this->actingAs($user)->get('/admin/clients/create')->assertStatus(403);
        $this->actingAs($user)->get("/admin/clients/{$client->id}")->assertStatus(403);
        $this->actingAs($user)->get("/admin/clients/{$client->id}/edit")->assertStatus(403);
    }
}

