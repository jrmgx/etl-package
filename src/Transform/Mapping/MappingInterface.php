<?php

namespace Jrmgx\Etl\Transform\Mapping;

use Jrmgx\Etl\Config\MappingConfig;
use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.transform.mapping')]
interface MappingInterface extends EtlComponentInterface
{
    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function map(array $data, MappingConfig $config): array;
}
