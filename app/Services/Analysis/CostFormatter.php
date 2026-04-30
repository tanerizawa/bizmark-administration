<?php

namespace App\Services\Analysis;

/**
 * Adaptive Rupiah formatter for permit cost estimation.
 *
 * Output examples:
 *   format(0)        => "Rp 0"
 *   format(750_000)  => "Rp 750rb"
 *   format(3_500_000)=> "Rp 3,5 Juta"
 *   format(2_500_000_000) => "Rp 2,5M"
 */
class CostFormatter
{
    public static function format(float $value): string
    {
        if ($value >= 1_000_000_000) {
            return 'Rp '.number_format($value / 1_000_000_000, 1, ',', '.').'M';
        }

        if ($value >= 1_000_000) {
            $formatted = number_format($value / 1_000_000, 1, ',', '.');
            // Strip trailing ,0 → "3,0 Juta" => "3 Juta"
            $formatted = rtrim(rtrim($formatted, '0'), ',');

            return 'Rp '.$formatted.' Juta';
        }

        if ($value >= 1000) {
            return 'Rp '.number_format($value / 1000, 0, ',', '.').'rb';
        }

        if ($value > 0) {
            return 'Rp '.number_format($value, 0, ',', '.');
        }

        return 'Rp 0';
    }

    public static function range(float $min, float $max): string
    {
        if ($min == 0 && $max == 0) {
            return 'Rp 0';
        }

        if ($min == $max) {
            return self::format($min);
        }

        return self::format($min).' - '.self::format($max);
    }
}
