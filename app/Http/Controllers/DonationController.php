<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Notifications\GuestDonationLoggedNotification;
use App\Notifications\GuestDonationReceiptNotification;
use App\Support\Services\DonationReceiptService;
use App\Support\Services\KycService;
use App\Support\Services\TelebirrService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    /**
     * Store a newly created guest donation in storage.
     *
     * @param StoreDonationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeGuest(StoreDonationRequest $request, DonationReceiptService $receiptService, KycService $kycService)
    {
        $validatedData = $request->validated();

        $elderId = $validatedData['elder_id'] ?? null;
        $elder = $elderId ? Elder::find($elderId) : null;

        $uploadedReceiptPath = null;
        if ($request->hasFile('receipt')) {
            $uploadedReceiptPath = $request->file('receipt')->store('receipts/manual', 'public');
        }

        $kycRequired = $kycService->shouldRequire($validatedData['amount'], 'ETB');
        $donationMode = $validatedData['donation_mode'] ?? 'one_time';

        $donation = Donation::create([
            'elder_id' => $elder?->id,
            'branch_id' => $elder?->branch_id,
            'amount' => $validatedData['amount'],
            'guest_name' => $validatedData['name'] ?? null,
            'guest_email' => $validatedData['email'] ?? null,
            'guest_phone' => $validatedData['phone'] ?? null,
            'receipt_path' => $uploadedReceiptPath,
            'payment_gateway' => 'manual',
            'payment_id' => null,
            'status' => 'pending',
            'currency' => 'ETB', // Default currency
            'donation_type' => $donationMode === 'sponsorship' ? 'guest_sponsorship' : 'guest_meal',
            'campaign_id' => $validatedData['campaign_id'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
            'kyc_required' => $kycRequired,
            'kyc_status' => $kycRequired ? 'pending' : 'not_required',
        ]);

        $donation->setRelation('elder', $elder);

        $receiptStoragePath = $receiptService->ensureReceipt($donation);
        $receiptUrl = $receiptStoragePath ? url(route('receipts.show', $donation->receipt_uuid, false)) : null;

        if ($donation->guest_email && $receiptStoragePath) {
            Notification::route('mail', $donation->guest_email)
                ->notify(new GuestDonationReceiptNotification($donation, $receiptStoragePath));
        }

        $this->notifyBranchTeam($donation, $receiptUrl);

        return redirect()->route('thank-you')->with('donation_summary', [
            'relationship' => $validatedData['relationship'] ?? 'sponsorship',
            'amount' => $donation->amount,
            'elder_name' => optional($elder)->first_name ? ($elder->first_name . ' ' . $elder->last_name) : null,
            'mode' => $donationMode,
        ])->with('success', 'Thank you! Your donation is pending confirmation.');
    }

    /**
     * Store a newly created resource in storage.
     * This method can be used for authenticated users and integrated payment gateways.
     */
    public function store(StoreDonationRequest $request, TelebirrService $telebirrService, KycService $kycService)
    {
        $validatedData = $request->validated();

        $elderId = $validatedData['elder_id'] ?? null;
        $elder = $elderId ? Elder::find($elderId) : null;

        $gatewayReference = (string) Str::uuid();

        $kycRequired = $kycService->shouldRequire($validatedData['amount'], 'ETB');
        $donation = Donation::create([
            'user_id' => Auth::id(),
            'elder_id' => $elder?->id,
            'branch_id' => $elder?->branch_id,
            'amount' => $validatedData['amount'],
            'guest_name' => $validatedData['name'] ?? null,
            'guest_email' => $validatedData['email'] ?? null,
            'guest_phone' => $validatedData['phone'] ?? null,
            'payment_gateway' => 'telebirr',
            'payment_id' => $gatewayReference,
            'status' => 'pending',
            'currency' => 'ETB',
            'donation_type' => 'guest_one_time',
            'campaign_id' => $validatedData['campaign_id'] ?? null,
            'kyc_required' => $kycRequired,
            'kyc_status' => $kycRequired ? 'pending' : 'not_required',
        ]);

        PaymentTransaction::create([
            'donation_id' => $donation->id,
            'branch_id' => $donation->branch_id,
            'gateway' => 'telebirr',
            'gateway_reference' => $gatewayReference,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'status' => 'pending',
        ]);

        $paymentResponse = $telebirrService->initiatePayment(
            $gatewayReference,
            (float) $donation->amount,
            (string) Str::uuid(),
            'Donation'
        );

        if (($paymentResponse['status'] ?? null) !== 'success' || empty($paymentResponse['redirect_url'])) {
            return redirect()->back()->with('error', 'Payment failed: ' . ($paymentResponse['message'] ?? 'Unable to initiate payment'));
        }

        return redirect()->away($paymentResponse['redirect_url']);
    }

    protected function notifyBranchTeam(Donation $donation, ?string $receiptUrl = null): void
    {
        $recipients = User::role('Branch Admin')
            ->when($donation->branch_id, fn($query) => $query->where('branch_id', $donation->branch_id))
            ->get();

        if ($recipients->isEmpty()) {
            $recipients = User::role('Admin')->get();
        }

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new GuestDonationLoggedNotification($donation, $receiptUrl)
        );
    }
}
