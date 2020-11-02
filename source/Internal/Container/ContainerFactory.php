<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\FilesystemContainerCache;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @var ContainerInterface
     */
    private $symfonyContainer = null;

    /**
     * @var ContainerCacheInterface
     */
    private $cache;

    /**
     * ContainerFactory constructor.
     *
     * Make the constructor private
     */
    private function __construct()
    {
        $this->cache = new FilesystemContainerCache(new BasicContext());
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if (null === $this->symfonyContainer) {
            $this->initializeContainer();
        }

        return $this->symfonyContainer;
    }

    /**
     * Loads container from cache if available, otherwise
     * create the container from scratch.
     */
    private function initializeContainer(): void
    {
        if ($this->cache->exists()) {
            $this->symfonyContainer = $this->cache->get();
        } else {
            $this->getCompiledSymfonyContainer();
            $this->cache->put($this->symfonyContainer);
        }
    }

    private function getCompiledSymfonyContainer(): void
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $this->symfonyContainer = $containerBuilder->getContainer();
        $this->symfonyContainer->compile();
    }

    /**
     * @return ContainerFactory
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Forces reload of the ContainerFactory on next request.
     */
    public static function resetContainer(): void
    {
        self::getInstance()->cache->invalidate();
        self::$instance = null;
    }
}
