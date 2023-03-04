<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

abstract class ConfigDefinition
{
    /**
     * @param array<mixed> $config
     */
    public function __construct(protected array $config)
    {
        $processor = new Processor();
        $this->config = $processor->process(
            $this->configTreeBuilder()->buildTree(),
            [$this->name() => $this->config]
        );
    }

    abstract protected function name(): string;

    /**
     * @return array<mixed>
     */
    public function resolveOptions(?TreeBuilder $treeBuilder): array
    {
        if (null === $treeBuilder) {
            throw new \Exception('No options have been configured');
        }

        $processor = new Processor();

        return $processor->process($treeBuilder->buildTree(), [
            'options' => $this->config['options'] ?? [],
        ]);
    }

    abstract protected function configTreeBuilder(): TreeBuilder;
}
