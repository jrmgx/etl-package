<?php

namespace Jrmgx\Etl\Load\Write;

use Jrmgx\Etl\Config\WriteConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'json')]
class JsonWrite implements WriteInterface
{
    public static function optionsDefinition(): ?TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('flags')
                ->info('JSON encode flags for advanced usages, see: https://www.php.net/manual/en/function.json-encode.php')
                ->defaultValue(0)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function write(array $data, WriteConfig $config): mixed
    {
        $options = $config->resolveOptions(self::optionsDefinition());

        return json_encode($data, $options['flags']);
    }
}
