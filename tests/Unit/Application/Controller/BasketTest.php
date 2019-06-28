<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for basket class
 */
class BasketTest extends \OxidTestCase
{

    /**
     * Test error destination.
     *
     * @return null
     */
    public function testGetErrorDestination()
    {
        $oBasket = oxNew('basket');
        $this->assertEquals('basket', $oBasket->getErrorDestination());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     *
     * @return null
     */
    public function testAddVoucherChecksGetShowVouchers()
    {
        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $oCfg = $this->getMock("stdClass", array("getShowVouchers"));
        $oCfg->expects($this->once())->method('getShowVouchers')->will($this->returnValue(false));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array("getViewConfig", 'getSession'));
        $oBasket->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oBasket->expects($this->never())->method('getSession');

        $this->assertSame(null, $oBasket->addVoucher());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     *
     * @return null
     */
    public function testRemoveVoucherChecksGetShowVouchers()
    {
        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $oCfg = $this->getMock("stdClass", array("getShowVouchers"));
        $oCfg->expects($this->once())->method('getShowVouchers')->will($this->returnValue(false));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array("getViewConfig", 'getSession'));
        $oBasket->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oBasket->expects($this->never())->method('getSession');

        $this->assertSame(null, $oBasket->removeVoucher());
    }

    /**
     * test render
     *
     * @return null
     */
    public function testRenderNoSE()
    {
        oxRegistry::getUtils()->setSearchEngine(false);

        $oBasket = oxNew('basket');

        $this->assertEquals('page/checkout/basket.tpl', $oBasket->render());
    }

