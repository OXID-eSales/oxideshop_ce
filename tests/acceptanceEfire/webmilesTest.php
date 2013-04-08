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


require_once 'acceptance/oxidAdditionalSeleniumFunctions.php';

class AcceptanceEfire_webmilesTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ eFire modules for eShop ----------------------------------


    /**
     * webmiles in frontend
     * @group webmiles
     */
    public function testWebmiles()
    {
        //testing search
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->assertTrue($this->isTextPresent("Sie sammeln"), "Webmiles are not setupped correctly!");
        $this->searchFor("1000");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("breadCrumb"));
        $this->assertEquals("Sie sammeln 1", $this->clearString($this->getText("webmiles_prod_searchList_1")));
        $this->selectDropDown("viewOptions", "Liste");
        $this->type("amountToBasket_searchList_1", "10");
        $this->clickAndWait("toBasket_searchList_1");
        $this->assertEquals("webmiles 13", $this->getText("//div[@id='basketFlyout']//p[3]"));
        $this->openBasket("Deutsch");
        $this->assertEquals("Sie können 13 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertFalse($this->isTextPresent("Noch für 9,00 € einkaufen und 1 extra sammeln!"));
        $this->type("am_1", "1");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isTextPresent("Noch für 9,00 € einkaufen und 1 extra sammeln!"));
        $this->assertEquals("Sie können 3 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->executeSql("DELETE FROM `efi_webmiles` WHERE `efifield` = 'campaign_bonus'");
        $this->clickAndWait("basketUpdate");
        $this->assertTrue($this->isTextPresent("Noch für 9,00 € einkaufen und 1 extra sammeln!"));
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->assertTrue($this->isTextPresent("Als Neukunde sammeln Sie 1 extra!"));
        $this->clickAndWait("//div[@id='optionRegistration']//button");
        $this->type("userLoginName", "birute@nfq.lt");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->type("invadr[oxuser__oxfname]", "Testing account");
        $this->type("invadr[oxuser__oxlname]", "Webmiles");
        $this->type("invadr[oxuser__oxstreet]", "Musterstr.");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "79098");
        $this->type("invadr[oxuser__oxcity]", "Musterstadt");
        $this->select("invadr[oxuser__oxcountryid]", "label=Deutschland");
        $this->type("order_remark", "Testing webmiles in eShop");
        $this->uncheck("//input[@name='blnewssubscribed' and @value='1']");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->clickAndWait("link=1. Warenkorbübersicht");
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxfname]");
        $this->assertEquals("Testing account",$this->getValue("invadr[oxuser__oxfname]"), "User entered data is gone!");
        $this->check("//input[@name='blnewssubscribed' and @value='1']");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->clickAndWait("link=1. Warenkorbübersicht");
        $this->assertEquals("Sie können 3 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxfname]");
        $this->uncheck("//input[@name='blnewssubscribed' and @value='1']");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->clickAndWait("link=1. Warenkorbübersicht");
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->click("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->type("username", "stewerner");
        $this->type("password", "oxid123support");
        $this->clickAndWait("//input[@name='Button' and @value='']");
        $this->assertTrue($this->isTextPresent("Sie sammeln mit Ihrem webmiles Konto \"stewerner\" 2 webmiles."));
        $this->assertEquals("Sie sammeln: 2", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->clickAndWait("//div[@id='orderPayment']//button");
        $this->click("payment_oxidinvoice");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->assertEquals("Sie sammeln: 2", $this->getText("//div[@id='basketSummary']//tr[5]"));
        $this->assertEquals("Gesamtsumme 4,90 €", $this->getText("//div[@id='basketSummary']//tr[6]"));
        $this->clickAndWait("//button[text()='Zahlungspflichtig bestellen']");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"));
        $this->assertTrue($this->isTextPresent("Sie haben mit Ihrer Bestellung 2 gesammelt."));
        $this->assertTrue($this->isTextPresent("wertvolle webmiles sammeln"));
        $this->assertTrue($this->isTextPresent("und in tolle Prämien einlösen!"));

        $this->clickAndWait("link=Startseite");
        $this->searchFor("1000");
        $this->clickAndWait("toBasket_searchList_1");
        $this->openBasket("Deutsch");
        $this->assertEquals("Sie sammeln: 11", $this->getText("//div[@id='basketSummary']//tr[5]"));
        $this->clickAndWait("logoutLink");
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));

        //testing in admin, if order is saved correctly
        $this->loginAdmin("Administer Orders", "Orders", "btn.help", "link=2");
        $this->openTab("link=2", "setfolder");
        $this->frame("list");
        $this->assertTrue($this->isElementPresent("del.1"));
        $this->assertTrue($this->isElementPresent("where[oxorder][oxordernr]"));
        $this->clickAndConfirm("del.1");
        $this->assertTrue($this->isElementPresent("where[oxorder][oxordernr]"));
        $this->assertFalse($this->isElementPresent("del.2"));
    }

}
