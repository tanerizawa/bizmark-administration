<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Projects
            ['name' => 'projects.view', 'display_name' => 'View Projects', 'group' => 'projects'],
            ['name' => 'projects.create', 'display_name' => 'Create Projects', 'group' => 'projects'],
            ['name' => 'projects.edit', 'display_name' => 'Edit Projects', 'group' => 'projects'],
            ['name' => 'projects.delete', 'display_name' => 'Delete Projects', 'group' => 'projects'],
            
            // Clients
            ['name' => 'clients.view', 'display_name' => 'View Clients', 'group' => 'clients'],
            ['name' => 'clients.create', 'display_name' => 'Create Clients', 'group' => 'clients'],
            ['name' => 'clients.edit', 'display_name' => 'Edit Clients', 'group' => 'clients'],
            ['name' => 'clients.delete', 'display_name' => 'Delete Clients', 'group' => 'clients'],
            
            // Invoices
            ['name' => 'invoices.view', 'display_name' => 'View Invoices', 'group' => 'invoices'],
            ['name' => 'invoices.create', 'display_name' => 'Create Invoices', 'group' => 'invoices'],
            ['name' => 'invoices.edit', 'display_name' => 'Edit Invoices', 'group' => 'invoices'],
            ['name' => 'invoices.delete', 'display_name' => 'Delete Invoices', 'group' => 'invoices'],
            ['name' => 'invoices.approve', 'display_name' => 'Approve Invoices', 'group' => 'invoices'],
            
            // Finances
            ['name' => 'finances.view', 'display_name' => 'View Finances', 'group' => 'finances'],
            ['name' => 'finances.manage_payments', 'display_name' => 'Manage Payments', 'group' => 'finances'],
            ['name' => 'finances.manage_expenses', 'display_name' => 'Manage Expenses', 'group' => 'finances'],
            ['name' => 'finances.manage_accounts', 'display_name' => 'Manage Cash Accounts', 'group' => 'finances'],
            ['name' => 'finances.view_reports', 'display_name' => 'View Financial Reports', 'group' => 'finances'],
            
            // Tasks
            ['name' => 'tasks.view', 'display_name' => 'View Tasks', 'group' => 'tasks'],
            ['name' => 'tasks.create', 'display_name' => 'Create Tasks', 'group' => 'tasks'],
            ['name' => 'tasks.edit', 'display_name' => 'Edit Tasks', 'group' => 'tasks'],
            ['name' => 'tasks.delete', 'display_name' => 'Delete Tasks', 'group' => 'tasks'],
            ['name' => 'tasks.assign', 'display_name' => 'Assign Tasks', 'group' => 'tasks'],
            
            // Documents
            ['name' => 'documents.view', 'display_name' => 'View Documents', 'group' => 'documents'],
            ['name' => 'documents.upload', 'display_name' => 'Upload Documents', 'group' => 'documents'],
            ['name' => 'documents.delete', 'display_name' => 'Delete Documents', 'group' => 'documents'],
            
            // Users & Settings
            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'group' => 'users'],
            ['name' => 'settings.manage', 'display_name' => 'Manage Settings', 'group' => 'settings'],
            ['name' => 'roles.manage', 'display_name' => 'Manage Roles', 'group' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Full access to all features',
            'is_system' => true,
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager'], [
            'display_name' => 'Manager',
            'description' => 'Manage projects, clients, and team members',
            'is_system' => true,
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant'], [
            'display_name' => 'Accountant',
            'description' => 'Manage finances, invoices, and payments',
            'is_system' => true,
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff'], [
            'display_name' => 'Staff',
            'description' => 'Basic access to projects and tasks',
            'is_system' => true,
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer'], [
            'display_name' => 'Viewer',
            'description' => 'Read-only access',
            'is_system' => true,
        ]);

        // Assign Permissions to Roles
        
        // Admin: All permissions
        $admin->permissions()->sync(Permission::all());

        // Manager: Most permissions except sensitive settings
        $managerPermissions = Permission::whereIn('group', [
            'projects', 'clients', 'invoices', 'tasks', 'documents'
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);
        $manager->grantPermission('finances.view');
        $manager->grantPermission('finances.view_reports');

        // Accountant: Finance and invoice focused
        $accountantPermissions = Permission::whereIn('group', [
            'invoices', 'finances'
        ])->pluck('id');
        $accountant->permissions()->sync($accountantPermissions);
        $accountant->grantPermission('projects.view');
        $accountant->grantPermission('clients.view');

        // Staff: Basic access
        $staffPermissions = [
            'projects.view',
            'tasks.view',
            'tasks.edit',
            'documents.view',
            'documents.upload',
        ];
        foreach ($staffPermissions as $perm) {
            $staff->grantPermission($perm);
        }

        // Viewer: Read-only
        $viewerPermissions = Permission::where('name', 'LIKE', '%.view')->pluck('id');
        $viewer->permissions()->sync($viewerPermissions);
    }
}
