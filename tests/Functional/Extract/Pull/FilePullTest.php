<?php

namespace Jrmgx\Etl\Tests\Functional\Extract\Pull;

use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Extract\Pull\FilePull;
use Jrmgx\Etl\Tests\BaseTestCase;

class FilePullTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [new PullConfig([
                'type' => 'file',
                'uri' => './data/data_in.csv',
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testPull(PullConfig $config): void
    {
        $etl = $this->etl(pullServices: $this->etlServiceStub([
            'file' => new FilePull(),
        ]));
        $result = $etl->pull($config);
        $this->assertStringStartsWith(
            '"Name",     "Sex", "Age", "Height (in)", "Weight (lbs)"' . \PHP_EOL .
            '"Alex",       "M",   41,       74,      170',
            $result
        );
        $this->assertStringEndsWith(
            '"Ruth",       "F",   28,       65,      131' . \PHP_EOL . \PHP_EOL,
            $result
        );
        $this->assertSame(849, \mb_strlen($result));
    }
}
