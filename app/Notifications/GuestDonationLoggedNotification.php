<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

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
        $this->donation->loadMissing(['branch', 'elder']);
        $elder = $this->donation->elder;
        $fundingGoal = $elder->funding_goal ?? 0;
        $fundingReceived = $elder->funding_received ?? 0;
        $fundingProgress = $fundingGoal > 0
            ? min($fundingReceived / $fundingGoal, 1)
            : 0;
        $fundingNeeded = max($fundingGoal - $fundingReceived, 0);

        $dashboardUrl = route('sponsorships.index');

        return [
            'type' => 'donation.guest_recorded',
            'donation_id' => $this->donation->id,
            'branch_id' => $this->donation->branch_id,
            'branch_name' => optional($this->donation->branch)->name,
            'amount' => $this->donation->amount,
            'currency' => $this->donation->currency,
            'guest_name' => $this->donation->guest_name,
            'payment_gateway' => $this->donation->payment_gateway,
            'payment_status' => $this->donation->payment_status,
            'cadence' => $this->donation->cadence,
            'deduction_schedule' => $this->donation->deduction_schedule,
            'receipt_url' => $this->receiptUrl,
            'mandate_url' => $this->donation->mandate_path
                ? Storage::disk('public')->url($this->donation->mandate_path)
                : null,
            'payment_reference' => $this->donation->payment_reference,
            'notes' => $this->donation->notes,
            'elder_name' => optional($this->donation->elder)->name,
            'donation_type' => $this->donation->donation_type,
            'elder_funding_goal' => $fundingGoal,
            'elder_funding_received' => $fundingReceived,
            'elder_funding_progress' => $fundingProgress,
            'elder_funding_needed' => $fundingNeeded,
            'elder_is_funded' => $fundingGoal > 0 && $fundingReceived >= $fundingGoal,
            'message' => sprintf(
                'Guest donation received from %s (%s ETB).',
                $this->donation->guest_name ?: 'Anonymous',
                number_format((float) $this->donation->amount, 2)
            ),
            'url' => $dashboardUrl,
        ];
    }
}
