<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessSetting::query()->firstOrCreate([], [
            'company_name' => 'Bizmark.ID',
            'company_email' => 'support@bizmark.id',
            'company_phone' => '0812-1234-5678',
            'company_website' => 'https://bizmark.id',
            'company_address' => 'Jl. Contoh No. 123, Jakarta',
            'maintenance_mode' => false,
            'email_notifications' => true,
        ]);
    }
}
