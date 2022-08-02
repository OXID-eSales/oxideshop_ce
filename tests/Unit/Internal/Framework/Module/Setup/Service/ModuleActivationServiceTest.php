<?php

declare(strict_types=1);

/**
* Copyright © OXID eSales AG. All rights reserved.
* See LICENSE file for license details.
*/

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ExtensionChainServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleServicesActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleConfigurationHandlingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleActivationServiceTest extends TestCase
{
    private $shopId = 1;

    public function testThrowOnActivationIfModuleIsAlreadyActive()
    {
        $this->expectException(ModuleSetupException::class);
        $this->getTestModuleActivationService()
             ->activate('alreadyActiveModuleId', $this->shopId);
    }

    public function testThrowOnDeactivationIfModuleIsNotActive()
    {
        $this->expectException(ModuleSetupException::class);
        $this->getTestModuleActivationService()
             ->deactivate('alreadyDeactiveModuleId', $this->shopId);
    }

    private function getTestModuleActivationService(): ModuleActivationService
    {
        $activeModuleConfiguration = new ModuleConfiguration();
        $activeModuleConfiguration->setId('alreadyActiveModuleId');
        $activeModuleConfiguration->setActivated(true);

        $inactiveModuleConfiguration = new ModuleConfiguration();
        $inactiveModuleConfiguration->setId('alreadyDeactiveModuleId');
        $inactiveModuleConfiguration->setActivated(false);

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->method('get')
            ->willReturnMap([
                ['alreadyActiveModuleId', $this->shopId, $activeModuleConfiguration],
                ['alreadyDeactiveModuleId', $this->shopId, $inactiveModuleConfiguration],
            ]);

        $moduleActivationService = new ModuleActivationService(
            $moduleConfigurationDao,
            $this->getMockBuilder(EventDispatcherInterface::class)->getMock(),
            $this->getMockBuilder(ModuleConfigurationHandlingServiceInterface::class)->getMock(),
            $this->getMockBuilder(ExtensionChainServiceInterface::class)->getMock(),
            $this->getMockBuilder(ModuleServicesActivationServiceInterface::class)->getMock()
        );

        return $moduleActivationService;
    }
}
