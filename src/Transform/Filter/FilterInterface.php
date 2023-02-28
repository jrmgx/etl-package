<?php

namespace Jrmgx\Etl\Transform\Filter;

use Jrmgx\Etl\Config\FilterConfig;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.transform.filter')]
interface FilterInterface
{
    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function filter(array $data, FilterConfig $config): array;
}
