<?php

namespace Jrmgx\Etl\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    private array $warnings = [];

    protected function setUp(): void
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            $this->warnings[] = $errstr;
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

        $this->assertSame($expectedMessage, $lastWarning, $message);
    }

    protected function tearDown(): void
    {
        if (\count($this->warnings) > 0) {
            $this->fail(
                'Some warning did not have been handled:' . \PHP_EOL .
                implode(\PHP_EOL, $this->warnings)
            );
        }
    }
}
