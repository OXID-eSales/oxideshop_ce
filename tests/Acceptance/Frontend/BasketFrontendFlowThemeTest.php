<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FlowThemeTestCase;

/**
 * Class BasketFrontendFlowThemeTest
 */
class BasketFrontendFlowThemeTest extends FlowThemeTestCase
{
    public function testChangeEmailAddressToExistingEmailInBasketForGuestUsers()
    {
        $this->addToBasket("1001");
        $this->addToBasket("1002-2");

        //Order Step1
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        $this->assertEquals("You are here:Address", $this->getText("breadcrumb"));
        $this->assertElementPresent("optionNoRegistration");
        $this->assertElementPresent("optionRegistration");
        $this->assertElementPresent("optionLogin");

        //Order without registration
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        //Order step2
        $this->assertEquals("You are here:Address", $this->getText("breadcrumb"));
        $this->assertElementPresent("//h3[text()='Customer information']");
        $this->assertElementPresent("//h3[text()='Billing address']");
        $this->assertElementNotPresent("optionNoRegistration");
        $this->assertElementNotPresent("optionRegistration");
        $this->assertElementNotPresent("optionLogin");
        $this->type("//input[@id='userLoginName']", "test1@oxid-esales.dev");
        $this->type("invadr[oxuser__oxfname]", "tname");
        $this->type("invadr[oxuser__oxlname]", "fname");
        $this->type("invadr[oxuser__oxstreet]", "test street");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "55555");
        $this->type("invadr[oxuser__oxcity]", "Berlin");
        $this->select("invadr[oxuser__oxcountryid]", "Germany");
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        //Order step 3
        $this->assertEquals("You are here:Pay", $this->getText("breadcrumb"));
        $this->assertElementNotPresent("//h3[text()='Customer information']");
        $this->assertElementNotPresent("//h3[text()='Billing address']");
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        // step 4
        $this->assertEquals("You are here:Order", $this->getText("breadcrumb"));
        $this->clickAndWait("checkAgbTop");
        $this->clickAndWait("//button[contains(@class, 'nextStep')]");

        // step 5
        $this->assertEquals("You are here:Order completed", $this->getText("breadcrumb"));

        // order again with another email ========================================================================

        $this->clickAndWait("link=%HOME%");
        $this->addToBasket("1001");
        $this->addToBasket("1002-2");

        //Order Step1
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        $this->assertEquals("You are here:Address", $this->getText("breadcrumb"));
        $this->assertElementPresent("optionNoRegistration");
        $this->assertElementPresent("optionRegistration");
        $this->assertElementPresent("optionLogin");

        //Order without registration
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        //Order step2
        $this->assertEquals("You are here:Address", $this->getText("breadcrumb"));
        $this->assertElementPresent("//h3[text()='Customer information']");
        $this->assertElementPresent("//h3[text()='Billing address']");
        $this->assertElementNotPresent("optionNoRegistration");
        $this->assertElementNotPresent("optionRegistration");
        $this->assertElementNotPresent("optionLogin");
        $this->type("//input[@id='userLoginName']", "test2@oxid-esales.dev");
        $this->type("invadr[oxuser__oxfname]", "tname");
        $this->type("invadr[oxuser__oxlname]", "fname");
        $this->type("invadr[oxuser__oxstreet]", "test street");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "55555");
        $this->type("invadr[oxuser__oxcity]", "Berlin");
        $this->select("invadr[oxuser__oxcountryid]", "Germany");
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        //Order step 3
        $this->assertEquals("You are here:Pay", $this->getText("breadcrumb"));
        $this->assertElementNotPresent("//h3[text()='Customer information']");
        $this->assertElementNotPresent("//h3[text()='Billing address']");
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        // step 4
        $this->assertEquals("You are here:Order", $this->getText("breadcrumb"));

        // back to order step 2
        $this->clickAndWait("//*[contains(text(), 'Address')]");
        $this->assertEquals("You are here:Address", $this->getText("breadcrumb"));

        // edit order address
        $this->clickAndWait("//button[@id='userChangeAddress']");
        $this->clearString($this->getText("invadr[oxuser__oxusername]"));
        $this->type("invadr[oxuser__oxusername]", "test1@oxid-esales.dev");
        $this->clickAndWait("//button[contains(text(), 'Continue to the next step')]");

        // check if next step is showing

        $this->assertEquals("You are here:Pay", $this->getText("breadcrumb"));
        $this->assertElementNotPresent("//h3[text()='Customer information']");
        $this->assertElementNotPresent("//h3[text()='Billing address']");
    }
}
