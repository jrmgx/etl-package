<?php

namespace Jrmgx\Etl\Load\Push;

use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.load.push')]
interface PushInterface extends EtlComponentInterface
{
    public function push(mixed $resource, PushConfig $config): void;
}
