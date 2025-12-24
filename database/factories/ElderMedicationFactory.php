<?php

namespace Database\Factories;

use App\Models\ElderMedication;
use App\Models\Elder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElderMedicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ElderMedication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'elder_id' => Elder::inRandomOrder()->first()->id,
            'medication_name' => $this->faker->word,
            'dosage' => $this->faker->randomElement(['1 tablet', '2 tablets', '10ml', '20ml']),
            'frequency' => $this->faker->randomElement(['Once a day', 'Twice a day', 'As needed']),
            'started_at' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'ended_at' => $this->faker->optional(0.3)->dateTimeBetween('-6 months', 'now'),
            'notes' => $this->faker->sentence,
        ];
    }
}
