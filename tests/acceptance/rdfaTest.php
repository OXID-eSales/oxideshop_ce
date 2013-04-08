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

class Acceptance_rdfaTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    /**
     * testing RDFa. Business entity
     * @group rdfa
     */
    public function testBusinessEntity()
    {
        //RDFa is disabled
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='About Us']");
        $this->assertEquals("You are here: / About Us", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("//div[@typeof='gr:BusinessEntity']"));
        $this->assertFalse($this->isElementPresent("//div[@property='gr:legalName vcard:fn' and @content='Your Company Name']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:country-name' and @content='United States']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:locality' and @content='Any City, CA']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:postal-code' and @content='9041']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:street-address' and @content='2425 Maple Street']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:tel' and @content='217-8918712']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:fax' and @content='217-8918713']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:latitude' and @content='20']"));
        $this->assertFalse($this->isElementPresent("//div[@property='vcard:longitude' and @content='10']"));
        $this->assertFalse($this->isElementPresent("//div[@rel='vcard:logo foaf:logo' and @content='http://www.oxid-esales.com/files/logo-claim-header.png']"));
        $this->assertFalse($this->isElementPresent("//div[@property='gr:hasDUNS' and @content='123456789']"));
        $this->assertFalse($this->isElementPresent("//div[@property='gr:hasGlobalLocationNumber' and @content='123456789']"));


        //switch on and select options in Technical configuration tab
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=RDFa", "link=Global configuration");

        $this->click("link=Global configuration");
        $this->assertEquals("false", $this->getValue("confbools[blRDFaEmbedding]"));
        $this->assertEquals("Terms and Conditions", $this->getSelectedLabel("confstrs[sRDFaBusinessEntityLoc]"));
        $this->assertEquals("Terms and Conditions", $this->getSelectedLabel("confstrs[sRDFaPaymentChargeSpecLoc]"));
        $this->assertEquals("Terms and Conditions", $this->getSelectedLabel("confstrs[sRDFaDeliveryChargeSpecLoc]"));
        $this->assertEquals("", $this->getValue("confstrs[iRDFaMinRating]"));
        $this->assertEquals("", $this->getValue("confstrs[iRDFaMaxRating]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaLogoUrl]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaLongitude]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaLatitude]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaGLN]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaISIC]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaNAICS]"));
        $this->assertEquals("", $this->getValue("confstrs[sRDFaDUNS]"));
        $this->assertEquals("-", $this->getSelectedLabel("confstrs[iRDFaVAT]"));
        $this->assertEquals("-", $this->getSelectedLabel("confstrs[iRDFaCondition]"));
        $this->assertEquals("None of the shown", $this->getSelectedLabel("confstrs[sRDFaBusinessFnc]"));
        $this->assertEquals("-", $this->getSelectedLabel("confstrs[iRDFaOfferingValidity]"));
        $this->assertEquals("-", $this->getSelectedLabel("confstrs[iRDFaPriceValidity]"));

        $this->check("//input[@name='confbools[blRDFaEmbedding]' and @value='true']");
        $this->select("confstrs[sRDFaBusinessEntityLoc]", "label=About Us");
        $this->select("confstrs[sRDFaPaymentChargeSpecLoc]", "label=Terms and Conditions");
        $this->select("confstrs[sRDFaDeliveryChargeSpecLoc]", "label=Shipping and Charges");
        $this->type("confstrs[iRDFaMinRating]", "0");
        $this->type("confstrs[iRDFaMaxRating]", "5");

        //select options in Provider information tab
        $this->click("link=Shop information");
        $this->type("confstrs[sRDFaLogoUrl]", "http://www.oxid-esales.com/files/logo-claim-header.png");
        $this->type("confstrs[sRDFaLongitude]", "10");
        $this->type("confstrs[sRDFaLatitude]", "20");
        $this->type("confstrs[sRDFaGLN]", "123456789");
        $this->type("confstrs[sRDFaISIC]", "123456789");
        $this->type("confstrs[sRDFaNAICS]", "123456789");
        $this->type("confstrs[sRDFaDUNS]", "123456789");

        //select options in Global special offer data tab
        $this->click("link=Special product information");
        $this->select("confstrs[iRDFaVAT]", "label=incl. VAT.");
        $this->select("confstrs[iRDFaCondition]", "label=new");
        $this->select("confstrs[sRDFaBusinessFnc]", "label=Lease out");
        $this->select("confarrs[aRDFaCustomers][]", "label=End user");
        $this->select("confstrs[iRDFaOfferingValidity]", "label=7 days (1 week)");
        $this->select("confstrs[iRDFaPriceValidity]", "label=14 days (2 weeks)");
        $this->check("//input[@name='confbools[blShowRDFaProductStock]' and @value='true']");

        $this->clickAndWait("save");

        // check is option selected correct

        $this->click("link=Global configuration");
        $this->click("link=Shop information");
        $this->click("link=Special product information");
      //  $this->assertEquals("true", $this->getValue("confbools[blRDFaEmbedding]"));
        $this->assertEquals("About Us", $this->getSelectedLabel("confstrs[sRDFaBusinessEntityLoc]"));
        $this->assertEquals("Terms and Conditions", $this->getSelectedLabel("confstrs[sRDFaPaymentChargeSpecLoc]"));
        $this->assertEquals("Shipping and Charges", $this->getSelectedLabel("confstrs[sRDFaDeliveryChargeSpecLoc]"));
        $this->assertEquals("0", $this->getValue("confstrs[iRDFaMinRating]"));
        $this->assertEquals("5", $this->getValue("confstrs[iRDFaMaxRating]"));
        $this->assertEquals("http://www.oxid-esales.com/files/logo-claim-header.png", $this->getValue("confstrs[sRDFaLogoUrl]"));
        $this->assertEquals("10", $this->getValue("confstrs[sRDFaLongitude]"));
        $this->assertEquals("20", $this->getValue("confstrs[sRDFaLatitude]"));
        $this->assertEquals("123456789", $this->getValue("confstrs[sRDFaGLN]"));
        $this->assertEquals("123456789", $this->getValue("confstrs[sRDFaISIC]"));
        $this->assertEquals("123456789", $this->getValue("confstrs[sRDFaNAICS]"));
      //  $this->assertEquals("123456789", $this->getValue("confstrs[sRDFaDUNS]"));
        $this->assertEquals("incl. VAT.", $this->getSelectedLabel("confstrs[iRDFaVAT]"));
        $this->assertEquals("new", $this->getSelectedLabel("confstrs[iRDFaCondition]"));
        $this->assertEquals("Lease out", $this->getSelectedLabel("confstrs[sRDFaBusinessFnc]"));
        $this->assertEquals("End user", $this->getSelectedLabel("confarrs[aRDFaCustomers][]"));
        $this->assertEquals("7 days (1 week)", $this->getSelectedLabel("confstrs[iRDFaOfferingValidity]"));
        $this->assertEquals("14 days (2 weeks)", $this->getSelectedLabel("confstrs[iRDFaPriceValidity]"));


        $this->openShop();


        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='About Us']");
        $this->assertEquals("You are here: / About Us", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//div[@typeof='gr:BusinessEntity']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:legalName vcard:fn' and @content='Your Company Name']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:country-name' and @content='United States']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:locality' and @content='Any City, CA']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:postal-code' and @content='9041']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:street-address' and @content='2425 Maple Street']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:tel' and @content='217-8918712']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:fax' and @content='217-8918713']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:latitude' and @content='20']"));
        $this->assertTrue($this->isElementPresent("//div[@property='vcard:longitude' and @content='10']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='vcard:logo foaf:logo' and @resource='http://www.oxid-esales.com/files/logo-claim-header.png']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasDUNS' and @content='123456789']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasGlobalLocationNumber' and @content='123456789']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasNAICS' and @content='123456789']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasISICv4' and @content='123456789']"));

        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");

        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasMinValue' and @content='15']"));
        $this->assertTrue($this->isElementPresent("//div[@typeof='gr:Offering']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:name' and @content='Test product 0 [EN] šÄßüл']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:description' and @content=' Test product 0 long description [EN] šÄßüл ']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasStockKeepingUnit' and @content='1000']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:valueAddedTaxIncluded' and @content='true']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:condition' and @content='new']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:hasBusinessFunction' and @resource='http://purl.org/goodrelations/v1#LeaseOut']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:eligibleCustomerTypes' and @resource='http://purl.org/goodrelations/v1#Enduser']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrency' and @content='EUR']"));
        $this->assertTrue($this->isElementPresent("//p[@id='currencyTrigger']//*[text()='EUR']"));
        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[5]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertTrue($this->isElementPresent("//p[@id='currencyTrigger']//*[text()='USD']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrency' and @content='USD']"));

        //cheking review  tags
        $this->clickAndWait("link=Home");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->assertTrue($this->isTextPresent("No review available for this item."));
        $this->assertEquals("No ratings.", $this->getText("itemRatingText"));
        $this->click("writeNewReview");
        sleep(1);
        $this->click("//ul[@id='reviewRating']/li[@class='s4']/a");
        sleep(1);
        $this->type("rvw_txt", "recommendation for this list");
        $this->clickAndWait("reviewSave");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->assertTrue($this->isElementPresent("//div[@property='v:rating' and @content='4.2']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:validFrom']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:validThrough']"));

    }


    /**
     * testing RDFa. Payment Methods entity
     * @group rdfa
     */
    public function testRDFaPaymentMethodsInfo()
    {
        if (!isSUBSHOP) {
        //RDFa is disabled. Go to Terms and Conditions page to confirm that RDFa info is missing
        $this->openShop();
        $this->searchFor("1");
        $this->clickAndWait("searchList_1");
        $this->assertFalse($this->isElementPresent("//div[@typeof='gr:BusinessEntity']"));
        $this->assertFalse($this->isElementPresent("//div[@typeof='gr:PaymentMethod']"));

        //switch RDFa ON and select options in Global configuration tab
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=RDFa", "link=Global configuration");

        $this->click("link=Global configuration");
        $this->assertEquals("false", $this->getValue("confbools[blRDFaEmbedding]"));
        $this->check("//input[@name='confbools[blRDFaEmbedding]' and @value='true']");
        $this->select("confstrs[sRDFaBusinessEntityLoc]", "label=About Us");
        $this->select("confstrs[sRDFaPaymentChargeSpecLoc]", "label=Terms and Conditions");
        $this->select("confstrs[sRDFaDeliveryChargeSpecLoc]", "label=Shipping and Charges");
        //select options in Global special offer data tab
        $this->click("link=Global configuration");
        $this->select("confstrs[iRDFaVAT]", "label=incl. VAT.");
        $this->clickAndWait("save");


        // Check NOT mapped Payment Methods info in Terms and Conditions page
        //go to Payment Methods to set needed RDFa options for...
        $this->selectMenu("Shop Settings", "Payment Methods");
        $this->clickAndWaitFrame("link=2", "list");
        // disable Method: Test payment method [EN] šÄßüл (which is not active, to check if not actives methods are not included in RDFa results)
        $this->clickAndWaitFrame("link=Test payment method [EN] šÄßüл", "edit");
        $this->frame("edit");
        $this->uncheck("editval[oxpayments__oxactive]");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        //$this->clickAndWaitFrame("document.myedit.save[1]", "list");
    /*    // Method: Empty - map the "Empty" method to any option (then it should not be displayed in frontend)
        $this->frame("list");
        $this->clickAndWaitFrame("link=Empty", "list");
        $this->openTab("link=RDFa");
        $this->check("//input[@name='ardfapayments[]' and @value='Cash']");
        $this->clickAndWait("save");
*/

        $this->openShop();
        $this->searchFor("1");
        $this->clickAndWait("searchList_1");
        // change the language in frontend to DE and check if got correct texts about
        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->click("//ul[@id='languages']/li[2]/a");
        $this->waitForItemDisappear("languages");

        //go to Payment Methods to set needed RDFa options for...
        $this->loginAdmin("Shop Settings", "Payment Methods");

        // Method: Cash in advance
        $this->clickAndWaitFrame("link=Cash in advance", "list");
        $this->openTab("link=RDFa");
        $this->check("//input[@name='ardfapayments[]' and @value='ByBankTransferInAdvance']");
        $this->clickAndWait("save");
        $this->frame("list");
        $this->clickAndWaitFrame("link=2", "list");

        // Method: COD (Cash on Delivery)
        $this->clickAndWaitFrame("link=COD (Cash on Delivery)", "edit");
        $this->frame("edit");
        $this->check("//input[@name='ardfapayments[]' and @value='COD']");
        $this->clickAndWait("save");

        // Method: Credit Card
        $this->frame("list");
        $this->clickAndWaitFrame("link=Credit Card", "edit");
        $this->frame("edit");
        $this->check("//input[@name='ardfapayments[]' and @value='AmericanExpress']");
        $this->check("//input[@name='ardfapayments[]' and @value='DinersClub']");
        $this->check("//input[@name='ardfapayments[]' and @value='Discover']");
        $this->check("//input[@name='ardfapayments[]' and @value='JCB']");
        $this->check("//input[@name='ardfapayments[]' and @value='MasterCard']");
        $this->check("//input[@name='ardfapayments[]' and @value='VISA']");
        $this->clickAndWait("save");

        // Method: Direct Debit
        $this->frame("list");
        $this->clickAndWaitFrame("link=Direct Debit", "edit");
        $this->frame("edit");
        $this->check("//input[@name='ardfapayments[]' and @value='DirectDebit']");
        $this->clickAndWait("save");

        // Method: Invoice
        $this->frame("list");
        $this->clickAndWaitFrame("link=Invoice", "edit");
        $this->frame("edit");
        $this->check("//input[@name='ardfapayments[]' and @value='ByInvoice']");
        $this->clickAndWait("save");

        // Method: Empty (all other options set for this payment method)
        $this->frame("list");
        $this->clickAndWaitFrame("link=Empty", "edit");
        $this->frame("edit");
        $this->check("//input[@name='ardfapayments[]' and @value='CheckInAdvance']");
        $this->check("//input[@name='ardfapayments[]' and @value='GoogleCheckout']");
        $this->check("//input[@name='ardfapayments[]' and @value='PayPal']");
        $this->check("//input[@name='ardfapayments[]' and @value='PaySwarm']");
        $this->clickAndWait("save");

        // go to frontend to check the RDFa info results in "Terms and Conditions" page

        $this->openShop();
        $this->searchFor("1");
        $this->clickAndWait("searchList_1");

        $sLocation = $this->getLocation();
        $this->assertFalse($this->isElementPresent("//div[@about='{$sLocation}#COD(CashonDelivery)_oxidcashondel' and @typeof='gr:PaymentMethod']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='COD (Cash on Delivery)']"));
        $this->assertFalse($this->isElementPresent("//div[@about='{$sLocation}#CreditCard_oxidcreditcard' and @typeof='gr:PaymentMethod']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Credit Card']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:comment' and @content='Your Credit Card will be charged when you submit the order.']"));
        $this->assertFalse($this->isElementPresent("//div[@about='{$sLocation}#DirectDebit_oxiddebitnote' and @typeof='gr:PaymentMethod']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Direct Debit']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:comment' and @content='Your bank account will be charged when the order is shipped.']"));
        $this->assertFalse($this->isElementPresent("//div[@about='{$sLocation}#Cashinadvance_oxidpayadvance' and @typeof='gr:PaymentMethod']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Cash in advance']"));
        $this->assertFalse($this->isElementPresent("//div[@about='{$sLocation}#Invoice_oxidinvoice' and @typeof='gr:PaymentMethod']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Invoice']"));
        $this->assertFalse($this->isElementPresent("//div[@about='{$sLocation}#Empty_oxempty' and @typeof='gr:PaymentMethod']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Empty']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:comment' and @content='An example. Maybe for use with other countries']"));
        $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Test payment method [EN] šÄßüл']"));


        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#COD']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#VISA']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#MasterCard']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#JCB']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#Discover']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#DinersClub']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#AmericanExpress']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#DirectDebit']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#ByBankTransferInAdvance']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#ByInvoice']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#DirectDebit']"));
    }
    }

    /**
     * testing RDFa. Shipping Methods entity
     * @group rdfa
     */
    public function testRDFaShippingMethodsInfo()
    {
        if (!isSUBSHOP) {
        //RDFa is disabled. Go to Shipping and Charges page to confirm that RDFa info is missing
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Shipping and Charges']");
        $this->assertEquals("You are here: / Shipping and Charges", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("//div[@typeof='gr:BusinessEntity']"));
     //   $this->assertFalse($this->isElementPresent("//div[@typeof='gr:DeliveryMethod']"));

        //switch RDFa on and select options in Technical configuration tab
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=RDFa", "link=Global configuration");
        $this->check("//input[@name='confbools[blRDFaEmbedding]' and @value='true']");
       // $this->check("confbools[blRDFaEmbedding]");

        $this->select("confstrs[sRDFaBusinessEntityLoc]", "label=About Us");
        $this->select("confstrs[sRDFaPaymentChargeSpecLoc]", "label=Terms and Conditions");
        $this->select("confstrs[sRDFaDeliveryChargeSpecLoc]", "label=Shipping and Charges");

        //select options in Global special offer data tab
        $this->select("confstrs[iRDFaVAT]", "label=incl. VAT.");
        $this->clickAndWait("save");
        $this->click("link=Global configuration");

        $this->assertEquals("About Us", $this->getSelectedLabel("confstrs[sRDFaBusinessEntityLoc]"));
        $this->assertEquals("Terms and Conditions", $this->getSelectedLabel("confstrs[sRDFaPaymentChargeSpecLoc]"));
        $this->assertEquals("Shipping and Charges", $this->getSelectedLabel("confstrs[sRDFaDeliveryChargeSpecLoc]"));

        $this->storeChecked("//input[@name='confbools[blRDFaEmbedding]' and @value='true']");

        //go to Shipping Methods to set needed RDFa options for...
        $this->selectMenu("Shop Settings", "Shipping Methods");

        // disable Method: 1 EN test S&H set šÄßüл
        $this->clickAndWaitFrame("link=1 EN test S&H set šÄßüл", "list");
        $this->frame("edit");
        $this->uncheck("editval[oxdeliveryset__oxactive]");
        $this->clickAndWait("save");
        sleep(10);
        // Method: Test S&H set [EN] šÄßüл map with FedEx method
        $this->frame("list");
        $this->clickAndWaitFrame("link=Test S&H set [EN] šÄßüл", "list");
        $this->openTab("link=RDFa");
        $this->check("//input[@name='ardfadeliveries[]' and @value='FederalExpress']");
        $this->clickAndWait("save");
        $this->frame("list");

        // go to frontend to check the RDFa info results in "Shipping and Charges" page
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Shipping and Charges']");
        // check if disabled method is not included
       // $this->assertFalse($this->isElementPresent("//div[@property='rdfs:label' and @content='Test S&H set [EN] šÄßüл']"));
        // check if there is info about active not mapped methods

        $sLocation = $this->getLocation();
        $this->assertTrue($this->isElementPresent("//div[@about='{$sLocation}#Standard_oxidstandard' and @typeof='gr:DeliveryMethod']"));
        $this->assertTrue($this->isElementPresent("//div[@property='rdfs:label' and @content='Standard']"));
        $this->assertTrue($this->isElementPresent("//div[@about='{$sLocation}#ExampleSet1:UPS48hours_1b842e732a23255b1.91207750' and @typeof='gr:DeliveryMethod']"));
        $this->assertTrue($this->isElementPresent("//div[@property='rdfs:label' and @content='Example Set1: UPS 48 hours']"));
        $this->assertTrue($this->isElementPresent("//div[@about='{$sLocation}#ExampleSet2:UPSExpress24hours_1b842e732a23255b1.91207751' and @typeof='gr:DeliveryMethod']"));
        $this->assertTrue($this->isElementPresent("//div[@property='rdfs:label' and @content='Example Set2: UPS Express 24 hours']"));
        $this->assertTrue($this->isElementPresent("//div[@typeof='gr:DeliveryChargeSpecification' and @about='{$sLocation}#1b842e734b62a4775.45738618']"));
        $this->assertTrue($this->isElementPresent("//div[@property='rdfs:comment' and @content='Shipping costs for Standard: Free shipping for orders over $80']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:validFrom' and @datatype='xsd:dateTime']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:validThrough' and @datatype='xsd:dateTime']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:valueAddedTaxIncluded' and @content='true' and @datatype='xsd:boolean']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrency' and @content='EUR' and @datatype='xsd:string']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrencyValue' and @content='0' and @datatype='xsd:float']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:eligibleTransactionVolume']"));
        $this->assertTrue($this->isElementPresent("//div[@typeof='gr:UnitPriceSpecification']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasUnitOfMeasurement' and @content='C62' and @datatype='xsd:string']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrency' and @content='EUR' and @datatype='xsd:string']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasMinCurrencyValue' and @content='80' and @datatype='xsd:float']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasMaxCurrencyValue' and @content='999999' and @datatype='xsd:float']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#Standard_oxidstandard']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:eligibleRegions' and @content='DE' and @datatype='xsd:string']"));
        $this->assertTrue($this->isElementPresent("//div[@typeof='gr:DeliveryChargeSpecification' and @about='{$sLocation}#testdel']"));
        $this->assertTrue($this->isElementPresent("//div[@property='rdfs:comment' and @content='Test delivery category [EN] šÄßüл']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:validFrom' and @datatype='xsd:dateTime']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:validThrough' and @datatype='xsd:dateTime']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:valueAddedTaxIncluded' and @content='true' and @datatype='xsd:boolean']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrency' and @content='EUR' and @datatype='xsd:string']"));
        $this->assertTrue($this->isElementPresent("//div[@property='gr:hasCurrencyValue' and @content='1.5' and @datatype='xsd:float']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:appliesToDeliveryMethod' and @resource='http://purl.org/goodrelations/v1#FederalExpress']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:eligibleRegions' and @content='DE' and @datatype='xsd:string']"));

        $this->searchFor("1");
        $this->clickAndWait("searchList_1");

        $this->assertTrue($this->isElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#Standard_oxidstandard']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#ExampleSet1:UPS48hours_1b842e732a23255b1.91207750']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#ExampleSet2:UPSExpress24hours_1b842e732a23255b1.91207751']"));
        $this->assertTrue($this->isElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='http://purl.org/goodrelations/v1#FederalExpress']"));
        }

    }
}
