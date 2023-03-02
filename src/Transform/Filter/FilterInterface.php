<?php

namespace Jrmgx\Etl\Transform\Filter;

use Jrmgx\Etl\Config\FilterConfig;
use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.transform.filter')]
interface FilterInterface extends EtlComponentInterface
{
    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function filter(array $data, FilterConfig $config): array;
}
