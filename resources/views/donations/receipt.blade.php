<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mekodonia Donation Receipt</title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 32px; color: #0f172a; }
        h1 { margin-bottom: 8px; }
        .muted { color: #475569; font-size: 0.95rem; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
        .card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Mekodonia Donation Receipt</h1>
    <p class="muted">
        Reference: {{ $donation->receipt_uuid }} · Issued {{ now()->format('F j, Y H:i') }}
    </p>

    <div class="grid">
        <div class="card">
            <h3>Donor Information</h3>
            <p><strong>Name:</strong> {{ $donation->guest_name ?? optional($user)->name ?? 'Guest Donor' }}</p>
            <p><strong>Email:</strong> {{ $donation->guest_email ?? optional($user)->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $donation->guest_phone ?? optional($user)->phone_number ?? 'N/A' }}</p>
        </div>
        <div class="card">
            <h3>Donation Details</h3>
            <p><strong>Amount:</strong> {{ number_format((float) $donation->amount, 2) }} {{ $donation->currency ?? 'ETB' }}</p>
            <p><strong>Purpose:</strong> {{ $elder ? 'Support for '.$elder->first_name.' '.$elder->last_name : 'General Fund' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($donation->status) }}</p>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Donation ({{ $donation->donation_type ?? 'one-time' }})</td>
            <td>{{ number_format((float) $donation->amount, 2) }} {{ $donation->currency ?? 'ETB' }}</td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th>Total</th>
            <th>{{ number_format((float) $donation->amount, 2) }} {{ $donation->currency ?? 'ETB' }}</th>
        </tr>
        </tfoot>
    </table>

    <p class="muted" style="margin-top: 24px;">
        Thank you for supporting Mekodonia. This receipt acknowledges that the organization received
        your contribution. Please keep it for your records.
    </p>
</body>
</html>
