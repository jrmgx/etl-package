<?php

namespace Jrmgx\Etl\Tests\Functional\Load\Write;

use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Load\Write\TwigWrite;
use Jrmgx\Etl\Tests\BaseTestCase;

class TwigWriteTest extends BaseTestCase
{
    public static function configProvider(): array
    {
        return [
            [[
                ['name' => 'Alice', 'age' => 3],
                ['name' => 'Bob', 'age' => 1],
                ['name' => 'Cathy', 'age' => 32],
                ['name' => 'Dan', 'age' => 42],
            ], new WriteConfig([
                'format' => 'twig',
                'options' => [
                    'template' => './data/document_template.html.twig',
                ],
            ])],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @group Network
     */
    public function testWrite(array $data, WriteConfig $config): void
    {
        $etl = $this->etl(writeServices: $this->etlServiceStub([
            'twig' => new TwigWrite(),
        ]));
        $html = <<<HTML
<!doctype html>
<html lang="en">
<head><title>My Document</title></head>
<body>
    <h1>List of People</h1>
    <ul>
            <li>Alice: 3 years old</li>
            <li>Bob: 1 year old</li>
            <li>Cathy: 32 years old</li>
            <li>Dan: 42 years old</li>
        </ul>
</body>
</html>

HTML;
        $result = $etl->write($data, $config);
        $this->assertSame($html, $result);
    }
}
