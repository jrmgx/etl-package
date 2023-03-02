<?php

namespace Jrmgx\Etl\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.extract.read')]
interface ReadInterface extends EtlComponentInterface
{
    /**
     * @return array<mixed>
     */
    public function read(mixed $resource, ReadConfig $config): array;
}
