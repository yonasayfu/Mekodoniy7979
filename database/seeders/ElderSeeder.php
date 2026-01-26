<?php

namespace Database\Seeders;

use App\Models\Elder;
use Illuminate\Database\Seeder;

class ElderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = \App\Models\Branch::limit(3)->get();

        $elders = [
            [
                'branch_id' => $branches->first()->id ?? null,
                'first_name' => 'Abebe',
                'last_name' => 'Tadesse',
                'relationship_type' => 'father',
                'relationship_priority' => 1,
                'priority_level' => 'high',
                'gender' => 'male',
                'date_of_birth' => now()->subYears(74),
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'bio' => 'Retired teacher who now guides the boys at the center.',
                'profile_picture_path' => 'images/elder-1.jpg',
                'monthly_expenses' => 2200,
                'funding_goal' => 7000,
                'funding_received' => 4000,
                'health_status' => 'fair',
            ],
            [
                'branch_id' => $branches->skip(1)->first()->id ?? $branches->first()->id,
                'first_name' => 'Tigist',
                'last_name' => 'Ayele',
                'relationship_type' => 'mother',
                'relationship_priority' => 1,
                'priority_level' => 'medium',
                'gender' => 'female',
                'date_of_birth' => now()->subYears(69),
                'city' => 'Hawassa',
                'country' => 'Ethiopia',
                'bio' => 'Former nurse who loves knitting and community meals.',
                'profile_picture_path' => 'images/elder-2.jpg',
                'monthly_expenses' => 1800,
                'funding_goal' => 5000,
                'funding_received' => 1800,
                'health_status' => 'good',
            ],
            [
                'branch_id' => $branches->skip(2)->first()->id ?? $branches->first()->id,
                'first_name' => 'Mekdes',
                'last_name' => 'Girma',
                'relationship_type' => 'sister',
                'relationship_priority' => 2,
                'priority_level' => 'low',
                'gender' => 'female',
                'date_of_birth' => now()->subYears(63),
                'city' => 'Bahir Dar',
                'country' => 'Ethiopia',
                'bio' => 'Enjoys coffee ceremonies and telling stories of Gondar.',
                'profile_picture_path' => 'images/elder-3.jpg',
                'monthly_expenses' => 1600,
                'funding_goal' => 6500,
                'funding_received' => 2200,
                'health_status' => 'good',
            ],
        ];

        foreach ($elders as $elderData) {
            Elder::updateOrCreate(
                ['first_name' => $elderData['first_name'], 'last_name' => $elderData['last_name']],
                array_merge($elderData, [
                    'health_conditions' => json_encode(['Hypertension']),
                    'monthly_expenses_breakdown' => json_encode([
                        'food' => 600,
                        'medical' => 300,
                        'housing' => 400,
                        'other' => 200,
                    ]),
                ]),
            );
        }
    }
}
