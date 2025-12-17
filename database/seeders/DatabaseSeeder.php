<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
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
        ]);

        // Create sample branches first
        $branches = \App\Models\Branch::factory()->count(5)->create();

        $approvalTimestamp = Carbon::now();

        $admin = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'recovery_email' => 'recovery_admin@example.com',
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_INTERNAL,
                'approved_at' => $approvalTimestamp,
                'approved_by' => null,
                'branch_id' => $branches->first()->id,
            ]);

        $admin->assignRole('Admin');

        Staff::factory()
            ->for($admin, 'user')
            ->create([
                'email' => $admin->email,
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'job_title' => 'Super Administrator',
                'status' => 'active',
            ]);





        $samples = [
            [
                'role' => 'Manager',
                'name' => 'Morgan Manager',
                'email' => 'manager@example.com',
                'first_name' => 'Morgan',
                'last_name' => 'Manager',
                'job_title' => 'Operations Manager',
                'status' => 'active',
                'account_type' => User::TYPE_INTERNAL,
            ],
            [
                'role' => 'Technician',
                'name' => 'Taylor Technician',
                'email' => 'technician@example.com',
                'first_name' => 'Taylor',
                'last_name' => 'Technician',
                'job_title' => 'Field Technician',
                'status' => 'active',
                'account_type' => User::TYPE_INTERNAL,
            ],
            [
                'role' => 'Staff',
                'name' => 'Sydney Staff',
                'email' => 'staff@example.com',
                'first_name' => 'Sydney',
                'last_name' => 'Staff',
                'job_title' => 'Support Specialist',
                'status' => 'active',
                'account_type' => User::TYPE_INTERNAL,
            ],
            [
                'role' => 'Auditor',
                'name' => 'Avery Auditor',
                'email' => 'auditor@example.com',
                'first_name' => 'Avery',
                'last_name' => 'Auditor',
                'job_title' => 'Compliance Auditor',
                'status' => 'active',
                'account_type' => User::TYPE_INTERNAL,
            ],
            [
                'role' => 'ReadOnly',
                'name' => 'Riley Readonly',
                'email' => 'readonly@example.com',
                'first_name' => 'Riley',
                'last_name' => 'Readonly',
                'job_title' => 'Reporting Analyst',
                'status' => 'inactive',
                'account_type' => User::TYPE_INTERNAL,
            ],
        ];

        foreach ($samples as $sample) {
            $user = User::factory()
                ->withoutTwoFactor()
                ->create([
                    'name' => $sample['name'],
                    'email' => $sample['email'],
                    'recovery_email' => 'recovery_' . strtolower(str_replace(' ', '', $sample['role'])) . '@example.com',
                    'account_status' => User::STATUS_ACTIVE,
                    'account_type' => $sample['account_type'],
                    'approved_at' => $approvalTimestamp,
                    'approved_by' => $admin->id,
                    'branch_id' => $branches->random()->id,
                ]);

            $user->assignRole($sample['role']);

            Staff::factory()
                ->for($user, 'user')
                ->create([
                    'email' => $sample['email'],
                    'first_name' => $sample['first_name'],
                    'last_name' => $sample['last_name'],
                    'job_title' => $sample['job_title'],
                    'status' => $sample['status'],
                ]);


        }

        // Create sample elders
        $elders = \App\Models\Elder::factory()->count(50)->create();

        // Create sample pledges (wall of love)
        $users = User::all();
        foreach ($elders as $elder) {
            // Each elder gets 1-3 pledges
            $pledgeCount = rand(1, 3);
            for ($i = 0; $i < $pledgeCount; $i++) {
                \App\Models\Pledge::factory()->create([
                    'user_id' => $users->random()->id,
                    'elder_id' => $elder->id,
                ]);
            }
        }

        // Create sample visits
        foreach ($elders as $elder) {
            // Each elder gets 0-5 visits
            $visitCount = rand(0, 5);
            for ($i = 0; $i < $visitCount; $i++) {
                \App\Models\Visit::factory()->create([
                    'user_id' => $users->random()->id,
                    'elder_id' => $elder->id,
                    'branch_id' => $branches->random()->id,
                ]);
            }
        }

        // Create some additional regular users for pledges
        $regularUsers = User::factory()->count(20)->create([
            'account_status' => User::STATUS_ACTIVE,
            'account_type' => User::TYPE_EXTERNAL,
            'approved_at' => $approvalTimestamp,
            'approved_by' => $admin->id,
        ]);

        // Assign basic role to regular users
        foreach ($regularUsers as $user) {
            $user->assignRole('External');
        }

        // Create more pledges from regular users
        foreach ($regularUsers as $user) {
            $pledgeCount = rand(0, 2);
            for ($i = 0; $i < $pledgeCount; $i++) {
                $pledge = \App\Models\Pledge::factory()->create([
                    'user_id' => $user->id,
                    'elder_id' => $elders->random()->id,
                ]);

                \App\Models\Donation::factory()->create([
                    'user_id' => $pledge->user_id,
                    'pledge_id' => $pledge->id,
                    'elder_id' => $pledge->elder_id,
                    'amount' => $pledge->amount,
                ]);

                \App\Models\TimelineEvent::factory()->create([
                    'user_id' => $pledge->user_id,
                    'elder_id' => $pledge->elder_id,
                    'donation_id' => null,
                    'type' => 'Pledge Started',
                    'description' => 'Pledge started for ' . $pledge->elder->name,
                    'occurred_at' => $pledge->start_date,
                ]);
            }
        }
    }
}
