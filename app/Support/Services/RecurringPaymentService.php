<?php

namespace App\Support\Services;

use App\Models\Donation;
use App\Models\PaymentTransaction;
use App\Models\Sponsorship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecurringPaymentService
{
    protected array $telebirrConfig;
    protected array $cbeConfig;

    public function __construct()
    {
        $this->telebirrConfig = config('services.telebirr_recurring', []);
        $this->cbeConfig = config('services.cbe_recurring', []);
    }

    /**
     * Initiate a recurring subscription with the specified gateway.
     */
    public function createSubscription(User $user, Sponsorship $sponsorship, string $gateway = 'telebirr', array $options = []): array
    {
        $sponsorship->loadMissing(['user', 'elder']);
        $gatewayToUse = $gateway ?: ($sponsorship->subscription_gateway ?? 'telebirr');

        $result = $gatewayToUse === 'cbe'
            ? $this->createCbeSubscription($user, $sponsorship, $options)
            : $this->createTelebirrSubscription($user, $sponsorship, $options);

        if ($result['status'] === 'success') {
            $sponsorship->forceFill([
                'subscription_id' => $result['subscription_id'],
                'subscription_gateway' => $gatewayToUse,
                'subscription_metadata' => array_merge(
                    $sponsorship->subscription_metadata ?? [],
                    [
                        'gateway' => $gatewayToUse,
                        'created_at' => now()->toIso8601String(),
                    ]
                ),
                'next_billing_date' => Carbon::parse($result['next_payment_date'])->toDateString(),
                'status' => $sponsorship->status === 'pending' ? 'active' : $sponsorship->status,
            ])->save();
        }

        return $result;
    }

    public function updateSubscription(Sponsorship $sponsorship): array
    {
        if (! $sponsorship->subscription_id) {
            return [
                'status' => 'failed',
                'message' => 'Sponsorship does not have an active subscription to update.',
            ];
        }

        $gateway = $sponsorship->subscription_gateway ?? 'telebirr';
        $result = $gateway === 'cbe'
            ? $this->updateCbeSubscription($sponsorship)
            : $this->updateTelebirrSubscription($sponsorship);

        return $result;
    }

    public function cancelSubscription(Sponsorship $sponsorship): array
    {
        if (! $sponsorship->subscription_id) {
            return [
                'status' => 'failed',
                'message' => 'Sponsorship does not have an active subscription to cancel.',
            ];
        }

        $gateway = $sponsorship->subscription_gateway ?? 'telebirr';

        $result = $gateway === 'cbe'
            ? $this->cancelCbeSubscription($sponsorship)
            : $this->cancelTelebirrSubscription($sponsorship);

        if ($result['status'] === 'success') {
            $sponsorship->forceFill([
                'subscription_id' => null,
                'subscription_metadata' => array_merge($sponsorship->subscription_metadata ?? [], [
                    'cancelled_at' => now()->toIso8601String(),
                ]),
            ])->save();
        }

        return $result;
    }

    /**
     * Charge a sponsorship that is due.
     */
    public function processScheduledPayment(Sponsorship $sponsorship): array
    {
        $sponsorship->loadMissing(['user', 'elder']);

        if (! $sponsorship->subscription_id) {
            return [
                'status' => 'failed',
                'message' => 'Sponsorship does not have an active subscription for scheduled payment.',
            ];
        }

        $gateway = $sponsorship->subscription_gateway ?? 'telebirr';
        $result = $gateway === 'cbe'
            ? $this->chargeCbeSubscription($sponsorship)
            : $this->chargeTelebirrSubscription($sponsorship);

        if ($result['status'] !== 'completed') {
            $sponsorship->increment('missed_payment_count');
            $sponsorship->forceFill(['promise_kept_last_month' => false])->save();

            return $result;
        }

        $donation = $this->recordRecurringDonation($sponsorship, $result);

        $nextBillingDate = $this->calculateNextBillingDate($sponsorship)->toDateString();

        $sponsorship->forceFill([
            'next_billing_date' => $nextBillingDate,
            'consecutive_months_kept' => $sponsorship->consecutive_months_kept + 1,
            'promise_kept_last_month' => true,
            'subscription_metadata' => array_merge($sponsorship->subscription_metadata ?? [], [
                'last_transaction_id' => $result['transaction_id'] ?? null,
                'last_charged_at' => now()->toIso8601String(),
            ]),
        ])->save();

        return [
            ...$result,
            'donation_id' => $donation->id,
            'next_payment_date' => $nextBillingDate,
        ];
    }

    protected function createTelebirrSubscription(User $user, Sponsorship $sponsorship, array $options = []): array
    {
        $nextDate = $this->calculateNextBillingDate($sponsorship)->toDateString();

        if ($this->shouldSimulateTelebirr()) {
            return $this->simulateSubscriptionResponse('telebirr', $nextDate);
        }

        $payload = [
            'appId' => $this->telebirrConfig['app_id'],
            'merchantId' => $this->telebirrConfig['merchant_id'],
            'subscriberName' => $user->name,
            'payerMsisdn' => $options['msisdn'] ?? $user->phone,
            'amount' => (float) $sponsorship->amount,
            'currency' => $sponsorship->currency ?? 'ETB',
            'frequency' => $sponsorship->frequency,
            'startDate' => Carbon::parse($sponsorship->start_date ?? now())->format('YmdHis'),
            'callbackUrl' => $this->telebirrConfig['notify_url'],
            'requestId' => Str::uuid()->toString(),
            'nonceStr' => Str::random(16),
        ];

        $payload['sign'] = $this->signPayload($payload, $this->telebirrConfig['app_key']);

        try {
            $response = Http::baseUrl(rtrim($this->telebirrConfig['base_url'], '/').'/')
                ->acceptJson()
                ->timeout(15)
                ->post($this->telebirrConfig['subscribe_endpoint'], $payload);

            if (! $response->successful()) {
                Log::warning('RecurringPaymentService: Telebirr subscription HTTP failure.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'status' => 'failed',
                    'message' => 'Telebirr rejected the subscription request.',
                    'subscription_id' => null,
                    'next_payment_date' => null,
                ];
            }

            $body = $response->json();
            if (data_get($body, 'status') !== 'SUCCESS') {
                return [
                    'status' => 'failed',
                    'message' => data_get($body, 'message', 'Telebirr could not create the subscription.'),
                    'subscription_id' => null,
                    'next_payment_date' => null,
                    'raw' => $body,
                ];
            }

            return [
                'status' => 'success',
                'message' => data_get($body, 'message', 'Subscription created.'),
                'subscription_id' => data_get($body, 'data.subscriptionId'),
                'next_payment_date' => $nextDate,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: Telebirr subscription exception.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Failed to create Telebirr subscription: '.$e->getMessage(),
                'subscription_id' => null,
                'next_payment_date' => null,
            ];
        }
    }

    protected function createCbeSubscription(User $user, Sponsorship $sponsorship, array $options = []): array
    {
        $nextDate = $this->calculateNextBillingDate($sponsorship)->toDateString();

        if ($this->shouldSimulateCbe()) {
            return $this->simulateSubscriptionResponse('cbe', $nextDate);
        }

        $payload = [
            'customer_name' => $user->name,
            'customer_phone' => $options['phone'] ?? $user->phone,
            'amount' => (float) $sponsorship->amount,
            'currency' => $sponsorship->currency ?? 'ETB',
            'frequency' => $sponsorship->frequency,
            'start_date' => Carbon::parse($sponsorship->start_date ?? now())->toDateString(),
            'meta' => [
                'sponsorship_id' => $sponsorship->id,
                'elder_id' => $sponsorship->elder_id,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->cbeConfig['api_key'],
            ])
                ->baseUrl(rtrim($this->cbeConfig['api_url'], '/').'/')
                ->acceptJson()
                ->timeout(15)
                ->post($this->cbeConfig['subscribe_endpoint'], $payload);

            if (! $response->successful()) {
                Log::warning('RecurringPaymentService: CBE subscription HTTP failure.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'status' => 'failed',
                    'message' => 'CBE rejected the subscription request.',
                    'subscription_id' => null,
                    'next_payment_date' => null,
                ];
            }

            $body = $response->json();

            return [
                'status' => 'success',
                'message' => data_get($body, 'message', 'Subscription created.'),
                'subscription_id' => data_get($body, 'data.subscription_reference'),
                'next_payment_date' => $nextDate,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: CBE subscription exception.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Failed to create CBE subscription: '.$e->getMessage(),
                'subscription_id' => null,
                'next_payment_date' => null,
            ];
        }
    }

    protected function updateTelebirrSubscription(Sponsorship $sponsorship): array
    {
        if ($this->shouldSimulateTelebirr()) {
            return [
                'status' => 'success',
                'message' => 'Telebirr subscription updated (simulated).',
            ];
        }

        try {
            $payload = [
                'subscriptionId' => $sponsorship->subscription_id,
                'amount' => (float) $sponsorship->amount,
                'frequency' => $sponsorship->frequency,
            ];

            Http::baseUrl(rtrim($this->telebirrConfig['base_url'], '/').'/')
                ->acceptJson()
                ->timeout(10)
                ->post($this->telebirrConfig['subscribe_endpoint'].'/update', $payload);

            return [
                'status' => 'success',
                'message' => 'Telebirr subscription updated.',
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: Telebirr update error.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Failed to update Telebirr subscription: '.$e->getMessage(),
            ];
        }
    }

    protected function updateCbeSubscription(Sponsorship $sponsorship): array
    {
        if ($this->shouldSimulateCbe()) {
            return [
                'status' => 'success',
                'message' => 'CBE subscription updated (simulated).',
            ];
        }

        try {
            Http::withHeaders(['x-api-key' => $this->cbeConfig['api_key']])
                ->baseUrl(rtrim($this->cbeConfig['api_url'], '/').'/')
                ->acceptJson()
                ->timeout(10)
                ->post($this->cbeConfig['subscribe_endpoint'].'/update', [
                    'subscription_reference' => $sponsorship->subscription_id,
                    'amount' => (float) $sponsorship->amount,
                ]);

            return [
                'status' => 'success',
                'message' => 'CBE subscription updated.',
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: CBE update error.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Failed to update CBE subscription: '.$e->getMessage(),
            ];
        }
    }

    protected function cancelTelebirrSubscription(Sponsorship $sponsorship): array
    {
        if ($this->shouldSimulateTelebirr()) {
            return [
                'status' => 'success',
                'message' => 'Telebirr subscription cancelled (simulated).',
            ];
        }

        try {
            Http::baseUrl(rtrim($this->telebirrConfig['base_url'], '/').'/')
                ->acceptJson()
                ->timeout(10)
                ->post($this->telebirrConfig['cancel_endpoint'], [
                    'subscriptionId' => $sponsorship->subscription_id,
                ]);

            return [
                'status' => 'success',
                'message' => 'Telebirr subscription cancelled.',
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: Telebirr cancel error.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Failed to cancel Telebirr subscription: '.$e->getMessage(),
            ];
        }
    }

    protected function cancelCbeSubscription(Sponsorship $sponsorship): array
    {
        if ($this->shouldSimulateCbe()) {
            return [
                'status' => 'success',
                'message' => 'CBE subscription cancelled (simulated).',
            ];
        }

        try {
            Http::withHeaders(['x-api-key' => $this->cbeConfig['api_key']])
                ->baseUrl(rtrim($this->cbeConfig['api_url'], '/').'/')
                ->acceptJson()
                ->timeout(10)
                ->post($this->cbeConfig['cancel_endpoint'], [
                    'subscription_reference' => $sponsorship->subscription_id,
                ]);

            return [
                'status' => 'success',
                'message' => 'CBE subscription cancelled.',
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: CBE cancel error.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Failed to cancel CBE subscription: '.$e->getMessage(),
            ];
        }
    }

    protected function chargeTelebirrSubscription(Sponsorship $sponsorship): array
    {
        if ($this->shouldSimulateTelebirr()) {
            return [
                'status' => 'completed',
                'message' => 'Telebirr auto-debit simulated.',
                'transaction_id' => 'TEL_REC_' . Str::uuid(),
                'amount' => (float) $sponsorship->amount,
                'raw' => ['simulate' => true],
            ];
        }

        try {
            $payload = [
                'subscriptionId' => $sponsorship->subscription_id,
                'amount' => (float) $sponsorship->amount,
                'currency' => $sponsorship->currency ?? 'ETB',
                'requestId' => Str::uuid()->toString(),
            ];

            $response = Http::baseUrl(rtrim($this->telebirrConfig['base_url'], '/').'/')
                ->acceptJson()
                ->timeout(15)
                ->post($this->telebirrConfig['charge_endpoint'], $payload);

            if (! $response->successful()) {
                return [
                    'status' => 'failed',
                    'message' => 'Telebirr could not process the charge.',
                ];
            }

            $body = $response->json();

            if (data_get($body, 'status') !== 'SUCCESS') {
                return [
                    'status' => 'failed',
                    'message' => data_get($body, 'message', 'Telebirr charge failed.'),
                    'raw' => $body,
                ];
            }

            return [
                'status' => 'completed',
                'message' => data_get($body, 'message', 'Charge completed.'),
                'transaction_id' => data_get($body, 'data.transactionId'),
                'amount' => (float) $sponsorship->amount,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: Telebirr charge error.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'Telebirr charge failed: '.$e->getMessage(),
            ];
        }
    }

    protected function chargeCbeSubscription(Sponsorship $sponsorship): array
    {
        if ($this->shouldSimulateCbe()) {
            return [
                'status' => 'completed',
                'message' => 'CBE auto-debit simulated.',
                'transaction_id' => 'CBE_REC_' . Str::uuid(),
                'amount' => (float) $sponsorship->amount,
                'raw' => ['simulate' => true],
            ];
        }

        try {
            $response = Http::withHeaders(['x-api-key' => $this->cbeConfig['api_key']])
                ->baseUrl(rtrim($this->cbeConfig['api_url'], '/').'/')
                ->acceptJson()
                ->timeout(15)
                ->post($this->cbeConfig['charge_endpoint'], [
                    'subscription_reference' => $sponsorship->subscription_id,
                    'amount' => (float) $sponsorship->amount,
                ]);

            if (! $response->successful()) {
                return [
                    'status' => 'failed',
                    'message' => 'CBE charge failed.',
                ];
            }

            $body = $response->json();

            return [
                'status' => 'completed',
                'message' => data_get($body, 'message', 'Charge completed.'),
                'transaction_id' => data_get($body, 'data.transaction_reference'),
                'amount' => (float) $sponsorship->amount,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('RecurringPaymentService: CBE charge error.', ['error' => $e->getMessage()]);

            return [
                'status' => 'failed',
                'message' => 'CBE charge failed: '.$e->getMessage(),
            ];
        }
    }

    protected function recordRecurringDonation(Sponsorship $sponsorship, array $result): Donation
    {
        $donation = Donation::create([
            'user_id' => $sponsorship->user_id,
            'elder_id' => $sponsorship->elder_id,
            'branch_id' => $sponsorship->branch_id,
            'sponsorship_id' => $sponsorship->id,
            'amount' => (float) $sponsorship->amount,
            'currency' => $sponsorship->currency ?? 'ETB',
            'payment_gateway' => $sponsorship->subscription_gateway ?? 'telebirr',
            'payment_id' => $result['transaction_id'] ?? $sponsorship->subscription_id,
            'status' => 'completed',
            'donation_type' => 'recurring_auto_debit',
            'notes' => 'Auto-debit processed via '.$sponsorship->subscription_gateway,
        ]);

        PaymentTransaction::create([
            'donation_id' => $donation->id,
            'branch_id' => $donation->branch_id,
            'gateway' => $sponsorship->subscription_gateway ?? 'telebirr',
            'gateway_reference' => $sponsorship->subscription_id,
            'gateway_transaction_id' => $result['transaction_id'] ?? null,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'status' => $result['status'],
            'raw_payload' => $result['raw'] ?? null,
            'processed_at' => now(),
        ]);

        return $donation;
    }

    protected function calculateNextBillingDate(Sponsorship $sponsorship): Carbon
    {
        $anchor = $sponsorship->next_billing_date
            ? Carbon::parse($sponsorship->next_billing_date)
            : Carbon::parse($sponsorship->start_date ?? now());

        return match ($sponsorship->frequency) {
            'quarterly' => $anchor->copy()->addMonths(3),
            'annually' => $anchor->copy()->addYear(),
            default => $anchor->copy()->addMonth(),
        };
    }

    protected function simulateSubscriptionResponse(string $gateway, string $nextDate): array
    {
        return [
            'status' => 'success',
            'message' => ucfirst($gateway).' subscription simulated.',
            'subscription_id' => strtoupper($gateway).'_SUB_'.Str::uuid(),
            'next_payment_date' => $nextDate,
            'raw' => ['simulate' => true],
        ];
    }

    protected function shouldSimulateTelebirr(): bool
    {
        return ($this->telebirrConfig['simulate'] ?? true)
            || empty($this->telebirrConfig['app_id'])
            || empty($this->telebirrConfig['app_key'])
            || empty($this->telebirrConfig['merchant_id']);
    }

    protected function shouldSimulateCbe(): bool
    {
        return ($this->cbeConfig['simulate'] ?? true)
            || empty($this->cbeConfig['api_url'])
            || empty($this->cbeConfig['api_key']);
    }

    protected function signPayload(array $payload, string $secret): string
    {
        ksort($payload);

        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}
