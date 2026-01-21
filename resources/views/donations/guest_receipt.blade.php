<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Donation Receipt</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #111827; padding: 32px; }
        h1 { margin-bottom: 16px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
        .card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Mekodonia Guest Donation Receipt</h1>
    <p class="muted">Reference: #{{ $donation->id }} · {{ $donation->created_at->format('F j, Y H:i') }}</p>

    <div class="grid">
        <div class="card">
            <h3>Donor</h3>
            <p><strong>Name:</strong> {{ $donation->guest_name ?? 'Anonymous' }}</p>
            <p><strong>Email:</strong> {{ $donation->guest_email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $donation->guest_phone ?? 'N/A' }}</p>
        </div>
        <div class="card">
            <h3>Donation</h3>
            <p><strong>Amount:</strong> {{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}</p>
            <p><strong>Elder:</strong> {{ $elder?->name ?? 'General Fund' }}</p>
            <p><strong>Status:</strong> Pending confirmation</p>
        </div>
    </div>

    @if($donation->notes)
        <div class="card">
            <h3>Message</h3>
            <p>{{ $donation->notes }}</p>
        </div>
    @endif

    <p class="muted" style="margin-top: 24px;">
        This document acknowledges that Mekodonia received your pledge. Our branch coordinators
        will confirm payment and update the status within 2 business days.
    </p>
</body>
</html>
