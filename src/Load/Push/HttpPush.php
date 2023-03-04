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
            ->ignoreExtraKeys(false)
            ->children()
                ->scalarNode('method')
                    ->defaultValue('POST')
                ->end()
                ->scalarNode('auth_basic')
                    ->info('array containing the username as first value, and optionally the password as the second one')
                    ->defaultNull()
                ->end()
                ->scalarNode('auth_bearer')
                    ->info('a token enabling HTTP Bearer authorization')
                    ->defaultNull()
                ->end()
                ->arrayNode('headers')
                    ->info('array of header names and values: "X-My-Header: My-Value"')
                    ->ignoreExtraKeys(false)
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    // https://symfony.com/doc/current/http_client.html
    public function push(mixed $resource, PushConfig $config): mixed
    {
        $options = $config->resolveOptions(self::optionsDefinition());

        $method = $options['method'];
        unset($options['method']);

        $options['body'] = $resource;

        $response = $this->httpClient->request($method, $config->getUri(), $options);

        return $response->getContent();
    }
}
