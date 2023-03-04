<?php

namespace Jrmgx\Etl\Tests\Functional\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\Read\JsonRead;
use Jrmgx\Etl\Tests\BaseTestCase;

class JsonReadTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [json_encode([
                ['name' => 'Alice', 'city' => 'Alice-city', 'age' => 11],
                ['name' => 'Bob', 'city' => 'Bob-city', 'age' => 42],
                ['name' => 'Carla', 'city' => 'Carla-city', 'age' => 4],
            ]), new ReadConfig([
                'format' => 'json',
            ])],
            // TODO add more cases
//            [json_encode(['Letters' => ['a', 'b', 'c']]), new ReadConfig([
//                'format' => 'json',
//            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @param mixed $resource a string containing a valid JSON representation of some values
     */
    public function testRead(mixed $resource, ReadConfig $config): void
    {
        $etl = $this->etl(readServices: $this->etlServiceStub([
            'json' => new JsonRead(),
        ]));
        $result = $etl->read($resource, $config);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(3, $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('city', $result[0]);
        $this->assertArrayHasKey('age', $result[0]);
        $this->assertSame(11, $result[0]['age']);
    }
}
