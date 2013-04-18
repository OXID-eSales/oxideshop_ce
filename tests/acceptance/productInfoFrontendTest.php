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

class Acceptance_productInfoFrontendTest extends oxidAdditionalSeleniumFunctions
{
    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ---------------------- Frontend: product information/ details related tests -------------------------------


    /**
     * Orders: buying more items than available
     * @group order
     * @group user
     * @group navigation
     * @group product
     */
    public function testFrontendEuroSignInTitle()
    {
            $this->loginAdmin("Administer Products", "Products");
            $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
            $this->type("where[oxarticles][oxartnum]", "1002");
            $this->clickAndWait("submitit");
            $this->openTab("link=1002");
            $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
            $this->type("editval[oxarticles__oxtitle]", "[DE 2] Test product 2 šÄßüл €");
            $this->clickAndWait("saveArticle");
            $this->assertEquals("[DE 2] Test product 2 šÄßüл €", $this->getValue("editval[oxarticles__oxtitle]"));
            $this->openShop();
            $this->click("languageTrigger");
            $this->waitForItemAppear("languages");
            $this->click("//ul[@id='languages']/li[2]/a");
            $this->waitForItemDisappear("languages");
            $this->searchFor("1002");
            $this->assertEquals("[DE 2] Test product 2 šÄßüл €", $this->getText("searchList_1"));
    }

