<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions based on sidebar configuration
        $permissions = [
            'activity-logs.view',
            'mailbox.view',
            'branches.manage',
            'elders.manage',
            'sponsorships.manage',
            'visits.manage',
            'campaigns.manage',
            'reports.view',
            'staff.view',
            'users.manage',
            'roles.manage',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create an 'Admin' role if it doesn't exist
        $adminRole = Role::findOrCreate('Admin');

        // Assign all defined permissions to the 'Admin' role
        $adminRole->givePermissionTo($permissions);

        // Find the first user and assign the 'Admin' role to them
        // You might want to modify this to target a specific user by email or ID
        $user = User::first(); 
        if ($user) {
            $user->assignRole($adminRole);
            $this->command->info("Assigned 'Admin' role to user: {$user->email}");
        } else {
            $this->command->warn('No user found to assign Admin role to. Please create a user first.');
        }

        $this->command->info('Permissions and roles seeded successfully!');
    }
}