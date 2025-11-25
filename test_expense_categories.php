<?php
/**
 * Test Expense Categories Structure
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProjectExpense;

echo "=== Testing Expense Categories Structure ===\n\n";

// Get categories by group
$categoriesByGroup = ProjectExpense::categoriesByGroup();

echo "Total Groups: " . count($categoriesByGroup) . "\n\n";

foreach ($categoriesByGroup as $groupName => $categories) {
    echo "Group: {$groupName}\n";
    echo "  Categories: " . count($categories) . "\n";
    
    // Show first category structure
    if (count($categories) > 0) {
        $firstCategory = $categories[0];
        echo "  Sample structure:\n";
        echo "    Keys: " . implode(', ', array_keys($firstCategory)) . "\n";
        echo "    Value: " . ($firstCategory['value'] ?? 'MISSING') . "\n";
        echo "    Label: " . ($firstCategory['label'] ?? 'MISSING') . "\n";
        echo "    Icon: " . ($firstCategory['icon'] ?? 'MISSING') . "\n";
        
        // Check if 'value' key exists
        if (isset($firstCategory['value'])) {
            echo "    ✓ 'value' key EXISTS\n";
        } else {
            echo "    ✗ 'value' key MISSING!\n";
        }
    }
    echo "\n";
}

echo "=== Test Complete ===\n";
