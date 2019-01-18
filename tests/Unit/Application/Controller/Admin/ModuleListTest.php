<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use oxConfig;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
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
        $oView = oxNew('Module_List');
        $this->assertEquals('module_list.tpl', $oView->render());
    }

    public function testRenderWithCorrectModuleNames()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $config->expects($this->any())->method('getModulesDir')->will($this->returnValue(__DIR__.'/../../../testData/modules/'));

        $oView = oxNew('Module_List');
        Registry::set(Config::class, $config);
        $this->assertEquals('module_list.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $aModulesNames = array_keys($aViewData['mylist']);
        $this->assertSame('testmodule', current($aModulesNames));
    }
}
