<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * @covers \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
 */
class ShopConfigurationTest extends UnitTestCase
{
    private $testModuleId = 'testModuleId';

    public function testSaveConfVars(): void
    {
        $_POST['confstrs'] = ['stringSetting' => 'newValue'];

        $shopConfigurationController = $this->getMockBuilder(ShopConfiguration::class)
            ->setMethods(['_getModuleForConfigVars'])
            ->disableOriginalConstructor()
            ->getMock();
        $shopConfigurationController->method('_getModuleForConfigVars')->willReturn('module:testModuleId');
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
        $_POST['confstrs'] = ['nonExisting' => 'newValue'];

        $shopConfigurationController = $this->getMockBuilder(ShopConfiguration::class)
            ->setMethods(['_getModuleForConfigVars'])
            ->disableOriginalConstructor()
            ->getMock();
        $shopConfigurationController->method('_getModuleForConfigVars')->willReturn('module:testModuleId');
        $shopConfigurationController->saveConfVars();

        $container = ContainerFactory::getInstance()->getContainer();
        $valueFromDatabase = $container->get(SettingDaoInterface::class)->get('nonExisting', $this->testModuleId, 1);

        $this->assertSame(
            'newValue',
            $valueFromDatabase->getValue()
        );
    }
}
