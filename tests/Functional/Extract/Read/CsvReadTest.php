<?php

namespace Jrmgx\Etl\Tests\Functional\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\Read\CsvRead;
use Jrmgx\Etl\Tests\BaseTestCase;

class CsvReadTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [
                '"Name", "Sex", "Age"' . \PHP_EOL .
                '"Alex", "M", 41' . \PHP_EOL .
                '"Bert", "M", 42' . \PHP_EOL,
                new ReadConfig([
                    'format' => 'csv',
            ])],
            [
                "'Name'; 'Sex'; 'Age'" . \PHP_EOL .
                "'Alex'; ' M '; 41" . \PHP_EOL .
                "'Bert'; ' M '; 42" . \PHP_EOL,
                new ReadConfig([
                    'format' => 'csv',
                    'options' => [
                        'trim' => true,
                        'header' => true,
                        'separator' => ';',
                        'enclosure' => "'",
                    ],
                ])],
            [file_get_contents(__DIR__ . '/../../../data/data_in.csv'), new ReadConfig([
                'format' => 'csv',
                'options' => [
                    'with_header' => ['Name', 'Sex', 'Age', 'Height', 'Weight'],
                ],
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @param mixed $resource a string containing a valid CSV representation of some values
     */
    public function testRead(mixed $resource, ReadConfig $config): void
    {
        $etl = $this->etl(readServices: $this->etlServiceStub([
            'csv' => new CsvRead(),
        ]));
        $result = $etl->read($resource, $config);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(3, $result[0]);
        $this->assertArrayHasKey('Name', $result[0]);
        $this->assertArrayHasKey('Sex', $result[0]);
        $this->assertArrayHasKey('Age', $result[0]);
        $this->assertSame('M', $result[0]['Sex']);
    }
}
