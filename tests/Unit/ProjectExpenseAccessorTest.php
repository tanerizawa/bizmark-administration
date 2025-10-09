<?php

namespace Tests\Unit;

use App\Models\ProjectExpense;
use PHPUnit\Framework\TestCase;

class ProjectExpenseAccessorTest extends TestCase
{
    public function test_category_accessors_return_expected_values(): void
    {
        $expense = ProjectExpense::make([
            'category' => 'communication',
        ]);

        $this->assertSame('Komunikasi & Internet', $expense->category_name);
        $this->assertSame('📞', $expense->category_icon);
    }
}
