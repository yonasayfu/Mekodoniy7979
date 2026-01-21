<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mekodonia Impact Book</title>
    <style>
        body {
            font-family: 'Instrument Sans', 'Inter', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 0;
            background: #01030a;
        }

        .container {
            padding: 32px 48px;
        }

        .hero {
            position: relative;
            min-height: 260px;
            border-radius: 32px;
            overflow: hidden;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.92), rgba(2, 6, 23, 0.95));
            margin-bottom: 32px;
            box-shadow: 0 30px 70px rgba(2, 6, 23, 0.45);
        }

        .hero img.cover {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 420px;
            object-fit: cover;
            height: 100%;
            opacity: 0.65;
        }

        .hero-content {
            position: relative;
            padding: 40px;
            max-width: 620px;
        }

        .hero h1 {
            font-size: 38px;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .hero p {
            color: rgba(255, 255, 255, 0.82);
            margin-top: 0;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 36px;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
        }

        .grid-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 16px;
        }

        .grid-table th,
        .grid-table td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
            font-size: 0.95rem;
        }

        .grid-table th {
            background: #f8fafc;
            font-weight: 600;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 4px 12px;
            background: #fef9c3;
            color: #a16207;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
        }

        .timeline-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            margin-top: 12px;
        }

        .timeline-item + .timeline-item {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 12px;
        }

        .muted {
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="hero">
        <img src="{{ $hero_photo }}" alt="Mekodonia Elder" class="cover">
        <div class="hero-content">
            <img src="{{ $hero_logo }}" alt="Mekodonia" width="140" />
            <h1>Mekodonia Impact Book</h1>
            <p>Prepared for {{ $user->name }} on {{ now()->format('F j, Y') }} ({{ $andegna_date }})</p>
            <p class="muted">An annual reflection on how your generosity transforms Mekodonia elders.</p>
        </div>
    </div>

    <div class="container">
        <div class="metrics">
            <div class="card">
                <h2>Total Donations</h2>
                <p class="muted mb-2">ETB collected in {{ $year }}</p>
                <p style="font-size: 2rem; font-weight: 600;">{{ number_format($total_donations ?? 0, 2) }} ETB</p>
            </div>
            <div class="card">
                <h2>Supported Elders</h2>
                <p class="muted mb-2">Sponsorships at year end</p>
                <p style="font-size: 2rem; font-weight: 600;">{{ ($supportedElders ?? collect())->count() }}</p>
            </div>
            <div class="card">
                <h2>Andegna Snapshot</h2>
                <p class="muted mb-2">Current Ethiopian date</p>
                <p style="font-size: 1.6rem; font-weight: 600;">{{ $andegna_date }}</p>
            </div>
        </div>

        <div class="section">
            <h2>Supported Elders</h2>
            @if(($supportedElders ?? collect())->isEmpty())
                <p class="muted">No active sponsorships recorded yet. Your next pledge will appear here.</p>
            @else
                <table class="grid-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Priority</th>
                            <th>Relationship</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supportedElders as $elder)
                            <tr>
                                <td>{{ $elder->first_name }} {{ $elder->last_name }}</td>
                                <td><span class="badge">{{ strtoupper($elder->priority_level ?? 'N/A') }}</span></td>
                                <td>{{ $elder->relationship_type ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="section">
            <h2>Timeline Highlights</h2>
            <div class="timeline-card">
                @if(($keyTimelineEvents ?? collect())->isEmpty())
                    <p class="muted">Timeline events will appear once you begin sponsoring or visiting elders.</p>
                @else
                    @foreach($keyTimelineEvents as $event)
                        <div class="timeline-item">
                            <p style="margin: 0; font-weight: 600;">
                                {{ \Illuminate\Support\Carbon::parse($event->occurred_at ?? $event->created_at)->format('M j, Y') }}
                            </p>
                            <p style="margin: 4px 0;">{{ $event->description }}</p>
                            @if($event->elder)
                                <p class="muted" style="margin: 0;">— {{ $event->elder->first_name }} {{ $event->elder->last_name }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="section">
            <h2>Donation Trend</h2>
            @if(($donation_trend ?? collect())->isEmpty())
                <p class="muted">Donation history will populate once contributions are recorded.</p>
            @else
                <table class="grid-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donation_trend as $point)
                            <tr>
                                <td>{{ $point['label'] }}</td>
                                <td>{{ number_format($point['amount'] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="section">
            <h2>Gratitude</h2>
            <p>
                Dear {{ $user->name }}, thank you for keeping your promise in {{ $year }}.
                Your steadfast support ensures Mekodonia elders receive nutrition, healthcare, and daily dignity.
            </p>
        </div>
    </div>
</body>
</html>
