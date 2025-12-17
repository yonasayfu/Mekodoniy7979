<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\Elder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimelineEvent>
 */
class TimelineEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'elder_id' => Elder::factory(),
            'donation_id' => null,
            'type' => $this->faker->randomElement(['Pledge Started', 'Donation Received', 'Visit Scheduled']),
            'description' => $this->faker->sentence,
            'occurred_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
