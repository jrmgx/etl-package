<?php

namespace Jrmgx\Etl\Extract\Pull;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Config\PullConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'file')]
class FilePull implements PullInterface
{
    public function optionsDefinition(): TreeBuilder
    {
        return new TreeBuilder('options');
    }

    public function pull(PullConfig $config): mixed
    {
        return file_get_contents(Config::resolvePath($config->getUri()));
    }
}
