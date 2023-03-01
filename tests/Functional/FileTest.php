<?php

namespace Jrmgx\Etl\Tests\Functional;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Etl;
use Jrmgx\Etl\Extract\Pull\FilePull;
use Jrmgx\Etl\Extract\Read\CsvRead;
use Jrmgx\Etl\Load\Push\FilePush;
use Jrmgx\Etl\Load\Write\JsonWrite;
use Jrmgx\Etl\Tests\BaseTestCase;
use Jrmgx\Etl\Transform\Mapping\ExpressiveMapping;
use Jrmgx\Etl\Transform\Mapping\SimpleMapping;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class FileTest extends BaseTestCase
{
    public function testFileToFile(): void
    {
        $out = __DIR__ . '/../data/data_out.json';
        if (file_exists($out)) {
            unlink($out);
        }

        $configYaml = <<<YAML
extract:
  pull:
    type: file
    uri: ./data/data_in.csv
  read:
    format: csv
    options:
      trim: true
      with_header: ["Name", "Sex", "Age", "Height", "Weight"]

transformers:
  first_transform:
    mapping:
      type: simple

  second_transform:
    mapping:
      type: expressive
      map:
        out.name: in.Name
        out.sex: in.Sex
        out.squared: 'in.Height * in.Weight'

load:
  write:
    format: json
  push:
    type: file
    uri: ./data/data_out.json
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

        /** @var ContainerInterface $mappingServices */
        $mappingServices = $this->createStub(ContainerInterface::class);
        $mappingServices
            ->method('get')
            ->willReturnCallback(fn (string $arg) => match ($arg) {
                'simple' => new SimpleMapping(),
                'expressive' => new ExpressiveMapping(),
            })
        ;

        /** @var ContainerInterface $writeServices */
        $writeServices = $this->createStub(ContainerInterface::class);
        $writeServices
            ->method('get')
            ->willReturn(new JsonWrite())
        ;

        /** @var ContainerInterface $pushServices */
        $pushServices = $this->createStub(ContainerInterface::class);
        $pushServices
            ->method('get')
            ->willReturn(new FilePush())
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

        $this->assertTrue(file_exists($out));

        $data = json_decode(file_get_contents($out), true);
        $this->assertIsArray($data);
        $this->assertCount(18, $data);
        $this->assertIsArray($data[0]);
        $this->assertTrue($data[0] === [
            'name' => 'Alex',
            'sex' => 'M',
            'squared' => 12580,
        ]);

        unlink($out);
    }
}
