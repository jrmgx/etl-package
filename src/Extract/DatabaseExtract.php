<?php

namespace Jrmgx\Etl\Extract;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\Pull\PullInterface;
use Jrmgx\Etl\Extract\Read\ReadInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsTaggedItem(index: 'database')]
class DatabaseExtract implements PullInterface, ReadInterface
{
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

        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setRequired(['from']);
            $resolver->setDefaults([
                'select' => '*',
                'where' => null,
                'parameters' => [],
            ]);
        });

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
