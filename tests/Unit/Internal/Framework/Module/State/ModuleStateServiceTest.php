<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\State;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateService;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleStateServiceTest extends TestCase
{
    public function testIsActiveReturnsTrueIfModuleIsActive()
    {
        $moduleStateService = new ModuleStateService($this->getShopConfigurationSettingDao());

        $this->assertTrue(
            $moduleStateService->isActive('activeModuleId', 1)
        );
    }

    public function testIsActiveReturnsFalseIfModuleIsNotActive()
    {
        $moduleStateService = new ModuleStateService($this->getShopConfigurationSettingDao());

        $this->assertFalse(
            $moduleStateService->isActive('notActiveModuleId', 1)
        );
    }

    public function testIsActiveReturnsFalseIfNoActiveModules()
    {
        $shopConfigurationSettingDao = $this->getMockBuilder(ShopConfigurationSettingDaoInterface::class)->getMock();
        $shopConfigurationSettingDao
            ->method('get')
            ->willThrowException(new EntryDoesNotExistDaoException());

        $moduleStateService = new ModuleStateService($shopConfigurationSettingDao);

        $this->assertFalse(
            $moduleStateService->isActive('notActiveModuleId', 1)
        );
    }

    private function getShopConfigurationSettingDao(): ShopConfigurationSettingDaoInterface
    {
        $activeModulesSetting = new ShopConfigurationSetting();
        $activeModulesSetting->setValue([
            'activeModuleId',
            'anotherActiveModuleId',
        ]);

        $shopConfigurationSettingDao = $this->getMockBuilder(ShopConfigurationSettingDaoInterface::class)->getMock();
        $shopConfigurationSettingDao
            ->method('get')
            ->with(ShopConfigurationSetting::ACTIVE_MODULES, 1)
            ->willReturn($activeModulesSetting);

        return $shopConfigurationSettingDao;
    }
}
