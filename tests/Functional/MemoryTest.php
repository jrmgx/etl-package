<?php

namespace Jrmgx\Etl\Tests\Functional;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Etl;
use Jrmgx\Etl\Extract\Pull\FilePull;
use Jrmgx\Etl\Extract\Read\CsvRead;
use Jrmgx\Etl\Load\MemoryLoad;
use Jrmgx\Etl\MemoryCommon;
use Jrmgx\Etl\Transform\Filter\NoneFilter;
use Jrmgx\Etl\Transform\Mapping\SimpleMapping;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class MemoryTest extends TestCase
{
    public function testMemoryToMemory(): void
    {
        $configYaml = <<<YAML
extract:
  pull:
    type: file
    uri: ./data/data_in.csv
  read:
    format: csv
    options:
      trim: true

transform:
  mapping:
    type: simple

load:
  write:
    format: memory
  push:
    type: memory
    uri: memory://test
YAML;

        $configFile = Yaml::parse($configYaml);
        $config = new Config($configFile, __DIR__ . '/../');

        /** @var ContainerInterface $pullServices */
        $pullServices = $this->createStub(ContainerInterface::class);
        $pullServices
            ->method('get')
            ->willReturn(new FilePull())
        ;

        /** @var ContainerInterface $readServices */
        $readServices = $this->createStub(ContainerInterface::class);
        $readServices
            ->method('get')
            ->willReturn(new CsvRead())
        ;

        /** @var ContainerInterface $filterServices */
        $filterServices = $this->createStub(ContainerInterface::class);
        $filterServices
            ->method('get')
            ->willReturn(new NoneFilter())
        ;

        /** @var ContainerInterface $mappingServices */
        $mappingServices = $this->createStub(ContainerInterface::class);
        $mappingServices
            ->method('get')
            ->willReturn(new SimpleMapping())
        ;

        /** @var ContainerInterface $writeServices */
        $writeServices = $this->createStub(ContainerInterface::class);
        $writeServices
            ->method('get')
            ->willReturn(new MemoryLoad())
        ;

        /** @var ContainerInterface $pushServices */
        $pushServices = $this->createStub(ContainerInterface::class);
        $pushServices
            ->method('get')
            ->willReturn(new MemoryLoad())
        ;

        $etl = new Etl(
            $pullServices,
            $readServices,
            $filterServices,
            $mappingServices,
            $writeServices,
            $pushServices,
        );

        $etl->execute($config);

        $memoryClass = new class() extends MemoryCommon {
            public static function get(): mixed
            {
                return self::$memory;
            }
        };

        $this->assertTrue($memoryClass::get()['test'][0] === [
            'Name' => 'Alex',
            'Sex' => 'M',
            'Age' => '41',
            'Height (in)' => '74',
            'Weight (lbs)' => '170',
        ]);
    }
}
