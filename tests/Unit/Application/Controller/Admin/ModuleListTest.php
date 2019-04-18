<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use oxConfig;
use OxidEsales\Eshop\Application\Controller\Admin\ModuleList;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ModuleListTest extends \OxidTestCase
{
    public function setUp()
    {
        parent::setUp();
        ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')
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
        $this->assertEquals('module_list.tpl', $moduleList->render());
    }

    public function testRenderWithCorrectModuleNames()
    {
        $modulesDirectory = __DIR__.'/../../../testData/modules/';

        /** @var oxConfig|MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $config->expects($this->any())->method('getModulesDir')->will($this->returnValue($modulesDirectory));

        $container = ContainerFactory::getInstance()->getContainer();

        $container->get(ModuleInstallerInterface::class)->install(
            new OxidEshopPackage('testmodule', $modulesDirectory . 'testmodule')
        );

        $oView = oxNew('Module_List');
        $oView->setConfig($config);
        $this->assertEquals('module_list.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $aModulesNames = array_keys($aViewData['mylist']);
        $this->assertSame('testmodule', current($aModulesNames));
    }
}
