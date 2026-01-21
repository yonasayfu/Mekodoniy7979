<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDonorOnboardingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class DonorOnboardingController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->donorProfile;

        $defaults = [
            'relationship_goal' => $profile?->relationship_goal,
            'monthly_budget' => $profile?->monthly_budget,
            'frequency' => $profile?->frequency ?? 'monthly',
            'preferred_contact_method' => $profile?->preferred_contact_method,
            'contact_channels' => $profile?->contact_channels ?? [],
            'payment_preference' => $profile?->payment_preference,
            'notes' => $profile?->notes,
            'onboarding_step' => $profile?->onboarding_step ?? 'relationship',
            'is_completed' => (bool) $profile?->is_completed,
        ];

        return Inertia::render('DonorOnboarding/Wizard', [
            'profile' => $defaults,
            'steps' => $this->steps(),
        ]);
    }

    public function store(UpdateDonorOnboardingRequest $request)
    {
        $user = $request->user();
        $complete = $request->boolean('complete');
        $payload = $request->safe()->except(['complete']);

        $profile = $user->donorProfile()->updateOrCreate(
            [],
            [
                ...$payload,
                'contact_channels' => Arr::wrap($payload['contact_channels'] ?? []),
                'is_completed' => $complete ? true : ($user->donorProfile->is_completed ?? false),
                'completed_at' => $complete ? now() : ($user->donorProfile->completed_at ?? null),
            ]
        );

        $message = $complete ? 'Welcome aboard! Your donor onboarding is complete.' : 'Preferences saved. Continue to the next step.';

        if ($complete) {
            return redirect()->route('donors.dashboard')->with('success', $message);
        }

        return back()->with('success', $message);
    }

    protected function steps(): array
    {
        return [
            [
                'key' => 'relationship',
                'title' => 'Choose Your Connection',
                'description' => 'Tell us how you hope to show up for an elder—Father, Mother, Brother, or Sister.',
            ],
            [
                'key' => 'pledge',
                'title' => 'Set Your Monthly Promise',
                'description' => 'Pick an amount and cadence that feels sustainable for you.',
            ],
            [
                'key' => 'contact',
                'title' => 'Communication Preferences',
                'description' => 'Let us know the best way to keep you updated and reminded.',
            ],
            [
                'key' => 'payment',
                'title' => 'Payment & Finishing Touches',
                'description' => 'Choose how you want to send support and review your details.',
            ],
        ];
    }
}
