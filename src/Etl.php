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

    public function execute(Config $config): void
    {
        $data = $this->extract($config->getPullConfig(), $config->getReadConfig());
        $data = $this->transform($data, $config->getTransformers());
        $this->load($data, $config->getWriteConfig(), $config->getPushConfig());
    }

    public function extract(PullConfig $pullConfig, ReadConfig $readConfig): mixed
    {
        /** @var PullInterface $extractPullService */
        $extractPullService = $this->pullServices->get($pullConfig->getType());
        $readResource = $extractPullService->pull($pullConfig);

        /** @var ReadInterface $extractReadService */
        $extractReadService = $this->readServices->get($readConfig->getFormat());

        return $extractReadService->read($readResource, $readConfig);
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
                /** @var FilterInterface $filterService */
                $filterService = $this->filterServices->get($filterType);
                $data = $filterService->filter($data, $filterConfig);
            }

            /** @var MappingInterface $mappingService */
            $mappingService = $this->mappingServices->get($mappingConfig->getType());

            $data = $mappingService->map($data, $mappingConfig);
        }

        return $data;
    }

    /**
     * @param array<mixed> $data
     */
    public function load(array $data, WriteConfig $writeConfig, PushConfig $pushConfig): void
    {
        /** @var WriteInterface $loadWriteService */
        $loadWriteService = $this->writeServices->get($writeConfig->getFormat());
        $writeResource = $loadWriteService->write($data, $writeConfig);

        /** @var PushInterface $loadPushService */
        $loadPushService = $this->pushServices->get($pushConfig->getType());
        $loadPushService->push($writeResource, $pushConfig);
    }
}
