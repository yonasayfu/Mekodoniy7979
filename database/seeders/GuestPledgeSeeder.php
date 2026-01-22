<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\User;
use App\Services\PreSponsorshipService;
use App\Services\SponsorshipPromotionService;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuestPledgeSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        $elders = Elder::inRandomOrder()->take(4)->get();
        if (! $branch || $elders->isEmpty()) {
            return;
        }

        $faker = Faker::create();
        $preSponsorshipService = app(PreSponsorshipService::class);
        $promotionService = app(SponsorshipPromotionService::class);

        $donors = User::role('Donor')->get();
        if ($donors->isEmpty()) {
            $donors = collect([
                User::factory()->create([
                    'name' => 'Demo Donor One',
                    'email' => 'donor.one@example.com',
                ]),
                User::factory()->create([
                    'name' => 'Demo Donor Two',
                    'email' => 'donor.two@example.com',
                ]),
            ]);
            $donors->each->assignRole('Donor');
        }

        foreach ($elders as $index => $elder) {
            $guestName = $faker->name();
            $guestEmail = $faker->unique()->safeEmail();
            $donor = $donors->random();

            $donation = Donation::create([
                'branch_id' => $branch->id,
                'elder_id' => $elder->id,
                'user_id' => $index === 0 ? null : $donor->id,
                'amount' => $faker->numberBetween(1500, 5200),
                'currency' => 'ETB',
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
                'guest_phone' => $faker->phoneNumber(),
                'payment_gateway' => 'manual',
                'payment_id' => Str::uuid(),
                'status' => $index === 0 ? 'pending' : 'approved',
                'donation_type' => 'guest_sponsorship',
                'notes' => $faker->sentence(),
            ]);

            $relationship = $faker->randomElement(['father', 'mother', 'brother', 'sister']);
            $preSponsorship = $preSponsorshipService->syncFromDonation($donation, $relationship);

            if ($index > 0) {
                $promotionService->promote($preSponsorship);
            }
        }
    }
}
