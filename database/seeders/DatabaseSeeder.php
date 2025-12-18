<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use App\Models\Branch;
use App\Notifications\DataExportReadyNotification;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MekodoniaSeeder::class,
            SponsorshipSeeder::class,
        ]);

        // Create additional sample branches for testing
        $additionalBranches = \App\Models\Branch::factory()->count(2)->create([
            'is_active' => true,
        ]);

        // Create default admin user
        $this->createDefaultAdmin();

        // Create additional staff users for testing
        $this->createAdditionalStaff();
    }

    /**
     * Create additional staff users for testing purposes.
     */
    private function createAdditionalStaff(): void
    {
        $branches = \App\Models\Branch::all();

        $additionalStaff = [
            [
                'role' => 'Auditor',
                'name' => 'Avery Auditor',
                'email' => 'auditor@example.com',
                'first_name' => 'Avery',
                'last_name' => 'Auditor',
                'job_title' => 'Compliance Auditor',
                'status' => 'active',
            ],
            [
                'role' => 'Reporting Analyst',
                'name' => 'Riley Readonly',
                'email' => 'readonly@example.com',
                'first_name' => 'Riley',
                'last_name' => 'Readonly',
                'job_title' => 'Reporting Analyst',
                'status' => 'inactive',
            ],
        ];

        foreach ($additionalStaff as $staff) {
            $user = User::factory()
                ->withoutTwoFactor()
                ->create([
                    'name' => $staff['name'],
                    'email' => $staff['email'],
                    'recovery_email' => 'recovery_' . strtolower(str_replace(' ', '', $staff['role'])) . '@example.com',
                    'account_status' => User::STATUS_ACTIVE,
                    'account_type' => User::TYPE_INTERNAL,
                    'approved_at' => Carbon::now(),
                    'approved_by' => 1,
                    'branch_id' => $branches->random()->id,
                ]);

            $user->assignRole($staff['role']);

            Staff::factory()
                ->for($user, 'user')
                ->create([
                    'email' => $staff['email'],
                    'first_name' => $staff['first_name'],
                    'last_name' => $staff['last_name'],
                    'job_title' => $staff['job_title'],
                    'status' => $staff['status'],
                ]);
        }
    }

    /**
     * Create the default admin user for easy login.
     */
    private function createDefaultAdmin(): void
    {
        $branches = Branch::all();
        $approvalTimestamp = Carbon::now();

        $admin = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'recovery_email' => 'recovery_admin@example.com',
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_INTERNAL,
                'approved_at' => $approvalTimestamp,
                'approved_by' => null,
                'branch_id' => $branches->first()->id,
            ]);

        $admin->assignRole('Super Admin');

        Staff::factory()
            ->for($admin, 'user')
            ->create([
                'email' => $admin->email,
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'job_title' => 'Super Administrator',
                'status' => 'active',
            ]);
    }
}
