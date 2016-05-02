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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Actions_List class
 */
class ActionsListTest extends \OxidTestCase
{

    /**
     * Actions_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getProxyClass("Actions_List");
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();

        $this->assertEquals('oxactions', $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals(array('oxactions' => array('oxtitle' => 'asc')), $oView->getListSorting());
        $this->assertEquals('actions_list.tpl', $sTplName);
    }

    /**
     * Actions_List::Render() test case
     *
     * @return null
     */
    public function testPromotionsRender()
    {
        $this->setRequestParameter("displaytype", "testType");

        $oView = $this->getProxyClass("Actions_List");
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();

        $this->assertEquals('oxactions', $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals(array('oxactions' => array('oxtitle' => 'asc')), $oView->getListSorting());
        $this->assertEquals('testType', $aViewData['displaytype']);
        $this->assertEquals('actions_list.tpl', $sTplName);
    }

    /**
     * Actions_List::_prepareWhereQuery() test case
     */
    public function testPrepareWhereQuery()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $iTime = time();
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{ return ' . $iTime . '; }');
        $sTable = getViewName("oxactions");
        $sNow = date('Y-m-d H:i:s', $iTime);

        $oView = oxNew('Actions_List');

        $sQ = " and $sTable.oxactivefrom < '$sNow' and $sTable.oxactiveto > '$sNow' ";
        $this->setRequestParameter('displaytype', 1);
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery(array(), ""));

        $sQ = " and $sTable.oxactivefrom > '$sNow' ";
        $this->setRequestParameter('displaytype', 2);
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery(array(), ""));

        $sQ = " and $sTable.oxactiveto < '$sNow' and $sTable.oxactiveto != '0000-00-00 00:00:00' ";
        $this->setRequestParameter('displaytype', 3);
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery(array(), ""));
    }
}
