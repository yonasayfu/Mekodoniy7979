<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GuestDonationLoggedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Donation $donation,
        protected ?string $receiptUrl = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'donation.guest_recorded',
            'donation_id' => $this->donation->id,
            'branch_id' => $this->donation->branch_id,
            'amount' => $this->donation->amount,
            'currency' => $this->donation->currency,
            'guest_name' => $this->donation->guest_name,
            'receipt_url' => $this->receiptUrl,
            'message' => sprintf(
                'Guest donation received from %s (%s ETB).',
                $this->donation->guest_name ?: 'Anonymous',
                number_format((float) $this->donation->amount, 2)
            ),
            'url' => route('reports.donations'),
        ];
    }
}
