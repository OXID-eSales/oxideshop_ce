<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class ModuleDiEventsTest extends IntegrationTestCase
{
    private int $shopId = 1;
    private string $testModuleId = 'testModuleId';

    public function testActivationEventIsDispatchedForModule(): void
    {
        $this->persistTestModuleConfiguration();

        $receivedModuleId = null;
        $receivedShopId = null;
        $this->get(EventDispatcherInterface::class)->addListener(
            FinalizingModuleActivationEvent::class,
            function (FinalizingModuleActivationEvent $event) use (&$receivedModuleId, &$receivedShopId): void {
                $receivedModuleId = $event->getModuleId();
                $receivedShopId = $event->getShopId();
            }
        );

        $this->get(ModuleActivationServiceInterface::class)->activate($this->testModuleId, $this->shopId);

        $this->assertSame($this->testModuleId, $receivedModuleId);
        $this->assertSame($this->shopId, $receivedShopId);
    }

    public function testDeactivationEventIsDispatchedForModule(): void
    {
        $this->persistTestModuleConfiguration();

        $receivedModuleId = null;
        $receivedShopId = null;
        $this->get(EventDispatcherInterface::class)->addListener(
            FinalizingModuleDeactivationEvent::class,
            function (FinalizingModuleDeactivationEvent $event) use (&$receivedModuleId, &$receivedShopId): void {
                $receivedModuleId = $event->getModuleId();
                $receivedShopId = $event->getShopId();
            }
        );

        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);

        $this->assertSame($this->testModuleId, $receivedModuleId);
        $this->assertSame($this->shopId, $receivedShopId);
    }

    private function persistTestModuleConfiguration(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($this->testModuleId)
            ->setModuleSource('test');

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $this->get(ShopConfigurationDaoInterface::class)->save($shopConfiguration, $this->shopId);
    }
}
