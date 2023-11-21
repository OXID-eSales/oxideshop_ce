<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\FilesystemContainerCache;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ContainerFactory
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var ContainerInterface
     */
    private $symfonyContainer;

    /**
     * @var ContainerCacheInterface
     */
    private $cache;

    /**
     * The constructor's private to make class a singleton
     */
    private function __construct()
    {
        $this->cache = new FilesystemContainerCache(new BasicContext(), new Filesystem());
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if ($this->symfonyContainer === null) {
            $this->initializeContainer();
        }

        return $this->symfonyContainer;
    }

    /**
     * Loads container from cache if available, otherwise
     * create the container from scratch.
     */
    private function initializeContainer()
    {
        $shopId = self::getShopId();

        if ($this->cache->exists($shopId)) {
            $this->symfonyContainer = $this->cache->get($shopId);
        } else {
            $this->getCompiledSymfonyContainer();
            $this->cache->put($this->symfonyContainer, $shopId);
        }
    }

    private function getCompiledSymfonyContainer()
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $this->symfonyContainer = $containerBuilder->getContainer();
        $this->symfonyContainer->compile(true);
    }

    /**
     * @return ContainerFactory
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ContainerFactory();
        }
        return self::$instance;
    }

    /**
     * Forces reload of the ContainerFactory on next request.
     */
    public static function resetContainer()
    {
        self::getInstance()->cache->invalidate(self::getShopId());
        self::$instance = null;
    }

    private static function getShopId(): int
    {
        $shopIdCalculator = new ShopIdCalculator(new FileCache());

        return (int) $shopIdCalculator->getShopId();
    }
}
