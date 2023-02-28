<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterConfig extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => 'none',
            'options' => [],
        ]);
        $resolver->setRequired(['type']);
        $resolver->setAllowedTypes('type', 'string');
    }

    public function getType(): string
    {
        return $this->config['type'];
    }
}
