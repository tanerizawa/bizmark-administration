<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use Carbon\Carbon;

class MigrateClientsFromProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data klien berdasarkan projects yang sudah ada
        $clients = [
            [
                'name' => 'PT RINDU ALAM SEJAHTERA',
                'company_name' => 'PT RINDU ALAM SEJAHTERA',
                'client_type' => 'company',
                'industry' => 'Lingkungan & Limbah B3',
                'email' => 'info@ras.co.id',
                'phone' => '0267-8461234',
                'mobile' => '6283879602855',
                'address' => 'Karawang, Jawa Barat',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'postal_code' => '41361',
                'contact_person' => 'Bapak Hendra',
                'status' => 'active',
                'notes' => 'Klien rutin untuk proyek lingkungan, Kartu Pengawasan, dan pengelolaan Limbah B3',
            ],
            [
                'name' => 'PT PUTRA JAYA LAKSANA',
                'company_name' => 'PT PUTRA JAYA LAKSANA',
                'client_type' => 'company',
                'industry' => 'Lingkungan & Konsultan',
                'email' => 'info@putrajayalaksana.co.id',
                'phone' => '0267-8462345',
                'mobile' => '0838796028551',
                'address' => 'Karawang, Jawa Barat',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'postal_code' => '41361',
                'contact_person' => 'Ibu Siti',
                'status' => 'active',
                'notes' => 'Proyek UKL-UPL (Upaya Kelayakan Lingkungan & Upaya Pemantauan Lingkungan)',
            ],
            [
                'name' => 'PT ASIACON',
                'company_name' => 'PT ASIACON',
                'client_type' => 'company',
                'industry' => 'Konstruksi & Lingkungan',
                'email' => 'contact@asiacon.co.id',
                'phone' => '0267-8463456',
                'mobile' => '0838796028552',
                'address' => 'Karawang, Jawa Barat',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'postal_code' => '41361',
                'contact_person' => 'Bapak Agus',
                'status' => 'active',
                'notes' => 'Proyek UKL-UPL dengan nilai kontrak besar',
            ],
            [
                'name' => 'PT MAULIDA',
                'company_name' => 'PT MAULIDA',
                'client_type' => 'company',
                'industry' => 'Manufaktur & Lingkungan',
                'email' => 'maulida@company.co.id',
                'phone' => '0267-8464567',
                'mobile' => '0838796028553',
                'address' => 'Karawang, Jawa Barat',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'postal_code' => '41361',
                'contact_person' => 'Ibu Maulida',
                'status' => 'active',
                'notes' => 'Proyek UKL-UPL',
            ],
            [
                'name' => 'PT MEGA CORPORINDO MANDIRI',
                'company_name' => 'PT MEGA CORPORINDO MANDIRI',
                'client_type' => 'company',
                'industry' => 'Trading & Manufaktur',
                'email' => 'info@megacorporindo.co.id',
                'phone' => '0267-8465678',
                'mobile' => '0838796028554',
                'address' => 'Karawang, Jawa Barat',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'postal_code' => '41361',
                'contact_person' => 'Bapak Susanto',
                'status' => 'active',
                'notes' => 'Proyek UKL-UPL',
            ],
            [
                'name' => 'PT NUSANTARA GRUP',
                'company_name' => 'PT NUSANTARA GRUP',
                'client_type' => 'company',
                'industry' => 'Teknologi & Sistem Informasi',
                'email' => 'contact@nusantaragrup.co.id',
                'phone' => '0267-8466789',
                'mobile' => '0838796028555',
                'address' => 'Karawang, Jawa Barat',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'postal_code' => '41361',
                'contact_person' => 'Bapak Darmawan',
                'status' => 'active',
                'notes' => 'Proyek pembuatan sistem administrasi',
            ],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create($clientData);
            
            // Update projects table untuk link ke client_id
            DB::table('projects')
                ->where('client_name', $clientData['company_name'])
                ->update([
                    'client_id' => $client->id,
                    'updated_at' => Carbon::now(),
                ]);
            
            $this->command->info("✓ Created client: {$clientData['name']} (ID: {$client->id})");
        }

        $totalProjects = DB::table('projects')->whereNotNull('client_id')->count();
        
        $this->command->info("\n" . str_repeat('=', 60));
        $this->command->info("Migration Summary:");
        $this->command->info("- Total clients created: " . count($clients));
        $this->command->info("- Total projects linked: {$totalProjects}");
        $this->command->info(str_repeat('=', 60));
    }
}
