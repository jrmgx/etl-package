<?php

namespace Jrmgx\Etl\Common;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\DBAL\Types\Types;
use Jrmgx\Etl\Config\Config;

class Database
{
    protected function getConnection(string $uri): Connection
    {
        $dsnParser = new DsnParser([
            'mysql' => 'pdo_mysql',
            'sqlite' => 'pdo_sqlite',
            'pgsql' => 'pdo_pgsql',
            'oci' => 'pdo_oci',
            'sqlsrv' => 'pdo_sqlsrv',
        ]);
        $prefix = '`^sqlite://\./`';
        if (preg_match($prefix, $uri)) {
            /** @var string $fileUri */
            $fileUri = preg_replace($prefix, './', $uri);
            $fileUri = Config::resolvePath($fileUri);
            $uri = 'sqlite:///' . $fileUri;
        }
        $connectionParams = $dsnParser->parse($uri);

        return DriverManager::getConnection($connectionParams);
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
    public static function deduceTypeFromColumn(array $column): string
    {
        if (empty($column)) {
            return Types::BLOB;
        }

        if (array_reduce($column, fn (bool $carry, mixed $item) => $carry && TypeHelper::isInt($item), true)) {
            return Types::INTEGER;
        }

        if (array_reduce($column, fn (bool $carry, mixed $item) => $carry && TypeHelper::isFloat($item), true)) {
            return Types::FLOAT;
        }

        if (array_reduce($column, fn (bool $carry, mixed $item) => $carry && TypeHelper::isStringable($item), true)) {
            return Types::TEXT;
        }

        return Types::BLOB;
    }
}
