<?php

namespace App\Notifications;

use App\Models\Visit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Visit $visit;
    protected User $visitor;

    /**
     * Create a new notification instance.
     */
    public function __construct(Visit $visit, User $visitor)
    {
        $this->visit = $visit;
        $this->visitor = $visitor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Customize channels based on user preferences if desired
        $channels = ['mail', 'database'];

        // If SMS integration is set up
        // if ($notifiable->phone && config('services.twilio.sid')) {
        //     $channels[] = 'nexmo';
        // }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Visit Reminder: Your Upcoming Visit to ' . $this->visit->elder->full_name)
            ->greeting('Hello ' . $this->visitor->name . ',')
            ->line('This is a reminder for your upcoming visit to ' . $this->visit->elder->full_name . '.')
            ->line('Date: ' . $this->visit->visit_date->format('M d, Y'))
            ->line('Time: ' . $this->visit->visit_date->format('h:i A'))
            ->line('Purpose: ' . $this->visit->purpose)
            ->action('View Your Visit Details', url('/visits/' . $this->visit->id))
            ->line('We appreciate your dedication to ' . $this->visit->elder->full_name . '.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'visit_id' => $this->visit->id,
            'elder_name' => $this->visit->elder->full_name,
            'visit_date' => $this->visit->visit_date->toDateTimeString(),
            'purpose' => $this->visit->purpose,
            'message' => 'Visit reminder for ' . $this->visit->elder->full_name . '.',
        ];
    }
}