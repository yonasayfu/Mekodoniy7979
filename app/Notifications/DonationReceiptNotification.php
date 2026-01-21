<?php

namespace App\Notifications;

use App\Models\Donation;
use App\Support\Services\DonationReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class DonationReceiptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Donation $donation,
        protected string $receiptPath
    ) {
        //
    }

    public static function fromService(Donation $donation, DonationReceiptService $service): self
    {
        $path = $service->ensureReceipt($donation);

        return new self($donation, $path);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Mekodonia Donation Receipt')
            ->greeting('Selam ' . $notifiable->name . ',')
            ->line('Thank you for your recent donation to Mekodonia.')
            ->line('Amount: ' . number_format((float) $this->donation->amount, 2) . ' ' . ($this->donation->currency ?? 'ETB'))
            ->line('Receipt reference: ' . $this->donation->receipt_uuid)
            ->line('You can download the attached receipt or view it any time online.');

        if (Storage::disk('public')->exists($this->receiptPath)) {
            $message->attach(
                Storage::disk('public')->path($this->receiptPath),
                [
                    'as' => 'mekodonia_receipt_' . $this->donation->id . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
        }

        $message->action('View Receipt', route('receipts.show', $this->donation->receipt_uuid));

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'donation.receipt',
            'donation_id' => $this->donation->id,
            'receipt_uuid' => $this->donation->receipt_uuid,
            'amount' => $this->donation->amount,
            'currency' => $this->donation->currency,
            'message' => 'Your donation receipt is ready.',
        ];
    }
}
