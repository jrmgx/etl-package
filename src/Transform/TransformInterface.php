<?php

namespace Jrmgx\Etl\Transform;

use Jrmgx\Etl\Config\TransformConfig;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.transform.mapping')]
interface TransformInterface
{
    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function map(array $data, TransformConfig $config): array;
}
