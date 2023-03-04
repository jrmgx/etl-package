<?php

namespace Jrmgx\Etl\Extract\Pull;

use Jrmgx\Etl\Config\PullConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTaggedItem(index: 'http')]
class HttpPull implements PullInterface
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
                    ->defaultValue('GET')
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

    public function pull(PullConfig $config): mixed
    {
        $options = $config->resolveOptions(self::optionsDefinition());

        $method = $options['method'];
        unset($options['method']);

        $response = $this->httpClient->request($method, $config->getUri(), $options);

        return $response->getContent();
    }
}
