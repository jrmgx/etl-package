<?php

namespace Jrmgx\Etl\Load\Write;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Config\WriteConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[AsTaggedItem(index: 'twig')]
class TwigWrite implements WriteInterface
{
    public function write(array $data, WriteConfig $config): mixed
    {
        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setRequired(['template']);
        });

        $templateContent = file_get_contents(Config::resolvePath($options['template']));

        $loader = new ArrayLoader(['template' => $templateContent]);
        $twig = new Environment($loader);

        return $twig->render('template', ['output' => $data]);
    }
}
