<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for basket class
 */
class BasketTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test error destination.
     */
    public function testGetErrorDestination()
    {
        $oBasket = oxNew('basket');
        $this->assertSame('basket', $oBasket->getErrorDestination());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     */
    public function testAddVoucherChecksGetShowVouchers()
    {
        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $oCfg = $this->getMock(Config::class, ["getShowVouchers"]);
        $oCfg->expects($this->once())->method('getShowVouchers')->willReturn(false);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ["getViewConfig"]);
        $oBasket->expects($this->once())->method('getViewConfig')->willReturn($oCfg);

        $this->assertNull($oBasket->addVoucher());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     */
    public function testRemoveVoucherChecksGetShowVouchers()
    {
        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $oCfg = $this->getMock(Config::class, ["getShowVouchers"]);
        $oCfg->expects($this->once())->method('getShowVouchers')->willReturn(false);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ["getViewConfig"]);
        $oBasket->expects($this->once())->method('getViewConfig')->willReturn($oCfg);

        $this->assertNull($oBasket->removeVoucher());
    }

    /**
     * test render
     */
    public function testRenderNoSE()
    {
        oxRegistry::getUtils()->setSearchEngine(false);

        $oBasket = oxNew('basket');

        $this->assertSame('page/checkout/basket', $oBasket->render());
    }

    public function testGetBasketArticles()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getBasketArticles']);
        $oB->expects($this->once())->method('getBasketArticles')->willReturn('bitems');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);
        $o = oxNew(\OxidEsales\Eshop\Application\Controller\BasketController::class);
        $this->assertSame('bitems', $o->getBasketArticles());
    }

    public function testGetFirstBasketProduct()
    {
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['getBasketArticles']);
        $o->expects($this->once())->method('getBasketArticles')->willReturn(['asd', 'fds']);

        $this->assertSame('asd', $o->getFirstBasketProduct());
    }

    public function testGetBasketSimilarList()
    {
        $oP = $this->getMock('stdclass', ['getSimilarProducts']);
        $oP->expects($this->once())->method('getSimilarProducts')->willReturn(['asd', 'fds']);
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['getFirstBasketProduct']);
        $o->expects($this->once())->method('getFirstBasketProduct')->willReturn($oP);

        $this->assertSame(['asd', 'fds'], $o->getBasketSimilarList());
    }


    public function testShowBackToShop()
    {
        $oConf = $this->getMock(Config::class, ['getConfigParam']);
        $oConf->expects($this->exactly(2))->method('getConfigParam')->with('iNewBasketItemMessage')->willReturn(3);
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConf);

        $this->assertEquals(false, $o->showBackToShop());
        $this->getSession()->setVariable('_backtoshop', 1);
        $this->assertEquals(true, $o->showBackToShop());
    }


    public function testAddVoucher()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['addVoucher']);
        $oB->expects($this->once())->method('addVoucher')->with('vouchnr');
        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge', 'getBasket'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        $session->method('getBasket')->willReturn($oB);
        Registry::set(Session::class, $session);
        $o = oxNew(\OxidEsales\Eshop\Application\Controller\BasketController::class);

        $this->setRequestParameter('voucherNr', 'vouchnr');
        $this->assertEquals(null, $o->addVoucher());
    }

    public function testRemoveVoucher()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['removeVoucher']);
        $oB->expects($this->once())->method('removeVoucher')->with('vouchnr');
        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge', 'getBasket'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        $session->method('getBasket')->willReturn($oB);
        Registry::set(Session::class, $session);
        $o = oxNew(\OxidEsales\Eshop\Application\Controller\BasketController::class);

        $this->setRequestParameter('voucherId', 'vouchnr');
        $this->assertEquals(null, $o->removeVoucher());
    }

    public function testBackToShop()
    {
        $this->getSession()->setVariable('_backtoshop', 'asd');
        $oConf = $this->getMock(Config::class, ['getConfigParam']);
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with('iNewBasketItemMessage')->willReturn(2);
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConf);
        $this->assertNull($o->backToShop());
    }

    public function testBackToShopShowPage()
    {
        $this->getSession()->setVariable('_backtoshop', 'asd');
        $oConf = $this->getMock(Config::class, ['getConfigParam']);
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with('iNewBasketItemMessage')->willReturn(3);
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConf);
        $this->assertSame('asd', $o->backToShop());

        $this->assertNull(oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    public function testBackToShopShowPageNoPage()
    {
        $this->getSession()->setVariable('_backtoshop', '');
        $oConf = $this->getMock(Config::class, ['getConfigParam']);
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with('iNewBasketItemMessage')->willReturn(3);
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConf);
        $this->assertNull($o->backToShop());

        $this->assertSame('', oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    /**
     * Test get ids for similar recommendation list.
     */
    public function testGetSimilarRecommListIds()
    {
        $articleId = "articleId";
        $aArrayKeys = [$articleId];
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getId"]);
        $oProduct->expects($this->once())->method("getId")->willReturn($articleId);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ["getFirstBasketProduct"]);
        $oDetails->expects($this->once())->method("getFirstBasketProduct")->willReturn($oProduct);
        $this->assertSame($aArrayKeys, $oDetails->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of key from result of getFirstBasketProduct()");
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        $this->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $oS->expects($this->never())->method('getBasketReservations');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oS);

        $oB = oxNew(\OxidEsales\Eshop\Application\Controller\BasketController::class);
        $oB->render();
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        $this->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', ['renewExpiration']);
        $oR->expects($this->once())->method('renewExpiration')->willReturn(null);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $oS->expects($this->once())->method('getBasketReservations')->willReturn($oR);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oS);

        $oB = oxNew(\OxidEsales\Eshop\Application\Controller\BasketController::class);
        $oB->render();
    }

    /**
     * Testing Basket::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oBasket = oxNew('Basket');

        $this->assertCount(1, $oBasket->getBreadCrumb());
    }

    /**
     * Testing Basket::getWrappingList()
     */
    public function testGetWrappingList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getWrappingList"; }');

        $oView = oxNew('Basket');
        $this->assertSame("getWrappingList", $oView->getWrappingList());
    }

    /**
     * Testing Basket::getCardList()
     */
    public function testGetCardList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getCardList"; }');

        $oView = oxNew('Basket');
        $this->assertSame("getCardList", $oView->getCardList());
    }

    /**
     * Testing Wrapping::changeWrapping()
     */
    public function testChangeWrapping()
    {
        $this->setRequestParameter("wrapping", [1 => 2]);
        $this->setRequestParameter("giftmessage", "testCardMessage");
        $this->setRequestParameter("chosencard", "testCardId");

        $oBasketItem1 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["setWrapping"]);
        $oBasketItem1->expects($this->once())->method('setWrapping')->with(2);

        $oBasketItem2 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["setWrapping"]);
        $oBasketItem2->expects($this->never())->method('setWrapping');

        $oContents = oxNew('oxList');
        $oContents->offsetSet(1, $oBasketItem1);
        $oContents->offsetSet(2, $oBasketItem2);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["getContents", "setCardMessage", "setCardId", "onUpdate"]);
        $oBasket->expects($this->atLeastOnce())->method('getContents')->willReturn($oContents);
        $oBasket->expects($this->atLeastOnce())->method('setCardMessage')->with("testCardMessage");
        $oBasket->expects($this->atLeastOnce())->method('setCardId')->with("testCardId");
        $oBasket->expects($this->atLeastOnce())->method('onUpdate');

        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge', 'getBasket'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        $session->method('getBasket')->willReturn($oBasket);
        Registry::set(Session::class, $session);

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getShowGiftWrapping"]);
        $oViewConfig->expects($this->atLeastOnce())->method('getShowGiftWrapping')->willReturn(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ["getViewConfig"], [], '', false);
        $oView->expects($this->atLeastOnce())->method('getViewConfig')->willReturn($oViewConfig);
        $oView->changeWrapping();
    }

    /**
     * Test is Wrapping
     */
    public function testIsWrapping()
    {
        $oView = oxNew('Basket');
        $this->assertTrue($oView->isWrapping());
    }

    /**
     * Test oxViewConfig::getShowGiftWrapping() affection
     */
    public function testIsWrappingIfWrappingIsOff()
    {
        $this->setConfigParam('bl_showGiftWrapping', false);

        $oView = oxNew('Basket');
        $this->assertFalse($oView->isWrapping());
    }
}
