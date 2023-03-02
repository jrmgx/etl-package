<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class MappingConfig extends ConfigDefinition
{
    public function getType(): string
    {
        return $this->config['type'];
    }

    /**
     * @return ?array<mixed>
     */
    public function getMap(): ?array
    {
        return $this->config['map'] ?? null;
    }

    protected function name(): string
    {
        return 'mapping';
    }

    protected function configTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->name());
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('type')->end()
                ->arrayNode('map')->ignoreExtraKeys(false)->end()
                ->arrayNode('options')->ignoreExtraKeys(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
