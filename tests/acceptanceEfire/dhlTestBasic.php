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

class AcceptanceEfire_dhlTestBasic extends oxidAdditionalSeleniumFunctions
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
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->waitForItemAppear("test_RightBasketTitle_1000_1");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhl']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhlwelt']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhleuro']"), "Dhl connector for live testing is not fully setupped");

        //weltpaket
        $this->selectAndWait("sShipSet", "label=Weltpaket");
		$this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));
		$this->clickAndWait("//a[@id='test_Step2']");
		$this->type("invadr[oxuser__oxfon]", "+49 761 36889-0");
		$this->clickAndWait("//a[@id='test_Step3']");
		
		$this->assertTrue($this->isElementPresent("css=option[value='efidhl']"));
		$this->assertTrue($this->isElementPresent("css=option[value='efidhlwelt']"));
		$this->assertTrue($this->isElementPresent("css=option[value='efidhleuro']"));

        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@name='order']/div/div")));
        $this->assertEquals("Charges: 32,00 €", $this->getText("test_shipSetCost"));
		$this->assertTrue($this->isElementPresent("//div[@class='left']//a[@href='http://www.dhl.de/en/paket/privatkunden/klimafreundlicher-versand.html']"));
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 32,00 €", $this->getText("test_shipSetCost"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("test_Payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcashondel"));

        //euro paket
        $this->selectAndWait("sShipSet", "label=DHL Europaket");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@name='order']/div/div")));
        $this->assertEquals("Charges: 14,00 €", $this->getText("test_shipSetCost"));
        $this->click("//input[@value='efidhleuroggrn']");
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 14,10 €", $this->getText("test_shipSetCost"));
        $this->assertFalse($this->isElementPresent("test_Payment_efidhlcod"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcashondel"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcreditcard"));

        //dhl paket
        $this->selectAndWait("sShipSet", "label=DHL Paket");
        $this->assertEquals("Charges: 6,00 €", $this->getText("test_shipSetCost"));
        $this->assertTrue($this->isElementPresent("//input[@value='efidhlggrn']"));
        $this->assertTrue($this->isElementPresent("//input[@value='efidhlprsnl']"));
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@name='order']/div/div[1]")));
        $this->assertEquals("Delivery to addressee only 1,80 €", $this->clearString($this->getText("//form[@name='order']/div/div[2]")));
        $this->assertEquals("Transport insurance (up to 500,00 €)", $this->clearString($this->getText("//form[@name='order']/div/div[4]")));
        $this->assertEquals("Supplementary insurance 3,50 €", $this->clearString($this->getText("//form[@name='order']/div/div[5]")));
        $this->click("//input[@value='efidhlinsa']");
        $this->click("//input[@value='efidhlggrn']");
        $this->click("//input[@value='efidhlprsnl']");
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 11,40 €", $this->getText("test_shipSetCost"));
        $this->assertEquals("GoGreen (0,10 €)", $this->clearString($this->getText("//div[@id='body']/form/div/div[1]")));
        $this->assertEquals("Delivery to addressee only (1,80 €)", $this->clearString($this->getText("//div[@id='body']/form/div/div[2]")));
        $this->assertEquals("Supplementary insurance (3,50 €)", $this->clearString($this->getText("//div[@id='body']/form/div/div[3]")));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidcashondel"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("//input[@value='efidhlggrn']"));
        $this->assertFalse($this->isElementPresent("//input[@value='efidhlprsnl']"));
        $this->clickAndWait("changedelservices");
        $this->assertEquals("Charges: 11,40 €", $this->getText("test_shipSetCost"));
        $this->click("//input[@value='efidhlprsnl']");
        $this->click("//input[@value='efidhlggrn']");
        $this->click("//form[@name='order']/div/div[4]//input");
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 6,00 €", $this->getText("test_shipSetCost"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidcashondel"));
        $this->assertTrue($this->isElementPresent("test_Payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcreditcard"));
        $this->assertFalse($this->isTextPresent("GoGreen"));
        $this->assertFalse($this->isTextPresent("Delivery to addressee only"));
        $this->assertFalse($this->isTextPresent("Supplementary insurance"));
        $this->click("test_Payment_efidhlcod");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("1,00 €", $this->getText("test_orderGrossPrice"));
        $this->assertEquals("6,00 €", $this->getText("test_orderShippingNet"));
        $this->assertEquals("5,00 €", $this->getText("test_orderPaymentNet"));
        $this->assertEquals("12,00 €", $this->getText("test_orderGrandTotal"));
        $this->clickAndWait("test_OrderSubmitBottom");
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
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->waitForItemAppear("test_RightBasketTitle_1000_1");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        //changing country to Liechtenstein
        $this->select("invadr[oxuser__oxcountryid]", "label=Liechtenstein");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->assertFalse($this->isElementPresent("//option[@value='efidhl']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhlwelt']"), "Dhl connector for live testing is not fully setupped");
        $this->assertFalse($this->isElementPresent("//option[@value='efidhleuro']"), "Dhl connector for live testing is not fully setupped");

        //weltpaket
        $this->assertEquals("Weltpaket", $this->getSelectedLabel("sShipSet"));
		$this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));
		$this->clickAndWait("//a[@id='test_Step2']");
		$this->type("invadr[oxuser__oxfon]", "+49 761 36889-0");
		$this->clickAndWait("//a[@id='test_Step3']");
        $this->assertTrue($this->isTextPresent("GoGreen"), "Dhl connector for live testing is not fully setupped");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@name='order']/div/div")));
        $this->assertEquals("Charges: 32,00 €", $this->getText("test_shipSetCost"));
        $this->click("//input[@value='efidhlweltggrn']");
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 32,10 €", $this->getText("test_shipSetCost"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("test_Payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcashondel"));
        $this->assertEquals("Payment Method", $this->getText("test_PaymentHeader"));
        $this->assertTrue($this->isTextPresent("Service configuration you choose has no payment methods available"));
        $this->assertFalse($this->isElementPresent("test_PaymentNextStepBottom"));
        /*
        //changes in dhlCod payment. it is not available for Wellpaket anymore.
        $this->assertEquals("0,84 €", $this->getText("test_orderGrossPrice"));
        $this->assertEquals("32,10 €", $this->getText("test_orderShippingNet"));
        $this->assertEquals("5,00 €", $this->getText("test_orderPaymentNet"));
        $this->assertEquals("37,94 €", $this->getText("test_orderGrandTotal"));
        $this->clickAndWait("test_OrderSubmitBottom");
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
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->waitForItemAppear("test_RightBasketTitle_1000_1");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        //changing country to Austria
        $this->select("invadr[oxuser__oxcountryid]", "label=Austria");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->assertFalse($this->isElementPresent("//option[@value='efidhl']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhlwelt']"), "Dhl connector for live testing is not fully setupped");
        $this->assertTrue($this->isElementPresent("//option[@value='efidhleuro']"), "Dhl connector for live testing is not fully setupped");

        //weltpaket
        if ("Weltpaket" != $this->getSelectedLabel("sShipSet")) {
            $this->selectAndWait("sShipSet", "label=Weltpaket");
        }
		$this->assertTrue($this->isTextPresent("Please enter a phone number. This is required for the shipment via Weltpaket."));
		$this->clickAndWait("//a[@id='test_Step2']");
		$this->type("invadr[oxuser__oxfon]", "+49 761 36889-0");
		$this->clickAndWait("//a[@id='test_Step3']");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@name='order']/div/div")));
        $this->assertEquals("Charges: 32,00 €", $this->getText("test_shipSetCost"));
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 32,00 €", $this->getText("test_shipSetCost"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("test_Payment_efidhlcod"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcashondel"));

        //euro paket
        $this->selectAndWait("sShipSet", "label=DHL Europaket");
        $this->assertEquals("GoGreen 0,10 €", $this->clearString($this->getText("//form[@name='order']/div/div")));
        $this->assertEquals("Charges: 14,00 €", $this->getText("test_shipSetCost"));
        $this->click("//input[@value='efidhleuroggrn']");
        $this->clickAndWait("//input[@value='Continue to payment selection']");
        $this->assertEquals("Charges: 14,10 €", $this->getText("test_shipSetCost"));
        $this->assertFalse($this->isElementPresent("test_Payment_efidhlcod"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcashondel"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidcreditcard"));
    }


}
