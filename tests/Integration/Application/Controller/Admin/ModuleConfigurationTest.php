<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration as ModuleConfigurationController;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
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

        $shopModuleSettings = $moduleConfiguration->getSetting(ModuleSetting::SHOP_MODULE_SETTING)->getValue();

        $this->assertSame('newValue', $shopModuleSettings[0]['value']);
    }

    private function prepareTestModuleConfiguration()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);
        $moduleConfiguration->setPath('testModule');
        $moduleConfiguration
            ->addSetting(new ModuleSetting(
                ModuleSetting::SHOP_MODULE_SETTING,
                [
                    [
                        'group' => 'frontend',
                        'name'  => 'stringSetting',
                        'type'  => 'str',
                        'value' => 'row',
                    ],
                    [
                        'group' => 'frontend',
                        'name'  => 'array',
                        'type'  => 'aarr',
                        'value' => ['1', '2'],
                    ],
                ]
            ));

        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfigurationDao = $container->get(ShopConfigurationDaoBridgeInterface::class);

        $shopConfiguration = $shopConfigurationDao->get();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfigurationDao->save($shopConfiguration);
    }
}
