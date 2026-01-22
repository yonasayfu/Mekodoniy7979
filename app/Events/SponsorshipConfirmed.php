<?php

namespace App\Events;

use App\Models\PreSponsorship;
use App\Models\Sponsorship;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SponsorshipConfirmed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Sponsorship $sponsorship, public ?PreSponsorship $preSponsorship = null)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        $branchId = $this->sponsorship->branch_id ?? $this->sponsorship->elder?->branch_id ?? 0;

        return new PrivateChannel("branch.{$branchId}.sponsorships");
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->sponsorship->id,
            'pre_sponsorship_id' => $this->preSponsorship?->id,
            'elder_id' => $this->sponsorship->elder_id,
            'amount' => $this->sponsorship->amount,
            'status' => $this->sponsorship->status,
            'relationship_type' => $this->sponsorship->relationship_type,
            'created_at' => $this->sponsorship->created_at?->toIsoString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sponsorship.confirmed';
    }
}
