<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\FilesystemContainerCache;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ContainerFactory
{
    private static $instance;
    private ContainerInterface $symfonyContainer;
    private ContainerCacheInterface $cache;
    private static ?int $shopId;

    /**
     * The constructor's private to make class a singleton
     */
    private function __construct()
    {
        $this->cache = new FilesystemContainerCache(new BasicContext(), new Filesystem());
    }

    public function getContainer(): ContainerInterface
    {
        if (!isset($this->symfonyContainer)) {
            $this->initializeContainer();
        }

        return $this->symfonyContainer;
    }

    private function initializeContainer(): void
    {
        if ($this->cache->exists(self::getShopId())) {
            $this->symfonyContainer = $this->cache->get(self::getShopId());
        } else {
            $this->compileSymfonyContainer();
            $this->cache->put($this->symfonyContainer, self::getShopId());
        }
    }

    private function compileSymfonyContainer(): void
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $this->symfonyContainer = $containerBuilder->getContainer();
        $this->symfonyContainer->compile(true);
    }

    public static function getInstance(): ContainerFactory
    {
        if (self::$instance === null) {
            self::$instance = new ContainerFactory();
        }
        return self::$instance;
    }

    public static function resetContainer(): void
    {
        self::$shopId = null;
        self::getInstance()->cache->invalidate(self::getShopId());
        self::$instance = null;
    }

    private static function getShopId(): int
    {
        if (!isset(self::$shopId)) {
            self::$shopId = (int)(new ShopIdCalculator(new FileCache()))->getShopId();
        }
        return self::$shopId;
    }
}
