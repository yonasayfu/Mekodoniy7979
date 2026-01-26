<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DonorDonationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $donationModels = Donation::with(['elder', 'branch'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $guestDonationPath = route('guest.donation', [], false);

        $donations = $donationModels->map(function (Donation $donation) use ($guestDonationPath) {
            return [
                'id' => $donation->id,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'status' => $donation->status,
                'donation_type' => $donation->donation_type,
                'donation_mode' => $donation->donation_mode,
                'cadence' => $donation->cadence,
                'deduction_schedule' => $donation->deduction_schedule,
                'recurrence_duration' => $donation->recurrence_duration,
                'payment_gateway' => $donation->payment_gateway,
                'payment_status' => $donation->payment_status,
                'payment_reference' => $donation->payment_reference,
                'notes' => $donation->notes,
                'relationship' => $donation->relationship,
                'created_at' => optional($donation->created_at)->toIso8601String(),
                'elder_name' => optional($donation->elder)->name,
                'elder_relationship' => optional($donation->elder)->relationship_type,
                'branch_name' => optional($donation->branch)->name,
                'receipt_url' => $donation->receipt_path
                    ? Storage::disk('public')->url($donation->receipt_path)
                    : null,
                'mandate_url' => $donation->mandate_path
                    ? Storage::disk('public')->url($donation->mandate_path)
                    : null,
                'manage_url' => $donation->payment_reference
                    ? $guestDonationPath . '?payment_reference=' . urlencode($donation->payment_reference)
                    : null,
            ];
        });

        $totalDonations = $donationModels->sum('amount');
        $pendingDonations = $donationModels->filter(fn (Donation $donation) => in_array($donation->payment_status, ['pending', 'awaiting_receipt'], true))->count();

        return Inertia::render('DonorDonations', [
            'donations' => $donations,
            'totalDonations' => $totalDonations,
            'pendingDonations' => $pendingDonations,
        ]);
    }
}
