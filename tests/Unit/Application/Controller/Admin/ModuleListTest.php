<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ModuleList;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\ModuleInstallerTrait;

class ModuleListTest extends \OxidTestCase
{
    use ModuleInstallerTrait;

    public function setup(): void
    {
        parent::setUp();
        ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
    }

    /**
     * Module_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $moduleList = oxNew(ModuleList::class);
        $this->assertEquals('module_list', $moduleList->render());
    }

    public function testRenderWithCorrectModuleNames()
    {
        $modulesDirectory = __DIR__ . '/../../../testData/modules/';
        $this->installModule($modulesDirectory . 'testmodule');

        $oView = oxNew('Module_List');
        $this->assertEquals('module_list', $oView->render());

        $aViewData = $oView->getViewData();
        $aModulesNames = array_keys($aViewData['mylist']);
        $this->assertSame('testmodule', current($aModulesNames));
    }

    public function testModulesSortedByTitle(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();

        $moduleA = new ModuleConfiguration();
        $moduleA
            ->setId('a')
            ->setModuleSource('some')
            ->setTitle(['en' => 'A']);

        $moduleB = new ModuleConfiguration();
        $moduleB
            ->setId('b')
            ->setModuleSource('some')
            ->setTitle(['en' => 'B']);

        $shopConfiguration->addModuleConfiguration($moduleB);
        $shopConfiguration->addModuleConfiguration($moduleA);

        $container->get(ShopConfigurationDaoBridgeInterface::class)->save($shopConfiguration);

        $moduleList = oxNew(ModuleList::class);
        $moduleList->render();

        $modules = array_values($moduleList->getViewData()['mylist']);

        $this->assertSame(
            'A',
            $modules[0]->getTitle()
        );

        $this->assertSame(
            'B',
            $modules[1]->getTitle()
        );
    }
}
