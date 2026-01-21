<?php

declare(strict_types=1);

namespace App\Support\Calendars;

use Carbon\CarbonInterface;

class EthiopianCalendar
{
    private const MONTHS = [
        'Meskerem',
        'Tikimt',
        'Hidar',
        'Tahsas',
        'Tir',
        'Yekatit',
        'Megabit',
        'Miyazya',
        'Ginbot',
        'Sene',
        'Hamle',
        'Nehase',
        'Pagumen',
    ];

    /**
     * Convert a Gregorian date to the Ethiopian calendar.
     */
    public static function convert(CarbonInterface $date): array
    {
        $jd = self::gregorianToJulianDay(
            (int) $date->year,
            (int) $date->month,
            (int) $date->day,
        );

        $ethiopianDay = self::julianDayToEthiopian($jd);

        return [
            'year' => $ethiopianDay['year'],
            'month' => $ethiopianDay['month'],
            'day' => $ethiopianDay['day'],
            'month_name' => self::MONTHS[$ethiopianDay['month'] - 1] ?? 'Unknown',
            'formatted' => sprintf(
                '%02d %s %d',
                $ethiopianDay['day'],
                self::MONTHS[$ethiopianDay['month'] - 1] ?? 'Unknown',
                $ethiopianDay['year'],
            ),
        ];
    }

    /**
     * Format a Carbon date using Ethiopian month names.
     */
    public static function format(CarbonInterface $date): string
    {
        $info = self::convert($date);
        return $info['formatted'];
    }

    /**
     * Convert a Gregorian date to a Julian Day number.
     */
    private static function gregorianToJulianDay(int $year, int $month, int $day): int
    {
        $a = (int) floor((14 - $month) / 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;

        return $day
            + (int) floor((153 * $m + 2) / 5)
            + 365 * $y
            + (int) floor($y / 4)
            - (int) floor($y / 100)
            + (int) floor($y / 400)
            - 32045;
    }

    /**
     * Convert a Julian Day number to the Ethiopian calendar.
     */
    private static function julianDayToEthiopian(int $jd): array
    {
        $j = $jd - 1723856;
        $n = (int) floor($j / 1461);
        $r = $j % 1461;
        $o = (int) floor($r / 365);

        $year = 4 * $n + $o;
        $dayOfYear = $r % 365;

        if ($dayOfYear === 365) {
            $dayOfYear = 0;
            $year += 1;
        }

        $month = (int) floor($dayOfYear / 30) + 1;
        $day = ($dayOfYear % 30) + 1;

        return [
            'year' => $year - 1,
            'month' => $month,
            'day' => $day,
        ];
    }
}
