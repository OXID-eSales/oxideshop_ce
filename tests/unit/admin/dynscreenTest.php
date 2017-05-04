<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Dynscreen class
 */
class Unit_Admin_DynscreenTest extends OxidTestCase
{

    /**
     * Dynscreen::SetupNavigation() test case
     *
     * @return null
     */
    public function testSetupNavigation()
    {
        $sNode = "testNode";
        modConfig::setRequestParameter("menu", $sNode);
        modConfig::setRequestParameter('actedit', 1);

        $oNavigation = $this->getMock("oxnavigationtree", array("getListUrl", "getEditUrl", "getTabs", "getActiveTab", "getBtn"));
        $oNavigation->expects($this->any())->method('getActiveTab')->will($this->returnValue("testEdit"));
        $oNavigation->expects($this->once())->method('getListUrl')->with($this->equalTo($sNode))->will($this->returnValue("testListUrl"));
        $oNavigation->expects($this->once())->method('getEditUrl')->with($this->equalTo($sNode), $this->equalTo(1))->will($this->returnValue("testEditUrl"));
        $oNavigation->expects($this->once())->method('getTabs')->with($this->equalTo($sNode), $this->equalTo(1))->will($this->returnValue("editTabs"));
        $oNavigation->expects($this->once())->method('getBtn')->with($this->equalTo($sNode))->will($this->returnValue("testBtn"));

        $oView = $this->getMock("Dynscreen", array("getNavigation"));
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
        $oView = new Dynscreen();
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
        $oView = $this->getMock("Dynscreen", array("_setupNavigation"));
        $oView->expects($this->once())->method('_setupNavigation');
        $this->assertEquals('dynscreen.tpl', $oView->render());
    }

}
