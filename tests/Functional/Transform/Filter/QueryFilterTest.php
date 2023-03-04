<?php

namespace Jrmgx\Etl\Tests\Functional\Transform\Filter;

use Jrmgx\Etl\Config\FilterConfig;
use Jrmgx\Etl\Tests\BaseTestCase;
use Jrmgx\Etl\Transform\Filter\QueryFilter;

class QueryFilterTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        $data = [
            ['name' => 'Bob', 'age' => 2, 'complex' => null],
            ['name' => 'Alice', 'age' => 3, 'complex' => ['a', 'b', 'c' => 'c']],
            ['name' => 'Dan', 'age' => 42],
            ['name' => 'Cathy', 'age' => 32],
        ];

        return [
            [$data, new FilterConfig([
                'type' => 'query',
                'options' => [
                    'select' => ['name', 'complex'],
                    'where' => 'age < :old',
                    'parameters' => [
                        'old' => '40',
                    ],
                ],
            ])],
            [$data, new FilterConfig([
                'type' => 'query',
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testFilter(array $data, FilterConfig $config): void
    {
        $etl = $this->etl(filterServices: $this->etlServiceStub([
            'query' => new QueryFilter(),
        ]));
        $result = $etl->filter($data, $config);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(3, \count($result));
        $this->assertGreaterThanOrEqual(2, \count($result[0]));
        $this->assertSame('Bob', $result[0]['name']);
        $this->assertNull($result[0]['complex']);
        $this->assertSame('Alice', $result[1]['name']);
        $this->assertSame('b', $result[1]['complex'][1]);
        $this->assertSame('c', $result[1]['complex']['c']);
        $this->assertNull($result[2]['complex']);
    }
}
