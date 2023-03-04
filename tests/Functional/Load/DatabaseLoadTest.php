<?php

namespace Jrmgx\Etl\Tests\Functional\Load;

use Jrmgx\Etl\Common\Database;
use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Load\DatabaseLoad;
use Jrmgx\Etl\Tests\BaseTestCase;

class DatabaseLoadTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [[[
                'key' => 'some key',
                'value' => 'some value',
            ], [
                'key' => 'some other key',
                'value' => 'some other value',
            ]], new WriteConfig([
                'format' => 'database',
            ]), new PushConfig([
                'type' => 'database',
                'uri' => 'sqlite://./data/database_out.sqlite',
                'options' => [
                    'into' => 'key_values',
                ],
            ])],
        ];
    }

    /**
     * @param array<mixed> $resource
     *
     * @dataProvider configProvider
     */
    public function testLoad(array $resource, WriteConfig $writeConfig, PushConfig $pushConfig): void
    {
        $database = new class('sqlite://./data/database_out.sqlite') extends Database {
            public function __construct(private string $uri)
            {
            }

            public function reset(): void
            {
                self::getConnection($this->uri)
                    ->executeQuery('DELETE FROM "key_values"')
                ;
            }

            public function count(): int
            {
                return self::getConnection($this->uri)
                    ->executeQuery('SELECT COUNT(*) FROM "key_values"')
                    ->fetchFirstColumn()[0]
                ;
            }
        };
        $database->reset();
        $databaseLoad = new DatabaseLoad();
        $etl = $this->etl(writeServices: $this->etlServiceStub([
            'database' => $databaseLoad,
        ]), pushServices: $this->etlServiceStub([
            'database' => $databaseLoad,
        ]));
        $etl->load($resource, $writeConfig, $pushConfig);
        $this->assertSame(2, $database->count());
    }
}
