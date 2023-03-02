<?php

namespace Jrmgx\Etl\Transform\Mapping;

use Jrmgx\Etl\Config\MappingConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[AsTaggedItem(index: 'expressive')]
class ExpressiveMapping implements MappingInterface
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function optionsDefinition(): TreeBuilder
    {
        return new TreeBuilder('options');
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function map(array $data, MappingConfig $config): array
    {
        $mapping = $config->getMap();
        if (null === $mapping) {
            return $data; // identity
        }

        $keys = array_map(fn (string $k) => mb_substr($k, 4), array_keys($mapping));
        $mapping = array_combine($keys, array_values($mapping));

        $result = [];

        foreach ($data as $line) {
            $newLine = [];
            foreach ($mapping as $mappingOut => $mappingIn) {
                $encoded = json_encode($line);
                if (false === $encoded) {
                    throw new \Exception();
                }
                $obj = json_decode($encoded);
                $newLine[$mappingOut] = $this->expressionLanguage->evaluate($mappingIn, ['in' => $obj]);
            }
            $result[] = $newLine;
        }

        return $result;
    }
}
