<?php

namespace App\Support\Services;

use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\SponsorshipProposal;

class ElderMatchStateService
{
    /**
     * Synchronize the elder's current_status field with their match context.
     */
    public function sync(Elder $elder): void
    {
        $elderId = $elder->getKey();

        if (! $elderId) {
            return;
        }

        $status = 'available';

        $hasActiveSponsorship = Sponsorship::withoutGlobalScopes()
            ->where('elder_id', $elderId)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActiveSponsorship) {
            $status = 'sponsored';
        } else {
            $hasPendingProposal = SponsorshipProposal::where('elder_id', $elderId)
                ->where('status', SponsorshipProposal::STATUS_PENDING)
                ->exists();

            if ($hasPendingProposal) {
                $status = 'pending_match';
            }
        }

        if ($elder->current_status !== $status) {
            $elder->forceFill(['current_status' => $status])->save();
        }
    }
}
