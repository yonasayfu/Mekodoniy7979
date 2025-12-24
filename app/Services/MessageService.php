<?php

namespace App\Services;

use App\Jobs\SendOutboundMessage;
use App\Models\OutboundMessage;
use App\Models\User;
use App\Models\Elder;
use Illuminate\Support\Str;

class MessageService
{
    public function sendEmail(User|Elder|string $recipient, string $subject, string $content, ?string $template = null, ?array $templateData = []): OutboundMessage
    {
        $to = $this->resolveRecipientEmail($recipient);
        $userId = $this->resolveUserId($recipient);
        $elderId = $this->resolveElderId($recipient);

        return $this->createMessage(
            channel: 'email',
            to: $to,
            subject: $subject,
            content: $content,
            template: $template,
            templateData: $templateData,
            userId: $userId,
            elderId: $elderId
        );
    }

    public function sendSms(User|Elder|string $recipient, string $content, ?string $template = null, ?array $templateData = []): OutboundMessage
    {
        $to = $this->resolveRecipientPhone($recipient);
        $userId = $this->resolveUserId($recipient);
        $elderId = $this->resolveElderId($recipient);

        return $this->createMessage(
            channel: 'sms',
            to: $to,
            content: $content,
            template: $template,
            templateData: $templateData,
            userId: $userId,
            elderId: $elderId
        );
    }

    public function sendWhatsApp(User|Elder|string $recipient, string $content, ?string $template = null, ?array $templateData = []): OutboundMessage
    {
        $to = $this->resolveRecipientPhone($recipient);
        $userId = $this->resolveUserId($recipient);
        $elderId = $this->resolveElderId($recipient);

        return $this->createMessage(
            channel: 'whatsapp',
            to: $to,
            content: $content,
            template: $template,
            templateData: $templateData,
            userId: $userId,
            elderId: $elderId
        );
    }

    protected function createMessage(
        string $channel,
        string $to,
        string $content,
        ?string $subject = null,
        ?string $template = null,
        ?array $templateData = [],
        ?int $userId = null,
        ?int $elderId = null
    ): OutboundMessage {
        $message = OutboundMessage::create([
            'user_id' => $userId,
            'elder_id' => $elderId,
            'channel' => $channel,
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'template' => $template,
            'template_data' => $templateData,
            'status' => 'pending',
        ]);

        // Dispatch job to send the message
        SendOutboundMessage::dispatch($message);

        return $message;
    }

    // Helper methods to resolve recipient information
    protected function resolveRecipientEmail($recipient): ?string
    {
        if ($recipient instanceof User || $recipient instanceof Elder) {
            return $recipient->email;
        }
        return filter_var($recipient, FILTER_VALIDATE_EMAIL) ? $recipient : null;
    }

    protected function resolveRecipientPhone($recipient): ?string
    {
        if ($recipient instanceof User || $recipient instanceof Elder) {
            return $recipient->phone;
        }
        return is_string($recipient) ? $recipient : null;
    }

    protected function resolveUserId($recipient): ?int
    {
        return $recipient instanceof User ? $recipient->id : null;
    }

    protected function resolveElderId($recipient): ?int
    {
        return $recipient instanceof Elder ? $recipient->id : null;
    }

    // Retry failed messages
    public function retryFailedMessages(int $hoursOld = 24, int $limit = 100): int
    {
        $count = 0;
        $messages = OutboundMessage::failed()
            ->olderThan($hoursOld)
            ->where('attempts', '<', config('outbound.max_attempts', 3))
            ->limit($limit)
            ->get();

        foreach ($messages as $message) {
            if ($message->canRetry()) {
                SendOutboundMessage::dispatch($message);
                $count++;
            }
        }

        return $count;
    }
}
