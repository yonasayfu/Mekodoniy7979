<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ThankYouController extends Controller
{
    public function show(Request $request)
    {
        $data = $request->session()->pull('donation_summary', [
            'relationship' => 'sponsorship',
            'amount' => null,
            'elder_name' => null,
            'mode' => 'sponsorship',
            'cadence' => null,
            'payment_gateway' => null,
            'payment_status' => null,
            'deduction_schedule' => null,
            'receipt_url' => null,
            'mandate_url' => null,
            'payment_reference' => null,
            'donor_name' => null,
            'donor_phone' => null,
            'member_login_phone' => null,
            'member_password' => null,
        ]);

        return Inertia::render('ThankYou', [
            'donation' => $data,
        ]);
    }
}
