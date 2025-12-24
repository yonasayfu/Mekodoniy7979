<?php

namespace App\Jobs;

use App\Models\OutboundMessage;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;
use Exception;
use Throwable;
use Illuminate\Support\Str;

class SendOutboundMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [30, 60, 120]; // Retry after 30s, 60s, then 120s

    protected $externalId;

    public function __construct(public OutboundMessage $message)
    {
        $this->onQueue('messages');
    }

    public function handle(MessageService $messageService): void
    {
        // Skip if already sent or being processed
        if (!in_array($this->message->status, ['pending', 'failed'])) {
            return;
        }

        // Mark as sending
        if (!$this->message->markAsSending()) {
            Log::error('Failed to mark message as sending', ['message_id' => $this->message->id]);
            return;
        }

        try {
            switch ($this->message->channel) {
                case 'email':
                    $this->sendEmail();
                    break;
                case 'sms':
                    $this->sendSms();
                    break;
                case 'whatsapp':
                    $this->sendWhatsApp();
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported channel: {$this->message->channel}");
            }

            $this->message->markAsSent($this->getExternalId() ?? (string) Str::uuid());
            
        } catch (Exception $e) {
            Log::error('Failed to send message', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->message->markAsFailed($e->getMessage());
            
            // Re-throw to allow for retries
            throw $e;
        }
    }

    protected function sendEmail(): void
    {
        $mailable = new GenericMail(
            subject: $this->message->subject,
            content: $this->message->content,
            template: $this->message->template,
            templateData: $this->message->template_data ?? []
        );

        Mail::to($this->message->to)->send($mailable);
    }

    protected function sendSms(): void
    {
        // TODO: Implement SMS sending logic
        // This would integrate with your SMS provider (e.g., Twilio, AWS SNS, etc.)
        // For now, we'll just log it
        Log::info('SMS sent', [
            'to' => $this->message->to,
            'content' => $this->message->content
        ]);

        // Simulate external ID
        $this->externalId = 'sms-' . now()->timestamp;
    }

    protected function sendWhatsApp(): void
    {
        // TODO: Implement WhatsApp sending logic
        // This would integrate with WhatsApp Business API or a service like Twilio
        // For now, we'll just log it
        Log::info('WhatsApp message sent', [
            'to' => $this->message->to,
            'content' => $this->message->content
        ]);

        // Simulate external ID
        $this->externalId = 'wa-' . now()->timestamp;
    }

    protected function getExternalId(): ?string
    {
        return $this->externalId ?? null;
    }

    public function failed(Throwable $exception): void
    {
        Log::error('SendOutboundMessage job failed', [
            'message_id' => $this->message->id,
            'error' => $exception->getMessage()
        ]);

        $this->message->markAsFailed($exception->getMessage());
    }
}
