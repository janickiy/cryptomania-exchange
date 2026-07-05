<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var array<int, callable>
     */
    private array $originalErrorHandlers = [];

    /**
     * @var array<int, callable>
     */
    private array $originalExceptionHandlers = [];

    protected function setUp(): void
    {
        $this->originalErrorHandlers = $this->captureErrorHandlers();
        $this->originalExceptionHandlers = $this->captureExceptionHandlers();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            $this->restoreErrorHandlers();
            $this->restoreExceptionHandlers();
        }
    }

    /**
     * @return array<int, callable>
     */
    private function captureErrorHandlers(): array
    {
        $handlers = [];

        while (true) {
            $previousHandler = set_error_handler(static fn (int $level, string $message): bool => false);
            restore_error_handler();

            if ($previousHandler === null) {
                break;
            }

            $handlers[] = $previousHandler;
            restore_error_handler();
        }

        $handlers = array_reverse($handlers);

        foreach ($handlers as $handler) {
            set_error_handler($handler);
        }

        return $handlers;
    }

    /**
     * @return array<int, callable>
     */
    private function captureExceptionHandlers(): array
    {
        $handlers = [];

        while (true) {
            $previousHandler = set_exception_handler(static fn (Throwable $exception): null => null);
            restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }

            $handlers[] = $previousHandler;
            restore_exception_handler();
        }

        $handlers = array_reverse($handlers);

        foreach ($handlers as $handler) {
            set_exception_handler($handler);
        }

        return $handlers;
    }

    private function restoreErrorHandlers(): void
    {
        while (get_error_handler() !== null) {
            restore_error_handler();
        }

        foreach ($this->originalErrorHandlers as $handler) {
            set_error_handler($handler);
        }
    }

    private function restoreExceptionHandlers(): void
    {
        while (get_exception_handler() !== null) {
            restore_exception_handler();
        }

        foreach ($this->originalExceptionHandlers as $handler) {
            set_exception_handler($handler);
        }
    }
}
