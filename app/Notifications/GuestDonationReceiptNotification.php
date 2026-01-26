<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class GuestDonationReceiptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Donation $donation,
        protected string $receiptPath,
        protected ?string $memberPassword = null,
        protected ?string $memberPhone = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $elderName = $this->donation->elder?->name ?? 'one of our elders';

        $message = (new MailMessage)
            ->subject('Mekodonia Donation Receipt')
            ->greeting('Selam ' . ($this->donation->guest_name ?: 'Friend') . ',')
            ->line('Thank you for supporting ' . $elderName . '.')
            ->line('We have recorded your guest donation of ' . number_format((float) $this->donation->amount, 2) . ' ETB.')
            ->line('Please keep the attached receipt for your records.')
            ->line('We will notify you once the branch confirms the funds.');

        if ($this->memberPhone && $this->memberPassword) {
            $message->line(sprintf(
                'You can log in with phone %s and the password %s to manage your donations or update the pledge.',
                $this->memberPhone,
                $this->memberPassword,
            ));
        }

        if (Storage::disk('public')->exists($this->receiptPath)) {
            $message->attach(
                Storage::disk('public')->path($this->receiptPath),
                [
                    'as' => 'mekodonia_receipt_' . $this->donation->id . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
        }

        return $message;
    }
}
