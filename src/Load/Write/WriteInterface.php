<?php

namespace Jrmgx\Etl\Load\Write;

use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('etl.load.write')]
interface WriteInterface extends EtlComponentInterface
{
    /**
     * @param array<mixed> $data
     */
    public function write(array $data, WriteConfig $config): mixed;
}
