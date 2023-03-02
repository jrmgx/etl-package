<?php

namespace Jrmgx\Etl;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

interface EtlComponentInterface
{
    public function optionsDefinition(): TreeBuilder;
}
