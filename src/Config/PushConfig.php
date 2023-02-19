<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PushConfig extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['options' => []]);
        $resolver->setRequired(['type', 'uri']);
        $resolver->setAllowedTypes('type', 'string');
        $resolver->setAllowedTypes('uri', 'string');
    }

    public function getType(): string
    {
        return $this->config['type'];
    }

    public function getUri(): string
    {
        return $this->config['uri'];
    }
}
