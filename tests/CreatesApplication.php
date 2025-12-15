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
        $this->setEnvIfNotSet('APP_ENV', 'testing');
        $this->setEnvIfNotSet('DB_CONNECTION', 'mysql');
        $this->setEnvIfNotSet('DB_DATABASE', 'laravel_test');
        $this->setEnvIfNotSet('DB_PASSWORD', 'AQWzsxEDC00');

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    private function setEnvIfNotSet(string $key, string $value): void
    {
        if (!isset($_ENV[$key]) && !isset($_SERVER[$key]) && !getenv($key)) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
