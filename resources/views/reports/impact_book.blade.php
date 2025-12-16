<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impact Book - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            color: #337ab7;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
        }
        .stat-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #5cb85c;
        }
        .stat-item .label {
            font-size: 14px;
            color: #777;
        }
        .elder-list, .timeline-list {
            list-style: none;
            padding: 0;
        }
        .elder-list li, .timeline-list li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-left: 5px solid #5cb85c;
            border-radius: 3px;
        }
        .elder-list li h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .timeline-list li {
            border-left: 5px solid #5bc0de;
        }
        .timeline-list li strong {
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if (file_exists(public_path('images/logo.svg')))
                <img src="{{ public_path('images/logo.svg') }}" alt="Mekodonia Logo">
            @endif
            <h1>Your Mekodonia Impact Book</h1>
            <p>A summary of your invaluable contributions.</p>
        </div>

        <div class="section">
            <h2>Dear {{ $user->name }},</h2>
            <p>
                Thank you for your generous spirit and unwavering support for the elders at Mekodonia. Your commitment
                makes a profound difference in their lives. This Impact Book summarizes your journey with us and the
                positive change you've helped create.
            </p>
        </div>

        <div class="section">
            <h2>Your Contributions</h2>
            <div class="stats">
                <div class="stat-item">
                    <div class="value">{{ number_format($totalDonations, 2) }} ETB</div>
                    <div class="label">Total Donations</div>
                </div>
                <div class="stat-item">
                    <div class="value">{{ $supportedElders->count() }}</div>
                    <div class="label">Elders Supported</div>
                </div>
            </div>
        </div>

        @if($supportedElders->count())
        <div class="section">
            <h2>Elders You Support</h2>
            <ul class="elder-list">
                @foreach($supportedElders as $elder)
                    <li>
                        <h3>{{ $elder->first_name }} {{ $elder->last_name }}</h3>
                        <p>Priority: {{ ucfirst($elder->priority_level) }}</p>
                        <p>Location: {{ $elder->branch->name ?? 'N/A' }}</p>
                        {{-- Add more elder details if needed --}}
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($keyTimelineEvents->count())
        <div class="section">
            <h2>Key Moments in Your Timeline</h2>
            <ul class="timeline-list">
                @foreach($keyTimelineEvents as $event)
                    <li>
                        <strong>{{ \Carbon\Carbon::parse($event->occurred_at)->format('M d, Y') }}:</strong>
                        {{ $event->description }}
                        @if($event->elder)
                            (Related to {{ $event->elder->first_name }} {{ $event->elder->last_name }})
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="footer">
            <p>&copy; {{ date('Y') }} Mekodonia Home Connect. All rights reserved.</p>
            <p>Generated on {{ date('M d, Y') }}</p>
        </div>
    </div>
</body>
</html>
