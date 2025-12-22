<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;

class CheckMonthlySponsorships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorships:check-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for missed monthly sponsorship payments.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking monthly promise fulfillment...');

        $missedPayments = $this->reportService->checkMonthlyPromises();

        if (count($missedPayments) > 0) {
            $this->warn("Found " . count($missedPayments) . " missed payments");
            foreach ($missedPayments as $sponsorship) {
                $this->line("- {$sponsorship->user->name}: {$sponsorship->elder->first_name} {$sponsorship->elder->last_name}");
            }
        } else {
            $this->info('All promises were fulfilled this month!');
        }

        $this->info('Monthly promise check completed.');
    }
}
