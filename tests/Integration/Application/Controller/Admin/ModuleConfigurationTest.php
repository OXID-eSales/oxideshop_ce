<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration as ModuleConfigurationController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * @internal
 */
class ModuleConfigurationTest extends UnitTestCase
{
    private $testModuleId = 'testModuleId';

    public function testRender()
    {
        $this->prepareTestModuleConfiguration();

        $_POST['oxid'] = $this->testModuleId;

        $moduleConfigurationController = oxNew(ModuleConfigurationController::class);

        $this->assertEquals('module_config.tpl', $moduleConfigurationController->render());

        $viewData = $moduleConfigurationController->getViewData();

        $this->assertSame(
            $this->testModuleId,
            $viewData['oModule']->getId()
        );
    }

    public function testSaveConfVars()
    {
        $this->prepareTestModuleConfiguration();

        $_POST['oxid'] = $this->testModuleId;
        $_POST['confstrs'] = ['stringSetting' => 'newValue'];

        $moduleConfigurationController = oxNew(ModuleConfigurationController::class);
        $moduleConfigurationController->saveConfVars();

        $container = ContainerFactory::getInstance()->getContainer();
        $moduleConfiguration = $container->get(ModuleConfigurationDaoBridgeInterface::class)->get($this->testModuleId);

        $this->assertSame(
            'newValue',
            $moduleConfiguration->getModuleSettings()[0]->getValue()
        );
    }

    private function prepareTestModuleConfiguration()
    {
        $setting = new Setting();
        $setting
            ->setName('stringSetting')
            ->setValue('row')
            ->setType('str');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);
        $moduleConfiguration->setPath('testModule');
        $moduleConfiguration->addModuleSetting($setting);

        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfigurationDao = $container->get(ShopConfigurationDaoBridgeInterface::class);

        $shopConfiguration = $shopConfigurationDao->get();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfigurationDao->save($shopConfiguration);
    }
}
