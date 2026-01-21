<?php

namespace App\Http\Controllers;

use App\Models\OutboundMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class OutboundMessageController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('notifications.view');

        $filters = $request->only(['channel', 'status']);

        $messages = OutboundMessage::query()
            ->when($filters['channel'] ?? null, fn ($query, $channel) => $query->where('channel', $channel))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (OutboundMessage $message) => [
                'id' => $message->id,
                'channel' => $message->channel,
                'to' => $message->to,
                'subject' => $message->subject,
                'status' => $message->status,
                'attempts' => $message->attempts,
                'sent_at' => optional($message->sent_at)->toDateTimeString(),
                'failed_at' => optional($message->failed_at)->toDateTimeString(),
                'error_message' => $message->error_message,
            ]);

        return Inertia::render('Outbound/Index', [
            'messages' => $messages,
            'filters' => $filters,
            'channels' => ['email', 'sms', 'whatsapp'],
            'statuses' => ['pending', 'sending', 'sent', 'delivered', 'failed'],
        ]);
    }
}
