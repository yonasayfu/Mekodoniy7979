<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\PreSponsorship;
use App\Services\SponsorshipPromotionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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

        if ($request->header('X-Inertia') || $request->wantsJson()) {
            return response()->json([
                'pre_sponsorship' => $preSponsorship->only([
                    'id',
                    'relationship_type',
                    'created_at',
                ]),
            ], 201);
        }

        return redirect()->route('guest.donation', [
            'pre_sponsorship_id' => $preSponsorship->id,
            'relationship' => $preSponsorship->relationship_type,
            'mode' => 'sponsorship',
        ])->with('success', 'Thank you for your interest! Please complete the donation form.');
    }

    public function edit(PreSponsorship $preSponsorship): Response
    {
        $elders = Elder::orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name'])
            ->map(fn (Elder $elder) => [
                'id' => $elder->id,
                'name' => trim($elder->first_name . ' ' . $elder->last_name),
            ]);

        return Inertia::render('PreSponsorships/Edit', [
            'preSponsorship' => [
                'id' => $preSponsorship->id,
                'name' => $preSponsorship->name,
                'email' => $preSponsorship->email,
                'phone' => $preSponsorship->phone,
                'relationship_type' => $preSponsorship->relationship_type,
                'elder_id' => $preSponsorship->elder_id,
                'amount' => $preSponsorship->amount,
                'currency' => $preSponsorship->currency,
                'notes' => $preSponsorship->notes,
                'status' => $preSponsorship->status,
                'donation_id' => $preSponsorship->donation_id,
            ],
            'elders' => $elders,
        ]);
    }

    public function update(PreSponsorship $preSponsorship, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'elder_id' => ['required', 'integer', 'exists:elders,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'max:3'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'relationship_type' => ['required', 'string', 'in:father,mother,brother,sister'],
        ]);

        $preSponsorship->update([
            'elder_id' => $validated['elder_id'],
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'notes' => $validated['notes'] ?? null,
            'relationship_type' => $validated['relationship_type'],
        ]);

        return redirect()->route('pre-sponsorships.edit', $preSponsorship->id)
            ->with('flash', [
                'banner' => 'Pledge updated successfully. Confirm when you are ready.',
                'bannerStyle' => 'success',
            ]);
    }

    public function reject(PreSponsorship $preSponsorship): RedirectResponse
    {
        $preSponsorship->update(['status' => 'rejected']);

        return redirect()->route('sponsorships.index')
            ->with('flash', [
                'banner' => 'The pledge has been rejected.',
                'bannerStyle' => 'info',
            ]);
    }

    public function promote(PreSponsorship $preSponsorship, SponsorshipPromotionService $promotionService)
    {
        try {
            $sponsorship = $promotionService->promote($preSponsorship);
        } catch (\RuntimeException $exception) {
            return redirect()->back()->with('flash', [
                'banner' => $exception->getMessage(),
                'bannerStyle' => 'danger',
            ]);
        }

        return redirect()->route('sponsorships.index', [
            'sort' => 'created_at',
            'direction' => 'desc',
        ])->with('flash', [
            'banner' => 'The guest pledge was promoted to a confirmed sponsorship.',
            'bannerStyle' => 'success',
        ]);
    }
}
