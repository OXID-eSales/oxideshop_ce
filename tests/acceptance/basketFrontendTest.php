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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'oxidAdditionalSeleniumFunctions.php';

class Acceptance_basketFrontendTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Tests related creating of orders in frontend ----------------------------------

    /**
     * Basket flyout
     * @group navigation
     * @group order
     * @group basketfrontend
     */
    public function testFrontendBasketFlyout()
    {
        $this->openShop();
        $this->assertFalse($this->isElementPresent("basketFlyout"));
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->assertFalse($this->isVisible("basketFlyout"));
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("basketFlyout");
        $this->assertEquals("1 Items in Cart:", $this->getText("//div[@id='basketFlyout']/p[1]/strong"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//div[@id='basketFlyout']//li[1]//span"));
        $this->assertEquals("50,00 €", $this->getText("//div[@id='basketFlyout']//li[1]//strong"));
        $this->assertEquals("Total", $this->getText("//div[@id='basketFlyout']/p[2]/span"));
        $this->assertEquals("50,00 €", $this->getText("//div[@id='basketFlyout']/p[2]/strong"));
        $this->click("//div[@id='basketFlyout']//img");
        $this->waitForItemDisappear("basketFlyout");

        //adding few more products to basket
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->clickAndWait("//ul[@id='newItems']/li[2]//a");
        $this->clickAndWait("toBasket");
        $this->assertEquals("3", $this->getText("//div[@id='miniBasket']/span"));
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("basketFlyout");
        $this->assertEquals("3 Items in Cart:", $this->getText("//div[@id='basketFlyout']/p[1]/strong"));
        $this->assertEquals("2 x Test product 0 [EN] šÄßüл", $this->getText("//div[@id='basketFlyout']//li[1]//span"));
        $this->assertEquals("100,00 €", $this->getText("//div[@id='basketFlyout']//li[1]//strong"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//div[@id='basketFlyout']//li[2]//span"));
        $this->assertEquals("101,00 €", $this->getText("//div[@id='basketFlyout']//li[2]//strong"));
        $this->assertEquals("Total", $this->getText("//div[@id='basketFlyout']/p[2]/span"));
        $this->assertEquals("201,00 €", $this->getText("//div[@id='basketFlyout']/p[2]/strong"));

        $this->clickAndWait("//div[@id='basketFlyout']//li[1]/a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productTitle"));

        //user is not logged in
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("//div[@id='basketFlyout']//a[text()='Checkout']");
        $this->clickAndWait("//div[@id='basketFlyout']//a[text()='Checkout']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"), "mantis #2603");

        //checkout button leads to basket step3
        $this->clickAndWait("link=Home");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("//div[@id='basketFlyout']//a[text()='Checkout']");
        $this->clickAndWait("//div[@id='basketFlyout']//a[text()='Checkout']");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"), "mantis #2603");

    }

    /**
     * Frontend: product is sold out by other user during order process.
     * testing if no fatal errors or exceptions are thrown
     * @group order
     * @group basketfrontend
     */
    public function testFrontendOutOfStockOfflineProductDuringOrder()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("newItems_1"));
        if ( !isSUBSHOP ) {
            $this->assertTrue($this->isElementPresent("newItems_4"));
            $this->assertFalse($this->isElementPresent("newItems_5"));
        }
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//a[text()='Test product 0 [EN] šÄßüл']"));
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("Grand Total: 90,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")));
        //product is already in the basket. making product out of stock now
        $aArticleParams = array("oxstock" => 0, "oxstockflag" => 2);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        //in second step, now it is checked, if product is still available
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //in 3rd step, if continued, it will be redirected to home page
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));
        //checking if it is was redirected to home page
        //element is already offline
        $this->assertNotEquals("Test product 0 [EN] šÄßüл", $this->getText("newItems_1"));
        $this->assertFalse($this->isElementPresent("newItems_4"));
        $this->assertTrue($this->isTextPresent("Just arrived!"));

        //product is in stock again
        $aArticleParams = array("oxstock" => 2);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        $this->clickAndWait("link=Home");
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//a[text()='Test product 0 [EN] šÄßüл']"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //making product out of stock again
        $aArticleParams = array("oxstock" => 0);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        //in 4rd step it should be checked, if product is still in stock
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));
        $this->assertEquals("You are here: / Complete Order", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."), "missing translation for constant: PAGE_CHECKOUT_ORDER_BASKETEMPTY");
        //product is in stock again
        $aArticleParams = array("oxstock" => 2);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_1", "2");
        $this->clickAndWait("toBasket_searchList_1");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //making product out of stock again
        $aArticleParams = array("oxstock" => 0);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[2]/div"));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        //before order submit it should be checked, if product is still in stock
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."), "missing translation for constant: PAGE_CHECKOUT_ORDER_BASKETEMPTY");
    }


    /**
     * Frontend: product is sold out by other user during order process.
     * testing if no fatal errors or exceptions are thrown
     * @group order
     * @group basketfrontend
     */
    public function testFrontendOutOfStockNotOrderableProductDuringOrder()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("100");
        $this->clickAndWait("searchList_1");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("186,00 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        //product is already in the basket. making product out of stock now
        $aArticleParams = array("oxstock" => 0, "oxstockflag" => 3);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        //in second step, product availability is now checked.
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //in 3rd step it should be checked, if product is still in stock
        $this->assertTrue($this->isTextPresent("Product is not buyable"));
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));
        $this->assertEquals("Standard", $this->getSelectedLabel("sShipSet"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//td[2]/div"));
        $this->assertFalse($this->isElementPresent("cartItem_2"));
        $this->assertEquals("103,50 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));

        //product is in stock again
        $aArticleParams = array("oxstock" => 2);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->check("//tr[@id='cartItem_1']/td/input");
        $this->clickAndWait("basketRemove");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));

        //making product out of stock again
        $aArticleParams = array("oxstock" => 0);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        //in 4rd step it should be checked, if product is still in stock
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Product is not buyable"));
        $this->assertEquals("You are here: / Complete Order", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."), "missing translation for constant: PAGE_CHECKOUT_ORDER_BASKETEMPTY");
        //product is in stock again
        $aArticleParams = array("oxstock" => 2);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //making product out of stock again
        $aArticleParams = array("oxstock" => 0);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[2]/div"));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        //before order submit it should be checked, if product is still in stock
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertTrue($this->isTextPresent("Product is not buyable"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."), "missing translation for constant: PAGE_CHECKOUT_ORDER_BASKETEMPTY");
        $this->assertEquals("You are here: / Complete Order", $this->getText("breadCrumb"));
    }


    /**
     * Testing min order sum
     * @group navigation
     * @group user
     * @group order
     * @group basketfrontend
     */
    public function testFrontendMinOrderSum()
    {
        $this->executeSql("UPDATE `oxdelivery` SET `OXTITLE_1` = `OXTITLE` WHERE `OXTITLE_1` = '';");
        $this->openShop();

        //creating order
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();

        //min order sum is 49 €
        //when user is not logged in, default s&h are calculated and no discount applied. sum total is > 49 €
        $this->assertTrue($this->isElementPresent("//button[text()='Continue to Next Step']"));
        $this->assertFalse($this->isTextPresent("Minimum order price 49,00 €"));

        //when user logs in, discount is applied and sum total is < 49. order not allowed
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertFalse($this->isElementPresent("//button[text()='Continue to Next Step']"));
        $this->assertTrue($this->isTextPresent("Minimum order price 49,00"));

        //when buying 2 items, and amount is > 49 and order is allowed
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertFalse($this->isTextPresent("Minimum order price 49,00 €"));

        //voucher affects order min.sum calculation
        $this->type("voucherNr", "123123");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertTrue($this->isTextPresent("Minimum order price 49,00 €"));
        //removing voucher
        $this->clickAndWait("//div[@id='basketSummary']//a[text()='remove']");
        $this->assertFalse($this->isTextPresent("Minimum order price 49,00 €"));

        //checking if order step2 is loaded correctly
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isElementPresent("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->assertEquals("on", $this->getValue("showShipAddress"));
    }


    /**
     * Itm discount for products (special case according Mantis#320)
     * @group discount
     * @group basketfrontend
     */
    public function testFrontendItmDiscounts()
    {
        $this->executeSql("DELETE FROM `oxobject2discount` WHERE `OXDISCOUNTID` = 'testitmdiscount'");
        $this->executeSql("UPDATE `oxdiscount` SET `OXAMOUNT` = 1 WHERE `OXID` = 'testitmdiscount'");
        $this->executeSql("UPDATE `oxdiscount` SET `OXACTIVE` = 1 WHERE `OXPRICE` = 200 AND `OXPRICETO` = 999999");
        $this->openShop();
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        $this->type("am_1", "5");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isElementPresent("cartItem_1"));
        $this->assertTrue($this->isElementPresent("cartItem_2"));
        $this->assertFalse($this->isElementPresent("cartItem_3"));

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));
        $this->assertEquals("Art.No.: 1000", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("5", $this->getValue("am_1"));

        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div[1]"));
        $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_2']/td[5]"));
        $this->assertEquals("Discount 10% on 200 Euro or more", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]/th")), " Mantis #320. discount '10% ab 200 €o Einkaufswert' is active, but ignored");
        $this->assertEquals("-25,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "test for Mantis #320");
        $this->assertEquals("Grand Total: 225,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "test for Mantis #320");
    }

    /**
     * several itm discounts in basket
     * @group order
     * @group basketfrontend
     */
    public function testFewItmDiscounts()
    {
            $this->executeSql("INSERT INTO `oxdiscount` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXTITLE`, `OXTITLE_1`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXITMARTID`, `OXITMAMOUNT`) VALUES ('6a58b47', 'oxbaseshop', 1, 'test_discount_1', 'test_discount_1', 9999, 30, 'itm', '1003', 1);");
            $this->executeSql("INSERT INTO `oxdiscount` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXTITLE`, `OXTITLE_1`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXITMARTID`, `OXITMAMOUNT`) VALUES ('6282d39', 'oxbaseshop', 1, 'test_discount_2', 'test_discount_2', 9999, 30, 'itm', '1002-1', 1);");
        $this->openShop();
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->assertEquals("3", $this->getText("//div[@id='miniBasket']/span"));
        $this->openBasket();

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div[1]"));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_2']/td[5]"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]/div[1]"));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_3']/td[5]"));
        $this->assertEquals("Grand Total: 50,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));

        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div[1]"));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_2']/td[5]"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]/div[1]"));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_3']/td[5]"));
        $this->assertEquals("Grand Total: 100,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("2", $this->getValue("am_1"));
    }


    /**
     * Checking when prices are entered in NETTO
     * @group order
     * @group basketfrontend
     */
    public function testFrontendNettoPrices()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blDeliveryVatOnTop'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blWrappingVatOnTop'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blEnterNetPrice'");
        $this->clearTmp();
        $this->openShop();
        $this->assertEquals("52,50 € *", $this->clearString($this->getText("//ul[@id='newItems']/li[1]//span[@class='price']")));
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        $this->assertEquals("52,50 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->type("am_1", "3");
        $this->clickAndWait("basketUpdate");
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("//ul[@id='wrapp_1']/li[4]//label"));
        $this->assertEquals("Test card [EN] šÄßüл 0,24 €", $this->getText("//ul[@id='wrappCard']/li[4]//label"));
        $this->click("//ul[@id='wrapp_1']/li[4]/input");
        $this->click("//ul[@id='wrappCard']/li[4]//input");
        $this->clickAndWait("//button[text()='Apply']");
        $this->assertEquals("Gift Wrapping/Greeting Card 3,05 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "wrapping price is missing");
    }



    /**
     * Checking VAT displaying
     * @group order
     * @group basketfrontend
     */
    public function testFrontendVATOptions()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blShowVATForDelivery'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blShowVATForPayCharge'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blShowVATForWrapping'");

        $this->clearTmp();
        $this->openShop();
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();

        $this->type("am_1", "3");
        $this->clickAndWait("basketUpdate");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("3", $this->getValue("am_1"));
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->click("//ul[@id='wrapp_1']/li[4]//input");
        $this->clickAndWait("//button[text()='Apply']");
        $this->assertEquals("Total Products (gross): 150,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("Discount discount for category [EN] šÄßüл -15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (net): 128,57 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("plus VAT 5% Amount: 6,43 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Gift Wrapping/Greeting Card (net): 2,57 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")), "wrapping price is missing");
        $this->assertEquals("plus VAT 5% Amount: 0,13 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[7]")), "wrapping price is missing");
    }

    /**
     * Vats for products (category, product and personal product vat)
     * @group vat
     * @group basketfrontend
     */
    public function testFrontendVAT()
    {
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->clickAndWait("searchList_2");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();

        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("plus VAT 5% Amount: 2,38 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("plus VAT 10% Amount: 9,18 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("plus VAT 19% Amount: 11,97 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");

        //for austria vat is 0% without vatID checking
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->select("invadr[oxuser__oxcountryid]", "label=Austria");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("47,62 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("91,82 €", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("63,03 €", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));

        //for Belgium vat 0% only with valid VATID
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("2,38 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("9,18 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxustid]", "BE0876797054");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("47,62 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("91,82 €", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("63,03 €", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));

        //Germany
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        //vat is lover than before, becouse discount is applied for category products (1000, 1001) for Germany user
        $this->assertEquals("193,50 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("2,28 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("8,78 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("11,44 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
    }


    /**
     * testing option 'Product can be customized' from Administer products -> Extend tab
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendPersparam()
    {
        //enabling field
        $this->executeSql("UPDATE `oxarticles` SET `OXISCONFIGURABLE` = 1 WHERE `OXID` = '1000'");
        $this->openShop();
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->assertTrue($this->isElementPresent("persparam[details]"));
        $this->type("persistentParam", "test label šÄßüл");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]//a"));
        $this->assertEquals("test label šÄßüл", $this->getValue("//tr[@id='cartItem_1']/td[5]/p/input"));

        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[2]//a"));
        $this->assertEquals("Details: test label šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[2]/p")));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        //checking in Admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=12");
        $this->assertTrue($this->isTextPresent("details : test label šÄßüл"));
        //disabling field
        $this->executeSql("UPDATE `oxarticles` SET `OXISCONFIGURABLE` = 0 WHERE `OXID` = '1000'");
        $this->openShop();
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']/li//a");
        $this->assertFalse($this->isElementPresent("persparam[details]"));
    }


    /**
     * PersParam functionality
     * @group navigation
     * @group order
     * @group main
     * @group basketfrontend
     */
    public function testFrontendPersParamSaveBasket()
    {
        $this->executeSql("UPDATE `oxarticles` SET `OXISCONFIGURABLE` = 1 WHERE `OXID` = '1000'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='blPerfNoBasketSaving'");
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1001");
        $this->selectVariant("selectlistsselector_searchList_1", 1, "selvar2 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->selectVariant("productSelections", 1, "selvar4 [EN] šÄßüл +2%", "Test product 1 [EN]");
        $this->clickAndWait("toBasket");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->type("persparam[details]", "test");
        $this->clickAndWait("toBasket");
        $this->openBasket();

        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div"));
        $this->assertEquals("selvar2 [EN] šÄßüл", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_2']//span"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]/div"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[3]/div"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_3']/td[5]/p/input"));
        $this->assertEquals("test", $this->getValue("//tr[@id='cartItem_4']/td[5]/p/input"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar3 [EN] šÄßüл -2,00 €", "Grand Total");
        $this->type("am_3", "2");
        $this->type("//tr[@id='cartItem_4']/td[5]/p/input", "test1");
        $this->clickAndWait("basketUpdate");

        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_2']//span"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_3']/td[5]/p/input"));
        $this->assertEquals("2", $this->getValue("am_3"));
        $this->assertEquals("test1", $this->getValue("//tr[@id='cartItem_4']/td[5]/p/input"));
        $this->assertEquals("1", $this->getValue("am_4"));
        //checking if this basket was saved
        $this->clearTmp();
        $this->openShop();
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("5", $this->getText("//div[@id='miniBasket']/span"));
        $this->openBasket();
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_3']//span"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_4']//span"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_1']/td[5]/p/input"));
        $this->assertEquals("2", $this->getValue("am_1"));
        $this->assertEquals("test1", $this->getValue("//tr[@id='cartItem_2']/td[5]/p/input"));
        $this->assertEquals("1", $this->getValue("am_4"));
        $this->assertFalse($this->isElementPresent("cartItem_5"));

        //submitting order
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[2]/div"));
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_3']//span"));
        $this->assertFalse($this->isElementPresent("//div[@id='cartItemSelections_3']//ul"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[2]/div"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_4']//span"));
        $this->assertFalse($this->isElementPresent("//div[@id='cartItemSelections_4']//ul"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[2]/div"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[2]/div"));
        $this->assertFalse($this->isElementPresent("cartItem_5"));
        $this->assertEquals("334,50 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("Thank you", $this->getText("//h3"));
        $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));
    }


    /**
     * My Account navigation: Order history
     * Testing min order price
     * @group navigation
     * @group user
     * @group order
     * @group basketfrontend
     */
    public function testFrontendMyAccountOrdersHistory()
    {
        $this->executeSql("UPDATE `oxdelivery` SET `OXTITLE_1` = `OXTITLE` WHERE `OXTITLE_1` = '';");
        $this->openShop();

        //checking if its ok with no history
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']//a");
        $this->clickAndWait("//div[@id='sidebar']//a[text()='Order History']");
        $this->assertEquals("Order History", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Order History is empty"), "Text 'Order History is empty' is missing");
        //creating order
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Please select a state", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Hesse");
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->assertEquals("Hesse", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->type("deladr[oxaddress__oxfname]", "deliveryNamešÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "deliverySurnamešÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "deliveryStreetšÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "2");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "deliveryCityšÄßüл");
        $this->assertEquals("-", $this->getSelectedLabel("delCountrySelect"));
        $this->select("delCountrySelect", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_deladr[oxaddress__oxstateid]");
        $this->assertEquals("Please select a state", $this->getSelectedLabel("oxStateSelect_deladr[oxaddress__oxstateid]"));
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Lower Saxony");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
           $this->assertEquals("E-mail: birute_test@nfq.lt UserCompany šÄßüл User additional info šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл Musterstr.šÄßüл 1 HE 79098 Musterstadt šÄßüл Germany Phone: 0800 111111 Fax: 0800 111112 Celluar Phone: 0800 111114 Personal Phone: 0800 111113", $this->clearString($this->getText("//div[@id='orderAddress']/dl/dd[1]")));
        $this->assertEquals("Mr deliveryNamešÄßüл deliverySurnamešÄßüл deliveryStreetšÄßüл 2 NI 3000 deliveryCityšÄßüл Germany", $this->clearString($this->getText("//div[@id='orderAddress']/dl/dd[2]")));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("Thank you", $this->getText("//h3"));

        //orders history
        $this->clickAndWait("orderHistory");
        $this->assertEquals("Order History", $this->getText("//h1"));

        $this->assertEquals("Not yet shipped.", $this->getText("accOrderStatus_12"));
        $this->assertEquals("12", $this->getText("accOrderNo_12"));
        $this->assertEquals("deliveryNamešÄßüл deliverySurnamešÄßüл", $this->clearString($this->getText("accOrderName_12")));
        $this->assertEquals("Test product 0 [EN] šÄßüл - 2 qty.", $this->clearString($this->getText("accOrderAmount_12_1")));
        $this->clickAndWait("accOrderLink_12_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
    }


    /**
     * Order steps: Step1. checking navigation and other additional info
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep1Navigation()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar3 [EN] šÄßüл -2,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[4]//button");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->openBasket();
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));

        //Order Step1
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]//a"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]//a"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]//a"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[3]//a"));
        $this->assertEquals("Art.No.: 1000", $this->getText("//tr[@id='cartItem_4']/td[3]/div[2]"));
        $this->assertEquals("Art.No.: 1001", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("Art.No.: 1002-2", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
        $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_3']/td[3]/div[2]"));

        //testing navigation to details page
        $this->clickAndWait("//tr[@id='cartItem_4']/td[2]//img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->openBasket();
        $this->clickAndWait("//tr[@id='cartItem_2']/td[3]//a");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));
        $this->openBasket();

        //removing some products
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->clickAndWait("basketRemove");

        //navigation between order step1 and step2
        $this->clickAndWait("//div[@id='content']/div[1]//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->clickAndWait("link=1. Cart");
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->clickAndWait("//div[@id='content']/div[2]//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
    }


    /**
     * Vouchers is disabled via performance options
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendDisabledVouchers()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showVouchers'");
        $this->openShop();
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        $this->assertFalse($this->isElementPresent("voucherNr"));
        $this->assertFalse($this->isElementPresent("//button[text()='Submit Coupon']"));
        $this->assertFalse($this->isTextPresent("Enter Coupon Number"));
    }

    /**
     * Vouchers for specific products and categories
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendVouchersForSpecificCategoriesAndProducts()
    {
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->clickAndWait("searchList_2");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var1 [EN] šÄßüл", "Selected combination: var1 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->type("voucherNr", "test111");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertTrue($this->isTextPresent("Reason: The coupon is not valid for your user group!"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->type("voucherNr", "test111");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertEquals("Coupon (No. test111) remove", $this->getText("//div[@id='basketSummary']//tr[8]/th"));
        $this->type("voucherNr", "test222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertEquals("Coupon (No. test111) remove", $this->getText("//div[@id='basketSummary']//tr[8]/th"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
        $this->assertEquals("Coupon (No. test222) remove", $this->getText("//div[@id='basketSummary']//tr[9]/th"));
        $this->assertEquals("-9,00 €", $this->getText("//div[@id='basketSummary']//tr[9]/td"));
        $this->type("am_4", "3");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
        $this->assertEquals("-15,00 €", $this->getText("//div[@id='basketSummary']//tr[9]/td"));
        $this->check("//tr[@id='cartItem_1']/td[1]//input");
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->clickAndWait("basketRemove");
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
        $this->assertEquals("-3,00 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
    }

    /**
     * Order steps: Step1. Calculation and Vouchers
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep1CalculationVoucher()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar3 [EN] šÄßüл -2,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл ");
        $this->clickAndWait("toBasket");
        $this->searchFor("100");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_4", "6");
        $this->clickAndWait("toBasket_searchList_4");
        $this->clickAndWait("toBasket_searchList_1");
        $this->openBasket();

        //voucher
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertTrue($this->isTextPresent("Your Coupon “222222” couldn't be accepted."));
        $this->assertTrue($this->isTextPresent("The coupon is not valid for your user group!"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->type("voucherNr", "111111");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertEquals("Coupon (No. 111111) remove", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/th")));
        $this->assertEquals("-10,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/td")));
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertTrue($this->isTextPresent("Your Coupon “222222” couldn't be accepted."));
        $this->assertTrue($this->isTextPresent("Accumulation with coupons of other series is not allowed!"));
        $this->type("voucherNr", "111111");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertEquals("Coupon (No. 111111) remove", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/th")));
        $this->assertEquals("-10,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/td")));
        $this->assertEquals("Coupon (No. 111111) remove", $this->getText("//div[@id='basketSummary']//tr[9]/th"));
        $this->assertEquals("-10,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[9]/td")));
        $this->clickAndWait("//div[@id='basketSummary']//tr[8]/th/a");
        $this->assertEquals("Coupon (No. 111111) remove", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/th")));
        $this->assertNotEquals("Coupon (No. 111111) remove", $this->clearString($this->getText("//div[@id='basketSummary']//tr[9]/th")));
        $this->clickAndWait("//div[@id='basketSummary']//tr[8]/th/a");
        $this->assertNotEquals("Coupon (No. 111111) remove", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/th")));
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertEquals("Coupon (No. 222222) remove", $this->getText("//div[@id='basketSummary']//tr[8]/th"));
            $this->assertEquals("-26,12 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/td")));
        //removing few articles
        $this->check("//tr[@id='cartItem_2']/td[1]//input");
        $this->check("//tr[@id='cartItem_3']/td[1]//input");
        $this->clickAndWait("basketRemove");
        $this->assertEquals("-6,90 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]/td")));
    }

    /**
     * Order steps: Step1. Calculation
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep1Calculation()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar3 [EN] šÄßüл -2,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл ");
        $this->clickAndWait("toBasket");
        $this->searchFor("100");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_4", "6");
        $this->clickAndWait("toBasket_searchList_4");
        $this->clickAndWait("toBasket_searchList_1");
        $this->openBasket();

        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->type("voucherNr", "111111");
        $this->clickAndWait("//button[text()='Submit Coupon']");

        //testing product with selection list
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("98,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("98,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar2 [EN] šÄßüл", "You are here: / View cart");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("100,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("100,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar4 [EN] šÄßüл +2%", "You are here: / View cart");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("102,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("102,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar1 [EN] šÄßüл +1,00 €", "You are here: / View cart");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));

        //testing product with staffelpreis
            $this->assertEquals("6", $this->getValue("am_3"));
            $this->assertEquals("60,00 €", $this->getText("//tr[@id='cartItem_3']/td[6]"));
            $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
            $this->assertEquals("360,00 €", $this->getText("//tr[@id='cartItem_3']/td[8]"));
            $this->type("am_3", "1");
            $this->clickAndWait("basketUpdate");
            $this->assertEquals("75,00 €", $this->getText("//tr[@id='cartItem_3']/td[6]"));
            $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
            $this->assertEquals("75,00 €", $this->getText("//tr[@id='cartItem_3']/td[8]"));
            $this->type("am_3", "6");
            $this->clickAndWait("basketUpdate");

        //discounts
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-10,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]/td")));
        $this->assertTrue($this->isTextPresent("discount for product [EN] šÄßüл"));
        $this->assertEquals("Coupon (No. 111111) remove", $this->getText("//div[@id='basketSummary']//tr[8]/th"));
        $this->assertEquals("-10,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]/td")));
        $this->assertEquals("1,50 €", $this->getText("//div[@id='basketSummary']//tr[9]/td"));
            $this->assertEquals("-42,70 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
            $this->assertEquals("578,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
            $this->assertEquals("516,80 €", $this->getText("//div[@id='basketSummary']//tr[10]/td"));
            $this->assertEquals("444,21 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
            $this->assertEquals("8,19 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
            $this->assertEquals("60,78 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
            $this->assertEquals("2,12 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));

        $this->clickAndWait("//div[@id='basketSummary']//tr[8]/th/a");
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");

        //removing few articles
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->check("//tr[@id='cartItem_3']/td[1]//input");
        $this->clickAndWait("basketRemove");

        //basket calculation
        $this->assertEquals("168,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("136,53 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("8,46 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("9,86 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("-8,15 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]/td")));
        $this->assertEquals("1,50 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        $this->assertEquals("156,35 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
    }


    /**
     * PersParam functionality in Admin
     * @group navigation
     * @group order
     * @group basketfrontend
     */
    public function testFrontendPersParamOrder()
    {
        $this->executeSql("UPDATE `oxarticles` SET `OXISCONFIGURABLE` = 1 WHERE `OXID` = '1000'");
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->type("persparam[details]", "test");
        $this->clickAndWait("toBasket");

        $this->openBasket();
        $this->type("am_1", "3");
        $this->clickAndWait("basketUpdate");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("Thank you", $this->getText("//h3"));
        $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));

        //checking in admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=12", "save");
        $this->assertEquals("3 *", $this->getText("//table[2]/tbody/tr/td[1]"));
        $this->assertEquals("Test product 0 [EN]", $this->getText("//td[3]"));
        $this->assertEquals("150,00 EUR", $this->getText("//td[5]"));
        $this->assertEquals(", details : test", $this->getText("//td[6]"));
        $this->assertEquals("142,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Products", "//input[@value='Update']");
        $this->assertEquals("3", $this->getValue("//tr[@id='art.1']/td[1]/input"));
        $this->assertEquals(", details : test", $this->getText("//tr[@id='art.1']/td[5]"));
        $this->assertEquals("50,00 EUR", $this->getText("//tr[@id='art.1']/td[8]"));
        $this->assertEquals("150,00 EUR", $this->getText("//tr[@id='art.1']/td[9]"));
        $this->type("//tr[@id='art.1']/td[1]/input", "5");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals(", details : test", $this->getText("//tr[@id='art.1']/td[5]"));
        $this->assertEquals("50,00 EUR", $this->getText("//tr[@id='art.1']/td[8]"));
        $this->assertEquals("250,00 EUR", $this->getText("//tr[@id='art.1']/td[9]"));
        $this->assertEquals("232,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
    }

    /**
     * Orders: buying more items than available
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep1BuyingLimit()
    {
        $this->executeSql("UPDATE `oxarticles` SET `OXSTOCKFLAG` = 3 WHERE `OXID` LIKE '1002%'");
        $this->openShop();
        //adding products to the basket
        $this->searchFor("1002");
        $this->selectVariant("variantselector_searchList_1", 1, "var1 [EN] šÄßüл");
        $this->type("amountToBasket", "10");
        $this->clickAndWait("toBasket");
        $this->assertEquals("5", $this->getText("//div[@id='miniBasket']/span"));
        $this->openBasket();
        $this->assertFalse($this->isTextPresent("No enought items of this article in stock! Available: 5"));
        $this->assertEquals("5", $this->getValue("am_1"));
        $this->assertEquals("275,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->type("am_1", "10");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("5", $this->getValue("am_1"));
        $this->assertTrue($this->isTextPresent("Not enough items of this product in stock! Available: 5"));
        $this->assertEquals("275,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("5", $this->getValue("am_1"));
        $this->assertFalse($this->isTextPresent("Not enough items of this product in stock! Available: 5"));
        $this->assertEquals("275,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->type("am_1", "1");
        $this->clickAndWait("basketUpdate");
        $this->assertFalse($this->isTextPresent("Not enough items of this product in stock! Available: 5"));
        $this->assertEquals("1", $this->getValue("am_1"));
        $this->assertEquals("55,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
    }

    /**
     * Discounts for products (category, product and itm discounts)
     * @group discount
     * @group basketfrontend
     */
    public function testFrontendDiscounts()
    {
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->selectVariant("variantselector_searchList_3", 1, "var1 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertFalse($this->isTextPresent("discount"));
        $this->assertFalse($this->isElementPresent("cartItem_3"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertFalse($this->isElementPresent("cartItem_3"));
        $this->assertFalse($this->isTextPresent("discount for product [EN] šÄßüл"));

        $this->type("am_2", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertTrue($this->isTextPresent("discount for product [EN] šÄßüл"));
        $this->assertEquals("-11,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertFalse($this->isElementPresent("cartItem_3"));

        $this->type("am_1", "5");
        $this->clickAndWait("basketUpdate");

        $this->assertEquals("Test product 3 [EN] šÄßüл Art.No.: 1003", $this->clearString($this->getText("//tr[@id='cartItem_3']/td[3]")));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_3']/td[5]"));
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-25,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertTrue($this->isTextPresent("discount for product [EN] šÄßüл"));
        $this->assertEquals("-11,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("1,50 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        $this->assertEquals("360,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("297,48 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("10,71 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("15,81 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
        $this->assertEquals("325,50 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
        //test for #1822
        $aDiscountParams = array("oxactive" => 1 );
        $this->callShopSC("oxDiscount", "save", "testdiscount5", $aDiscountParams);
        $this->clickAndWait("link=1. Cart");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->assertFalse($this->isElementPresent("//a[text()='Logout']"));
        $this->check("//tr[@id='cartItem_2']/td[1]/input");
        $this->clickAndWait("basketRemove");
        $this->type("am_1", "1");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isTextPresent("1 EN test discount šÄßüл"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
    }

    /**
     * orders with fraction order quantities.
     * @group order
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrdersFractionQuantities()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");

        //ordering fraction quantities
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_1", "3.4");
        $this->clickAndWait("toBasket_searchList_1");
        $this->openBasket();
        $this->assertEquals("3.4", $this->getValue("am_1"));
        $this->assertEquals("170,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        //checking fraction quantities in admin
        //checking if product amount can be changed correctly
        if (!isSUBSHOP) { //for main shop
            $this->loginAdmin("Administer Products", "Products");
            $this->type("where[oxarticles][oxartnum]", "1000");
            $this->clickAndWait("submitit");
            $this->clickAndWaitFrame("link=1000", "edit");
            $this->openTab("link=Stock");
            $this->assertEquals("11.6", $this->getValue("editval[oxarticles__oxstock]"));
            $this->type("editval[oxarticles__oxstock]", "13.5");
            $this->clickAndWait("save");
            $this->assertEquals("13.5", $this->getValue("editval[oxarticles__oxstock]"));
        }

        //checking when disabled fraction quantity
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blAllowUnevenAmounts'");
        $this->openShop();
        $this->searchFor("100");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_1", "3.4");
        $this->clickAndWait("toBasket_searchList_1");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->type("amountToBasket", "0.3");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("linkNextArticle");
        $this->type("amountToBasket", "1.5");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("3", $this->getValue("am_1"));
        $this->assertEquals("2", $this->getValue("am_2"));
        //product 1001 was not added, because 0.3 is rounded to 0
        $this->assertFalse($this->isElementPresent("cartItem_3"));
    }

    /**
     * performing order when delivery country does not have any of payment methods
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderToOtherCountries()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("toBasket_searchList_1");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Please select your shipping method", $this->getText("//h3"));
        $this->assertTrue($this->isElementPresent("sShipSet"));
        $this->clickAndWait("link=2. Address");
        $this->select("invadr[oxuser__oxcountryid]", "label=Spain");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Payment Information", $this->getText("//h3"));
        $this->assertTrue($this->isTextPresent("Currently we have no shipping method set up for this country."));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Shipping Carrier modify", $this->getText($this->clearString("orderShipping")));
        $this->assertEquals("Type of Payment modify Empty", $this->getText($this->clearString("orderPayment")));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
    }



     /**
     * Order step 2
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep2Options()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл ");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        //Order Step1
        //checking if order via option 1 (without password) can be disabled
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //option 1 is available
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("optionNoRegistration"));
        $this->assertTrue($this->isElementPresent("optionRegistration"));
        $this->assertTrue($this->isElementPresent("optionLogin"));
        //checking on option 'Disable order without registration.'
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blOrderDisWithoutReg';");
        $this->clickAndWait("link=1. Cart");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //Order step2
        //option 1 is not available
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("//button[text()='Continue to Next Step']"));
        $this->assertFalse($this->isElementPresent("//h3[text()='Billing Address']"));
        $this->assertFalse($this->isElementPresent("//h3[text()='Shipping Address']"));
        $this->assertFalse($this->isElementPresent("optionNoRegistration"));
        $this->assertTrue($this->isElementPresent("optionRegistration"));
        $this->assertTrue($this->isElementPresent("optionLogin"));
        $this->type("//div[@id='optionLogin']//input[@name='lgn_usr']", "birute_test@nfq.lt");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='optionLogin']//button");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//button[text()='Continue to Next Step']"));
        $this->assertTrue($this->isTextPresent("Billing Address"));
        $this->assertTrue($this->isTextPresent("Shipping Address"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"));
    }


    /**
     * Order steps: Step2 and Step3
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep2And3()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл ");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //Order Step1
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //Order step2

        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent('Billing Address'));
        $this->assertEquals("Mr", $this->getSelectedLabel("invadr[oxuser__oxsal]"));
        $this->assertEquals("UserNamešÄßüл", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("UserSurnamešÄßüл", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("UserCompany šÄßüл", $this->getValue("invadr[oxuser__oxcompany]"));
        $this->assertEquals("Musterstr.šÄßüл", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("79098", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("Musterstadt šÄßüл", $this->getValue("invadr[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("invadr[oxuser__oxustid]"));
        $this->assertEquals("User additional info šÄßüл", $this->getValue("invadr[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("0800 111111", $this->getValue("invadr[oxuser__oxfon]"));
        $this->assertEquals("0800 111112", $this->getValue("invadr[oxuser__oxfax]"));
        $this->assertEquals("0800 111114", $this->getValue("invadr[oxuser__oxmobfon]"));
        $this->assertEquals("0800 111113", $this->getValue("invadr[oxuser__oxprivfon]"));
        $this->assertEquals("01", $this->getValue("invadr[oxuser__oxbirthdate][day]"));
        $this->assertEquals("01", $this->getValue("invadr[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1980", $this->getValue("invadr[oxuser__oxbirthdate][year]"));
        $this->assertEquals("off", $this->getValue("subscribeNewsletter"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=2. Address");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //Order Step3
        $this->assertEquals("Please select your shipping method", $this->getText("deliveryHeader"));
        $this->assertEquals("Payment Method", $this->getText("paymentHeader"));
        $this->assertEquals("Charges: 1,50 €", $this->getText("shipSetCost"));
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getSelectedLabel("sShipSet"));
        $this->assertFalse($this->isVisible("testpayment_1"));
        $this->assertEquals("off", $this->getValue("payment_testpayment"));
        $this->assertTrue($this->isElementPresent("payment_oxidcashondel"));
        $this->assertTrue($this->isElementPresent("payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("payment_oxiddebitnote"));

        $this->selectAndWait("sShipSet", "label=Standard");
        $this->assertFalse($this->isElementPresent("shipSetCost"));
        $this->assertTrue($this->isElementPresent("payment_oxidpayadvance"));
        $this->assertTrue($this->isElementPresent("payment_oxiddebitnote"));
        $this->assertFalse($this->isElementPresent("payment_testpayment"));
        $this->select("sShipSet", "label=Test S&H set [EN] šÄßüл");
        $this->waitForItemAppear("shipSetCost");
        $this->assertEquals("Charges: 1,50 €", $this->getText("shipSetCost"));
        $this->click("payment_testpayment");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Complete Order", $this->getText("breadCrumb"));
        $this->clickAndWait("link=3. Pay");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"));
    }

   /**
     * Order steps (without any special checking for discounts, various VATs and user registration)
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep4()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //Order Step1
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");

        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->click("//ul[@id='wrapp_1']/li[4]/input");
        $this->click("//ul[@id='wrappCard']/li[4]//input");
        $this->type("//textarea", "Greeting card text");
        $this->clickAndWait("//button[text()='Apply']");
        $this->assertTrue($this->isTextPresent("Greeting card text"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isVisible("orderRemark"));
        $this->type("orderRemark", "remark text");
        $this->assertTrue($this->isVisible("subscribeNewsletter"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]")));
        $this->assertTrue($this->isTextPresent("Greeting card text"));
        $this->assertTrue($this->isTextPresent("What I wanted to say ...: remark text"));

        //link to billing and address
        $this->assertEquals("E-mail: birute_test@nfq.lt UserCompany šÄßüл User additional info šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл Musterstr.šÄßüл 1 79098 Musterstadt šÄßüл Germany Phone: 0800 111111 Fax: 0800 111112 Celluar Phone: 0800 111114 Personal Phone: 0800 111113", $this->clearString($this->getText("//div[@id='orderAddress']//dl/dd")));
        //$this->assertEquals("What I wanted to say ...: Here you can enter an optional message.", $this->clearString($this->getText("//div[@id='orderAddress']/div")));
        $this->assertFalse($this->isTextPresent("Here you can enter an optional message."));
        $this->clickAndWait("//div[@id='orderAddress']//button");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->type("order_remark", "my message");
        $this->assertEquals("on", $this->getValue("showShipAddress"));
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->assertEquals($this->getSelectedLabel("addressId"), "New Address");
        $this->checkForErrors();
        $this->type("deladr[oxaddress__oxfname]", "first");
        $this->type("deladr[oxaddress__oxlname]", "last");
        $this->type("deladr[oxaddress__oxcompany]", "company");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "1");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("E-mail: birute_test@nfq.lt UserCompany šÄßüл User additional info šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл Musterstr.šÄßüл 1 79098 Musterstadt šÄßüл Germany Phone: 0800 111111 Fax: 0800 111112 Celluar Phone: 0800 111114 Personal Phone: 0800 111113", $this->clearString($this->getText("//div[@id='orderAddress']//dl/dd")));
        $this->assertEquals("company Mr first last street 1 3000 city Germany", $this->clearString($this->getText("//div[@id='orderAddress']//dl/dd[2]")));
        $this->assertEquals("What I wanted to say ...: my message", $this->clearString($this->getText("//div[@id='orderAddress']/div")));

        //link to payment method
        $this->assertEquals("Shipping Carrier modify Standard", $this->clearString($this->getText("orderShipping")));
        $this->assertEquals("Type of Payment modify COD (Cash on Delivery)", $this->clearString($this->getText("orderPayment")));
        $this->clickAndWait("//div[@id='orderShipping']//button");
        $this->select("sShipSet", "label=Standard");
        $this->click("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Shipping Carrier modify Standard", $this->clearString($this->getText("orderShipping")));
        $this->assertEquals("Type of Payment modify Cash in advance", $this->clearString($this->getText("orderPayment")));
        $this->clickAndWait("//div[@id='orderPayment']//button");
        $this->select("sShipSet", "label=Standard");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Shipping Carrier modify Standard", $this->clearString($this->getText("orderShipping")));
        $this->assertEquals("Type of Payment modify COD (Cash on Delivery)", $this->clearString($this->getText("orderPayment")));
        //testing displayed information
        $this->assertEquals("168,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("Discount discount for category [EN] šÄßüл", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]/th")));
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("136,53 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("8,46 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("9,86 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("Coupon (No. 222222)", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]/th")));
        $this->assertEquals("-8,15 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        $this->assertEquals("7,50 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
        $this->assertEquals("1,10 €", $this->getText("//div[@id='basketSummary']//tr[9]/td"));
        $this->assertEquals("163,45 €", $this->getText("//div[@id='basketSummary']//tr[10]/td"));
    }

    /**
     * Order steps (without any special checking for discounts, various VATs and user registration)
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderStep4and5()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //Order Step4
        //rights of withdrawal
        $this->assertTrue($this->isElementPresent("//form[@id='orderConfirmAgbTop']//a[text()='Terms and Conditions']"));
        $this->assertTrue($this->isElementPresent("//form[@id='orderConfirmAgbTop']//a[text()='Right of Withdrawal']"));
        //testing links to products
        $this->clickAndWait("//tr[@id='cartItem_1']/td/a");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->clickAndWait("//tr[@id='cartItem_2']/td[2]//a");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //submit without checkbox
        $this->click("//form[@id='orderConfirmAgbTop']//button");
        $this->waitForText("Please read and confirm our terms and conditions.");
        //successful submit
        $this->click("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));
        //testing info in 5th page
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
        $this->assertTrue($this->isElementPresent("backToShop"));
        $this->assertEquals("back to Startpage", $this->getText("backToShop"));
        $this->clickAndWait("orderHistory");
        $this->assertEquals("You are here: / My Account / Order History", $this->getText("breadCrumb"));
        $this->assertEquals("Order History", $this->getText("//h1"));
        $this->assertEquals("Test product 1 [EN] šÄßüл test selection list [EN] šÄßüл : selvar1 [EN] šÄßüл +1,00 € - 1 qty.", $this->clearString($this->getText("//tr[@id='accOrderAmount_12_1']/td")));
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл - 1 qty.", $this->clearString($this->getText("//tr[@id='accOrderAmount_12_2']/td")));
    }

    /**
     * Checking Performance options
     * option: Load "Customers who bought this product also purchased..."
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendPerfOptionsAlsoBought()
    {
        $this->openShop();
        //creating order
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkPrevArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->check("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");

        //Load "Customers who bought this product also purchased..."  is ON
        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->clickAndWait("searchList_1");
        $this->assertTrue($this->isElementPresent("//h3[text()='Customer who bought this product also bought:']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='alsoBought']/li[1]//img"));
        //fix it in future: mouseOver effect is implemented via css. Selenium does not support it yet.
        //$this->mouseOverAndClick("//ul[@id='alsoBought']/li[1]", "//ul[@id='alsoBought']/li[1]//a");
        $this->clickAndWait("//ul[@id='alsoBought']/li[1]//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->check("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertEquals("Customers who bought this products, also bought:", $this->clearString($this->getText("//h1")));
        $this->assertTrue($this->isElementPresent("//ul[@id='alsoBoughtThankyou']/li[1]"));
        //fix it in future: mouseOver effect is not working after latest jQuery update. use mouse over when working solution will be find
        //$this->mouseOverAndClick("//ul[@id='alsoBoughtThankyou']/li[1]", "//ul[@id='alsoBoughtThankyou']/li[1]//a");
        $this->clickAndWait("//ul[@id='alsoBoughtThankyou']/li[1]//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));

        //turning Load "Customers who bought this product also purchased..." OFF
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadCustomerWhoBoughtThis'");
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "Line");
        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertFalse($this->isElementPresent("//h3[text()='Customer who bought this product also bought:']"));
        $this->assertFalse($this->isElementPresent("alsoBought"));
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->check("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertFalse($this->isElementPresent("//h1[text()='Customer who bought this product also bought:']"));
        $this->assertFalse($this->isElementPresent("alsoBoughtThankyou"));
    }

    /**
     * Testing giftWrapping selection.
     * @group order
     * @group user
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendOrderGiftWrapping()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("1001");
        $this->selectVariant("selectlistsselector_searchList_1", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        //both wrapping and greeting cart exist
        $this->assertEquals("add", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[4]")));
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("//ul[@id='wrapp_1']/li[4]//label"));
        $this->assertEquals("Test card [EN] šÄßüл 0,20 €", $this->getText("//ul[@id='wrappCard']/li[4]//label"));
        $this->assertTrue($this->isElementPresent("giftmessage"));
        $this->clickAndWait("//button[text()='Apply']");

        //only giftWrapping exist (none of greeging cards)
        $this->executeSql("DELETE FROM `oxwrapping` WHERE `OXTYPE` = 'CARD'");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("//ul[@id='wrapp_1']/li[4]//label"));
        $this->assertFalse($this->isElementPresent("wrappCard"));
        $this->assertFalse($this->isElementPresent("giftmessage"));
        $this->clickAndWait("//button[text()='Apply']");

        //also removing wrapping. gift wrapping selection now is not accessible
        $this->executeSql("DELETE FROM `oxwrapping` WHERE `OXTYPE` = 'WRAP'");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertFalse($this->isElementPresent("//tr[@id='cartItem_1']/td[4]/a"));
    }

    /**
     * Gift wrapping is disabled via performance options
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendDisabledGiftWrapping()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showGiftWrapping'");
        $this->openShop();
        $this->searchFor("1001");
        $this->selectVariant("selectlistsselector_searchList_1", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[4]")));
        $this->assertFalse($this->isElementPresent("//tr[@id='cartItem_1']/td[4]/a"));
    }



    /**
     * Checking VAT functionality, when it is calculated for Billing country
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendVatForBillingCountry()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("193,50 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("2,28 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("8,78 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("11,44 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));


        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Germany", $this->getSelectedLabel("invCountrySelect"));
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Switzerland");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("2,38 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("9,18 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));

        $this->clickAndWait("link=2. Address");
        //changing country for billing address
        $this->select("invCountrySelect", "label=Switzerland");
        //changing country for delivery address
        $this->select("delCountrySelect", "label=Germany");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Baden-Wurttemberg");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("192,47 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
    }

    /**
     * Checking VAT functionality, when it is calculated for Shipping country
     * @group navigation
     * @group basketfrontend
     */
    public function testFrontendVatForShippingCountry()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x07 WHERE `OXVARNAME`='blShippingCountryVat';");
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();

        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("193,50 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("2,28 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("8,78 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("11,44 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //no shipping address
        $this->check("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("193,50 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("2,28 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("8,78 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("11,44 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));

        $this->clickAndWait("link=2. Address");
        //billing germany, shipping Switzerland
        $this->assertEquals("Germany", $this->getSelectedLabel("invCountrySelect"));
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Switzerland");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->check("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertFalse($this->isTextPresent("-10,00"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->clickAndWait("link=2. Address");
        //billing switzerland, shipping germany
        $this->select("invCountrySelect", "label=Switzerland");
        $this->select("delCountrySelect", "label=Germany");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Baden-Wurttemberg");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("193,50 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("2,28 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->assertEquals("8,78 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("11,44 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        $this->assertEquals("216,00 €", $this->getText("//div[@id='basketSummary']//tr[8]/td"));
    }

/**
     * checking on weight depending delivery costs
     * @group admin
     * @group order
     * @group basketfrontend
     */
    public function testDeliveryByWeight()
    {
        //calculating delivery for every product in basket
        $this->executeSql( "UPDATE `oxarticles` SET `OXACTIVE` = 1 WHERE `OXID` = '10011'" );
        $this->executeSql( "UPDATE `oxarticles` SET `OXACTIVE` = 1 WHERE `OXID` = '10012'" );
        $this->executeSql( "UPDATE `oxarticles` SET `OXACTIVE` = 1 WHERE `OXID` = '10013'" );
        $this->executeSql( "UPDATE `oxdeliveryset` SET `OXACTIVE` = 1 WHERE `OXID` = 'testshset7'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXACTIVE` = 1 WHERE `OXID` = 'testsh1'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXACTIVE` = 1 WHERE `OXID` = 'testsh2'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXACTIVE` = 1 WHERE `OXID` = 'testsh5'" );

        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']/li[2]//button");
        $this->clickAndWait("//ul[@id='searchList']/li[3]//button");
        $this->clickAndWait("//ul[@id='searchList']/li[4]//button");
        $this->assertEquals("3", $this->getText("//div[@id='miniBasket']/span"));

        $this->openBasket();
        $this->assertEquals("12,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->type("am_2", "3"); //product 10012
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("14,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->check("//tr[@id='cartItem_3']/td/input"); //product 10013
        $this->clickAndWait("basketRemove");
        $this->assertEquals("4,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        $this->type("am_2", "1"); //product 10012
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("2,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        //delivery once a cart
        $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 0 WHERE `OXID` = 'testsh1'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 0 WHERE `OXID` = 'testsh2'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 0 WHERE `OXID` = 'testsh5'" );
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("5,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
        //delivery once every product
        $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 1 WHERE `OXID` = 'testsh1'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 1 WHERE `OXID` = 'testsh2'" );
        $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 1 WHERE `OXID` = 'testsh5'" );
        $this->type("am_1", "2");
        $this->type("am_2", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("6,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
    }


    /**
    * Order steps changing Shipping Addresses in Account/Address processing checkout will change shipping address at order too
    * @group order
    * @group user
    * @group navigation
    * @group basketfrontend
    */
    public function testFrontendOrderStep4ChangedAddress()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Germany");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Order Step4
        $this->openWindow(shopURL."en/my-address/", "222");
        $this->selectWindow("222");
        $this->waitForText("Shipping addresses");
        $this->click("userChangeShippingAddress");
        $this->waitForItemAppear("delCountrySelect");
        $this->select("delCountrySelect", "label=Luxembourg");
        $this->clickAndWait("accUserSaveTop");
        $this->close();
        $this->selectWindow(null);
        // submit
        $this->click("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        //delivery country was changed and we are redirected to payment step
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertFalse($this->isTextPresent("Billing or shipping address have been changed during checkout. Please check again."));
        //changing billing address once more
        $this->openWindow(shopURL."en/my-address/", "222");
        $this->selectWindow("222");
        $this->waitForText("Shipping addresses");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invCountrySelect");
        $this->select("invCountrySelect", "label=Austria");
        $this->clickAndWait("accUserSaveTop");
        $this->close();
        $this->selectWindow(null);

        // submit
        $this->click("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");

        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
         //billing address was changed, so messega was displayed:" Billing or shipping address have been changed during checkout. Please check again."
        //$this->assertTrue($this->isTextPresent("Billing or shipping address have been changed during checkout. Please check again."));
            // submit
        $this->click("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
        $this->assertTrue($this->isElementPresent("backToShop"));
        $this->assertEquals("back to Startpage", $this->getText("backToShop"));
            }


}