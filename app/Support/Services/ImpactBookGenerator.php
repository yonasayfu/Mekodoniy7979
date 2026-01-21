<?php

namespace App\Support\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ImpactBookGenerator
{
    public function generate(User $user, array $payload, int $year): string
    {
        $viewData = array_merge($payload, [
            'user' => $user,
            'year' => $year,
            'keyTimelineEvents' => $payload['timeline_events'] ?? [],
            'donation_trend' => $payload['donation_trend'] ?? [],
            'andegna_date' => $payload['andegna_date'] ?? ethiopian_date(now()),
            'hero_logo' => $payload['hero_logo'] ?? asset('images/mekodonia-logo.svg'),
            'hero_photo' => $payload['hero_photo'] ?? asset('images/monk-mekodoniya.jpg'),
        ]);

        $pdf = Pdf::loadView('reports.impact_book', $viewData)
            ->setPaper('a4', 'portrait');

        $relativePath = "impact_books/{$year}/impact_book_{$user->id}.pdf";

        Storage::disk('public')->put($relativePath, $pdf->output());

        return $relativePath;
    }
}
