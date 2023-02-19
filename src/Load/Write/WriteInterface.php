<?php

namespace Jrmgx\Etl\Load\Write;

use Jrmgx\Etl\Config\WriteConfig;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.load.write')]
interface WriteInterface
{
    /**
     * @param array<mixed> $data
     */
    public function write(array $data, WriteConfig $config): mixed;
}
