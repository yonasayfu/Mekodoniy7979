<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;

class CheckMonthlyPromises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promises:check-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check monthly promise fulfillment and send reminders for missed payments';

    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking monthly promise fulfillment...');

        $missedPayments = $this->reportService->checkMonthlyPromises();

        if (count($missedPayments) > 0) {
            $this->warn("Found " . count($missedPayments) . " missed payments");
            foreach ($missedPayments as $pledge) {
                $this->line("- {$pledge->user->name}: {$pledge->elder->first_name} {$pledge->elder->last_name}");
            }
        } else {
            $this->info('All promises were fulfilled this month!');
        }

        $this->info('Monthly promise check completed.');
    }
}
