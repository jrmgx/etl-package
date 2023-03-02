<?php

namespace Jrmgx\Etl\Extract;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\Pull\PullInterface;
use Jrmgx\Etl\Extract\Read\ReadInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'database')]
class DatabaseExtract implements PullInterface, ReadInterface
{
    public function optionsDefinition(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('select')
                    ->ignoreExtraKeys(false)
                ->end()
                ->scalarNode('from')->end()
                ->scalarNode('where')->end()
                ->arrayNode('parameters')
                    ->ignoreExtraKeys(false)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function pull(PullConfig $config): mixed
    {
        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser->parse($config->getUri());

        return DriverManager::getConnection($connectionParams);
    }

    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!$resource instanceof Connection) {
            throw new \Exception();
        }

        $options = $config->resolveOptions($this->optionsDefinition());

        $queryBuilder = $resource->createQueryBuilder();
        $queryBuilder->select($options['select']);
        $queryBuilder->from($options['from']);
        if (null !== $options['where']) {
            $queryBuilder->andWhere($options['where']);
            $queryBuilder->setParameters($options['parameters']);
        }

        return $queryBuilder->fetchAllAssociative();
    }
}
