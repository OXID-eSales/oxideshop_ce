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
 * @package main
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'acceptance/oxidAdditionalSeleniumFunctions.php';

/**
 * FactFinder related tests
 *
 */
class AcceptanceEfire_factfinderTestBasic extends oxidAdditionalSeleniumFunctions
{
    /**
     * tests setUp
     *
     * @param boolean $skipDemoData defines to insert or skip selenium demodata insertion
     *
     * @return null
     */
    public function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ eFire modules for eShop ----------------------------------

    /**
     * factfinder search. PE version (demodata differs for PE and EE)
     *
     * @group factfinder
     *
     * @return null
     */
    public function testFactfinderGeneral()
    {
            //testing search
            $this->openShop();
            $this->clickAndWait("test_Lang_Deutsch");
            $this->type("searchparam", "*");
            $this->clickAndWait("test_searchGo");
            $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
            $this->assertTrue($this->isElementPresent("test_smallHeader"));
            $this->assertEquals("51 Treffer für \"*\"", $this->getText("test_smallHeader"));
            $this->assertEquals("6", $this->getText("test_PageNrTop_6"));
            $this->assertTrue($this->isElementPresent("test_cntr_10"));
            $this->assertEquals("*", $this->getValue("searchparam"));

            //price is displayed on left side, before category price
            $this->assertTrue($this->isElementPresent("link=Preis"));
            $this->assertTrue($this->isElementPresent("link=< 25,00 (6)"));
            $this->assertTrue($this->isElementPresent("link=25,00 - 99,99 (15)"));
            $this->assertTrue($this->isElementPresent("link=100,00 - 249,99 (6)"));
            $this->assertTrue($this->isElementPresent("link=250,00 - 499,99 (7)"));
            $this->assertTrue($this->isElementPresent("link=>= 500,00 (17)"));

            //vendor and categories are displayed as selectlist in top of the page
            $this->assertEquals("Bitte auswählen Kiteboarding (24) Bekleidung (17) Wakeboarding (8) Angebote (2)", $this->getText("query_0"));
            $this->assertEquals("Bitte auswählen Kuyichi (10) Liquid Force Kite (7) Core Kiteboarding (3) ION (3) Naish (3) RRD (3) Cabrinha (2) NPX (2) Flyboards (1) Flysurfer (1) Jucker Hawaii (1) Spleene (1)", $this->getText("query_1"));

            //english language is now available for factfinder also
            $this->clickAndWait("test_Lang_English");
            $this->assertTrue($this->isElementPresent("link=Price"));
            $this->assertTrue($this->isElementPresent("link=< 25,00 (6)"));
            $this->assertTrue($this->isElementPresent("link=25,00 - 99,99 (15)"));
            $this->assertTrue($this->isElementPresent("link=100,00 - 249,99 (6)"));
            $this->assertTrue($this->isElementPresent("link=250,00 - 499,99 (7)"));
            $this->assertTrue($this->isElementPresent("link=>= 500,00 (17)"));
            $this->assertEquals("Please choose Kiteboarding (24) Gear (17) Wakeboarding (8) Special Offers (2)", $this->getText("query_0"));
            $this->assertEquals("Please choose Kuyichi (10) Liquid Force Kite (7) Core Kiteboarding (3) ION (3) Naish (3) RRD (3) Cabrinha (2) NPX (2) Flyboards (1) Flysurfer (1) Jucker Hawaii (1) Spleene (1)", $this->getText("query_1"));

            //going back to DE lang
            $this->clickAndWait("test_Lang_Deutsch");
            //selecting filtering by price (left side)
            $this->clickAndWait("link=< 25,00 (6)");
            $this->assertEquals("Bitte auswählen Angebote (2) Bekleidung (2) Kiteboarding (2)", $this->getText("query_0"));
            $this->assertEquals("Bitte auswählen Kuyichi (2) Jucker Hawaii (1)", $this->getText("query_1"));
            $this->assertFalse($this->isElementPresent("attribute_0"));

            //search param should be saved, so going back to initial search
            $this->clickAndWait("test_searchGo");
            //search by vendor
            $this->selectAndWait("query_1", "index=3");
            $this->assertEquals("*", $this->getValue("searchparam"));
            $this->assertEquals("3 Treffer für \"*\"", $this->getText("test_smallHeader"));
            $this->assertTrue($this->isElementPresent("test_cntr_1_1208"));
            $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
            $this->clickAndWait("//div[@id='test_cntr_1_1208']/a");
            $this->assertEquals("Sie sind hier: / Suchergebnis für \"*\"", $this->getText("path"));
            $this->assertEquals("Kite CORE GTS", $this->getText("test_product_name"));
            $this->assertEquals("*", $this->getValue("searchparam"));
    }


