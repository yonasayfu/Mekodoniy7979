<?php

namespace App\Console\Commands;

use App\Models\OutboundMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedOutboundMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbound:retry
                            {--hours=24 : Only retry messages failed within the last X hours}
                            {--limit=50 : Maximum number of messages to retry}
                            {--force : Retry even if max attempts reached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry sending failed outbound messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $this->info("Looking for failed messages from the last {$hours} hours...");

        $query = OutboundMessage::where('status', 'failed')
            ->where('created_at', '>=', Carbon::now()->subHours($hours));

        if (!$force) {
            $maxAttempts = config('outbound.queue.tries', 3);
            $query->where('attempts', '<', $maxAttempts);
            $this->line("Will not retry messages that have reached the maximum of {$maxAttempts} attempts (use --force to override)");
        }

        $failedMessages = $query->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        $count = $failedMessages->count();

        if ($count === 0) {
            $this->info('No failed messages found to retry.');
            return 0;
        }

        if (!$this->confirm("Queue {$count} failed messages for retry?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $retried = 0;
        $skipped = 0;

        foreach ($failedMessages as $message) {
            try {
                // Reset status to pending and clear error message
                $message->update([
                    'status' => 'pending',
                    'error_message' => null,
                    'attempts' => 0, // Reset attempts to allow full retry count
                ]);

                // Dispatch the job
                \App\Jobs\SendOutboundMessage::dispatch($message);
                $retried++;
            } catch (\Exception $e) {
                Log::error('Failed to queue message for retry', [
                    'message_id' => $message->id,
                    'error' => $e->getMessage(),
                ]);
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully queued {$retried} messages for retry." . ($skipped > 0 ? " {$skipped} messages could not be queued." : ""));

        // Log the retry operation
        Log::info('Outbound messages retry completed', [
            'retried' => $retried,
            'skipped' => $skipped,
            'hours' => $hours,
            'limit' => $limit,
        ]);

        return 0;
    }
}
