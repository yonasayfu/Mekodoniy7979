<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\Pledge;
use App\Models\AnnualReport;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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

            // If Super Admin, they might want to see global stats (branchId = null)
            if ($user->hasRole('Super Admin')) {
                $branchId = $request->input('branch_id', null);
            }

            $data = $this->reportService->getEnhancedAdminDashboard($branchId);

            // Get branches for filtering (only for Super Admin)
            $branches = [];
            if ($user->hasRole('Super Admin')) {
                $branches = Branch::select('id', 'name')->get();
            }

            return Inertia::render('Reports/AdminDashboard', [
                'stats' => $data,
                'filters' => [
                    'branch_id' => $request->input('branch_id'),
                    'date_range' => $request->input('date_range', '30'),
                ],
                'branches' => $branches,
            ]);
        }

        // 2. Donor View (Heart Dashboard)
        $range = $request->input('range', '30');
        $data = $this->reportService->getDonorImpactData($user, $range);

        $pledges = Pledge::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('elder')
            ->get();

        $annualReports = AnnualReport::where('user_id', $user->id)
            ->orderBy('report_year', 'desc')
            ->get();

        return Inertia::render('Reports/DonorImpact', [
            'impact' => $data,
            'filters' => ['range' => $range],
            'pledges' => $pledges,
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

        // Fetch data for the Impact Book from the service
        $impactData = $this->reportService->getImpactBookData($targetUser);

        // Pass data to a Blade view for PDF generation
        $pdf = Pdf::loadView('reports.impact_book', array_merge(['user' => $targetUser], $impactData));

        // You can customize the PDF filename
        $filename = 'Impact_Book_' . $targetUser->name . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
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
        $dateRange = $request->input('date_range', 30);

        $data = $this->reportService->getEnhancedAdminDashboard($branchId);

        // For now, return JSON. In a real implementation, you'd generate PDF/Excel
        if ($format === 'pdf') {
            // Generate PDF report
            return response()->json(['message' => 'PDF export not yet implemented', 'data' => $data]);
        } elseif ($format === 'excel') {
            // Generate Excel report
            return response()->json(['message' => 'Excel export not yet implemented', 'data' => $data]);
        }

        return response()->json($data);
    }
}