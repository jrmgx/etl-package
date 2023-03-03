<?php

namespace Jrmgx\Etl\Load;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Load\Push\PushInterface;
use Jrmgx\Etl\Load\Write\WriteInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'database')]
class DatabaseLoad implements WriteInterface, PushInterface
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
        $options = $config->resolveOptions(self::optionsDefinition());

        return [$options, $data];
    }

    public function push(mixed $resource, PushConfig $config): void
    {
        if (!\is_array($resource)) {
            throw new \Exception();
        }

        [$options, $data] = $resource;

        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser->parse($config->getUri());
        $connection = DriverManager::getConnection($connectionParams);
        $connection->beginTransaction();
        try {
            $queryBuilder = $connection->createQueryBuilder();
            foreach ($data as $d) {
                $questionMarks = array_fill(0, \count($d), '?');
                $values = array_combine(array_keys($d), $questionMarks);
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
    }
}
