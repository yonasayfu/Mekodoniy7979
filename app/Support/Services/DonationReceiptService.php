<?php

namespace App\Support\Services;

use App\Models\Donation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationReceiptService
{
    public function ensureReceipt(Donation $donation): string
    {
        $donation->loadMissing(['user', 'elder']);

        if (! $donation->receipt_uuid) {
            $donation->receipt_uuid = (string) Str::uuid();
        }

        $path = $donation->receipt_path ?: $this->buildPath($donation);

        if (! Storage::disk('public')->exists($path)) {
            $pdf = Pdf::loadView('donations.receipt', [
                'donation' => $donation,
                'elder' => $donation->elder,
                'user' => $donation->user,
            ]);

            Storage::disk('public')->put($path, $pdf->output());
        }

        $donation->forceFill([
            'receipt_path' => $path,
            'receipt_issued_at' => now(),
        ])->save();

        return $path;
    }

    public function downloadStream(Donation $donation)
    {
        $path = $this->ensureReceipt($donation);

        return response()->file(Storage::disk('public')->path($path));
    }

    public function generateAnnualStatement(User $user, int $year)
    {
        $donations = $user->donations()
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('donations.annual_statement', [
            'user' => $user,
            'year' => $year,
            'donations' => $donations,
            'total' => $donations->sum('amount'),
        ]);

        return $pdf->download("mekodonia_receipts_{$year}.pdf");
    }

    protected function buildPath(Donation $donation): string
    {
        return sprintf(
            'receipts/generated/%s_%s.pdf',
            $donation->receipt_uuid,
            now()->format('YmdHis')
        );
    }
}
