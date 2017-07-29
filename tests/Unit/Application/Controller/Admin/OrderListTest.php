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
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \oxDb;
use \oxTestModules;

/**
 * Testing order_list class.
 */
class OrderListTest extends \OxidTestCase
{

    /**
     * order_list::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxorder', 'load', '{ $this->oxorder__oxdeltype = new oxField("test"); $this->oxorder__oxtotalbrutsum = new oxField(10); $this->oxorder__oxcurrate = new oxField(10); }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('order_list');
        $this->assertEquals('order_list.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['folder']));
        $this->assertTrue(isset($aViewData['afolder']));
    }

    /**
     * order_list::storno() test case
     *
     * @return null
     */
    public function testStorno()
    {
        oxTestModules::addFunction('oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction('oxorder', 'cancelOrder', '{ return true; }');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\OrderList::class, array("init"));
        $oView->expects($this->once())->method('init');
        $oView->storno();
    }

    /**
     * order_list::_buildSelectString() test case
     *
     * @return null
     */
    public function testBuildSelectStringForOrderList()
    {
        $oDb = oxDb::getDb();
        $oListObject = oxNew('oxOrder');

        $this->setRequestParameter("addsearch", "oxorderarticles");
        $oView = oxNew('order_list');
        $sQ = $oView->UNITbuildSelectString($oListObject);
        $this->assertTrue(strpos($sQ, "oxorder where oxorder.oxpaid like " . $oDb->quote("%oxorderarticles%") . " and ") !== false);

        $this->setRequestParameter("addsearchfld", "oxorderarticles");
        $sQ = $oView->UNITbuildSelectString($oListObject);
        $this->assertTrue(strpos($sQ, "oxorder left join oxorderarticles on oxorderarticles.oxorderid=oxorder.oxid where ( oxorderarticles.oxartnum like " . $oDb->quote("%oxorderarticles%") . " or oxorderarticles.oxtitle like " . $oDb->quote("%oxorderarticles%") . " ) and ") !== false);

        $this->setRequestParameter("addsearchfld", "oxpayments");
        $sQ = $oView->UNITbuildSelectString($oListObject);
        $this->assertTrue(strpos($sQ, "oxorder left join oxpayments on oxpayments.oxid=oxorder.oxpaymenttype where oxpayments.oxdesc like " . $oDb->quote("%oxorderarticles%") . " and ") !== false);
    }

    /**
     * Test prepare where query.
     *
     * @return null
     */
    public function testPrepareWhereQuery()
    {
        oxTestModules::addFunction("oxlang", "isAdmin", "{return 1;}");
        $sExpQ = " and ( oxorder.oxfolder = 'ORDERFOLDER_NEW' )";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sExpQ .= " and oxorder.oxshopid = '1'";
        }
        $oOrderList = oxNew('order_list');
        $sQ = $oOrderList->UNITprepareWhereQuery(array(), "");
        $this->assertEquals($sExpQ, $sQ);
    }

    /**
     * Test prepare where query if folder is selected.
     *
     * @return null
     */
    public function testPrepareWhereQueryIfFolderSelected()
    {
        oxTestModules::addFunction("oxlang", "isAdmin", "{return 1;}");
        $this->setRequestParameter('folder', 'ORDERFOLDER_FINISHED');
        $sExpQ = " and ( oxorder.oxfolder = 'ORDERFOLDER_FINISHED' )";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sExpQ .= " and oxorder.oxshopid = '1'";
        }
        $oOrderList = oxNew('order_list');
        $sQ = $oOrderList->UNITprepareWhereQuery(array(), "");
        $this->assertEquals($sExpQ, $sQ);
    }
}
