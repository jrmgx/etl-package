<?php

namespace Jrmgx\Etl\Transform\Filter;

use Jrmgx\Etl\Config\FilterConfig;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('none')]
class NoneFilter implements FilterInterface
{
    public function filter(array $data, FilterConfig $config): array
    {
        return $data;
    }
}
