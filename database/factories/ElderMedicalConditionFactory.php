<?php

namespace Database\Factories;

use App\Models\ElderMedicalCondition;
use App\Models\Elder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElderMedicalConditionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ElderMedicalCondition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'elder_id' => Elder::inRandomOrder()->first()->id,
            'condition_name' => $this->faker->randomElement(['Hypertension', 'Diabetes', 'Arthritis', 'Dementia', 'Osteoporosis', 'Asthma', 'Heart Disease']),
            'diagnosed_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'status' => $this->faker->randomElement(['active', 'in_remission', 'cured']),
            'notes' => $this->faker->paragraph,
        ];
    }
}
