<?php

namespace Database\Factories;

use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sponsorship>
 */
class SponsorshipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sponsorship::class;

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
            'branch_id' => function (array $attributes) {
                if (! isset($attributes['elder_id'])) {
                    return null;
                }

                return Elder::withoutGlobalScopes()
                    ->whereKey($attributes['elder_id'])
                    ->value('branch_id');
            },
            'amount' => $this->faker->numberBetween(50, 500),
            'currency' => 'ETB',
            'frequency' => $this->faker->randomElement(['monthly', 'quarterly', 'annually', 'one-time']),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+2 years'),
            'status' => $this->faker->randomElement(['pending', 'active', 'completed', 'cancelled']),
            'relationship_type' => $this->faker->randomElement(['father', 'mother', 'brother', 'sister']),
            'notes' => $this->faker->optional(0.5)->paragraph(),
            'subscription_id' => $this->faker->boolean(60)
                ? 'sub_' . $this->faker->unique()->lexify('??????????????')
                : null,
            'subscription_gateway' => $this->faker->randomElement(['telebirr', 'cbe']),
            'subscription_metadata' => [
                'origin' => 'factory',
                'msisdn' => $this->faker->phoneNumber(),
            ],
            'next_billing_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * Indicate that the sponsorship is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the sponsorship is monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'monthly',
        ]);
    }
}
