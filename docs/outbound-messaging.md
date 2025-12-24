# Outbound Messaging System

A robust and extensible system for sending and tracking emails, SMS, and WhatsApp messages with support for templates, retries, and delivery tracking.

## Features

- **Multiple Channels**: Send messages via Email, SMS, and WhatsApp
- **Templating**: Support for Blade templates with dynamic data
- **Queue Support**: Asynchronous message processing
- **Retry Logic**: Automatic retries for failed messages
- **Delivery Tracking**: Track message status (pending, sent, delivered, failed)
- **Command Line Tools**: Manage and monitor messages
- **Cleanup**: Automatic cleanup of old messages

## Installation

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag=config
```

2. Run migrations:

```bash
php artisan migrate
```

3. Add the service provider to `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\OutboundServiceProvider::class,
],
```

## Configuration

Edit `config/outbound.php` to configure:

- Default sender information
- Queue settings
- Rate limiting
- Message retention
- SMS/WhatsApp providers

## Usage

### Sending Messages

```php
use App\Services\MessageService;

// Send an email
$message = app(MessageService::class)->sendEmail(
    recipient: 'user@example.com',
    subject: 'Welcome!',
    content: 'Thank you for joining!',
    template: 'emails.welcome',
    templateData: ['name' => 'John']
);

// Send an SMS
$message = app(MessageService::class)->sendSms(
    recipient: '+1234567890',
    content: 'Your verification code is 1234',
    template: 'sms.verification'
);
```

### Commands

- **View Message Status**:
  ```bash
  php artisan outbound:status
  ```

- **Retry Failed Messages**:
  ```bash
  php artisan outbound:retry
  ```

- **Cleanup Old Messages**:
  ```bash
  php artisan outbound:cleanup
  ```

### Scheduling

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Retry failed messages every 5 minutes
    $schedule->command('outbound:retry --hours=1')->everyFiveMinutes();
    
    // Cleanup old messages daily
    $schedule->command('outbound:cleanup')->daily();
}
```

## Templates

Create email templates in `resources/views/emails/`. Example:

```blade
<!-- resources/views/emails/welcome.blade.php -->
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    # Welcome, {{ $name }}!

    {{ $content }}

    @component('mail::button', ['url' => $url ?? '#'])
        Get Started
    @endcomponent

    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
```

## Testing

To test without sending real messages, set these in your `.env`:

```
SMS_DRIVER=log
WHATSAPP_DRIVER=log
MAIL_MAILER=log
```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
