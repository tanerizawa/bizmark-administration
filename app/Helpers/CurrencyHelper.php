<?php

namespace App\Helpers;

/**
 * ===================================================================
 * CURRENCY HELPER - STANDARD UNTUK SELURUH SISTEM
 * ===================================================================
 * 
 * Format: 1,234.56 (comma = thousand separator, period = decimal)
 * 
 * Usage di Blade:
 * {{ CurrencyHelper::format($amount) }}
 * 
 * Usage di Controller/Model:
 * use App\Helpers\CurrencyHelper;
 * $formatted = CurrencyHelper::format($amount);
 * 
 * @version 1.0
 * @date 2025-11-22
 */
class CurrencyHelper
{
    /**
     * Default currency symbol
     */
    const CURRENCY_SYMBOL = 'Rp ';
    
    /**
     * Format number to currency display
     * 
     * @param float|int|string $value - Value to format
     * @param int $decimals - Number of decimal places (default: 2)
     * @param bool $withSymbol - Include currency symbol (default: true)
     * @return string - Formatted currency string (e.g., "Rp 1,234.56")
     */
    public static function format($value, int $decimals = 2, bool $withSymbol = true): string
    {
        if ($value === null || $value === '') {
            return $withSymbol ? self::CURRENCY_SYMBOL . '0' : '0';
        }
        
        $number = self::normalize($value);
        
        // Format: number_format(value, decimals, decimal_point, thousands_separator)
        // For 1,234.56 format: dec_point = '.', thousands_sep = ','
        $formatted = number_format($number, $decimals, '.', ',');
        
        return $withSymbol ? self::CURRENCY_SYMBOL . $formatted : $formatted;
    }
    
    /**
     * Format number to compact format (e.g., 1.2M, 450K)
     * 
     * @param float|int|string $value - Value to format
     * @param int $decimals - Number of decimal places (default: 1)
     * @param bool $withSymbol - Include currency symbol (default: true)
     * @return string - Compact formatted currency
     */
    public static function formatCompact($value, int $decimals = 1, bool $withSymbol = true): string
    {
        $number = self::normalize($value);
        $symbol = $withSymbol ? self::CURRENCY_SYMBOL : '';
        
        if ($number >= 1000000000) {
            return $symbol . number_format($number / 1000000000, $decimals, '.', ',') . 'B';
        } elseif ($number >= 1000000) {
            return $symbol . number_format($number / 1000000, $decimals, '.', ',') . 'M';
        } elseif ($number >= 1000) {
            return $symbol . number_format($number / 1000, $decimals, '.', ',') . 'K';
        }
        
        return $symbol . number_format($number, 0, '.', ',');
    }
    
    /**
     * Parse formatted currency string to float
     * Handles both Indonesian (1.234,56) and International (1,234.56) formats
     * 
     * @param string|float $value - Value to parse
     * @return float - Numeric value
     */
    public static function parse($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        if (!is_string($value)) {
            return 0.0;
        }
        
        // Remove currency symbol and spaces
        $cleaned = trim(str_replace(['Rp', 'IDR', '$', '€', '£'], '', $value));
        
        return self::normalize($cleaned);
    }
    
    /**
     * Normalize number from various formats to float
     * Supports: Indonesian (1.234,56), International (1,234.56), Raw (1234.56)
     * 
     * @param string|float|int $value - Value to normalize
     * @return float - Normalized number
     */
    public static function normalize($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        if (!is_string($value)) {
            return 0.0;
        }
        
        $cleaned = trim($value);
        
        // Remove any non-numeric characters except comma, period, and minus
        $cleaned = preg_replace('/[^\d,.\-]/', '', $cleaned);
        
        // Count separators to detect format
        $commaCount = substr_count($cleaned, ',');
        $dotCount = substr_count($cleaned, '.');
        
        // Indonesian format: 1.234,56 (dot = thousand, comma = decimal)
        if ($dotCount > 1 || ($commaCount === 1 && $dotCount >= 1 && strpos($cleaned, '.') < strpos($cleaned, ','))) {
            $cleaned = str_replace('.', '', $cleaned); // Remove thousand separator
            $cleaned = str_replace(',', '.', $cleaned); // Convert decimal separator
        }
        // International format: 1,234.56 (comma = thousand, dot = decimal)
        elseif ($commaCount > 0) {
            $cleaned = str_replace(',', '', $cleaned); // Remove thousand separator
        }
        
        return (float) $cleaned;
    }
    
