<?php

namespace Database\Seeders;

use App\Models\SecuritySetting;
use Illuminate\Database\Seeder;

class SecuritySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SecuritySetting::query()->firstOrCreate([], [
            'min_password_length' => 8,
            'require_special_char' => true,
            'require_number' => true,
            'require_mixed_case' => true,
            'enforce_password_expiration' => false,
            'password_expiration_days' => 90,
            'session_timeout_minutes' => 30,
            'allow_concurrent_sessions' => true,
            'two_factor_enabled' => false,
            'activity_log_enabled' => true,
        ]);
    }
}
