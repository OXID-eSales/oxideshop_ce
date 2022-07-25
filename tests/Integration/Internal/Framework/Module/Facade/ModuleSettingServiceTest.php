<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\u;

final class ModuleSettingServiceTest extends TestCase
{
    use ContainerTrait;

    private string $testModuleId = 'testSettingModuleId';
    private ModuleSettingServiceInterface $settingFacade;


    protected function setUp(): void
    {
        parent::setUp();
        $this->settingFacade = $this->get(ModuleSettingServiceInterface::class);
        $this->prepareTestShopConfiguration();
    }

    public function testInteger(): void
    {
        $this->settingFacade->saveInteger('intSetting', 777, $this->testModuleId);

        $this->assertSame(777, $this->settingFacade->getInteger('intSetting', $this->testModuleId));
    }

    public function testBoolean(): void
    {
        $this->settingFacade->saveBoolean('boolSetting', true, $this->testModuleId);

        $this->assertSame(true, $this->settingFacade->getBoolean('boolSetting', $this->testModuleId));
    }

    public function testString(): void
    {
        $this->settingFacade->saveString('stringSetting', 'test', $this->testModuleId);

        $this->assertEquals(u('test'), $this->settingFacade->getString('stringSetting', $this->testModuleId));
    }

    public function testCollection(): void
    {
        $this->settingFacade->saveCollection('arraySetting', [1, 2, 3], $this->testModuleId);

        $this->assertSame([1, 2, 3], $this->settingFacade->getCollection('arraySetting', $this->testModuleId));
    }

    public function testGetterReturnsValueFromCache(): void
    {
        $this->settingFacade->saveString('stringSetting', 'cachedValue', $this->testModuleId);
        $this->settingFacade->getString('stringSetting', $this->testModuleId);

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->testModuleId, 1);
        $setting = $moduleConfiguration->getModuleSetting('stringSetting');
        $setting->setValue('newValue');
        $this->get(ModuleConfigurationDaoInterface::class)->save($moduleConfiguration, 1);

        $this->assertEquals('cachedValue', $this->settingFacade->getString('stringSetting', $this->testModuleId));
    }

    public function testModuleActivationCleansCache(): void
    {
        $this->settingFacade->saveString('stringSetting', 'cachedValue', $this->testModuleId);
        $this->settingFacade->getString('stringSetting', $this->testModuleId);

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->testModuleId, 1);
        $setting = $moduleConfiguration->getModuleSetting('stringSetting');
        $setting->setValue('newValue');
        $this->get(ModuleConfigurationDaoInterface::class)->save($moduleConfiguration, 1);

        $this->assertEquals('cachedValue', $this->settingFacade->getString('stringSetting', $this->testModuleId));

        $this->get(ModuleActivationServiceInterface::class)->activate($this->testModuleId, 1);

        $this->assertEquals('newValue', $this->settingFacade->getString('stringSetting', $this->testModuleId));
    }

    private function prepareTestShopConfiguration(): void
    {
        $integerSetting = new Setting();
        $integerSetting
            ->setName('intSetting')
            ->setValue(0);

        $booleanSetting = new Setting();
        $booleanSetting
            ->setName('boolSetting')
            ->setValue(false);

        $stringSetting = new Setting();
        $stringSetting
            ->setName('stringSetting')
            ->setValue('default');

        $collectionSetting = new Setting();
        $collectionSetting
            ->setName('arraySetting')
            ->setValue([]);


        $testModule = new ModuleConfiguration();
        $testModule
            ->setId($this->testModuleId)
            ->setModuleSource('testPath')
            ->addModuleSetting($integerSetting)
            ->addModuleSetting($booleanSetting)
            ->addModuleSetting($stringSetting)
            ->addModuleSetting($collectionSetting);


        /** @var ShopConfigurationDaoInterface $dao */
        $dao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfiguration = $dao->get(1);
        $shopConfiguration->addModuleConfiguration($testModule);

        $dao->save($shopConfiguration, 1);
    }
}
