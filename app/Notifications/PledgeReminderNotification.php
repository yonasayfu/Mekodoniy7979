<?php

namespace App\Notifications;

use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PledgeReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Sponsorship $sponsorship;
    protected User $donor;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sponsorship $sponsorship, User $donor)
    {
        $this->sponsorship = $sponsorship;
        $this->donor = $donor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // You can customize channels based on user preferences stored in UserNotificationPreference
        // For simplicity, we'll send email and store in database
        $channels = ['mail', 'database'];

        // If SMS integration is set up (e.g., with Twilio)
        // if ($notifiable->phone && config('services.twilio.sid')) {
        //     $channels[] = 'nexmo'; // or 'twilio' depending on your package
        // }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sponsorship Reminder: Your Support for ' . $this->sponsorship->elder->full_name)
            ->greeting('Hello ' . $this->donor->name . ',')
            ->line('This is a friendly reminder about your upcoming sponsorship payment for ' . $this->sponsorship->elder->full_name . '.')
            ->line('Amount: ' . $this->sponsorship->amount . ' ' . $this->sponsorship->currency)
            ->line('Due Date: ' . $this->sponsorship->next_payment_date->format('M d, Y'))
            ->action('View Your Sponsorship', url('/sponsorships/' . $this->sponsorship->id))
            ->line('Thank you for your continuous support in enriching the life of ' . $this->sponsorship->elder->full_name . '.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sponsorship_id' => $this->sponsorship->id,
            'elder_name' => $this->sponsorship->elder->full_name,
            'amount' => $this->sponsorship->amount,
            'currency' => $this->sponsorship->currency,
            'due_date' => $this->sponsorship->next_payment_date->toDateTimeString(),
            'message' => 'Sponsorship reminder for ' . $this->sponsorship->elder->full_name . '.',
        ];
    }
}