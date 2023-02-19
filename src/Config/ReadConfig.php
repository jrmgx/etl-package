<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadConfig extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['options' => []]);
        $resolver->setRequired(['format']);
        $resolver->setAllowedTypes('format', 'string');
    }

    public function getFormat(): string
    {
        return $this->config['format'];
    }
}
