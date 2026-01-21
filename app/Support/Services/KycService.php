<?php

declare(strict_types=1);

namespace App\Support\Services;

use Illuminate\Support\Facades\Config;

class KycService
{
    public function shouldRequire(float $amount, string $currency = 'ETB'): bool
    {
        $usdEquivalent = $this->convertToUsd($amount, $currency);

        return $usdEquivalent >= (float) Config::get('kyc.threshold_usd', 500);
    }

    private function convertToUsd(float $amount, string $currency): float
    {
        if (strtoupper($currency) === 'USD') {
            return $amount;
        }

        $rate = (float) Config::get('kyc.usd_to_etb_rate', 55);
        if ($rate <= 0) {
            return $amount;
        }

        return $amount / $rate;
    }
}
