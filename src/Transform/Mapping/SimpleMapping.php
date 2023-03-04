<?php

namespace Jrmgx\Etl\Transform\Mapping;

use Jrmgx\Etl\Common\TypeHelper;
use Jrmgx\Etl\Config\MappingConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsTaggedItem(index: 'simple')]
class SimpleMapping implements MappingInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    public static function optionsDefinition(): ?TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('flatten')
                ->info('Convert multi-dimensional values to string (json encoding)')
                ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
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
        $values = array_map(fn (string $v) => mb_substr($v, 3), array_values($mapping));
        $mapping = array_combine($keys, $values);
        $wholesKeys = TypeHelper::wholeKeys($data);
        $missingValues = array_combine($wholesKeys, array_fill(0, \count($wholesKeys), null));

        $options = $config->resolveOptions(self::optionsDefinition());

        $result = [];

        foreach ($data as $line) {
            $line = array_merge($missingValues, $line);
            $newLine = [];
            foreach ($mapping as $mappingOut => $mappingIn) {
                $encoded = json_encode($line);
                if (false === $encoded) {
                    throw new \Exception('Fail to json_encode the data');
                }
                $obj = json_decode($encoded);
                $value = $this->propertyAccessor->getValue($obj, $mappingIn);
                if ($options['flatten'] && TypeHelper::isComplex($value)) {
                    $value = json_encode($value);
                }
                $newLine[$mappingOut] = $value;
            }
            $result[] = $newLine;
        }

        return $result;
    }
}
