<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class WriteConfig extends ConfigDefinition
{
    public function getFormat(): string
    {
        return $this->config['format'];
    }

    protected function name(): string
    {
        return 'write';
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
