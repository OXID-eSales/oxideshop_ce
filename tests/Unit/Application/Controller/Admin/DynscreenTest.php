<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Dynscreen class
 */
class DynscreenTest extends \OxidTestCase
{

    /**
     * Dynscreen::SetupNavigation() test case
     *
     * @return null
     */
    public function testSetupNavigation()
    {
        $sNode = "testNode";
        $this->setRequestParameter("menu", $sNode);
        $this->setRequestParameter('actedit', 1);

        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array("getListUrl", "getEditUrl", "getTabs", "getActiveTab", "getBtn"));
        $oNavigation->expects($this->any())->method('getActiveTab')->will($this->returnValue("testEdit"));
        $oNavigation->expects($this->once())->method('getListUrl')->with($this->equalTo($sNode))->will($this->returnValue("testListUrl"));
        $oNavigation->expects($this->once())->method('getEditUrl')->with($this->equalTo($sNode), $this->equalTo(1))->will($this->returnValue("testEditUrl"));
        $oNavigation->expects($this->once())->method('getTabs')->with($this->equalTo($sNode), $this->equalTo(1))->will($this->returnValue("editTabs"));
        $oNavigation->expects($this->once())->method('getBtn')->with($this->equalTo($sNode))->will($this->returnValue("testBtn"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicScreenController::class, array("getNavigation"));
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $oView->UNITsetupNavigation($sNode);
        $this->assertEquals("testListUrl&actedit=1", $oView->getViewDataElement("listurl"));
        $this->assertEquals("?testEditUrl&actedit=1", $oView->getViewDataElement("editurl"));
        $this->assertEquals("editTabs", $oView->getViewDataElement("editnavi"));
        $this->assertEquals("testEdit", $oView->getViewDataElement("actlocation"));
        $this->assertEquals("testEdit", $oView->getViewDataElement("default_edit"));
        $this->assertEquals(1, $oView->getViewDataElement("actedit"));
        $this->assertEquals("testBtn", $oView->getViewDataElement("bottom_buttons"));
    }

    /**
     * Dynscreen::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = oxNew('Dynscreen');
        $this->assertEquals('dyn_menu', $oView->getViewId());
    }

    /**
     * Dynscreen::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DynamicScreenController::class, array("_setupNavigation"));
        $oView->expects($this->once())->method('_setupNavigation');
        $this->assertEquals('dynscreen.tpl', $oView->render());
    }
}
