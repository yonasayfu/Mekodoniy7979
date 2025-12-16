<?php

namespace App\Notifications;

use App\Models\Pledge;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PledgeReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Pledge $pledge;
    protected User $donor;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pledge $pledge, User $donor)
    {
        $this->pledge = $pledge;
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
            ->subject('Pledge Reminder: Your Support for ' . $this->pledge->elder->full_name)
            ->greeting('Hello ' . $this->donor->name . ',')
            ->line('This is a friendly reminder about your upcoming pledge payment for ' . $this->pledge->elder->full_name . '.')
            ->line('Amount: ' . $this->pledge->amount . ' ' . $this->pledge->currency)
            ->line('Due Date: ' . $this->pledge->next_payment_date->format('M d, Y'))
            ->action('View Your Pledge', url('/pledges/' . $this->pledge->id))
            ->line('Thank you for your continuous support in enriching the life of ' . $this->pledge->elder->full_name . '.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pledge_id' => $this->pledge->id,
            'elder_name' => $this->pledge->elder->full_name,
            'amount' => $this->pledge->amount,
            'currency' => $this->pledge->currency,
            'due_date' => $this->pledge->next_payment_date->toDateTimeString(),
            'message' => 'Pledge reminder for ' . $this->pledge->elder->full_name . '.',
        ];
    }
}