<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload;

use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\UnknownClassProbingActiveModulesDataProviderBridge;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class ModuleAutoloadTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testAutoloadIgnoresClassWithoutParentSuffix(): void
    {
        $this->assertFalse(ModuleAutoload::autoload('Vendor\NotInstalledPackage\SomeClass'));
    }

    public function testActiveModulesServiceIsCreatedWhenItProbesUnknownClass(): void
    {
        $this->bootContainerWithActiveModulesServiceProbingUnknownClass();

        $activeModulesDataProvider = ContainerFacade::get(ActiveModulesDataProviderBridgeInterface::class);

        $this->assertInstanceOf(UnknownClassProbingActiveModulesDataProviderBridge::class, $activeModulesDataProvider);
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
}
