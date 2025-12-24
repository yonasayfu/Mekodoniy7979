<?php

namespace Database\Factories;

use App\Models\ElderHealthAssessment;
use App\Models\Elder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElderHealthAssessmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ElderHealthAssessment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'elder_id' => Elder::inRandomOrder()->first()->id,
            'assessment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'summary' => $this->faker->paragraph,
            'mobility_level' => $this->faker->randomElement(['fully_mobile', 'partially_mobile', 'not_mobile']),
            'risk_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'created_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
