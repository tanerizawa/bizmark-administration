<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailAccount;
use App\Models\User;

class EmailAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailAccounts = [
            [
                'email' => 'cs@bizmark.id',
                'name' => 'Customer Service',
                'type' => 'shared',
                'department' => 'cs',
                'description' => 'General customer service inquiries and support',
                'is_active' => true,
                'auto_reply_enabled' => false,
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
                'signature' => "Best regards,\nSales Team\nBizmark.id",
            ],
            [
                'email' => 'cs@bizmark.id',
                'name' => 'Technical Support',
                'type' => 'shared',
                'department' => 'support',
                'description' => 'Technical support and troubleshooting',
                'is_active' => true,
                'auto_reply_enabled' => false,
                'signature' => "Best regards,\nTechnical Support Team\nBizmark.id",
            ],
            [
                'email' => 'cs@bizmark.id',
                'name' => 'General Information',
                'type' => 'shared',
                'department' => 'general',
                'description' => 'General information and inquiries',
                'is_active' => true,
                'auto_reply_enabled' => true,
                'auto_reply_message' => "Thank you for contacting Bizmark.id. We have received your message and will respond within 24 hours.",
                'signature' => "Best regards,\nBizmark.id Team",
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
