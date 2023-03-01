<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Config extends AbstractConfig
{
    protected function configureOptionsResolver(OptionsResolver $resolver): void
    {
        $transformOptionsResolver = function (OptionsResolver $innerResolver) {
            $innerResolver->setDefaults(['filter' => []]);
            $innerResolver->setRequired(['mapping']);
            $innerResolver->setAllowedTypes('filter', 'array');
            $innerResolver->setAllowedTypes('mapping', 'array');
        };

        if (isset($this->config['transformers'])) {
            $resolver->setDefault('transformers', function (OptionsResolver $innerResolver) use ($transformOptionsResolver) {
                $innerResolver->setPrototype(true);
                $transformOptionsResolver($innerResolver);
            });
        } else {
            $resolver->setDefault('transform', $transformOptionsResolver);
        }

        $resolver->setDefaults([
            'extract' => function (OptionsResolver $innerResolver) {
                $innerResolver->setRequired(['pull', 'read']);
                $innerResolver->setAllowedTypes('pull', 'array');
                $innerResolver->setAllowedTypes('read', 'array');
            },
            'load' => function (OptionsResolver $innerResolver) {
                $innerResolver->setRequired(['write', 'push']);
                $innerResolver->setAllowedTypes('write', 'array');
                $innerResolver->setAllowedTypes('push', 'array');
            },
        ]);
        $resolver->setRequired(['extract', 'load']);
        $resolver->setAllowedTypes('extract', 'array');
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

    /**
     * @return \Generator<array{FilterConfig, MappingConfig}>
     */
    public function getTransformers(): \Generator
    {
        if (isset($this->config['transformers']) && \count($this->config['transformers']) > 0) {
            foreach ($this->config['transformers'] as $transformer) {
                yield [
                    new FilterConfig($transformer['filter'] ?? []),
                    new MappingConfig($transformer['mapping']),
                ];
            }
        } else {
            yield [
                new FilterConfig($this->config['transform']['filter'] ?? []),
                new MappingConfig($this->config['transform']['mapping']),
            ];
        }
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
