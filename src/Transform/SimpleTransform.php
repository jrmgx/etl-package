<?php

namespace Jrmgx\Etl\Transform;

use Jrmgx\Etl\Config\TransformConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsTaggedItem(index: 'simple')]
class SimpleTransform implements TransformInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function map(array $data, TransformConfig $config): array
    {
        $mapping = $config->getMapping();
        if (null === $mapping) {
            return $data; // identity
        }

        $keys = array_map(fn (string $k) => mb_substr($k, 4), array_keys($mapping));
        $values = array_map(fn (string $v) => mb_substr($v, 3), array_values($mapping));
        $mapping = array_combine($keys, $values);

        $result = [];

        foreach ($data as $line) {
            $newLine = [];
            foreach ($mapping as $mappingOut => $mappingIn) {
                $encoded = json_encode($line);
                if (false === $encoded) {
                    throw new \Exception();
                }
                $obj = json_decode($encoded);
                $newLine[$mappingOut] = $this->propertyAccessor->getValue($obj, $mappingIn);
            }
            $result[] = $newLine;
        }

        return $result;
    }
}
