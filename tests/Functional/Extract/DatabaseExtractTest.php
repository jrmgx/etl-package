<?php

namespace Jrmgx\Etl\Tests\Functional\Extract;

use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\DatabaseExtract;
use Jrmgx\Etl\Tests\BaseTestCase;

class DatabaseExtractTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [new PullConfig([
                'type' => 'database',
                'uri' => 'sqlite://./data/database_in.sqlite',
            ]), new ReadConfig([
                'format' => 'database',
                'options' => [
                    'from' => 'key_values',
                ],
            ])],
            [new PullConfig([
                'type' => 'database',
                'uri' => 'sqlite://./data/database_in.sqlite',
            ]), new ReadConfig([
                'format' => 'database',
                'options' => [
                    'from' => 'key_values',
                    'where' => 'key <> :forbidden_value',
                    'parameters' => [
                        'forbidden_value' => 'foo',
                    ],
                ],
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testExtract(PullConfig $pullConfig, ReadConfig $readConfig): void
    {
        $database = new DatabaseExtract();
        $etl = $this->etl(pullServices: $this->etlServiceStub([
            'database' => $database,
        ]), readServices: $this->etlServiceStub([
            'database' => $database,
        ]));
        $result = $etl->extract($pullConfig, $readConfig);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(3, \count($result));
        $this->assertCount(2, $result[0]);
        $this->assertArrayHasKey('key', $result[0]);
        $this->assertArrayHasKey('value', $result[0]);
        $this->assertSame('lang', $result[0]['key']);
    }
}
