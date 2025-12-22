<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Support\Services\TelebirrService;
use App\Support\Services\TimelineEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Donation::create([
            'amount' => $validatedData['amount'],
            'guest_name' => $validatedData['name'] ?? null,
            'guest_email' => $validatedData['email'] ?? null,
            'guest_phone' => $validatedData['phone'] ?? null,
            'receipt_path' => $receiptPath,
            'status' => 'pending',
            'currency' => 'ETB', // Default currency
        ]);

        return redirect()->route('home')->with('success', 'Thank you! Your donation is pending confirmation.');
    }

    /**
     * Store a newly created resource in storage.
     * This method can be used for authenticated users and integrated payment gateways.
     */
    public function store(StoreDonationRequest $request, TelebirrService $telebirrService, TimelineEventService $timelineEventService)
    {
        $validatedData = $request->validated();

        // Process payment using TelebirrService
        $paymentResponse = $telebirrService->charge([
            'amount' => $validatedData['amount'],
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            // Add other necessary details for Telebirr charge
        ]);

        if ($paymentResponse['status'] === 'success') {
            $donation = Donation::create([
                'user_id' => Auth::id(),
                'amount' => $validatedData['amount'],
                'guest_name' => $validatedData['name'],
                'guest_email' => $validatedData['email'],
                'payment_gateway' => 'telebirr',
                'transaction_id' => $paymentResponse['transaction_id'],
                'status' => 'completed',
            ]);

            // Create timeline event for successful donation
            $timelineEventService->createEvent(
                'donation',
                'Donation of ' . $donation->amount . ' ETB received from ' . ($donation->guest_name ?? 'Guest'),
                Carbon::now(),
                Auth::check() ? Auth::user() : null,
                null, // No elder directly associated with guest donation yet
                $donation
            );

            return redirect()->route('home')->with('success', 'Donation successful! Thank you.');
        } else {
            // Handle failed payment
            return redirect()->back()->with('error', 'Payment failed: ' . $paymentResponse['message']);
        }
    }
}