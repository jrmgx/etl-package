<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Config extends ConfigDefinition
{
    protected static ?string $rootPath = null;

    /**
     * @param array<mixed> $config
     * @param string       $rootPath directory from where relative path will be resolved in your configuration
     */
    public function __construct(protected array $config, string $rootPath)
    {
        parent::__construct($this->config);

        self::$rootPath = $rootPath;
    }

    public static function resolvePath(string $path): string
    {
        if (null === self::$rootPath || !($projectRoot = realpath(self::$rootPath))) {
            $projectRoot = realpath(__DIR__ . '/../../../');
        }

        return (string) preg_replace('`^\./`', $projectRoot . '/', $path);
    }

    public function getPullConfig(): PullConfig
    {
        return new PullConfig($this->config['extract']['pull']);
    }

    public function getReadConfig(): ReadConfig
    {
        return new ReadConfig($this->config['extract']['read']);
    }

    /**
     * @return \Generator<array{FilterConfig, MappingConfig}>
     */
    public function getTransformers(): \Generator
    {
        if (isset($this->config['transformers']) && \count($this->config['transformers']) > 0) {
            foreach ($this->config['transformers'] as $transformer) {
                yield [
                    new FilterConfig($transformer['filter'] ?? []),
                    new MappingConfig($transformer['mapping']),
                ];
            }
        } else {
            yield [
                new FilterConfig($this->config['transform']['filter'] ?? []),
                new MappingConfig($this->config['transform']['mapping']),
            ];
        }
    }

    public function getWriteConfig(): WriteConfig
    {
        return new WriteConfig($this->config['load']['write']);
    }

    public function getPushConfig(): PushConfig
    {
        return new PushConfig($this->config['load']['push']);
    }

    protected function name(): string
    {
        return 'etl';
    }

    protected function configTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->name());
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('extract')
                    ->children()
                        ->arrayNode('pull')->isRequired()->ignoreExtraKeys(false)->end()
                        ->arrayNode('read')->isRequired()->ignoreExtraKeys(false)->end()
                    ->end()
                ->end()
                ->arrayNode('transformers')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('filter')->ignoreExtraKeys(false)->end()
                            ->arrayNode('mapping')->isRequired()->ignoreExtraKeys(false)->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('transform')
                    ->children()
                        ->arrayNode('filter')->ignoreExtraKeys(false)->end()
                        ->arrayNode('mapping')->isRequired()->ignoreExtraKeys(false)->end()
                    ->end()
                ->end()
                ->arrayNode('load')
                    ->children()
                        ->arrayNode('write')->isRequired()->ignoreExtraKeys(false)->end()
                        ->arrayNode('push')->isRequired()->ignoreExtraKeys(false)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
