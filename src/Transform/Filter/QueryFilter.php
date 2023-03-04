<?php

namespace Jrmgx\Etl\Transform\Filter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Jrmgx\Etl\Common\Database;
use Jrmgx\Etl\Config\FilterConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('query')]
class QueryFilter implements FilterInterface
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

    public function filter(array $data, FilterConfig $config): array
    {
        if (0 === \count($data)) {
            return [];
        }

        $options = $config->resolveOptions(self::optionsDefinition());

        // Preparatory work

        $keyValues = [];
        foreach ($data as $lines) {
            foreach ($lines as $key => $value) {
                $keyValues[$key][] = $value;
            }
        }

        // Build the schema

        $connection = DriverManager::getConnection(['dbname' => ':memory:', 'driver' => 'pdo_sqlite']);
        $platform = new SqlitePlatform();
        $schema = new Schema();

        $keyType = [];
        $table = $schema->createTable('filterTable');
        foreach ($keyValues as $key => $values) {
            $type = Database::deduceTypeFromColumn($values);
            $table->addColumn($connection->quoteIdentifier((string) $key), $type, [
                'notnull' => false,
                'default' => null,
            ]);
            $keyType[$key] = $type;
        }

        $queries = $schema->toSql($platform);
        foreach ($queries as $query) {
            $connection->executeQuery($query);
        }

        // Import data in + serialize

        $queryBuilder = $connection->createQueryBuilder();
        foreach ($data as $lines) {
            $values = [];
            $parameters = [];
            foreach ($lines as $key => $value) {
                $values[$connection->quoteIdentifier($key)] = '?';
                if (Types::BLOB === $keyType[$key]) {
                    $value = serialize($value);
                }
                $parameters[] = $value;
            }
            $queryBuilder
                ->insert('filterTable')
                ->values($values)
                ->setParameters($parameters)
                ->executeQuery()
            ;
        }

        // Apply the query

        $queryBuilder = $connection->createQueryBuilder();
        $select = array_map($connection->quoteIdentifier(...), $options['select']);
        $queryBuilder->select(\count($select) > 0 ? $select : '*');
        $queryBuilder->from('filterTable');
        if (null !== $options['where']) {
            $queryBuilder->andWhere($options['where']);
            $queryBuilder->setParameters($options['parameters']);
        }

        // Get result + deserialize

        $fetch = $queryBuilder->fetchAllAssociative();
        $results = [];
        foreach ($fetch as $line) {
            foreach ($line as $key => &$value) {
                if (null !== $value && Types::BLOB === $keyType[$key]) {
                    $value = unserialize($value);
                }
            }
            $results[] = $line;
        }

        return $results;
    }
}
