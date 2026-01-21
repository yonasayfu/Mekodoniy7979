<?php

namespace Database\Seeders;

use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Database\Seeder;

class SponsorshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are enough users and elders to associate with sponsorships
        if (User::count() < 10) {
            User::factory(10)->create();
        }

        if (Elder::count() < 10) {
            Elder::factory(10)->create();
        }

        $users = User::all();
        $elders = Elder::all();

        if ($users->isEmpty() || $elders->isEmpty()) {
            return;
        }

        Sponsorship::factory()
            ->count(30)
            ->state(function () use ($users, $elders) {
                $elder = $elders->random();

                return [
                    'user_id' => $users->random()->id,
                    'elder_id' => $elder->id,
                    'branch_id' => $elder->branch_id,
                ];
            })
            ->create();
    }
}
