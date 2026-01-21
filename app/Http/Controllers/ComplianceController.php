<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RedactRequest;
use App\Http\Requests\UpdateKycStatusRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ComplianceController extends Controller
{
    public function exportDonorData(Request $request)
    {
        $donor = $request->user();

        $payload = [
            'donor' => $donor->only(['id', 'name', 'email', 'phone', 'created_at']),
            'donations' => $donor->donations()
                ->with('elder')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn (Donation $donation) => [
                    'id' => $donation->id,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'status' => $donation->status,
                    'campaign_id' => $donation->campaign_id,
                    'branch_id' => $donation->branch_id,
                    'elder_id' => $donation->elder_id,
                    'elder_name' => $donation->elder?->name,
                    'created_at' => optional($donation->created_at)->toDateTimeString(),
                ]),
            'sponsorships' => $donor->sponsorships()
                ->with('elder')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($sponsorship) => [
                    'id' => $sponsorship->id,
                    'elder_id' => $sponsorship->elder_id,
                    'elder_name' => $sponsorship->elder?->name,
                    'amount' => $sponsorship->amount,
                    'status' => $sponsorship->status,
                    'created_at' => optional($sponsorship->created_at)->toDateTimeString(),
                ]),
        ];

        $encrypted = Crypt::encryptString(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $filename = sprintf(
            'donor-export-%s-%s.json.enc',
            $donor->id,
            now()->format('YmdHis')
        );

        return response($encrypted, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function requestRedaction(RedactRequest $request)
    {
        $user = $request->user();
        $user->update([
            'redaction_requested_at' => now(),
            'redaction_reason' => $request->input('reason'),
        ]);

        return back()->with('success', __('Redaction request recorded. We will review it shortly.'));
    }

    public function updateKycStatus(UpdateKycStatusRequest $request, Donation $donation)
    {
        $data = $request->validated();

        if ($request->hasFile('document')) {
            $data['kyc_document_path'] = $request->file('document')->store('donations/kyc', 'public');
        }

        $donation->update([
            'kyc_status' => $data['status'],
            'kyc_review_notes' => $data['notes'] ?? null,
            'kyc_verified_at' => $data['status'] === 'verified' ? now() : null,
            'kyc_document_path' => $data['kyc_document_path'] ?? $donation->kyc_document_path,
        ]);

        return back()->with('success', __('KYC status updated.'));
    }

    public function finalizeRedaction(Request $request, User $user)
    {
        $user->update([
            'data_redacted_at' => now(),
            'redaction_reason' => $request->input('reason', $user->redaction_reason),
        ]);

        return back()->with('success', __('User data marked as redacted.'));
    }
}
