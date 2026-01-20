<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds for roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Define comprehensive permissions for Mekodonia charity system
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.impersonate',
            'users.manage',

            // Staff Management
            'staff.view',
            'staff.create',
            'staff.update',
            'staff.delete',
            'staff.manage',

            // Role & Permission Management
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.manage',
            'permissions.view',
            'permissions.manage',

            // Branch Management
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',
            'branches.manage',

            // Elder Management
            'elders.view',
            'elders.create',
            'elders.update',
            'elders.delete',
            'elders.manage',

            // Sponsorship & Pledges
            'sponsorships.view',
            'sponsorships.create',
            'sponsorships.update',
            'sponsorships.delete',
            'sponsorships.manage',

            // Donations
            'donations.view',
            'donations.create',
            'donations.update',
            'donations.delete',
            'donations.manage',
            'donations.approve',

            // Visits & Follow-ups
            'visits.view',
            'visits.create',
            'visits.update',
            'visits.delete',
            'visits.manage',
            'visits.approve',

            // Reports & Analytics
            'reports.view',
            'reports.generate',
            'reports.export',
            'reports.impact_book',
            'reports.financial',
            'reports.operational',
            'reports.view_all',

            // Campaigns
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'campaigns.manage',

            // Mailbox & Communication
            'mailbox.view',
            'mailbox.send',
            'mailbox.process',
            'mailbox.manage',

            // Activity Logs & Audit
            'activity_logs.view',
            'activity_logs.manage',

            // Data Export
            'data_exports.view',
            'data_exports.create',
            'data_exports.manage',

            // System Administration
            'system.settings',
            'system.backup',
            'system.maintenance',
            'system.logs',

            // Timeline & Success Stories
            'timeline.view',
            'timeline.create',
            'timeline.update',
            'timeline.delete',
            'timeline.manage',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
        
        // Define wildcard permissions
        $wildcardPermissions = [
            'users.*',
            'staff.*',
            'roles.*',
            'permissions.*',
            'branches.*',
            'elders.*',
            'sponsorships.*',
            'donations.*',
            'visits.*',
            'reports.*',
            'campaigns.*',
            'mailbox.*',
            'activity_logs.*',
            'data_exports.*',
            'system.*',
            'timeline.*',
        ];

        foreach ($wildcardPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define roles with their permissions
        $roles = [
            'Super Admin' => [
                'users.*', 'staff.*', 'roles.*', 'permissions.*', 'branches.*', 'elders.*',
                'sponsorships.*', 'donations.*', 'visits.*', 'reports.*', 'campaigns.*',
                'mailbox.*', 'activity_logs.*', 'data_exports.*', 'system.*', 'timeline.*',
            ],
            'Admin' => [
                'users.*', 'staff.*', 'roles.view', 'roles.update', 'permissions.view',
                'branches.*', 'elders.*', 'sponsorships.*', 'donations.*', 'visits.*',
                'reports.*', 'mailbox.*', 'activity_logs.*', 'data_exports.*', 'system.*',
                'timeline.*',
            ],
            'Manager' => [
                'users.view', 'staff.*', 'branches.view', 'elders.*', 'sponsorships.*',
                'donations.*', 'visits.*', 'reports.view', 'reports.generate', 'reports.export',
                'reports.operational', 'mailbox.view', 'mailbox.send', 'activity_logs.view',
                'timeline.view', 'timeline.create', 'timeline.update',
            ],
            'Branch Coordinator' => [
                'users.view', 'staff.view', 'staff.update', 'branches.view', 'elders.view',
                'elders.update', 'donations.view', 'donations.create', 'visits.*', 'reports.view',
                'reports.generate', 'timeline.view', 'timeline.create',
            ],
            'Field Officer' => [
                'users.view', 'staff.view', 'elders.view', 'donations.view', 'visits.view',
                'visits.create', 'visits.update', 'timeline.view', 'timeline.create',
            ],
            'Finance Officer' => [
                'users.view', 'donations.*', 'reports.view', 'reports.financial',
                'reports.generate', 'reports.export', 'data_exports.view', 'data_exports.create',
            ],
            'Auditor' => [
                'users.view', 'staff.view', 'elders.view', 'donations.view', 'visits.view',
                'reports.view', 'reports.generate', 'activity_logs.view', 'data_exports.view',
            ],
            'Reporting Analyst' => [
                'users.view', 'staff.view', 'elders.view', 'donations.view', 'visits.view',
                'reports.view', 'reports.generate', 'reports.export', 'data_exports.view',
            ],
            'External' => [
                'users.view', 'sponsorships.view', 'donations.view', 'reports.view',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        // Clear cache again
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
