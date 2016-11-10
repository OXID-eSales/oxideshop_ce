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
namespace Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\ArticleList;
use \oxField;
use \oxTestModules;

/**
 * Tests for Account class
 */
class AccountOrderTest extends \OxidTestCase
{

    /**
     * Account_Order::getPageNavigation() test case
     *
     * @return  null
     */
    public function testGetPageNavigation()
    {
        $oView = $this->getMock("Account_Order", array("generatePageNavigation"));
        $oView->expects($this->once())->method('generatePageNavigation');
        $this->assertNull($oView->getPageNavigation());
    }

    /**
     * Testing Account_Order::getOrderArticleList()
     *
     * @return null
     */
    public function testGetOrderArticleListEmptyOrderList()
    {
        $oView = $this->getMock("Account_Order", array("getOrderList"));
        $oView->expects($this->any())->method('getOrderList')->will($this->returnValue(false));
        $this->assertFalse($oView->getOrderArticleList());
    }

    /**
     * Testing Account_Order::getOrderArticleList()
     *
     * @return null
     */
    public function testGetOrderArticleList()
    {
        oxTestModules::addFunction('oxarticlelist', 'loadOrderArticles', '{ return "testOrderArticles"; }');

        $oOrderList = $this->getMock("oxlist", array("count"));
        $oOrderList->expects($this->any())->method('count')->will($this->returnValue(1));

        $oView = $this->getMock("Account_Order", array("getOrderList"));
        $oView->expects($this->any())->method('getOrderList')->will($this->returnValue($oOrderList));
        $this->assertTrue($oView->getOrderArticleList() instanceof ArticleList);
    }

    /**
     * Testing Account_Order::getOrderList()
     *
     * @return null
     */
    public function testGetOrderListNoSessionUser()
    {
        $oView = $this->getMock("Account_Order", array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals(0, count($oView->getOrderList()));
    }

    /**
     * Testing Account_Order::getOrderList()
     *
     * @return null
     */
    public function testGetOrderList()
    {
        $oUser = $this->getMock("oxUser", array("getOrders", "getOrderCount"));
        $oUser->expects($this->once())->method('getOrders')->will($this->returnValue("testOrders"));
        $oUser->expects($this->once())->method('getOrderCount')->will($this->returnValue(1));

        $oView = $this->getMock("Account_Order", array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals("testOrders", $oView->getOrderList());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock("Account_Order", array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('page/account/login.tpl', $oView->render());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        $oView = $this->getMock("Account_Order", array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals('page/account/order.tpl', $oView->render());
    }

    /**
     * Testing Account_Orders::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccOrder = oxNew('Account_Order');

        $this->assertEquals(2, count($oAccOrder->getBreadCrumb()));
    }
}
