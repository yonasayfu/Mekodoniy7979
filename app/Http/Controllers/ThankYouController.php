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
        ]);

        return Inertia::render('ThankYou', [
            'donation' => $data,
        ]);
    }
}
