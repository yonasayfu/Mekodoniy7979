<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    use HandlesDataExport;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ActivityLog::class);

        $activityLogs = ActivityLog::with('causer:id,name,email')
            ->latest()
            ->paginate(10);

        return Inertia::render('ActivityLogs/Index', [
            'activityLogs' => $activityLogs,
            'breadcrumbs' => [
                ['title' => 'Activity Logs', 'href' => route('activity-logs.index')],
            ],
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        return $this->handleExport(
            $request,
            ActivityLog::class,
            ExportConfig::activityLogs(),
            [
                'label' => 'Activity Log Audit Export',
                'type' => 'activity_logs',
            ],
        );
    }
}
