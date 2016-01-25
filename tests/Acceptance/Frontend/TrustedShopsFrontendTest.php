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

namespace OxidEsales\Eshop\Tests\Acceptance\Frontend;

use Exception;
use OxidEsales\Eshop\Tests\Acceptance\FrontendTestCase;

/** Mall functionality: subshops & inheritance */
class TrustedShopsFrontendTest extends FrontendTestCase
{
    /**
     * Check if tests should be skipped.
     */
    protected function setUp()
    {
        parent::setUp();
        if (!file_exists($this->getReferenceToCredentialsFile())) {
            $this->markTestSkipped('Credentials file for Trusted shop tests must be created.');
        }
    }

    /**
     * testing trusted shops. excellence Ts. functionality depends on order price
     *
     * @group trustedShops
     */
    public function testTsExcellence()
    {
        if (isSUBSHOP) {
            $this->markTestSkipped('This test case is only actual when SubShops are available.');
        }
        $this->_prepareTestTsExcellence();

        $oValidator = $this->getObjectValidator();

        //checking in frontend. order < 500eur
        $this->clearCache();
        $this->openShop();

        $this->switchLanguage("Deutsch");
        $this->clickAndWait("//ul[@id='newItems']/li[2]/form//a");
        $this->clickAndWait("toBasket");
        $this->openBasket("Deutsch");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));
        $this->click("payment_oxidcashondel");
        $this->waitForItemAppear("bltsprotection");
        $this->assertFalse($this->isVisible("stsprotection"));
        $this->assertTextPresent("%TRUSTED_SHOP_PROTECTION_FROM% 500,00 € (0,90 € %INCLUDE_VAT%)");
        $this->check("bltsprotection");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertTextPresent("0,90 €");
        $this->assertTextPresent("%TRUSTED_SHOP_BUYER_PROTECTION%");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        $this->assertTrue($oValidator->validate('oxorder', array("OXTSPROTECTCOSTS" => "0.9")), $oValidator->getErrorMessage());

        //order > 500eur
        $this->clickAndWait("link=%HOME%");
        $this->clickAndWait("//ul[@id='newItems']/li[2]/form//a");
        $this->type("amountToBasket", "6");
        $this->clickAndWait("toBasket");
        $this->openBasket("Deutsch");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertElementPresent("bltsprotection");
        $this->assertElementPresent("stsprotection");
        $this->assertEquals("%TRUSTED_SHOP_PROTECTION_FROM% 500,00 € (0,90 € %INCLUDE_VAT%) %TRUSTED_SHOP_PROTECTION_FROM% 1.500,00 € (2,72 € %INCLUDE_VAT%)", $this->getText("stsprotection"));
        $this->select("stsprotection", "label=%TRUSTED_SHOP_PROTECTION_FROM% 1.500,00 € (2,72 € %INCLUDE_VAT%)");
        $this->check("bltsprotection");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertTextPresent("2,72 €");
        $this->assertTextPresent("%TRUSTED_SHOP_BUYER_PROTECTION%");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        $this->assertTrue($oValidator->validate('oxorder', array("OXTSPROTECTCOSTS" => "2.72")), $oValidator->getErrorMessage());
    }

     /**
     * testing trusted shops. Rating of eShop
      *
     * @group trustedShops
     */
    public function testTsRatings()
    {
        if (isSUBSHOP) {
            $this->markTestSkipped('This test case is only actual when SubShops are available.');
        }
        //trusted shops are disabled
        $this->clearCache();
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->assertElementNotPresent("test_RightSideTsWidgetBox");

        $this->_enableTsRatings();

        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->assertElementPresent('//div[@id[starts-with(.,"tsbadge_")]]');
    }

    /**
     * Testing trusted shops international delivery fees message.
     *
     * @group trustedShops
     */
    public function testTsInternationalFeesMessage()
    {
        if (isSUBSHOP) {
            $this->markTestSkipped('This test case is only actual when SubShops are available.');
        }

        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->switchLanguage("English");

        //add product to basket
        $this->clickAndWait("//ul[@id='newItems']/li[4]//button");

        //going to 4th checkout step
        $this->openBasket();
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertTextPresent("Delivery to non-EU countries may cause additional custom, taxes and fees");

        $this->switchLanguage("Deutsch");
        $this->assertTextPresent("Bei Lieferung ins Nicht-EU-Ausland können zusätzlich Zölle, Steuern und Gebühren anfallen");

        //disabling message
        $this->callShopSC("oxConfig", null, null, array("blShowTSInternationalFeesMessage" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->switchLanguage("English");

        //add product to basket
        $this->clickAndWait("//ul[@id='newItems']/li[4]//button");

        //going to 4th checkout step
        $this->openBasket();
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertTextNotPresent("Delivery to non-EU countries may cause additional custom, taxes and fees");
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("Bei Lieferung ins Nicht-EU-Ausland können zusätzlich Zölle, Steuern und Gebühren anfallen");
    }

    /**
     * Testing trusted shops COD fees message.
     *
     * @group trustedShops
     */
    public function testTsCODFeesMessage()
    {
        if (isSUBSHOP) {
            $this->markTestSkipped('This test case is only actual when SubShops are available.');
        }

        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->switchLanguage("English");

        //add product to basket
        $this->clickAndWait("//ul[@id='newItems']/li[4]//button");

        //going to 4th checkout step
        $this->openBasket();
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertTextPresent("Plus 2,- EUR for money transfer that will be collected on site by messenger");

        $this->switchLanguage("Deutsch");
        $this->assertTextPresent("Zzgl. 2,- EUR für die Geldübermittlung, die der Zusteller vor Ort erhebt");

        //choosing other payment method
        $this->switchLanguage("English");
        $this->openBasket();
        $this->switchLanguage("English");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidpayadvance");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertTextNotPresent("Plus 2,- EUR for money transfer that will be collected on site by messenger");
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("Zzgl. 2,- EUR für die Geldübermittlung, die der Zusteller vor Ort erhebt");

        //disabling message
        $this->callShopSC("oxConfig", null, null, array("blShowTSCODMessage" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->switchLanguage("English");

        //add product to basket
        $this->clickAndWait("//ul[@id='newItems']/li[4]//button");

        //going to 4th checkout step
        $this->openBasket();
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertTextNotPresent("Plus 2,- EUR for money transfer that will be collected on site by messenger");
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("Zzgl. 2,- EUR für die Geldübermittlung, die der Zusteller vor Ort erhebt");
    }

    /**
     * Prepare data for testTsExcellence
     */
    protected function _prepareTestTsExcellence()
    {
        $this->callShopSC(
            "oxConfig",
            null,
            null,
            array(
                "iShopID_TrustedShops" => array( "type" => "aarr", "value" => array( $this->getLoginDataByName('trustedShopsIdForLanguageDe') ) ),
                "aTsUser"              => array( "type" => "aarr", "value" => array( $this->getLoginDataByName('userName') ) ),
                "aTsPassword"          => array( "type" => "aarr", "value" => array( $this->getLoginDataByName('password') ) ),
                "tsSealType"           => array( "type" => "aarr", "value" => array( "EXCELLENCE" ) ), // response from validation service
                "tsSealActive"         => array( "type" => "bool", "value" => 'true' ),
                "tsTestMode"           => array( "type" => "bool", "value" => 'true' ),
            ),
            null,
            1
        );

        $aPaymentIds = array(
            'oxidcashondel',
            'oxidcreditcard',
            'oxiddebitnote',
            'oxidpayadvance',
            'oxidinvoice',
            'oxempty',
            'testpayment',
        );

        foreach ($aPaymentIds as $sPaymentId) {
            $this->callShopSC("oxPayment", "save", $sPaymentId, array( 'oxtspaymentid' => 'DIRECT_DEBIT' ), null, 1);
        }
    }

    /**
     * enable testTsRatings
     */
    protected function _enableTsRatings()
    {
        $oServiceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
        $oServiceCaller->setParameter('cl', 'dyn_trusted');
        $oServiceCaller->setParameter('fnc', 'save');
        $oServiceCaller->setParameter('editval', array(
            'oxshops__oxid'=> oxSHOPID
        ));
        $oServiceCaller->setParameter('aTsUser', array('', ''));
        $oServiceCaller->setParameter('aTsPassword', array('', ''));
        $oServiceCaller->setParameter('tsSealActive', true);
        $oServiceCaller->setParameter('aShopID_TrustedShops', array(
            $this->getLoginDataByName('trustedShopsIdForLanguageDe'),
            $this->getLoginDataByName('trustedShopsIdForLanguageEn'),
        ));
        $oServiceCaller->setParameter('paymentids', array(
            "oxidcashondel" => "DIRECT_DEBIT",
            "oxidcreditcard" => "DIRECT_DEBIT",
            "oxiddebitnote" => "DIRECT_DEBIT",
            "oxidpayadvance" => "DIRECT_DEBIT",
            "oxidinvoice" => "DIRECT_DEBIT",
            "oxempty" => "DIRECT_DEBIT",
            "testpayment" => "DIRECT_DEBIT",
        ));
        $_POST = $oServiceCaller->getParameters();
        try {
            $oServiceCaller->callService('ShopObjectConstructor');
        } catch (Exception $oException) {
            $this->fail("Exception caught calling ShopObjectConstructor with message: '{$oException->getMessage()}'");
        }
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
