<?php
/**
 * Test Script for Amount Parser Function
 * Tests various Indonesian and International number formats
 */

// Test data with expected results
$testCases = [
    // Indonesian format (dot as thousand, comma as decimal)
    ['input' => '1.234.567,89', 'expected' => 1234567.89, 'description' => 'Indonesian format with decimals'],
    ['input' => '1.234.567', 'expected' => 1234567, 'description' => 'Indonesian format without decimals'],
    ['input' => '42.485.447,23', 'expected' => 42485447.23, 'description' => 'Large Indonesian amount'],
    
    // International format (comma as thousand, dot as decimal)
    ['input' => '1,234,567.89', 'expected' => 1234567.89, 'description' => 'International format'],
    ['input' => '1,234,567', 'expected' => 1234567, 'description' => 'International format no decimals'],
    
    // Simple formats
    ['input' => '1234567.89', 'expected' => 1234567.89, 'description' => 'No separators with decimal'],
    ['input' => '1234567', 'expected' => 1234567, 'description' => 'No separators no decimal'],
    ['input' => '1234,89', 'expected' => 1234.89, 'description' => 'Small amount with comma decimal'],
    
    // Edge cases
    ['input' => '0', 'expected' => 0, 'description' => 'Zero'],
    ['input' => '', 'expected' => 0, 'description' => 'Empty string'],
    ['input' => '-', 'expected' => 0, 'description' => 'Just dash'],
    ['input' => '3', 'expected' => 3, 'description' => 'Single digit'],
    ['input' => '6.5', 'expected' => 6.5, 'description' => 'Small decimal'],
    
    // With currency symbols
    ['input' => 'Rp 1.234.567,89', 'expected' => 1234567.89, 'description' => 'With Rupiah symbol'],
    ['input' => '$ 1,234.56', 'expected' => 1234.56, 'description' => 'With dollar symbol'],
    
    // Scientific notation (should be handled)
    ['input' => '5.2605120566028E+15', 'expected' => 0, 'description' => 'Scientific notation overflow (should return 0)'],
    ['input' => '2.910950312E+15', 'expected' => 0, 'description' => 'Another overflow case'],
    
    // Real data from error
    ['input' => '485.447,23', 'expected' => 485447.23, 'description' => 'From error log 1'],
    ['input' => '1.678.447,23', 'expected' => 1678447.23, 'description' => 'From error log 2'],
    ['input' => '40.485.447,23', 'expected' => 40485447.23, 'description' => 'From error log 3'],
    ['input' => '14101100000', 'expected' => 14101100000, 'description' => 'Large number no separator'],
    ['input' => '191030100000', 'expected' => 191030100000, 'description' => 'Very large number'],
];

/**
 * Parse amount function (copy from controller)
 */
function parseAmount($amountString)
{
    // Remove all whitespace
    $cleaned = trim($amountString);
    
    // Handle empty or invalid input
    if (empty($cleaned) || $cleaned === '-' || $cleaned === '0') {
        return 0;
    }
    
    // Check for scientific notation BEFORE any other processing
    if (stripos($cleaned, 'E') !== false || stripos($cleaned, 'e') !== false) {
        $value = floatval($cleaned);
        // Scientific notation values are usually too large - return 0
        echo "WARNING: Scientific notation detected: {$amountString} = {$value}. Setting to 0 to prevent overflow.\n";
        return 0;
    }
    
    // Remove currency symbols and other non-numeric characters except dots, commas, and minus
    $cleaned = preg_replace('/[^0-9,.-]/', '', $cleaned);
    
    // Detect format: Indonesian (1.234.567,89) vs International (1,234,567.89)
    // Count dots and commas to determine format
    $dotCount = substr_count($cleaned, '.');
    $commaCount = substr_count($cleaned, ',');
    
    if ($dotCount > 0 && $commaCount > 0) {
        // Both present - determine which is decimal separator
        $lastDotPos = strrpos($cleaned, '.');
        $lastCommaPos = strrpos($cleaned, ',');
        
        if ($lastCommaPos > $lastDotPos) {
            // Indonesian format: 1.234.567,89
            $cleaned = str_replace('.', '', $cleaned); // Remove thousand separator
            $cleaned = str_replace(',', '.', $cleaned); // Convert decimal separator
        } else {
            // International format: 1,234,567.89
            $cleaned = str_replace(',', '', $cleaned); // Remove thousand separator
        }
    } elseif ($commaCount > 0) {
        // Only comma present
        if ($commaCount > 1) {
            // Multiple commas = thousand separators (1,234,567)
            $cleaned = str_replace(',', '', $cleaned);
        } else {
            // Single comma - check position to determine if decimal or thousand
            $parts = explode(',', $cleaned);
            if (isset($parts[1]) && strlen($parts[1]) <= 2) {
                // Likely decimal: 1234,89
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                // Likely thousand: 1,234
                $cleaned = str_replace(',', '', $cleaned);
            }
        }
    } elseif ($dotCount > 1) {
        // Multiple dots = Indonesian thousand separator (1.234.567)
        $cleaned = str_replace('.', '', $cleaned);
    }
    // Single dot with no comma = decimal point (123.45) - leave as is
    
    // Convert to float
    $value = floatval($cleaned);
    
    // Validate range for PostgreSQL NUMERIC(15,2)
    // Maximum absolute value: 9,999,999,999,999.99 (10^13 - 0.01)
    $maxValue = 9999999999999.99;
    
    if (abs($value) > $maxValue) {
        // Log warning and return 0 to prevent database error
        echo "WARNING: Amount value too large: {$amountString} (parsed as {$value}). Setting to 0.\n";
        return 0;
    }
    
    return $value;
}

// Run tests
echo "=== Amount Parser Test Suite ===\n\n";

$passed = 0;
$failed = 0;

foreach ($testCases as $i => $test) {
    $result = parseAmount($test['input']);
    $success = abs($result - $test['expected']) < 0.01; // Allow small floating point difference
    
    if ($success) {
        echo "✓ Test " . ($i + 1) . ": PASSED - {$test['description']}\n";
        echo "  Input: '{$test['input']}' → Output: {$result} (Expected: {$test['expected']})\n\n";
        $passed++;
    } else {
        echo "✗ Test " . ($i + 1) . ": FAILED - {$test['description']}\n";
        echo "  Input: '{$test['input']}'\n";
        echo "  Expected: {$test['expected']}\n";
        echo "  Got: {$result}\n\n";
        $failed++;
    }
}

echo "=== Test Summary ===\n";
echo "Total: " . count($testCases) . " tests\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";

if ($failed === 0) {
    echo "\n🎉 All tests passed!\n";
} else {
    echo "\n⚠️  Some tests failed. Please review.\n";
}
