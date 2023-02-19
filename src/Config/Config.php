<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Config extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'extract' => function (OptionsResolver $innerResolver) {
                $innerResolver->setRequired(['pull', 'read']);
                $innerResolver->setAllowedTypes('pull', 'array');
                $innerResolver->setAllowedTypes('read', 'array');
            },
            'transform' => [],
            'load' => function (OptionsResolver $innerResolver) {
                $innerResolver->setRequired(['write', 'push']);
                $innerResolver->setAllowedTypes('write', 'array');
                $innerResolver->setAllowedTypes('push', 'array');
            },
        ]);
        $resolver->setRequired(['extract', 'load']);
        $resolver->setAllowedTypes('extract', 'array');
        $resolver->setAllowedTypes('transform', 'array');
        $resolver->setAllowedTypes('load', 'array');
    }

    public function getPullConfig(): PullConfig
    {
        return new PullConfig($this->config['extract']['pull']);
    }

    public function getReadConfig(): ReadConfig
    {
        return new ReadConfig($this->config['extract']['read']);
    }

    public function getTransformConfig(): TransformConfig
    {
        return new TransformConfig($this->config['transform']);
    }

    public function getWriteConfig(): WriteConfig
    {
        return new WriteConfig($this->config['load']['write']);
    }

    public function getPushConfig(): PushConfig
    {
        return new PushConfig($this->config['load']['push']);
    }
}
