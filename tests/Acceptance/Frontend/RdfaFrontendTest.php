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

class RdfaFrontendTest extends FrontendTestCase
{
    /**
     * Testing RDFa. Business entity
     * @group rdfa
     */
    public function testBusinessEntity()
    {
        //RDFa is disabled
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='About Us']");
        $this->assertEquals("You are here: / About Us", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("//div[@typeof='gr:BusinessEntity']");
        $this->assertElementNotPresent("//div[@property='gr:legalName vcard:fn' and @content='Your Company Name']");
        $this->assertElementNotPresent("//div[@property='vcard:country-name' and @content='United States']");
        $this->assertElementNotPresent("//div[@property='vcard:locality' and @content='Any City, CA']");
        $this->assertElementNotPresent("//div[@property='vcard:postal-code' and @content='9041']");
        $this->assertElementNotPresent("//div[@property='vcard:street-address' and @content='2425 Maple Street']");
        $this->assertElementNotPresent("//div[@property='vcard:tel' and @content='217-8918712']");
        $this->assertElementNotPresent("//div[@property='vcard:fax' and @content='217-8918713']");
        $this->assertElementNotPresent("//div[@property='vcard:latitude' and @content='20']");
        $this->assertElementNotPresent("//div[@property='vcard:longitude' and @content='10']");
        $this->assertElementNotPresent("//div[@rel='vcard:logo foaf:logo' and @content='http://www.oxid-esales.com/files/logo-claim-header.png']");
        $this->assertElementNotPresent("//div[@property='gr:hasDUNS' and @content='123456789']");
        $this->assertElementNotPresent("//div[@property='gr:hasGlobalLocationNumber' and @content='123456789']");

        $this->_turnOnRdfa();

        $this->openShop();

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='About Us']");
        $this->assertEquals("You are here: / About Us", $this->getText("breadCrumb"));
        $this->assertElementPresent("//div[@typeof='gr:BusinessEntity']");
        $this->assertElementPresent("//div[@property='gr:legalName vcard:fn' and @content='Your Company Name']");
        $this->assertElementPresent("//div[@property='vcard:country-name' and @content='United States']");
        $this->assertElementPresent("//div[@property='vcard:locality' and @content='Any City, CA']");
        $this->assertElementPresent("//div[@property='vcard:postal-code' and @content='9041']");
        $this->assertElementPresent("//div[@property='vcard:street-address' and @content='2425 Maple Street']");
        $this->assertElementPresent("//div[@property='vcard:tel' and @content='217-8918712']");
        $this->assertElementPresent("//div[@property='vcard:fax' and @content='217-8918713']");
        $this->assertElementPresent("//div[@property='vcard:latitude' and @content='20']");
        $this->assertElementPresent("//div[@property='vcard:longitude' and @content='10']");
        $this->assertElementPresent("//div[@rel='vcard:logo foaf:logo' and @resource='http://www.oxid-esales.com/files/logo-claim-header.png']");
        $this->assertElementPresent("//div[@property='gr:hasDUNS' and @content='123456789']");
        $this->assertElementPresent("//div[@property='gr:hasGlobalLocationNumber' and @content='123456789']");
        $this->assertElementPresent("//div[@property='gr:hasNAICS' and @content='123456789']");
        $this->assertElementPresent("//div[@property='gr:hasISICv4' and @content='123456789']");

        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");

        $this->assertElementPresent("//div[@property='gr:hasMinValue' and @content='15']");
        $this->assertElementPresent("//div[@typeof='gr:Offering']");
        $this->assertElementPresent("//div[@property='gr:name' and @content='Test product 0 [EN] šÄßüл']");
        $this->assertElementPresent("//div[@property='gr:description' and @content=' Test product 0 long description [EN] šÄßüл ']");
        $this->assertElementPresent("//div[@property='gr:hasStockKeepingUnit' and @content='1000']");
        $this->assertElementPresent("//div[@property='gr:valueAddedTaxIncluded' and @content='true']");
        $this->assertElementPresent("//div[@property='gr:condition' and @content='new']");
        $this->assertElementPresent("//div[@rel='gr:hasBusinessFunction' and @resource='http://purl.org/goodrelations/v1#LeaseOut']");
        $this->assertElementPresent("//div[@rel='gr:eligibleCustomerTypes' and @resource='http://purl.org/goodrelations/v1#Enduser']");
        $this->assertElementPresent("//div[@property='gr:hasCurrency' and @content='EUR']");
        $this->assertElementPresent("//p[@id='currencyTrigger']//*[text()='EUR']");
        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[5]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertElementPresent("//p[@id='currencyTrigger']//*[text()='USD']");
        $this->assertElementPresent("//div[@property='gr:hasCurrency' and @content='USD']");

