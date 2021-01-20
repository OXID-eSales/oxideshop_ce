<?php declare(strict_types=1);

/**
* Copyright © OXID eSales AG. All rights reserved.
* See LICENSE file for license details.
*/

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
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
        $moduleStateService = $this->getMockBuilder(ModuleStateServiceInterface::class)->getMock();
        $moduleStateService
            ->method('isActive')
            ->willReturnMap([
                ['alreadyActiveModuleId', $this->shopId, true],
                ['alreadyDeactiveModuleId', $this->shopId, false],
            ]);

        $moduleActivationService = new ModuleActivationService(
            $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock(),
            $this->getMockBuilder(EventDispatcherInterface::class)->getMock(),
            $this->getMockBuilder(ModuleConfigurationHandlingServiceInterface::class)->getMock(),
            $moduleStateService,
            $this->getMockBuilder(ExtensionChainServiceInterface::class)->getMock(),
            $this->getMockBuilder(ModuleServicesActivationServiceInterface::class)->getMock()
        );

        return $moduleActivationService;
    }
}
