<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminDashboardExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected array $row;

    public function __construct(array $metrics, array $filters = [])
    {
        $relationshipDistribution = collect(Arr::get($metrics, 'relationship_distribution', []))
            ->map(fn ($count, $relationship) => ucfirst($relationship) . ': ' . $count)
            ->values()
            ->implode(', ');

        $this->row = [
            'Branch Filter' => $filters['branch_id'] ? '#'.$filters['branch_id'] : 'All branches',
            'Date Range (days)' => $filters['date_range'] ?? 30,
            'Total Sponsorships' => Arr::get($metrics, 'total_sponsorships', 0),
            'Total Elders' => Arr::get($metrics, 'total_elders', 0),
            'Total Donors' => Arr::get($metrics, 'total_donors', 0),
            'Promise Fulfillment %' => round(Arr::get($metrics, 'promise_fulfillment_rate', 0), 2),
            'Missed Payments' => Arr::get($metrics, 'missed_payments', 0),
            'Monthly Expenses Covered (ETB)' => number_format((float) Arr::get($metrics, 'monthly_expenses_covered', 0), 2),
            'Guest Donations Today (ETB)' => number_format((float) Arr::get($metrics, 'guest_donations_today', 0), 2),
            'Relationship Distribution' => $relationshipDistribution ?: 'No data',
            'Featured Matches' => count(Arr::get($metrics, 'featured_matches', [])),
            'Monthly Trend' => $this->formatTrend(Arr::get($metrics, 'monthly_trend', [])),
        ];
    }

    public function array(): array
    {
        return [array_values($this->row)];
    }

    public function headings(): array
    {
        return array_keys($this->row);
    }

    protected function formatTrend(array $trend): string
    {
        if (empty($trend)) {
            return 'No trend data';
        }

        return collect($trend)
            ->map(fn ($point) => sprintf('%s: %s ETB', $point['label'], number_format($point['amount'] ?? 0, 2)))
            ->implode(' | ');
    }
}
