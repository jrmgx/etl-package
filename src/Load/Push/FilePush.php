<?php

namespace Jrmgx\Etl\Load\Push;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Config\PushConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'file')]
class FilePush implements PushInterface
{
    public static function optionsDefinition(): ?TreeBuilder
    {
        return null;
    }

    public function push(mixed $resource, PushConfig $config): mixed
    {
        file_put_contents(Config::resolvePath($config->getUri()), $resource);

        return null;
    }
}
