<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => bcrypt('T@n12089'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Administrator utama sistem'
            ],
            [
                'name' => 'manager',
                'full_name' => 'Budi Santoso',
                'position' => 'Project Manager',
                'email' => 'manager@bizmark.id',
                'phone' => '081234567891',
                'role' => 'admin',
                'password' => bcrypt('manager123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Manager proyek perizinan'
            ],
            [
                'name' => 'staff1',
                'full_name' => 'Siti Aminah',
                'position' => 'Konsultan Senior',
                'email' => 'siti@bizmark.id',
                'phone' => '081234567892',
                'role' => 'staff',
                'password' => bcrypt('staff123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Konsultan perizinan lingkungan'
            ],
            [
                'name' => 'staff2',
                'full_name' => 'Ahmad Fadli',
                'position' => 'Konsultan Junior',
                'email' => 'ahmad@bizmark.id',
                'phone' => '081234567893',
                'role' => 'staff',
                'password' => bcrypt('staff123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Konsultan perizinan lalu lintas'
            ],
            [
                'name' => 'staff3',
                'full_name' => 'Maya Dewi',
                'position' => 'Document Controller',
                'email' => 'maya@bizmark.id',
                'phone' => '081234567894',
                'role' => 'staff',
                'password' => bcrypt('staff123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'notes' => 'Pengendali dokumen dan administrasi'
            ]
        ];

        foreach ($users as $user) {
            \DB::table('users')->insert(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
