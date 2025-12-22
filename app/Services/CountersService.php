<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CountersService
{
    public function forgetWelcomeCounters(): void
    {
        Cache::forget($this->welcomeCacheKey());
    }

    public function warmWelcomeCounters(): array
    {
        $data = $this->computeWelcomeCounters();

        Cache::put($this->welcomeCacheKey(), $data, now()->addMinutes(5));

        return $data;
    }

    public function getWelcomeCounters(): array
    {
        return Cache::remember($this->welcomeCacheKey(), now()->addMinutes(5), function () {
            return $this->computeWelcomeCounters();
        });
    }

    public function forgetDonorCounters(int $userId): void
    {
        Cache::forget($this->donorCacheKey($userId));
    }

    public function warmDonorCounters(User $user): array
    {
        $data = $this->computeDonorCounters($user);

        Cache::put($this->donorCacheKey($user->id), $data, now()->addMinutes(5));

        return $data;
    }

    public function getDonorCounters(User $user): array
    {
        return Cache::remember($this->donorCacheKey($user->id), now()->addMinutes(5), function () use ($user) {
            return $this->computeDonorCounters($user);
        });
    }

    private function welcomeCacheKey(): string
    {
        return 'counters:welcome:v1';
    }

    private function donorCacheKey(int $userId): string
    {
        return 'counters:donor:v1:' . $userId;
    }

    private function computeWelcomeCounters(): array
    {
        $totalElders = Elder::count();
        $matchedElders = Sponsorship::where('status', 'active')
            ->distinct('elder_id')
            ->count('elder_id');

        $eldersWaiting = max($totalElders - $matchedElders, 0);

        $visitsThisMonth = Visit::whereMonth('visit_date', now()->month)
            ->whereYear('visit_date', now()->year)
            ->where('status', 'completed')
            ->count();

        return [
            'eldersWaiting' => $eldersWaiting,
            'matchedElders' => $matchedElders,
            'visitsThisMonth' => $visitsThisMonth,
        ];
    }

    private function computeDonorCounters(User $user): array
    {
        $totalDonations = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $eldersSupported = Elder::whereHas('donations', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'completed');
        })->count();

        $lastDonation = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        return [
            'total_donations' => $totalDonations,
            'elders_supported' => $eldersSupported,
            'last_donation_human' => $lastDonation ? $lastDonation->created_at->diffForHumans() : null,
        ];
    }
}
