<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DataExport;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'active')->count();
        $inactiveStaff = $totalStaff - $activeStaff;
        $staffCreatedThisMonth = Staff::where('created_at', '>=', $startOfMonth)->count();
        $staffCreatedLastMonth = Staff::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count();

        $totalUsers = User::count();
        $usersWithTwoFactor = User::whereNotNull('two_factor_secret')->count();

        $completedExportsThisWeek = DataExport::where('status', DataExport::STATUS_COMPLETED)
            ->where('completed_at', '>=', $now->copy()->startOfWeek())
            ->count();

        $metrics = [
            [
                'label' => 'Total Staff',
                'value' => $totalStaff,
                'change' => $this->formatChange($staffCreatedThisMonth, $staffCreatedLastMonth),
                'description' => 'New hires compared to last month',
                'icon' => 'Users',
            ],
            [
                'label' => 'Active Staff',
                'value' => $activeStaff,
                'change' => null,
                'description' => sprintf('%d inactive', max($inactiveStaff, 0)),
                'icon' => 'UserCheck',
            ],
            [
                'label' => 'System Users',
                'value' => $totalUsers,
                'change' => [
                    'direction' => $totalUsers > 0 ? 'up' : 'flat',
                    'percentage' => $totalUsers > 0 ? round(($usersWithTwoFactor / max($totalUsers, 1)) * 100, 1) : 0,
                    'label' => '2FA coverage',
                ],
                'description' => sprintf('%d users with 2FA', $usersWithTwoFactor),
                'icon' => 'ShieldCheck',
            ],
            [
                'label' => 'Weekly Exports',
                'value' => $completedExportsThisWeek,
                'change' => null,
                'description' => 'Completed downloads this week',
                'icon' => 'Download',
            ],
        ];

        $staffTrend = $this->buildStaffTrend($now);



        $recentExports = DataExport::with('user:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->map(function (DataExport $export) {
                return [
                    'id' => $export->uuid,
                    'name' => $export->name,
                    'type' => ucfirst($export->type),
                    'status' => ucfirst($export->status),
                    'completed_at' => optional($export->completed_at)->toDateTimeString(),
                    'requested_by' => optional($export->user)->name,
                ];
            });

        $recentActivity = ActivityLog::with('causer:id,name')
            ->latest()
            ->take(6)
            ->get()
            ->map(function (ActivityLog $activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'action' => $activity->action,
                    'causer' => optional($activity->causer)->name,
                    'occurred_at' => optional($activity->created_at)->toDateTimeString(),
                ];
            });

        return Inertia::render('Dashboard', [
            'metrics' => $metrics,
            'staffTrend' => $staffTrend,
            'recentExports' => $recentExports,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function formatChange(int $current, int $previous): ?array
    {
        if ($current === 0 && $previous === 0) {
            return null;
        }

        if ($previous === 0) {
            return [
                'direction' => 'up',
                'percentage' => 100,
                'label' => 'vs last month',
            ];
        }

        $difference = (($current - $previous) / max($previous, 1)) * 100;

        return [
            'direction' => $difference === 0 ? 'flat' : ($difference > 0 ? 'up' : 'down'),
            'percentage' => round(abs($difference), 1),
            'label' => 'vs last month',
        ];
    }

    /**
     * Construct a month-over-month staff onboarding trend for the past six months.
     */
    private function buildStaffTrend(Carbon $now): array
    {
        $start = $now->copy()->subMonths(5)->startOfMonth();

        $driver = config('database.default');
        $dateFormat = $driver === 'mysql'
            ? 'DATE_FORMAT(created_at, "%Y-%m")'
            : 'TO_CHAR(created_at, \'YYYY-MM\')';

        $raw = Staff::selectRaw("{$dateFormat} as period, COUNT(*) as count")
            ->where('created_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period');

        $labels = [];
        $series = [];
        $cursor = $start->copy();

        while ($cursor <= $now) {
            $periodKey = $cursor->format('Y-m');
            $labels[] = $cursor->format('M Y');
            $series[] = (int) ($raw[$periodKey] ?? 0);
            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }


}
