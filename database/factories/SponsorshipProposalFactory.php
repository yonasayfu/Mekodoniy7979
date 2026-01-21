<?php

namespace Database\Factories;

use App\Models\Elder;
use App\Models\SponsorshipProposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorshipProposal>
 */
class SponsorshipProposalFactory extends Factory
{
    protected $model = SponsorshipProposal::class;

    public function definition(): array
    {
        return [
            'elder_id' => Elder::factory(),
            'donor_id' => User::factory(),
            'proposed_by' => User::factory(),
            'amount' => $this->faker->numberBetween(1000, 2500),
            'frequency' => 'monthly',
            'relationship_type' => $this->faker->randomElement(['father', 'mother', 'brother', 'sister']),
            'notes' => $this->faker->optional()->sentence(),
            'status' => SponsorshipProposal::STATUS_PENDING,
            'expires_at' => now()->addDays(3),
        ];
    }
}