    public function testGetBasketArticles()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getBasketArticles'));
        $oB->expects($this->once())->method('getBasketArticles')->will($this->returnValue('bitems'));
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oB));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->assertEquals('bitems', $o->getBasketArticles());
    }

    public function testGetFirstBasketProduct()
    {
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getBasketArticles'));
        $o->expects($this->once())->method('getBasketArticles')->will($this->returnValue(array('asd', 'fds')));

        $this->assertEquals('asd', $o->getFirstBasketProduct());
    }

    public function testGetBasketSimilarList()
    {
        $oP = $this->getMock('stdclass', array('getSimilarProducts'));
        $oP->expects($this->once())->method('getSimilarProducts')->will($this->returnValue(array('asd', 'fds')));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getFirstBasketProduct'));
        $o->expects($this->once())->method('getFirstBasketProduct')->will($this->returnValue($oP));

        $this->assertEquals(array('asd', 'fds'), $o->getBasketSimilarList());
    }


    public function testShowBackToShop()
    {
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(2))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getConfig'));
        $o->expects($this->exactly(2))->method('getConfig')->will($this->returnValue($oConf));

        $this->assertEquals(false, $o->showBackToShop());
        $this->getSession()->setVariable('_backtoshop', 1);
        $this->assertEquals(true, $o->showBackToShop());
    }


    public function testAddVoucher()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('addVoucher'));
        $oB->expects($this->once())->method('addVoucher')->with($this->equalTo('vouchnr'));
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oB));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $this->setRequestParameter('voucherNr', 'vouchnr');
        $this->assertEquals(null, $o->addVoucher());
    }

    public function testRemoveVoucher()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('removeVoucher'));
        $oB->expects($this->once())->method('removeVoucher')->with($this->equalTo('vouchnr'));
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oB));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $this->setRequestParameter('voucherId', 'vouchnr');
        $this->assertEquals(null, $o->removeVoucher());
    }

    public function testBackToShop()
    {
        $this->getSession()->setVariable('_backtoshop', 'asd');
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(2));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame(null, $o->backToShop());
    }

    public function testBackToShopShowPage()
    {
        $this->getSession()->setVariable('_backtoshop', 'asd');
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame('asd', $o->backToShop());

        $this->assertSame(null, oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    public function testBackToShopShowPageNoPage()
    {
        $this->getSession()->setVariable('_backtoshop', '');
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame(null, $o->backToShop());

        $this->assertSame('', oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    /**
     * Test get ids for similar recommendation list.
     *
     * @return null
     */
    public function testGetSimilarRecommListIds()
    {
        $articleId = "articleId";
        $aArrayKeys = array($articleId);
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getId"));
        $oProduct->expects($this->once())->method("getId")->will($this->returnValue($articleId));

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array("getFirstBasketProduct"));
        $oDetails->expects($this->once())->method("getFirstBasketProduct")->will($this->returnValue($oProduct));
        $this->assertEquals($aArrayKeys, $oDetails->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of key from result of getFirstBasketProduct()");
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        $this->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getSession'));
        $oB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oB->render();
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        $this->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array('getSession'));
        $oB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oB->render();
    }

    /**
     * Testing Basket::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oBasket = oxNew('Basket');

        $this->assertEquals(1, count($oBasket->getBreadCrumb()));
    }

    /**
     * Testing Basket::getWrappingList()
     *
     * @return null
     */
    public function testGetWrappingList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getWrappingList"; }');

        $oView = oxNew('Basket');
        $this->assertEquals("getWrappingList", $oView->getWrappingList());
    }

    /**
     * Testing Basket::getCardList()
     *
     * @return null
     */
    public function testGetCardList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getCardList"; }');

        $oView = oxNew('Basket');
        $this->assertEquals("getCardList", $oView->getCardList());
    }

    /**
     * Testing Wrapping::changeWrapping()
     *
     * @return null
     */
    public function testChangeWrapping()
    {
        $this->setRequestParameter("wrapping", array(1 => 2));
        $this->setRequestParameter("giftmessage", "testCardMessage");
        $this->setRequestParameter("chosencard", "testCardId");

        $oBasketItem1 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array("setWrapping"));
        $oBasketItem1->expects($this->once())->method('setWrapping')->with($this->equalTo(2));

        $oBasketItem2 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array("setWrapping"));
        $oBasketItem2->expects($this->never())->method('setWrapping');

        $oContents = oxNew('oxList');
        $oContents->offsetSet(1, $oBasketItem1);
        $oContents->offsetSet(2, $oBasketItem2);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("getContents", "setCardMessage", "setCardId", "onUpdate"));
        $oBasket->expects($this->atLeastOnce())->method('getContents')->will($this->returnValue($oContents));
        $oBasket->expects($this->atLeastOnce())->method('setCardMessage')->with($this->equalTo("testCardMessage"));
        $oBasket->expects($this->atLeastOnce())->method('setCardId')->with($this->equalTo("testCardId"));
        $oBasket->expects($this->atLeastOnce())->method('onUpdate');

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("getBasket"));
        $oSession->expects($this->atLeastOnce())->method('getBasket')->will($this->returnValue($oBasket));

        $session = $this->getMockBuilder(Session::class)->setMethods(['checkSessionChallenge'])->getMock();
        $session->method('checkSessionChallenge')->willReturn(true);
        Registry::set(Session::class, $session);

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, array("getShowGiftWrapping"));
        $oViewConfig->expects($this->atLeastOnce())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, array("getViewConfig", "getSession"), array(), '', false);
        $oView->expects($this->atLeastOnce())->method('getViewConfig')->will($this->returnValue($oViewConfig));
        $oView->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oSession));
        $oView->changeWrapping();
    }

    /**
     * Test is Wrapping
     *
     * @return null
     */
    public function testIsWrapping()
    {
        $oView = oxNew('Basket');
        $this->assertTrue($oView->isWrapping());
    }

    /**
     * Test oxViewConfig::getShowGiftWrapping() affection
     *
     * @return null
     */
    public function testIsWrappingIfWrappingIsOff()
    {
        $this->setConfigParam('bl_showGiftWrapping', false);

        $oView = oxNew('Basket');
        $this->assertSame(false, $oView->isWrapping());
    }
}
