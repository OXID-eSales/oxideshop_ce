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
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleManager;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleManagerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\KernelStub;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\FilesystemContainerCache;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

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

    private BundleManagerInterface $bundleManager;

    /** @var BundleInterface[] */
    private array $bundles = [];

    private ?KernelStub $kernelStub = null;

    private bool $compiling = false;

    /**
     * The constructor's private to make class a singleton
     */
    private function __construct()
    {
        $context = new BasicContext();
        $this->cache = new FilesystemContainerCache($context, new Filesystem());
        $this->bundleManager = new BundleManager();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if ($this->symfonyContainer === null) {
            if ($this->compiling) {
                throw new \LogicException('Cannot access the container while it is being compiled.');
            }
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
        $shopId = self::getShopId();

        if ($this->cache->exists($shopId)) {
            $this->symfonyContainer = $this->cache->get($shopId);
            $this->bundles = $this->instantiateBundlesFromContainer();
        } else {
            $this->compiling = true;
            try {
                $this->getCompiledSymfonyContainer();
                $this->cache->put($this->symfonyContainer, $shopId);
            } finally {
                $this->compiling = false;
            }
        }

        $this->setKernelService();
        $this->bundleManager->boot($this->symfonyContainer, $this->bundles);
    }

    private function getCompiledSymfonyContainer(): void
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $this->symfonyContainer = $containerBuilder->getContainer();
        $this->bundles = $containerBuilder->getBundles();
        $this->kernelStub = $containerBuilder->createKernelStub();
        $this->symfonyContainer->compile(true);
    }

    private function setKernelService(): void
    {
        if ($this->kernelStub === null) {
            $this->kernelStub = $this->createKernelStubFromContext();
        }
        $this->kernelStub->setContainer($this->symfonyContainer);
        $this->symfonyContainer->set('kernel', $this->kernelStub);
    }

    private function createKernelStubFromContext(): KernelStub
    {
        $context = new BasicContext();
        $containerBuilder = new \OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder($context);
        $bundleMap = [];
        foreach ($this->bundles as $bundle) {
            $bundleMap[$bundle->getName()] = $bundle;
        }

        return new KernelStub(
            environment: $_ENV['OXID_ENV'] ?? $_SERVER['OXID_ENV'] ?? 'prod',
            debug: $this->symfonyContainer->getParameter('kernel.debug'),
            projectDir: $context->getShopRootPath(),
            cacheDir: $context->getCacheDirectory(),
            logDir: $context->getShopRootPath() . '/var/log',
            bundles: $bundleMap,
            oxidContainerBuilder: $containerBuilder,
        );
    }

    /**
     * @return BundleInterface[]
     */
    private function instantiateBundlesFromContainer(): array
    {
        $classes = $this->symfonyContainer->getParameter('oxid.bundle_classes');
        $bundles = [];
        foreach ($classes as $class) {
            $bundles[] = new $class();
        }
        return $bundles;
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
        $instance = self::getInstance();
        $instance->shutdownBundles();
        $instance->cache->invalidate(self::getShopId());
        self::$instance = null;
    }

    /**
     * Shutdown all registered bundles.
     */
    public function shutdownBundles(): void
    {
        $this->bundleManager->shutdown();
    }

    /**
     * Get the bundle manager instance.
     */
    public function getBundleManager(): BundleManagerInterface
    {
        return $this->bundleManager;
    }

    private static function getShopId(): int
    {
        $shopIdCalculator = new ShopIdCalculator(new FileCache());

        return (int) $shopIdCalculator->getShopId();
    }
}
