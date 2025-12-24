<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Message' }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            background-color: #4a6fa5;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 4px 4px 0 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        /* Content */
        .content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999999;
            text-align: center;
            padding: 10px 0;
        }
        
        /* Buttons */
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4a6fa5;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        /* Responsive */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                padding: 10px;
            }
            
            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            @if(isset($greeting))
                <p>{{ $greeting }}</p>
            @endif
            
            {!! $content !!}
            
            @if(isset($actionText))
                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
                </div>
            @endif
            
            @if(!isset($hideSignature) || $hideSignature !== false)
                <p>Best regards,<br>The {{ config('app.name') }} Team</p>
            @endif
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            
            @if(isset($unsubscribeUrl))
                <p style="margin-top: 10px;">
                    <a href="{{ $unsubscribeUrl }}" style="color: #999999; text-decoration: underline;">Unsubscribe</a> from these emails.
                </p>
            @endif
            
            <p style="margin-top: 10px; font-size: 11px; color: #bbbbbb;">
                This email was sent to {{ $to ?? 'you' }}. 
                @if(isset($accountUrl))
                    <br>Manage your email preferences <a href="{{ $accountUrl }}" style="color: #bbbbbb; text-decoration: underline;">here</a>.
                @endif
            </p>
        </div>
    </div>
</body>
</html>
