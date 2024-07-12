<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxTestModules;

class WrappingTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing Wrapping::render()
     */
    public function testRender()
    {
        $oView = oxNew('Wrapping');
        $this->assertSame('page/checkout/wrapping', $oView->render());
    }

    /**
     * Testing Wrapping::getBasketItems()
     */
    public function testGetBasketItems()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["getBasketArticles"]);
        $oBasket->expects($this->once())->method('getBasketArticles')->willReturn("getBasketArticles");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["getBasket"]);
        $session->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew(\OxidEsales\Eshop\Application\Controller\WrappingController::class);
        $this->assertSame("getBasketArticles", $oView->getBasketItems());
    }

    /**
     * Testing Wrapping::getWrappingList()
     */
    public function testGetWrappingList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getWrappingList"; }');

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getShowGiftWrapping"]);
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->willReturn(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, ["getViewConfig"], [], '', false);
        $oView->expects($this->once())->method('getViewConfig')->willReturn($oViewConfig);
        $this->assertSame("getWrappingList", $oView->getWrappingList());
    }

    /**
     * Testing Wrapping::getCardList()
     */
    public function testGetCardList()
    {
        oxTestModules::addFunction('oxwrapping', 'getWrappingList', '{ return "getCardList"; }');

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getShowGiftWrapping"]);
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->willReturn(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, ["getViewConfig"], [], '', false);
        $oView->expects($this->once())->method('getViewConfig')->willReturn($oViewConfig);
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
        $oBasketItem1->expects($this->atLeastOnce())->method('setWrapping')->with(2);

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

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["getBasket"]);
        $session->expects($this->atLeastOnce())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getShowGiftWrapping"]);
        $oViewConfig->expects($this->atLeastOnce())->method('getShowGiftWrapping')->willReturn(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WrappingController::class, ["getViewConfig"], [], '', false);
        $oView->expects($this->atLeastOnce())->method('getViewConfig')->willReturn($oViewConfig);
        $this->assertSame("order", $oView->changeWrapping());
    }
}
