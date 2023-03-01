<?php

namespace Jrmgx\Etl\Transform\Filter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Jrmgx\Etl\Config\FilterConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsTaggedItem('query')]
class QueryFilter implements FilterInterface
{
    public function filter(array $data, FilterConfig $config): array
    {
        if (0 === \count($data)) {
            return [];
        }

        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setDefaults([
                'select' => '*',
                'where' => null,
                'parameters' => [],
            ]);
        });

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
            $type = $this->deduceTypeFromColumn($values);
            $table->addColumn($connection->quoteIdentifier((string) $key), $type);
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
        if (\is_array($options['select'])) {
            $select = array_map($connection->quoteIdentifier(...), $options['select']);
        } else {
            $select = $options['select'];
        }
        $queryBuilder->select($select);
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
                if (Types::BLOB === $keyType[$key]) {
                    $value = unserialize($value);
                }
            }
            $results[] = $line;
        }

        return $results;
    }

    protected static function isFloat(mixed $candidate): bool
    {
        return null === $candidate || (
            is_numeric($candidate) && (string) (float) $candidate === (string) $candidate
        );
    }

    protected static function isInt(mixed $candidate): bool
    {
        return null === $candidate || (
            is_numeric($candidate) && (string) (int) $candidate === (string) $candidate
        );
    }

    protected static function isStringable(mixed $candidate): bool
    {
        return (null === $candidate || \is_scalar($candidate)) && !\is_bool($candidate);
    }

    /**
     * Given a list of value, try to return the best SQLite column type.
     * Possible types: integer, text, blob, real
     * Then we use doctrine, so it becomes: integer, text, blob, float.
     *
     * @see https://www.sqlite.org/datatype3.html
     *
     * @param array<mixed> $column
     */
    protected static function deduceTypeFromColumn(array $column): string
    {
        if (empty($column)) {
            return Types::BLOB;
        }

        if (array_reduce($column, fn (bool $carry, mixed $item) => $carry && self::isInt($item), true)) {
            return Types::INTEGER;
        }

        if (array_reduce($column, fn (bool $carry, mixed $item) => $carry && self::isFloat($item), true)) {
            return Types::FLOAT;
        }

        if (array_reduce($column, fn (bool $carry, mixed $item) => $carry && self::isStringable($item), true)) {
            return Types::TEXT;
        }

        return Types::BLOB;
    }
}
