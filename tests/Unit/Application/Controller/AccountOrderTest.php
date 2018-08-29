<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("generatePageNavigation"));
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("getOrderList"));
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

        $oOrderList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, array("count"));
        $oOrderList->expects($this->any())->method('count')->will($this->returnValue(1));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("getOrderList"));
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("getUser"));
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
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("getOrders", "getOrderCount"));
        $oUser->expects($this->once())->method('getOrders')->will($this->returnValue("testOrders"));
        $oUser->expects($this->once())->method('getOrderCount')->will($this->returnValue(1));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("getUser"));
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("getUser"));
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, array("getUser"));
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
