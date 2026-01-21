<?php

namespace App\Notifications;

use App\Models\AnnualReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnualImpactBookReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected AnnualReport $annualReport;

    public function __construct(AnnualReport $annualReport)
    {
        $this->annualReport = $annualReport;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Annual Impact Book is Ready')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for your consistent support. Your annual Impact Book for ' . $this->annualReport->report_year . ' is ready.')
            ->action('Download Impact Book', route('annual-reports.download', $this->annualReport))
            ->line('We appreciate your continued partnership with Mekodonia.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->annualReport->id,
            'report_year' => $this->annualReport->report_year,
            'message' => 'Your annual Impact Book is ready for download.',
            'url' => route('annual-reports.download', $this->annualReport),
        ];
    }
}
