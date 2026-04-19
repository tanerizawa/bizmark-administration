<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AdminPaymentAuthorizationTest extends TestCase
{
    public function test_admin_payments_routes_require_finance_permission(): void
    {
        $role = Role::create([
            'name' => 'no_finance',
            'display_name' => 'No Finance',
            'description' => null,
            'is_system' => false,
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $payment = Payment::factory()->create();

        $this->actingAs($user)->get("/admin/payments/{$payment->id}")->assertStatus(403);
        $this->actingAs($user)->get("/admin/payments/{$payment->id}/proof")->assertStatus(403);
    }
}

