<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'hadez',
                'full_name' => 'Hadez Administrator',
                'position' => 'System Administrator',
                'email' => 'hadez@bizmark.id',
                'phone' => '6283879602855',
                'role' => 'admin',
                'password' => 'T@n12089',
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Administrator utama sistem'
            ],
            [
                'name' => 'manager',
                'full_name' => 'Budi Santoso',
                'position' => 'Project Manager',
                'email' => 'manager@bizmark.id',
                'phone' => '0838796028551',
                'role' => 'admin',
                'password' => 'manager123',
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Manager proyek perizinan'
            ],
            [
                'name' => 'staff1',
                'full_name' => 'Siti Aminah',
                'position' => 'Konsultan Senior',
                'email' => 'siti@bizmark.id',
                'phone' => '0838796028552',
                'role' => 'staff',
                'password' => 'staff123',
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Konsultan perizinan lingkungan'
            ],
            [
                'name' => 'staff2',
                'full_name' => 'Ahmad Fadli',
                'position' => 'Konsultan Junior',
                'email' => 'ahmad@bizmark.id',
                'phone' => '0838796028553',
                'role' => 'staff',
                'password' => 'staff123',
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Konsultan perizinan lalu lintas'
            ],
            [
                'name' => 'staff3',
                'full_name' => 'Maya Dewi',
                'position' => 'Document Controller',
                'email' => 'maya@bizmark.id',
                'phone' => '0838796028554',
                'role' => 'staff',
                'password' => 'staff123',
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Pengendali dokumen dan administrasi'
            ]
        ];

        $roleIds = Role::pluck('id', 'name');
        $defaultRoleId = $roleIds['staff'] ?? $roleIds->first();

        foreach ($users as $user) {
            $roleName = $user['role'] ?? null;
            $roleId = $roleIds[$roleName] ?? $defaultRoleId;

            $payload = Arr::except($user, ['role']);
            $payload['role_id'] = $roleId;
            $payload['password'] = Hash::make($payload['password']);

            User::updateOrCreate(
                ['email' => $payload['email']],
                array_merge($payload, [
                    'name' => $payload['name'],
                    'updated_at' => now(),
                ])
            );
        }
    }
}
