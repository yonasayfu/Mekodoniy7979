<?php

namespace App\Support\Services;

use App\Models\Pledge;
use App\Models\User;

class RecurringPaymentService
{
    /**
     * Simulate creating a recurring payment subscription.
     * In a real application, this would involve API calls to Telebirr/CBE.
     *
     * @param User $user
     * @param Pledge $pledge
     * @return array
     */
    public function createSubscription(User $user, Pledge $pledge): array
    {
        // Simulate a successful subscription creation
        $subscriptionId = 'SUB' . time() . rand(1000, 9999);
        $nextBillingDate = now()->addMonth()->startOfDay()->toDateTimeString();

        return [
            'status' => 'success',
            'message' => 'Subscription created successfully',
            'subscription_id' => $subscriptionId,
            'next_billing_date' => $nextBillingDate,
            'amount' => $pledge->amount,
            'frequency' => $pledge->frequency,
        ];
    }

    /**
     * Simulate cancelling a recurring payment subscription.
     *
     * @param string $subscriptionId
     * @return array
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        // Simulate a successful cancellation
        return [
            'status' => 'success',
            'message' => "Subscription {$subscriptionId} cancelled successfully",
        ];
    }
}
