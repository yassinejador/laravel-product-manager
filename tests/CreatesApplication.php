<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        // Set environment variables for testing
        putenv('APP_ENV=testing');
        putenv('DB_CONNECTION=mysql');
        putenv('DB_DATABASE=laravel_test');
        putenv('DB_PASSWORD=AQWzsxEDC00');

        $_ENV['APP_ENV'] = 'testing';
        $_ENV['DB_CONNECTION'] = 'mysql';
        $_ENV['DB_DATABASE'] = 'laravel_test';
        $_ENV['DB_PASSWORD'] = 'AQWzsxEDC00';

        $_SERVER['APP_ENV'] = 'testing';
        $_SERVER['DB_CONNECTION'] = 'mysql';
        $_SERVER['DB_DATABASE'] = 'laravel_test';
        $_SERVER['DB_PASSWORD'] = 'AQWzsxEDC00';

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
