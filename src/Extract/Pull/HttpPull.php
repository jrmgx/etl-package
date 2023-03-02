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

    public function optionsDefinition(): TreeBuilder
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
    public function pull(PullConfig $config): mixed
    {
        $options = $config->resolveOptions($this->optionsDefinition());

        $method = $options['method'];
        unset($options['method']);

        $response = $this->httpClient->request($method, $config->getUri(), $options);
        if (200 !== $response->getStatusCode()) {
            throw new \Exception();
        }

        return $response->getContent();
    }
}
