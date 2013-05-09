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

class AcceptanceEfire_paypalTestBasic extends oxidAdditionalSeleniumFunctions
{
    protected $_sVersion = "EE";

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);

            $this->_sVersion = "CE";
    }


    /**
     * Executed after test is down
     *
     */
    protected function tearDown()
    {
        $this->callUrl(shopURL."/_restoreDB.php", "restoreDb=1");
        parent::tearDown();
    }

    /**
     * Call script file
     *
     * @return void
     */
    public function callUrl($sShopUrl, $sParams = "")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sShopUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $sParams );
        curl_setopt( $ch, CURLOPT_USERAGENT, "OXID-SELENIUMS-CONNECTOR" );
        $sRes = curl_exec($ch);

        curl_close($ch);
    }

    /**
     * Copy files to shop
     *
     * @param string $sCopyDir copy dir
     * @param string $sShopDir shop dir
     *
     * @return void
     */
    public function copyFile($sCopyDir, $sShopDir)
    {
        $sCmd = "cp -frT ".escapeshellarg($sCopyDir)." ".escapeshellarg($sShopDir);
        if (SHOP_REMOTE) {
            $sCmd = "scp -rp ".escapeshellarg($sCopyDir."/.")." ".escapeshellarg(SHOP_REMOTE);
        }
        exec($sCmd, $sOut, $ret);
        $sOut = implode("\n",$sOut);
        if ( $ret > 0 ) {
            throw new Exception( $sOut );
        }
    }
    // ------------------------ eFire modules for eShop ----------------------------------

    /**
     * test for activating PayPal and sending conector
     * @group paypal
     */
    public function testActivatePaypal()
    {
        //copy module files to shop
        $sModuleDir = MODULE_PKG_DIR;
        $sCopyDir = rtrim($sModuleDir, "/") . "/copy_this";
        $this->copyFile( $sCopyDir, oxPATH );
        $sCopyDir = rtrim($sModuleDir, "/") . "/changed_full";
        $this->copyFile( $sCopyDir, oxPATH );
        $this->open(shopURL."_prepareDB.php?version=".$this->_sVersion.'&theme=basic');
        $this->open(shopURL."admin");
        $this->loginAdminForModule("Extensions", "Modules");
        $this->openTab("link=PayPal");
        $this->frame("edit");
        $this->clickAndWait("module_activate");
        $this->downloadConnector("ee_paypal_demo", "pp6677ggR");
        $this->callUrl(shopURL."/_restoreDB.php", "dumpDb=1");
        // $this->waitForText("db Dumptime:");
    }

    /**
     * Perform login to paypal.
     */
    protected function _loginToSandbox()
    {
        $this->open("https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize?client_id=eee5d94d000903b29b1263ae5654b369&response_type=code&scope=openid%20profile%20email%20address%20https://uri.paypal.com/services/paypalattributes&redirect_uri=https://developer.paypal.com/webapps/developer/access&nonce=05d0a60ee1f44361f449496505e05116&state=784d8bc3fe3a48a5105b4f8ddd8ae0e7");
        $this->type("email", "caroline.helbing@oxid-esales.com");
        $this->type("password", "QT0Km5OyJzoiUC" );
        $this->click("name=_eventId_submit");
        $this->waitForPageToLoad("30000");
    }

    // ------------------------ eFire modules for eShop ----------------------------------


    /**
     * testing paypal payment selection
     * @group paypal
     */
    public function testPaypalPayment()
    {
        //login to sanbox
        $this->_loginToSandbox();
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->type("test_UsrOpt2_usr", "birute_test@nfq.lt");
        $this->type("test_UsrOpt2_pwd", "useruser");
        $this->clickAndWait("test_UsrOpt2");
        $this->type("order_remark", "Testing paypal");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->click("//input[@name='paymentid' and @value='oxidpaypal']");
        $this->click("test_PaymentNextStepBottom");
        $this->waitForElement("login.x");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->clickAndWait("login.x");
        //$this->waitForText("Lieferadresse", false, 120);
        $this->clickAndWait("continue");
        //$this->waitForTextDisappear("Lieferadresse auswählen");
        $this->assertEquals("0,99 €", $this->getText("test_orderGrandTotal"));
        $this->assertEquals("PayPal", $this->getText("test_orderPayment"));
        $this->assertEquals("Test S&H set", $this->getText("test_orderShipping"));
        $this->assertEquals("Rechnungsadresse E-Mail: birute_test@nfq.lt SeleniumTestCase Äß'ü Herr Testing user acc Äß'ü PayPal Äß'ü Testing acc for Selenium Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Deutschland", $this->clearString($this->getText("test_orderBillAdress")));
        //$this->check("test_OrderConfirmAGBBottom"); //disabled according TrustedShops
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"));
        //checking if order is saved in Admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->assertEquals("PayPal Äß'ü Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->clickAndWaitFrame("link=1", "edit", "link=Main");
        //$this->openTab("link=1");
        //$this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"));
        $this->assertEquals("PayPal", $this->getSelectedLabel("setPayment"));
    }

    /**
     * testing paypal express button
     * @group paypal
     */
    public function testPaypalExpress()
    {
        //login to sanbox
        $this->_loginToSandbox();
        //express when user logged in
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertTrue($this->isElementPresent("//input[@class='paypalExpressCheckoutSubmitButton']"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isElementPresent("//input[@class='paypalExpressCheckoutSubmitButton']"));
        $this->clickAndWait("submit");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->clickAndWait("login.x");
        $this->waitForText("Zahlungsmethode", false, 120);
        $this->clickAndWait("continue");
        $this->waitForElement("test_orderGrandTotal");
       // $this->waitForTextDisappear("Lieferadresse");
        $this->assertTrue($this->isElementPresent("test_orderGrandTotal"));
        $this->assertEquals("0,01 €", $this->getText("test_orderGrandTotal"));
        $this->assertEquals("Rechnungsadresse E-Mail: birute_test@nfq.lt SeleniumTestCase Äß'ü Mr Testing user acc Äß'ü PayPal Äß'ü Testing acc for Selenium Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Deutschland", $this->clearString($this->getText("test_orderBillAdress")));
        //express when user is not logged in
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("submit");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->clickAndWait("login.x");
        $this->waitForText("Zahlungsmethode", false, 120);
        $this->clickAndWait("continue");
		  $this->waitForElement("test_orderGrandTotal");
       // $this->waitForTextDisappear("Lieferadresse");
        $this->assertTrue($this->isElementPresent("test_orderGrandTotal"),  " Mantis #1224 ");
        $this->assertEquals("0,01 €", $this->getText("test_orderGrandTotal"));
        $this->assertEquals("Rechnungsadresse E-Mail: caroline.helbing@oxid-esales.com OXID eSales AG Produktentwicklung Bertoldstraße 48 79098 Freiburg Deutschland", $this->clearString($this->getText("test_orderBillAdress")));
        //$this->check("test_OrderConfirmAGBBottom"); //disabled according TrustedShops
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"));
        //checking if order is saved in Admin
        $this->loginAdmin("Administer Orders", "Orders", "btn.help", "link=1");
        $this->openTab("link=1", "setfolder");
        $this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"));
        $this->assertTrue($this->isElementPresent("setPayment"));
        $this->assertEquals("PayPal", $this->getSelectedLabel("setPayment"));
        //checking when standard delivery set is deleted at all
        $this->executeSql("DELETE FROM `oxdeliveryset` WHERE `OXID` = 'oxidstandard';");
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("submit");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->clickAndWait("login.x");
        $this->waitForText("Zahlungsmethode", false, 120);
        $this->clickAndWait("continue");
        $this->waitForElement("test_orderGrandTotal");
       // $this->waitForTextDisappear("Lieferadresse");
        //switching possible deliveySets
        $this->assertEquals("PayPal", $this->getText("test_orderPayment"));
        $this->assertEquals("0,99 €", $this->getText("test_orderGrandTotal"));
    }

    /*
 	 * testing if express button is not visible when paypal is not active
    * @group paypal
    */
    public function testPaypalExpressWhenPaypalInactive()
    {
        //login to sanbox
        $this->_loginToSandbox();
    	//disable Paypal
    	$this->executeSql("UPDATE `oxpayments` SET `OXACTIVE` = '0' WHERE `OXID` = 'oxidpaypal';");
    	$this->openShop();
    	$this->clickAndWait("test_Lang_Deutsch");
    	$this->type("f.search.param", "1001");
    	$this->clickAndWait("test_searchGo");
    	$this->clickAndWait("test_toBasket_Search_1001");
    	$this->clickAndWait("test_RightBasketOpen");
    	$this->assertFalse($this->isElementPresent("//input[@class='paypalExpressCheckoutSubmitButton']"));
    	$this->type("test_RightLogin_Email", "birute_test@nfq.lt");
    	$this->type("test_RightLogin_Pwd", "useruser");
    	$this->clickAndWait("test_RightLogin_Login");
    	$this->assertFalse($this->isElementPresent("//input[@class='paypalExpressCheckoutSubmitButton']"));

    }
}
