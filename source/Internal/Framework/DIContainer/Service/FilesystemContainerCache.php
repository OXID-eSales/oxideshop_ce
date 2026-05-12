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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FilesystemContainerCache implements ContainerCacheInterface
{
    public function __construct(private BasicContextInterface $context, private Filesystem $filesystem)
    {
    }

    public function put(ContainerBuilder $container, int $shopId): void
    {
        $dumper = new PhpDumper($container);
        $this->filesystem->dumpFile($this->context->getContainerCacheFilePath($shopId), $dumper->dump());
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    public function get(int $shopId): ContainerInterface
    {
        include_once $this->context->getContainerCacheFilePath($shopId);
        return new \ProjectServiceContainer();
    }

    public function exists(int $shopId): bool
    {
        $path = $this->context->getContainerCacheFilePath($shopId);
        return $this->filesystem->exists($path);
    }

    public function invalidate(int $shopId): void
    {
        if ($this->filesystem->exists($this->context->getContainerCacheFilePath($shopId))) {
            $this->filesystem->remove($this->context->getContainerCacheFilePath($shopId));
        }
        $kernelCacheDir = Path::join($this->context->getCacheDirectory(), 'shop_' . $shopId);
        $filesystem = $this->filesystem;
        register_shutdown_function(static function () use ($filesystem, $kernelCacheDir): void {
            if ($filesystem->exists($kernelCacheDir)) {
                $filesystem->remove($kernelCacheDir);
            }
        });
    }
}
