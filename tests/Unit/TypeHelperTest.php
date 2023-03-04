<?php

namespace Jrmgx\Etl\Tests\Unit;

use Jrmgx\Etl\Common\TypeHelper;
use Jrmgx\Etl\Tests\BaseTestCase;

class TypeHelperTest extends BaseTestCase
{
    public function testIsFloat(): void
    {
        $this->assertTrue(TypeHelper::isFloat('123.456'));
        $this->assertTrue(TypeHelper::isFloat(123.456));
        $this->assertTrue(TypeHelper::isFloat('123'));
        $this->assertTrue(TypeHelper::isFloat(123));

        $this->assertFalse(TypeHelper::isFloat('ok'));
        $this->assertFalse(TypeHelper::isFloat('12ok'));
        $this->assertFalse(TypeHelper::isFloat(false));
        $this->assertFalse(TypeHelper::isFloat([]));
        $this->assertFalse(TypeHelper::isFloat('12.34.56'));
    }

    public function testIsInt(): void
    {
        $this->assertTrue(TypeHelper::isInt('123'));
        $this->assertTrue(TypeHelper::isInt(123));

        $this->assertFalse(TypeHelper::isInt('123.456'));
        $this->assertFalse(TypeHelper::isInt(123.456));
        $this->assertFalse(TypeHelper::isInt('ok'));
        $this->assertFalse(TypeHelper::isInt('12ok'));
        $this->assertFalse(TypeHelper::isInt(false));
        $this->assertFalse(TypeHelper::isInt([]));
        $this->assertFalse(TypeHelper::isInt('12.34.56'));
    }

    public function testIsStringable(): void
    {
        $this->assertTrue(TypeHelper::isStringable('123'));
        $this->assertTrue(TypeHelper::isStringable(123));
        $this->assertTrue(TypeHelper::isStringable('123.456'));
        $this->assertTrue(TypeHelper::isStringable(123.456));
        $this->assertTrue(TypeHelper::isStringable('ok'));
        $this->assertTrue(TypeHelper::isStringable('12ok'));
        $this->assertTrue(TypeHelper::isStringable(null));
        $this->assertTrue(TypeHelper::isStringable('12.34.56'));

        $this->assertFalse(TypeHelper::isStringable(false));
        $this->assertFalse(TypeHelper::isStringable([]));
    }

    public function testIsComplex(): void
    {
        $this->assertFalse(TypeHelper::isComplex('123.456'));
        $this->assertFalse(TypeHelper::isComplex(123.456));
        $this->assertFalse(TypeHelper::isComplex('123'));
        $this->assertFalse(TypeHelper::isComplex(123));
        $this->assertFalse(TypeHelper::isComplex('ok'));
        $this->assertFalse(TypeHelper::isComplex('12ok'));
        $this->assertFalse(TypeHelper::isComplex(false));
        $this->assertFalse(TypeHelper::isComplex('12.34.56'));
        $this->assertFalse(TypeHelper::isComplex(null));
        $this->assertFalse(TypeHelper::isComplex(true));

        $this->assertTrue(TypeHelper::isComplex([]));
        $this->assertTrue(TypeHelper::isComplex(['a']));
        $this->assertTrue(TypeHelper::isComplex(['a' => 'b']));
        $this->assertTrue(TypeHelper::isComplex(new \stdClass()));
    }

    public static function arrayWithKeys(): array
    {
        return [
            [
                ['name', 'age', 'job', 'date'],
                [[
                    'name' => 'a',
                    'age' => 43,
                    'job' => 'a',
                ], [
                    'name' => 'b',
                    'age' => 41,
                    'job' => 'b',
                ], [
                    'name' => 'a',
                    'age' => 43,
                    'date' => '2023-03-07',
                ]],
            ],
            [
                ['a', 'b', 'c'],
                [[
                    'a' => 'a',
                ], [
                    'b' => 'b',
                ], [
                    'c' => ['c'],
                ]],
            ],
        ];
    }

    /**
     * @dataProvider arrayWithKeys
     */
    public function testWholeKeys(array $keys, array $data): void
    {
        $this->assertCount(0, array_diff($keys, TypeHelper::wholeKeys($data)));
    }
}
