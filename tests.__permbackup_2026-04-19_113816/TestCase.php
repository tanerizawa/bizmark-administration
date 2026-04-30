<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $defaultConnection = config('database.default');
        $databaseName = config("database.connections.{$defaultConnection}.database");

        // Safety guard: tests must never run against production database.
        if ($databaseName === 'bizmark_db') {
            throw new RuntimeException('Unsafe test configuration: refusing to run tests against bizmark_db.');
        }
    }
}
