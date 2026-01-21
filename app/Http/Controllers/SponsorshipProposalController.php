<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorshipProposalRequest;
use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\SponsorshipProposal;
use App\Models\User;
use App\Notifications\SponsorshipProposalCreatedNotification;
use App\Notifications\SponsorshipProposalStatusNotification;
use App\Support\Services\ElderMatchStateService;
use App\Support\Services\TimelineEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SponsorshipProposalController extends Controller
{
    public function __construct(protected ElderMatchStateService $matchStateService)
    {
    }

    public function store(
        StoreSponsorshipProposalRequest $request,
        Elder $elder,
        TimelineEventService $timelineEventService
    ) {
        $donor = User::findOrFail($request->integer('donor_id'));

        if (! $donor->hasAnyRole(['External', 'Donor'])) {
            return back()->withErrors([
                'donor_id' => 'Selected user is not eligible for donor proposals.',
            ]);
        }

        $proposal = SponsorshipProposal::create([
            'elder_id' => $elder->id,
            'donor_id' => $donor->id,
            'proposed_by' => $request->user()->id,
            'amount' => $request->input('amount'),
            'frequency' => $request->input('frequency'),
            'relationship_type' => $request->input('relationship_type'),
            'notes' => $request->input('notes'),
            'status' => SponsorshipProposal::STATUS_PENDING,
            'expires_at' => now()->addHours($request->integer('expires_in_hours', 72)),
        ]);

        $proposal->loadMissing(['donor', 'elder', 'proposer']);

        $this->matchStateService->sync($elder);

        $timelineEventService->createEvent(
            'proposal_created',
            'Match proposal sent to '.$proposal->donor->name,
            now(),
            $request->user(),
            $elder
        );

        $proposal->donor->notify(new SponsorshipProposalCreatedNotification($proposal));

        return back()->with('success', 'Proposal sent to donor.');
    }

    public function cancel(
        Elder $elder,
        SponsorshipProposal $proposal,
        TimelineEventService $timelineEventService
    ) {
        abort_unless($proposal->elder_id === $elder->id, 404);
        Gate::authorize('sponsorships.manage');

        if (! $proposal->isPending()) {
            return back()->with('error', 'Proposal already processed.');
        }

        $proposal->update([
            'status' => SponsorshipProposal::STATUS_CANCELLED,
            'responded_at' => now(),
        ]);

        $proposal->loadMissing(['elder', 'donor']);

        $this->matchStateService->sync($elder);

        $actor = Auth::user();

        $timelineEventService->createEvent(
            'proposal_cancelled',
            'Proposal for '.$proposal->donor->name.' was cancelled.',
            now(),
            $actor,
            $elder
        );

        $proposal->donor->notify(new SponsorshipProposalStatusNotification(
            $proposal,
            SponsorshipProposal::STATUS_CANCELLED,
            true,
            $actor?->name
        ));

        return back()->with('success', 'Proposal cancelled.');
    }

    public function accept(
        Request $request,
        SponsorshipProposal $proposal,
        TimelineEventService $timelineEventService
    ) {
        $user = $request->user();
        abort_unless($user && $proposal->donor_id === $user->id, 403);

        if (! $proposal->isPending()) {
            return back()->with('error', 'Proposal already processed.');
        }

        $sponsorship = Sponsorship::create([
            'user_id' => $proposal->donor_id,
            'elder_id' => $proposal->elder_id,
            'branch_id' => $proposal->elder->branch_id,
            'amount' => $proposal->amount,
            'currency' => 'ETB',
            'frequency' => $proposal->frequency,
            'relationship_type' => $proposal->relationship_type,
            'start_date' => now()->toDateString(),
            'status' => 'active',
            'notes' => $proposal->notes,
        ]);

        $proposal->update([
            'status' => SponsorshipProposal::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);

        $proposal->loadMissing(['elder', 'donor', 'proposer']);

        $this->matchStateService->sync($proposal->elder);

        $timelineEventService->createEvent(
            'sponsorship_created',
            'Proposal accepted by '.$user->name,
            now(),
            $user,
            $proposal->elder
        );

        if ($proposal->proposer) {
            $proposal->proposer->notify(new SponsorshipProposalStatusNotification(
                $proposal,
                SponsorshipProposal::STATUS_ACCEPTED
            ));
        }

        return back()->with('success', 'Thank you! Sponsorship activated.');
    }

    public function decline(
        Request $request,
        SponsorshipProposal $proposal,
        TimelineEventService $timelineEventService
    ) {
        $user = $request->user();
        abort_unless($user && $proposal->donor_id === $user->id, 403);

        if (! $proposal->isPending()) {
            return back()->with('error', 'Proposal already processed.');
        }

        $proposal->update([
            'status' => SponsorshipProposal::STATUS_DECLINED,
            'responded_at' => now(),
        ]);

        $proposal->loadMissing(['elder', 'proposer']);

        $this->matchStateService->sync($proposal->elder);

        $timelineEventService->createEvent(
            'proposal_declined',
            'Proposal declined by '.$user->name,
            now(),
            $user,
            $proposal->elder
        );

        if ($proposal->proposer) {
            $proposal->proposer->notify(new SponsorshipProposalStatusNotification(
                $proposal,
                SponsorshipProposal::STATUS_DECLINED
            ));
        }

        return back()->with('success', 'Proposal declined.');
    }
}
