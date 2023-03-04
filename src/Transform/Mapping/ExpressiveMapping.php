<?php

namespace Jrmgx\Etl\Transform\Mapping;

use Jrmgx\Etl\Common\TypeHelper;
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

    public static function optionsDefinition(): ?TreeBuilder
    {
        return null;
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
        $wholesKeys = TypeHelper::wholeKeys($data);
        $missingValues = array_combine($wholesKeys, array_fill(0, \count($wholesKeys), null));

        $result = [];

        foreach ($data as $line) {
            $line = array_merge($missingValues, $line);
            $newLine = [];
            foreach ($mapping as $mappingOut => $mappingIn) {
                $encoded = json_encode($line);
                if (false === $encoded) {
                    throw new \Exception('Failed to json_encode the data');
                }
                $obj = json_decode($encoded);
                $newLine[$mappingOut] = $this->expressionLanguage->evaluate($mappingIn, ['in' => $obj]);
            }
            $result[] = $newLine;
        }

        return $result;
    }
}
