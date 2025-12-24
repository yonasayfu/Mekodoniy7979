<?php

namespace App\Console\Commands;

use App\Models\OutboundMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOutboundMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbound:cleanup
                            {--days-sent=90 : Number of days to keep sent messages}
                            {--days-failed=30 : Number of days to keep failed messages}
                            {--dry-run : Run in dry-run mode to see what would be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old outbound messages from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysSent = (int) $this->option('days-sent');
        $daysFailed = (int) $this->option('days-failed');
        $dryRun = $this->option('dry-run');

        $this->info('Starting outbound messages cleanup...');
        $this->line("Keeping sent messages from the last {$daysSent} days");
        $this->line("Keeping failed messages from the last {$daysFailed} days");

        // Clean up sent messages
        $sentCutoff = Carbon::now()->subDays($daysSent);
        $sentQuery = OutboundMessage::whereIn('status', ['sent', 'delivered'])
            ->where('created_at', '<', $sentCutoff);

        $sentCount = $sentQuery->count();

        if ($dryRun) {
            $this->info("[DRY RUN] Would delete {$sentCount} sent messages older than {$sentCutoff->toDateString()}");
        } else {
            $deleted = $sentQuery->delete();
            $this->info("Deleted {$deleted} sent messages older than {$sentCutoff->toDateString()}");
        }

        // Clean up failed messages
        $failedCutoff = Carbon::now()->subDays($daysFailed);
        $failedQuery = OutboundMessage::where('status', 'failed')
            ->where('created_at', '<', $failedCutoff);

        $failedCount = $failedQuery->count();

        if ($dryRun) {
            $this->info("[DRY RUN] Would delete {$failedCount} failed messages older than {$failedCutoff->toDateString()}");
        } else {
            $deleted = $failedQuery->delete();
            $this->info("Deleted {$deleted} failed messages older than {$failedCutoff->toDateString()}");
        }

        // Log the cleanup
        if (!$dryRun) {
            Log::info('Outbound messages cleanup completed', [
                'sent_deleted' => $sentCount,
                'failed_deleted' => $failedCount,
                'sent_cutoff' => $sentCutoff->toDateString(),
                'failed_cutoff' => $failedCutoff->toDateString(),
            ]);
        }

        $this->info('Cleanup completed!');
    }
}
