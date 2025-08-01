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
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

class TestContainerFactory implements ContainerProviderInterface
{
    private static $symfonyContainer;

    private ContextStub $context;

    public function __construct()
    {
        $this->context = new ContextStub();
    }

    public function create(): SymfonyContainerBuilder
    {
        $shopId = (new ShopIdCalculator(new UtilsServer()))->getShopId();
        $contextStub = new ContextStub($shopId);

        $container = (new ContainerBuilder($contextStub, $shopId))->getContainer();
        $container->set(ContextInterface::class, $contextStub);
        $container->set(BasicContextInterface::class, $contextStub);
        $container->autowire(BasicContextInterface::class, BasicContextStub::class);
        $container->autowire(ContextInterface::class, ContextStub::class);

        return $container;
    }

    public static function get(): ContainerInterface
    {
        if (self::$symfonyContainer === null) {
            self::$symfonyContainer = (new self())->create();
            self::$symfonyContainer->compile(true);
        }

        return self::$symfonyContainer;
    }

    public static function setContainer(ContainerInterface $container): void
    {
        self::$symfonyContainer = $container;
    }

    public static function resetContainer(): void
    {
        self::$symfonyContainer = null;
    }

    private function setAllServicesAsPublic(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        return $container;
    }

    private function setBasicContextStub(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        $container->set(BasicContextInterface::class, $this->context);
        $container->autowire(BasicContextInterface::class, BasicContextStub::class);

        return $container;
    }

    private function setContextStub(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        $container->set(ContextInterface::class, $this->context);
        $container->autowire(ContextInterface::class, ContextStub::class);

        return $container;
    }
}
