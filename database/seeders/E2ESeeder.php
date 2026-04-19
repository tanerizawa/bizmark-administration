<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\PermitType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2ESeeder extends Seeder
{
    public function run(): void
    {
        PermitType::query()->updateOrCreate(
            ['code' => 'e2e-permit-type'],
            ['name' => 'E2E Permit Type', 'category' => 'business', 'is_active' => true]
        );

        Client::query()->updateOrCreate(['email' => 'e2e-client@example.com'], [
            'name' => 'E2E Client',
            'company_name' => 'E2E Client Company',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $adminRole = Role::query()->where('name', 'admin')->first();
        if (! $adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access',
                'is_system' => true,
            ]);
        }

        User::query()->updateOrCreate(['email' => 'e2e-admin@example.com'], [
            'name' => 'E2E Admin',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }
}
