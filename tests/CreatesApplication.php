<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * @var array<string, string>
     */
    private array $testingEnvironment = [
        'APP_ENV' => 'testing',
        'APP_DEBUG' => 'false',
        'BROADCAST_DRIVER' => 'log',
        'CACHE_DRIVER' => 'array',
        'CACHE_STORE' => 'array',
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => ':memory:',
        'MAIL_DRIVER' => 'array',
        'MAIL_MAILER' => 'array',
        'QUEUE_CONNECTION' => 'sync',
        'QUEUE_DRIVER' => 'sync',
        'SESSION_DRIVER' => 'array',
    ];

    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $this->setTestingEnvironment();

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    private function setTestingEnvironment(): void
    {
        foreach ($this->testingEnvironment as $key => $value) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
