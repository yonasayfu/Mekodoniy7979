<?php

namespace Database\Seeders;

use App\Models\DonorProfile;
use App\Models\User;
use App\Models\Staff;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $this->createDefaultAdmin();

        // Create scoped branch roles
        $this->createBranchAdmin();
        $this->createBranchCoordinator();

        // Create a donor/external account for experience testing
        $this->createDemoDonor();

        // Create additional staff users for testing
        $this->createAdditionalStaff();
        
        // Create 10 random users
        User::factory(10)->create();
    }

    /**
     * Create additional staff users for testing purposes.
     */
    private function createAdditionalStaff(): void
    {
        $branches = Branch::all();

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
            [
                'role' => 'Manager',
                'name' => 'Maya Manager',
                'email' => 'manager@example.com',
                'first_name' => 'Maya',
                'last_name' => 'Manager',
                'job_title' => 'Branch Manager',
                'status' => 'active',
            ],
            [
                'role' => 'Finance Officer',
                'name' => 'Faith Finance',
                'email' => 'finance@example.com',
                'first_name' => 'Faith',
                'last_name' => 'Finance',
                'job_title' => 'Finance Officer',
                'status' => 'active',
            ],
            [
                'role' => 'Field Officer',
                'name' => 'Felix Field',
                'email' => 'field@example.com',
                'first_name' => 'Felix',
                'last_name' => 'Field',
                'job_title' => 'Field Officer',
                'status' => 'active',
            ],
        ];

        foreach ($additionalStaff as $staff) {
            $user = User::updateOrCreate(['email' => $staff['email']], [
                'name' => $staff['name'],
                'password' => Hash::make('password'),
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_INTERNAL,
                'approved_at' => Carbon::now(),
                'approved_by' => 1,
                'branch_id' => $branches->random()->id,
            ]);

            $user->assignRole($staff['role']);

            Staff::updateOrCreate(['email' => $staff['email']], [
                'user_id' => $user->id,
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

        $admin = User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'System Administrator',
            'password' => bcrypt('password'),
            'recovery_email' => 'recovery_admin@example.com',
            'account_status' => User::STATUS_ACTIVE,
            'account_type' => User::TYPE_INTERNAL,
            'approved_at' => $approvalTimestamp,
            'approved_by' => null,
            'branch_id' => $branches->first()->id,
        ]);

        $admin->assignRole('Super Admin');

        Staff::updateOrCreate(['email' => $admin->email], [
            'user_id' => $admin->id,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'job_title' => 'Super Administrator',
            'status' => 'active',
        ]);
    }

    private function createBranchAdmin(): void
    {
        $branch = Branch::query()->first();

        if (! $branch) {
            return;
        }

        $branchAdmin = User::updateOrCreate(['email' => 'branchadmin@example.com'], [
            'name' => 'Branch Administrator',
            'password' => Hash::make('password'),
            'account_status' => User::STATUS_ACTIVE,
            'account_type' => User::TYPE_INTERNAL,
            'approved_at' => Carbon::now(),
            'branch_id' => $branch->id,
        ]);

        $branchAdmin->assignRole('Branch Admin');

        Staff::updateOrCreate(['email' => $branchAdmin->email], [
            'user_id' => $branchAdmin->id,
            'first_name' => 'Branch',
            'last_name' => 'Admin',
            'job_title' => 'Branch Administrator',
            'status' => 'active',
        ]);
    }

    private function createBranchCoordinator(): void
    {
        $branch = Branch::query()->skip(1)->first() ?? Branch::query()->first();

        if (! $branch) {
            return;
        }

        $coordinator = User::updateOrCreate(['email' => 'coordinator@example.com'], [
            'name' => 'Field Coordinator',
            'password' => Hash::make('password'),
            'account_status' => User::STATUS_ACTIVE,
            'account_type' => User::TYPE_INTERNAL,
            'approved_at' => Carbon::now(),
            'branch_id' => $branch->id,
        ]);

        $coordinator->assignRole('Branch Coordinator');

        Staff::updateOrCreate(['email' => $coordinator->email], [
            'user_id' => $coordinator->id,
            'first_name' => 'Field',
            'last_name' => 'Coordinator',
            'job_title' => 'Branch Coordinator',
            'status' => 'active',
        ]);
    }

    private function createDemoDonor(): void
    {
        $donor = User::updateOrCreate(['email' => 'donor@example.com'], [
            'name' => 'Demo Donor',
            'password' => Hash::make('password'),
            'account_status' => User::STATUS_ACTIVE,
            'account_type' => User::TYPE_EXTERNAL,
            'approved_at' => Carbon::now(),
            'branch_id' => null,
        ]);

        $donor->syncRoles(['External', 'Donor']);

        DonorProfile::updateOrCreate(
            ['user_id' => $donor->id],
            [
                'relationship_goal' => 'father',
                'monthly_budget' => 1800,
                'frequency' => 'monthly',
                'preferred_contact_method' => 'email',
                'contact_channels' => ['email', 'sms'],
                'payment_preference' => 'telebirr_auto',
                'notes' => 'Seeded profile for demos.',
                'onboarding_step' => 'payment',
                'is_completed' => true,
                'completed_at' => Carbon::now()->subWeek(),
            ]
        );
    }
}
