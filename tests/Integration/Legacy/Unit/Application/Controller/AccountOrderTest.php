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
class AccountOrderTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Account_Order::getPageNavigation() test case
     */
    public function testGetPageNavigation()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["generatePageNavigation"]);
        $oView->expects($this->once())->method('generatePageNavigation');
        $this->assertNull($oView->getPageNavigation());
    }

    /**
     * Testing Account_Order::getOrderArticleList()
     */
    public function testGetOrderArticleListEmptyOrderList()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["getOrderList"]);
        $oView->method('getOrderList')->willReturn(false);
        $this->assertFalse($oView->getOrderArticleList());
    }

    /**
     * Testing Account_Order::getOrderArticleList()
     */
    public function testGetOrderArticleList()
    {
        oxTestModules::addFunction('oxarticlelist', 'loadOrderArticles', '{ return "testOrderArticles"; }');

        $oOrderList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ["count"]);
        $oOrderList->method('count')->willReturn(1);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["getOrderList"]);
        $oView->method('getOrderList')->willReturn($oOrderList);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\ArticleList::class, $oView->getOrderArticleList());
    }

    /**
     * Testing Account_Order::getOrderList()
     */
    public function testGetOrderListNoSessionUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["getUser"]);
        $oView->method('getUser')->willReturn(false);
        $this->assertCount(0, $oView->getOrderList());
    }

    /**
     * Testing Account_Order::getOrderList()
     */
    public function testGetOrderList()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getOrders", "getOrderCount"]);
        $oUser->expects($this->once())->method('getOrders')->willReturn("testOrders");
        $oUser->expects($this->once())->method('getOrderCount')->willReturn(1);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertSame("testOrders", $oView->getOrderList());
    }

    /**
     * Testing Account_Newsletter::render()
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["getUser"]);
        $oView->method('getUser')->willReturn(false);
        $this->assertSame('page/account/login', $oView->render());
    }

    /**
     * Testing Account_Newsletter::render()
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountOrderController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertSame('page/account/order', $oView->render());
    }

    /**
     * Testing Account_Orders::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccOrder = oxNew('Account_Order');

        $this->assertCount(2, $oAccOrder->getBreadCrumb());
    }
}
