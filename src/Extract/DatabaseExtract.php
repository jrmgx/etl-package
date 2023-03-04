<?php

namespace Jrmgx\Etl\Extract;

use Doctrine\DBAL\Connection;
use Jrmgx\Etl\Common\Database;
use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\Pull\PullInterface;
use Jrmgx\Etl\Extract\Read\ReadInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'database')]
class DatabaseExtract extends Database implements PullInterface, ReadInterface
{
    public static function optionsDefinition(): ?TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('select')
                    ->beforeNormalization()->castToArray()->end()
                    ->ignoreExtraKeys(false)
                    ->addDefaultsIfNotSet()
                ->end()
                ->scalarNode('from')->isRequired()->end()
                ->scalarNode('where')
                    ->info('Prepared SQL statements with placeholder: i.e. "size > :size"')
                    ->defaultNull()
                ->end()
                ->arrayNode('parameters')
                    ->info('Associate placeholders from the "where" part with the value you want: i.e. "{ size: 10 }"')
                    ->ignoreExtraKeys(false)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function pull(PullConfig $config): mixed
    {
        return $this->getConnection($config->getUri());
    }

    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!$resource instanceof Connection) {
            throw new \Exception($this::class . ' can only read Connection');
        }

        $options = $config->resolveOptions(self::optionsDefinition());

        $queryBuilder = $resource->createQueryBuilder();
        $select = array_map($resource->quoteIdentifier(...), $options['select']);
        $queryBuilder->select(\count($select) > 0 ? $select : '*');
        $queryBuilder->from($options['from']);
        if (null !== $options['where']) {
            $queryBuilder->andWhere($options['where']);
            $queryBuilder->setParameters($options['parameters'] ?? []);
        }

        return $queryBuilder->fetchAllAssociative();
    }
}
