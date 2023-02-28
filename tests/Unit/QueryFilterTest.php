<?php

namespace Jrmgx\Etl\Tests\Unit;

use Jrmgx\Etl\Transform\Filter\QueryFilter;
use PHPUnit\Framework\TestCase;

class QueryFilterTest extends TestCase
{
    public function testIsFloat(): void
    {
        $class = new class() extends QueryFilter {
            public static function isFloatPublic(mixed $candidate): bool
            {
                return self::isFloat($candidate);
            }
        };

        $this->assertTrue($class::isFloatPublic('123.456'));
        $this->assertTrue($class::isFloatPublic(123.456));
        $this->assertTrue($class::isFloatPublic('123'));
        $this->assertTrue($class::isFloatPublic(123));

        $this->assertFalse($class::isFloatPublic('ok'));
        $this->assertFalse($class::isFloatPublic('12ok'));
        $this->assertFalse($class::isFloatPublic(false));
        $this->assertFalse($class::isFloatPublic([]));
        $this->assertFalse($class::isFloatPublic('12.34.56'));
    }

    public function testIsInt(): void
    {
        $class = new class() extends QueryFilter {
            public static function isIntPublic(mixed $candidate): bool
            {
                return self::isInt($candidate);
            }
        };

        $this->assertTrue($class::isIntPublic('123'));
        $this->assertTrue($class::isIntPublic(123));

        $this->assertFalse($class::isIntPublic('123.456'));
        $this->assertFalse($class::isIntPublic(123.456));
        $this->assertFalse($class::isIntPublic('ok'));
        $this->assertFalse($class::isIntPublic('12ok'));
        $this->assertFalse($class::isIntPublic(false));
        $this->assertFalse($class::isIntPublic([]));
        $this->assertFalse($class::isIntPublic('12.34.56'));
    }

    public function testIsStringable(): void
    {
        $class = new class() extends QueryFilter {
            public static function isStringablePublic(mixed $candidate): bool
            {
                return self::isStringable($candidate);
            }
        };

        $this->assertTrue($class::isStringablePublic('123'));
        $this->assertTrue($class::isStringablePublic(123));
        $this->assertTrue($class::isStringablePublic('123.456'));
        $this->assertTrue($class::isStringablePublic(123.456));
        $this->assertTrue($class::isStringablePublic('ok'));
        $this->assertTrue($class::isStringablePublic('12ok'));
        $this->assertTrue($class::isStringablePublic(null));
        $this->assertTrue($class::isStringablePublic('12.34.56'));

        $this->assertFalse($class::isStringablePublic(false));
        $this->assertFalse($class::isStringablePublic([]));
    }
}
