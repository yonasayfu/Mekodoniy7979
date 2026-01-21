<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $year }} Donation Statement</title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 32px; color: #0f172a; }
        h1 { margin-bottom: 0; }
        .muted { color: #475569; font-size: 0.95rem; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; font-size: 0.9rem; }
        th { background: #f8fafc; }
        tfoot th { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $year }} Donation Statement</h1>
    <p class="muted">
        Prepared for {{ $user->name }} · Generated {{ now()->format('F j, Y') }}
    </p>

    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Elder / Purpose</th>
            <th>Type</th>
            <th>Status</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        @forelse($donations as $donation)
            <tr>
                <td>{{ $donation->created_at->format('M d, Y') }}</td>
                <td>{{ $donation->elder?->first_name }} {{ $donation->elder?->last_name ?? 'General Fund' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $donation->donation_type ?? 'one-time')) }}</td>
                <td>{{ ucfirst($donation->status) }}</td>
                <td>{{ number_format((float) $donation->amount, 2) }} {{ $donation->currency ?? 'ETB' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No donations recorded for {{ $year }}.</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <th colspan="4">Total</th>
            <th>{{ number_format((float) $total, 2) }} ETB</th>
        </tr>
        </tfoot>
    </table>

    <p class="muted" style="margin-top: 16px;">
        Mekodonia Home Connect is grateful for your continued support.
    </p>
</body>
</html>
