<?php

namespace Jrmgx\Etl\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'json')]
class JsonRead implements ReadInterface
{
    public static function optionsDefinition(): ?TreeBuilder
    {
        return null;
    }

    /**
     * @param mixed $resource a string containing a valid JSON representation of some values
     */
    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!\is_string($resource)) {
            throw new \Exception($this::class . ' can only read string');
        }

        return (array) json_decode($resource, associative: true);
    }
}
