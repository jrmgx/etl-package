<?php

namespace Jrmgx\Etl;

use Jrmgx\Etl\Config\Config;
use Jrmgx\Etl\Config\FilterConfig;
use Jrmgx\Etl\Config\MappingConfig;
use Jrmgx\Etl\Config\PullConfig;
use Jrmgx\Etl\Config\PushConfig;
use Jrmgx\Etl\Config\ReadConfig;
use Jrmgx\Etl\Config\WriteConfig;
use Jrmgx\Etl\Extract\Pull\PullInterface;
use Jrmgx\Etl\Extract\Read\ReadInterface;
use Jrmgx\Etl\Load\Push\PushInterface;
use Jrmgx\Etl\Load\Write\WriteInterface;
use Jrmgx\Etl\Transform\Filter\FilterInterface;
use Jrmgx\Etl\Transform\Mapping\MappingInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;

class Etl
{
    public function __construct(
        #[TaggedLocator(tag: 'etl.extract.pull')]
        private readonly ContainerInterface $pullServices,
        #[TaggedLocator(tag: 'etl.extract.read')]
        private readonly ContainerInterface $readServices,
        #[TaggedLocator(tag: 'etl.transform.filter')]
        private readonly ContainerInterface $filterServices,
        #[TaggedLocator(tag: 'etl.transform.mapping')]
        private readonly ContainerInterface $mappingServices,
        #[TaggedLocator(tag: 'etl.load.write')]
        private readonly ContainerInterface $writeServices,
        #[TaggedLocator(tag: 'etl.load.push')]
        private readonly ContainerInterface $pushServices,
    ) {
    }

    public function execute(Config $config): mixed
    {
        $data = $this->extract($config->getPullConfig(), $config->getReadConfig());
        $data = $this->transform($data, $config->getTransformers());

        return $this->load($data, $config->getWriteConfig(), $config->getPushConfig());
    }

    public function extract(PullConfig $pullConfig, ReadConfig $readConfig): mixed
    {
        $pullResource = $this->pull($pullConfig);

        return $this->read($pullResource, $readConfig);
    }

    public function pull(PullConfig $pullConfig): mixed
    {
        /** @var PullInterface $extractPullService */
        $extractPullService = $this->pullServices->get($pullConfig->getType());

        return $extractPullService->pull($pullConfig);
    }

    /**
     * @return array<mixed>
     */
    public function read(mixed $resource, ReadConfig $readConfig): array
    {
        /** @var ReadInterface $extractReadService */
        $extractReadService = $this->readServices->get($readConfig->getFormat());

        return $extractReadService->read($resource, $readConfig);
    }

    /**
     * @param array<mixed> $data
     * @param \Generator<array{FilterConfig, MappingConfig}> $configs
     *
     * @return array<mixed>
     */
    public function transform(array $data, \Generator $configs): array
    {
        foreach ($configs as $transformer) {
            [$filterConfig, $mappingConfig] = $transformer;

            $filterType = $filterConfig->getType();
            if ('none' !== $filterType) {
                $data = $this->filter($data, $filterConfig);
            }

            $data = $this->map($data, $mappingConfig);
        }

        return $data;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function filter(array $data, FilterConfig $filterConfig): array
    {
        /** @var FilterInterface $filterService */
        $filterService = $this->filterServices->get($filterConfig->getType());

        return $filterService->filter($data, $filterConfig);
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function map(array $data, MappingConfig $mappingConfig): array
    {
        /** @var MappingInterface $mappingService */
        $mappingService = $this->mappingServices->get($mappingConfig->getType());

        return $mappingService->map($data, $mappingConfig);
    }

    /**
     * @param array<mixed> $data
     */
    public function load(array $data, WriteConfig $writeConfig, PushConfig $pushConfig): mixed
    {
        $writeResource = $this->write($data, $writeConfig);

        return $this->push($writeResource, $pushConfig);
    }

    /**
     * @param array<mixed> $data
     */
    public function write(array $data, WriteConfig $writeConfig): mixed
    {
        /** @var WriteInterface $loadWriteService */
        $loadWriteService = $this->writeServices->get($writeConfig->getFormat());

        return $loadWriteService->write($data, $writeConfig);
    }

    public function push(mixed $resource, PushConfig $pushConfig): mixed
    {
        /** @var PushInterface $loadPushService */
        $loadPushService = $this->pushServices->get($pushConfig->getType());

        return $loadPushService->push($resource, $pushConfig);
    }
}
