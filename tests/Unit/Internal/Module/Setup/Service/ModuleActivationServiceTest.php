<?php declare(strict_types=1);

/**
* Copyright © OXID eSales AG. All rights reserved.
* See LICENSE file for license details.
*/

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ExtensionChainServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationService;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleServicesActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleConfigurationHandlingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleActivationServiceTest extends TestCase
{
    private $shopId = 1;

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSetupException
     */
    public function testThrowOnActivationIfModuleIsAlreadyActive()
    {
        $this->getTestModuleActivationService()
             ->activate('alreadyActiveModuleId', $this->shopId);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSetupException
     */
    public function testThrowOnDeactivationIfModuleIsNotActive()
    {
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
