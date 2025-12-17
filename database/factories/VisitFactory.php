<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Elder;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Visit::class;

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
            'branch_id' => Branch::factory(),
            'visit_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'purpose' => $this->faker->randomElement([
                'Regular check-in',
                'Medical assistance',
                'Grocery shopping',
                'House cleaning',
                'Companionship visit',
                'Transportation assistance',
                'Medication pickup',
                'Home repair',
                'Holiday celebration',
                'Birthday celebration'
            ]),
            'notes' => $this->faker->optional(0.8)->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'approved_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the visit is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the visit is this month.
     */
    public function thisMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'visit_date' => $this->faker->dateTimeThisMonth(),
        ]);
    }
}