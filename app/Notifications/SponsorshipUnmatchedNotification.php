<?php

namespace App\Notifications;

use App\Models\Sponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SponsorshipUnmatchedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Sponsorship $sponsorship,
        protected ?string $reason = null
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $elderName = $this->sponsorship->elder?->name ?? 'one of our elders';

        $message = (new MailMessage)
            ->subject('Sponsorship updated for ' . $elderName)
            ->greeting('Selam ' . $notifiable->name . ',')
            ->line('The Mekodonia team has closed your sponsorship for ' . $elderName . '.');

        if ($this->reason) {
            $message->line('Reason: ' . $this->reason);
        }

        $message->line('The elder has been returned to the pool so another donor can step in.');

        $message->action('Visit my dashboard', route('donors.dashboard'));

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sponsorship.unmatched',
            'sponsorship_id' => $this->sponsorship->id,
            'elder_id' => $this->sponsorship->elder_id,
            'elder_name' => $this->sponsorship->elder?->name,
            'reason' => $this->reason,
            'message' => 'Your sponsorship for ' . ($this->sponsorship->elder?->name ?? 'an elder') . ' has been closed.',
        ];
    }
}
