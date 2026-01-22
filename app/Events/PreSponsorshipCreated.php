<?php

namespace App\Events;

use App\Models\PreSponsorship;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreSponsorshipCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PreSponsorship $preSponsorship)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        $branchId = $this->preSponsorship->branch_id ?? $this->preSponsorship->donation?->branch_id ?? 0;

        return new PrivateChannel("branch.{$branchId}.pledges");
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->preSponsorship->id,
            'donation_id' => $this->preSponsorship->donation_id,
            'elder_id' => $this->preSponsorship->elder_id ?? $this->preSponsorship->donation?->elder_id,
            'relationship_type' => $this->preSponsorship->relationship_type,
            'amount' => $this->preSponsorship->amount,
            'status' => $this->preSponsorship->status,
            'created_at' => $this->preSponsorship->created_at?->toIsoString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'pre-sponsorship.created';
    }
}
