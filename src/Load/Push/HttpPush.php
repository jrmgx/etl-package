<?php

namespace Jrmgx\Etl\Load\Push;

use Jrmgx\Etl\Config\PushConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTaggedItem(index: 'http')]
class HttpPush implements PushInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    // https://symfony.com/doc/current/http_client.html
    public function push(mixed $resource, PushConfig $config): void
    {
        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setDefaults(array_merge(HttpClientInterface::OPTIONS_DEFAULTS, [
                'method' => 'POST',
            ]));
        });

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
