<?php

namespace Jrmgx\Etl\Tests\Functional\Load\Write;

use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Load\Write\JsonWrite;
use Jrmgx\Etl\Tests\BaseTestCase;

class JsonWriteTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [[
                ['name' => 'Alice', 'age' => 3],
                ['name' => 'Bob', 'age' => 2],
                ['name' => 'Cathy', 'age' => 32],
                ['name' => 'Dan', 'age' => 42],
            ], new WriteConfig([
                'format' => 'json',
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @group Network
     */
    public function testWrite(array $data, WriteConfig $config): void
    {
        $etl = $this->etl(writeServices: $this->etlServiceStub([
            'json' => new JsonWrite(),
        ]));
        $result = $etl->write($data, $config);
        $this->assertSame(
            '[{"name":"Alice","age":3},{"name":"Bob","age":2},{"name":"Cathy","age":32},{"name":"Dan","age":42}]',
            $result
        );
    }
}
