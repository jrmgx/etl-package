<?php

namespace Jrmgx\Etl\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * @var array<array{int, string, string, int}>
     */
    private array $warnings = [];

    protected function setUp(): void
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            $this->warnings[] = [$errno, $errstr, $errfile, $errline];
        });
    }

    protected function expectWarning(string $expectedMessage, string $message = ''): void
    {
        if (0 === \count($this->warnings)) {
            $this->fail('No warning have been triggered');
        }

        $warnings = $this->warnings;
        $lastWarning = array_shift($warnings);
        $this->warnings = $warnings;

        $this->assertSame($expectedMessage, $lastWarning[1], $message);
    }

    protected function tearDown(): void
    {
        if (\count($this->warnings) > 0) {
            $this->fail(
                'Some warning did not have been handled:' . \PHP_EOL .
                json_encode($this->warnings)
            );
        }
    }
}
