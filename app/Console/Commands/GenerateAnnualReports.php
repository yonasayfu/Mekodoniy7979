<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;

class GenerateAnnualReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-annual {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate annual thank you reports for consistent donors';

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
        $year = $this->argument('year') ?? now()->year;

        $this->info("Generating annual reports for {$year}...");

        $generated = $this->reportService->generateAnnualReports($year);

        $this->info("Generated {$generated} annual reports for {$year}.");

        return Command::SUCCESS;
    }
}
