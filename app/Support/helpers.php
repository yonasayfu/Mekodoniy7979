<?php

declare(strict_types=1);

use App\Support\Calendars\EthiopianCalendar;
use Carbon\Carbon;
if (! function_exists('format_currency')) {
    /**
     * Format a numeric amount as a currency string.
     */
    function format_currency(float|int|string|null $amount, string $currency = 'ETB'): string
    {
        $value = $amount ?? 0;

        try {
            $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            return $formatter->formatCurrency((float) $value, strtoupper($currency));
        } catch (\Throwable $exception) {
            return number_format((float) $value, 2) . ' ' . strtoupper($currency);
        }
    }
}

if (! function_exists('format_etb')) {
    /**
     * Shortcut for formatting ETB values.
     */
    function format_etb(float|int|string|null $amount): string
    {
        return format_currency($amount, 'ETB');
    }
}

if (! function_exists('ethiopian_date')) {
    /**
     * Return an Ethiopian calendar representation for the provided date.
     */
function ethiopian_date(\DateTimeInterface|string|null $date = null): string
    {
        $carbon = $date ? Carbon::parse($date) : Carbon::now();
        return EthiopianCalendar::format($carbon);
    }
}
