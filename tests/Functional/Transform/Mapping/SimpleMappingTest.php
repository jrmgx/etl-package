<?php

namespace Jrmgx\Etl\Tests\Functional\Transform\Mapping;

use Jrmgx\Etl\Config\MappingConfig;
use Jrmgx\Etl\Tests\BaseTestCase;
use Jrmgx\Etl\Transform\Mapping\SimpleMapping;

class SimpleMappingTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        $data = [
            ['First_name' => 'bob', 'Age' => 2, 'Grades' => null],
            ['First_name' => 'alice', 'Age' => 3, 'Grades' => ['a' => 4, 'b' => 7]],
            ['First_name' => 'dan', 'Age' => 42],
            ['First_name' => 'cathy', 'Age' => 32, 'Grades' => ['c' => 2]],
        ];

        return [
            [$data, new MappingConfig([
                'type' => 'simple',
                'map' => [
                    'out.name' => 'in.First_name',
                    'out.grade' => 'in.Grades',
                    'out.age' => 'in.Age',
                ],
                'options' => [
                    'flatten' => true,
                ],
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testMap(array $data, MappingConfig $config): void
    {
        $etl = $this->etl(mappingServices: $this->etlServiceStub([
            'simple' => new SimpleMapping(),
        ]));
        $result = $etl->map($data, $config);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertCount(3, $result[0]);

        $this->assertSame('bob', $result[0]['name']);
        $this->assertNull($result[0]['grade']);
        $this->assertSame(2, $result[0]['age']);
        $this->assertSame('{"a":4,"b":7}', $result[1]['grade']);
        $this->assertNull($result[2]['grade']);
        $this->assertSame('{"c":2}', $result[3]['grade']);
        $this->assertSame(32, $result[3]['age']);
    }
}
