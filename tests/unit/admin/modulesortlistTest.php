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
class Unit_Admin_ModuleSortListTest extends OxidTestCase
{

    /**
     * Module_SortList::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Module_SortList();
        $this->assertEquals('module_sortlist.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['aExtClasses']));
        $this->assertTrue(isset($aViewData['aDisabledModules']));
    }

    /**
     * Module_SortList::save()
     *
     * @return null
     */
    public function testSave()
    {
        $json = json_encode(array("oxarticle" => array("dir1/module1", "dir2/module2")));
        $this->getConfig()->setRequestParameter("aModules", $json);
        $this->getConfig()->setAdminMode(true);
        // result data
        $aModules = array("oxarticle" => "dir1/module1&dir2/module2");

        $oConfig = $this->getMock('oxConfig', array('saveShopConfVar'));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aModules"), $this->equalTo($aModules));

        $oView = $this->getMock('Module_SortList', array('getConfig'), array(), "", false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oView->save();
    }

    /**
     * Module_SortList::remove()
     *
     * @return null
     */
    public function testRemove()
    {
        $this->getConfig()->setRequestParameter("noButton", true);
        $oView = new Module_SortList();
        $oView->remove();
        $this->assertTrue($this->getSession()->getVariable("blSkipDeletedExtChecking"));
    }
}
