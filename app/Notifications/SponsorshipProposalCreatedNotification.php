<?php

namespace App\Notifications;

use App\Models\SponsorshipProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SponsorshipProposalCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected SponsorshipProposal $proposal)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $elder = $this->proposal->elder;
        $expiresAt = optional($this->proposal->expires_at)->timezone(config('app.timezone'));

        $message = (new MailMessage)
            ->subject('New Mekodonia match invitation')
            ->greeting('Selam ' . $notifiable->name . ',')
            ->line('The Mekodonia team believes you would be a great match for ' . ($elder?->name ?? 'one of our elders') . '.')
            ->line('Monthly support amount: ' . number_format((float) $this->proposal->amount, 2) . ' ETB (' . ucfirst($this->proposal->frequency) . ')');

        if ($this->proposal->relationship_type) {
            $message->line('Requested relationship: ' . ucfirst($this->proposal->relationship_type));
        }

        if ($expiresAt) {
            $message->line('Please respond before ' . $expiresAt->toDayDateTimeString() . '.');
        } else {
            $message->line('Please respond within the next 72 hours.');
        }

        $message->action('Review invitation', route('donors.dashboard'))
            ->line('Thank you for opening your heart to our elders.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sponsorship.proposal.created',
            'proposal_id' => $this->proposal->id,
            'elder_id' => $this->proposal->elder_id,
            'elder_name' => $this->proposal->elder?->name,
            'amount' => $this->proposal->amount,
            'frequency' => $this->proposal->frequency,
            'relationship_type' => $this->proposal->relationship_type,
            'expires_at' => optional($this->proposal->expires_at)->toIso8601String(),
            'message' => 'New sponsorship invitation awaiting your response.',
        ];
    }
}
