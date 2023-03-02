<?php

namespace Jrmgx\Etl\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'csv')]
class CsvRead implements ReadInterface
{
    public function optionsDefinition(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('trim')
                    ->defaultValue(false)
                ->end()
                ->booleanNode('header')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('separator')
                    ->defaultValue(',')
                ->end()
                ->scalarNode('enclosure')
                    ->defaultValue('"')
                ->end()
                ->scalarNode('escape')
                    ->defaultValue('\\')
                ->end()
                ->arrayNode('with_header')
                    ->ignoreExtraKeys(false)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param mixed $resource a string containing a valid CSV representation of some values
     */
    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!\is_string($resource)) {
            return []; // TODO error
        }

        $options = $config->resolveOptions($this->optionsDefinition());

        $data = [];
        $headerData = null;
        if (false === $options['header'] && \is_array($options['with_header'])) {
            $headerData = $options['with_header'];
        }
        foreach (explode("\n", $resource) as $fileLine) {
            $fileLine = trim($fileLine);
            if ('' === $fileLine) {
                continue;
            }

            $dataLine = str_getcsv($fileLine, $options['separator'], $options['enclosure'], $options['escape']);

            if ($options['trim']) {
                /** @phpstan-ignore-next-line */
                $dataLine = array_map(trim(...), $dataLine);
            }

            if (true === $options['header'] && null === $headerData) {
                /** @var array<int, string> $headerData */
                $headerData = $dataLine;
                if (\is_array($options['with_header'])) {
                    $headerData = $options['with_header'];
                }
                foreach ($headerData as $key) {
                    if (preg_match('`[^a-zA-Z0-9_-]`mu', $key)) {
                        throw new \Exception('CSV header key "' . $key . '" contains special characters (other than [a-zA-Z0-9_-]). It will be problematic down the chain. Use the `with_header` option to override it.');
                    }
                }
            } else {
                if (null !== $headerData) {
                    $dataLine = array_combine($headerData, $dataLine);
                }
                $data[] = $dataLine;
            }
        }

        return $data;
    }
}