        //checking review  tags
        $this->clickAndWait("link=Home");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->assertTextPresent("No review available for this item.");
        $this->assertEquals("No ratings", $this->getText("itemRatingText"));
        $this->click("writeNewReview");
        $this->click("//ul[@id='reviewRating']/li[@class='s4']/a");
        $this->type("rvw_txt", "recommendation for this list");
        $this->clickAndWait("reviewSave");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Log out']");
        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");

        $this->assertElementPresent("//div[@property='gr:validFrom']");
        $this->assertElementPresent("//div[@property='gr:validThrough']");
    }

    /**
     * testing RDFa. Payment Methods entity
     * @group rdfa
     */
    public function testRDFaPaymentMethodsInfo()
    {
        if (!isSUBSHOP) {
            //RDFa is disabled. Go to Terms and Conditions page to confirm that RDFa info is missing
            $this->clearCache();
            $this->openShop();
            $this->searchFor("1");
            $this->clickAndWait("searchList_1");
            $this->assertElementNotPresent("//div[@typeof='gr:BusinessEntity']");
            $this->assertElementNotPresent("//div[@typeof='gr:PaymentMethod']");

            $this->_turnOnRdfa();

            $this->openShop();
            $this->searchFor("1");
            $this->clickAndWait("searchList_1");
            // change the language in frontend to DE and check if got correct texts about
            $this->click("languageTrigger");
            $this->waitForItemAppear("languages");
            $this->click("//ul[@id='languages']/li[2]/a");
            $this->waitForItemDisappear("languages");

            $this->_setAllPaymentMethodRdfaOptions();

            // go to frontend to check the RDFa info results in "Terms and Conditions" page
            $this->clearCache();
            $this->openShop();
            $this->searchFor("1");
            $this->clickAndWait("searchList_1");

            $sLocation = $this->getLocation();
            $this->assertElementNotPresent("//div[@about='{$sLocation}#COD(CashonDelivery)_oxidcashondel' and @typeof='gr:PaymentMethod']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='COD (Cash on Delivery)']");
            $this->assertElementNotPresent("//div[@about='{$sLocation}#CreditCard_oxidcreditcard' and @typeof='gr:PaymentMethod']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='Credit Card']");
            $this->assertElementNotPresent("//div[@property='rdfs:comment' and @content='Your Credit Card will be charged when you submit the order.']");
            $this->assertElementNotPresent("//div[@about='{$sLocation}#DirectDebit_oxiddebitnote' and @typeof='gr:PaymentMethod']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='Direct Debit']");
            $this->assertElementNotPresent("//div[@property='rdfs:comment' and @content='Your bank account will be charged when the order is shipped.']");
            $this->assertElementNotPresent("//div[@about='{$sLocation}#Cashinadvance_oxidpayadvance' and @typeof='gr:PaymentMethod']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='Cash in advance']");
            $this->assertElementNotPresent("//div[@about='{$sLocation}#Invoice_oxidinvoice' and @typeof='gr:PaymentMethod']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='Invoice']");
            $this->assertElementNotPresent("//div[@about='{$sLocation}#Empty_oxempty' and @typeof='gr:PaymentMethod']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='Empty']");
            $this->assertElementNotPresent("//div[@property='rdfs:comment' and @content='An example. Maybe for use with other countries']");
            $this->assertElementNotPresent("//div[@property='rdfs:label' and @content='Test payment method [EN] šÄßüл']");

            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#COD']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#VISA']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#MasterCard']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#JCB']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#Discover']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#DinersClub']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#AmericanExpress']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#DirectDebit']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#ByBankTransferInAdvance']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#ByInvoice']");
            $this->assertElementPresent("//div[@rel='gr:acceptedPaymentMethods' and @resource='http://purl.org/goodrelations/v1#DirectDebit']");
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
            $this->clearCache();
            $this->openShop();
            $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Shipping and Charges']");
            $this->assertEquals("You are here: / Shipping and Charges", $this->getText("breadCrumb"));
            $this->assertElementNotPresent("//div[@typeof='gr:BusinessEntity']");

            $this->_turnOnRdfa();

            $this->_setDeliverySetRdfaOption('testdelset', 'FederalExpress');

            // go to frontend to check the RDFa info results in "Shipping and Charges" page
            $this->clearCache();
            $this->openShop();
            $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Shipping and Charges']");

            $sLocation = $this->getLocation();
            $this->assertElementPresent("//div[@about='{$sLocation}#Standard_oxidstandard' and @typeof='gr:DeliveryMethod']");
            $this->assertElementPresent("//div[@property='rdfs:label' and @content='Standard']");
            $this->assertElementPresent("//div[@about='{$sLocation}#ExampleSet1:UPS48hours_1b842e732a23255b1.91207750' and @typeof='gr:DeliveryMethod']");
            $this->assertElementPresent("//div[@property='rdfs:label' and @content='Example Set1: UPS 48 hours']");
            $this->assertElementPresent("//div[@about='{$sLocation}#ExampleSet2:UPSExpress24hours_1b842e732a23255b1.91207751' and @typeof='gr:DeliveryMethod']");
            $this->assertElementPresent("//div[@property='rdfs:label' and @content='Example Set2: UPS Express 24 hours']");
            $this->assertElementPresent("//div[@typeof='gr:DeliveryChargeSpecification' and @about='{$sLocation}#1b842e734b62a4775.45738618']");
            $this->assertElementPresent("//div[@property='rdfs:comment' and @content='Shipping costs for Standard: Free shipping for orders over $80']");
            $this->assertElementPresent("//div[@property='gr:validFrom' and @datatype='xsd:dateTime']");
            $this->assertElementPresent("//div[@property='gr:validThrough' and @datatype='xsd:dateTime']");
            $this->assertElementPresent("//div[@property='gr:valueAddedTaxIncluded' and @content='true' and @datatype='xsd:boolean']");
            $this->assertElementPresent("//div[@property='gr:hasCurrency' and @content='EUR' and @datatype='xsd:string']");
            $this->assertElementPresent("//div[@property='gr:hasCurrencyValue' and @content='0' and @datatype='xsd:float']");
            $this->assertElementPresent("//div[@rel='gr:eligibleTransactionVolume']");
            $this->assertElementPresent("//div[@typeof='gr:UnitPriceSpecification']");
            $this->assertElementPresent("//div[@property='gr:hasUnitOfMeasurement' and @content='C62' and @datatype='xsd:string']");
            $this->assertElementPresent("//div[@property='gr:hasMinCurrencyValue' and @content='80' and @datatype='xsd:float']");
            $this->assertElementPresent("//div[@property='gr:hasMaxCurrencyValue' and @content='999999' and @datatype='xsd:float']");
            $this->assertElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#Standard_oxidstandard']");
            $this->assertElementPresent("//div[@rel='gr:eligibleRegions' and @content='DE' and @datatype='xsd:string']");
            $this->assertElementPresent("//div[@typeof='gr:DeliveryChargeSpecification' and @about='{$sLocation}#testdel']");
            $this->assertElementPresent("//div[@property='rdfs:comment' and @content='Test delivery category [EN] šÄßüл']");
            $this->assertElementPresent("//div[@property='gr:hasCurrencyValue' and @content='1.5' and @datatype='xsd:float']");
            $this->assertElementPresent("//div[@rel='gr:appliesToDeliveryMethod' and @resource='http://purl.org/goodrelations/v1#FederalExpress']");

            $this->searchFor("1");
            $this->clickAndWait("searchList_1");
            $this->assertElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#Standard_oxidstandard']");
            $this->assertElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#ExampleSet1:UPS48hours_1b842e732a23255b1.91207750']");
            $this->assertElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='{$sLocation}#ExampleSet2:UPSExpress24hours_1b842e732a23255b1.91207751']");
            $this->assertElementPresent("//div[@rel='gr:availableDeliveryMethods' and @resource='http://purl.org/goodrelations/v1#FederalExpress']");
        }
    }

    /**
     * Turn on RDFA.
     */
    private function _turnOnRdfa()
    {
        $aRdfaSettings['sRDFaBusinessEntityLoc'] = array("type" => "str", "value" =>  "oximpressum");
        $aRdfaSettings['sRDFaPaymentChargeSpecLoc'] = array("type" => "str", "value" =>  "oxagb");
        $aRdfaSettings['sRDFaDeliveryChargeSpecLoc'] = array("type" => "str", "value" =>  "oxdeliveryinfo");
        $aRdfaSettings['iRDFaMinRating'] = array("type" => "str", "value" => "0");
        $aRdfaSettings['iRDFaMaxRating'] = array("type" => "str", "value" => "5");

        $aRdfaSettings['sRDFaLogoUrl'] = array("type" => "str", "value" => "http://www.oxid-esales.com/files/logo-claim-header.png");
        $aRdfaSettings['sRDFaLongitude'] = array("type" => "str", "value" => "10");
        $aRdfaSettings['sRDFaLatitude'] = array("type" => "str", "value" => "20");
        $aRdfaSettings['sRDFaGLN'] = array("type" => "str", "value" => "123456789");
        $aRdfaSettings['sRDFaISIC'] = array("type" => "str", "value" => "123456789");
        $aRdfaSettings['sRDFaNAICS'] = array("type" => "str", "value" => "123456789");
        $aRdfaSettings['sRDFaDUNS'] = array("type" => "str", "value" => "123456789");

        $aRdfaSettings['iRDFaVAT'] = array("type" => "str", "value" => "1");
        $aRdfaSettings['iRDFaCondition'] = array("type" => "str", "value" => "new");
        $aRdfaSettings['sRDFaBusinessFnc'] = array("type" => "str", "value" => "LeaseOut");
        $aRdfaSettings['aRDFaCustomers'] = array("type" => "arr", "value" => array("Enduser"));
        $aRdfaSettings['iRDFaOfferingValidity'] = array("type" => "str", "value" => "7");
        $aRdfaSettings['iRDFaPriceValidity'] = array("type" => "str", "value" => "14");
        $aRdfaSettings['blShowRDFaProductStock'] = array("type" => "bool", "value" => "true");
        $aRdfaSettings['blRDFaEmbedding'] = array("type" => "bool", "value" => "true");

        $this->callShopSC("oxConfig", null, null, $aRdfaSettings);
        $this->clearCache();
    }

    /**
     * @param $sDeliveryId
     * @param $sObjectId
     */
    private function _setDeliverySetRdfaOption($sDeliveryId, $sObjectId)
    {
        $aRdfaOption = array();
        $aRdfaOption['oxdeliveryid'] = $sDeliveryId;
        $aRdfaOption['oxobjectid'] = $sObjectId;
        $aRdfaOption['oxtype'] = 'rdfadeliveryset';
        $this->callShopSC('oxBase', 'save', 'oxobject2delivery', $aRdfaOption);
    }

    /**
     * @param $sPaymentId
     * @param $sObjectId
     */
    private function _setPaymentMethodRdfaOption($sPaymentId, $sObjectId)
    {
        $aRdfaOption = array();
        $aRdfaOption['oxpaymentid'] = $sPaymentId;
        $aRdfaOption['oxobjectid'] = $sObjectId;
        $aRdfaOption['oxtype'] = 'rdfapayment';
        $this->callShopSC('oxBase', 'save', 'oxobject2payment', $aRdfaOption);
    }

    /**
     * Set RDFA options for payment methods
     */
    protected function _setAllPaymentMethodRdfaOptions()
    {
        // Method: Cash in advance
        $this->_setPaymentMethodRdfaOption( 'oxidpayadvance', 'ByBankTransferInAdvance' );

        // Method: COD (Cash on Delivery)
        $this->_setPaymentMethodRdfaOption( 'oxidcashondel', 'COD' );

        // Method: Credit Card
        $this->_setPaymentMethodRdfaOption( 'oxidcreditcard', 'AmericanExpress' );
        $this->_setPaymentMethodRdfaOption( 'oxidcreditcard', 'DinersClub' );
        $this->_setPaymentMethodRdfaOption( 'oxidcreditcard', 'Discover' );
        $this->_setPaymentMethodRdfaOption( 'oxidcreditcard', 'JCB' );
        $this->_setPaymentMethodRdfaOption( 'oxidcreditcard', 'MasterCard' );
        $this->_setPaymentMethodRdfaOption( 'oxidcreditcard', 'VISA' );

        // Method: Direct Debit
        $this->_setPaymentMethodRdfaOption( 'oxiddebitnote', 'DirectDebit' );

        // Method: Invoice
        $this->_setPaymentMethodRdfaOption( 'oxidinvoice', 'ByInvoice' );

        // Method: Empty (all other options set for this payment method)
        $this->_setPaymentMethodRdfaOption( 'oxidempty', 'CheckInAdvance' );
        $this->_setPaymentMethodRdfaOption( 'oxidempty', 'GoogleCheckout' );
        $this->_setPaymentMethodRdfaOption( 'oxidempty', 'PayPal' );
        $this->_setPaymentMethodRdfaOption( 'oxidempty', 'PaySwarm' );
    }
}
