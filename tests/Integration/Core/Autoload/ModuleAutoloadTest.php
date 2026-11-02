<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload;

use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\UnknownClassProbingActiveModulesDataProviderBridge;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class ModuleAutoloadTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testUnknownClassLookupDoesNotBuildContainer(): void
    {
        $containerCache = $this->get(ContainerCacheInterface::class);
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->discardShopContainer();

        class_exists('Vendor\NotInstalledPackage\SomeClass');

        $this->assertFalse($containerCache->exists($shopId));
    }

    public function testActiveModulesServiceCanProbeUnknownClassWhileBeingCreated(): void
    {
        $this->bootContainerWithActiveModulesServiceProbingUnknownClass();

        $this->expectNotToPerformAssertions();

        ContainerFacade::get(ActiveModulesDataProviderBridgeInterface::class);
    }

    private function bootContainerWithActiveModulesServiceProbingUnknownClass(): void
    {
        $this->createContainer();
        $this->container
            ->register(
                ActiveModulesDataProviderBridgeInterface::class,
                UnknownClassProbingActiveModulesDataProviderBridge::class
            )
            ->setPublic(true);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();
        ModuleVariablesLocator::resetModuleVariables();
    }

    private function discardShopContainer(): void
    {
        ContainerFactory::resetContainer();
        ModuleVariablesLocator::resetModuleVariables();
    }
}
