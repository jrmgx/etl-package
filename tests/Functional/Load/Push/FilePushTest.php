<?php

namespace Jrmgx\Etl\Tests\Functional\Load\Push;

use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Load\Push\FilePush;
use Jrmgx\Etl\Tests\BaseTestCase;

class FilePushTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            ['Anything', new PushConfig([
                'type' => 'file',
                'uri' => './data/data_out.txt',
            ])],
            ['Anything Else', new PushConfig([
                'type' => 'file',
                'uri' => __DIR__ . '/../../../data/data_out.txt',
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testPush(mixed $resource, PushConfig $config): void
    {
        $outFilename = __DIR__ . '/../../../data/data_out.txt';
        if (file_exists($outFilename)) {
            unlink($outFilename);
        }

        $etl = $this->etl(pushServices: $this->etlServiceStub([
            'file' => new FilePush(),
        ]));
        $etl->push($resource, $config);
        $this->assertFileExists($outFilename);
        $this->assertSame($resource, file_get_contents($outFilename));

        unlink($outFilename);
    }
}
