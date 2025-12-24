<?php

namespace App\Traits;

use App\Services\MessageService;
use App\Models\OutboundMessage;

trait CanReceiveMessages
{
    /**
     * Get the email address for the model.
     */
    public function getEmailForMessages(): ?string
    {
        return $this->email ?? null;
    }

    /**
     * Get the phone number for the model.
     */
    public function getPhoneForMessages(): ?string
    {
        return $this->phone ?? $this->phone_number ?? null;
    }

    /**
     * Send an email to the model.
     */
    public function sendEmail(string $subject, string $content, ?string $template = null, array $templateData = []): OutboundMessage
    {
        return app(MessageService::class)->sendEmail(
            recipient: $this,
            subject: $subject,
            content: $content,
            template: $template,
            templateData: $templateData
        );
    }

    /**
     * Send an SMS to the model.
     */
    public function sendSms(string $content, ?string $template = null, array $templateData = []): OutboundMessage
    {
        return app(MessageService::class)->sendSms(
            recipient: $this,
            content: $content,
            template: $template,
            templateData: $templateData
        );
    }

    /**
     * Send a WhatsApp message to the model.
     */
    public function sendWhatsApp(string $content, ?string $template = null, array $templateData = []): OutboundMessage
    {
        return app(MessageService::class)->sendWhatsApp(
            recipient: $this,
            content: $content,
            template: $template,
            templateData: $templateData
        );
    }

    /**
     * Get all messages sent to this model.
     */
    public function messages()
    {
        return $this->morphMany(OutboundMessage::class, 'recipient');
    }

    /**
     * Get the most recent message sent to this model.
     */
    public function latestMessage()
    {
        return $this->morphOne(OutboundMessage::class, 'recipient')->latestOfMany();
    }
}
