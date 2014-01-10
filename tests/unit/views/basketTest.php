<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

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
        $this->assertEquals( 'basket', $oBasket->getErrorDestination() );
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testgetSimilarRecommListsIfOff()
    {
        $oCfg = $this->getMock( "stdClass", array( "getShowListmania" ) );
        $oCfg->expects( $this->once() )->method( 'getShowListmania')->will($this->returnValue( false ) );

        $oBasket = $this->getMock( "basket", array( "getViewConfig", 'getArticleList' ) );
        $oBasket->expects( $this->once() )->method( 'getViewConfig')->will($this->returnValue( $oCfg ) );
        $oBasket->expects( $this->never() )->method( 'getArticleList');

        $this->assertSame(false, $oBasket->getSimilarRecommLists());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     *
     * @return null
     */
    public function testAddVoucherChecksGetShowVouchers()
    {
        $oCfg = $this->getMock( "stdClass", array( "getShowVouchers" ) );
        $oCfg->expects( $this->once() )->method( 'getShowVouchers')->will($this->returnValue( false ) );

        $oBasket = $this->getMock( "basket", array( "getViewConfig", 'getSession' ) );
        $oBasket->expects( $this->once() )->method( 'getViewConfig')->will($this->returnValue( $oCfg ) );
        $oBasket->expects( $this->never() )->method( 'getSession');

        $this->assertSame(null, $oBasket->addVoucher());
    }

    /**
     * Test oxViewConfig::getShowVouchers() affection
     *
     * @return null
     */
    public function testRemoveVoucherChecksGetShowVouchers()
    {
        $oCfg = $this->getMock( "stdClass", array( "getShowVouchers" ) );
        $oCfg->expects( $this->once() )->method( 'getShowVouchers')->will($this->returnValue( false ) );

        $oBasket = $this->getMock( "basket", array( "getViewConfig", 'getSession' ) );
        $oBasket->expects( $this->once() )->method( 'getViewConfig')->will($this->returnValue( $oCfg ) );
        $oBasket->expects( $this->never() )->method( 'getSession');

        $this->assertSame(null, $oBasket->removeVoucher());
    }

    /**
     * test render
     *
     * @return null
     */
    public function testRenderNoSE()
    {
        oxTestModules::addFunction('oxUtils', 'isSearchEngine($sClient = NULL)', '{return false;}');
        $oBasket = new basket();

        $this->assertEquals('page/checkout/basket.tpl', $oBasket->render());
    }

    /**
     * test render
     *
     * @return null
     */
    public function testRenderSE()
    {
        oxTestModules::addFunction('oxUtils', 'isSearchEngine($sClient = NULL)', '{return true;}');
        $oBasket = $this->getMock( "basket", array( "getBasketArticles", 'getBasketSimilarList', 'getSimilarRecommLists', 'showBackToShop' ) );
        $oBasket->expects( $this->never() )->method( 'getBasketArticles')->will($this->returnValue( 'getBasketArticles' ) );
        $oBasket->expects( $this->never() )->method( 'getBasketSimilarList')->will($this->returnValue( 'getBasketSimilarList' ) );
        $oBasket->expects( $this->never() )->method( 'getSimilarRecommLists')->will($this->returnValue( 'getSimilarRecommLists' ) );
        $oBasket->expects( $this->never() )->method( 'showBackToShop')->will($this->returnValue( 'showBackToShop' ) );

        $this->assertEquals('content.tpl', $oBasket->render());
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
        oxSession::setVar( '_backtoshop', 1 );
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

        modConfig::setParameter( 'voucherNr', 'vouchnr' );
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

        modConfig::setParameter( 'voucherId', 'vouchnr' );
        $this->assertEquals(null, $o->removeVoucher());
    }

    public function testBackToShop()
    {
        oxSession::setVar( '_backtoshop', 'asd' );
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(2));
        $o = $this->getMock('Basket', array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame(null, $o->backToShop()  );
    }

    public function testBackToShopShowPage()
    {
        oxSession::setVar( '_backtoshop', 'asd' );
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock('Basket', array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame('asd', $o->backToShop()  );

        $this->assertSame(null, oxSession::getVar( '_backtoshop')  );
    }

    public function testBackToShopShowPageNoPage()
    {
        oxSession::setVar( '_backtoshop', '' );
        $oConf = $this->getMock('stdclass', array('getConfigParam'));
        $oConf->expects($this->exactly(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));
        $o = $this->getMock('Basket', array('getConfig'));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConf));
        $this->assertSame(null, $o->backToShop()  );

        $this->assertSame('', oxSession::getVar( '_backtoshop')  );
    }

    public function testGetSimilarRecommLists()
    {
        $oP = $this->getMock('stdclass', array('getId'));
        $oP->expects($this->once())->method('getId')->will($this->returnValue('fds'));
        $oVC = $this->getMock('stdclass', array('getShowListmania'));
        $oVC->expects($this->once())->method('getShowListmania')->will($this->returnValue(true));
        $o = $this->getMock('Basket', array('getFirstBasketProduct', 'getViewConfig'));
        $o->expects($this->once())->method('getFirstBasketProduct')->will($this->returnValue($oP));
        $o->expects($this->once())->method('getViewConfig')->will($this->returnValue($oVC));

        $oRecommList = $this->getMock('stdclass', array('getRecommListsByIds'));
        $oRecommList->expects($this->once())->method('getRecommListsByIds')->with($this->equalTo(array('fds')))->will($this->returnValue('asdads'));
        $oUtilsObj = $this->getMock('oxUtilsObject', array('oxNew'));
        $oUtilsObj->expects($this->once())->method('oxNew')->with($this->equalTo('oxrecommlist'))->will($this->returnValue($oRecommList));
        modInstances::addMod('oxUtilsObject', $oUtilsObj);

        $this->assertEquals('asdads', $o->getSimilarRecommLists());
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');

        $oB = $this->getMock('basket', array('getSession'));
        $oB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oB->render();
    }
    public function testRenderDoesCleanReservationsIfOn()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', true);

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
     * Testing Wrapping::changeWrapping()
     *
     * @return null
     */
    public function testChangeWrapping()
    {
        modConfig::setParameter( "wrapping", array( 1 => 2 ) );
        modConfig::setParameter( "giftmessage", "testCardMessage" );
        modConfig::setParameter( "chosencard", "testCardId" );

        $oBasketItem1 = $this->getMock( "oxStdClass", array( "setWrapping" ));
        $oBasketItem1->expects( $this->once() )->method( 'setWrapping' )->with( $this->equalTo( 2 ) );

        $oBasketItem2 = $this->getMock( "oxStdClass", array( "setWrapping" ));
        $oBasketItem2->expects( $this->never() )->method( 'setWrapping' );

        $oContents = new oxList();
        $oContents->offsetSet( 1 , $oBasketItem1 );
        $oContents->offsetSet( 2 , $oBasketItem2 );

        $oBasket = $this->getMock( "oxStdClass", array( "getContents", "setCardMessage", "setCardId", "onUpdate" ) );
        $oBasket->expects( $this->once() )->method( 'getContents' )->will( $this->returnValue( $oContents ) );
        $oBasket->expects( $this->once() )->method( 'setCardMessage' )->with( $this->equalTo( "testCardMessage" ) );
        $oBasket->expects( $this->once() )->method( 'setCardId' )->with( $this->equalTo( "testCardId" ) );
        $oBasket->expects( $this->once() )->method( 'onUpdate' );

        $oSession = $this->getMock( "oxStdClass", array( "getBasket" ) );
        $oSession->expects( $this->once() )->method( 'getBasket' )->will( $this->returnValue( $oBasket ) );

        $oViewConfig = $this->getMock( "oxStdClass", array( "getShowGiftWrapping" ) );
        $oViewConfig->expects( $this->once() )->method( 'getShowGiftWrapping' )->will( $this->returnValue( true ) );

        $oView = $this->getMock( "basket", array( "getViewConfig", "getSession" ), array(), '', false );
        $oView->expects( $this->once() )->method( 'getViewConfig' )->will( $this->returnValue( $oViewConfig ) );
        $oView->expects( $this->once() )->method( 'getSession' )->will( $this->returnValue( $oSession ) );
        $oView->changeWrapping();
    }
}
