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
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\TestData\TestModule\ModuleEvents;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;

/**
 * @internal
 */
class ModuleEventsTest extends IntegrationTestCase
{
    private $shopId = 1;
    private $testModuleId = 'testModuleId';

    public function testActivationEventWasExecuted()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addEvent(new Event('onActivate', ModuleEvents::class . '::onActivate'));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onActivate was called', $eventMessage);
    }

    public function testActivationEventWasExecutedSecondTime()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addEvent(new Event('onActivate', ModuleEvents::class . '::onActivate'));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        ob_end_clean();

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onActivate was called', $eventMessage);
    }


    public function testDeactivationEventWasExecuted()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addEvent(new Event('onDeactivate', ModuleEvents::class . '::onDeactivate'));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        ob_start();
        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onDeactivate was called', $eventMessage);
    }

    private function getTestModuleConfiguration(): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);
        $moduleConfiguration
            ->setModuleSource('test');

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     */
    private function persistModuleConfiguration(ModuleConfiguration $moduleConfiguration)
    {
        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $this->get(ShopConfigurationDaoInterface::class)->save(
            $shopConfiguration,
            $this->shopId
        );
    }
}
