<?php

namespace Jrmgx\Etl\Tests\Functional\Load\Push;

use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Load\Push\HttpPush;
use Jrmgx\Etl\Tests\BaseTestCase;
use Symfony\Component\HttpClient\CurlHttpClient;

class HttpPushTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        // Request are posted here: https://webhook.site/#!/f24c112b-8344-4fe3-a9e5-53baf36c912f
        return [
            ['Automated test', new PushConfig([
                'type' => 'http',
                'uri' => 'https://webhook.site/f24c112b-8344-4fe3-a9e5-53baf36c912f',
                'options' => [
                    'method' => 'POST',
                    'headers' => [
                        'X-Custom-Header-1: My-First-Value',
                        'X-Custom-Header-2: My-Second-Value',
                    ],
                ],
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @group Network
     */
    public function testPush(mixed $resource, PushConfig $config): void
    {
        $etl = $this->etl(pushServices: $this->etlServiceStub([
            'http' => new HttpPush(new CurlHttpClient()),
        ]));
        $etl->push($resource, $config);
        $this->assertTrue(true);
    }
}
