<?php

namespace Jrmgx\Etl\Load;

use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Extract\MemoryExtract;
use Jrmgx\Etl\Load\Push\PushInterface;
use Jrmgx\Etl\Load\Write\WriteInterface;
use Jrmgx\Etl\MemoryCommon;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'memory')]
class MemoryLoad extends MemoryCommon implements WriteInterface, PushInterface
{
    public function write(array $data, WriteConfig $config): mixed
    {
        return $data;
    }

    public function push(mixed $resource, PushConfig $config): void
    {
        $identifier = MemoryExtract::memoryIdentifier($config->getUri());

        self::$memory[$identifier] = $resource;
    }
}
