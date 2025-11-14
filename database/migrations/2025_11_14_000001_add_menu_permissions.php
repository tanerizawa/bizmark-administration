<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Recruitment Group
            ['name' => 'recruitment.view_jobs', 'display_name' => 'View Jobs', 'group' => 'recruitment', 'description' => 'Can view job listings'],
            ['name' => 'recruitment.manage_jobs', 'display_name' => 'Manage Jobs', 'group' => 'recruitment', 'description' => 'Can create, edit, and delete jobs'],
            ['name' => 'recruitment.view_applications', 'display_name' => 'View Applications', 'group' => 'recruitment', 'description' => 'Can view job applications'],
            ['name' => 'recruitment.process_applications', 'display_name' => 'Process Applications', 'group' => 'recruitment', 'description' => 'Can review and process applications'],
            
            // Email Group
            ['name' => 'email.view_inbox', 'display_name' => 'View Inbox', 'group' => 'email', 'description' => 'Can view email inbox'],
            ['name' => 'email.send_email', 'display_name' => 'Send Email', 'group' => 'email', 'description' => 'Can send emails'],
            ['name' => 'email.manage_accounts', 'display_name' => 'Manage Email Accounts', 'group' => 'email', 'description' => 'Can manage email accounts'],
            ['name' => 'email.manage_campaigns', 'display_name' => 'Manage Campaigns', 'group' => 'email', 'description' => 'Can create and manage email campaigns'],
            ['name' => 'email.manage_subscribers', 'display_name' => 'Manage Subscribers', 'group' => 'email', 'description' => 'Can manage email subscribers'],
            ['name' => 'email.manage_templates', 'display_name' => 'Manage Templates', 'group' => 'email', 'description' => 'Can manage email templates'],
            ['name' => 'email.manage_settings', 'display_name' => 'Manage Email Settings', 'group' => 'email', 'description' => 'Can configure email settings'],
            
            // Institutions Group
            ['name' => 'institutions.view', 'display_name' => 'View Institutions', 'group' => 'institutions', 'description' => 'Can view institutions'],
            ['name' => 'institutions.create', 'display_name' => 'Create Institutions', 'group' => 'institutions', 'description' => 'Can create new institutions'],
            ['name' => 'institutions.edit', 'display_name' => 'Edit Institutions', 'group' => 'institutions', 'description' => 'Can edit institutions'],
            ['name' => 'institutions.delete', 'display_name' => 'Delete Institutions', 'group' => 'institutions', 'description' => 'Can delete institutions'],
            
            // Master Data Group
            ['name' => 'master_data.view', 'display_name' => 'View Master Data', 'group' => 'master_data', 'description' => 'Can view master data'],
            ['name' => 'master_data.edit_permit_types', 'display_name' => 'Edit Permit Types', 'group' => 'master_data', 'description' => 'Can edit permit types'],
            ['name' => 'master_data.edit_permit_templates', 'display_name' => 'Edit Permit Templates', 'group' => 'master_data', 'description' => 'Can edit permit templates'],
            
            // Content Group
            ['name' => 'content.view_articles', 'display_name' => 'View Articles', 'group' => 'content', 'description' => 'Can view articles'],
            ['name' => 'content.create_articles', 'display_name' => 'Create Articles', 'group' => 'content', 'description' => 'Can create new articles'],
            ['name' => 'content.edit_articles', 'display_name' => 'Edit Articles', 'group' => 'content', 'description' => 'Can edit articles'],
            ['name' => 'content.delete_articles', 'display_name' => 'Delete Articles', 'group' => 'content', 'description' => 'Can delete articles'],
            ['name' => 'content.publish_articles', 'display_name' => 'Publish Articles', 'group' => 'content', 'description' => 'Can publish articles'],
        ];
        
        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
            
            if (!$exists) {
                DB::table('permissions')->insert(array_merge($permission, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('group', [
            'recruitment',
            'email',
            'institutions',
            'master_data',
            'content'
        ])->delete();
    }
};
