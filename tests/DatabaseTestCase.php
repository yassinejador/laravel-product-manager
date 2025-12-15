<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Configure test database
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'laravel_test']);
    }
}
