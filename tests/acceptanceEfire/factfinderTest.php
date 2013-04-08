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
class AcceptanceEfire_factfinderTest extends oxidAdditionalSeleniumFunctions
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
        $this->switchLanguage("Deutsch");
        $this->searchFor("*");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertEquals("51 Treffer für \"*\"", $this->getHeadingText("//h1"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()=3]"));
        $this->assertEquals("*", $this->getValue("searchparam"));

        //price is displayed on left side, before category price
        $this->click("//div[@id='groupFilterLeft[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilterLeft[0]']//ul");
        $this->assertEquals("Preis: < 25,00 (6) 25,00 - 99,99 (15) 100,00 - 249,99 (6) 250,00 - 499,99 (7) >= 500,00 (17)", $this->clearString($this->getText("//div[@id='groupFilterLeft[0]']//ul")));

        //vendor and categories are displayed as selectlist in top of the page
        $this->click("//div[@id='groupFilter[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilter[0]']//ul");
        $this->assertEquals("Kategorie: Bitte auswählen Kiteboarding (24) Bekleidung (17) Wakeboarding (8) Angebote (2)", $this->clearString($this->getText("//div[@id='groupFilter[0]']//ul")));
        $this->click("//div[@id='groupFilter[1]']/p");
        $this->waitForItemAppear("//div[@id='groupFilter[1]']//ul");
        $this->assertEquals("Hersteller: Bitte auswählen Kuyichi (10) Liquid Force Kite (7) Core Kiteboarding (3) ION (3) Naish (3) RRD (3) Cabrinha (2) NPX (2) Flyboards (1) Flysurfer (1) Jucker Hawaii (1) Spleene (1)", $this->clearString($this->getText("//div[@id='groupFilter[1]']//ul")));
        //other filters returnet by factfinder
        $this->click("//div[@id='attributeFilter[2]']/p");
        $this->waitForItemAppear("//div[@id='attributeFilter[2]']//ul");
        $this->assertEquals("Lieferumfang: Bitte auswählen Kite (1) Kite, Backpack, Reparaturset (3) Kite, Global Bar, Tasche, Pumpe (1) Kite, Tasche (2) Kite, Tasche, CPR Control System, Pumpe (2) Kite, Tasche, Reparaturset (3)", $this->clearString($this->getText("//div[@id='attributeFilter[2]']//ul")));
        $this->click("//div[@id='attributeFilter[3]']/p");
        $this->waitForItemAppear("//div[@id='attributeFilter[3]']//ul");
        $this->assertEquals("Einsatzbereich: Bitte auswählen All-Terrain (1) All-Terrain, Freeride (1) Allround (4) Freeride (2) Freeride, Freestyle, New-School (1) Freeride, Wakestyle, Wave (1) Old-School, Big Air (1) Progressive Freestyle, Wakestyle (1)", $this->clearString($this->getText("//div[@id='attributeFilter[3]']//ul")));
        //english language is now available for factfinder also
        $this->switchLanguage("English");
        $this->click("//div[@id='groupFilterLeft[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilterLeft[0]']//ul");
        $this->assertEquals("Price: < 25,00 (6) 25,00 - 99,99 (15) 100,00 - 249,99 (6) 250,00 - 499,99 (7) >= 500,00 (17)", $this->clearString($this->getText("//div[@id='groupFilterLeft[0]']//ul")));
        $this->click("//div[@id='groupFilter[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilter[0]']//ul");
        $this->assertEquals("Category: Please choose Kiteboarding (24) Gear (17) Wakeboarding (8) Special Offers (2)", $this->clearString($this->getText("//div[@id='groupFilter[0]']//ul")));
        $this->click("//div[@id='groupFilter[1]']/p");
        $this->waitForItemAppear("//div[@id='groupFilter[1]']//ul");
        $this->assertEquals("Manufacturer: Please choose Kuyichi (10) Liquid Force Kite (7) Core Kiteboarding (3) ION (3) Naish (3) RRD (3) Cabrinha (2) NPX (2) Flyboards (1) Flysurfer (1) Jucker Hawaii (1) Spleene (1)", $this->clearString($this->getText("//div[@id='groupFilter[1]']//ul")));
        $this->click("//div[@id='attributeFilter[2]']/p");
        $this->waitForItemAppear("//div[@id='attributeFilter[2]']//ul");
        $this->assertEquals("Included in delivery: Please choose kite (1) kite, backpack, repair kit (3) kite, bag (2) kite, bag, CPR control system, pump (2) kite, bag, repair kit (3) kite, global bar, bag, pump (1)", $this->clearString($this->getText("//div[@id='attributeFilter[2]']//ul")));
        $this->click("//div[@id='attributeFilter[3]']/p");
        $this->waitForItemAppear("//div[@id='attributeFilter[3]']//ul");
        $this->assertEquals("Area of Application: Please choose All-terrain (1) All-terrain, Freeride (1) Allround (4) Freeride (2) Freeride, Freestyle, New-School (1) Freeride, Wakestyle, Wave (1) Old-School, Big Air (1) Progressive Freestyle, Wakestyle (1)", $this->clearString($this->getText("//div[@id='attributeFilter[3]']//ul")));

        //going back to DE lang
        $this->switchLanguage("Deutsch");
        $this->selectDropDown("groupFilterLeft[0]", "", "li[2]");  // < 25.00 (6)
        $this->assertEquals("6 Treffer für \"*\"", $this->getHeadingText("//h1"));
        $this->click("//div[@id='groupFilter[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilter[0]']//ul");
        $this->assertEquals("Kategorie: Bitte auswählen Angebote (2) Bekleidung (2) Kiteboarding (2)", $this->clearString($this->getText("//div[@id='groupFilter[0]']//ul")));
        $this->click("//div[@id='groupFilter[1]']/p");
        $this->waitForItemAppear("//div[@id='groupFilter[1]']//ul");
        $this->assertEquals("Hersteller: Bitte auswählen Kuyichi (2) Jucker Hawaii (1)", $this->clearString($this->getText("//div[@id='groupFilter[1]']//ul")));
        $this->assertFalse($this->isElementPresent("//div[@id='attributeFilter[2]']//ul"));
        //search param should be saved, so going back to initial search
        $this->assertEquals("*", $this->getValue("searchParam"));
        $this->clickAndWait("//form[@name='search']//input[@type='submit']");
        //search by vendor
        $this->selectDropDown("groupFilter[1]", "", "li[4]");  // Core Kiteboarding (3)
        $this->assertEquals("*", $this->getValue("searchParam"));
        $this->assertEquals("3 Treffer für \"*\"", $this->getHeadingText("//h1"));
        $this->assertEquals("Kite CORE GTS", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"*\"", $this->getText("breadCrumb"));
        $this->assertEquals("Kite CORE GTS", $this->getText("//h1"));
        $this->assertEquals("*", $this->getValue("searchParam"));
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
        $this->switchLanguage("Deutsch");
        $this->searchFor("\"kite\"");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("link=\"kite\""));
        $this->assertEquals("\"kite\"", $this->getValue("searchParam"));
        $this->assertEquals("32 Treffer für \"\"kite\"\"", $this->getHeadingText("//h1"));
        $this->click("//div[@id='groupFilterLeft[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilterLeft[0]']//ul");
        $this->assertEquals("Preis: < 50,00 (6) 50,00 - 249,99 (4) 250,00 - 499,99 (5) 500,00 - 999,99 (13) >= 1000,00 (4)", $this->clearString($this->getText("//div[@id='groupFilterLeft[0]']//ul")));
        //checking first product in the list details
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Kite CORE GTS", $this->getText("//h1"));
        $this->clickAndWait("toBasket");
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"\"kite\"\"", $this->getText("breadCrumb"));
        $this->assertEquals("ARTIKEL 1 VON 32", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->assertTrue($this->isElementPresent("linkNextArticle"));
        $this->clickAndWait("linkNextArticle");
        $this->assertEquals("Kite NBK EVO 2010", $this->getText("//h1"));
        $this->assertEquals("ARTIKEL 2 VON 32", $this->getText("//div[@id='detailsItemsPager']/span"));


        //TODO: finish when backToOverview will be reintroduced
        //$this->clickAndWait("test_BackOverviewTop");
        //TODO: check if params are ok after returning with backToOverview

        $this->searchFor("kite");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertEquals("kite", $this->getValue("searchParam"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//a[text()='kite']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']/a[text()='4']"));
        $this->selectDropDown("groupFilter[0]", "", "li[3]"); //Wakeboarding
        $this->assertEquals("7 Treffer für \"kite\"", $this->getHeadingText("//h1"));
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertEquals("kite", $this->getValue("searchParam"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//a[text()='kite']"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//a[text()='Wakeboarding']"));
        $this->click("//div[@id='groupFilterLeft[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilterLeft[0]']//ul");
        $this->assertEquals("Preis: < 250,00 (1) 250,00 - 299,99 (1) 300,00 - 399,99 (3) 400,00 - 599,99 (1) >= 600,00 (1)", $this->clearString($this->getText("//div[@id='groupFilterLeft[0]']//ul")));
        $this->clickAndWait("//div[@id='content']//a[text()='kite']");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertEquals("kite", $this->getValue("searchParam"));
        $this->assertEquals("32 Treffer für \"kite\"", $this->getHeadingText("//h1"));
        $this->click("//div[@id='groupFilterLeft[0]']/p");
        $this->waitForItemAppear("//div[@id='groupFilterLeft[0]']//ul");
        $this->assertEquals("Preis: < 50,00 (6) 50,00 - 249,99 (4) 250,00 - 499,99 (5) 500,00 - 999,99 (13) >= 1000,00 (4)", $this->clearString($this->getText("//div[@id='groupFilterLeft[0]']//ul")));
        $this->clickAndWait("searchList_2");
        $this->assertEquals("Kite CORE GTS", $this->getText("//h1"));
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"kite\"", $this->getText("breadCrumb"));
        $this->assertEquals("ARTIKEL 2 VON 32", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"kite\"", $this->getText("breadCrumb"));
        $this->assertEquals("kite", $this->getValue("searchParam"));
        $this->assertTrue($this->isElementPresent("linkNextArticle"));
        $this->assertTrue($this->isElementPresent("linkPrevArticle"));
        $this->clickAndWait("linkNextArticle");
        $this->assertEquals("Kite NBK EVO 2010", $this->getText("//h1"));
        $this->assertEquals("ARTIKEL 3 VON 32", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->assertEquals("Sie sind hier: / Suchergebnis für \"kite\"", $this->getText("breadCrumb"));
        $this->assertEquals("kite", $this->getValue("searchParam"));

        //submitting order
        $this->openBasket("Deutsch");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("order_remark");
        $this->type("order_remark", "testing factfinder");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("payment_testpayment");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->clickAndWait("//button[text()='Zahlungspflichtig bestellen']");
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
        $this->switchLanguage("Deutsch");

        //flyout menu testing
        $this->type("//input[@id='searchParam']", "kite");
        $this->waitForItemAppear("suggestLayer");
        $this->keyUp("//input[@id='searchParam']","e");
        $this->waitForElement("document.getElementById('suggestLayer_9')");
        $this->mouseOver("document.getElementById('suggestLayer_3')");
        $this->clickAt("document.getElementById('suggestLayer_3')");
        $this->waitForPageToLoad();
        $this->checkForErrors();
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getValue("//input[@id='searchParam']"));
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getText("searchList_1"));
        $this->assertEquals("1 Treffer für \"Kite FLYSURFER SPEED3\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getValue("//input[@id='searchParam']"));
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getText("//h1"));
        $this->assertEquals("ARTIKEL 1 VON 1", $this->getText("//div[@id='detailsItemsPager']/span"));
        $this->assertEquals("Kite FLYSURFER SPEED3", $this->getText("//h2"));

        //agb search in factfinder should open agb page
        $this->searchFor("agb");
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
        $this->switchLanguage("Deutsch");
        $this->searchFor("*");
        $this->assertNotEquals("Wakeboard LIQUID FORCE GROOVE 2010", $this->clearString($this->getText("searchList_1")));
        $this->assertFalse($this->isElementPresent("//div[@id='content']//img[@alt='OXID Geschenke Shop']"));

        $this->searchFor("kite");
        //specific product can be set for showing in first possition
        //one of new implemented factfinder functionalities
        $this->assertEquals("Wakeboard LIQUID FORCE GROOVE 2010", $this->clearString($this->getText("searchList_1")));
        //banner can be set for showing before search results
        $this->assertTrue($this->isElementPresent("//div[@id='content']//img[@alt='OXID Geschenke Shop']"));

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
        $this->switchLanguage("Deutsch");
        $this->searchFor("Gürtel");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Verfeinern:  Gürtel"));
        $this->assertTrue($this->isElementPresent("link=Gürtel"));
        $this->assertEquals("2 Treffer für \"Gürtel\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("link=Gürtel");
        $this->assertEquals("Gürtel", $this->getValue("//input[@id='searchParam']"));
        $this->assertTrue($this->isElementPresent("link=Gürtel"));
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertEquals("2 Treffer für \"Gürtel\"", $this->getHeadingText("//h1"));
    }
}
