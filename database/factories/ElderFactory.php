<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Elder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Elder>
 */
class ElderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Elder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->dateTimeBetween('-90 years', '-60 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->paragraph(),
            'profile_picture_path' => $this->faker->imageUrl(400, 400, 'people', true, 'elder'),
            'priority_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'health_status' => $this->faker->randomElement(['good', 'fair', 'poor', 'critical']),
            'special_needs' => $this->faker->optional(0.3)->sentence(),
            'monthly_expenses' => $this->faker->numberBetween(500, 2000),
            'video_url' => $this->faker->optional(0.2)->url(),
            'health_conditions' => implode(', ', $this->faker->randomElements(['Hypertension', 'Diabetes', 'Arthritis', 'Dementia', 'Osteoporosis'], $this->faker->numberBetween(1, 3))),
            'sponsorship_status' => $this->faker->randomElement(['sponsored', 'not_sponsored', 'partially_sponsored']),
        ];
    }

    /**
     * Indicate that the elder has high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_level' => 'high',
        ]);
    }

    /**
     * Indicate that the elder has special needs.
     */
    public function withSpecialNeeds(): static
    {
        return $this->state(fn (array $attributes) => [
            'special_needs' => $this->faker->sentence(),
        ]);
    }
}
