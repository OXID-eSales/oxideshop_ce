<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

/**
 * Class ShippingAddressesFrontendTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Acceptance\Frontend
 */
class ShippingAddressesFrontendTest extends \OxidEsales\EshopCommunity\Tests\Acceptance\FlowThemeTestCase
{
    /**
     * Test that a user can delete shipping addresses in MyAccount and checkout step2
     *
     * @group flow-theme
     */
    public function testDeleteShippingAddress()
    {
        $this->add3ShippingAddressesToUser();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->openMyAddressesPage();
        $this->showShippingAddresses();
        $this->deleteShippingAddress();
        $this->expectedNumberOfShippingAddresses(2);
        $this->addToBasket("1001");
        $this->clickNextStep();
        $this->showShippingAddresses();
        $this->expectedNumberOfShippingAddresses(2);
        $this->deleteShippingAddress();
        $this->expectedNumberOfShippingAddresses(1);
    }

    private function add3ShippingAddressesToUser()
    {
        $sql = "INSERT INTO `oxaddress` (`OXID`,`OXUSERID`,`OXADDRESSUSERID`,`OXCOMPANY`,`OXFNAME`,`OXLNAME`,`OXSTREET`,
                `OXSTREETNR`,`OXADDINFO`,`OXCITY`,`OXCOUNTRY`,`OXCOUNTRYID`,`OXSTATEID`,`OXZIP`,`OXFON`,`OXFAX`,`OXSAL`,
                `OXTIMESTAMP`) VALUES
                ('9c1e6ec3a40a70d4e5dfb3a78089bc57','testuser','','','OtherFirstname','OtherLastname','Musterstr','8',
                '','Freiburg','Deutschland','a7c40f631fc920687.20179984','','79098','','','MRS','2018-04-09 13:37:15'),
                ('9c1e6ec3a40a70d4e5dfb3a78089bc58','testuser','','','Firstname2','Lastname2','Bertoldstr','48','',
                'Freiburg','Deutschland','a7c40f631fc920687.20179984','','79098','','','MRS','2018-04-09 13:37:15'),
                ('9c1e6ec3a40a70d4e5dfb3a78089bc59','testuser','','','Firstname3','Lastname3','Bertoldstr','48',
                '','Freiburg','Deutschland','a7c40f631fc920687.20179984','','79098','','','MR','2018-04-09 13:37:15');";

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->execute($sql);
    }

    private function clickNextStep()
    {
        $this->clickAndWait("//button[contains(@class,'nextStep')]");
    }

    private function openMyAddressesPage()
    {
        $this->openMyAccountPage();
        $this->clickAndWait("//a[@title='%BILLING_SHIPPING_SETTINGS%']");
    }

    private function openMyAccountPage()
    {
        $this->click("//div[contains(@class, 'service-menu')]/button");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li/a");
    }

    private function deleteShippingAddress()
    {
        $this->click("//div[contains(@class,'dd-available-addresses')]/div[1]/div/div/label");
        $this->click("//button[@data-target='#delete_shipping_address_1']");
        $this->clickAndWait("//div[@id='delete_shipping_address_1']//button[contains(@class, 'btn-danger')]");
    }

    private function showShippingAddresses()
    {
        $this->click("//input[@id='showShipAddress']");
    }

    /**
     * @param $expectedNumberOfShippingAddresses
     */
    private function expectedNumberOfShippingAddresses($expectedNumberOfShippingAddresses)
    {
        // "add new address" is also one box but should not be counted
        $actualNumberOfShippingAddresses =
            $this->getXpathCount("//div[contains(@class, 'dd-available-addresses')]/div") - 1;

        $this->assertEquals(
            $expectedNumberOfShippingAddresses,
            $actualNumberOfShippingAddresses,
            "Expected to see $expectedNumberOfShippingAddresses shipping addresses but 
            $actualNumberOfShippingAddresses shipping addresses are shown."
        );
    }
}