    /**
     * factfinder search. ordering product from it
     *
     * @group factfinder
     *
     * @return null
     */
    public function testFactfinderOrder()
    {
        //testing search
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("searchparam", "\"kite\"");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("link=\"kite\""));
        $this->assertTrue($this->isElementPresent("test_smallHeader"));
        $this->assertEquals("32 Treffer für \"\"kite\"\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("link=Preis"));
        $this->assertTrue($this->isElementPresent("link=< 50,00 (6)"));
        $this->assertTrue($this->isElementPresent("link=50,00 - 249,99 (4)"));
        $this->assertTrue($this->isElementPresent("link=250,00 - 499,99 (5)"));
        $this->assertTrue($this->isElementPresent("link=500,00 - 999,99 (13)"));
        $this->assertTrue($this->isElementPresent("link=>= 1000,00 (4)"));
        $this->assertEquals("\"kite\"", $this->getValue("searchparam"));

        //checking first product in the list details
        $this->clickAndWait("//div[@id='test_cntr_1_1208']/a");
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"\"kite\"\"", $this->getText("path"));
        $this->assertEquals("Kite CORE GTS", $this->getText("test_product_name"));
        $this->clickAndWait("test_toBasket");
        $this->assertTrue($this->isElementPresent("test_prodXofY_Top"));
        $this->assertEquals("Artikel 1 / 32", $this->getText("test_prodXofY_Top"));

        $this->clickAndWait("test_link_nextArticleTop");
        $this->assertEquals("Artikel 2 / 32", $this->getText("test_prodXofY_Top"));

        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertEquals("\"kite\"", $this->getValue("searchparam"));
        $this->assertTrue($this->isElementPresent("link=\"kite\""));
        $this->assertEquals("Seite 1 / 4", $this->getText("test_listXofY_Top"));
        $this->clickAndWait("link=\"kite\"");
        $this->assertEquals("\"kite\"", $this->getValue("searchparam"));
        $this->assertTrue($this->isElementPresent("link=\"kite\""));
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_smallHeader"));
        $this->assertEquals("32 Treffer für \"\"kite\"\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("link=Preis"));
        $this->assertTrue($this->isElementPresent("link=< 50,00 (6)"));
        $this->assertTrue($this->isElementPresent("link=50,00 - 249,99 (4)"));
        $this->assertTrue($this->isElementPresent("link=250,00 - 499,99 (5)"));
        $this->assertTrue($this->isElementPresent("link=500,00 - 999,99 (13)"));
        $this->assertTrue($this->isElementPresent("link=>= 1000,00 (4)"));

        //checking product details
        $this->type("searchparam", "kite");
        $this->clickAndWait("test_searchGo");
        $this->selectAndWait("query_0", "index=2");
        $this->assertEquals("7 Treffer für \"kite\"", $this->getText("test_smallHeader"));
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertEquals("kite", $this->getValue("searchparam"));
        $this->assertTrue($this->isElementPresent("//div[@id='body']//a[text()='kite']"));
        $this->assertTrue($this->isElementPresent("//div[@id='body']//a[text()='Wakeboarding']"));
        $this->assertTrue($this->isElementPresent("link=Preis"));
        $this->assertTrue($this->isElementPresent("link=< 250,00 (1)"));
        $this->assertTrue($this->isElementPresent("link=250,00 - 299,99 (1)"));
        $this->assertTrue($this->isElementPresent("link=300,00 - 399,99 (3)"));
        $this->assertTrue($this->isElementPresent("link=400,00 - 599,99 (1)"));
        $this->assertTrue($this->isElementPresent("link=>= 600,00 (1)"));
        $this->clickAndWait("//div[@id='body']//a[text()='kite']");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertEquals("kite", $this->getValue("searchparam"));
        $this->assertEquals("32 Treffer für \"kite\"", $this->getText("test_smallHeader"));

        $this->assertTrue($this->isElementPresent("link=Preis"));
        $this->assertTrue($this->isElementPresent("link=< 50,00 (6)"));
        $this->assertTrue($this->isElementPresent("link=50,00 - 249,99 (4)"));
        $this->assertTrue($this->isElementPresent("link=250,00 - 499,99 (5)"));
        $this->assertTrue($this->isElementPresent("link=500,00 - 999,99 (13)"));
        $this->assertTrue($this->isElementPresent("link=>= 1000,00 (4)"));
        $this->clickAndWait("//div[@id='test_cntr_3_1211']/a");
        $this->assertEquals("Kite NBK EVO 2010", $this->getText("test_product_name"));
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"kite\"", $this->getText("path"));
        $this->assertEquals("Artikel 3 / 32", $this->getText("test_prodXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_link_nextArticleTop"));
        $this->assertTrue($this->isElementPresent("test_link_prevArticleTop"));
        $this->clickAndWait("test_link_nextArticleTop");
        $this->assertEquals("Artikel 4 / 32", $this->getText("test_prodXofY_Top"));
        $this->assertEquals("Kite CORE RIOT XR", $this->getText("test_product_name"));

