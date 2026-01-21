<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Elder;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
        $needsTranslator = $this->faker->boolean(30);
        $needsTransport = $this->faker->boolean(45);
        $status = $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed']);
        $scheduledAt = Carbon::instance(
            $this->faker->dateTimeBetween('-6 months', '+2 months')
        );
        $approvalDeadline = $scheduledAt->copy()->subHours($this->faker->numberBetween(6, 48));
        $approvedTimestamp = in_array($status, ['approved', 'completed'], true)
            ? $approvalDeadline->copy()->subHours($this->faker->numberBetween(1, 6))
            : null;

        return [
            'user_id' => User::factory(),
            'elder_id' => Elder::factory(),
            'branch_id' => Branch::inRandomOrder()->first()->id,
            'visit_date' => $scheduledAt,
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
            'status' => $status,
            'approved_by' => $approvedTimestamp ? User::factory() : null,
            'needs_translator' => $needsTranslator,
            'translator_language' => $needsTranslator
                ? $this->faker->randomElement(['Amharic', 'Afaan Oromo', 'Tigrinya', 'English'])
                : null,
            'needs_transport' => $needsTransport,
            'transport_preference' => $needsTransport
                ? $this->faker->randomElement(['Organization van', 'Private car', 'Ride-share'])
                : null,
            'logistics_notes' => $this->faker->optional()->sentence(),
            'approval_deadline' => $approvalDeadline,
            'approved_at' => $approvedTimestamp,
            'sla_reminder_sent_at' => null,
            'sla_breached_notified_at' => null,
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
