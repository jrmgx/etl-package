<?php

namespace Jrmgx\Etl\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsTaggedItem(index: 'json')]
class JsonRead implements ReadInterface
{
    /**
     * @param mixed $resource a string containing a valid JSON representation of some values
     */
    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!\is_string($resource)) {
            return []; // TODO error
        }

        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setDefaults([
                'associative' => true,
            ]);
        });

        return json_decode($resource, $options['associative']);
    }
}
