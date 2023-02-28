<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class MappingConfig extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => 'simple',
            'map' => null,
            'options' => [],
        ]);
        $resolver->setRequired(['type']);
        $resolver->setAllowedTypes('type', 'string');
    }

    public function getType(): string
    {
        return $this->config['type'];
    }

    /**
     * @return ?array<mixed>
     */
    public function getMap(): ?array
    {
        return $this->config['map'];
    }
}
