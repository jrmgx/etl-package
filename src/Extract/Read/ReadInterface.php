<?php

namespace Jrmgx\Etl\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.extract.read')]
interface ReadInterface
{
    /**
     * @return array<mixed>
     */
    public function read(mixed $resource, ReadConfig $config): array;
}
