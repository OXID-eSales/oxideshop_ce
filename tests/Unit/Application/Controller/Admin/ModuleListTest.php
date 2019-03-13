<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use oxConfig;
use OxidEsales\Eshop\Application\Controller\Admin\ModuleList;
use PHPUnit\Framework\MockObject\MockObject;

class ModuleListTest extends \OxidTestCase
{
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
        /** @var oxConfig|MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $config->expects($this->any())->method('getModulesDir')->will($this->returnValue(__DIR__.'/../../../testData/modules/'));

        $oView = oxNew('Module_List');
        $oView->setConfig($config);
        $this->assertEquals('module_list.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $aModulesNames = array_keys($aViewData['mylist']);
        $this->assertSame('testmodule', current($aModulesNames));
    }
}
