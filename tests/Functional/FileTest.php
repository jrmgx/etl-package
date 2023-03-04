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
use Symfony\Component\Yaml\Yaml;

class FileTest extends BaseTestCase
{
    public function testFileToFile(): void
    {
        $out = __DIR__ . '/../data/data_out.json';
        if (file_exists($out)) {
            unlink($out);
        }

        $configFile = Yaml::parseFile(__DIR__ . '/../config/FileTest.yaml');
        $config = new Config($configFile, __DIR__ . '/../');

        $etl = new Etl(
            pullServices: $this->etlServiceStub(['file' => new FilePull()]),
            readServices: $this->etlServiceStub(['csv' => new CsvRead()]),
            filterServices: $this->etlServiceStub(),
            mappingServices: $this->etlServiceStub([
                'simple' => new SimpleMapping(),
                'expressive' => new ExpressiveMapping(),
            ]),
            writeServices: $this->etlServiceStub(['json' => new JsonWrite()]),
            pushServices: $this->etlServiceStub(['file' => new FilePush()]),
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
