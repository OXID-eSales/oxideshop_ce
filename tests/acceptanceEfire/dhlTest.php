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

class AcceptanceEfire_dhlTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ eFire modules for eShop ----------------------------------


    /**
     * dhl in frontend
     * @group dhl
     */
    public function testDhlPaket()
    {
        //testing search
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("1", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");

        //TODO: change when templates will be applied for 4.5.1
         $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhl']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhlwelt']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhleuro']"), "Dhl connector for live testing is not fully setupped");

        //weltpaket
        $this->selectAndWait("sShipSet", "label=Weltpaket");
        $this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));
        $this->clickAndWait("//ul[@class='checkoutSteps clear']/li[2]//a");



         $this->click("userChangeAddress");
        $this->type("invadr[oxuser__oxfon]", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
       // $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Charges: 32,00 €", $this->getText("shipSetCost"));
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 32,00 €", $this->getText("shipSetCost"));
        $this->assertTrue($this->isElementPresent("payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("payment_oxidcashondel"));

        //euro paket
        $this->selectAndWait("sShipSet", "label=DHL Europaket");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Charges: 14,00 €", $this->getText("shipSetCost"));
        $this->click("//input[@value='efidhleuroggrn']");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 14,10 €", $this->getText("shipSetCost"));
        $this->assertFalse($this->isElementPresent("payment_efidhlcod"));
        $this->assertTrue($this->isElementPresent("payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("payment_oxidcashondel"));
        $this->assertFalse($this->isElementPresent("payment_oxidcreditcard"));

        //dhl paket
        $this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->assertEquals("Charges: 6,00 €", $this->getText("shipSetCost"));
        $this->assertTrue($this->isElementPresent("//input[@value='efidhlggrn']"));
        $this->assertTrue($this->isElementPresent("//input[@value='efidhlprsnl']"));
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Delivery to addressee only 1,80 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[2]")));
        $this->assertEquals("Transport insurance (up to 500,00 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[3]")));
        $this->assertEquals("Supplementary insurance 3,50 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[4]")));
        $this->click("//input[@value='efidhlinsa']");
        $this->click("//input[@value='efidhlggrn']");
        $this->click("//input[@value='efidhlprsnl']");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 11,40 €", $this->getText("shipSetCost"));
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Delivery to addressee only 1,80 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[2]")));
        $this->assertEquals("Supplementary insurance 3,50 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[3]")));
        $this->assertTrue($this->isElementPresent("payment_oxidcashondel"));
        $this->assertFalse($this->isElementPresent("payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("//input[@value='efidhlggrn']"));
        $this->assertFalse($this->isElementPresent("//input[@value='efidhlprsnl']"));
        $this->clickAndWait("changedelservices");
        $this->assertEquals("Charges: 11,40 €", $this->getText("shipSetCost"));
        $this->click("//input[@value='efidhlprsnl']");
        $this->click("//input[@value='efidhlggrn']");
        $this->click("//form[@id='dhlpayment']//dl[3]//input");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 6,00 €", $this->getText("shipSetCost"));
        $this->assertTrue($this->isElementPresent("payment_oxidcashondel"));
        $this->assertTrue($this->isElementPresent("payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("payment_oxidcreditcard"));
        $this->assertFalse($this->isTextPresent("GoGreen"));
        $this->assertFalse($this->isTextPresent("Delivery to addressee only"));
        $this->assertFalse($this->isTextPresent("Supplementary insurance"));
        $this->click("payment_efidhlcod");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Total Products (gross): 1,00 €", $this->getText("//div[@id='basketSummary']//tr[3]"));
        $this->assertEquals("Shipping cost 6,00 €", $this->getText("//div[@id='basketSummary']//tr[4]"));
        $this->assertEquals("Surcharge Type of Payment: 5,00 €", $this->getText("//div[@id='basketSummary']//tr[5]"));
        $this->assertEquals("Grand Total: 12,00 €", $this->getText("//div[@id='basketSummary']//tr[6]"));
        $this->clickAndWait("//button[text()='Order now']");
        $this->assertTrue($this->isTextPresent("We registered your order"));

    }

    /**
     * dhl in frontend
     * @group dhl
     */
    public function testDhlWeltpaket()
    {
        //testing search
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("1", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //TODO: change when templates will be applied for 4.5.1
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //$this->clickAndWait("link=Basket");

        //changing country to Liechtenstein
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invCountrySelect");
        $this->select("invCountrySelect", "label=Liechtenstein");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));

        $this->clickAndWait("//ul[@class='checkoutSteps clear']/li[2]//a");
        $this->click("userChangeAddress");
        $this->type("invadr[oxuser__oxfon]", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->assertFalse($this->isElementPresent("//option[@value='efidhl']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhlwelt']"), "Dhl connector for live testing is not fully setupped");
        $this->assertFalse($this->isElementPresent("//option[@value='efidhleuro']"), "Dhl connector for live testing is not fully setupped");

        //weltpaket
        $this->assertEquals("Weltpaket", $this->getSelectedLabel("sShipSet"));
        $this->assertTrue($this->isElementPresent("//form[@id='dhlpayment']"), "Dhl connector for live testing is not fully setupped");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Charges: 32,00 €", $this->getText("shipSetCost"));
        $this->assertTrue($this->isElementPresent("//input[@value='efidhlweltggrn']"), "Dhl connector for live testing is not fully setupped");
        $this->click("//input[@value='efidhlweltggrn']");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 32,10 €", $this->getText("shipSetCost"));
        $this->assertFalse($this->isElementPresent("payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("payment_oxidcashondel"));
        $this->assertEquals("Payment Method", $this->getText("paymentHeader"));
        $this->assertTrue($this->isTextPresent("Service configuration you choose has no payment methods available"));
        $this->assertFalse($this->isElementPresent("//button[text()='Continue to Next Step']"));


        $this->clickAndWait("//ul[@class='checkoutSteps clear']/li[2]//a");
        $this->click("showPackStationAddress");
        $this->type("//ul[@id='packstationAddressForm']/li[4]/input", "deliveryNamešÄßüл");
        $this->type("//ul[@id='packstationAddressForm']/li[5]/input", "deliverySurnamešÄßüл");
        $this->type("//ul[@id='packstationAddressForm']/li[6]//input[@name='deladr[oxaddress__oxaddinfo]']", "12345678");
        $this->type("//ul[@id='packstationAddressForm']/li[7]//input[@name='deladr[oxaddress__oxstreetnr]']", "123456");
        $this->type("//ul[@id='packstationAddressForm']/li[8]/input", "06108");
        $this->type("//ul[@id='packstationAddressForm']/li[8]//input[@name='deladr[oxaddress__oxcity]']", "Halle");
        $this->type("//ul[@id='packstationAddressForm']/li[10]/input", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //$this->clickAndWait("//button[text()='Continue to Next Step']");
    //	$this->clickAndWait("//button[text()='Purchase']");

    //	$this->loginAdmin("Administer Orders", "Orders");


        /*
        //changes in dhlCod payment. it is not available for Wellpaket anymore.
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("Total Products (gross): 0,84 €", $this->getText("//div[@id='basketSummary']//tr[3]"));
        $this->assertEquals("Shipping cost 32,10 €", $this->getText("//div[@id='basketSummary']//tr[4]"));
        $this->assertEquals("Surcharge Type of Payment: 5,00 €", $this->getText("//div[@id='basketSummary']//tr[5]"));
        $this->assertEquals("Grand Total: 37,94 €", $this->getText("//div[@id='basketSummary']//tr[6]"));
        $this->clickAndWait("//button[text()='Submit Order']");
        $this->assertTrue($this->isTextPresent("We registered your order"));
        */
    }

    /**
     * dhl in frontend
     * @group dhl
     */
    public function testDhlEuropaket()
    {
        //testing search
        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("1", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //TODO: change when templates will be applied for 4.5.1
         $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //changing country to Austria
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invCountrySelect");
        $this->select("invCountrySelect", "label=Austria");
        $this->type("invadr[oxuser__oxfon]", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertFalse($this->isElementPresent("//option[@value='efidhl']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhlwelt']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhleuro']"), "Dhl connector for live testing is not fully setupped");

        //weltpaket
        if ("Weltpaket" != $this->getSelectedLabel("sShipSet")) {
            $this->selectAndWait("sShipSet", "label=Weltpaket");
        }

        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Charges: 32,00 €", $this->getText("shipSetCost"));
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 32,00 €", $this->getText("shipSetCost"));
        $this->assertTrue($this->isElementPresent("payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("payment_oxidcashondel"));

        //euro paket
        $this->selectAndWait("sShipSet", "label=DHL Europaket");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->assertEquals("Charges: 14,00 €", $this->getText("shipSetCost"));
        $this->click("//input[@value='efidhleuroggrn']");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->assertEquals("Charges: 14,10 €", $this->getText("shipSetCost"));
        $this->assertFalse($this->isElementPresent("payment_efidhlcod"));
        $this->assertTrue($this->isElementPresent("payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("payment_oxidcashondel"));
        $this->assertFalse($this->isElementPresent("payment_oxidcreditcard"));

    }


    /**
    * dhl with packstation
    * @group dhl
    */
    public function testDhlPackstation()
    {
        //testing search
        $this->openShop();
        $this->searchFor("1401");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("1", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");

        //openBasket
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->assertEquals("Charges: 6,00 €", $this->getText("shipSetCost"));
        $this->clickAndWait("//button[text()='Select Packstation']");
        $this->select("packstationId","label=New Address");

        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[4]//input[@name='deladr[oxaddress__oxfname]']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[5]//input[@name='deladr[oxaddress__oxlname]']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[6]//input[@name='deladr[oxaddress__oxaddinfo]']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[7]//input[@name='deladr[oxaddress__oxstreetnr]']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxzip]']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxcity]']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='packstationAddressForm']//li[9]//select[@name='deladr[oxaddress__oxcountryid]']"));

        // go to fill packstation form
        $this->type("//ul[@id='packstationAddressForm']//li[4]//input[@name='deladr[oxaddress__oxfname]']", "shipping name_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[5]//input[@name='deladr[oxaddress__oxlname]']", "shipping surname_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[6]//input[@name='deladr[oxaddress__oxaddinfo]']", "post_nr_321");
        $this->type("//ul[@id='packstationAddressForm']//li[7]//input[@name='deladr[oxaddress__oxstreetnr]']", "packst_nr_123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxzip]']", "PP123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxcity]']", "shipping city_šÄßüл");
        $this->select("//ul[@id='packstationAddressForm']//li[9]//select[@name='deladr[oxaddress__oxcountryid]']", "label=Germany");
       // $this->type("//ul[@id='packstationAddressForm']/li[10]/input", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertFalse($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));
        $this->assertTrue($this->isTextPresent("DHL Packstation"));
        $this->assertEquals("DHL Packstation", $this->getText("//div[@id='content']/form[2]/div/p[2]/b"));
        $this->assertTrue($this->isElementPresent("css=button.submitButton.largeButton"));

        $this->assertEquals("Continue to payment selection", $this->getText("css=button[name=\"savedelservices\"]"));
        $this->clickAndWait("//button[text()='Continue to payment selection']");

        $this->assertTrue($this->isElementPresent("css=option[value='efidhl']"));
        $this->assertTrue($this->isElementPresent("css=option[value='efidhlwelt']"));
        $this->assertTrue($this->isElementPresent("css=option[value='efidhleuro']"));

        $this->click("id=paymentBackStepBottom");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent("Mr shipping name_šÄßüл"));
        $this->assertTrue($this->isTextPresent("shipping surname_šÄßüл"));
        $this->assertTrue($this->isTextPresent("post_nr_321"));
        $this->assertTrue($this->isTextPresent("packst_nr_123"));
        $this->assertTrue($this->isTextPresent("PP123"));
        $this->assertTrue($this->isTextPresent("shipping city_šÄßüл"));
        $this->assertTrue($this->isTextPresent("Germany"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");


        $this->assertTrue($this->isTextPresent("Change shipping address"));
        $this->assertEquals("DHL Paket Weltpaket DHL Europaket", $this->getText("name=sShipSet"));


    //$this->assertEquals("GoGreen ", $this->getText("//img[@src='http://testshops/461maintenance/modules/oe/efi_dhl/out/img/dhl-gogreen-logo.png']"));
    //$this->assertTrue($this->isElementPresent("//a[@target='_blank']/img[@src='http://testshops/461maintenance/modules/oe/efi_dhl/out/img/dhl_qmark.png']"));
    //$this->assertTrue($this->isElementPresent("//a[@target='_blank']/img[@src='http://testshops/461maintenance/modules/oe/efi_dhl/out/img/dhl_qmark.png']"));
    //$this->assertTrue($this->isElementPresent("//a[@target='_blank']/img[@src='http://testshops/461maintenance/modules/oe/efi_dhl/out/img/dhl_qmark.png']"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Mr shipping name_šÄßüл"));
        $this->assertTrue($this->isTextPresent("shipping surname_šÄßüл"));
        $this->assertTrue($this->isTextPresent("post_nr_321"));
        $this->assertTrue($this->isTextPresent("packst_nr_123"));
        $this->assertTrue($this->isTextPresent("PP123"));
        $this->assertTrue($this->isTextPresent("shipping city_šÄßüл"));
        $this->assertTrue($this->isTextPresent("Germany"));
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertTrue($this->isTextPresent("We registered your order"));

        //go to Admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->frame("list");
        $this->clickAndWait("link=2");
        $this->frame("edit");

     //  $this->assertEquals("Billing Address: \n \n Company SeleniumTestCase Äß'ü\n Testing acc for Selenium\n Mr Testing user acc Äß'ü PayPal Äß'ü\n Musterstr. Äß'ü 1\n 79098 Musterstadt Äß'ü\n Germany\n \n E-mail: birute_test@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
     //   $this->assertEquals("Shipping Address: post_nr_321 Mr shipping name_šÄßüл shipping surname_šÄßüл Packstation packst_nr_123 PP123 shipping city_šÄßüл Germany", $this->clearString($this->getText("//td[2]")));


        $this->selectAndWait("setfolder", "label=Finished");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("129,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("108,40", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("20,60", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("6,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("DHL Paket", $this->getText("//table[4]/tbody/tr[2]/td[2]"));
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=Finished");

        $this->openTab("link=Main", "editval[oxorder__oxordernr]");
        $this->assertEquals("2", $this->getValue("editval[oxorder__oxordernr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxbillnr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxtrackcode]"));
        $this->assertEquals("6", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $this->assertEquals("DHL Paket", $this->getSelectedLabel("setDelSet"));

        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("20,60", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));;

    }


    /**
     * dhl in frontend
     * @group dhl
     */
    public function testDhlPackstationTabDHL()
    {
        //testing search
        $this->openShop();
        $this->searchFor("1401");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("1", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");

        //openBasket
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->assertEquals("Charges: 6,00 €", $this->getText("shipSetCost"));

        $this->clickAndWait("//button[text()='Select Packstation']");
        $this->select("packstationId","label=New Address");

        // go to fill packstation form
        $this->type("//ul[@id='packstationAddressForm']//li[4]//input[@name='deladr[oxaddress__oxfname]']", "shipping name_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[5]//input[@name='deladr[oxaddress__oxlname]']", "shipping surname_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[6]//input[@name='deladr[oxaddress__oxaddinfo]']", "post_nr_321");
        $this->type("//ul[@id='packstationAddressForm']//li[7]//input[@name='deladr[oxaddress__oxstreetnr]']", "packst_nr_123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxzip]']", "PP123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxcity]']", "shipping city_šÄßüл");
        $this->select("//ul[@id='packstationAddressForm']//li[9]//select[@name='deladr[oxaddress__oxcountryid]']", "label=Germany");
        $this->type("//ul[@id='packstationAddressForm']/li[10]/input", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertFalse($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));
        $this->assertTrue($this->isTextPresent("DHL Packstation"));
        $this->assertEquals("DHL Packstation", $this->getText("//div[@id='content']/form[2]/div/p[2]/b"));
        $this->assertTrue($this->isElementPresent("css=button.submitButton.largeButton"));

        $this->assertEquals("Continue to payment selection", $this->getText("css=button[name=\"savedelservices\"]"));
        $this->assertTrue($this->isElementPresent("css=option[value='efidhl']"));
        $this->assertTrue($this->isElementPresent("css=option[value='efidhlwelt']"));
        $this->assertTrue($this->isElementPresent("css=option[value='efidhleuro']"));
        $this->clickAndWait("//button[text()='Continue to payment selection']");
        $this->click("id=paymentBackStepBottom");
        $this->waitForPageToLoad("30000");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Change shipping address"));
        $this->assertEquals("DHL Paket Weltpaket DHL Europaket", $this->getText("name=sShipSet"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
       // $this->clickAndWait("//button[text()='Submit Order']");
        $this->assertTrue($this->isTextPresent("We registered your order"));

        //go to Admin

        $this->loginAdmin("Administer Orders", "Orders");
        $this->frame("list");
        $this->clickAndWaitFrame("link=2", "edit");
        $this->openTab("link=DHL");

        //// go to DHL tab
        $this->assertTrue($this->isTextPresent("DHL Product"));
        $this->assertTrue($this->isTextPresent("Services"));
        $this->assertTrue($this->isElementPresent("//select[@name='dhl_product']/option[@value='DHL_PROD_01']"));
        $this->assertEquals("DHL Paket", $this->getText("//select[@name='dhl_product']/option[@value='DHL_PROD_01']"));
        $this->assertEquals("Weltpaket", $this->getText("//select[@name='dhl_product']/option[@value='DHL_PROD_53']"));
        $this->assertEquals("DHL Europaket", $this->getText("//select[@name='dhl_product']/option[@value='DHL_PROD_54']"));

        $this->assertEquals("GoGreen", $this->getText("//select[@id='_product_DHL_PROD_01']//option[@value='GGRN']"));
        $this->assertEquals("Cash On Delivery", $this->getText("//select[@id='_product_DHL_PROD_01']/option[@value='COD']"));
        $this->assertEquals("Multipack Delivery, will be delivered alltogether", $this->getText("//select[@id='_product_DHL_PROD_01']/option[@value='MPKCONS']"));
        $this->assertEquals("Insurance A", $this->getText("//select[@id='_product_DHL_PROD_01']/option[@value='INS_A']"));
        $this->assertEquals("Bulkfreight", $this->getText("//select[@id='_product_DHL_PROD_01']/option[@value='BLKFRGHT']"));
        $this->assertEquals("Multipack Delivery, will be delivered partially", $this->getText("//select[@id='_product_DHL_PROD_01']/option[@value='MPKPRT']"));
        $this->assertEquals("Personally", $this->getText("//select[@id='_product_DHL_PROD_01']/option[@value='PRSNLLY']"));

        $this->assertTrue($this->isElementPresent("//div[@class='buttons']//input[@id='_submit_print']"));
        $this->assertTrue($this->isElementPresent("//div[@class='buttons']//input[@id='_submit_reprint']"));
        $this->assertTrue($this->isElementPresent("//div[@class='buttons']//input[@id='_submit_cancel']"));


        $this->assertTrue($this->isElementPresent("//div[@class='buttons']/input[@id='helpBtn_HELP_EFIDHL_PRINT_LABEL']"));
        $this->assertTrue($this->isElementPresent("//div[@class='buttons']/input[@id='helpBtn_HELP_EFIDHL_REPRINT_LABEL']"));
        $this->assertTrue($this->isElementPresent("//div[@class='buttons']/input[@id='helpBtn_HELP_EFIDHL_CANCEL_SHIPMENT']"));

        //checked services "Multipack Delivery, will be delivered alltogether"
        $this->select("//select[@id='_product_DHL_PROD_01']", "value=MPKCONS");
        $this->assertTrue($this->isTextPresent("Number of packages"));
        $this->assertEquals("1", $this->getText("//select[@id='_package_count']/option[@value='1']"));
        $this->assertEquals("2", $this->getText("//select[@id='_package_count']/option[@value='2']"));
        $this->assertEquals("3", $this->getText("//select[@id='_package_count']/option[@value='3']"));
        $this->assertEquals("4", $this->getText("//select[@id='_package_count']/option[@value='4']"));
        $this->assertEquals("5", $this->getText("//select[@id='_package_count']/option[@value='5']"));
        $this->assertEquals("6", $this->getText("//select[@id='_package_count']/option[@value='6']"));
        $this->assertEquals("7", $this->getText("//select[@id='_package_count']/option[@value='7']"));
        $this->assertEquals("8", $this->getText("//select[@id='_package_count']/option[@value='8']"));
        $this->assertEquals("9", $this->getText("//select[@id='_package_count']/option[@value='9']"));
        $this->assertEquals("10", $this->getText("//select[@id='_package_count']/option[@value='10']"));
        $this->assertEquals("11", $this->getText("//select[@id='_package_count']/option[@value='11']"));

        //checked services "Multipack Delivery, will be delivered partially"
        $this->select("//select[@id='_product_DHL_PROD_01']", "value=MPKPRT");
        $this->assertTrue($this->isTextPresent("Number of packages"));
        $this->assertEquals("1", $this->getText("//select[@id='_package_count']/option[@value='1']"));
        $this->assertEquals("2", $this->getText("//select[@id='_package_count']/option[@value='2']"));
        $this->assertEquals("3", $this->getText("//select[@id='_package_count']/option[@value='3']"));
        $this->assertEquals("4", $this->getText("//select[@id='_package_count']/option[@value='4']"));
        $this->assertEquals("5", $this->getText("//select[@id='_package_count']/option[@value='5']"));
        $this->assertEquals("6", $this->getText("//select[@id='_package_count']/option[@value='6']"));
        $this->assertEquals("7", $this->getText("//select[@id='_package_count']/option[@value='7']"));
        $this->assertEquals("8", $this->getText("//select[@id='_package_count']/option[@value='8']"));
        $this->assertEquals("9", $this->getText("//select[@id='_package_count']/option[@value='9']"));
        $this->assertEquals("10", $this->getText("//select[@id='_package_count']/option[@value='10']"));
        $this->assertEquals("11", $this->getText("//select[@id='_package_count']/option[@value='11']"));

    }
   /**
    * DHL in neto mode
    * @group dhl
    */
    public function testDhlPackstationNetoMode()
    {
        // Go to admin and activate the necessary options Neto mode
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=VAT");
        $this->check("//input[@name='confbools[blShowNetPrice]'and @value='true']");
        $this->check("//input[@name='confbools[blShowVATForDelivery]'and @value='true']");
        $this->clickAndWait("save");
        //Go to shop and add product 1401
	    $this->openShop();
        $this->searchFor("1401");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("1", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //OpenBasket
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
		//Select label DHL Paket
        $this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->assertEquals("Charges: 5,04 € (plus VAT 0,96 €)", $this->getText("shipSetCost"));
		$this->assertEquals("Additional shipping services", $this->getText("deliveryServicesHeader"));
	    $this->assertEquals("GoGreen 0,08 € (plus VAT 0,02 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
		$this->assertEquals("Delivery to addressee only 1,51 € (plus VAT 0,29 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[2]")));
		$this->assertEquals("Transport insurance (up to 500,00 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[3]")));
		$this->assertEquals("Supplementary insurance 2,94 € (plus VAT 0,56 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[4]")));
		//Europaket
		$this->selectAndWait("sShipSet", "label=DHL Europaket");
		$this->assertEquals("Charges: 11,76 € (plus VAT 2,24 €)", $this->getText("shipSetCost"));
		$this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."),"error message is not displayed in fronted");
		$this->selectAndWait("sShipSet", "label=Weltpaket");
		$this->assertEquals("Charges: 26,89 € (plus VAT 5,11 €)", $this->getText("shipSetCost"));
		$this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."),"error message is not displayed in fronted");
        //select DHL Paket
        $this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->clickAndWait("//button[text()='Select Packstation']");
        $this->select("packstationId","label=New Address");
        // go to fill packstation form
        $this->type("//ul[@id='packstationAddressForm']//li[4]//input[@name='deladr[oxaddress__oxfname]']", "shipping name_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[5]//input[@name='deladr[oxaddress__oxlname]']", "shipping surname_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[6]//input[@name='deladr[oxaddress__oxaddinfo]']", "post_nr_321");
        $this->type("//ul[@id='packstationAddressForm']//li[7]//input[@name='deladr[oxaddress__oxstreetnr]']", "packst_nr_123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxzip]']", "PP123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxcity]']", "shipping city_šÄßüл");
        $this->select("//ul[@id='packstationAddressForm']//li[9]//select[@name='deladr[oxaddress__oxcountryid]']", "label=Germany");
        $this->type("//ul[@id='packstationAddressForm']/li[10]/input", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
		 //checking dhl paket in neto mode
		$this->assertEquals("GoGreen 0,08 € (plus VAT 0,02 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")), "Additional shipping services (Go green) price in neto mode is not displayed");
		$this->assertEquals("Delivery to addressee only 1,51 € (plus VAT 0,29 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[2]")),  "Additional shipping services  (Delivery to addressee) price in neto mode is not displayed");
		$this->assertEquals("Transport insurance (up to 500,00 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[3]")));
		$this->assertEquals("Supplementary insurance 2,94 € (plus VAT 0,56 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[4]")));
		$this->click("//input[@value='efidhlggrn']");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
	    $this->assertEquals("GoGreen (0,08 € plus VAT 0,02 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Mr shipping name_šÄßüл"), "Mr shipping name_šÄßüл is not displayed");
        $this->assertTrue($this->isTextPresent("shipping surname_šÄßüл"), "shipping surname_šÄßüл is not displayed");
        $this->assertTrue($this->isTextPresent("post_nr_321"),  "post_nr_321 is not displayed");
        $this->assertTrue($this->isTextPresent("packst_nr_123"));
        $this->assertTrue($this->isTextPresent("PP123"));
        $this->assertTrue($this->isTextPresent("shipping city_šÄßüл"));
        $this->assertTrue($this->isTextPresent("Germany"));
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertTrue($this->isTextPresent("We registered your order"));
        //Go to admin and check the order
        $this->loginAdmin("Administer Orders", "Orders");
        $this->frame("list");
        $this->clickAndWait("link=2");
        $this->frame("edit");
        $this->selectAndWait("setfolder", "label=Finished");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("108,40", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"), "Product Net Price is not displayed");
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"), "Discount price is not displayed");
        $this->assertEquals("20,60", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"), "VAT (19%) price is not displayed");
        $this->assertEquals("129,00", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"), "Product Gross Price is not displayed");
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]/td[1]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("6,10", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("DHL Paket", $this->getText("//table[4]/tbody/tr[2]/td[2]"));
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=Finished");
		// go to Main tab
        $this->openTab("link=Main", "editval[oxorder__oxordernr]");
        $this->assertEquals("2", $this->getValue("editval[oxorder__oxordernr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxbillnr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxtrackcode]"));
        $this->assertEquals("6.1", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $this->assertEquals("DHL Paket", $this->getSelectedLabel("setDelSet"));
       //go to Overview tab
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("129,00", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
    }
	
    /**
    * dhl with proportional Vat
    * @group dhl
    */
	public function testDhlPackstationProportionalVat()
    {
       // Go to admin and activate the necessary options for proportional Vat
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=VAT");
        $this->check("//input[@name='confbools[blShowVATForDelivery]'and @value='true']");
		$this->click("//input[@value='proportional']");
        $this->clickAndWait("save");
        //Testing search
	    $this->openShop();
        $this->searchFor("1008");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
	    $this->searchFor("1009");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->waitForItemAppear("countValue");
        $this->assertEquals("2", $this->getText("countValue"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //openBasket
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
		$this->clickAndWait("//button[text()='Continue to Next Step']");
		$this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->clickAndWait("//button[text()='Select Packstation']");
        $this->select("packstationId","label=New Address");
        //Go to fill packstation form
        $this->type("//ul[@id='packstationAddressForm']//li[4]//input[@name='deladr[oxaddress__oxfname]']", "shipping name_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[5]//input[@name='deladr[oxaddress__oxlname]']", "shipping surname_šÄßüл");
        $this->type("//ul[@id='packstationAddressForm']//li[6]//input[@name='deladr[oxaddress__oxaddinfo]']", "post_nr_321");
        $this->type("//ul[@id='packstationAddressForm']//li[7]//input[@name='deladr[oxaddress__oxstreetnr]']", "packst_nr_123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxzip]']", "PP123");
        $this->type("//ul[@id='packstationAddressForm']//li[8]//input[@name='deladr[oxaddress__oxcity]']", "shipping city_šÄßüл");
        $this->select("//ul[@id='packstationAddressForm']//li[9]//select[@name='deladr[oxaddress__oxcountryid]']", "label=Germany");
        $this->type("//ul[@id='packstationAddressForm']/li[10]/input", "+49 761 36889-0");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
		//DHL Paket
        $this->assertEquals("Charges: 5,54 € (plus VAT 0,46 €)", $this->getText("shipSetCost"));
		$this->assertEquals("Additional shipping services", $this->getText("deliveryServicesHeader"));
	    $this->assertEquals("GoGreen 0,09 € (plus VAT 0,01 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")),"Additional shipping services (Go green) price in neto mode is not displayed");
		$this->assertEquals("Delivery to addressee only 1,66 € (plus VAT 0,14 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[2]")),"Additional shipping services (Delivery to addressee only) price in neto mode is not displayed");
		$this->assertEquals("Transport insurance (up to 500,00 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[3]")), "Additional shipping services (Transport insurance) price in neto mode is not displayed");
		$this->assertEquals("Supplementary insurance 3,23 € (plus VAT 0,27 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[4]")), "Additional shipping services (Supplementary insurance) price in neto mode is not displayed");
		//Europaket 
		$this->selectAndWait("sShipSet", "label=DHL Europaket");
		$this->assertEquals("Charges: 12,92 € (plus VAT 1,08 €)", $this->getText("shipSetCost"));
		$this->assertFalse($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."),"error message is displayed in fronted");
		$this->assertEquals("GoGreen 0,09 € (plus VAT 0,01 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
		//Welpaket
		$this->selectAndWait("sShipSet", "label=Weltpaket");
		$this->assertEquals("Charges: 29,54 € (plus VAT 2,46 €)", $this->getText("shipSetCost"));
		$this->assertFalse($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."),"error message is displayed in fronted");
        $this->assertEquals("GoGreen 0,09 € (plus VAT 0,01 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
		$this->selectAndWait("sShipSet", "label=DHL Paket");	
        $this->click("//input[@value='efidhlggrn']");
        $this->clickAndWait("//button[text()='Continue to payment selection']");
	    $this->assertEquals("GoGreen (0,09 € plus VAT 0,01 €)", $this->clearString($this->getText("//form[@id='dhlpayment']//dl[1]")));
        $this->clickAndWait("//button[text()='Continue to Next Step']");;
        $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
        $this->assertTrue($this->isTextPresent("We registered your order"));
    }
}
