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
 * Tests for Shop_Config class
 */
class Unit_Admin_ModuleMainTest extends OxidTestCase
{

    /**
     * Theme_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Module_Main();
        $this->assertEquals('module_main.tpl', $oView->render());
    }


    /**
     * Theme_Main::Render() test case - loading module object
     *
     * @return null
     */
    public function testRender_loadingObject()
    {
        $oView = $this->getMock('Module_Main', array('getEditObjectId'));
        $oView->expects($this->any())->method('getEditObjectId')->will($this->returnValue('oe/invoicepdf'));
        $oView->render();

        $aViewData = $oView->getViewData();

        $oModule = $aViewData['oModule'];
        $this->assertEquals("invoicepdf", $oModule->getInfo("id"));
    }
}
