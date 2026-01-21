<?php

namespace App\Http\Controllers;

use App\Models\PreSponsorship;
use Illuminate\Http\Request;

class PreSponsorshipController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'relationship_type' => 'required|string|in:father,mother,brother,sister',
        ]);

        $preSponsorship = PreSponsorship::create($validated);

        // Redirect to guest donation page, possibly with some pre-filled data or a token
        return redirect()->route('guest.donation', [
            'pre_sponsorship_id' => $preSponsorship->id,
            'relationship' => $preSponsorship->relationship_type,
            'mode' => 'sponsorship',
        ])->with('success', 'Thank you for your interest! Please complete the donation form.');
    }
}
