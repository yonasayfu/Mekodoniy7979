<?php

namespace App\Console\Commands;

use App\Models\SponsorshipProposal;
use App\Notifications\SponsorshipProposalStatusNotification;
use App\Support\Services\ElderMatchStateService;
use App\Support\Services\TimelineEventService;
use Illuminate\Console\Command;

class ExpireSponsorshipProposals extends Command
{
    protected $signature = 'sponsorship-proposals:expire';

    protected $description = 'Expire any pending sponsorship proposals whose expiry timestamp has passed.';

    public function __construct(
        protected ElderMatchStateService $matchStateService,
        protected TimelineEventService $timelineEventService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $expired = SponsorshipProposal::where('status', SponsorshipProposal::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No proposals to expire.');
            return self::SUCCESS;
        }

        foreach ($expired as $proposal) {
            $proposal->markExpired();
            $proposal->loadMissing(['elder', 'donor', 'proposer']);

            if ($proposal->elder) {
                $this->matchStateService->sync($proposal->elder);

                $this->timelineEventService->createEvent(
                    'proposal_expired',
                    'Proposal for '.$proposal->donor?->name.' expired automatically.',
                    now(),
                    null,
                    $proposal->elder
                );
            }

            if ($proposal->proposer) {
                $proposal->proposer->notify(new SponsorshipProposalStatusNotification(
                    $proposal,
                    SponsorshipProposal::STATUS_EXPIRED
                ));
            }

            if ($proposal->donor) {
                $proposal->donor->notify(new SponsorshipProposalStatusNotification(
                    $proposal,
                    SponsorshipProposal::STATUS_EXPIRED,
                    true
                ));
            }
        }

        $this->info("Expired {$expired->count()} proposals.");

        return self::SUCCESS;
    }
}
