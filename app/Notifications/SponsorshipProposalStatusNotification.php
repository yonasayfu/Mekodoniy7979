<?php

namespace App\Notifications;

use App\Models\SponsorshipProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SponsorshipProposalStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected SponsorshipProposal $proposal,
        protected string $event,
        protected bool $forDonor = false,
        protected ?string $actorName = null
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->mailSubject())
            ->greeting('Selam ' . $notifiable->name . ',')
            ->line($this->mailBody());

        $message->action(
            $this->forDonor ? 'Go to my dashboard' : 'View elder profile',
            $this->forDonor
                ? route('donors.dashboard')
                : route('elders.show', $this->proposal->elder_id)
        );

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sponsorship.proposal.' . $this->event,
            'proposal_id' => $this->proposal->id,
            'elder_id' => $this->proposal->elder_id,
            'elder_name' => $this->proposal->elder?->name,
            'donor_id' => $this->proposal->donor_id,
            'donor_name' => $this->proposal->donor?->name,
            'event' => $this->event,
            'message' => $this->mailBody(),
        ];
    }

    protected function mailSubject(): string
    {
        return match ($this->event) {
            'accepted' => 'Sponsorship proposal accepted',
            'declined' => 'Sponsorship proposal declined',
            'cancelled' => 'Sponsorship proposal cancelled',
            'expired' => 'Sponsorship proposal expired',
            default => 'Sponsorship proposal update',
        };
    }

    protected function mailBody(): string
    {
        $elderName = $this->proposal->elder?->name ?? 'the elder';
        $actor = $this->actorName ?? 'The Mekodonia team';

        return match ($this->event) {
            'accepted' => $this->forDonor
                ? 'Your sponsorship for ' . $elderName . ' is now active. Thank you for confirming so quickly.'
                : $this->proposal->donor?->name . ' accepted the match invitation for ' . $elderName . '.',
            'declined' => $this->forDonor
                ? 'The proposal for ' . $elderName . ' has been marked as declined.'
                : $this->proposal->donor?->name . ' declined the match invitation for ' . $elderName . '.',
            'cancelled' => $this->forDonor
                ? $actor . ' cancelled the match invitation for ' . $elderName . '. Feel free to browse other elders any time.'
                : 'The proposal for ' . $elderName . ' was cancelled.',
            'expired' => $this->forDonor
                ? 'The invitation to sponsor ' . $elderName . ' expired without a response. You can submit a new pledge from your dashboard.'
                : 'The proposal for ' . $elderName . ' expired without a donor response.',
            default => 'There was an update to the match invitation for ' . $elderName . '.',
        };
    }
}
