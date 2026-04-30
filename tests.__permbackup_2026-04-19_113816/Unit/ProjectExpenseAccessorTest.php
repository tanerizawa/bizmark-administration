<?php

namespace Tests\Unit;

use App\Models\ExpenseCategory;
use App\Models\ProjectExpense;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectExpenseAccessorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ProjectExpense::clearCategoryCache();
        Cache::forget('expense_categories.options');

        Schema::dropIfExists('expense_categories');
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('group')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        ExpenseCategory::query()->create([
            'slug' => 'communication',
            'name' => 'Komunikasi & Internet',
            'group' => null,
            'icon' => '📞',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('expense_categories');
        ProjectExpense::clearCategoryCache();
        Cache::forget('expense_categories.options');
        parent::tearDown();
    }

    public function test_category_accessors_return_expected_values(): void
    {
        $expense = ProjectExpense::make([
            'category' => 'communication',
        ]);

        $this->assertSame('Komunikasi & Internet', $expense->category_name);
        $this->assertSame('📞', $expense->category_icon);
    }
}
