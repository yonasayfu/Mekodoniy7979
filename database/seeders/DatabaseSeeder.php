<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            MekodoniaSeeder::class,
            CampaignSeeder::class,
            DonationSeeder::class,
            ElderSeeder::class,
            VisitSeeder::class,
            StaffSeeder::class,
            CaseNoteSeeder::class,
            ElderHealthAssessmentSeeder::class,
            ElderMedicalConditionSeeder::class,
            ElderMedicationSeeder::class,
            TimelineEventSeeder::class,
            ActivityLogSeeder::class,
            SponsorshipSeeder::class,
        ]);
    }
}
