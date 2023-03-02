<?php

namespace Jrmgx\Etl\Load\Push;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Config\PushConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'file')]
class FilePush implements PushInterface
{
    public function optionsDefinition(): TreeBuilder
    {
        return new TreeBuilder('options');
    }

    public function push(mixed $resource, PushConfig $config): void
    {
        file_put_contents(Config::resolvePath($config->getUri()), $resource);
    }
}
