<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxTestModules;

class WrappingTest extends \OxidTestCase
{

    /**
     * Testing Wrapping::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oView = oxNew('Wrapping');
        $this->assertEquals('page/checkout/wrapping.tpl', $oView->render());
    }

    /**
     * Testing Wrapping::getBasketItems()
     *
     * @return null
     */
    public function testGetBasketItems()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("getBasketArticles"));
        $oBasket->expects($this->once())->method('getBasketArticles')->will($this->returnValue("getBasketArticles"));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("getBasket"));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, array("getSession"), array(), '', false);
        $oView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $this->assertEquals("getBasketArticles", $oView->getBasketItems());
    }

    /**
     * Testing Wrapping::getWrappingList()
     *
     * @return null
     */
    public function testGetWrappingList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getWrappingList"; }');

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, array("getShowGiftWrapping"));
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, array("getViewConfig"), array(), '', false);
        $oView->expects($this->once())->method('getViewConfig')->will($this->returnValue($oViewConfig));
        $this->assertEquals("getWrappingList", $oView->getWrappingList());
    }

    /**
     * Testing Wrapping::getCardList()
     *
     * @return null
     */
    public function testGetCardList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getCardList"; }');

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, array("getShowGiftWrapping"));
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, array("getViewConfig"), array(), '', false);
        $oView->expects($this->once())->method('getViewConfig')->will($this->returnValue($oViewConfig));
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
        $oBasketItem1->expects($this->atLeastOnce())->method('setWrapping')->with($this->equalTo(2));

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

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, array("getShowGiftWrapping"));
        $oViewConfig->expects($this->atLeastOnce())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, array("getViewConfig", "getSession"), array(), '', false);
        $oView->expects($this->atLeastOnce())->method('getViewConfig')->will($this->returnValue($oViewConfig));
        $oView->expects($this->atLeastOnce())->method('getSession')->will($this->returnValue($oSession));
        $this->assertEquals("order", $oView->changeWrapping());
    }
}
