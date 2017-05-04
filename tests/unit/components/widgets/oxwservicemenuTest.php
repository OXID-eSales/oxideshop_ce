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
 * Tests for oxwServiceMenu class
 */
class Unit_Components_Widgets_oxwServiceMenuTest extends OxidTestCase
{

    /**
     * Testing oxwServiceMenu::getCompareItemsCnt()
     *
     * @return null
     */
    public function testGetCompareItemsCnt()
    {
        $oCompare = $this->getMock("compare", array("getCompareItemsCnt"));
        $oCompare->expects($this->once())->method("getCompareItemsCnt")->will($this->returnValue(10));
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = new oxwServiceMenu();
        $this->assertEquals(10, $oServiceMenu->getCompareItemsCnt());
    }

    /**
     * Testing oxwServiceMenu::getCompareItems()
     *
     * @return null
     */
    public function testGetCompareItems()
    {
        $aItems = array("testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3");
        $oCompare = $this->getMock("compare", array("getCompareItems"));
        $oCompare->expects($this->once())->method("getCompareItems")->will($this->returnValue($aItems));
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = new oxwServiceMenu();
        $this->assertEquals($aItems, $oServiceMenu->getCompareItems());
    }

    /**
     * Testing oxwServiceMenu::getCompareItems()
     *
     * @return null
     */
    public function testGetCompareItemsInJson()
    {
        $aItems = array("testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3");
        $aResult = '{"testId1":"testVal1","testId2":"testVal2","testId3":"testVal3"}';
        $oCompare = $this->getMock("compare", array("getCompareItems"));
        $oCompare->expects($this->once())->method("getCompareItems")->will($this->returnValue($aItems));
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = new oxwServiceMenu();
        $this->assertEquals($aResult, $oServiceMenu->getCompareItems(true));
    }

}