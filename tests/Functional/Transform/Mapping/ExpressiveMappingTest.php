<?php

namespace Jrmgx\Etl\Tests\Functional\Transform\Mapping;

use Jrmgx\Etl\Config\MappingConfig;
use Jrmgx\Etl\Tests\BaseTestCase;
use Jrmgx\Etl\Transform\Mapping\ExpressiveMapping;

class ExpressiveMappingTest extends BaseTestCase
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
                'type' => 'expressive',
                'map' => [
                    'out.name' => 'in.First_name',
                    // The expression is a bit complex because `.Grades` does not always exist on the object
                    'out.grade' => '(in.Grades.a ?? null) ? "A" : "Other"',
                    'out.age' => 'in.Age * 1000',
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
            'expressive' => new ExpressiveMapping(),
        ]));
        $result = $etl->map($data, $config);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertCount(3, $result[0]);

        $this->assertSame('bob', $result[0]['name']);
        $this->assertSame('Other', $result[0]['grade']);
        $this->assertSame(2000, $result[0]['age']);
        $this->assertSame('A', $result[1]['grade']);
        $this->assertSame('Other', $result[3]['grade']);
        $this->assertSame(32000, $result[3]['age']);
    }
}
