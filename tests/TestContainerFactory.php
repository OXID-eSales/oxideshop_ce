<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Container\ContainerProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\OxidKernel;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\CompilerPass\MakeAllServicesPublicPass;
use OxidEsales\EshopCommunity\Tests\CompilerPass\ReplaceContextStubsPass;
use OxidEsales\EshopCommunity\Tests\CompilerPass\SilenceLoggerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

class TestContainerFactory implements ContainerProviderInterface
{
    private static ?OxidKernel $kernel = null;
    private static ?ContainerInterface $symfonyContainer = null;

    /**
     * @deprecated Use get() instead. This method exists for backward compatibility
     *             with tests that need an uncompiled container.
     */
    public function create(): SymfonyContainerBuilder
    {
        $shopId = (new ShopIdCalculator(new UtilsServer()))->getShopId();
        $contextStub = new ContextStub($shopId);

        $container = (new ContainerBuilder($contextStub, $shopId))->getContainer();
        $container->set(ContextInterface::class, $contextStub);
        $container->set(BasicContextInterface::class, $contextStub);
        $container->autowire(BasicContextInterface::class, \OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub::class);
        $container->autowire(ContextInterface::class, ContextStub::class);

        $this->setAllServicesAsPublic($container);

        $container->addCompilerPass(new ReplaceContextStubsPass());
        $container->addCompilerPass(new MakeAllServicesPublicPass());
        $container->addCompilerPass(new SilenceLoggerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -100);

        return $container;
    }

    public static function get(): ContainerInterface
    {
        if (self::$symfonyContainer === null) {
            self::$kernel = new OxidKernel('test', true);
            self::$kernel->addCompilerPass(new MakeAllServicesPublicPass());
            self::$kernel->addCompilerPass(new ReplaceContextStubsPass());
            self::$kernel->addCompilerPass(new SilenceLoggerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -100);
            self::$kernel->boot();
            self::$symfonyContainer = self::$kernel->getContainer();
        }
        return self::$symfonyContainer;
    }

    public static function setContainer(ContainerInterface $container): void
    {
        self::$symfonyContainer = $container;
    }

    public static function resetContainer(): void
    {
        self::$kernel?->shutdown();
        self::$kernel = null;
        self::$symfonyContainer = null;
        self::clearKernelFreshnessCache();
        clearstatcache();
    }

    /**
     * Symfony's Kernel caches the freshness check result per-process in a static property.
     * `SelfCheckingResourceChecker` likewise caches per-resource-and-timestamp.
     * Tests that reset the container and write YAML between boots must clear both static caches
     * so the next boot actually re-checks resource freshness.
     */
    private static function clearKernelFreshnessCache(): void
    {
        self::clearStaticCache(\Symfony\Component\HttpKernel\Kernel::class, 'freshCache');
        self::clearStaticCache(\Symfony\Component\Config\Resource\SelfCheckingResourceChecker::class, 'cache');
    }

    private static function clearStaticCache(string $className, string $propertyName): void
    {
        $reflection = new \ReflectionClass($className);
        if ($reflection->hasProperty($propertyName)) {
            $reflection->getProperty($propertyName)->setValue(null, []);
        }
    }

    private function setAllServicesAsPublic(SymfonyContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
    }
}
