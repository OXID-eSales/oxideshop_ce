<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** My account related tests */
class MyAccountFrontendTest extends FrontendTestCase
{
    /**
     * Checking Listmania
     *
     * @group myAccount
     */
    public function testFrontendListmaniaInfo()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }

        $aWrappingParams = array("OXTYPE" => 'oxrecommlist');
        $this->callShopSC("oxReview", "delete", $sOxid = "testrecomreview", $aWrappingParams, null, 1);
        $aRatingParams = array("OXTYPE" => 'oxrecommlist');
        $this->callShopSC("oxRating", "delete", $sOxid = "testrecomrating", $aRatingParams, null, 1);
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //checking small listmania box
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");

        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//article[@id='recommendationsBox']/h3"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->clearString($this->getHeadingText("//article[@id='recommendationsBox']/h3")));
        $this->clickAndWait("//article[@id='recommendationsBox']//ul/li[1]/a");
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->switchLanguage('Deutsch');
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->switchLanguage('English');
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//article[@id='recommendationsBox']/h3"));
        $this->clickAndWait("//article[@id='recommendationsBox']/a/img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->clearString($this->getHeadingText("//article[@id='recommendationsBox']/h3")));
        $this->assertEquals("recomm title", $this->getText("//article[@id='recommendationsBox']//ul/li[1]/a"));
        $this->assertEquals("%LIST_BY%: recomm author", $this->getText("//article[@id='recommendationsBox']//ul/li[1]/div"));

        //writing recommendation for listmania
        $this->clickAndWait("//article[@id='recommendationsBox']//ul/li[1]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//a[@id='rssRecommListProducts']");
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->assertTextPresent("recom introduction");
        $this->assertTextPresent("recom introduction");
        $this->assertTextPresent("%NO_REVIEW_AVAILABLE%");
        $this->assertEquals("%NO_RATINGS%", $this->getText("itemRatingText"));
        $this->click("writeNewReview");
        $this->click("//ul[@id='reviewRating']/li[@class='s3']/a");
        $this->type("rvw_txt", "recommendation for this list");
        $this->clickAndWait("reviewSave");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("UserNamešÄßüл writes: ".date("d.m.Y"), $this->clearString($this->getText("reviewName_1")));
        $this->assertEquals("recommendation for this list", $this->getText("reviewText_1"));
        $this->assertEquals("(1)", $this->getText("itemRatingText"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_productList_1"));
        $this->assertElementPresent("//form[@name='tobasket.productList_1']//div[text()='comment for product 1000']");
        $this->assertEquals("50,00 € *", $this->getText("productPrice_productList_1"));
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=recomm title");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//form[@name='tobasket.productList_1']/div[2]//a"));
        $this->clickAndWait("//ul[@id='productList']/li[1]/form/div[2]//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=recomm title");

        $this->type("amountToBasket_productList_1", "2");
        $this->clickAndWait("toBasket_productList_1");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//form[@name='tobasket.productList_1']/div[2]//a"));
    }

    /**
     * Checking Listmania
     *
     * @group myAccount
     */
    public function testFrontendListmaniaAddSearch()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //adding other products to listmania

        $this->openArticle(1001);
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1001");
        $this->clickAndWait("//button[text()='%ADD_TO_LISTMANIA_LIST%']");

        $this->openArticle(1002);
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1002");
        $this->clickAndWait("//button[text()='%ADD_TO_LISTMANIA_LIST%']");

        //search in listmania
        $this->type("searchRecomm", "title");
        $this->clickAndWait("//article[@id='recommendationsBox']//button");
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->assertEquals("1 %HITS_FOR% \"title\"", $this->getText("//h1"));
        $this->assertEquals("recomm title", $this->getText("//ul[@id='recommendationsLists']/li[1]//a"));
        $this->assertEquals("recom introduction", $this->getText("//ul[@id='recommendationsLists']/li[1]//div[2]"));
        $this->assertTextPresent("recomm title : %LIST_BY% recomm author");
        $this->clickAndWait("link=recomm title");
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->assertTextPresent("recom introduction");
        $this->assertEquals("%WRITE_REVIEW%", $this->getText("writeNewReview"));
        $this->assertEquals("recommendation for this list", $this->getText("reviewText_1"));

        $expected = array("Test product 0 [EN] šÄßüл",
                          "Test product 1 [EN] šÄßüл",
                          "Test product 2 [EN] šÄßüл");

        $check = array($this->getText("productList_1"),
                       $this->getText("productList_2"),
                       $this->getText("productList_3"));
        sort($check);

        $this->assertEquals($expected, $check);
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->type("searchRecomm", "no entry");
        $this->clickAndWait("//article[@id='recommendationsBox']//button");
        $this->assertEquals("no entry", $this->getValue("searchRecomm"));
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("0 %HITS_FOR% \"no entry\"", $this->getText("//h1"));
        $this->assertTextPresent("%NO_LISTMANIA_LIST_FOUND%");

        //editing listmania (with articles)
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[2]/a");
        $this->clickAndWait("link=%MY_LISTMANIA%");
        $this->clickAndWait("//ul[@id='recommendationsLists']/li[1]//button[text()='%EDIT%']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));
        $this->assertEquals("recomm title", $this->getValue("recomm_title"));
        $this->assertEquals("recomm author", $this->getValue("recomm_author"));
        $this->assertEquals("recom introduction", $this->getValue("recomm_desc"));
        $this->type("recomm_desc", "recom introduction1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_title", "recomm title1");
        $this->clickAndWait("//form[@name='saverecommlist']//button[text()='%SAVE%']");
        $this->assertTextPresent("%LISTMANIA_LIST_SAVED%");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));
    }

    /**
     * Checking Listmania
     *
     * @group myAccount
     */
    public function testFrontendListmaniaDelete()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->openArticle(1001);

        //adding other products to listmania
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1001");
        $this->clickAndWait("//button[text()='%ADD_TO_LISTMANIA_LIST%']");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT% - \"example_test@oxid-esales.dev\"", $this->getText("//h1"));
        $this->clickAndWait("//section[@id='content']//div[2]/dl[4]/dt/a");
        $this->clickAndWait("//section[@id='content']//ul/li[1]//button[@name='editList']");

        $first = $this->getText("recommendProductList_1");
        $check = 2;
        if (false !== strpos($first, 'product 1 [EN]')) {
            $check = 1;
        }
        $expected = "selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%";
        $this->assertEquals($expected, $this->clearString($this->getText("//div[@id='selectlistsselector_recommendProductList_{$check}']//ul")));

        //removing articles from list
        $this->clickAndWait("//button[@triggerform='remove_removeArticlerecommendProductList_2']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));
        $this->assertEquals($first, $this->getText("recommendProductList_1"));
        $this->assertElementNotPresent("recommendProductList_2");
        $this->clickAndWait("//aside[@id='sidebar']//a[text()='%MY_LISTMANIA%']");
        $this->assertEquals("recomm title", $this->getText("//ul[@id='recommendationsLists']/li[1]//a"));
        $this->assertTextPresent("recomm title : %LIST_BY% recomm author");
        $this->assertTextPresent("recom introduction");

        //deleting recom list
        $this->clickAndWait("//ul[@id='recommendationsLists']/li[1]//button[@name='deleteList']");
        $this->assertTextPresent("%NO_LISTMANIA_LIST_FOUND%");
    }
}
