<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

class RdfaAdminTest extends AdminTestCase
{
    /**
     * testing RDFa. Business entity
     * @group rdfaAdmin
     */
    public function testBusinessEntity()
    {
        //switch on and select options in Technical configuration tab
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("RDFa");

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
    }
}
