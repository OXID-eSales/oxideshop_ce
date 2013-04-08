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

class Acceptance_trustedShopsTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Mall functionality: subshops & inheritance ----------------------------------

    /**
     * testing trusted shops. seagel activation
     * @group admin
     * @group trustedShops
     */
    public function testTsSeagel()
    {
            //trusted shops are disabled
            $this->openShop();
            $this->click("languageTrigger");
            $this->waitForItemAppear("languages");
            $this->click("//ul[@id='languages']/li[2]/a");
            $this->waitForItemDisappear("languages");
            $this->assertFalse($this->isElementPresent("tsProfile"));
            $this->assertFalse($this->isTextPresent("ist ein von Trusted Shops geprüfter"));

            //trusted shops setup in admin
            $this->loginAdminTs();
            $this->assertTrue($this->isElementPresent("aShopID_TrustedShops[0]"));
            $this->type("aShopID_TrustedShops[0]", "XA2A8D35838AF5F63E5EB0E05847B1CB8");
            $this->check("//input[@name='tsTestMode' and @value='true']");
            $this->check("//input[@name='tsSealActive' and @value='true']");
          //  $this->assertEquals("Lastschrift/Bankeinzug Kreditkarte Rechnung Nachnahme Vorauskasse / Überweisung Verrechnungsscheck Paybox PayPal Zahlung bei Abholung Finanzierung Leasing T-Pay Click&Buy (Firstgate) Giropay Google Checkout Online Shop Zahlungskarte Sofortüberweisung.de Andere Zahlungsart", $this->getText("paymentids[oxidcashondel]"));
            $this->assertTrue($this->isTextPresent("Test payment method [EN] šÄßüл"));
            $this->assertEquals("Lastschrift/Bankeinzug Kreditkarte Rechnung Nachnahme Vorauskasse / Überweisung Verrechnungsscheck Paybox PayPal Zahlung bei Abholung Finanzierung Leasing T-Pay Click&Buy (Firstgate) Giropay Google Checkout Online Shop Zahlungskarte Sofortüberweisung.de Andere Zahlungsart", $this->getText("paymentids[testpayment]"));
            $this->select("paymentids[testpayment]", "label=Kreditkarte");
            $this->clickAndWait("save");
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID"), "Invalid Trusted Shops ID for testing");
            $this->assertEquals("Kreditkarte", $this->getSelectedLabel("paymentids[testpayment]"));
            $this->assertEquals("on", $this->getValue("//input[@name='tsSealActive' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='tsTestMode' and @value='true']"));
            $this->assertEquals("XA2A8D35838AF5F63E5EB0E05847B1CB8", $this->getValue("aShopID_TrustedShops[0]"));
            //$this->assertTrue($this->isElementPresent("//div[@id='liste']/table/tbody/tr[2]/td[@class='active']"));
            $this->type("aShopID_TrustedShops[0]", "XA2A8D35838AF5F63E5EB0E05847B1CB4");
            $this->clickAndWait("save");
            $this->assertTrue($this->isTextPresent("The certificate does not exist"));
            $this->assertEquals("XA2A8D35838AF5F63E5EB0E05847B1CB8", $this->getValue("aShopID_TrustedShops[0]"));


            $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
            $this->markTestSkipped("waiting for shop lupes where put TS logo");

            //checking in frontend
            $this->openShop();
            $this->switchLanguage("Deutsch");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->assertTrue($this->isTextPresent("ist ein von Trusted Shops geprüfter"));
            $this->clickAndWait("//ul[@id='newItems']/li[2]/form//a");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("toBasket");
            $this->openBasket("Deutsch");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->loginInFrontend("birute_test@nfq.lt", "useruser");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertEquals("Sie sind hier: / Bestellung abschliessen", $this->getText("breadCrumb"));
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->check("//form[@id='orderConfirmAgbTop']/input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
            $this->assertTrue($this->isElementPresent("formTsShops"));
            $this->assertTrue($this->isElementPresent("tsProfile"));
    }

    /**
     * testing trusted shops. excellence Ts. functionality depends on order price
     * @group admin
     * @group trustedShops
     */
    public function testTsExcellence()
    {
            //setupping ts
            $this->loginAdminTs();
            $this->type("aShopID_TrustedShops[0]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->type("aTsUser[0]", "testExcellencePartner");
            $this->type("aTsPassword[0]", "test12345678");
            $this->check("//input[@name='tsTestMode' and @value='true']");
            $this->clickAndWait("save");
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID"), "Invalid Trusted Shops ID for testing");
            $this->assertEquals("X41495A6E65ECDDCD554A02C0601D1C97", $this->getValue("aShopID_TrustedShops[0]"));
            $this->assertEquals("testExcellencePartner", $this->getValue("aTsUser[0]"));
            $this->assertEquals("test12345678", $this->getValue("aTsPassword[0]"));
            //$this->assertTrue($this->isElementPresent("//div[@id='liste']/table/tbody/tr[2]/td[@class='active']"));
            $this->check("//input[@name='tsSealActive' and @value='true']");
            $this->check("//input[@name='tsTestMode' and @value='true']");
            $this->clickAndWait("save");
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID"), "Invalid Trusted Shops ID for testing");
            //checking in frontend. order < 500eur
            $this->openShop();
            $this->switchLanguage("Deutsch");
            $this->clickAndWait("//ul[@id='newItems']/li[2]/form//a");
            $this->clickAndWait("toBasket");
            $this->openBasket("Deutsch");
            $this->loginInFrontend("birute_test@nfq.lt", "useruser");
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertEquals("Sie sind hier: / Bezahlen", $this->getText("breadCrumb"));
            $this->click("payment_oxidcashondel");
            $this->waitForItemAppear("bltsprotection");
            $this->assertFalse($this->isVisible("stsprotection"));
            $this->assertTrue($this->isTextPresent("Käuferschutz von 500 € (0,98 € inkl. MwSt.)"));
            $this->check("bltsprotection");
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertTrue($this->isTextPresent("0,98 €"));
            $this->assertTrue($this->isTextPresent("Trusted Shops Käuferschutz"));
            $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

            //order > 500eur
            $this->clickAndWait("link=Startseite");
            $this->clickAndWait("//ul[@id='newItems']/li[2]/form//a");
            $this->type("amountToBasket", "6");
            $this->clickAndWait("toBasket");
            $this->openBasket("Deutsch");
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertTrue($this->isElementPresent("bltsprotection"));
            $this->assertTrue($this->isElementPresent("stsprotection"));
            $this->assertEquals("Käuferschutz von 500 € (0,98 € inkl. MwSt.) Käuferschutz von 1500 € (2,94 € inkl. MwSt.)", $this->getText("stsprotection"));
            $this->select("stsprotection", "label=Käuferschutz von 1500 € (2,94 € inkl. MwSt.)");
            $this->check("bltsprotection");
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            $this->assertTrue($this->isTextPresent("2,94 €"));
            $this->assertTrue($this->isTextPresent("Trusted Shops Käuferschutz"));
            $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

            //checking orders in admin
            $this->loginAdmin("Administer Orders", "Orders");
            $this->waitForElement("link=12");
            $this->openTab("link=12");
            $this->assertTrue($this->isTextPresent("0,98"));
            $this->frame("list");
            $this->openTab("link=13");
            $this->assertTrue($this->isTextPresent("2,94"));
    }

     /**
     * testing trusted shops. Raiting of eShop
     * @group admin
     * @group trustedShops
     */
    public function testTsRatings()
    {
            //trusted shops are disabled
            $this->openShop();
            $this->switchLanguage("Deutsch");
            $this->assertFalse($this->isElementPresent("test_RightSideTsWidgetBox"));

             //setupping ts
            $this->loginAdminTs("link=Customer ratings", "//li[@id='nav-2-10-1']/a/b");
            $this->frame("list");
            $this->waitForElement("link=Interface");
            $this->clickAndWaitFrame("link=Interface", "edit");
            $this->frame("edit");
            $this->waitForElement("confaarrs[aTsLangIds][de]");
            $this->assertTrue($this->isElementPresent("confaarrs[aTsLangIds][de]"));
            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->type("confaarrs[aTsLangIds][en]", "XCDD9234E25B44A2119C3967A77A6EDBE");
            $this->check("//input[@name='confbools[blTsWidget]' and @value='true']");
            $this->check("//input[@name='confbools[blTsThankyouReview]' and @value='true']");
            $this->check("//input[@name='confbools[blTsOrderEmailReview]' and @value='true']");
            $this->check("//input[@name='confbools[blTsOrderSendEmailReview]' and @value='true']");
            $this->clickAndWait("save");
            $this->assertEquals("X41495A6E65ECDDCD554A02C0601D1C97", $this->getValue("confaarrs[aTsLangIds][de]"));
            $this->assertEquals("XCDD9234E25B44A2119C3967A77A6EDBE", $this->getValue("confaarrs[aTsLangIds][en]"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsWidget]' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsThankyouReview]' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsOrderEmailReview]' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsOrderSendEmailReview]' and @value='true']"));
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact"));

            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C9a");
            $this->clickAndWait("save");
            $this->assertTrue($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact"));
            $this->assertEquals("X41495A6E65ECDDCD554A02C0601D1C9a", $this->getValue("confaarrs[aTsLangIds][de]"));

            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->clickAndWait("save");
            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact"));

            //checking in frontend
            $this->openShop();
            $this->switchLanguage("Deutsch");
            //$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));

            $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
            $this->markTestSkipped("waiting for shop lupes where put TS logo and ratings");
            $this->clickAndWait("//ul[@id='newItems']/li[2]/form//a");
            $this->clickAndWait("toBasket");
            $this->openBasket("Deutsch");
            //$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            //$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->loginInFrontend("birute_test@nfq.lt", "useruser");
            //$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            //$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
            //$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->check("//form[@id='orderConfirmAgbTop']/input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
            $this->assertTrue($this->isElementPresent("//img[@alt='Bewerten Sie unseren Shop!']"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
    }
}