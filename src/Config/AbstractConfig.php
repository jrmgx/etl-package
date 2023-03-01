<?php

namespace Jrmgx\Etl\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfig
{
    protected static ?string $rootPath = null;

    protected OptionsResolver $optionsResolver;

    /**
     * @param array<mixed> $config
     * @param ?string      $rootPath directory from where relative path will be resolved in your configuration
     */
    public function __construct(protected array $config, ?string $rootPath = null)
    {
        $this->setRootPath($rootPath);

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptionsResolver($this->optionsResolver);

        $this->config = $this->optionsResolver->resolve($this->config);
    }

    public function setRootPath(?string $path): void
    {
        if (null === $path) {
            return;
        }
        self::$rootPath = $path;
    }

    public static function resolvePath(string $path): string
    {
        if (null === self::$rootPath || !($projectRoot = realpath(self::$rootPath))) {
            $projectRoot = realpath(__DIR__ . '/../../../');
        }

        return (string) preg_replace('`^\./`', $projectRoot . '/', $path);
    }

    abstract protected function configureOptionsResolver(OptionsResolver $resolver): void;

    /**
     * @return array<mixed>
     */
    public function resolveCustomOptions(callable $resolver): array
    {
        $this->optionsResolver->setRequired('options');
        $this->optionsResolver->setDefaults(['options' => $resolver]);

        return $this->optionsResolver->resolve($this->config)['options'];
    }
}
