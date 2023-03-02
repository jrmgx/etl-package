<?php

namespace Jrmgx\Etl\Load\Write;

use Jrmgx\Etl\Config\WriteConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'json')]
class JsonWrite implements WriteInterface
{
    public function optionsDefinition(): TreeBuilder
    {
        return new TreeBuilder('options');
    }

    public function write(array $data, WriteConfig $config): mixed
    {
        return json_encode($data);
    }
}
