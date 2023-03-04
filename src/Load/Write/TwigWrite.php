<?php

namespace Jrmgx\Etl\Load\Write;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Config\WriteConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[AsTaggedItem(index: 'twig')]
class TwigWrite implements WriteInterface
{
    public static function optionsDefinition(): ?TreeBuilder
    {
        $treeBuilder = new TreeBuilder('options');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('template')
                ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function write(array $data, WriteConfig $config): mixed
    {
        $options = $config->resolveOptions(self::optionsDefinition());

        $templateContent = file_get_contents(Config::resolvePath($options['template']));

        $loader = new ArrayLoader(['template' => $templateContent]);
        $twig = new Environment($loader);

        return $twig->render('template', ['output' => $data]);
    }
}
