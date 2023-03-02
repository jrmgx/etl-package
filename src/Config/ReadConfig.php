<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ReadConfig extends ConfigDefinition
{
    public function getFormat(): string
    {
        return $this->config['format'];
    }

    protected function name(): string
    {
        return 'read';
    }

    protected function configTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->name());
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('format')->end()
                ->arrayNode('options')->ignoreExtraKeys(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
