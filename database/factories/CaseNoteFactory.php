<?php

namespace Database\Factories;

use App\Models\CaseNote;
use App\Models\Elder;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseNoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CaseNote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'elder_id' => Elder::inRandomOrder()->first()->id,
            'branch_id' => Branch::inRandomOrder()->first()->id,
            'created_by' => User::inRandomOrder()->first()->id,
            'content' => $this->faker->paragraph,
            'visibility' => $this->faker->randomElement(['internal', 'donor_visible']),
        ];
    }
}
