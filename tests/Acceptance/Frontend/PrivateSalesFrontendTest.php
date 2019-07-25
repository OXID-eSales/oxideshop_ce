<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** Private sales related tests. */
class PrivateSalesFrontendTest extends FrontendTestCase
{
    /**
     * Basket exclusion: situation 1
     *
     * @group privateSales
     */
    public function testBasketExclusionCase1()
    {
        //basket exclusion is off
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->assertEquals("%YOU_ARE_HERE%: / Kiteboarding / Kites", $this->getText("breadCrumb"));

        //enabling basket exclusion
        $this->callShopSC('oxConfig', null, null, array('blBasketExcludeEnabled' => array("type" => "bool",  "value" => 'true' ) ));

        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->assertTextNotPresent("%ROOT_CATEGORY_CHANGED%");
        $this->clickAndWait("link=Kiteboarding");
        $this->assertElementPresent("scRootCatChanged");
        $this->assertTextPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementPresent("tobasket");
        $this->assertElementPresent("//button[text()='%CONTINUE_SHOPPING%']");
        $this->clickAndWait("tobasket");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']");
        $this->clickAndWait("link=%HOME%");
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("scRootCatChanged");
        $this->clickAndWait("moreSubCat_1");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->clickAndWait("//form[@name='tobasketproductList_1']//button");
        $this->waitForElementText("2", "//div[@id='miniBasket']/span");

        $this->clickAndWait("link=Kiteboarding");
        $this->assertElementPresent("scRootCatChanged");
        $this->assertTextPresent("%ROOT_CATEGORY_CHANGED%");
        $this->clickAndWait("//button[text()='%CONTINUE_SHOPPING%']");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait("//ul[@id='productList']/li[1]//button");
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");

        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementPresent("scRootCatChanged");
        $this->assertTextPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementPresent("tobasket");
        $this->assertElementPresent("//button[text()='%CONTINUE_SHOPPING%']");
    }

    /**
     * Basket exclusion: situation 2
     *
     * @group privateSales
     */
    public function testBasketExclusionCase2()
    {
        //enabling basket exclusion
        $this->callShopSC("oxConfig", null, null, array("blBasketExcludeEnabled" => array("type" => "bool", "value" => 'true')));
        //checking in frontend
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->clickAndWait("link=Test category 1 [EN] šÄßüл");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));

        $this->clickAndWait("link=Kiteboarding");
        $this->assertElementPresent("scRootCatChanged");
        $this->clickAndWait("tobasket");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");

        $this->click("checkAll");
        $this->clickAndWait("basketRemove");

        $this->assertTextPresent("%BASKET_EMPTY%");
        $this->clickAndWait("link=%HOME%");
        $this->clickAndWait("link=Kiteboarding");
        $this->assertTextNotPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTextNotPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementNotPresent("scRootCatChanged");
    }

    /**
     * Private sales: basket expiration
     *
     * @group privateSales
     */
    public function testPrivateShoppingBasketExpiration()
    {
        //products are offline, if bought out
        $this->callShopSC("oxArticle", "save", "1000", array("oxstock" => 2, "oxstockflag" => 2), null, 1);

        //enabling functionality to set basket expiration for 20 sek.
        $this->callShopSC("oxConfig", null, null, array("blPsBasketReservationEnabled" => array("type" => "bool",  "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("iPsBasketReservationTimeout" => array("type" => "str",  "value" => '20')));

        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//ul[@id='newItems']//input[@name='aid' and @value='1000']");
        $this->assertElementPresent("priceBargain_1");
        $this->searchFor("1000");
        $this->assertEquals("1 %HITS_FOR% \"1000\"", $this->getHeadingText("//h1"));
        $this->assertTextNotPresent("%EXPIRES_IN%:");
        $this->selectDropDown("viewOptions", "%line%");

        //adding product to basket
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");
        $this->assertTextPresent("%EXPIRES_IN%:");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->waitForElementText("2", "//div[@id='miniBasket']/span");
        $this->assertTextPresent("%EXPIRES_IN%:");

        //checking if product is reserved
        $this->searchFor("1000");
        $this->assertTextPresent("%NO_ITEMS_FOUND%");
        $this->assertTextPresent("%YOU_ARE_HERE%: / %SEARCH%");
        sleep(21); //waiting till basket will expire
        $this->assertElementNotPresent("basketFlyout", "expired products are still visible in basket popup...");
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");

        $this->searchFor("1000");
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->assertTextNotPresent("%EXPIRES_IN%:");
        $this->assertTextPresent("1 %HITS_FOR% \"1000\"");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));

        //adding to basket again and finishing order
        $this->assertElementPresent("//ul[@id='searchList']/li//button");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->openBasket();
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_usr']", "example_test@oxid-esales.dev");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='optionLogin']//button");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertElementPresent("orderConfirmAgbTop");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));
        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("//ul[@id='newItems']//input[@name='aid' and @value='1000']");
    }
}
