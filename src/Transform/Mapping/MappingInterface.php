<?php

namespace Jrmgx\Etl\Transform\Mapping;

use Jrmgx\Etl\Config\MappingConfig;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.transform.mapping')]
interface MappingInterface
{
    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function map(array $data, MappingConfig $config): array;
}