    /**
     * Validate currency value
     * 
     * @param mixed $value - Value to validate
     * @param array $options - Validation options
     * @return array - ['valid' => bool, 'error' => string|null]
     */
    public static function validate($value, array $options = []): array
    {
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? 9999999999999.99; // PostgreSQL NUMERIC(15,2) max
        $allowZero = $options['allow_zero'] ?? false;
        $allowNegative = $options['allow_negative'] ?? false;
        
        $number = self::normalize($value);
        
        if (!is_numeric($number)) {
            return ['valid' => false, 'error' => 'Nilai harus berupa angka'];
        }
        
        if (!$allowZero && $number == 0) {
            return ['valid' => false, 'error' => 'Nilai tidak boleh nol'];
        }
        
        if (!$allowNegative && $number < 0) {
            return ['valid' => false, 'error' => 'Nilai tidak boleh negatif'];
        }
        
        if ($number < $min) {
            return ['valid' => false, 'error' => 'Nilai minimal: ' . self::format($min)];
        }
        
        if ($number > $max) {
            return ['valid' => false, 'error' => 'Nilai maksimal: ' . self::format($max)];
        }
        
        return ['valid' => true, 'error' => null];
    }
    
    /**
     * Format percentage
     * 
     * @param float $part - Part value
     * @param float $total - Total value
     * @param int $decimals - Decimal places (default: 1)
     * @return string - Formatted percentage (e.g., "25.5%")
     */
    public static function formatPercentage(float $part, float $total, int $decimals = 1): string
    {
        if ($total == 0) {
            return '0%';
        }
        
        $percentage = ($part / $total) * 100;
        return number_format($percentage, $decimals, '.', ',') . '%';
    }
    
    /**
     * Calculate difference with sign
     * 
     * @param float $current - Current value
     * @param float $previous - Previous value
     * @param bool $asPercentage - Return as percentage (default: false)
     * @param int $decimals - Decimal places
     * @return string - Formatted difference with +/- sign
     */
    public static function formatDifference(float $current, float $previous, bool $asPercentage = false, int $decimals = 2): string
    {
        $diff = $current - $previous;
        $sign = $diff >= 0 ? '+' : '';
        
        if ($asPercentage && $previous != 0) {
            $percentage = ($diff / abs($previous)) * 100;
            return $sign . number_format($percentage, $decimals, '.', ',') . '%';
        }
        
        return $sign . self::format(abs($diff), $decimals, true);
    }
    
    /**
     * Generate input HTML for currency with dual input system
     * 
     * @param string $name - Input name attribute
     * @param float|null $value - Initial value
     * @param array $attributes - Additional HTML attributes
     * @return string - HTML for currency input
     */
    public static function input(string $name, $value = null, array $attributes = []): string
    {
        $displayId = $attributes['id'] ?? $name . '_display';
        $hiddenId = $attributes['id'] ?? $name;
        $placeholder = $attributes['placeholder'] ?? '0.00';
        $class = $attributes['class'] ?? 'form-control';
        $required = isset($attributes['required']) && $attributes['required'] ? 'required' : '';
        
        $formattedValue = $value ? self::format($value, 2, false) : '';
        $rawValue = $value ?? '';
        
        $html = <<<HTML
        <input type="text" 
               id="{$displayId}" 
               class="{$class}" 
               placeholder="{$placeholder}"
               value="{$formattedValue}"
               inputmode="decimal"
               {$required}>
        <input type="hidden" 
               id="{$hiddenId}" 
               name="{$name}" 
               value="{$rawValue}">
        <script>
            if (typeof setupCurrencyInput === 'function') {
                setupCurrencyInput('{$displayId}', '{$hiddenId}');
            }
        </script>
        HTML;
        
        return $html;
    }
    
    /**
     * Format for API response
     * 
     * @param float|int $value - Value to format
     * @return array - ['raw' => float, 'formatted' => string, 'display' => string]
     */
    public static function toArray($value): array
    {
        $normalized = self::normalize($value);
        
        return [
            'raw' => $normalized,
            'formatted' => number_format($normalized, 2, '.', ','),
            'display' => self::format($normalized, 2, true),
            'compact' => self::formatCompact($normalized, 1, true)
        ];
    }
    
    /**
     * Convert array of values to formatted
     * 
     * @param array $values - Array of values
     * @param int $decimals - Decimal places
     * @param bool $withSymbol - Include currency symbol
     * @return array - Array of formatted values
     */
    public static function formatArray(array $values, int $decimals = 2, bool $withSymbol = true): array
    {
        return array_map(function($value) use ($decimals, $withSymbol) {
            return self::format($value, $decimals, $withSymbol);
        }, $values);
    }
    
    /**
     * Sum array of currency values (handles formatted strings)
     * 
     * @param array $values - Array of values (can be formatted or raw)
     * @return float - Sum of all values
     */
    public static function sum(array $values): float
    {
        return array_reduce($values, function($carry, $value) {
            return $carry + self::normalize($value);
        }, 0.0);
    }
    
    /**
     * Average of currency values
     * 
     * @param array $values - Array of values
     * @return float - Average value
     */
    public static function average(array $values): float
    {
        $count = count($values);
        if ($count === 0) {
            return 0.0;
        }
        
        return self::sum($values) / $count;
    }
}
