<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => env('SEED_ADMIN_USERNAME', 'admin'),
                'full_name' => env('SEED_ADMIN_FULLNAME', 'System Administrator'),
                'position' => 'System Administrator',
                'email' => env('SEED_ADMIN_EMAIL', 'admin@bizmark.id'),
                'phone' => env('SEED_ADMIN_PHONE', ''),
                'role' => 'admin',
                'password' => env('SEED_ADMIN_PASSWORD'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Administrator utama sistem',
                'department' => 'general',
            ],
            [
                'name' => 'manager',
                'full_name' => 'Project Manager',
                'position' => 'Project Manager',
                'email' => env('SEED_MANAGER_EMAIL', 'manager@bizmark.id'),
                'phone' => '',
                'role' => 'admin',
                'password' => env('SEED_MANAGER_PASSWORD'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Manager proyek perizinan',
                'department' => 'general',
            ],
            [
                'name' => 'staff1',
                'full_name' => 'Staff Senior',
                'position' => 'Konsultan Senior',
                'email' => env('SEED_STAFF1_EMAIL', 'staff1@bizmark.id'),
                'phone' => '',
                'role' => 'staff',
                'password' => env('SEED_STAFF1_PASSWORD'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Konsultan perizinan lingkungan',
                'department' => 'technical',
            ],
            [
                'name' => 'staff2',
                'full_name' => 'Staff Junior',
                'position' => 'Konsultan Junior',
                'email' => env('SEED_STAFF2_EMAIL', 'staff2@bizmark.id'),
                'phone' => '',
                'role' => 'staff',
                'password' => env('SEED_STAFF2_PASSWORD'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Konsultan perizinan lalu lintas',
                'department' => 'technical',
            ],
            [
                'name' => 'staff3',
                'full_name' => 'Document Controller',
                'position' => 'Document Controller',
                'email' => env('SEED_STAFF3_EMAIL', 'staff3@bizmark.id'),
                'phone' => '',
                'role' => 'staff',
                'password' => env('SEED_STAFF3_PASSWORD'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Pengendali dokumen dan administrasi',
                'department' => 'support',
            ],
        ];

        $roleIds = Role::pluck('id', 'name');
        $defaultRoleId = $roleIds['staff'] ?? $roleIds->first();

        foreach ($users as $user) {
            $roleName = $user['role'] ?? null;
            $roleId = $roleIds[$roleName] ?? $defaultRoleId;

            // Kalau password tidak di-set via .env, generate random 24-char
            // dan cetak ke console. Jangan pernah hardcode credential di seeder.
            $plainPassword = $user['password'];
            $generatedRandom = false;
            if (empty($plainPassword)) {
                $plainPassword = Str::random(24);
                $generatedRandom = true;
            }

            $payload = Arr::except($user, ['role']);
            $payload['role_id'] = $roleId;
            $payload['password'] = Hash::make($plainPassword);

            $existed = User::where('email', $payload['email'])->exists();

            User::updateOrCreate(
                ['email' => $payload['email']],
                array_merge($payload, [
                    'name' => $payload['name'],
                    'updated_at' => now(),
                ])
            );

            if ($generatedRandom && ! $existed) {
                // Hanya cetak untuk user yang baru dibuat supaya tidak bocor
                // kalau seeder di-rerun di environment lain.
                $this->command?->warn(sprintf(
                    '[UserSeeder] %s password di-generate: %s (simpan sekarang, tidak akan ditampilkan lagi)',
                    $payload['email'],
                    $plainPassword
                ));
            }
        }
    }
}
