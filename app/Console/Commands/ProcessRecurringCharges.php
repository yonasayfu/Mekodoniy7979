<?php

namespace App\Console\Commands;

use App\Models\Sponsorship;
use App\Support\Services\RecurringPaymentService;
use Illuminate\Console\Command;

class ProcessRecurringCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:charge-due {--dry-run : Only list the sponsorships that would be charged}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charge all active recurring sponsorships whose next billing date is due.';

    /**
     * Execute the console command.
     */
    public function handle(RecurringPaymentService $recurringPaymentService): int
    {
        $sponsorships = Sponsorship::with(['user', 'elder'])
            ->where('status', 'active')
            ->whereNotNull('subscription_id')
            ->whereDate('next_billing_date', '<=', now()->toDateString())
            ->get();

        if ($sponsorships->isEmpty()) {
            $this->info('No recurring sponsorships are due today.');

            return self::SUCCESS;
        }

        $this->info("Processing {$sponsorships->count()} recurring sponsorship(s).");

        foreach ($sponsorships as $sponsorship) {
            $label = sprintf(
                '#%d · %s → %s',
                $sponsorship->id,
                $sponsorship->user->name ?? 'Unknown donor',
                $sponsorship->elder->name ?? 'Unknown elder'
            );

            if ($this->option('dry-run')) {
                $this->line("DRY-RUN: {$label} would be charged {$sponsorship->amount} {$sponsorship->currency}");
                continue;
            }

            $result = $recurringPaymentService->processScheduledPayment($sponsorship);

            if ($result['status'] === 'completed') {
                $this->info("Charged {$label}. Transaction {$result['transaction_id'] ?? 'n/a'}");
            } else {
                $this->warn("Failed to charge {$label}: {$result['message']}");
            }
        }

        return self::SUCCESS;
    }
}
