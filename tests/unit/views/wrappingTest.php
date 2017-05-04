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

class Unit_Views_wrappingTest extends OxidTestCase
{

    /**
     * Testing Wrapping::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oView = new Wrapping();
        $this->assertEquals('page/checkout/wrapping.tpl', $oView->render());
    }

    /**
     * Testing Wrapping::getBasketItems()
     *
     * @return null
     */
    public function testGetBasketItems()
    {
        $oBasket = $this->getMock("oxBasket", array("getBasketArticles"));
        $oBasket->expects($this->once())->method('getBasketArticles')->will($this->returnValue("getBasketArticles"));

        $oSession = $this->getMock("oxSession", array("getBasket"));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oView = $this->getMock("Wrapping", array("getSession"), array(), '', false);
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

        $oViewConfig = $this->getMock("oxViewConfig", array("getShowGiftWrapping"));
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock("Wrapping", array("getViewConfig"), array(), '', false);
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

        $oViewConfig = $this->getMock("oxViewConfig", array("getShowGiftWrapping"));
        $oViewConfig->expects($this->once())->method('getShowGiftWrapping')->will($this->returnValue(true));

        $oView = $this->getMock("Wrapping", array("getViewConfig"), array(), '', false);
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
        modConfig::setRequestParameter("wrapping", array(1 => 2));
        modConfig::setRequestParameter("giftmessage", "testCardMessage");
        modConfig::setRequestParameter("chosencard", "testCardId");

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

        $oView = $this->getMock("Wrapping", array("getViewConfig", "getSession"), array(), '', false);
        $oView->expects($this->once())->method('getViewConfig')->will($this->returnValue($oViewConfig));
        $oView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $this->assertEquals("order", $oView->changeWrapping());
    }
}
