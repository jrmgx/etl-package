<?php

namespace Jrmgx\Etl;

use Jrmgx\Etl\Config\Config;
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
        $extractPullType = $config->getPullConfig()->getType();
        /** @var PullInterface $extractPullService */
        $extractPullService = $this->pullServices->get($extractPullType);
        $readResource = $extractPullService->pull($config->getPullConfig());

        $extractReadFormat = $config->getReadConfig()->getFormat();
        /** @var ReadInterface $extractReadService */
        $extractReadService = $this->readServices->get($extractReadFormat);
        $read = $extractReadService->read($readResource, $config->getReadConfig());

        $filterType = $config->getFilterConfig()->getType();
        /** @var FilterInterface $filterService */
        $filterService = $this->filterServices->get($filterType);
        $filtered = $filterService->filter($read, $config->getFilterConfig());

        $mappingType = $config->getMappingConfig()->getType();
        /** @var MappingInterface $mappingService */
        $mappingService = $this->mappingServices->get($mappingType);
        $mapped = $mappingService->map($filtered, $config->getMappingConfig());

        $loadWriteFormat = $config->getWriteConfig()->getFormat();
        /** @var WriteInterface $loadWriteService */
        $loadWriteService = $this->writeServices->get($loadWriteFormat);
        $writeResource = $loadWriteService->write($mapped, $config->getWriteConfig());

        $loadPushType = $config->getPushConfig()->getType();
        /** @var PushInterface $loadPushService */
        $loadPushService = $this->pushServices->get($loadPushType);
        $loadPushService->push($writeResource, $config->getPushConfig());
    }
}
