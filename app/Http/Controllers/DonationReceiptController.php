<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Support\Services\DonationReceiptService;
use Illuminate\Http\Request;

class DonationReceiptController extends Controller
{
    public function show(string $receiptUuid, DonationReceiptService $receiptService)
    {
        $donation = Donation::withoutGlobalScopes()
            ->where('receipt_uuid', $receiptUuid)
            ->firstOrFail();

        return $receiptService->downloadStream($donation);
    }

    public function annual(Request $request, DonationReceiptService $receiptService, ?int $year = null)
    {
        $user = $request->user();
        abort_unless($user?->hasAnyRole(['External', 'Donor']), 403);

        $targetYear = $year ?? now()->year;

        return $receiptService->generateAnnualStatement($user, $targetYear);
    }
}
