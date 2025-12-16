<?php

namespace Database\Factories;

use App\Models\Elder;
use App\Models\Pledge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pledge>
 */
class PledgeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pledge::class;

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
            'amount' => $this->faker->numberBetween(50, 500),
            'frequency' => $this->faker->randomElement(['monthly', 'quarterly', 'annually', 'one-time']),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+2 years'),
            'status' => $this->faker->randomElement(['pending', 'active', 'completed', 'cancelled']),
            'notes' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    /**
     * Indicate that the pledge is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the pledge is monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'monthly',
        ]);
    }
}