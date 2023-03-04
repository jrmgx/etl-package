<?php

namespace Jrmgx\Etl\Tests\Integration;

use Jrmgx\Etl\Common\Database;
use Jrmgx\Etl\Config\FilterConfig;
use Jrmgx\Etl\Tests\BaseTestCase;
use Jrmgx\Etl\Transform\Filter\QueryFilter;

class DatabaseTest extends BaseTestCase
{
    /**
     * @dataProvider columData
     */
    public function testDeduceTypeFromColumn(array $values, string $type): void
    {
        $this->assertTrue(
            ($t = Database::deduceTypeFromColumn($values)) === $type,
            "Error '$type' not deduced from values but '$t': " . json_encode($values)
        );
    }

    /**
     * @dataProvider wholeTable
     */
    public function testFilter(array $values): void
    {
        $filterConfig = new FilterConfig([
            'options' => [
                'select' => ['first name', 'age', 'data'],
                'where' => 'size > :size',
                'parameters' => [
                    'size' => '1',
                ],
            ],
        ]);
        $queryFilter = new QueryFilter();
        $result = $queryFilter->filter($values, $filterConfig);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0]['data']['deep']['is']);
    }

    public static function columData(): array
    {
        return [
            [[], 'blob'],
            [['1', '2', 3], 'integer'],
            [['1', null, '2', 3], 'integer'],
            [['1.23', 4.567, 4], 'float'],
            [['1.23', 4.567, null, 4], 'float'],
            [['1', 'non', 3], 'text'],
            [['text', 'other text', 'some text'], 'text'],
            [['1', [], 'non', 3], 'blob'],
        ];
    }

    public static function wholeTable(): array
    {
        return [
            [[
                [
                    'age' => 13,
                    'first name' => 'Pierre',
                    'size' => 1.23,
                    'data' => [
                        'extra' => 'value',
                        'deep' => ['is' => true],
                    ],
                ], [
                    'age' => 14,
                    'first name' => 'Paul',
                    'size' => 1.13,
                    'data' => ['extra' => 'value'],
                ], [
                    'age' => 18,
                    'first name' => 'Jacques',
                    'size' => 1.93,
                    'data' => ['extra' => 'value'],
                ], [
                    'age' => 12,
                    'first name' => 'Nina',
                    'size' => '0.88',
                    'data' => ['extra' => 'value'],
                ], [
                    'age' => '1',
                    'first name' => 'Elise',
                    'size' => 0.23,
                    'data' => ['extra' => 'value'],
                ],
            ]],
        ];
    }
}
