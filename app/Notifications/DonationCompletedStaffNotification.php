<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DonationCompletedStaffNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Donation $donation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'donation.completed',
            'donation_id' => $this->donation->id,
            'amount' => $this->donation->amount,
            'currency' => $this->donation->currency,
            'branch_id' => $this->donation->branch_id,
            'message' => 'A donation was completed.',
            'url' => route('reports.donations'),
        ];
    }
}
