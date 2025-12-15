<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $connection = config('database.default');
        if ($connection === 'mysql') {
            config(['database.connections.mysql.database' => 'laravel_test']);
        }
    }
}
