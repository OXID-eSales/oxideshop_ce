<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\SettingModuleSettingHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SettingModuleSettingHandlerTest extends TestCase
{
    public function testHandlingOnModuleActivation()
    {
        $shopModuleSetting = $this->getTestSetting();

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addModuleSetting($shopModuleSetting);

        $shopModuleSettingDao = $this->getMockBuilder(SettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->once())
            ->method('save')
            ->with($shopModuleSetting);

        $handler = new SettingModuleSettingHandler($shopModuleSettingDao);
        $handler->handleOnModuleActivation($moduleConfiguration, 1);
    }

    public function testHandlingOnModuleDeactivation()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addModuleSetting($this->getTestSetting());

        $shopModuleSetting = $this->getTestSetting();

        $shopModuleSettingDao = $this->getMockBuilder(SettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->once())
            ->method('delete')
            ->with($shopModuleSetting);

        $handler = new SettingModuleSettingHandler($shopModuleSettingDao);
        $handler->handleOnModuleDeactivation($moduleConfiguration, 1);
    }

    private function getTestSetting(): Setting
    {
        $shopModuleSetting = new Setting();
        $shopModuleSetting
            ->setName('blCustomGridFramework')
            ->setValue('false')
            ->setType('bool')
            ->setConstraints(['1', '2', '3',])
            ->setGroupName('frontend')
            ->setPositionInGroup(5);

        return $shopModuleSetting;
    }
}
