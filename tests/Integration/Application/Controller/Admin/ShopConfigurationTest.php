<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * @covers \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
 */
final class ShopConfigurationTest extends UnitTestCase
{
    use ContainerTrait;

    private string $testModuleId = 'testModuleId';

    public function testSaveConfVars(): void
    {
        $this->prepareTestModuleConfiguration();

        $_POST['confstrs'] = ['stringSetting' => 'newValue'];

        $shopConfigurationController = $this->getMockBuilder(ShopConfiguration::class)
            ->setMethods(['getModuleForConfigVars'])
            ->disableOriginalConstructor()
            ->getMock();
        $shopConfigurationController->method('getModuleForConfigVars')->willReturn('module:testModuleId');
        $shopConfigurationController->saveConfVars();

        $container = ContainerFactory::getInstance()->getContainer();
        $moduleConfiguration = $container->get(ModuleConfigurationDaoBridgeInterface::class)->get($this->testModuleId);

        $this->assertSame(
            'newValue',
            $moduleConfiguration->getModuleSetting('stringSetting')->getValue()
        );
    }

    public function testSaveWhenSettingIsMissingInMetadata(): void
    {
        $this->prepareTestModuleConfiguration();

        $_POST['confstrs'] = ['nonExisting' => 'newValue'];

        $shopConfigurationController = $this->createPartialMock(ShopConfiguration::class, ['getModuleForConfigVars']);
        $shopConfigurationController->method('getModuleForConfigVars')->willReturn('module:testModuleId');
        $shopConfigurationController->saveConfVars();

        $this->assertSame(
            'newValue',
            Registry::getConfig()->getConfigParam('nonExisting')
        );
    }

    private function prepareTestModuleConfiguration(): void
    {
        $setting = new Setting();
        $setting
            ->setName('stringSetting')
            ->setValue('row')
            ->setType('str');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);
        $moduleConfiguration->setModuleSource('testModule');
        $moduleConfiguration->addModuleSetting($setting);

        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfigurationDao = $container->get(ShopConfigurationDaoBridgeInterface::class);

        $shopConfiguration = $shopConfigurationDao->get();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfigurationDao->save($shopConfiguration);
    }
}
