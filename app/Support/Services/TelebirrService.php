<?php

namespace App\Support\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelebirrService
{
    protected string $appId;
    protected string $appKey;
    protected string $publicKey;
    protected ?string $merchantId;
    protected ?string $merchantPrivateKey;
    protected string $notifyUrl;
    protected string $returnUrl;
    protected string $baseUrl;
    protected bool $simulate;

    public function __construct()
    {
        $this->appId = config('services.telebirr.app_id');
        $this->appKey = config('services.telebirr.app_key');
        $this->publicKey = config('services.telebirr.public_key'); // Telebirr's public key
        $this->merchantId = config('services.telebirr.merchant_id');
        $this->merchantPrivateKey = $this->formatKey(config('services.telebirr.merchant_private_key'), 'PRIVATE');
        $this->notifyUrl = config('services.telebirr.notify_url');
        $this->returnUrl = config('services.telebirr.return_url');
        $this->baseUrl = config('services.telebirr.base_url', 'https://app.telebirr.com/api/'); // Base URL for Telebirr API
        $this->simulate = (bool) config('services.telebirr.simulate', true);
    }

    /**
     * Backwards-compatible wrapper for older controller code.
     * Returns a similar shape to initiatePayment but also includes out_trade_no.
     */
    public function charge(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? $payload['out_trade_no'] ?? $payload['reference'] ?? 'ORDER_' . time());
        $amount = (float) ($payload['amount'] ?? 0);
        $nonce = (string) ($payload['nonce'] ?? uniqid('nonce_', true));
        $subject = (string) ($payload['subject'] ?? 'Donation');

        $response = $this->initiatePayment($orderId, $amount, $nonce, $subject);

