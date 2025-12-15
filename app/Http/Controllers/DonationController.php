<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Support\Services\TelebirrService;
use App\Support\Services\TimelineEventService; // Import TimelineEventService
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Carbon\Carbon; // Import Carbon for occurred_at

class DonationController extends Controller
{
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
                'amount' => $validatedData['amount'],
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'payment_gateway' => 'telebirr',
                'payment_id' => $paymentResponse['transaction_id'],
                'status' => 'completed',
            ]);

            // Create timeline event for successful donation
            $timelineEventService->createEvent(
                'donation',
                'Donation of ' . $donation->amount . ' ETB received from ' . ($donation->name ?? ($request->user() ? $request->user()->name : 'Guest')),
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