    /**
     * Product details. test for checking main product details as info, prices, buying etc
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendDetailsNavigationAndInfo()
    {
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[2]//a");

        //navigation between products (in details page)
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("PRODUCT 2 OF 4", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->clickAndWait("//div[@id='detailsItemsPager']/a[text()='Next Product']");
        $this->assertEquals("PRODUCT 3 OF 4", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='detailsItemsPager']/a[text()='Previous Product']");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("PRODUCT 2 OF 4", $this->getText("//div[@id='detailsItemsPager']/span"));

        //product info
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Art.No.: 1001", $this->getText("productArtnum"));
        $this->assertEquals("Test product 1 short desc [EN] šÄßüл", $this->getText("productShortdesc"));
        $this->assertTrue($this->isTextPresent("This item is not in stock and must be reordered"));
        $this->assertTrue($this->isTextPresent("Available on 2008-01-01"));
        $this->assertTrue($this->isElementPresent("productSelections"));
        $this->assertTrue($this->isElementPresent("//div[@id='productSelections']//ul"));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='productSelections']//ul"));
        $this->assertTrue($this->isTextPresent("RRP 150,00 €"));
        $this->assertEquals("100,00 € *", $this->clearString($this->getText("productPrice")));
        $this->assertEquals("Test product 1 long description [EN] šÄßüл", $this->getText("description"));

        $this->assertEquals("Specification", $this->clearString($this->getText("//ul[@id='itemTabs']/li[2]")));
        $this->click("//ul[@id='itemTabs']/li[2]/a");
        $this->waitForItemAppear("attributes");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[1]/th"));
        $this->assertEquals("attr value 11 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[1]/td"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[2]/th"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[2]/td"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[3]/th"));
        $this->assertEquals("attr value 12 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[3]/td"));

        $this->assertEquals("Tags", $this->clearString($this->getText("//ul[@id='itemTabs']/li[4]")));
        $this->click("//ul[@id='itemTabs']/li[4]/a");
        $this->waitForItemAppear("tags");
        $this->assertEquals("šÄßüл tag [EN] 1", $this->clearString($this->getText("tags")));

        //buying product
        $this->click("//div[@id='productSelections']//ul/li[2]/a");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertEquals("100", $this->getValue("searchParam"));

    }

    /**
     * Product details. test for checking main product details as info, prices, buying etc
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendDetailsAdditionalInfo()
    {
        if ( isSUBSHOP ) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->openShop();
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']//a");
        //staffelpreis
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("75,00 € *", $this->getText("productPrice"));
            $this->assertTrue($this->isElementPresent("amountPrice"));
            $this->click("amountPrice");
            $this->waitForItemAppear("priceinfo");
            $this->assertEquals("2", $this->getText("//ul[@id='priceinfo']/li[3]/label"));
            $this->assertEquals("75,00 €", $this->getText("//ul[@id='priceinfo']/li[3]/span"));
            $this->assertEquals("6", $this->getText("//ul[@id='priceinfo']/li[4]/label"));
            $this->assertEquals("20 % Discount", $this->getText("//ul[@id='priceinfo']/li[4]/span"));
            $this->click("amountPrice");
            $this->waitForItemDisappear("priceinfo");

        //review when user not logged in
        $this->assertTrue($this->isElementPresent("//h4[text()='Write Product Review']"));
        $this->assertTrue($this->isTextPresent("No review available for this item."));
        $this->assertEquals("You have to be logged in to write a review.", $this->getText("reviewsLogin"));

        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertEquals("Please login to access Wish List.", $this->getText("loginToNotice"));
        $this->assertEquals("Please login to access your Gift Registry.", $this->getText("loginToWish"));

        //compare link
        $this->assertFalse($this->isElementPresent("//p[@id='servicesTrigger']/span"));
        $this->clickAndWait("addToCompare");
        $this->assertEquals("1",$this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertEquals("remove from compare list", $this->getText("removeFromCompare"));
        $this->clickAndWait("removeFromCompare");
        $this->assertFalse($this->isElementPresent("//p[@id='servicesTrigger']/span"));
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertEquals("compare", $this->getText("addToCompare"));
        $this->clickAndWait("addToCompare");
        //check if compare products are not gone after you login
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("2",$this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->assertEquals("Product: 1",$this->clearString($this->getText("//div[@id='content']//div[2]/dl[3]/dd")));
    }

    /**
     * Performance option "Show compare list" is disabled
     * @group navigation
     * @group product
     */
    public function testFrontendDisabledCompare()
    {
        //(Use compare->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showCompareList" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showCompareList" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));
        $this->openShop();
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertFalse($this->isElementPresent("addToCompare"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertFalse($this->isElementPresent("addToCompare"));
    }


    /**
     * Check is Compare options works corectly
     * @group navigation
     * @group product
     */
    public function testCompareInFrontend()
    {

        $this->openShop();
        $this->clickAndWait('toCmp_newItems_1');
        $this->searchFor("1");
        $this->clickAndWait('toCmp_searchList_1');
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait('toCmp_productList_1');
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->assertTrue($this->isElementPresent('productPrice_1'));
        $this->assertTrue($this->isElementPresent("//a[text()='Test product 0 [EN] šÄßüл ']"));
        $this->assertTrue($this->isElementPresent('productPrice_2'));
        $this->assertTrue($this->isElementPresent("//a[text()='Kite CORE GTS ']"));
        $this->assertTrue($this->isElementPresent('productPrice_3'));
        $this->assertTrue($this->isElementPresent("//a[text()='Harness MADTRIXX ']"));
        $this->clickAndWait("link=Home");
        $this->clickAndWait('removeCmp_newItems_1');
        $this->searchFor("1");
        $this->clickAndWait('removeCmp_searchList_1');
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait('removeCmp_productList_1');
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->assertFalse($this->isElementPresent('productPrice_1'));
        $this->assertFalse($this->isElementPresent('productPrice_2'));
        $this->assertFalse($this->isElementPresent('productPrice_3'));
        $this->assertTrue($this->isTextPresent("Please select at least two products to be compared."));
    }

    /**
     * Product details. Sending remommendation of product
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendDetailsRecommend()
    {
        //In admin Set option (Installed GDLib Version) if "value" => ""
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iUseGDVersion" => array("type" => "str", "value" => "")));

        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        //recommend link
        $this->clickAndWait("suggest");
        $this->assertEquals("You are here: / Recommend Product", $this->getText("breadCrumb"));
        $this->assertEquals("Recommend Product", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Enter your address data and personal message."));
        $this->assertEquals("", $this->getValue("editval[rec_name]"));
        $this->assertEquals("", $this->getValue("editval[rec_email]"));
        $this->assertEquals("", $this->getValue("editval[send_name]"));
        $this->assertEquals("", $this->getValue("editval[send_email]"));
        $this->assertTrue($this->isElementPresent("editval[send_message]"));
        $this->click("//button[text()='Send']");
        $this->waitForText("Specify a value for this required field.");
        $this->type("editval[rec_name]", "Test User");
        $this->type("editval[rec_email]", "birute@nfq.lt");
        $this->type("editval[send_name]", "user");
        $this->type("editval[send_email]", "birute_test@nfq.lt");
        $this->type("editval[send_subject]", "Have a look at: Test product 1 [EN] šÄßüл");
        $this->type("c_mac", $this->getText("verifyTextCode"));
        $this->clickAndWait("//button[text()='Send']");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Product details. Testing price alert
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendDetailsPriceAlert()
    {
        //In admin Set option (Installed GDLib Version) if "value" => ""
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iUseGDVersion" => array("type" => "str", "value" => "")));
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->assertFalse($this->isVisible("c_mac"));
        $this->assertTrue($this->isElementPresent("link=[!] Price Alert"));
        $this->click("productLinks");
        $this->waitForItemAppear("//a[@id='priceAlarmLink']");
        $this->click("//a[@id='priceAlarmLink']");
        $this->waitForItemAppear("c_mac");
        $this->type("c_mac", $this->getText("verifyTextCode"));
        $this->type("pa[email]", "birute_test@nfq.lt");
        $this->type("pa[price]", "99.99");
        $this->clickAndWait("//form[@name='pricealarm']//button");
        $this->assertTrue($this->isTextPresent("We will inform you as soon as the price falls below 99,99"));
        $this->click("//ul[@id='itemTabs']//a[text()='Description']");
        $this->waitForItemAppear("description");
        $this->click("link=[!] Price Alert");
        $this->waitForItemAppear("c_mac");

        //disabling price alert for product(1001)
        $this->callShopSC("oxArticle", "save", "1001", array("oxblfixedprice" => 1),1);

        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->assertFalse($this->isElementPresent("link=[!] Price Alert"));
        $this->assertFalse($this->isElementPresent("c_mac"));
        $this->assertFalse($this->isElementPresent("pa[email]"));
        $this->assertFalse($this->isElementPresent("pa[price]"));
        //verifying if price alert is saved in shop
        $this->loginAdmin("Customer Info", "Price Alert");
        $this->type("where[oxpricealarm][oxemail]", "birute_test@nfq.lt");
        $this->clickAndWait("submitit");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("99,99 EUR", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("100,00 EUR", $this->getText("//tr[@id='row.1']/td[7]"));

    }

    /**
     * Product details. testing variants
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendDetailsVariants()
    {
        $this->openShop();
        $this->searchFor("1002");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->assertEquals("from 55,00 € *", $this->getText("productPrice"));
        $this->assertEquals("Art.No.: 1002", $this->getText("productArtnum"));
        $this->assertFalse($this->isEditable("toBasket")); //parent article is not buyable
        $this->assertEquals("review for parent product šÄßüл", $this->getText("reviewText_1"));
        $this->assertFalse($this->isElementPresent("reviewText_2"));

        $this->selectVariant("variants", 1, "var1 [EN] šÄßüл", "review for var1 šÄßüл");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("review for var1 šÄßüл", $this->getText("reviewText_1"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("reviewText_2"));
        $this->assertFalse($this->isElementPresent("reviewText_3"));
        $this->assertTrue($this->isEditable("toBasket"));
        $this->assertEquals("Art.No.: 1002-1", $this->getText("productArtnum"));
        $this->assertEquals("55,00 € *", $this->getText("productPrice"));
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->assertEquals("2 x Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл 110,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("Total 110,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));
        $this->type("amountToBasket", "1");
        $this->clickAndWait("toBasket");
        $this->assertEquals("3 x Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл 165,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("Total 165,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));

        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "review for var2 šÄßüл");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("review for var2 šÄßüл", $this->getText("reviewText_1"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("reviewText_2"));
        $this->assertFalse($this->isElementPresent("reviewText_3"));
        $this->assertTrue($this->isEditable("toBasket"));
        $this->assertEquals("Art.No.: 1002-2", $this->getText("productArtnum"));
        $this->assertEquals("67,00 € *", $this->getText("productPrice"));
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->type("amountToBasket", "1");
        $this->clickAndWait("toBasket");
        $this->assertEquals("6", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertEquals("3 x Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл 165,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("3 x Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл 201,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[2]")));
        $this->assertEquals("Total 366,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));
    }

    /**
     * Product details. testing variants. testing ptions related to parent product
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendDetailsVariantsParent()
    {
        //variants reviews will be shown for parent product active "Show Variant Ratings for "Parent" Product "
        //setting article parent as buyable
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blShowVariantReviews" => array("type" => "bool", "value" => 'true')));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blVariantParentBuyable" => array("type" => "bool", "value" => 'true')));
        $this->openShop();
        $this->searchFor("1002");
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("55,00 € *", $this->getText("productPrice"));
        $this->assertTrue($this->isEditable("toBasket")); //parent article is buyable
        $this->assertEquals("review for var2 šÄßüл", $this->getText("reviewText_1"));
        $this->assertEquals("review for var1 šÄßüл", $this->getText("reviewText_2"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("reviewText_3"));
        $this->assertFalse($this->isElementPresent("reviewText_4"));
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
        $this->clickAndWait("toBasket");
        $this->assertEquals("Test product 2 [EN] šÄßüл 55,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("Total 55,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));

        $this->selectVariant("variants", 1, "var1 [EN] šÄßüл", "Test product 2 [EN] šÄßüл var1 [EN] šÄßüл");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("review for var1 šÄßüл", $this->getText("reviewText_1"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("reviewText_2"));
        $this->assertFalse($this->isElementPresent("reviewText_3"));

        $this->openBasket();
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div/a"));
        $this->assertEquals("55,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("55,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));

    }

    /**
     * Product's accessories
     * @group navigation
     * @group product
     */
    public function testFrontendAccessories()
    {
        $this->openShop();
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']//a");

        $this->assertEquals("Accessories", $this->getText("//div[@id='accessories']/h3"));
        $this->assertEquals("Test product 2 [EN] šÄßüл from 55,00 €", $this->clearString($this->getText("//div[@id='accessories']/ul/li[2]/a")));

        $this->clickAndWait("//div[@id='accessories']/ul/li[2]/a");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Product's similar products
     * @group navigation
     * @group product
     */
    public function testFrontendSimilarProducts()
    {
        $this->openShop();
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Similar Products", $this->getText("//div[@id='similar']/h3"));
        $this->assertEquals("Test product 1 [EN] šÄßüл 100,00 €", $this->clearString($this->getText("//div[@id='similar']/ul/li[2]/a")));
        $this->clickAndWait("//div[@id='similar']/ul/li[2]/a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Similar Products", $this->getText("//div[@id='similar']/h3"));
        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 €", $this->clearString($this->getText("//div[@id='similar']/ul/li[2]/a")));
        $this->clickAndWait("//div[@id='similar']/ul/li[2]/a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Similar Products", $this->getText("//div[@id='similar']/h3"));
        $this->assertEquals("Test product 1 [EN] šÄßüл 100,00 €", $this->clearString($this->getText("//div[@id='similar']/ul/li[2]/a")));
    }

    /**
     * Product's crossselling
     * @group navigation
     * @group product
     */
    public function testFrontendCrossselling()
    {
        $this->openShop();
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->assertEquals("Have you seen ...?", $this->getText("//div[@id='cross']/h3"));
        $this->assertEquals("Test product 3 [EN] šÄßüл 75,00 €", $this->clearString($this->getText("//div[@id='cross']/ul/li[2]/a")));
        $this->clickAndWait("//div[@id='cross']/ul/li[2]/a");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));

    }


    /**
     * Checking Multidimensional variants functionality
     * @group navigation
     * @group product
     * @group main
     */
    public function testFrontendMultidimensionalVariantsOnDetailsPage()
    {
        //multidimensional variants on
        //active product WHERE `OXID`='10014'
        $aArticleParams = array("oxactive" => 1);
        $this->callShopSC("oxArticle", "save", "10014", $aArticleParams, 1);
        $this->openShop();
        $this->searchFor("10014");
        $this->clickAndWait("searchList_1");
        $this->assertEquals("14 EN product šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014", $this->getText("productArtnum"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("productShortdesc"));
        $this->assertEquals("from 15,00 € *", $this->getText("productPrice"));
        $this->assertFalse($this->isEditable("toBasket"));

        $this->assertEquals("size[EN]:", $this->getText("//div[@id='variants']/div//label"));
        $this->assertEquals("S M L", $this->getText("//div[@id='variants']/div//ul"));
        $this->assertTrue($this->isVisible("//div[@id='variants']/div//p"));
        $this->assertEquals("color:", $this->getText("//div[@id='variants']/div[2]//label"));
        $this->assertEquals("black white red", $this->getText("//div[@id='variants']/div[2]//ul"));
        $this->assertTrue($this->isVisible("//div[@id='variants']/div[2]//p"));
        $this->assertEquals("type:", $this->getText("//div[@id='variants']/div[3]//label"));
        $this->assertEquals("lether material", $this->getText("//div[@id='variants']/div[3]//ul"));
        $this->assertTrue($this->isVisible("//div[@id='variants']/div[3]//p"));

        $this->selectVariant("variants", 1, "S", "Selected combination: S");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 2, "black", "Selected combination: S, black");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 3, "lether", "Selected combination: S, black, lether");
        $this->assertEquals("14 EN product šÄßüл S | black | lether", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014-1-1", $this->getText("productArtnum"));
        $this->assertEquals("25,00 € *", $this->getText("productPrice"));
        $this->assertTrue($this->isEditable("toBasket"));

        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='white']"));
        $this->selectVariant("variants", 2, "white", "Selected combination: white");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 1, "S", "Selected combination: S, white");
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='lether']"));
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='material']"));
        $this->assertEquals("14 EN product šÄßüл S | white", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014-1-3", $this->getText("productArtnum"));
        $this->assertEquals("15,00 € *", $this->getText("productPrice"));
        $this->assertTrue($this->isEditable("toBasket"));

        $this->selectVariant("variants", 2, "black", "Selected combination: S, black");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->assertFalse($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='lether']"));
        $this->selectVariant("variants", 3, "material", "Selected combination: S, black, material");
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='white']"));
        $this->assertTrue($this->isEditable("toBasket"));

        $this->assertEquals("14 EN product šÄßüл S | black | material", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014-1-2", $this->getText("productArtnum"));
        $this->assertEquals("15,00 € *", $this->getText("productPrice"));
        $this->selectVariant("variants", 3, "lether", "Selected combination: S, black, lether");
        $this->assertEquals("14 EN product šÄßüл S | black | lether", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014-1-1", $this->getText("productArtnum"));
        $this->assertEquals("25,00 € *", $this->getText("productPrice"));
        $this->assertTrue($this->isEditable("toBasket"));

        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='M']"));
        $this->selectVariant("variants", 1, "M", "Selected combination: M");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 2, "red", "Selected combination: M, red");
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='lether']"));
        $this->assertEquals("14 EN product šÄßüл M | red", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014-2-4", $this->getText("productArtnum"));
        $this->assertEquals("15,00 € *", $this->getText("productPrice"));
        $this->assertTrue($this->isEditable("toBasket"));

        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='L']"));
        $this->assertFalse($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='S']"));
        $this->selectVariant("variants", 1, "S", "Selected combination: S, red");
        $this->assertTrue($this->isEditable("toBasket"));
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='lether']"));
        $this->selectVariant("variants", 2, "black", "Selected combination: S, black");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->assertFalse($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='lether']"));
        $this->selectVariant("variants", 3, "lether", "Selected combination: S, black, lether");
        $this->selectVariant("variants", 1, "L", "Selected combination: L, black, lether");
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//li[@class='js-disabled disabled']/a[text()='red']"));
        $this->assertEquals("14 EN product šÄßüл L | black | lether", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014-3-1", $this->getText("productArtnum"));
        $this->assertEquals("15,00 € *", $this->getText("productPrice"));
        $this->assertTrue($this->isEditable("toBasket"));
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        //reset button
        $this->click("//a[text()='Reset selection']");
        $this->waitForTextDisappear("Selected combination");
        $this->assertEquals("14 EN product šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Item #: 10014", $this->getText("productArtnum"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("productShortdesc"));
        $this->assertEquals("from 15,00 € *", $this->getText("productPrice"));
        $this->assertFalse($this->isEditable("toBasket"));

        $this->openBasket();
        $this->assertEquals("14 EN product šÄßüл, L | black | lether", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]//a")));
        $this->assertEquals("Item #: 10014-3-1", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]/div[2]")));
        $this->assertEquals("2", $this->getValue("am_1"));
        $this->assertFalse($this->isElementPresent("cartItem_2"));
        $this->assertEquals("30,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
    }

    /**
     * Checking Multidimensional variants functionality in lists
     * @group navigation
     * @group product
     */
    public function testFrontendMultidimensionalVariantsOnLists()
    {
        //multidimensional variants on
        //active product WHERE `OXID`='10014'
        $aArticleParams = array("oxactive" => 1);
        $this->callShopSC("oxArticle", "save", "10014", $aArticleParams,1);
        $this->openShop();
        $this->searchFor("10014");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertEquals("size[EN]:", $this->getText("//div[@id='variantselector_searchList_1']//label"));
        $this->assertEquals("S M L", $this->clearString($this->getText("//div[@id='variantselector_searchList_1']//ul")));
        $this->assertEquals("14 EN product šÄßüл", $this->getText("searchList_1"));
        $this->assertEquals("from 15,00 €", $this->getText("productPrice_searchList_1"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("//form[@name='tobasket.searchList_1']/div[2]/div[2]"));
        $this->assertTrue($this->isElementPresent("//form[@name='tobasket.searchList_1']//a[text()='more Info']"));
        $this->selectVariant("variantselector_searchList_1", 1, "M");
        $this->assertTrue($this->isTextPresent("Selected combination: M"));
        $this->assertEquals("size[EN]: M", $this->clearString($this->getText("//div[@id='variants']//p")));
        $this->click("//a[text()='Reset selection']");
        $this->waitForTextDisappear("Selected combination");

        $this->assertEquals("You are here: / Search result for \"10014\"", $this->getText("breadCrumb"));
        $this->assertEquals("14 EN product šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Art.No.: 10014", $this->getText("productArtnum"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("productShortdesc"));
        $this->assertEquals("from 15,00 € *", $this->getText("productPrice"));
        $this->assertFalse($this->isEditable("toBasket"));
    }

    /**
     * Checking Multidimensional variants functionality
     * @group navigation
     * @group product
     */
    public function testFrontendMultidimensionalVariantsOff()
    {
        //multidimensional variants off
        //selenium for bug #1427
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blUseMultidimensionVariants" => array("type" => "bool", "value" => '')));
        //active product WHERE `OXID`='10014'
        $aArticleParams = array("oxactive" => 1);
        $this->callShopSC("oxArticle", "save", "10014", $aArticleParams,1);
        $this->openShop();
        $this->searchFor("10014");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent("variantselector_searchList_1"));

        $this->assertEquals(
            "size[EN] | color | type: Choose variant S | black | lether S | black | material S | white S | red M | black | material M | white M | red L | black | lether L | black | material L | white",
            $this->clearString($this->getText("variantselector_searchList_1"))
        );
        $this->selectVariant("variantselector_searchList_1", 1, "S | black | material");

        $this->assertEquals("14 EN product šÄßüл S | black | material", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Selected combination: S | black | material"));
        $this->assertEquals(
            "Choose variant S | black | lether S | black | material S | white S | red M | black | material M | white M | red L | black | lether L | black | material L | white",
            $this->clearString($this->getText("//div[@id='variants']//ul"))
        );
        $this->assertTrue($this->isEditable("toBasket"));

        //10014-2-1: out of stock - offline
        $this->assertFalse($this->isElementPresent("//div[@id='variants']//ul//a[text()='M | black | lether']"));

        //10014-2-2: out of stock - not orderable
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//ul//a[text()='M | black | material']"));

        //making 10014-2-1 and 10014-2-2 variants in stock
        $aArticleParams = array("oxstock" => 1);
        $this->callShopSC("oxArticle", "save", "1001421", $aArticleParams);
        $this->callShopSC("oxArticle", "save", "1001422", $aArticleParams);
        $this->selectVariant("variants", 1, "S | white", "Selected combination: S | white");
        $this->assertEquals("14 EN product šÄßüл S | white", $this->getText("//h1"));
        $this->click("link=Reset selection");
        $this->waitForTextDisappear("Selected combination");
        $this->assertEquals("14 EN product šÄßüл", $this->getText("//h1"));
        $this->assertFalse($this->isEditable("toBasket"));
        $this->assertEquals(
            "S | black | lether S | black | material S | white S | red M | black | lether M | black | material M | white M | red L | black | lether L | black | material L | white",
            $this->clearString($this->getText("//div[@id='variants']//ul"))
        );
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//ul//a[text()='M | black | lether']"));
        $this->assertTrue($this->isElementPresent("//div[@id='variants']//ul//a[text()='M | black | material']"));
    }


    /**
     * Bundled product
     * @group navigation
     * @group user
     * @group product
     */
    public function testFrontendBundledProduct()
    {
            $this->executeSql("UPDATE `oxarticles` SET  `OXBUNDLEID` = '1003' WHERE `OXID` = '1000';");
            $this->openShop();
            $this->searchFor("1000");
            $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
            $this->assertFalse($this->isElementPresent("tobasketsearchList_2"));
            $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
            $this->openBasket();
            $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));
            $this->assertEquals("Art.No.: 1000", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
            $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div[1]"));
            $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
            $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_2']/td[5]"));
            $this->assertEquals("Grand Total: 50,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
    }

    /**
     * Product details. checking product price A, B, C
     * @group navigation
     * @group product
     */
    public function testFrontendPriceABC()
    {
        $this->openShop();
        //option "Use normal article price instead of zero A, B, C price" is ON
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertEquals("50,00 € *", $this->clearString($this->getText("productPrice_productList_1")));
        $this->assertEquals("100,00 €", $this->clearString($this->getText("productPrice_productList_2")));
        $this->loginInFrontend("birute0a@nfq.lt", "userAuser");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertEquals("100,00 €", $this->clearString($this->getText("productPrice_productList_2")));
        $this->clickAndWait("productList_1");
        $this->assertTrue($this->isElementPresent("breadCrumb"));
        $this->assertEquals("17,50 €/kg", $this->getText("productPriceUnit"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("productList_2");
        $this->clickAndWait("toBasket");
        $this->searchFor("1002");
        $this->assertEquals("from 45,00 €", $this->getText("productPrice_searchList_1"));
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("from 45,00 € *", $this->getText("productPrice"));
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("75,00 € *", $this->getText("productPrice"));
        $this->loginInFrontend("birute0a@nfq.lt", "userAuser");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->type("am_2", "3");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("96,00 € \n101,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");
        $this->assertEquals("63,00 € \n70,00 €", $this->getText("//tr[@id='cartItem_2']/td[6]"),"price with discount not shown in basket");
        if (!isSUBSHOP) {  //staffepreis is not inherited to subshop
            $this->type("am_2", "7");
            $this->clickAndWait("basketUpdate");
            $this->assertEquals("50,40 € \n56,00 €", $this->getText("//tr[@id='cartItem_2']/td[6]"),"price with discount not shown in basket");
        }
        //checking price C
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->loginInFrontend("birute0c@nfq.lt", "userCuser");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("27,50 €/kg", $this->getText("productPriceUnit"));
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->clickAndWait("//button[@id='toBasket']");
        $this->openBasket();
        $this->type("am_1", "3");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("67,50 € \n75,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");

        //checking price B
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->loginInFrontend("birute0b@nfq.lt", "userBuser");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("22,50 €/kg", $this->getText("productPriceUnit"));
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->clickAndWait("//button[@id='toBasket']");
        $this->openBasket();
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        if (!isSUBSHOP) {  //staffepreis(stock price for product) is not inherited to subshp
            $this->assertEquals("67,50 € \n75,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");
        } else {
            $this->assertEquals("76,50 € \n85,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");
        }
        //option "Use normal article price instead of zero A, B, C price" is OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blOverrideZeroABCPrices" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "Line");
        $this->loginInFrontend("birute0a@nfq.lt", "userAuser");
        $this->assertEquals("35,00 € *", $this->clearString($this->getText("productPrice_productList_1")));
        $this->assertEquals("0,00 €", $this->clearString($this->getText("productPrice_productList_2")));
    }

    /**
     * checking if after md variants selection in details page all other js are still working corectly
     * @group navigation
     * @group product
     */
    public function testMdVariantsAndJs()
    {
        $this->openShop();
        $this->searchFor("3571");
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Kuyichi Jeans CANDY", $this->getText("//h1"));
        $this->selectVariant("variants", 1, "W 31/L 34", "Selected combination: W 31/L 34");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 2, "Dark Blue", "Selected combination: W 31/L 34, Dark Blue");
        $this->assertFalse($this->isVisible("priceAlarmLink"));
        $this->click("productLinks");
        $this->waitForItemAppear("priceAlarmLink");
        $this->assertFalse($this->isVisible("pa[price]"));
        $this->click("priceAlarmLink");
        $this->waitForItemAppear("pa[price]");
        $this->assertFalse($this->isVisible("tags"));
        $this->click("//ul[@id='itemTabs']/li[4]/a");
        $this->waitForItemAppear("tags");
        $this->assertFalse($this->isVisible("attributes"));
        $this->click("//ul[@id='itemTabs']/li[2]/a");
        $this->waitForItemAppear("attributes");
        $this->assertFalse($this->isVisible("description"));
        $this->click("//ul[@id='itemTabs']/li[1]/a");
        $this->waitForItemAppear("description");
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("loginBoxOpener");
        $this->waitForItemAppear("loginBox");
        $this->assertFalse($this->isElementPresent("basketFlyout"));
        $this->clickAndWait("toBasket");
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("minibasketIcon");
        $this->waitForItemAppear("basketFlyout");
        $this->assertTrue($this->isTextPresent("Selected combination: W 31/L 34, Dark Blue"));
        $this->click("link=Reset selection");
        $this->waitForTextDisappear("Selected combination: W 31/L 34, Dark Blue");
    }

}

