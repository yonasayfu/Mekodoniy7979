<?php

namespace Database\Seeders;

use App\Models\Elder;
use App\Models\Pledge;
use App\Models\User;
use Illuminate\Database\Seeder;

class SponsorshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are enough users and elders to associate with pledges
        if (User::count() < 10) {
            User::factory(10)->create();
        }

        if (Elder::count() < 10) {
            Elder::factory(10)->create();
        }

        // Create 50 pledges, associating them with existing users and elders
        Pledge::factory(50)->create([
            'user_id' => User::inRandomOrder()->first()->id,
            'elder_id' => Elder::inRandomOrder()->first()->id,
        ]);
    }
}