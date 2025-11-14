<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles
        $admin = Role::where('name', 'admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $accountant = Role::where('name', 'accountant')->first();
        $staff = Role::where('name', 'staff')->first();
        $viewer = Role::where('name', 'viewer')->first();

        // ADMIN - Full Access to Everything
        if ($admin) {
            $allPermissions = Permission::all()->pluck('id')->toArray();
            $admin->permissions()->sync($allPermissions);
        }

        // MANAGER - Project, Team, Recruitment, Email, Content Management
        if ($manager) {
            $managerPermissions = Permission::whereIn('name', [
                // Projects
                'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
                
                // Clients
                'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
                
                // Tasks
                'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete', 'tasks.assign',
                
                // Documents
                'documents.view', 'documents.upload', 'documents.delete',
                
                // Institutions (view only)
                'institutions.view',
                
                // Users (limited - no role management)
                'users.view', 'users.create', 'users.edit',
                
                // Recruitment (full access)
                'recruitment.view_jobs', 'recruitment.manage_jobs',
                'recruitment.view_applications', 'recruitment.process_applications',
                
                // Email (full access)
                'email.view_inbox', 'email.send_email', 'email.manage_accounts',
                'email.manage_campaigns', 'email.manage_subscribers',
                'email.manage_templates', 'email.manage_settings',
                
                // Master Data (view only)
                'master_data.view',
                
                // Content (full access)
                'content.view_articles', 'content.create_articles',
                'content.edit_articles', 'content.delete_articles', 'content.publish_articles',
            ])->pluck('id')->toArray();
            
            $manager->permissions()->sync($managerPermissions);
        }

        // ACCOUNTANT - Finance Focus
        if ($accountant) {
            $accountantPermissions = Permission::whereIn('name', [
                // Projects (view only - for context)
                'projects.view',
                
                // Clients (view only - for invoicing)
                'clients.view',
                
                // Invoices (full access)
                'invoices.view', 'invoices.create', 'invoices.edit', 
                'invoices.delete', 'invoices.approve',
                
                // Finances (full access)
                'finances.view', 'finances.manage_payments', 'finances.manage_expenses',
                'finances.manage_accounts', 'finances.view_reports',
                
                // Master Data (view + cash accounts)
                'master_data.view',
            ])->pluck('id')->toArray();
            
            $accountant->permissions()->sync($accountantPermissions);
        }

        // STAFF - Operational
        if ($staff) {
            $staffPermissions = Permission::whereIn('name', [
                // Projects (view + contribute)
                'projects.view',
                
                // Tasks (view + create)
                'tasks.view', 'tasks.create',
                
                // Documents (view + upload)
                'documents.view', 'documents.upload',
                
                // Institutions (view only)
                'institutions.view',
                
                // Clients (view only)
                'clients.view',
            ])->pluck('id')->toArray();
            
            $staff->permissions()->sync($staffPermissions);
        }

        // VIEWER - Read-Only Access
        if ($viewer) {
            $viewerPermissions = Permission::whereIn('name', [
                // Projects (view only)
                'projects.view',
                
                // Tasks (view only)
                'tasks.view',
                
                // Documents (view only)
                'documents.view',
                
                // Institutions (view only)
                'institutions.view',
                
                // Clients (view only)
                'clients.view',
            ])->pluck('id')->toArray();
            
            $viewer->permissions()->sync($viewerPermissions);
        }

        $this->command->info('Role permissions assigned successfully!');
        $this->command->info('Admin: ' . ($admin ? $admin->permissions()->count() : 0) . ' permissions');
        $this->command->info('Manager: ' . ($manager ? $manager->permissions()->count() : 0) . ' permissions');
        $this->command->info('Accountant: ' . ($accountant ? $accountant->permissions()->count() : 0) . ' permissions');
        $this->command->info('Staff: ' . ($staff ? $staff->permissions()->count() : 0) . ' permissions');
        $this->command->info('Viewer: ' . ($viewer ? $viewer->permissions()->count() : 0) . ' permissions');
    }
}
