<?php

namespace App\Support\Services;

class TelebirrService
{
    /**
     * Simulate a Telebirr payment charge.
     * In a real application, this would involve API calls to Telebirr.
     *
     * @param array $donationDetails
     * @return array
     */
    public function charge(array $donationDetails): array
    {
        // Simulate a successful payment response
        $transactionId = 'TEL' . time() . rand(1000, 9999);

        return [
            'status' => 'success',
            'message' => 'Payment successful',
            'transaction_id' => $transactionId,
            'amount' => $donationDetails['amount'],
            'currency' => 'ETB',
            'paid_at' => now()->toDateTimeString(),
        ];
    }
}