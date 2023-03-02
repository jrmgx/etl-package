<?php

namespace Jrmgx\Etl\Extract\Pull;

use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.extract.pull')]
interface PullInterface extends EtlComponentInterface
{
    public function pull(PullConfig $config): mixed;
}