        return [
            ...$response,
            'out_trade_no' => $orderId,
        ];
    }

    /**
     * Simulate or initiate a Telebirr payment.
     * In a real application, this would involve signing the request and making an API call to Telebirr.
     *
     * @param string $orderId Unique order ID from your system
     * @param float $amount Amount to charge
     * @param string $nonce A unique string for each request to prevent replay attacks
     * @param string $subject Description of the payment
     * @return array
     */
    public function initiatePayment(string $orderId, float $amount, string $nonce, string $subject): array
    {
        Log::info("TelebirrService: Initiating payment for Order ID: {$orderId}, Amount: {$amount}");

        if ($this->simulate || ! $this->isConfigured()) {
            return $this->simulateResponse($orderId, $amount);
        }

        $payload = $this->buildRequestPayload($orderId, $amount, $nonce, $subject);

        try {
            $response = Http::baseUrl(rtrim($this->baseUrl, '/').'/')
                ->timeout(15)
                ->acceptJson()
                ->post('merchant/order/create', $payload);

            if (! $response->successful()) {
                Log::warning('TelebirrService: Non-success HTTP response.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'status' => 'failed',
                    'message' => 'Telebirr rejected the payment request.',
                    'transaction_id' => null,
                    'redirect_url' => null,
                ];
            }

            $body = $response->json();
            $redirectUrl = data_get($body, 'data.toPayUrl');

            if (! $redirectUrl) {
                Log::warning('TelebirrService: Missing redirect URL in response.', ['response' => $body]);

                return [
                    'status' => 'failed',
                    'message' => 'Telebirr did not return a redirect URL.',
                    'transaction_id' => data_get($body, 'data.tradeNo'),
                    'redirect_url' => null,
                ];
            }

            return [
                'status' => 'success',
                'message' => data_get($body, 'msg', 'Payment initiation successful.'),
                'transaction_id' => data_get($body, 'data.tradeNo'),
                'redirect_url' => $redirectUrl,
            ];
        } catch (\Throwable $e) {
            Log::error("TelebirrService: Payment initiation failed for Order ID: {$orderId}. Error: {$e->getMessage()}");
            return [
                'status' => 'failed',
                'message' => 'Failed to initiate payment: ' . $e->getMessage(),
                'transaction_id' => null,
                'redirect_url' => null,
            ];
        }
    }

    /**
     * Handle Telebirr callback/notification.
     * This method would process the POST request from Telebirr's server.
     *
     * @param Request $request The incoming callback request from Telebirr
     * @return array
     */
    public function handleCallback(Request $request): array
    {
        Log::info("TelebirrService: Handling callback. Request data: ", $request->all());

        $payload = $request->all();

        if (! $this->simulate && $this->isConfigured()) {
            $signature = $request->input('sign') ?? $request->header('X-Telebirr-Signature');
            if (! $signature || ! $this->verifySignature($payload, $signature)) {
                Log::warning('TelebirrService: Signature verification failed for callback.', ['payload' => $payload]);

                return [
                    'status' => 'failed',
                    'message' => 'Invalid Telebirr signature.',
                    'order_id' => $request->input('outTradeNo'),
                    'transaction_id' => null,
                    'amount' => null,
                ];
            }
        }

        $telebirrStatus = $request->input('status', 'success'); // Assume success by default for simulation
        $orderId = $request->input('outTradeNo'); // Your unique order ID
        $telebirrTransactionId = $request->input('tradeNo', 'TEL_' . time()); // Telebirr's transaction ID
        $amount = $request->input('totalAmount', 0);

        if ($telebirrStatus === 'success') {
            Log::info("TelebirrService: Payment successful for Order ID: {$orderId}, Telebirr Transaction ID: {$telebirrTransactionId}");
            return [
                'status' => 'completed',
                'message' => 'Payment confirmed by Telebirr callback.',
                'order_id' => $orderId,
                'transaction_id' => $telebirrTransactionId,
                'amount' => $amount / 100, // Telebirr often sends amount in cents
            ];
        } else {
            Log::warning("TelebirrService: Payment failed/cancelled for Order ID: {$orderId}. Telebirr Status: {$telebirrStatus}");
            return [
                'status' => 'failed',
                'message' => 'Payment not successful as per Telebirr callback.',
                'order_id' => $orderId,
                'transaction_id' => $telebirrTransactionId,
                'amount' => $amount / 100,
            ];
        }
    }

    /**
     * Verify the status of a Telebirr transaction (e.g., if callback was missed).
     *
     * @param string $orderId Your unique order ID
     * @return array
     */
    public function verifyTransactionStatus(string $orderId): array
    {
        Log::info("TelebirrService: Verifying transaction status for Order ID: {$orderId}");

        if ($this->simulate || ! $this->isConfigured()) {
            return [
                'status' => 'completed', // Assume it completed successfully for simulation
                'message' => 'Transaction verified successfully (simulated).',
                'transaction_id' => 'TEL_' . time() . '_verified',
                'amount' => 100.00, // Placeholder amount
            ];
        }

        $params = [
            'appId' => $this->appId,
            'traceNo' => $orderId,
            'timestamp' => now()->timestamp,
            'nonceStr' => Str::random(16),
        ];

        $params['sign'] = $this->signPayload($params);

        try {
            $response = Http::baseUrl(rtrim($this->baseUrl, '/').'/')
                ->timeout(10)
                ->acceptJson()
                ->post('merchant/order/query', $params);

            if (! $response->successful()) {
                Log::warning('TelebirrService: verifyTransactionStatus call failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'status' => 'unknown',
                    'message' => 'Unable to verify Telebirr status at this time.',
                    'transaction_id' => null,
                    'amount' => null,
                ];
            }

            $body = $response->json();

            return [
                'status' => data_get($body, 'data.tradeStatus', 'unknown'),
                'message' => data_get($body, 'msg', 'Query completed.'),
                'transaction_id' => data_get($body, 'data.tradeNo'),
                'amount' => (data_get($body, 'data.totalAmount') ?? 0) / 100,
            ];
        } catch (\Throwable $e) {
            Log::error('TelebirrService: verifyTransactionStatus exception.', ['error' => $e->getMessage()]);
            return [
                'status' => 'unknown',
                'message' => 'Verification failed: '.$e->getMessage(),
                'transaction_id' => null,
                'amount' => null,
            ];
        }
    }

    public function shouldVerifyCallback(): bool
    {
        return ! $this->simulate && $this->isConfigured();
    }

    public function validateCallback(array $payload, ?string $signature): bool
    {
        if (! $this->shouldVerifyCallback()) {
            return true;
        }

        if (! $signature) {
            return false;
        }

        return $this->verifySignature($payload, $signature);
    }

    // --- Internal helpers ---

    protected function buildRequestPayload(string $orderId, float $amount, string $nonce, string $subject): array
    {
        $bizContent = [
            'subject' => $subject,
            'outTradeNo' => $orderId,
            'totalAmount' => (int) round($amount * 100),
            'notifyUrl' => $this->notifyUrl,
            'returnUrl' => $this->returnUrl,
            'merchantId' => $this->merchantId,
        ];

        $signature = $this->signPayload($bizContent);

        return [
            'appId' => $this->appId,
            'appKey' => $this->appKey,
            'nonceStr' => $nonce,
            'timestamp' => now()->timestamp,
            'bizContent' => $bizContent,
            'sign' => $signature,
        ];
    }

    protected function signPayload(array $payload): ?string
    {
        if (! $this->merchantPrivateKey) {
            return null;
        }

        $normalized = $this->normalizePayload($payload);

        $signature = null;
        $privateKey = openssl_pkey_get_private($this->merchantPrivateKey);

        if (! $privateKey) {
            Log::error('TelebirrService: Unable to load merchant private key.');
            return null;
        }

        openssl_sign($normalized, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return $signature ? base64_encode($signature) : null;
    }

    protected function verifySignature(array $payload, string $signature): bool
    {
        if (! $this->publicKey) {
            return false;
        }

        $normalized = $this->normalizePayload(array_filter($payload, fn ($value, $key) => $key !== 'sign', ARRAY_FILTER_USE_BOTH));

        $publicKey = openssl_pkey_get_public($this->formatKey($this->publicKey, 'PUBLIC'));

        if (! $publicKey) {
            Log::error('TelebirrService: Unable to load Telebirr public key.');
            return false;
        }

        return openssl_verify($normalized, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    protected function normalizePayload(array $payload): string
    {
        ksort($payload);

        $segments = [];

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            if ($value === null || $value === '') {
                continue;
            }

            $segments[] = $key.'='.$value;
        }

        return implode('&', $segments);
    }

    protected function formatKey(?string $key, string $type = 'PRIVATE'): ?string
    {
        if (! $key) {
            return null;
        }

        if (Str::contains($key, 'BEGIN')) {
            return $key;
        }

        $wrapped = chunk_split(str_replace(["\r", "\n", ' '], '', $key), 64, "\n");

        return "-----BEGIN {$type} KEY-----\n{$wrapped}-----END {$type} KEY-----";
    }

    protected function isConfigured(): bool
    {
        return ! empty($this->appId)
            && ! empty($this->appKey)
            && ! empty($this->merchantId)
            && ! empty($this->merchantPrivateKey)
            && ! empty($this->notifyUrl)
            && ! empty($this->returnUrl);
    }

    protected function simulateResponse(string $orderId, float $amount): array
    {
        return [
            'status' => 'success',
            'message' => 'Payment initiation successful (simulation).',
            'transaction_id' => null,
            'redirect_url' => 'https://telebirr-sandbox.test/checkout?orderId='.$orderId.'&amount='.$amount,
        ];
    }

}
