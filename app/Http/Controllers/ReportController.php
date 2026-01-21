<?php

namespace App\Http\Controllers;

use App\Exports\AdminDashboardExport;
use App\Models\Branch;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\Sponsorship;
use App\Models\AnnualReport;
use App\Models\User;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('reports.view');

        $user = $request->user();

        // 1. Admin View (Super Admin, Branch Admin, or Admin)
        // We check if the user has administrative roles to show the "Brain" dashboard
        if ($user->hasRole('Super Admin') || $user->hasRole('Branch Admin') || $user->hasRole('Admin')) {
            $branchId = $user->branch_id;
            $dateRange = (int) $request->input('date_range', 30);

            // If Super Admin, they might want to see global stats (branchId = null)
            if ($user->hasRole('Super Admin')) {
                $branchId = $request->input('branch_id', null);
            }

            $data = $this->reportService->getEnhancedAdminDashboard($branchId, $dateRange);

            // Get branches for filtering (only for Super Admin)
            $branches = [];
            if ($user->hasRole('Super Admin')) {
                $branches = Branch::select('id', 'name')->get();
            }

            return Inertia::render('Reports/AdminDashboard', [
                'stats' => $data,
                'filters' => [
                    'branch_id' => $request->input('branch_id'),
                    'date_range' => $dateRange,
                ],
                'branches' => $branches,
            ]);
        }

        // 2. Donor View (Heart Dashboard)
        $range = $request->input('range', '30');
        $data = $this->reportService->getDonorImpactData($user, $range);

        $sponsorships = Sponsorship::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('elder')
            ->get();

        $annualReports = AnnualReport::where('user_id', $user->id)
            ->orderBy('report_year', 'desc')
            ->get()
            ->map(function (AnnualReport $report) {
                return [
                    'id' => $report->id,
                    'report_year' => $report->report_year,
                    'pdf_url' => Storage::disk('public')->url($report->pdf_path),
                    'impact_data' => $report->impact_data,
                ];
            });

        return Inertia::render('Reports/DonorImpact', [
            'impact' => $data,
            'filters' => ['range' => $range],
            'sponsorships' => $sponsorships,
            'annual_reports' => $annualReports,
        ]);
    }

    public function donationsReport(Request $request): \Inertia\Response
    {
        $donations = Donation::with(['user', 'elder'])->paginate(20);

        return Inertia::render('Reports/DonationsReport', [
            'donations' => $donations,
        ]);
    }

    /**
     * Generate a personalized "Impact Book" PDF for the authenticated donor.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateImpactBook(Request $request)
    {
        $targetUser = Auth::user();
        if ($request->has('user_id') && Auth::user()->can('reports.generate_impact_book')) {
            $targetUser = User::findOrFail($request->input('user_id'));
        }

        $this->authorize('generateImpactBook', $targetUser);

        $annualReport = $this->reportService->generateImpactBookForUser($targetUser);

        $filename = 'Impact_Book_' . str_replace(' ', '_', $targetUser->name) . '_' . now()->format('Ymd') . '.pdf';

        return Storage::disk('public')->download($annualReport->pdf_path, $filename);
    }

    /**
     * Show detailed report for a specific metric
     */
    public function detailedReport(Request $request)
    {
        $metric = $request->input('metric');
        $branchId = $request->input('branch_id');
        $dateRange = $request->input('date_range', 30);

        $user = $request->user();
        if (!$user->hasRole(['Super Admin', 'Branch Admin', 'Admin'])) {
            abort(403);
        }

        $data = [];

        switch ($metric) {
            case 'promise_fulfillment':
                $data = $this->reportService->getPromiseFulfillmentDetails($branchId, $dateRange);
                break;
            case 'missed_payments':
                $data = $this->reportService->getMissedPaymentsDetails($branchId, $dateRange);
                break;
            case 'guest_donations':
                $data = $this->reportService->getGuestDonationsDetails($branchId, $dateRange);
                break;
            case 'monthly_expenses':
                $data = $this->reportService->getMonthlyExpensesDetails($branchId, $dateRange);
                break;
            default:
                abort(404);
        }

        return Inertia::render('Reports/DetailedReport', [
            'metric' => $metric,
            'data' => $data,
            'filters' => [
                'branch_id' => $branchId,
                'date_range' => $dateRange,
            ],
        ]);
    }

    /**
     * Show activity report
     */
    public function activityReport(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole(['Super Admin', 'Branch Admin', 'Admin'])) {
            abort(403);
        }

        $branchId = $request->input('branch_id');
        $dateRange = $request->input('date_range', 30);

        $activities = $this->reportService->getActivityReport($branchId, $dateRange);

        return Inertia::render('Reports/ActivityReport', [
            'activities' => $activities,
            'filters' => [
                'branch_id' => $branchId,
                'date_range' => $dateRange,
            ],
        ]);
    }

    /**
     * Export report data
     */
    public function exportReport(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole(['Super Admin', 'Branch Admin', 'Admin'])) {
            abort(403);
        }

        $format = $request->input('format', 'pdf');
        $branchId = $request->input('branch_id');
        $dateRange = (int) $request->input('date_range', 30);

        $stats = $this->reportService->getEnhancedAdminDashboard($branchId, $dateRange);

        $filters = [
            'branch_id' => $branchId,
            'date_range' => $dateRange,
        ];

        if ($format === 'excel') {
            $filename = 'admin_dashboard_' . now()->format('Ymd_His') . '.xlsx';

            return Excel::download(new AdminDashboardExport($stats, $filters), $filename);
        }

        $pdf = Pdf::loadView('reports.admin_dashboard_export', [
            'stats' => $stats,
            'filters' => $filters,
            'generatedBy' => $user,
            'generatedAt' => now(),
            'trend' => $stats['monthly_trend'] ?? [],
        ]);

        $filename = 'admin_dashboard_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
