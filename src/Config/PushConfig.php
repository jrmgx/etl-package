<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class PushConfig extends ConfigDefinition
{
    public function getType(): string
    {
        return $this->config['type'];
    }

    public function getUri(): string
    {
        return $this->config['uri'];
    }

    protected function name(): string
    {
        return 'push';
    }

    protected function configTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->name());
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('type')->end()
                ->scalarNode('uri')->end()
                ->arrayNode('options')->ignoreExtraKeys(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
