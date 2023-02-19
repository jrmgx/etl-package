<?php

namespace Jrmgx\Etl\Extract\Read;

use Jrmgx\Etl\Config\ReadConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsTaggedItem(index: 'csv')]
class CsvRead implements ReadInterface
{
    /**
     * @param mixed $resource a string containing a valid CSV representation of some values
     */
    public function read(mixed $resource, ReadConfig $config): array
    {
        if (!\is_string($resource)) {
            return []; // TODO error
        }

        $options = $config->resolveCustomOptions(function (OptionsResolver $resolver) {
            $resolver->setDefaults([
                'trim' => false,
                'header' => true,
                'separator' => ',',
                'enclosure' => '"',
                'escape' => '\\',
            ]);
        });

        $data = [];
        $headerData = null;
        foreach (explode("\n", $resource) as $line) {
            $line = trim($line);
            if ('' === $line) {
                continue;
            }

            $d = str_getcsv($line, $options['separator'], $options['enclosure'], $options['escape']);

            if ($options['trim']) {
                /** @phpstan-ignore-next-line */
                $d = array_map(trim(...), $d);
            }

            if (true === $options['header'] && null === $headerData) {
                /** @var array<int, string> $headerData */
                $headerData = $d;
            } else {
                if (null !== $headerData) {
                    $d = array_combine($headerData, $d);
                }
                $data[] = $d;
            }
        }

        return $data;
    }
}
