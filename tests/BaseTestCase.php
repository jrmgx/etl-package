<?php

namespace Jrmgx\Etl\Tests;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Etl;
use Jrmgx\Etl\EtlComponentInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class BaseTestCase extends TestCase
{
    /**
     * @var array<array{int, string, string, int}>
     */
    private array $warnings = [];

    protected function setUp(): void
    {
        Config::setRootPath(__DIR__);

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

    /**
     * @param array<string, EtlComponentInterface> $keyService
     */
    protected function etlServiceStub(array $keyService = []): ContainerInterface
    {
        /** @var ContainerInterface $container */
        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(fn (string $key) => $keyService[$key])
        ;

        return $container;
    }

    protected function etl(
        ContainerInterface $pullServices = null,
        ContainerInterface $readServices = null,
        ContainerInterface $filterServices = null,
        ContainerInterface $mappingServices = null,
        ContainerInterface $writeServices = null,
        ContainerInterface $pushServices = null,
    ): Etl {
        return new Etl(
            $pullServices ?? $this->etlServiceStub(),
            $readServices ?? $this->etlServiceStub(),
            $filterServices ?? $this->etlServiceStub(),
            $mappingServices ?? $this->etlServiceStub(),
            $writeServices ?? $this->etlServiceStub(),
            $pushServices ?? $this->etlServiceStub(),
        );
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
