<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class FilesystemContainerCache implements ContainerCacheInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
    }

    public function put(ContainerBuilder $container): void
    {
        $dumper = new PhpDumper($container);
        file_put_contents($this->context->getContainerCacheFilePath(), $dumper->dump());
    }

    public function get(): ContainerInterface
    {
        include_once $this->context->getContainerCacheFilePath();
        return new \ProjectServiceContainer();
    }

    public function exists(): bool
    {
        return file_exists($this->context->getContainerCacheFilePath());
    }

    public function invalidate(): void
    {
        if (file_exists($this->context->getContainerCacheFilePath())) {
            unlink($this->context->getContainerCacheFilePath());
        }
    }
}
