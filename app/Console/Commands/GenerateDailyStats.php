<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\DailyStat;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\Pledge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDailyStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-daily-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store daily financial and operational statistics for all branches.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $this->info("Generating stats for: " . $today->toDateString());

        // 1. Get all branches
        $branches = Branch::all();
        
        // Add a "null" branch to represent Global/Headquarters stats
        $branchIds = $branches->pluck('id')->push(null);

        foreach ($branchIds as $branchId) {
            $this->generateStatsForBranch($branchId, $today);
        }

        $this->info('Daily stats generated successfully.');
    }

    private function generateStatsForBranch($branchId, $date)
    {
        // Scopes
        $donationQuery = Donation::query()->where('status', 'approved');
        $pledgeQuery = Pledge::query()->where('status', 'active');
        $elderQuery = Elder::query();
        $donorQuery = User::whereHas('roles', fn ($q) => $q->where('name', 'donor'));

        if ($branchId) {
            // Important: Use withoutGlobalScope to get accurate global stats
            $donationQuery->where('branch_id', $branchId);
            $pledgeQuery->where('branch_id', $branchId);
            $elderQuery->where('branch_id', $branchId);
            $donorQuery->where('branch_id', $branchId);
        }

        // Calculations
        // 1. Total Collected (Cumulative)
        $totalCollected = $donationQuery->sum('amount');

        // 2. Total Pledged (Monthly value of all active pledges)
        $totalPledged = $pledgeQuery->sum('amount');

        // 3. Operational Stats
        $activeElders = $elderQuery->count();
        
        // Matched Elders: Elders who have at least one active pledge
        $matchedElders = Elder::whereHas('pledges', function ($q) {
            $q->where('status', 'active');
        });
        if ($branchId) {
            $matchedElders->where('branch_id', $branchId);
        }
        $matchedEldersCount = $matchedElders->count();

        $activeDonors = $donorQuery->count();

        // Store in DB
        DailyStat::updateOrCreate(
            ['branch_id' => $branchId, 'date' => $date],
            [
                'total_pledged' => $totalPledged,
                'total_collected' => $totalCollected,
                'gap' => max(0, $totalPledged - $totalCollected), // This gap logic might need review
                'active_elders' => $activeElders,
                'matched_elders' => $matchedEldersCount,
                'active_donors' => $activeDonors,
            ]
        );
    }
}