<?php

namespace Jrmgx\Etl\Tests\Functional\Extract\Pull;

use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Extract\Pull\HttpPull;
use Jrmgx\Etl\Tests\BaseTestCase;
use Symfony\Component\HttpClient\CurlHttpClient;

class HttpPullTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [new PullConfig([
                'type' => 'http',
                'uri' => 'https://social.gangneux.net/users/jerome/outbox?page=true',
            ])],
            // TODO add more cases
//            [new PullConfig([
//                'type' => 'http',
//                'uri' => 'https://social.gangneux.net/users/jerome/outbox?page=true',
//                'options' => [
//                    'method' => 'POST',
//                ]
//            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @group Network
     */
    public function testPull(PullConfig $config): void
    {
        $etl = $this->etl(pullServices: $this->etlServiceStub([
            'http' => new HttpPull(new CurlHttpClient()),
        ]));
        $result = $etl->pull($config);
        $this->assertStringStartsWith(
            '{"@context":["https://www.w3.org/ns/activitystreams",{"ostatus":"http://ostatus.org#"',
            $result
        );
        $this->assertStringEndsWith(']}}}}]}', $result);
        $this->assertGreaterThan(500, mb_strlen($result));
    }
}
