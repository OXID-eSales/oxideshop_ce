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
 * Tests for basket class
 */
class Unit_Views_basketTest extends OxidTestCase
{

    /**
     * Test error destination.
     *
     * @return null
     */
    public function testGetErrorDestination()
    {
        $oBasket = new basket();
        $this->assertEquals('basket', $oBasket->getErrorDestination());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     *
     * @return null
     */
    public function testAddVoucherChecksGetShowVouchers()
    {
        $oCfg = $this->getMock("stdClass", array("getShowVouchers"));
        $oCfg->expects($this->once())->method('getShowVouchers')->will($this->returnValue(false));

        $oBasket = $this->getMock("basket", array("getViewConfig", 'getSession'));
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
        $oCfg = $this->getMock("stdClass", array("getShowVouchers"));
        $oCfg->expects($this->once())->method('getShowVouchers')->will($this->returnValue(false));

        $oBasket = $this->getMock("basket", array("getViewConfig", 'getSession'));
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

        $oBasket = new basket();

        $this->assertEquals('page/checkout/basket.tpl', $oBasket->render());
    }

    public function testGetBasketArticles()
    {
        $oB = $this->getMock('oxBasket', array('getBasketArticles'));
        $oB->expects($this->once())->method('getBasketArticles')->will($this->returnValue('bitems'));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oB));
        $o = $this->getMock('Basket', array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->assertEquals('bitems', $o->getBasketArticles());
    }

    public function testGetFirstBasketProduct()
    {
        $o = $this->getMock('Basket', array('getBasketArticles'));
        $o->expects($this->once())->method('getBasketArticles')->will($this->returnValue(array('asd', 'fds')));

        $this->assertEquals('asd', $o->getFirstBasketProduct());
    }

    public function testGetBasketSimilarList()
    {
        $oP = $this->getMock('stdclass', array('getSimilarProducts'));
        $oP->expects($this->once())->method('getSimilarProducts')->will($this->returnValue(array('asd', 'fds')));
        $o = $this->getMock('Basket', array('getFirstBasketProduct'));
        $o->expects($this->once())->method('getFirstBasketProduct')->will($this->returnValue($oP));

        $this->assertEquals(array('asd', 'fds'), $o->getBasketSimilarList());
    }


    public function testShowBackToShop()
    {
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(2))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock('Basket', array('getConfig'));
        $o->expects($this->exactly(2))->method('getConfig')->will($this->returnValue($oConf));

        $this->assertEquals(false, $o->showBackToShop());
        oxRegistry::getSession()->setVariable('_backtoshop', 1);
        $this->assertEquals(true, $o->showBackToShop());
    }


    public function testAddVoucher()
    {
        $oB = $this->getMock('oxBasket', array('addVoucher'));
        $oB->expects($this->once())->method('addVoucher')->with($this->equalTo('vouchnr'));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oB));
        $o = $this->getMock('Basket', array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->setRequestParam('voucherNr', 'vouchnr');
        $this->assertEquals(null, $o->addVoucher());
    }

    public function testRemoveVoucher()
    {
        $oB = $this->getMock('oxBasket', array('removeVoucher'));
        $oB->expects($this->once())->method('removeVoucher')->with($this->equalTo('vouchnr'));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oB));
        $o = $this->getMock('Basket', array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->setRequestParam('voucherId', 'vouchnr');
        $this->assertEquals(null, $o->removeVoucher());
    }

    public function testBackToShop()
    {
        oxRegistry::getSession()->setVariable('_backtoshop', 'asd');
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(2));
        $o = $this->getMock('Basket', array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame(null, $o->backToShop());
    }

    public function testBackToShopShowPage()
    {
        oxRegistry::getSession()->setVariable('_backtoshop', 'asd');
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock('Basket', array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame('asd', $o->backToShop());

        $this->assertSame(null, oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    public function testBackToShopShowPageNoPage()
    {
        oxRegistry::getSession()->setVariable('_backtoshop', '');
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock('Basket', array('getConfig'));
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
        $oProduct = $this->getMock("oxarticle", array("getId"));
        $oProduct->expects($this->once())->method("getId")->will($this->returnValue($articleId));

        $oDetails = $this->getMock("basket", array("getFirstBasketProduct"));
        $oDetails->expects($this->once())->method("getFirstBasketProduct")->will($this->returnValue($oProduct));
        $this->assertEquals($aArrayKeys, $oDetails->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of key from result of getFirstBasketProduct()");
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        $this->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');

        $oB = $this->getMock('basket', array('getSession'));
        $oB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oB->render();
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        $this->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oB = $this->getMock('basket', array('getSession'));
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
        $oBasket = new Basket();

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

        $oView = new Basket();
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

        $oView = new Basket();
        $this->assertEquals("getCardList", $oView->getCardList());
    }

    /**
     * Testing Wrapping::changeWrapping()
     *
     * @return null
     */
    public function testChangeWrapping()
    {
        $this->setRequestParam("wrapping", array(1 => 2));
        $this->setRequestParam("giftmessage", "testCardMessage");
        $this->setRequestParam("chosencard", "testCardId");

        $oBasketItem1 = $this->getMock("oxBasketItem", array("setWrapping"));
        $oBasketItem1->expects($this->once())->method('setWrapping')->with($this->equalTo(2));

        $oBasketItem2 = $this->getMock("oxBasketItem", array("setWrapping"));
        $oBasketItem2->expects($this->never())->method('setWrapping');

        $oContents = new oxList();
        $oContents->offsetSet(1, $oBasketItem1);
        $oContents->offsetSet(2, $oBasketItem2);

        $oBasket = $this->getMock("oxBasket", array("getContents", "setCardMessage", "setCardId", "onUpdate"));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue($oContents));
        $oBasket->expects($this->once())->method('setCardMessage')->with($this->equalTo("testCardMessage"));
        $oBasket->expects($this->once())->method('setCardId')->with($this->equalTo("testCardId"));
        $oBasket->expects($this->once())->method('onUpdate');

        $oSession = $this->getMock("oxSession", array("getBasket"));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oViewConfig = $this->getMock("oxViewConfig", array("getShowGiftWrapping"));
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock("basket", array("getViewConfig", "getSession"), array(), '', false);
        $oView->expects($this->once())->method('getViewConfig')->will($this->returnValue($oViewConfig));
        $oView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oView->changeWrapping();
    }

    /**
     * Test is Wrapping
     *
     * @return null
     */
    public function testIsWrapping()
    {
        $oView = new Basket();
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

        $oView = new Basket();
        $this->assertSame(false, $oView->isWrapping());
    }

}
