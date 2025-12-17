<?php

namespace Database\Factories;

use App\Models\Elder;
use App\Models\Pledge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
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
            'pledge_id' => null,
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'ETB',
            'payment_gateway' => $this->faker->randomElement(['Telebirr', 'CBE', 'Stripe']),
            'payment_id' => 'txn_' . $this->faker->unique()->lexify('??????????????'),
            'status' => $this->faker->randomElement(['pending', 'successful', 'failed']),
        ];
    }
}
