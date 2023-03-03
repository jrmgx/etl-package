<?php

namespace Jrmgx\Etl\Load\Push;

use Jrmgx\Etl\Config\PushConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTaggedItem(index: 'http')]
class HttpPush implements PushInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public static function optionsDefinition(): ?TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->ignoreExtraKeys(false) // TODO
            ->children()
                ->scalarNode('method')
                ->defaultValue('GET')
            ->end()
        ;

        return $treeBuilder;
    }

    // https://symfony.com/doc/current/http_client.html
    public function push(mixed $resource, PushConfig $config): void
    {
        $options = $config->resolveOptions(self::optionsDefinition());

        $method = $options['method'];
        unset($options['method']);
        unset($options['body']);

        $response = $this->httpClient->request($method, $config->getUri(), array_merge($options, [
            'body' => $resource,
        ]));
        if (200 !== $response->getStatusCode()) {
            throw new \Exception();
        }

        $response->getContent();
    }
}
