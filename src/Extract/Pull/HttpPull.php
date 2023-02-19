<?php

namespace Jrmgx\Etl\Extract\Pull;

use Jrmgx\Etl\Config\PullConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTaggedItem(index: 'http')]
class HttpPull implements PullInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    // https://symfony.com/doc/current/http_client.html
    public function pull(PullConfig $config): mixed
    {
        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setDefaults(array_merge(HttpClientInterface::OPTIONS_DEFAULTS, [
                'method' => 'GET',
            ]));
        });

        $method = $options['method'];
        unset($options['method']);

        $response = $this->httpClient->request($method, $config->getUri(), $options);
        if (200 !== $response->getStatusCode()) {
            throw new \Exception();
        }

        return $response->getContent();
    }
}
