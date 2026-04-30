<?php

namespace Database\Seeders;

use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailAccounts = [
            [
                'email' => 'info@bizmark.id',
                'name' => 'Info - Bizmark',
                'type' => 'shared',
                'department' => 'general',
                'description' => 'Main inbox for general information and inquiries',
                'is_active' => true,
                'auto_reply_enabled' => true,
                'auto_reply_message' => 'Terima kasih telah menghubungi Bizmark.id. Kami telah menerima pesan Anda dan akan merespon dalam waktu 1x24 jam.',
                'signature' => "Best regards,\nBizmark.id Team",
            ],
            [
                'email' => 'cs@bizmark.id',
                'name' => 'Customer Service',
                'type' => 'shared',
                'department' => 'cs',
                'description' => 'General customer service inquiries and support',
                'is_active' => true,
                'auto_reply_enabled' => false,
                'auto_reply_message' => null,
                'signature' => "Best regards,\nCustomer Service Team\nBizmark.id",
            ],
            [
                'email' => 'sales@bizmark.id',
                'name' => 'Sales Team',
                'type' => 'shared',
                'department' => 'sales',
                'description' => 'Sales inquiries, quotes, and partnerships',
                'is_active' => true,
                'auto_reply_enabled' => false,
                'auto_reply_message' => null,
                'signature' => "Best regards,\nSales Team\nBizmark.id",
            ],
        ];

        foreach ($emailAccounts as $accountData) {
            $account = EmailAccount::updateOrCreate(
                ['email' => $accountData['email']],
                $accountData
            );

            $this->command->info("Created/Updated: {$account->email}");

            // Assign to first user (admin) if exists
            $adminUser = User::first();

            if ($adminUser) {
                $account->assignUser($adminUser, [
                    'role' => 'primary',
                    'can_send' => true,
                    'can_receive' => true,
                    'can_delete' => true,
                    'can_assign_others' => true,
                ]);
                $this->command->info("  └─ Assigned to user: {$adminUser->email}");
            }
        }

        $this->command->info('Email accounts seeded successfully!');
    }
}
