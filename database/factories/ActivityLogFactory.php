<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Elder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $causer = User::inRandomOrder()->first();
        $subject = Elder::inRandomOrder()->first();

        return [
            'causer_id' => $causer->id,
            'action' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'description' => $this->faker->sentence,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->id,
            'changes' => json_encode(['old' => ['status' => 'active'], 'new' => ['status' => 'inactive']]),
        ];
    }
}
