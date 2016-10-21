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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** Frontend: product information/ details related tests */
class ProductInfoFrontendTest extends FrontendTestCase
{
    /**
     * Orders: buying more items than available
     *
     * @group product
     */
    public function testFrontendEuroSignInTitle()
    {
        if (isSUBSHOP) {
            $this->markTestSkipped('This test case is only actual when SubShops are available.');
        }
        $this->callShopSC('oxArticle', 'save', 1002, array('oxtitle' => '[DE 2] Test product 2 šÄßüл €'), array(), null, 'de');
        $this->clearCache();
        $this->openShop();
        $this->switchLanguage('Deutsch');
        $this->searchFor("1002");
        $this->assertEquals("[DE 2] Test product 2 šÄßüл €", $this->getText("searchList_1"));
    }

    /**
     * Product details. test for checking main product details as info, prices, buying etc
     *
     * @group product
     */
    public function testFrontendDetailsNavigationAndInfo()
    {
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[2]//a");

        //navigation between products (in details page)
        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%PRODUCT% 2 %OF% 4", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->clickAndWait("//div[@id='detailsItemsPager']/a[text()='%NEXT_PRODUCT%']");
        $this->assertEquals("%PRODUCT% 3 %OF% 4", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='detailsItemsPager']/a[text()='%PREVIOUS_PRODUCT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("%PRODUCT% 2 %OF% 4", $this->getText("//div[@id='detailsItemsPager']/span"));

        //product info
        $this->_assertArticle('Test product 1 [EN] šÄßüл', 'Test product 1 short desc [EN] šÄßüл', '1001', '100,00 € *');
        $this->assertTextPresent("%MESSAGE_NOT_ON_STOCK%");

        $this->assertTextPresent("%AVAILABLE_ON% 2008-01-01");
        $this->assertElementPresent("productSelections");
        $this->assertElementPresent("//div[@id='productSelections']//ul");
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='productSelections']//ul"));
        $this->assertTextPresent("%REDUCED_FROM_2% 150,00 €");
        $this->assertEquals("Test product 1 long description [EN] šÄßüл", $this->getText("//*[@id='description']"));

        $this->assertEquals("%SPECIFICATION%", $this->clearString($this->getText("//ul[@id='itemTabs']/li[2]")));
        $this->click("//ul[@id='itemTabs']/li[2]/a");
        $this->waitForItemAppear("attributes");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[1]/th"));
        $this->assertEquals("attr value 11 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[1]/td"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[2]/th"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[2]/td"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[3]/th"));
        $this->assertEquals("attr value 12 [EN] šÄßüл", $this->getText("//div[@id='attributes']//tr[3]/td"));


        //buying product
        //TODO: Selenium refactor with basket construct
        $this->click("//div[@id='productSelections']//ul/li[2]/a");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");

        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertEquals("100", $this->getValue("searchParam"));
    }

    /**
     * Product details. test for checking main product details as info, prices, buying etc
     *
     * @group product
     */
    public function testFrontendDetailsAdditionalInfo()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->clearCache();
        $this->openShop();
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']//a");
        //staffelpreis
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("75,00 € *", $this->getText("productPrice"));

        if ($this->getTestConfig()->getShopEdition() === 'EE' && !isSUBSHOP) {  //staffepreis is not inherited to subshp
            $this->assertElementPresent("amountPrice");
            $this->click("amountPrice");
            $this->waitForItemAppear("priceinfo");
            $this->assertEquals("2", $this->getText("//ul[@id='priceinfo']/li[3]/label"));
            $this->assertEquals("75,00 €", $this->getText("//ul[@id='priceinfo']/li[3]/span"));
            $this->assertEquals("6", $this->getText("//ul[@id='priceinfo']/li[4]/label"));
            $this->assertEquals("20 % %DISCOUNT%", $this->getText("//ul[@id='priceinfo']/li[4]/span"));
            $this->click("amountPrice");
            $this->waitForItemDisappear("priceinfo");
        }

        //review when user not logged in
        $this->assertElementPresent("//h4[text()='%WRITE_PRODUCT_REVIEW%']");
        $this->assertTextPresent("%NO_REVIEW_AVAILABLE%");
        $this->assertEquals("%MESSAGE_LOGIN_TO_WRITE_REVIEW%", $this->getText("reviewsLogin"));

        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertEquals("%LOGIN_TO_ACCESS_WISH_LIST%", $this->getText("loginToNotice"));
        $this->assertEquals("%LOGIN_TO_ACCESS_GIFT_REGISTRY%", $this->getText("loginToWish"));

        //compare link
        $this->assertElementNotPresent("//p[@id='servicesTrigger']/span");
        $this->clickAndWait("addToCompare");
        $this->assertEquals("1", $this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertEquals("%REMOVE_FROM_COMPARE_LIST%", $this->getText("removeFromCompare"));
        $this->clickAndWait("removeFromCompare");
        $this->assertElementNotPresent("//p[@id='servicesTrigger']/span");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertEquals("%COMPARE%", $this->getText("addToCompare"));
        $this->clickAndWait("addToCompare");
        //check if compare products are not gone after you login
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertEquals("2", $this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->assertEquals("%PRODUCT%: 1", $this->clearString($this->getText("//section[@id='content']//div[2]/dl[3]/dd")));
    }

    /**
     * Performance option "Show compare list" is disabled
     *
     * @group product
     */
    public function testFrontendDisabledCompare()
    {
        //(Use compare->callShopSC("oxConfig", null, null, array("bl_showCompareList" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));
        $this->callShopSC("oxConfig", null, null, array("bl_showCompareList" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));
        $this->clearCache();
        $this->openShop();
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertElementNotPresent("addToCompare");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertElementNotPresent("addToCompare");
    }


    /**
     * Check is Compare options works corectly
     *
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
        $this->clickAndWait("//ul[@id='services']/li[4]/a");
        $this->assertElementPresent('productPrice_1');
        $this->assertElementPresent("//a[text()='Test product 0 [EN] šÄßüл ']");
        $this->assertElementPresent("productPrice_2");
        $this->assertElementPresent("//a[text()='Kite CORE GTS ']");
        $this->assertElementPresent("productPrice_3");
        $this->assertElementPresent("//a[text()='Harness MADTRIXX ']");

        $this->clickAndWait("link=%HOME%");
        $this->clickAndWait('removeCmp_newItems_1');
        $this->searchFor("1");
        $this->clickAndWait('removeCmp_searchList_1');
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait('removeCmp_productList_1');
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->assertElementNotPresent('productPrice_1');
        $this->assertElementNotPresent('productPrice_2');
        $this->assertElementNotPresent('productPrice_3');

        $this->assertTextPresent("%MESSAGE_SELECT_AT_LEAST_ONE_PRODUCT%");
    }

    /**
     * Product details. Sending recommendation of product
     *
     * @group product
     */
    public function testFrontendDetailsRecommend()
    {
        //In admin Set option (Installed GDLib Version) if "value" => ""
        $this->callShopSC("oxConfig", null, null, array("iUseGDVersion" => array("type" => "str", "value" => 0)));

        $this->clearCache();
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']//a");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        //recommend link
        $this->clickAndWait("suggest");
        $this->assertEquals("%YOU_ARE_HERE%: / %RECOMMEND_PRODUCT%", $this->getText("breadCrumb"));
        $this->assertEquals("%RECOMMEND_PRODUCT%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_ENTER_YOUR_ADDRESS_AND_MESSAGE%");
        $this->assertEquals("", $this->getValue("editval[rec_name]"));
        $this->assertEquals("", $this->getValue("editval[rec_email]"));
        $this->assertEquals("", $this->getValue("editval[send_name]"));
        $this->assertEquals("", $this->getValue("editval[send_email]"));
        $this->assertElementPresent("editval[send_message]");
        $this->click("//button[text()='%SEND%']");
        $this->waitForText("%ERROR_MESSAGE_INPUT_NOTALLFIELDS%");
        $this->type("editval[rec_name]", "Test User");
        $this->type("editval[rec_email]", "example@oxid-esales.dev");
        $this->type("editval[send_name]", "user");
        $this->type("editval[send_email]", "example_test@oxid-esales.dev");
        $this->type("editval[send_subject]", "Have a look at: Test product 1 [EN] šÄßüл");
        $this->clickAndWait("//button[text()='%SEND%']");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Product details. Testing price alert
     *
     * @group product
     */
    public function testFrontendDetailsPriceAlert()
    {
        //In admin Set option (Installed GDLib Version) if "value" => ""
        $this->callShopSC("oxConfig", null, null, array("iUseGDVersion" => array("type" => "str", "value" => 0)));

        $this->openArticle( 1001, true );
        $this->assertElementPresent("link=%PRICE_ALERT%");
        $this->click("productLinks");
        $this->waitForItemAppear("//a[@id='priceAlarmLink']");
        $this->click("//a[@id='priceAlarmLink']");
        $this->type("pa[email]", "example_test@oxid-esales.dev");
        $this->type("pa[price]", "99.99");
        $this->clickAndWait("//form[@name='pricealarm']//button");
        $this->assertTextPresent("We'll inform you as soon as the price decreases below 99,99");
        $this->click("//ul[@id='itemTabs']//a[text()='%DESCRIPTION%']");
        $this->waitForItemAppear("//*[@id='description']");
        $this->click("link=%PRICE_ALERT%");

        //disabling price alert for product(1001)
        $this->callShopSC("oxArticle", "save", "1001", array("oxblfixedprice" => 1), null, 1);
        $this->clearTemp();

        $this->openArticle( 1001 );
        $this->assertElementNotPresent("link=%PRICE_ALERT%");
        $this->assertElementNotPresent("pa[email]");
        $this->assertElementNotPresent("pa[price]");

        $aPriceAlarmData['oxemail'] = 'example_test@oxid-esales.dev';
        $aPriceAlarmData['oxprice'] = '99.99';
        $aPriceAlarmData['oxcurrency'] = 'EUR';
        $aPriceAlarmData['oxartid'] = '1001';
        $oValidator = $this->getObjectValidator();
        $this->assertTrue($oValidator->validate('oxpricealarm', $aPriceAlarmData), $oValidator->getErrorMessage());
    }

    /**
     * Product details. testing variants
     *
     * @group product
     */
    public function testFrontendDetailsVariants()
    {
        $this->openArticle( 1002, true );
        $this->_assertArticle('', '', '1002', "%PRICE_FROM% 55,00 € *", false);
        $this->_assertReview(array("review for parent product šÄßüл"));

        $this->selectVariant("variants", 1, "var1 [EN] šÄßüл", "var1 [EN] šÄßüл");
        $this->_assertArticle('Test product 2 [EN] šÄßüл var1 [EN] šÄßüл', '', '1002-1', '55,00 € *');
        $this->_assertReview(array('review for var1 šÄßüл', 'review for parent product šÄßüл'));
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->assertEquals("2 x Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл 110,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("%TOTAL% 110,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));
        $this->type("amountToBasket", "1");
        $this->clickAndWait("toBasket");
        $this->assertEquals("3 x Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл 165,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("%TOTAL% 165,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));

        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "var2 [EN] šÄßüл");
        $this->_assertArticle('Test product 2 [EN] šÄßüл var2 [EN] šÄßüл', '', '1002-2', '67,00 € *');
        $this->_assertReview(array("review for var2 šÄßüл", "review for parent product šÄßüл"));

        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        $this->type("amountToBasket", "1");
        $this->clickAndWait("toBasket");
        $this->assertEquals("6", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertEquals("3 x Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл 165,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("3 x Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл 201,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[2]")));
        $this->assertEquals("%TOTAL% 366,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));
    }

    /**
     * Product details. testing variants. testing ptions related to parent product
     *
     * @group product
     */
    public function testFrontendDetailsVariantsParent()
    {
        //variants reviews will be shown for parent product active "Show Variant Ratings for "Parent" Product "
        //setting article parent as buyable
        $this->callShopSC("oxConfig", null, null, array("blShowVariantReviews" => array("type" => "bool", "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("blVariantParentBuyable" => array("type" => "bool", "value" => 'true')));
        $this->clearCache();
        $this->openShop();
        $this->searchFor("1002");

        $this->clickAndWait("searchList_1");
        $this->_assertArticle('Test product 2 [EN] šÄßüл', '', '1002', '55,00 € *');
        $this->_assertReview(array('review for var2 šÄßüл', 'review for var1 šÄßüл', 'review for parent product šÄßüл'));

        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->clickAndWait("toBasket");
        $this->assertEquals("Test product 2 [EN] šÄßüл 55,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']//li[1]")));
        $this->assertEquals("%TOTAL% 55,00 €", $this->clearString($this->getText("//div[@id='basketFlyout']/p[2]")));

        $this->selectVariant("variants", 1, "var1 [EN] šÄßüл", "var1 [EN] šÄßüл");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("//h1"));
        $this->_assertReview(array('review for var1 šÄßüл', 'review for parent product šÄßüл'));

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
        $this->openArticle( 1000, true );
        $this->assertEquals("%ACCESSORIES%", $this->getText("//div[@id='accessories']/h3"));
        $this->assertEquals("Test product 2 [EN] šÄßüл %PRICE_FROM% 55,00 €", $this->clearString($this->getText("//div[@id='accessories']/ul/li[2]/a")));

        $this->clickAndWait("//div[@id='accessories']/ul/li[2]/a");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Product's similar products
     *
     * @group product
     */
    public function testFrontendSimilarProducts()
    {
        $this->openArticle( 1000, true );
        $this->assertEquals( "Test product 0 [EN] šÄßüл", $this->getText( "//h1" ) );
        $this->assertEquals( "%SIMILAR_PRODUCTS%", $this->getText( "//div[@id='similar']/h3" ) );
        $this->assertEquals( "Test product 1 [EN] šÄßüл 100,00 €", $this->clearString( $this->getText( "//div[@id='similar']/ul/li[2]/a" ) ) );
        $this->clickAndWait( "//div[@id='similar']/ul/li[2]/a" );
        $this->assertEquals( "Test product 1 [EN] šÄßüл", $this->getText( "//h1" ) );
        $this->assertEquals( "%SIMILAR_PRODUCTS%", $this->getText( "//div[@id='similar']/h3" ) );
        $this->assertEquals( "Test product 0 [EN] šÄßüл 50,00 € *", $this->clearString( $this->getText( "//div[@id='similar']/ul/li[2]/a" ) ) );
        $this->clickAndWait( "//div[@id='similar']/ul/li[2]/a" );
        $this->assertEquals( "Test product 0 [EN] šÄßüл", $this->getText( "//h1" ) );
        $this->assertEquals( "%SIMILAR_PRODUCTS%", $this->getText( "//div[@id='similar']/h3" ) );
        $this->assertEquals( "Test product 1 [EN] šÄßüл 100,00 €", $this->clearString( $this->getText( "//div[@id='similar']/ul/li[2]/a" ) ) );
    }

    /**
     * Product's crossselling
     *
     * @group product
     */
    public function testFrontendCrossselling()
    {
        $this->openArticle(1000, true);
        $this->assertEquals("%HAVE_YOU_SEEN%", $this->getText("//div[@id='cross']/h3"));
        $this->assertEquals("Test product 3 [EN] šÄßüл 75,00 € *", $this->clearString($this->getText("//div[@id='cross']/ul/li[2]/a")));
        $this->clickAndWait("//div[@id='cross']/ul/li[2]/a");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));
    }


    /**
     * Checking Multidimensional variants functionality
     *
     * @group main
     */
    public function testFrontendMultidimensionalVariantsOnDetailsPage()
    {
        //multidimensional variants on
        //active product WHERE `OXID`='10014'
        $aArticleParams = array("oxactive" => 1);
        $this->callShopSC("oxArticle", "save", "10014", $aArticleParams, null, 1);

        $this->openArticle( 10014 );
        $this->_assertArticle('14 EN product šÄßüл', '13 EN description šÄßüл', '10014', 'from 15,00 € *', false);

        $this->assertEquals("size[EN]:", $this->getText("//div[@id='variants']/div//label"));
        $this->assertEquals("S M L", $this->getText("//div[@id='variants']/div//ul"));
        $this->assertTrue($this->isVisible("//div[@id='variants']/div//p"));
        $this->assertEquals("color:", $this->getText("//div[@id='variants']/div[2]//label"));
        $this->assertEquals("black white red", $this->getText("//div[@id='variants']/div[2]//ul"));
        $this->assertTrue($this->isVisible("//div[@id='variants']/div[2]//p"));
        $this->assertEquals("type:", $this->getText("//div[@id='variants']/div[3]//label"));
        $this->assertEquals("lether material", $this->getText("//div[@id='variants']/div[3]//ul"));
        $this->assertTrue($this->isVisible("//div[@id='variants']/div[3]//p"));

        $this->selectVariant("variants", 1, "S", "S");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 2, "black", "S, black");
        $this->assertFalse($this->isEditable("toBasket"));

        $this->selectVariant("variants", 3, "lether", "S, black, lether");
        $this->_assertArticle('14 EN product šÄßüл S | black | lether', '', '10014-1-1', '25,00 € *');
        $this->_assertVariants(array('white' => true));

        $this->selectVariant("variants", 2, "white", "white");
        $this->assertFalse($this->isEditable("toBasket"));

        $this->selectVariant("variants", 1, "S", "S, white");
        $this->_assertArticle('14 EN product šÄßüл S | white', '', '10014-1-3', '15,00 € *');
        $this->_assertVariants(array('lether' => true, 'material' => true));

        $this->selectVariant("variants", 2, "black", "S, black");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->_assertVariants(array('lether' => false));

        $this->selectVariant("variants", 3, "material", "S, black, material");
        $this->_assertArticle('14 EN product šÄßüл S | black | material', '', '10014-1-2', '15,00 € *');
        $this->_assertVariants(array('white' => true));

        $this->selectVariant("variants", 3, "lether", "S, black, lether");
        $this->_assertArticle('14 EN product šÄßüл S | black | lether', '', '10014-1-1', '25,00 € *');
        $this->_assertVariants(array('M' => true));

        $this->selectVariant("variants", 1, "M", "M");
        $this->assertFalse($this->isEditable("toBasket"));

        $this->selectVariant("variants", 2, "red", "M, red");
        $this->_assertArticle('14 EN product šÄßüл M | red', '', '10014-2-4', '15,00 € *');
        $this->_assertVariants(array('lether' => true, 'L' => true, 'S' => false));

        $this->selectVariant("variants", 1, "S", "S, red");
        $this->assertTrue($this->isEditable("toBasket"));
        $this->_assertVariants(array('lether' => true));

        $this->selectVariant("variants", 2, "black", "S, black");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->_assertVariants(array('lether' => false));

        $this->selectVariant("variants", 3, "lether", "S, black, lether");
        $this->selectVariant("variants", 1, "L", "L, black, lether");
        $this->_assertArticle('14 EN product šÄßüл L | black | lether', '', '10014-3-1', '15,00 € *');
        $this->_assertVariants(array('red' => true));

        $this->type("amountToBasket", "2");
        $this->clickAndWait("toBasket");
        //reset button
        $this->click("//a[text()='%RESET_SELECTION%']");
        $this->waitForTextDisappear("%SELECTED_COMBINATION%");
        $this->assertEquals("14 EN product šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%PRODUCT_NO%: 10014", $this->getText("productArtnum"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("productShortdesc"));
        $this->assertEquals("%PRICE_FROM% 15,00 € *", $this->getText("productPrice"));
        $this->assertFalse($this->isEditable("toBasket"));

        $this->openBasket();
        $this->assertEquals("14 EN product šÄßüл, L | black | lether", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]//a")));
        $this->assertEquals("%PRODUCT_NO%: 10014-3-1", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]/div[2]")));
        $this->assertEquals("2", $this->getValue("am_1"));
        $this->assertElementNotPresent("cartItem_2");
        $this->assertEquals("30,00 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
    }

    /**
     * Checking Multidimensional variants functionality in lists
     *
     * @group product
     */
    public function testFrontendMultidimensionalVariantsOnLists()
    {
        //multidimensional variants on
        //active product WHERE `OXID`='10014'
        $aArticleParams = array("oxactive" => 1);
        $this->callShopSC("oxArticle", "save", "10014", $aArticleParams, null, 1);
        $this->clearCache();
        $this->openShop();
        $this->searchFor("10014");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertEquals("size[EN]:", $this->getText("//div[@id='variantselector_searchList_1']//label"));
        $this->assertEquals("S M L", $this->clearString($this->getText("//div[@id='variantselector_searchList_1']//ul")));
        $this->assertEquals("14 EN product šÄßüл", $this->getText("searchList_1"));
        $this->assertEquals("%PRICE_FROM% 15,00 €", $this->getText("productPrice_searchList_1"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("//form[@name='tobasket.searchList_1']/div[2]/div[2]"));
        $this->assertElementPresent("//form[@name='tobasket.searchList_1']//a[text()='%MORE_INFO%']");
        $this->selectVariant("variantselector_searchList_1", 1, "M", "M");
        $this->assertEquals("size[EN]: M", $this->clearString($this->getText("//div[@id='variants']/div/p")));
        $this->waitForJQueryToFinish();
        $this->click("//a[text()='%RESET_SELECTION%']");
        $this->waitForTextDisappear("%SELECTED_COMBINATION%");

        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"10014\"", $this->getText("breadCrumb"));

        $this->_assertArticle('14 EN product šÄßüл', '13 EN description šÄßüл', '10014', 'from 15,00 € *', false);
    }

    /**
     * Checking Multidimensional variants functionality
     *
     * @related bug #1427
     *
     * @group product
     */
    public function testFrontendMultidimensionalVariantsOff()
    {
        //multidimensional variants off
        $this->callShopSC("oxConfig", null, null, array("blUseMultidimensionVariants" => array("type" => "bool", "value" => '')));
        $this->callShopSC("oxArticle", "save", "10014", array("oxactive" => 1), null, 1);

        $this->clearCache();
        $this->openShop();
        $this->searchFor("10014");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("variantselector_searchList_1");

        $this->assertEquals(
            "size[EN] | color | type: %CHOOSE_VARIANT% S | black | lether S | black | material S | white S | red M | black | material M | white M | red L | black | lether L | black | material L | white",
            $this->clearString($this->getText("variantselector_searchList_1"))
        );
        $this->selectVariant("variantselector_searchList_1", 1, "S | black | material", "S | black | material");
        $this->_assertArticle("14 EN product šÄßüл S | black | material", '', '', '', true);

        $this->assertEquals(
            "%CHOOSE_VARIANT% S | black | lether S | black | material S | white S | red M | black | material M | white M | red L | black | lether L | black | material L | white",
            $this->clearString($this->getText("//div[@id='variants']//ul"))
        );

        //10014-2-1: out of stock - offline
        $this->assertElementNotPresent("//div[@id='variants']//ul//a[text()='M | black | lether']");

        //10014-2-2: out of stock - not orderable
        $this->assertElementPresent("//div[@id='variants']//ul//a[text()='M | black | material']");

        //making 10014-2-1 and 10014-2-2 variants in stock
        $this->callShopSC("oxArticle", "save", "1001421", array("oxstock" => 1), null, 1);
        $this->callShopSC("oxArticle", "save", "1001422", array("oxstock" => 1), null, 1);

        $this->selectVariant("variants", 1, "S | white", "S | white");
        $this->assertEquals("14 EN product šÄßüл S | white", $this->getText("//h1"));
        $this->click("link=%RESET_SELECTION%");
        $this->waitForTextDisappear("%SELECTED_COMBINATION%");
        $this->_assertArticle("14 EN product šÄßüл", '', '', '', false);
        $this->assertEquals(
            "S | black | lether S | black | material S | white S | red M | black | lether M | black | material M | white M | red L | black | lether L | black | material L | white",
            $this->clearString($this->getText("//div[@id='variants']//ul"))
        );
        $this->assertElementPresent("//div[@id='variants']//ul//a[text()='M | black | lether']");
        $this->assertElementPresent("//div[@id='variants']//ul//a[text()='M | black | material']");
    }

    /**
     * Bundled product
     *
     * @group product
     */
    public function testFrontendBundledProduct()
    {
        $this->executeSql("UPDATE `oxarticles` SET  `OXBUNDLEID` = '1003' WHERE `OXID` = '1000';");

        $this->clearCache();
        $this->addToBasket("1000");

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));
        $this->assertEquals("%PRODUCT_NO%: 1000", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div[1]"));
        $this->assertEquals("%PRODUCT_NO%: 1003", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_2']/td[5]"));
        $this->assertEquals("50,00 €", $this->getText("basketGrandTotal"), "Garnd total price chenged or did't displayed");
    }

    /**
     * Product details. checking product price A, B, C
     *
     * @group product
     */
    public function testFrontendPriceABC()
    {
        $this->callShopSC("oxConfig", null, null, array('blPerfNoBasketSaving' => array('type' => 'bool', 'value' => false)));
        $this->openShop();
        //option "Use normal article price instead of zero A, B, C price" is ON
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertEquals("50,00 € *", $this->clearString($this->getText("productPrice_productList_1")));
        $this->assertEquals("100,00 €", $this->clearString($this->getText("productPrice_productList_2")));
        $this->loginInFrontend("example0a@oxid-esales.dev", "userAuser");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertEquals("100,00 €", $this->clearString($this->getText("productPrice_productList_2")));
        $this->clickAndWait("productList_1");
        $this->assertElementPresent("breadCrumb");
        $this->assertEquals("2 kg | 17,50 €/kg", $this->getText("productPriceUnit"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("productList_2");
        $this->clickAndWait("toBasket");
        $this->searchFor("1002");
        $this->assertEquals("%PRICE_FROM% 45,00 €", $this->getText("productPrice_searchList_1"));
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("%PRICE_FROM% 45,00 € *", $this->getText("productPrice"));
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
        $this->searchFor("1003");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("75,00 € *", $this->getText("productPrice"));
        $this->loginInFrontend("example0a@oxid-esales.dev", "userAuser");
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
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->loginInFrontend("example0c@oxid-esales.dev", "userCuser");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("2 kg | 27,50 €/kg", $this->getText("productPriceUnit"));

        $this->addToBasket( "1003", 3 );

        $this->assertEquals("67,50 € \n75,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");

        //checking price B
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->loginInFrontend("example0b@oxid-esales.dev", "userBuser");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("2 kg | 22,50 €/kg", $this->getText("productPriceUnit"));

        $this->addToBasket( "1003", 2 );

        if (!isSUBSHOP) {  //staffepreis(stock price for product) is not inherited to subshp
            $this->assertEquals("67,50 € \n75,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");
        } else {
            $this->assertEquals("76,50 € \n85,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");
        }
        //option "Use normal article price instead of zero A, B, C price" is OFF
        $this->callShopSC("oxConfig", null, null, array("blOverrideZeroABCPrices" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "%line%");
        $this->loginInFrontend("example0a@oxid-esales.dev", "userAuser");
        $this->assertEquals("35,00 € *", $this->clearString($this->getText("productPrice_productList_1")));
        $this->assertEquals("0,00 €", $this->clearString($this->getText("productPrice_productList_2")));
    }

    /**
     * checking if after md variants selection in details page all other js are still working correctly
     *
     * @group product
     */
    public function testMdVariantsAndJs()
    {
        $this->openShop();
        $this->searchFor("3571");
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Kuyichi Jeans CANDY", $this->getText("//h1"));
        $this->selectVariant("variants", 1, "W 31/L 34", "W 31/L 34");
        $this->assertFalse($this->isEditable("toBasket"));
        $this->selectVariant("variants", 2, "Dark Blue", "W 31/L 34, Dark Blue");
        $this->assertFalse($this->isVisible("priceAlarmLink"));
        $this->click("productLinks");
        $this->waitForItemAppear("priceAlarmLink");
        $this->assertFalse($this->isVisible("pa[price]"));
        $this->click("priceAlarmLink");
        $this->waitForItemAppear("pa[price]");
        $this->assertFalse($this->isVisible("attributes"));
        $this->click("//ul[@id='itemTabs']/li[2]/a");
        $this->waitForItemAppear("attributes");
        $this->assertFalse($this->isVisible("description"));
        $this->click("//ul[@id='itemTabs']/li[1]/a");
        $this->waitForItemAppear("//*[@id='description']");
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("loginBoxOpener");
        $this->waitForItemAppear("loginBox");
        $this->assertElementNotPresent("basketFlyout");
        $this->clickAndWait("toBasket");
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("minibasketIcon");
        $this->waitForItemAppear("basketFlyout");
        $this->assertTextPresent("%SELECTED_COMBINATION%: W 31/L 34, Dark Blue");
        $this->click("link=%RESET_SELECTION%");
        $this->waitForTextDisappear("%SELECTED_COMBINATION%: W 31/L 34, Dark Blue");
    }

    /**
     * Asserts that opened article information is correct
     *
     * @param string $sTitle article title
     * @param string $sDescription article short description
     * @param string $sArtNr article number
     * @param string $sPrice price, including currency and other characters belonging to it, like "15,00 € *"
     * @param bool $blToBasketActive whether toBasket button is active
     */
    private function _assertArticle($sTitle, $sDescription = '', $sArtNr = '', $sPrice = '', $blToBasketActive = true)
    {
        $sTitle ? $this->assertEquals($sTitle, $this->getText("productTitle")) : '';
        $sDescription ? $this->assertEquals($sDescription, $this->getText("productShortdesc")) : '';
        $sArtNr ? $this->assertEquals("%PRODUCT_NO%: $sArtNr", $this->getText("productArtnum")) : '';
        $sPrice ? $this->assertEquals($sPrice, $this->getText("productPrice")) : '';

        $sAssert = $blToBasketActive ? "assertTrue" : "assertFalse";
        $this->$sAssert($this->isEditable("toBasket"));
    }

    /**
     * Asserts whether variants are present
     *
     * @param string $sId
     * @param array $aVariants array(sVariantName => blVisibility, ...)
     */
    private function _assertVariants($aVariants, $sId = 'variants')
    {
        foreach ($aVariants as $sVariant => $blVisibility) {
            $sAssert = $blVisibility ? "assertElementPresent" : "assertElementNotPresent";
            $this->$sAssert("//div[@id='$sId']//li[@class='js-disabled disabled']/a[text()='$sVariant']");
        }
    }

    /**
     * Asserts that given reviews exists by the given order and no more reviews are present
     *
     * @param array $aReviews array(sReview, ...)
     */
    private function _assertReview($aReviews)
    {
        $aReviews = is_array($aReviews) ? $aReviews : array($aReviews);
        foreach ($aReviews as $iKey => $sReview) {
            $this->assertEquals($sReview, $this->getText("reviewText_" . ($iKey + 1)));
        }
        $this->assertElementNotPresent("reviewText_" . (count($aReviews) + 1));
    }
}

