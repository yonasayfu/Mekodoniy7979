<?php

namespace App\Console\Commands;

use App\Models\OutboundMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OutboundStatusCommand extends Command
{
    protected $signature = 'outbound:status
                            {--status= : Filter by status (pending, sending, sent, delivered, failed)}
                            {--channel= : Filter by channel (email, sms, whatsapp)}
                            {--hours=24 : Show messages from the last X hours}
                            {--limit=20 : Number of messages to show}';

    protected $description = 'Show status of outbound messages';

    public function handle()
    {
        $query = OutboundMessage::query()
            ->select([
                'id',
                'channel',
                'to',
                'subject',
                'status',
                'attempts',
                'created_at',
                'sent_at',
                'failed_at',
                'error_message',
            ])
            ->where('created_at', '>=', Carbon::now()->subHours($this->option('hours')))
            ->orderBy('created_at', 'desc');

        if ($status = $this->option('status')) {
            $query->where('status', $status);
        }

        if ($channel = $this->option('channel')) {
            $query->where('channel', $channel);
        }

        $messages = $query->limit($this->option('limit'))->get();

        if ($messages->isEmpty()) {
            $this->info('No messages found matching the criteria.');
            return 0;
        }

        $this->showSummary($messages);
        $this->newLine();
        $this->showMessagesTable($messages);

        return 0;
    }

    protected function showSummary(Collection $messages): void
    {
        $total = $messages->count();
        $byStatus = $messages->groupBy('status')->map->count()->sortDesc();
        $byChannel = $messages->groupBy('channel')->map->count()->sortDesc();

        $this->line("Showing {$total} messages");
        $this->line("Status: " . $byStatus->map(fn($c, $s) => "$s: $c")->join(', '));
        $this->line("Channels: " . $byChannel->map(fn($c, $ch) => "$ch: $c")->join(', '));
    }

    protected function showMessagesTable(Collection $messages): void
    {
        $this->table(
            ['ID', 'Channel', 'To', 'Subject', 'Status', 'Attempts', 'Created', 'Sent', 'Error'],
            $messages->map(function ($msg) {
                return [
                    $msg->id,
                    $msg->channel,
                    $this->truncate($msg->to, 20),
                    $this->truncate($msg->subject, 30),
                    $this->formatStatus($msg->status),
                    $msg->attempts . '/' . config('outbound.queue.tries', 3),
                    $msg->created_at->diffForHumans(),
                    $msg->sent_at ? $msg->sent_at->diffForHumans() : '-',
                    $msg->error_message ? $this->truncate($msg->error_message, 30) : '-',
                ];
            })
        );
    }

    protected function formatStatus(string $status): string
    {
        return match ($status) {
            'pending' => "<fg=yellow>pending</>",
            'sending' => "<fg=blue>sending</>",
            'sent' => "<fg=green>sent</>",
            'delivered' => "<fg=green>delivered</>",
            'failed' => "<fg=red>failed</>",
            default => $status,
        };
    }

    protected function truncate(?string $text, int $length): string
    {
        if (!$text) {
            return '-';
        }
        return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
    }
}
