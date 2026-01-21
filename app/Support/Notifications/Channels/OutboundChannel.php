<?php

namespace App\Support\Notifications\Channels;

use App\Models\OutboundMessage;
use App\Services\MessageService;
use Illuminate\Notifications\Notification;

class OutboundChannel
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function send(mixed $notifiable, Notification $notification): ?OutboundMessage
    {
        if (! method_exists($notification, 'toOutbound')) {
            return null;
        }

        $payload = $notification->toOutbound($notifiable);

        $channel = $payload['channel'] ?? 'email';
        $content = $payload['content'] ?? '';
        $subject = $payload['subject'] ?? null;
        $template = $payload['template'] ?? null;
        $templateData = $payload['template_data'] ?? [];

        return match ($channel) {
            'sms' => $this->messageService->sendSms($notifiable, $content, $template, $templateData),
            'whatsapp' => $this->messageService->sendWhatsApp($notifiable, $content, $template, $templateData),
            default => $this->messageService->sendEmail($notifiable, $subject ?? 'Update', $content, $template, $templateData),
        };
    }
}
