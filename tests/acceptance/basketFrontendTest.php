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
     * @gruop basketfrontend
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
     * @gruop basketfrontend
     */
    public function testFrontendOutOfStockOfflineProductDuringOrder()
    {
        $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
        $this->markTestSkipped("fix test after bug 0004596 fix");

        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("newItems_1"));
        if ( !isSUBSHOP ) {
            $this->assertTrue($this->isElementPresent("newItems_4"));
            $this->assertFalse($this->isElementPresent("newItems_5"));
        }
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']"));
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("90,00 €", $this->getText("basketGrandTotal"),"Garnd total price chenged or did't displayed");

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
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //making product out of stock again
        $aArticleParams = array("oxstock" => 0);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

        //in 4rd step it should be checked, if product is still in stock
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));


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
     */
    public function testFrontendOutOfStockNotOrderableProductDuringOrder()
    {
        $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
        $this->markTestSkipped("fix test after bug 0004596 fix");

        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("100");
        $this->clickAndWait("searchList_1");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("186,00 €", $this->getText("basketGrandTotal"),"Garnd total price chenged or did't displayed");

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
        $this->assertEquals("103,50 €", $this->getText("basketGrandTotal"),"Garnd total price chenged or did't displayed");

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
     * @gruop basketfrontend
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
     * Checking VAT displaying for all additional products in 1st order step
     * @group order
     * @gruop basketfrontend
     */
    public function testFrontendVATOptions()
    {
        //enabling config (Display shipping costs as net price and VAT (instead of gross) in shopping cart and invoice)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blShowVATForDelivery" => array("type" => "bool", "value" => "true")));

        //enabling config (Display VAT contained in Payment Method Charges in Shopping Cart and Invoice)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blShowVATForPayCharge" => array("type" => "bool", "value" => "true")));

        //enabling config (Display VAT contained in Gift Wrappings and Greeting Cards in Shopping Cart and Invoice)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blShowVATForWrapping" => array("type" => "bool", "value" => "true")));

        $this->openShop();
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
        $this->openBasket();
        //changed product quantities to 3
        $this->type("am_1", "3");
        $this->clickAndWait("basketUpdate");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("3", $this->getValue("am_1"));
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->click("//ul[@id='wrapp_1']/li[4]//input");
        $this->clickAndWait("//button[text()='Apply']");

        //in 1st order step check order information
        $this->assertTrue($this->isTextPresent("Total Products (net)"),"info about net total not displayes in cart");
        $this->assertTrue($this->isTextPresent("plus VAT 5% Amount:"),"info about product VAT not displayes in cart");
        $this->assertTrue($this->isTextPresent("Total Products (gross)"),"info about bruto total not displayes in cart");
        $this->assertTrue($this->isTextPresent("Shipping cost"),"info about shippig not displayes in cart");
        $this->assertTrue($this->isTextPresent("Gift Wrapping (net):"),"info about wrapping total not displayes in cart");
        $this->assertTrue($this->isTextPresent("plus VAT:"),"info about gift wraping vat not displayes in cart");
        $this->assertTrue($this->isTextPresent("Grand Total:"),"info about grand total not displayes in cart");

        $this->assertEquals("128,57 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("6,43 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 5% changed ");
        $this->assertEquals("135,00 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("2,57 €", $this->getText("basketWrappingNetto"), "Wraping price changed  or did't displayed");
        $this->assertEquals("0,13 €", $this->getText("basketWrappingVat"),"Wraping price changed  or did't displayed");
        $this->assertEquals("137,70 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");
    }

    /**
     * Vats for products (category, product and personal product vat)
     * @group vat
     * @gruop basketfrontend
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

        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 0% changed ");
        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("6,90 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("209,37 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");


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
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 0% changed ");

        //Germany
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        //vat is lower than before, becouse discount is applied for category products (1000, 1001) for Germany user
        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
    }


    /**
     * PersParam functionality in frontend
     * PersParam functionality in admin
     * testing option 'Product can be customized' from Administer products -> Extend tab
     * @group navigation
     * @group order
     * @group main
     * @gruop basketfrontend
     */
    public function testFrontendPersParamSaveBasket()
    {
       // active option (Product can be customized) where product ID is `OXID` = '1000
        $aArticleParams = array("oxisconfigurable" => 1);
        $this->callShopSC("oxArticle", "save", "1000", $aArticleParams);

       // active config option (Don't save Shopping Carts of registered Users)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blPerfNoBasketSaving" => array("type" => "bool", "value" => '')));
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1001");
        $this->selectVariant("selectlistsselector_searchList_1", 1, "selvar2 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->selectVariant("productSelections", 1, "selvar4 [EN] šÄßüл +2%", "Test product 1 [EN]");
        $this->clickAndWait("toBasket");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->assertTrue($this->isElementPresent("persparam[details]"));
        $this->clickAndWait("toBasket");
        $this->type("persparam[details]", "test label šÄßüл");
        $this->clickAndWait("toBasket");
        $this->openBasket();

        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div"));
        $this->assertEquals("selvar2 [EN] šÄßüл", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_2']//span"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]/div"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[3]/div"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_3']/td[3]/p/input"));
        $this->assertEquals("test label šÄßüл", $this->getValue("//tr[@id='cartItem_4']/td[3]/p/input"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar3 [EN] šÄßüл -2,00 €", "Grand Total");
        $this->type("am_3", "2");
        $this->type("//tr[@id='cartItem_4']/td[3]/p/input", "test label šÄßüл 1");
        $this->clickAndWait("basketUpdate");

        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_2']//span"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_3']/td[3]/p/input"));
        $this->assertEquals("2", $this->getValue("am_3"));
        $this->assertEquals("test label šÄßüл 1", $this->getValue("//tr[@id='cartItem_4']/td[3]/p/input"));
        $this->assertEquals("1", $this->getValue("am_4"));
        //checking if this basket was saved
        $this->openShop();
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("5", $this->getText("//div[@id='miniBasket']/span"));
        $this->openBasket();
        $this->type("am_2", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_3']//span"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_4']//span"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_1']/td[3]/p/input"));
        $this->assertEquals("2", $this->getValue("am_1"));
        $this->assertEquals("test label šÄßüл 1", $this->getValue("//tr[@id='cartItem_2']/td[3]/p/input"));
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
        $this->assertEquals("Label: test label šÄßüл 1", $this->clearString($this->getText("//tr[@id='cartItem_2']/td[2]/p")));
        $this->assertEquals("379,40 €", $this->getText("basketGrandTotal"),"Grand total price chenged or did't displayed");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        //checking in Admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=12", "save");
        $this->assertTrue($this->isTextPresent("Label: test label šÄßüл 1"));
        $this->assertEquals("2 *", $this->getText("//table[2]/tbody/tr/td[1]"));
        $this->assertEquals("Test product 0 [EN]", $this->getText("//td[3]"));
        $this->assertEquals("90,00 EUR", $this->getText("//td[5]"));
        $this->assertTrue($this->isTextPresent("Label: test label šÄßüл 1"));
        $this->frame("list");
        $this->openTab("link=Products", "//input[@value='Update']");
        $this->assertEquals("2", $this->getValue("//tr[@id='art.2']/td[1]/input"));
        $this->assertEquals("Label: test label šÄßüл 1", $this->getText("//tr[@id='art.2']/td[5]"));
        $this->assertEquals("45,00 EUR", $this->getText("//tr[@id='art.2']/td[7]"));
        $this->assertEquals("90,00 EUR", $this->getText("//tr[@id='art.2']/td[8]"));
        $this->type("//tr[@id='art.2']/td[1]/input", "1");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("Label: test label šÄßüл 1", $this->getText("//tr[@id='art.2']/td[5]"));
        $this->assertEquals("45,00 EUR", $this->getText("//tr[@id='art.2']/td[7]"));
        $this->assertEquals("45,00 EUR", $this->getText("//tr[@id='art.2']/td[8]"));
     //after recalculation fix sum total should be:
       // $this->assertEquals("514,40", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("336,42", $this->getText("//table[@id='order.info']/tbody/tr[8]/td[2]"));

        //disabling option (Product can be customized) where product ID is `OXID` = '1000
        $aCategoryParams = array("oxisconfigurable" => 0);
        $this->callShopSC("oxArticle", "save", "1000", $aCategoryParams);

        $this->openShop();
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']/li//a");
        $this->assertFalse($this->isElementPresent("persparam[details]"));
    }


    /**
     * My Account navigation: Order history
     * Testing min order price
     * @group navigation
     * @group user
     * @group order
     * @gruop basketfrontend
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
        $this->assertEquals("E-mail: birute_test@nfq.lt UserCompany šÄßüл User additional info šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл Musterstr.šÄßüл 1 HE 79098 Musterstadt šÄßüл Germany Phone: 0800 111111 Fax: 0800 111112 Celluar Phone: 0800 111114 Personal Phone: 0800 111113", $this->clearString($this->getText("//div[@id='orderAddress']/dl[1]/dd")));
        $this->assertEquals("Mr deliveryNamešÄßüл deliverySurnamešÄßüл deliveryStreetšÄßüл 2 NI 3000 deliveryCityšÄßüл Germany", $this->clearString($this->getText("//div[@id='orderAddress']/dl[2]/dd")));
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
     * @gruop basketfrontend
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
        $this->assertEquals("Art.No. 1000", $this->getText("//tr[@id='cartItem_4']/td[3]/div[2]"));
        $this->assertEquals("Art.No. 1001", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("Art.No. 1002-2", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
        $this->assertEquals("Art.No. 1003", $this->getText("//tr[@id='cartItem_3']/td[3]/div[2]"));

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
        $this->clickAndWait("//div[@id='content']/div[3]//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
    }


    /**
     * Vouchers is disabled via performance options
     * @group navigation
     * @gruop basketfrontend
     */
    public function testFrontendDisabledVouchers()
    {
        //disabling option (Use vouchers)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showVouchers" => array("type" => "bool", "value" => "false",  "module" => "theme:azure")));
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
     * @gruop basketfrontend
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
        $this->assertEquals("Coupon (No. test111) remove", $this->getText("//div[@id='basketSummary']//tr[6]/th"));
        $this->type("voucherNr", "test222");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertEquals("Coupon (No. test111) remove", $this->getText("//div[@id='basketSummary']//tr[6]/th"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
        $this->assertEquals("Coupon (No. test222) remove", $this->getText("//div[@id='basketSummary']//tr[7]/th"));
        $this->assertEquals("-9,00 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        $this->type("am_4", "3");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
        $this->assertEquals("-15,00 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        $this->check("//tr[@id='cartItem_1']/td[1]//input");
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->clickAndWait("basketRemove");
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->assertEquals("-3,00 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"));
    }

    /**
     * Orders: buying more items than available
     * @group order
     * @group user
     * @group navigation
     * @gruop basketfrontend
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
     * @gruop basketfrontend
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

        $this->assertFalse($this->isTextPresent("discount for category [EN] šÄßüл"),"name of category discount should not be displayed in basket");
        $this->assertEquals("45,00 € \n50,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");

        $this->assertFalse($this->isElementPresent("cartItem_3"));
        $this->assertFalse($this->isTextPresent("discount for product [EN] šÄßüл"));

        $this->type("am_2", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertfalse($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertFalse($this->isTextPresent("discount for product [EN] šÄßüл"));

        $this->assertEquals("45,00 € \n50,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");
        $this->assertFalse($this->isElementPresent("cartItem_3"));

        $this->type("am_1", "5");
        $this->clickAndWait("basketUpdate");

        $this->assertEquals("Test product 3 [EN] šÄßüл Art.No. 1003", $this->clearString($this->getText("//tr[@id='cartItem_3']/td[3]")));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_3']/td[5]"));

        $this->assertEquals("297,48 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("10,71 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 5% changed ");
        $this->assertEquals("15,81 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"),"VAT 5% changed ");
        $this->assertEquals("324,00 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("1,50 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("325,50 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");
        $this->assertEquals("45,00 € \n50,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");

        //test for #1822
        $this->executeSql("UPDATE `oxdiscount` SET `OXACTIVE` = 1 WHERE `OXID` = 'testdiscount5';");
        $this->clickAndWait("link=1. Cart");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->assertFalse($this->isElementPresent("//a[text()='Logout']"));
        $this->check("//tr[@id='cartItem_2']/td[1]/input");
        $this->type("am_1", "1");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isTextPresent("1 EN test discount šÄßüл"));
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
    }

   
    /**
     * performing order when delivery country does not have any of payment methods
     * @group order
     * @group user
     * @group navigation
     * @gruop basketfrontend
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

        $this->assertEquals("Shipping Carrier edit", $this->getText($this->clearString("orderShipping")));
        $this->assertEquals("Type of Payment edit Empty", $this->getText($this->clearString("orderPayment")));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
    }



     /**
     * Order step 2
     * @group order
     * @group user
     * @group navigation
      * @gruop basketfrontend
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
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blOrderDisWithoutReg" => array("type" => "bool", "value" => 'true')));
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
     * @gruop basketfrontend
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
    * @gruop basketfrontend
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
        $this->assertTrue($this->isTextPresent("Greeting card text"),"No greeting card message in the greeting card message field");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isVisible("orderRemark"),"I wanted to say field is not available");
        $this->type("orderRemark", "remark text");
        $this->assertTrue($this->isVisible("subscribeNewsletter"),"There is no Subscribe Newsletter check box");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]")),"Wraping for the product disapers from the basket");
        $this->assertTrue($this->isTextPresent("Greeting card text"),"Greeting card message is not visible in the basket");
        $this->assertTrue($this->isTextPresent("What I wanted to say remark text"), "What I wanted to say field disapear from  the basket");

        //link to billing and address
        $this->assertEquals("E-mail: birute_test@nfq.lt UserCompany šÄßüл User additional info šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл Musterstr.šÄßüл 1 79098 Musterstadt šÄßüл Germany Phone: 0800 111111 Fax: 0800 111112 Celluar Phone: 0800 111114 Personal Phone: 0800 111113", $this->clearString($this->getText("//div[@id='orderAddress']//dl/dd")),"user address changed");
        $this->assertFalse($this->isTextPresent("Here you can enter an optional message."));
        $this->clickAndWait("//div[@id='orderAddress']//button");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"),"user should be in 2nd order step");
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
        $this->assertEquals("E-mail: birute_test@nfq.lt UserCompany šÄßüл User additional info šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл Musterstr.šÄßüл 1 79098 Musterstadt šÄßüл Germany Phone: 0800 111111 Fax: 0800 111112 Celluar Phone: 0800 111114 Personal Phone: 0800 111113", $this->clearString($this->getText("//div[@id='orderAddress']//dl/dd")),"user address changed");
        $this->assertEquals("company Mr first last street 1 3000 city Germany", $this->clearString($this->getText("//div[@id='orderAddress']//dl[2]/dd[1]")),"billing address changed");
        $this->assertEquals("my message", $this->clearString($this->getText("//div[@id='orderAddress']//dl[3]/dd[1]")),"what i wanted to say text not dispayed i  the last order step");

        //link to payment method
        $this->assertEquals("Shipping Carrier edit Standard", $this->clearString($this->getText("orderShipping")),"shipping method not displayed correctly, should be Standart");
        $this->assertEquals("Type of Payment edit COD (Cash on Delivery)", $this->clearString($this->getText("orderPayment")),"paymenth method not displayed correctly, should be COD");
        $this->clickAndWait("//div[@id='orderShipping']//button");
        $this->select("sShipSet", "label=Standard");
        $this->click("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Type of Payment edit Cash in advance", $this->clearString($this->getText("orderPayment")),"payment method not displayed correctly, should be Cash in advance");
        $this->clickAndWait("//div[@id='orderPayment']//button");
        $this->select("sShipSet", "label=Standard");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Shipping Carrier edit Standard", $this->clearString($this->getText("orderShipping")),"shipping method not displayed correctly, should be Standart");
        $this->assertEquals("Type of Payment edit COD (Cash on Delivery)", $this->clearString($this->getText("orderPayment")),"paymant method not displayed correctly, should be COD");
        //testing displayed information
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_1']//td[5]/s"),"price with discount not shown in basket");
        $this->assertEquals("136,40 €", $this->getText("basketTotalProductsNetto"),"Neto price chenged or did't displayed");
        $this->assertEquals("8,29 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 10% chenged");
        $this->assertEquals("10,16 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"),"VAT 19%changed");
        $this->assertEquals("163,00 €", $this->getText("basketTotalProductsGross"),"Bruto price chenged or did't displayed");
        $this->assertEquals("Coupon (No. 222222)", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]/th")),"Cupon chenged or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"),"Shipping price chenged or did't displayed");
        $this->assertEquals("7,50 €", $this->getText("basketPaymentGross"),"Payment price chenged or did't displayed");
        $this->assertEquals("0,90 €", $this->getText("basketWrappingGross"),"Wraping price chenged or did't displayed");
        $this->assertEquals("0,20 €", $this->getText("basketGiftCardGross"),"Card price chenged or did't displayed");
        $this->assertEquals("163,45 €", $this->getText("basketGrandTotal"),"Garnd total price chenged or did't displayed");
    }

    /**
     * Order steps (without any special checking for discounts, various VATs and user registration)
     * @group order
     * @group user
     * @group navigation
     * @gruop basketfrontend
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
        $this->click("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
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
     * @gruop basketfrontend
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
        $this->check("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

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
        $this->check("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("Customers who bought this products, also bought:", $this->clearString($this->getText("//h1")));
        $this->assertTrue($this->isElementPresent("//ul[@id='alsoBoughtThankyou']/li[1]"));
        //fix it in future: mouseOver effect is not working after latest jQuery update. use mouse over when working solution will be find
        //$this->mouseOverAndClick("//ul[@id='alsoBoughtThankyou']/li[1]", "//ul[@id='alsoBoughtThankyou']/li[1]//a");
        $this->clickAndWait("//ul[@id='alsoBoughtThankyou']/li[1]//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));

        //turning Load "Customers who bought this product also purchased..." OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadCustomerWhoBoughtThis" => array("type" => "bool", "value" => "false")));

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
        $this->check("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertFalse($this->isElementPresent("//h1[text()='Customer who bought this product also bought:']"));
        $this->assertFalse($this->isElementPresent("alsoBoughtThankyou"));
    }

    /**
     * Testing giftWrapping selection.
     * @group order
     * @group user
     * @group navigation
     * @gruop basketfrontend
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
     * @gruop basketfrontend
     */
    public function testFrontendDisabledGiftWrapping()
    {
        ////disabling option in admin (Use gift wrapping)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showGiftWrapping" => array("type" => "bool", "value" => "false",  "module" => "theme:azure")));
        $this->openShop();
        $this->searchFor("1001");
        $this->selectVariant("selectlistsselector_searchList_1", 1, "selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[4]")));
        $this->assertFalse($this->isElementPresent("//tr[@id='cartItem_1']/td[4]/a"));
    }


    /**
     * Checking VAT functionality, when it is calculated for Shipping country
     * @group navigation
     * @gruop basketfrontend
     */
    public function testFrontendVatForShippingCountry()
    {
        //in admin set "Use shipping country for VAT calculation instead of billing country"
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blShippingCountryVat" => array("type" => "bool", "value" => "true")));
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
        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 5% changed ");
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"),"VAT 10% changed ");
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"),"VAT 19% changed ");
        $this->assertEquals("216,00 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("216,00 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //no shipping address
        $this->check("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[6]"));

        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 5% changed ");
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"),"VAT 10% changed ");
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"),"VAT 19% changed ");
        $this->assertEquals("216,00 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("216,00 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");

        $this->assertEquals("50,00 €", $this->getText("//tr[@id='cartItem_1']//td[5]/s"),"price with discount not shown in basket");
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_2']//td[5]/s"),"price with discount not shown in basket");

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
        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 0% changed ");
        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("6,90 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("209,37 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");

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
        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"),"Neto price changed or did't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"),"VAT 5% changed ");
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"),"VAT 10% changed ");
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"),"VAT 19% changed ");
        $this->assertEquals("216,00 €", $this->getText("basketTotalProductsGross"),"Bruto price changed  or did't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"),"Shipping price changed  or did't displayed");
        $this->assertEquals("216,00 €", $this->getText("basketGrandTotal"),"Garnd total price changed  or did't displayed");

        $this->assertEquals("50,00 €", $this->getText("//tr[@id='cartItem_1']//td[5]/s"),"price with discount not shown in basket");
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_2']//td[5]/s"),"price with discount not shown in basket");
    }


    /**
    * Order steps changing Shipping Addresses in Account/Address processing checkout will change shipping address at order too
    * @group order
    * @group user
    * @group navigation
    * @gruop basketfrontend
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
        $this->click("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

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
        $this->click("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        //billing address was changed, so messega was displayed:" Billing or shipping address have been changed during checkout. Please check again."
        $this->click("//form[@id='orderConfirmAgbTop']/div/input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
        $this->assertTrue($this->isElementPresent("backToShop"));
        $this->assertEquals("back to Startpage", $this->getText("backToShop"));
    }

}