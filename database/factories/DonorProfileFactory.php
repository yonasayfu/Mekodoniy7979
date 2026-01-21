<?php

namespace Database\Factories;

use App\Models\DonorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonorProfile>
 */
class DonorProfileFactory extends Factory
{
    protected $model = DonorProfile::class;

    public function definition(): array
    {
        $channels = $this->faker->randomElements(
            ['sms', 'email', 'phone', 'whatsapp'],
            $this->faker->numberBetween(1, 3)
        );

        return [
            'user_id' => User::factory(),
            'relationship_goal' => $this->faker->randomElement(['father', 'mother', 'brother', 'sister', 'open']),
            'monthly_budget' => $this->faker->randomElement([800, 1200, 1500, 2000]),
            'frequency' => 'monthly',
            'preferred_contact_method' => $channels[0],
            'contact_channels' => $channels,
            'payment_preference' => $this->faker->randomElement(['telebirr_auto', 'cbe_auto', 'manual']),
            'notes' => $this->faker->optional()->sentence(),
            'onboarding_step' => 'payment',
            'is_completed' => true,
            'completed_at' => now()->subDays($this->faker->numberBetween(1, 90)),
        ];
    }
}
