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
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('associative')
                ->defaultValue(false)
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param mixed $resource a string containing a valid JSON representation of some values
     */
    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!\is_string($resource)) {
            return []; // TODO error
        }

        $options = $config->resolveOptions(self::optionsDefinition());

        return json_decode($resource, $options['associative']);
    }
}
