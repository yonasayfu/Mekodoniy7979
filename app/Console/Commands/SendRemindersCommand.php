<?php

namespace App\Console\Commands;

use App\Models\Sponsorship;
use App\Models\Visit;
use App\Models\User; // Ensure User model is imported
use App\Notifications\PledgeReminderNotification;
use App\Notifications\VisitReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon; // Import Carbon for date manipulation

class SendRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming visits and pledges.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting reminder process...');
        Log::info('Reminder command initiated.');

        // --- Pledge Reminders ---
        $this->info('Checking for upcoming pledge reminders...');
        $upcomingSponsorships = Sponsorship::where('status', 'active')
            ->whereNotNull('next_payment_date')
            ->where('next_payment_date', '<=', Carbon::now()->addDays(7)) // Remind 7 days before due date
            ->get();

        foreach ($upcomingSponsorships as $sponsorship) {
            /** @var User $donor */
            $donor = $sponsorship->user;

            if ($donor) {
                // In a real system, you'd check if a reminder was recently sent
                // to avoid spamming the user.
                $donor->notify(new PledgeReminderNotification($sponsorship, $donor));
                $this->info("Sent pledge reminder to {$donor->email} for pledge ID: {$sponsorship->id}.");
                Log::info("Pledge reminder sent for pledge ID: {$sponsorship->id} to user ID: {$donor->id}.");
            }
        }
        $this->info('Pledge reminders processed.');

        // --- Visit Reminders ---
        $this->info('Checking for upcoming visit reminders...');
        $upcomingVisits = Visit::where('status', 'approved')
            ->where('visit_date', '>=', Carbon::now())
            ->where('visit_date', '<=', Carbon::now()->addDays(3)) // Remind 3 days before visit
            ->get();

        foreach ($upcomingVisits as $visit) {
            /** @var User $visitor */
            $visitor = $visit->user;

            if ($visitor) {
                // In a real system, you'd check if a reminder was recently sent.
                $visitor->notify(new VisitReminderNotification($visit, $visitor));
                $this->info("Sent visit reminder to {$visitor->email} for visit ID: {$visit->id}.");
                Log::info("Visit reminder sent for visit ID: {$visit->id} to user ID: {$visitor->id}.");
            }
        }
        $this->info('Visit reminders processed.');

        $this->info('Reminder process completed.');
        Log::info('Reminder command finished.');
    }
}