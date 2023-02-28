<?php

namespace Jrmgx\Etl\Extract;

use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Extract\Pull\PullInterface;
use Jrmgx\Etl\Extract\Read\ReadInterface;
use Jrmgx\Etl\MemoryCommon;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'memory')]
class MemoryExtract extends MemoryCommon implements PullInterface, ReadInterface
{
    public static function memoryIdentifier(string $uri): string
    {
        return (string) preg_replace('`^memory://`miu', '', $uri);
    }

    public function pull(PullConfig $config): mixed
    {
        return self::memoryIdentifier($config->getUri());
    }

    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!\is_string($resource)) {
            return []; // TODO error
        }

        return self::$memory[$resource] ?? [];
    }
}
