<?php

namespace App\Services;

use App\Events\SponsorshipConfirmed;
use App\Models\Donation;
use App\Models\PreSponsorship;
use App\Models\Sponsorship;
use Carbon\Carbon;

class SponsorshipPromotionService
{
    public function promote(PreSponsorship $preSponsorship): Sponsorship
    {
        if ($preSponsorship->status === 'confirmed' && $preSponsorship->donation?->sponsorship) {
            return $preSponsorship->donation->sponsorship;
        }

        $donation = $preSponsorship->donation;

        $elderId = $preSponsorship->elder_id ?: $donation?->elder_id;
        if (! $elderId) {
            throw new \RuntimeException('Cannot promote a pledge without an elder assigned.');
        }

        $branchId = $preSponsorship->branch_id ?: ($donation?->branch_id ?? null);
        $amount = $preSponsorship->amount ?: ($donation?->amount ?? 0);
        $currency = $preSponsorship->currency ?: ($donation?->currency ?? 'ETB');

        $sponsorship = Sponsorship::create([
            'user_id' => $donation?->user_id,
            'elder_id' => $elderId,
            'branch_id' => $branchId,
            'amount' => $amount,
            'currency' => $currency,
            'frequency' => 'monthly',
            'relationship_type' => $preSponsorship->relationship_type,
            'start_date' => Carbon::now()->toDateString(),
            'status' => 'active',
            'notes' => $preSponsorship->notes,
        ]);

        $preSponsorship->update([
            'status' => 'confirmed',
        ]);

        if ($donation) {
            $donation->update([
                'status' => 'approved',
                'sponsorship_id' => $sponsorship->id,
            ]);
        }

        event(new SponsorshipConfirmed($sponsorship, $preSponsorship));

        return $sponsorship;
    }
}
