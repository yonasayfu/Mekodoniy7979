<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\Elder;
use App\Models\PaymentTransaction;
use App\Support\Services\TelebirrService;
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
    public function storeGuest(StoreDonationRequest $request)
    {
        $validatedData = $request->validated();

        $elderId = $validatedData['elder_id'] ?? null;
        $elder = $elderId ? Elder::find($elderId) : null;

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Donation::create([
            'elder_id' => $elder?->id,
            'branch_id' => $elder?->branch_id,
            'amount' => $validatedData['amount'],
            'guest_name' => $validatedData['name'] ?? null,
            'guest_email' => $validatedData['email'] ?? null,
            'guest_phone' => $validatedData['phone'] ?? null,
            'receipt_path' => $receiptPath,
            'payment_gateway' => 'manual',
            'payment_id' => null,
            'status' => 'pending',
            'currency' => 'ETB', // Default currency
            'donation_type' => 'guest_meal',
            'campaign_id' => $validatedData['campaign_id'] ?? null,
        ]);

        return redirect()->route('home')->with('success', 'Thank you! Your donation is pending confirmation.');
    }

    /**
     * Store a newly created resource in storage.
     * This method can be used for authenticated users and integrated payment gateways.
     */
    public function store(StoreDonationRequest $request, TelebirrService $telebirrService)
    {
        $validatedData = $request->validated();

        $elderId = $validatedData['elder_id'] ?? null;
        $elder = $elderId ? Elder::find($elderId) : null;

        $gatewayReference = (string) Str::uuid();

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
}