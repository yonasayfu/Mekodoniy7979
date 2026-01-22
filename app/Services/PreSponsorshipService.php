<?php

namespace App\Services;

use App\Events\PreSponsorshipCreated;
use App\Models\Donation;
use App\Models\PreSponsorship;

class PreSponsorshipService
{
    public function syncFromDonation(Donation $donation, string $relationshipType, array $overrides = []): PreSponsorship
    {
        $data = [
            'donation_id' => $donation->id,
            'elder_id' => $donation->elder_id,
            'branch_id' => $donation->branch_id,
            'name' => $donation->guest_name ?? $donation->name,
            'email' => $donation->guest_email ?? $donation->email,
            'phone' => $donation->guest_phone ?? $donation->phone,
            'relationship_type' => $relationshipType,
            'amount' => $donation->amount,
            'currency' => $donation->currency ?? 'ETB',
            'status' => $donation->status ?? 'pending',
            'notes' => $donation->notes,
        ];

        $preSponsorship = PreSponsorship::updateOrCreate([
            'donation_id' => $donation->id,
        ], array_merge($data, $overrides));

        event(new PreSponsorshipCreated($preSponsorship));

        return $preSponsorship;
    }
}
