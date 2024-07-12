<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController;
use \oxField;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests for Account class
 * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController
 */
class AccountNoticeListControllerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing AccountNoticeListController::getSimilarRecommLists()
     */
    public function testGetSimilarRecommListIds()
    {
        $sArrayKey = "articleId";
        $aArrayKeys = [$sArrayKey];
        $aNoticeProdList = [$sArrayKey => "zyyy"];

        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oSearch */
        $oSearch = $this->getMock(AccountNoticeListController::class, ["getNoticeProductList"]);
        $oSearch->expects($this->once())->method("getNoticeProductList")->willReturn($aNoticeProdList);
        $this->assertSame(
            $aArrayKeys,
            $oSearch->getSimilarRecommListIds(),
            "getSimilarRecommListIds() should return array of keys from result of getNoticeProductList()"
        );
    }

    /**
     * Testing AccountNoticeListController::getSimilarProducts()
     */
    public function testGetSimilarProductsEmptyProductList()
    {
        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, ["getNoticeProductList"]);
        $oView->method('getNoticeProductList')->willReturn([]);
        $this->assertNull($oView->getSimilarProducts());
    }

    /**
     * Testing AccountNoticeListController::getSimilarProducts()
     */
    public function testGetSimilarProducts()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["getSimilarProducts"]);
        $oProduct->method('getSimilarProducts')->willReturn("testSimilarProducts");

        /** @var AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, ["getNoticeProductList"]);
        $oView->method('getNoticeProductList')->willReturn([$oProduct]);
        $this->assertSame("testSimilarProducts", $oView->getSimilarProducts());
    }

    /**
     * Testing AccountNoticeListController::getNoticeProductList()
     */
    public function testGetNoticeProductListNoSessionUser()
    {
        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, ["getUser"]);
        $oView->method('getUser')->willReturn(false);
        $this->assertNull($oView->getNoticeProductList());
    }

    /**
     * Testing AccountNoticeListController::getNoticeProductList()
     */
    public function testGetNoticeProductList()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["getArticles"]);
        $oBasket->expects($this->once())->method('getArticles')->willReturn("articles");

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getBasket"]);
        $oUser->expects($this->once())->method('getBasket')->with("noticelist")->willReturn($oBasket);

        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertSame("articles", $oView->getNoticeProductList());
    }

    /**
     * Testing Account_Newsletter::render()
     */
    public function testRenderNoUser()
    {
        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, ["getUser"]);
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

        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertSame('page/account/noticelist', $oView->render());
    }

    /**
     * Testing Account_Newsletter::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccNoticeList = new AccountNoticeListController();

        $this->assertCount(2, $oAccNoticeList->getBreadCrumb());
    }

    /**
     * Testing AccountNoticeListController::getNavigationParams()
     */
    public function testGetNavigationParams()
    {
        $oAccNoticeList = new AccountNoticeListController();

        $this->setRequestParameter('anid', 'testId');

        $aParams = $oAccNoticeList->getNavigationParams();

        $this->assertSame('testId', $aParams['anid'], "Should have correct anid navigation parameter");
    }
}
