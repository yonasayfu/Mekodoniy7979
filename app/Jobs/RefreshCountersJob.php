<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CountersService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshCountersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ?int $userId = null)
    {
    }

    public function handle(CountersService $counters): void
    {
        $counters->warmWelcomeCounters();

        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $counters->warmDonorCounters($user);
            }
        }
    }
}
