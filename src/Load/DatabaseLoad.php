<?php

namespace Jrmgx\Etl\Load;

use Jrmgx\Etl\Common\Database;
use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Load\Push\PushInterface;
use Jrmgx\Etl\Load\Write\WriteInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'database')]
class DatabaseLoad extends Database implements WriteInterface, PushInterface
{
    public static function optionsDefinition(): ?TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('into')->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function write(array $data, WriteConfig $config): mixed
    {
        return $data;
    }

    public function push(mixed $resource, PushConfig $config): mixed
    {
        if (!\is_array($resource)) {
            throw new \Exception($this::class . ' can only push array');
        }

        $options = $config->resolveOptions(self::optionsDefinition());

        $connection = $this->getConnection($config->getUri());
        $connection->beginTransaction();
        try {
            $queryBuilder = $connection->createQueryBuilder();
            foreach ($resource as $d) {
                $values = array_combine(
                    /* @phpstan-ignore-next-line */
                    array_map($connection->quoteIdentifier(...), array_keys($d)),
                    array_fill(0, \count($d), '?')
                );
                $parameters = array_values($d);
                $queryBuilder
                    ->insert($options['into'])
                    ->values($values)
                    ->setParameters($parameters)
                    ->executeQuery()
                ;
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return null;
    }
}
