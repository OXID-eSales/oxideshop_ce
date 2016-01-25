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

namespace OxidEsales\Eshop\Tests\Acceptance\Admin;

use OxidEsales\Eshop\Tests\Acceptance\AdminTestCase;

/** Mall functionality: subshops & inheritance */
class TrustedShopsAdminTest extends AdminTestCase
{
    /**
     * Enable dynamic content loading.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->callShopSC("oxConfig", null, null, array("blLoadDynContents" => array("type" => "bool", "value" => 'true')));
        if (!file_exists($this->getReferenceToCredentialsFile())) {
            $this->markTestSkipped('Credentials file for Trusted shop tests must be created.');
        }
    }

    /**
     * testing trusted shops. seagel activation
     *
     * @group trustedShopsAdmin
     */
    public function testTsSeagel()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for subshops');
        }

        //trusted shops setup in admin
        $this->loginAdminTs();
        $this->assertElementPresent("aShopID_TrustedShops[0]");
        $this->type("aShopID_TrustedShops[0]", $this->getLoginDataByName('trustedShopsIdForLanguageDe'));
        $this->check("//input[@name='tsTestMode' and @value='true']");
        $this->check("//input[@name='tsSealActive' and @value='true']");

        $this->assertEquals("Direct debit Credit Card / Debit Card Invoice Cash on delivery Cash in advance Cheque Paybox PayPal Amazon Payments Cash on pickup Financing Leasing T-Pay Click&Buy Giropay Google Checkout Online shop payment card SOFORT Banking moneybookers.com / Skrill Dotpay Przelewy24 Other method of payment", $this->getText("paymentids[oxidcashondel]"));
        $this->assertTextPresent("Test payment method [EN] šÄßüл");
        $this->assertEquals("Direct debit Credit Card / Debit Card Invoice Cash on delivery Cash in advance Cheque Paybox PayPal Amazon Payments Cash on pickup Financing Leasing T-Pay Click&Buy Giropay Google Checkout Online shop payment card SOFORT Banking moneybookers.com / Skrill Dotpay Przelewy24 Other method of payment", $this->getText("paymentids[testpayment]"));
        $this->select("paymentids[testpayment]", "label=Credit Card / Debit Card");
        $this->clickAndWait("save");
        $this->assertTextNotPresent("Invalid Trusted Shops ID", "Invalid Trusted Shops ID for testing");
        $this->assertEquals("Credit Card / Debit Card", $this->getSelectedLabel("paymentids[testpayment]"));
        $this->assertEquals("on", $this->getValue("//input[@name='tsSealActive' and @value='true']"));
        $this->assertEquals("on", $this->getValue("//input[@name='tsTestMode' and @value='true']"));
        $this->assertEquals($this->getLoginDataByName('trustedShopsIdForLanguageDe'), $this->getValue("aShopID_TrustedShops[0]"));
        $this->type("aShopID_TrustedShops[0]", "nonExisting");
        $this->clickAndWait("save");
        $this->assertTextPresent("The certificate does not exist");
        $this->assertEquals($this->getLoginDataByName('trustedShopsIdForLanguageDe'), $this->getValue("aShopID_TrustedShops[0]"));
    }

    /**
     * testing trusted shops. excellence Ts. functionality depends on order price
     *
     * @group trustedShopsAdmin
     */
    public function testTsExcellence()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for subshops');
        }

        //setupping ts
        $this->loginAdminTs();
        $this->type("aShopID_TrustedShops[0]", $this->getLoginDataByName('trustedShopsIdForLanguageDe'));
        $this->type("aTsUser[0]", "testExcellencePartner");
        $this->type("aTsPassword[0]", "test12345678");
        $this->check("//input[@name='tsTestMode' and @value='true']");
        $this->clickAndWait("save");
        $this->assertTextNotPresent("Invalid Trusted Shops ID", "Invalid Trusted Shops ID for testing");
        $this->assertEquals($this->getLoginDataByName('trustedShopsIdForLanguageDe'), $this->getValue("aShopID_TrustedShops[0]"));
        $this->assertEquals("testExcellencePartner", $this->getValue("aTsUser[0]"));
        $this->assertEquals("test12345678", $this->getValue("aTsPassword[0]"));
        //$this->assertElementPresent("//div[@id='liste']/table/tbody/tr[2]/td[@class='active']");
        $this->check("//input[@name='tsSealActive' and @value='true']");
        $this->check("//input[@name='tsTestMode' and @value='true']");
        $this->clickAndWait("save");
        $this->assertTextNotPresent("Invalid Trusted Shops ID", "Invalid Trusted Shops ID for testing");

        $aOrderParams1 = $this->_getNewTestOrderParams1();
        $aOrderParams2 = $this->_getNewTestOrderParams2();

        $this->callShopSC("oxOrder", "save", null, $aOrderParams1, null, 1);
        $this->callShopSC("oxOrder", "save", null, $aOrderParams2, null, 1);

        //checking orders in admin
        //$this->loginAdmin("Administer Orders", "Orders");
        $this->selectMenu("Administer Orders", "Orders");
        $this->openListItem("link=12");
        $this->assertTextPresent("0,90");
        $this->openListItem("link=13");
        $this->assertTextPresent("2,72");
    }

     /**
     * testing trusted shops. Raiting of eShop
      *
     * @group trustedShopsAdmin
     */
    public function testTsRatings()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for subshops');
        }

        // trusted shops are disabled
        $aConfigParams = $this->callShopSC( "oxConfig", null, null, array( "blTsWidget" ), null, 1 );
        $blTsWidget = ( $aConfigParams['blTsWidget'] === true);
        $this->assertFalse($blTsWidget, "Widget should not be visible in shop");

         // setupping ts
        $this->loginAdminTs("link=Customer ratings", "//li[@id='nav-2-10-1']/a/b");
        $this->frame("list");
        $this->waitForElement("link=Interface");
        $this->clickAndWaitFrame("link=Interface", "edit");
        $this->frame("edit");
        $this->waitForElement("confaarrs[aTsLangIds][de]");
        $this->assertElementPresent("confaarrs[aTsLangIds][de]");
        $this->type("confaarrs[aTsLangIds][de]", $this->getLoginDataByName('trustedShopsIdForLanguageDe'));
        $this->type("confaarrs[aTsLangIds][en]", $this->getLoginDataByName('trustedShopsIdForLanguageEn'));
        $this->check("//input[@name='confbools[blTsWidget]' and @value='true']");
        $this->check("//input[@name='confbools[blTsThankyouReview]' and @value='true']");
        $this->check("//input[@name='confbools[blTsOrderEmailReview]' and @value='true']");
        $this->check("//input[@name='confbools[blTsOrderSendEmailReview]' and @value='true']");
        $this->clickAndWait("save");
        $this->assertEquals($this->getLoginDataByName('trustedShopsIdForLanguageDe'), $this->getValue("confaarrs[aTsLangIds][de]"));
        $this->assertEquals($this->getLoginDataByName('trustedShopsIdForLanguageEn'), $this->getValue("confaarrs[aTsLangIds][en]"));
        $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsWidget]' and @value='true']"));
        $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsThankyouReview]' and @value='true']"));
        $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsOrderEmailReview]' and @value='true']"));
        $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsOrderSendEmailReview]' and @value='true']"));
        $this->assertTextNotPresent("Invalid Trusted Shops ID. Please sign up here or contact");

        $this->type("confaarrs[aTsLangIds][de]", "NonExistingId");
        $this->clickAndWait("save");
        $this->assertTextPresent("Invalid Trusted Shops ID. Please sign up here or contact");
        $this->assertEquals("NonExistingId", $this->getValue("confaarrs[aTsLangIds][de]"));

        $this->type("confaarrs[aTsLangIds][de]", $this->getLoginDataByName('trustedShopsIdForLanguageDe'));
        $this->clickAndWait("save");
        if ($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact")) {
            $this->type("confaarrs[aTsLangIds][de]", $this->getLoginDataByName('trustedShopsIdForLanguageDe'));
            $this->clickAndWait("save");
        }
        $this->assertTextNotPresent("Invalid Trusted Shops ID. Please sign up here or contact");

        // trusted shops are enabled
        $aConfigParams = $this->callShopSC( "oxConfig", null, null, array( "blTsWidget" ), null, 1 );
        $blTsWidget = ( $aConfigParams['blTsWidget'] === true);
        $this->assertTrue($blTsWidget, "Widget should be visible in shop");
    }

    /**
     * @return array
     */
    protected function _getNewTestOrderParams1()
    {
        $aOrderParams1 = array(
            'OXSHOPID'          => 1,
            'OXUSERID'          => 'testuser',
            'OXORDERDATE'       => '2014-02-24 09:33:36',
            'OXORDERNR'         => 13,
            'OXBILLCOMPANY'     => 'UserCompany šÄßüл',
            'OXBILLEMAIL'       => 'example_test@oxid-esales.dev',
            'OXBILLFNAME'       => 'UserNamešÄßüл',
            'OXBILLLNAME'       => 'UserSurnamešÄßüл',
            'OXBILLSTREET'      => 'Musterstr.šÄßüл',
            'OXBILLSTREETNR'    => '1',
            'OXBILLADDINFO'     => 'User additional info šÄßüл',
            'OXBILLUSTIDSTATUS' => 1,
            'OXBILLCITY'        => 'Musterstadt šÄßüл',
            'OXBILLCOUNTRYID'   => 'a7c40f631fc920687.20179984',
            'OXBILLZIP'         => '79098',
            'OXBILLFON'         => '0800 111111',
            'OXBILLFAX'         => '0800 111112',
            'OXBILLSAL'         => 'MR',
            'OXPAYMENTID'       => 'c3e8e49e401beaf0e7c030ececffd19f',
            'OXPAYMENTTYPE'     => 'oxidcashondel',
            'OXTOTALNETSUM'     => 523.64,
            'OXTOTALBRUTSUM'    => 576,
            'OXTOTALORDERSUM'   => 591.98,
            'OXARTVAT1'         => 10,
            'OXARTVATPRICE1'    => 52.36,
            'OXDELCOST'         => 5.76,
            'OXDELVAT'          => 10,
            'OXPAYCOST'         => 7.5,
            'OXPAYVAT'          => 10,
            'OXGIFTCARDVAT'     => 10,
            'OXBILLDATE'        => '0000-00-00',
            'OXSENDDATE'        => '0000-00-00 00:00:00',
            'OXCURRENCY'        => 'EUR',
            'OXCURRATE'         => 1,
            'OXFOLDER'          => 'ORDERFOLDER_NEW',
            'OXPAID'            => '0000-00-00 00:00:00',
            'OXTRANSSTATUS'     => 'OK',
            'OXDELTYPE'         => 'testdelset',
            'OXTSPROTECTID'     => 'TS080501_1500_30_EUR',
            'OXTSPROTECTCOSTS'  => 2.72,
            'OXTIMESTAMP'       => '2014-02-24 08:33:36',
        );

        return $aOrderParams1;
    }

    /**
     * @return array
     */
    protected function _getNewTestOrderParams2()
    {
        $aOrderParams2 = array(
            'OXSHOPID'          => 1,
            'OXUSERID'          => 'testuser',
            'OXORDERDATE'       => '2014-02-24 09:27:16',
            'OXORDERNR'         => 12,
            'OXBILLCOMPANY'     => 'UserCompany šÄßüл',
            'OXBILLEMAIL'       => 'example_test@oxid-esales.dev',
            'OXBILLFNAME'       => 'UserNamešÄßüл',
            'OXBILLLNAME'       => 'UserSurnamešÄßüл',
            'OXBILLSTREET'      => 'Musterstr.šÄßüл',
            'OXBILLSTREETNR'    => '1',
            'OXBILLADDINFO'     => 'User additional info šÄßüл',
            'OXBILLUSTIDSTATUS' => 1,
            'OXBILLCITY'        => 'Musterstadt šÄßüл',
            'OXBILLCOUNTRYID'   => 'a7c40f631fc920687.20179984',
            'OXBILLZIP'         => '79098',
            'OXBILLFON'         => '0800 111111',
            'OXBILLFAX'         => '0800 111112',
            'OXBILLSAL'         => 'MR',
            'OXPAYMENTID'       => '88002604425dfc359eb2439661399cb9',
            'OXPAYMENTTYPE'     => 'oxidcashondel',
            'OXTOTALNETSUM'     => 87.27,
            'OXTOTALBRUTSUM'    => 96,
            'OXTOTALORDERSUM'   => 105.36,
            'OXARTVAT1'         => 10,
            'OXARTVATPRICE1'    => 8.73,
            'OXDELCOST'         => 0.96,
            'OXDELVAT'          => 10,
            'OXPAYCOST'         => 7.5,
            'OXPAYVAT'          => 10,
            'OXGIFTCARDVAT'     => 10,
            'OXBILLDATE'        => '0000-00-00',
            'OXSENDDATE'        => '0000-00-00 00:00:00',
            'OXCURRENCY'        => 'EUR',
            'OXCURRATE'         => 1,
            'OXFOLDER'          => 'ORDERFOLDER_NEW',
            'OXPAID'            => '0000-00-00 00:00:00',
            'OXTRANSSTATUS'     => 'OK',
            'OXDELTYPE'         => 'testdelset',
            'OXTSPROTECTID'     => 'TS080501_500_30_EUR',
            'OXTSPROTECTCOSTS'  => 0.9,
            'OXTIMESTAMP'       => '2014-02-24 08:27:16',
        );

        return $aOrderParams2;
    }

    /**
     * Returns Trusted shop login data by variable name.
     *
     * @param string $variableName
     * @return mixed|null|string
     * @throws Exception
     */
    protected function getLoginDataByName($variableName)
    {
        if (!$variableValue = getenv($variableName)) {
            $variableValue = $this->getArrayValueFromFile(
                $variableName,
                $this->getReferenceToCredentialsFile()
            );
        }

        if (!$variableValue) {
            throw new Exception('Undefined variable: ' . $variableName);
        }

        return $variableValue;
    }

    /**
     * Returns reference to file.
     *
     * @return string
     */
    protected function getReferenceToCredentialsFile()
    {
        return $this->getTestConfig()->getShopTestsPath() . '/acceptance/Admin/testData/trustedShopsData.php';
    }
}
