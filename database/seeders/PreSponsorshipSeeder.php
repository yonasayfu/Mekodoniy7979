<?php

namespace Database\Seeders;

use App\Models\PreSponsorship;
use Illuminate\Database\Seeder;

class PreSponsorshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leads = [
            [
                'name' => 'Lily Haile',
                'email' => 'lily@example.com',
                'phone' => '+251911000001',
                'relationship_type' => 'mother',
            ],
            [
                'name' => 'Samuel Kidane',
                'email' => 'samuel@example.com',
                'phone' => '+251911000002',
                'relationship_type' => 'father',
            ],
            [
                'name' => 'Bethlehem Girma',
                'email' => 'bethlehem@example.com',
                'phone' => '+251911000003',
                'relationship_type' => 'sister',
            ],
        ];

        foreach ($leads as $lead) {
            PreSponsorship::updateOrCreate(
                ['email' => $lead['email']],
                $lead,
            );
        }
    }
}
