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
 * Tests for VoucherSerie_List class
 */
class Unit_Admin_VoucherSerieListTest extends OxidTestCase
{

    /**
     * VoucherSerie_List::DeleteEntry() test case
     *
     * @return null
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");
        oxTestModules::addFunction('oxvoucherserie', 'load', '{ return true; }');
        oxTestModules::addFunction('oxvoucherserie', 'deleteVoucherList', '{ return true; }');

        $oSess = $this->getMock('oxsession', array('checkSessionChallenge'));
        $oSess->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oView = $this->getMock($this->getProxyClassName('VoucherSerie_List'), array('getSession'));
        $oView->expects($this->any())->method('getSession')->will($this->returnValue($oSess));

        $oView->deleteEntry();
    }

    /**
     * VoucherSerie_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new VoucherSerie_List();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNull($aViewData["allowSharedEdit"]);
        $this->assertNull($aViewData["malladmin"]);
        $this->assertNull($aViewData["updatelist"]);
        $this->assertNull($aViewData["sort"]);

        $this->assertEquals('voucherserie_list.tpl', $sTplName);
    }
}
