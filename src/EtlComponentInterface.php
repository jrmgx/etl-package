<?php

namespace Jrmgx\Etl;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

interface EtlComponentInterface
{
    public static function optionsDefinition(): ?TreeBuilder;
}
