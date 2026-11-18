<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\UnknownClassProbingActiveModulesDataProviderBridge;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ModuleAutoloadTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testUnknownClassLookupDoesNotQueryModuleClassExtensions(): void
    {
        $activeModulesDataProvider = $this->createMock(ActiveModulesDataProviderBridgeInterface::class);
        $activeModulesDataProvider->expects($this->never())->method('getClassExtensions');
        $this->createContainer();
        $this->replaceService(ActiveModulesDataProviderBridgeInterface::class, $activeModulesDataProvider);
        $this->compileContainer();
        $this->replaceContainerInstance();

        class_exists('Vendor\NotInstalledPackage\SomeClass');
    }

    public function testActiveModulesServiceCanProbeUnknownClassWhileBeingCreated(): void
    {
        $this->createContainer();
        $this->container
            ->register(
                ActiveModulesDataProviderBridgeInterface::class,
                UnknownClassProbingActiveModulesDataProviderBridge::class
            )
            ->setPublic(true);
        $this->compileContainer();
        $this->replaceContainerInstance();

        $this->expectNotToPerformAssertions();

        ContainerFacade::get(ActiveModulesDataProviderBridgeInterface::class);
    }
}
