<?php

namespace App\Support\Services;

use App\Models\Donation;
use App\Models\Elder;
use App\Models\TimelineEvent;
use App\Models\User;
use Carbon\Carbon;

class TimelineEventService
{
    /**
     * Create a new timeline event.
     *
     * @param string $type
     * @param string $description
     * @param Carbon $occurredAt
     * @param User|null $user
     * @param Elder|null $elder
     * @param Donation|null $donation
     * @return TimelineEvent
     */
    public function createEvent(
        string $type,
        string $description,
        Carbon $occurredAt,
        ?User $user = null,
        ?Elder $elder = null,
        ?Donation $donation = null
    ): TimelineEvent {
        return TimelineEvent::create([
            'user_id' => $user ? $user->id : null,
            'elder_id' => $elder ? $elder->id : null,
            'donation_id' => $donation ? $donation->id : null,
            'type' => $type,
            'description' => $description,
            'occurred_at' => $occurredAt,
        ]);
    }
}