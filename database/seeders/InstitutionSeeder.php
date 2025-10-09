<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'Dinas Lingkungan Hidup Provinsi',
                'type' => 'DLH',
                'address' => 'Jl. Lingkungan Hidup No. 1',
                'phone' => '021-1234567',
                'email' => 'dlh@provinsi.go.id',
                'contact_person' => 'Dr. Ahmad Hidayat',
                'contact_position' => 'Kepala Bidang Perizinan',
                'notes' => 'Untuk izin lingkungan hidup dan AMDAL',
            ],
            [
                'name' => 'Badan Pertanahan Nasional',
                'type' => 'BPN',
                'address' => 'Jl. Pertanahan No. 2',
                'phone' => '021-2345678',
                'email' => 'bpn@go.id',
                'contact_person' => 'Ir. Siti Nurhaliza',
                'contact_position' => 'Kepala Seksi Hak Tanah',
                'notes' => 'Untuk sertifikat tanah dan Pertek',
            ],
            [
                'name' => 'Online Single Submission (OSS)',
                'type' => 'OSS',
                'address' => 'Portal Digital',
                'phone' => '1500-033',
                'email' => 'support@oss.go.id',
                'contact_person' => 'Call Center OSS',
                'contact_position' => 'Customer Service',
                'notes' => 'Perizinan berusaha online',
            ],
            [
                'name' => 'Notaris Andri Wijaya, S.H., M.Kn',
                'type' => 'Notaris',
                'address' => 'Jl. Hukum No. 3, Jakarta',
                'phone' => '021-3456789',
                'email' => 'notaris.andri@example.com',
                'contact_person' => 'Andri Wijaya',
                'contact_position' => 'Notaris',
                'notes' => 'Untuk akta notaris dan pendirian badan usaha',
            ],
            [
                'name' => 'Dinas Perhubungan Provinsi',
                'type' => 'DISHUB',
                'address' => 'Jl. Transportasi No. 4',
                'phone' => '021-4567890',
                'email' => 'dishub@provinsi.go.id',
                'contact_person' => 'Drs. Bambang Sutrisno',
                'contact_position' => 'Kepala Bidang Lalu Lintas',
                'notes' => 'Untuk izin andalalin (analisis dampak lalu lintas)',
            ],
            [
                'name' => 'Dinas Kesehatan Provinsi',
                'type' => 'DINKES',
                'address' => 'Jl. Kesehatan No. 5',
                'phone' => '021-5678901',
                'email' => 'dinkes@provinsi.go.id',
                'contact_person' => 'dr. Maya Sari, M.Kes',
                'contact_position' => 'Kepala Bidang Perizinan Kesehatan',
                'notes' => 'Untuk izin rumah sakit dan fasilitas kesehatan',
            ]
        ];

        foreach ($institutions as $institution) {
            \DB::table('institutions')->insert(array_merge($institution, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