        //submitting order
        $this->clickAndWait("test_TopBasketHeader");
        $this->assertEquals("791,10 €", $this->getText("test_basketGrandTotal"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->type("order_remark", "testing factfinder");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->click("test_Payment_testpayment");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("791,10 €", $this->getText("test_orderGrandTotal"));
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID"));
    }

    /**
     * checking flyout menu in search. also agb search. should be redirected to agb cms page
     *
     * @group factfinder
     *
     * @return null
     */
    public function testFactfinderFlyoutAndAgbSearch()
    {
        $sLink = "http://support.oxid-esales.com/oxidefiretestshops/pe_4.5_factfinder/AGB/?redirected=1";
        //testing search
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");

        //flyout menu testing
        $this->type("searchparam", "kite");
        $this->keyUp("searchparam","e");
        $this->waitForElement("document.getElementById('suggestLayer_9')");
        $this->mouseOver("document.getElementById('suggestLayer_3')");
        $this->clickAt("document.getElementById('suggestLayer_3')");
        $this->waitForPageToLoad();
        $this->checkForErrors();
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getValue("searchparam"));
        $this->assertTrue($this->isElementPresent("link=Kite FLYSURFER SPEED3"));
        $this->assertEquals("1 Treffer für \"Kite FLYSURFER SPEED3\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1201"));
        $this->clickAndWait("link=Kite FLYSURFER SPEED3");
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getValue("searchparam"));

        //agb search in factfinder should open agb page
        $this->type("searchparam", "agb");
        $this->click("test_searchGo");
        $this->waitForPageToLoad("60000");
        $this->assertEquals($this->getLocation(), $sLink);
    }

    /**
     * factfinder campaigns and search
     *
     * @group factfinder
     *
     * @return null
     */
    public function testFactfinderCampaignSearch()
    {
        //testing search
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("searchparam", "*");
        $this->clickAndWait("test_searchGo");
        $this->assertFalse($this->isElementPresent("test_cntr_1_2103"));
        $this->assertFalse($this->isElementPresent("//div[@id='body']/img"));

        $this->type("searchparam", "kite");
        $this->clickAndWait("test_searchGo");
        //specific product can be set for showing in first possition
        //one of new implemented factfinder functionalities
        $this->assertTrue($this->isElementPresent("test_cntr_1_2103"));
        //banner can be set for showing before search results
        $this->assertTrue($this->isElementPresent("//div[@id='body']/img"));

    }

    /**
     * factfinder special Umlauts in search
     *
     * @group factfinder
     *
     * @return null
     */
    public function testFactfinderUmlautsSearch()
    {
        //testing search
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("searchparam", "Gürtel");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Verfeinern:  Gürtel"));
        $this->assertEquals("Gürtel", $this->getText("link=Gürtel"));
        $this->assertEquals("2 Treffer für \"Gürtel\"", $this->getText("test_smallHeader"));
        $this->clickAndWait("link=Gürtel");
        $this->assertEquals("Gürtel", $this->getValue("searchparam"));
        $this->assertEquals("Gürtel", $this->getText("link=Gürtel"));
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertEquals("2 Treffer für \"Gürtel\"", $this->getText("test_smallHeader"));
    }
}
