<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TransformConfig extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => 'simple',
            'mapping' => null,
        ]);
        $resolver->setAllowedTypes('type', 'string');
        $resolver->setAllowedTypes('mapping', ['array', 'null']);
    }

    public function getType(): string
    {
        return $this->config['type'];
    }

    /**
     * @return ?array<mixed>
     */
    public function getMapping(): ?array
    {
        return $this->config['mapping'];
    }
}
