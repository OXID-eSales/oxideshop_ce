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

class AcceptanceEfire_paypalTest extends oxidAdditionalSeleniumFunctions
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
        $this->open(shopURL."_prepareDB.php?version=".$this->_sVersion);
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

    /**
     * testing paypal payment selection
    * @group paypal
    */
    public function testPaypalPayment1()
    {

        //login to sanbox
        $this->_loginToSandbox();

        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("Deutsch");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("order_remark");
        $this->type("order_remark", "Testing paypal");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->waitForElement("login.x");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        // Sleep for couple seconds as button might be visible but not available.
        sleep(5);
        $this->click("id=continue");
        $this->waitForText("Senden Sie Ihre Bestellung am unteren Ende dieser Übersicht ab");
        $this->assertEquals("Gesamtsumme 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Zahlungsart ändern PayPal", $this->clearString($this->getText("orderPayment")));
        $this->assertEquals("Versandart ändern Test S&H set", $this->clearString($this->getText("orderShipping")));
        $this->assertEquals("Adressen ändern Rechnungsadresse E-Mail: birute_test@nfq.lt SeleniumTestCase Äß'ü Testing acc for Selenium Herr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Deutschland Ihre Mitteilung an uns: Testing paypal", $this->clearString($this->getText("orderAddress")));
        $this->clickAndWait("//button[text()='Zahlungspflichtig bestellen']");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"), "Order is not finished successful");

        //Checking if order is saved in Admin
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");
        $this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"), "Shipping method is not displayed in admin");
        $this->assertEquals("PayPal", $this->getSelectedLabel("setPayment"));

    }


    /**
     * testing paypal express button
    * @group paypal
    */
    public function testPaypalExpress2()
    {

        //login to sanbox
        $this->_loginToSandbox();

        //Testing when user is logged in
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("Deutsch");
        $this->waitForElementPresent("paypalExpressCheckoutButton");
        $this->assertTrue($this->isElementPresent("paypalExpressCheckoutButton"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->waitForElementPresent("paypalExpressCheckoutButton");
        $this->assertTrue($this->isElementPresent("paypalExpressCheckoutButton"), "PayPal express button not displayed in the cart");

        //Go to PayPal express
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->click("id=continue");
        $this->waitForItemAppear("id=continue");
        $this->click("id=continue");

        $this->clickAndWait("continue");
        $this->assertEquals("Gesamtsumme 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Adressen ändern Rechnungsadresse E-Mail: birute_test@nfq.lt SeleniumTestCase Äß'ü Testing acc for Selenium Herr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Deutschland", $this->clearString($this->getText("orderAddress")));

        //Testing when user is not logged in
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("Deutsch");

        $this->waitForElementPresent("paypalExpressCheckoutButton");
        $this->assertTrue($this->isElementPresent("paypalExpressCheckoutButton"), "PayPal express button not displayed in the cart");

        //Go to PayPal express
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");

        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");

        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=shipping_method");
        $this->click("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed");
        $this->assertTrue($this->isTextPresent("€0,99"));
        $this->click("id=continue");
        //sleep(4);

        //User is on the 4th page
        $this->waitForText("Senden Sie Ihre Bestellung am unteren Ende dieser Übersicht ab");
        $this->assertEquals("Gesamtsumme 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Zahlungsart ändern PayPal", $this->clearString($this->getText("orderPayment")));
        $this->assertEquals("Adressen ändern Rechnungsadresse E-Mail: buyger_1346652948_pre@gmail.com gerName gerlastname ESpachstr. 1 79111 Freiburg Deutschland", $this->clearString($this->getText("orderAddress")));
        $this->assertEquals("Versandart ändern Test S&H set", $this->clearString($this->getText("orderShipping")));
        $this->clickAndWait("//button[text()='Zahlungspflichtig bestellen']");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"), "Order is not finished successful");

        //Checking if order is saved in Admin
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->openTab("link=2", "setfolder");
        $this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"));

    }


    /**
     * testing if express button is not visible when paypal is not active
    * @group paypal
    */
    public function testPaypalExpressWhenPaypalInactive()
    {

        //login to sanbox
        $this->_loginToSandbox();

        //Disable Paypal
        $this->loginAdminForModule("Extensions", "Modules");
        $this->openTab("link=PayPal");
        $this->frame("edit");
        $this->clickAndWait("module_deactivate");
        $this->assertTrue($this->isElementPresent("id=module_activate"), "The button Activate module is not displayed ");

        //After PayPal module is deactivated,  paypal express button should  not be availabe in basket
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("Deutsch");
        $this->assertFalse($this->isElementPresent("paypalExpressCheckoutBox"), "Paypal should not be displayed, because Paypal is deactivated");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertFalse($this->isElementPresent("paypalExpressCheckoutBox"), "Paypal should not be displayed, because Paypal is deactivated");
        $this->waitForText("Gesamtsumme 0,99 €");

        //On 2nd step
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->waitForText("Lieferadresse");

        //On 3rd step
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->waitForText("Bitte wählen Sie Ihre Versandart");
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->assertEquals("Kosten: 3,90 €", $this->getText("shipSetCost"));
        $this->assertFalse($this->isElementPresent("//input[@value='oxidpaypal']"));
        $this->selectAndWait("sShipSet", "label=Test S&H set");
        $this->assertTrue($this->isElementPresent("//input[@value='oxidpaypal']"));

        // clearing cache as disabled module is cached
        $this->clearTmp();
    }


    /**
     * testing when payment method has unasign country Germany, user is not login to the shop, and purchase as paypal user from Germany
    * @group paypal
    */
    public function testPaypalPaymentForGermany()
    {

        //login to sanbox
        $this->_loginToSandbox();

        //Make an order with paypal
        $this->openShop();
        $this->switchLanguage("Deutsch");
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("Deutsch");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("order_remark");
        $this->type("order_remark", "Testing paypal");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->click("name=sShipSet");
        $this->select("name=sShipSet", "label=Test S&H set");
        $this->waitForItemAppear("payment_oxidpaypal");
        $this->click("id=payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Weiter zum nächsten Schritt']");
        $this->waitForElement("login.x");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->click("id=continue");
        $this->waitForText("Senden Sie Ihre Bestellung am unteren Ende dieser Übersicht ab");
        $this->clickAndWait("//button[text()='Zahlungspflichtig bestellen']");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"), "The order not finished successful");

        //Go to an admin and check this order nr
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"), "Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");
        $this->assertTextPresent("Internal Status: OK");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Order No.: 2"), "Order number is not displayed in admin");

        //Check user's order information in admin
        $this->assertEquals("1 *", $this->getText("//table[2]/tbody/tr/td[1]"), "Quantity of product is not correct in admin");
        $this->assertEquals("Test product 1", $this->getText("//td[3]"), "Purchased product name is not displayed in admin");
        $this->assertEquals("0,99 EUR", $this->getText("//td[5]"), "Unit price is not displayed in admin");
        $this->assertEquals("0,99", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Products", "//input[@value='Update']");
        $this->assertEquals("1", $this->getValue("//tr[@id='art.1']/td[1]/input"), "Quantity of product is not correct in admin");
        $this->assertEquals("0,99 EUR", $this->getText("//tr[@id='art.1']/td[8]"), "Unit price is not displayed in admin");
        $this->assertEquals("0,99 EUR", $this->getText("//tr[@id='art.1']/td[9]"));

        //Update product quantities to 5
        $this->type("//tr[@id='art.1']/td[1]/input", "5");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("0,99 EUR", $this->getText("//tr[@id='art.1']/td[8]"));
        $this->assertEquals("4,95 EUR", $this->getText("//tr[@id='art.1']/td[9]"), "Total price is incorrect after update");
        $this->assertEquals("4,95", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"), "Shipping method is not displayed in admin");
        $this->assertEquals("PayPal", $this->getSelectedLabel("setPayment"));

        //Unassign Germany from Paypal payment method and assign United States
        $this->open(shopURL."/_updateDB.php?filename=unasignCountryFromPayPal.sql");

        ///Go to make an order but do not finish it
        $this->openShop();

        //Check if paypal logo in frontend is active in both languages
        $this->assertTrue($this->isElementPresent("paypalPartnerLogo"), "Paypal logo not shown in frontend page");
        $this->switchLanguage("Deutsch");
        $this->assertTrue($this->isElementPresent("paypalPartnerLogo"), "Paypal logo not shown in frontend page");
        $this->switchLanguage("English");

        //Search for the product and add to cart
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");
        $this->waitForElementPresent("paypalExpressCheckoutButton");
        $this->assertTrue($this->isElementPresent("link=Test product 1"),"Product:Test product 1 is not shown in 1st order step ");
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']/td[3]/div[2]"),"There product:Test product 1 is not shown in 1st order step" );
        $this->assertEquals("OXID Surf and Kite Shop | Cart | purchase online", $this->getTitle(), "Tittle of the page is incorrect");
        $this->assertEquals("Grand Total: 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")),"Grand total is not displayed correctly");
        $this->assertFalse($this->isTextPresent("Shipping cost"), "Shipping cost should not be displayed");
        $this->assertTrue($this->isTextPresent("exact:?"));
        $this->storeChecked("//input[@name='displayCartInPayPal' and @value='1']");
        $this->assertTrue($this->isTextPresent("Display cart in PayPal"),"An option text:Display cart in PayPal is not displayed");
        $this->assertTrue($this->isElementPresent("name=displayCartInPayPal"),"An option Display cart in PayPal is not displayed");

        //Go to paypal express to make an order
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");

        //Login to Paypal as US user
        $this->type("login_email", "buyus_1346652862_per@gmail.com");
        $this->type("login_password", "xxxxxxxxx");

        //After login to PayPal check does all necessary element displayed correctly
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isElementPresent("id=showname0"),"Purchased product is not displayed in basket in PayPal");
        $this->assertFalse($this->isTextPresent("Shipping method: Stadard Price:€6.90 EUR"), "Stadard Price:€6.90 EUR shipping cost for this user should not be dispyed in PayPal");
        $this->assertTrue($this->isTextPresent("buyus_1346652862_per@gmail.com"), "User login name is not displayed in PayPal ");
        $this->assertTrue($this->isElementPresent("id=showname0"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Item number: 1001"), "Product number is not displayed in PayPal ");
        $this->assertTrue($this->isTextPresent("Quantity: 1"), "Product quantities is not displayed in PayPal");
        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");

        //Go to shop
        $this->waitForTextPresent("Total €7.89 EUR");
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");

        //Now user is on the 1st "cart" step with an error message:
        $this->assertTrue($this->isTextPresent("Based on your choice in PayPal Express Checkout, order total has changed. Please check your shopping cart and continue. Hint: for continuing with Express Checkout press Express Checkout button again."), "An error message is not dispayed in shop 1st order step");
        $this->assertTrue($this->isElementPresent("id=basketRemoveAll"), "an option Remove is not displayed in 1st cart step");
        $this->assertTrue($this->isElementPresent("id=basketRemove"), "an option All is not displayed in 1st cart step");
        $this->assertTrue($this->isElementPresent("id=basketUpdate"), "an option Update is not displayed in 1st cart step");
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Purchased product name is not displayed");
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']/td[3]/div[2]"),"There product:Test product 1 is not shown in 1st order step" );
        $this->assertEquals("OXID Surf and Kite Shop | Cart | purchase online", $this->getTitle()," Title in 1st order step is incorrect");
        $this->assertEquals("Grand Total: 7,73 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "Grand total is not displayed correctly");
        $this->assertEquals("Shipping cost 6,90 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");

        $this->assertTrue($this->isTextPresent("Display cart in PayPal"),"Text:Display cart in PayPal for checkbox not displayed");
        $this->assertTrue($this->isElementPresent("name=displayCartInPayPal"), "Checkbox:Display cart in PayPal not displayed in cart" );
        $this->assertTrue($this->isElementPresent("paypalExpressCheckoutButton"),"PayPal express button not displayed in the cart");

        //Go to next step and change country to Germany
        $this->click("xpath=(//button[@type='submit'])[3]");
        $this->waitForItemAppear("id=userChangeAddress");
          $this->click("id=userChangeAddress");
        $this->click("id=invCountrySelect");
        $this->select("id=invCountrySelect", "label=Germany");
        $this->click("id=userNextStepTop");
        $this->waitForPageToLoad("30000");

        //Check if PayPal is not displayed for Germany
        $this->assertEquals("Test S&H set Standard Example Set1: UPS 48 hours Example Set2: UPS Express 24 hours", $this->getText("name=sShipSet"), "Not all shipping methods are available in dropdown");
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("//form[@id='payment']/dl[5]/dt/label/b"), "Wrong payment methode is shown");
        $this->assertTrue($this->isTextPresent("COD (Cash on Delivery)"), "Wrong payment methode is shown");
        $this->assertFalse($this->isTextPresent("PayPal (0,00 €)"), "PayPal should not be displayed as paymenth method");

        //Also check if PayPal not displayed in the 1st cart step
        $this->click("link=1. Cart");
           $this->waitForPageToLoad("30000");
        $this->assertFalse($this->isTextPresent("Display cart in PayPal"), "Text:Display cart in PayPal for checkbox not displayed");
        $this->assertFalse($this->isElementPresent("name=displayCartInPayPal"), "Checkbox:Display cart in PayPal not displayed in cart" );
        $this->assertFalse($this->isElementPresent("paypalExpressCheckoutButton"), "PayPal express button not displayed in the cart");

        ///Go to admin and check previous porder status and check if new order didn't appear in admin and it didn't overwritten on previous order.
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"),"Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");

        $this->assertTextPresent("Internal Status: NOT_FINISHED");
        $this->assertTrue($this->isTextPresent("Order No.: 2"), "Order number is not displayed in admin");

        //Check user's order nr 2 information in admin
        $this->assertEquals("5 *", $this->getText("//table[2]/tbody/tr/td[1]"), "Product quantities are incorect in admin");
        $this->assertEquals("Test product 1", $this->getText("//td[3]"), "Product name is incorect in admin");
        $this->assertEquals("4,95 EUR", $this->getText("//td[5]"));
        $this->assertEquals("4,95", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"), "Product total displayed ");
        $this->frame("list");
        $this->openTab("link=Products", "//input[@value='Update']");
        $this->assertEquals("5", $this->getValue("//tr[@id='art.1']/td[1]/input"), "Product quantities are incorect in admin");
        $this->assertEquals("0,99 EUR", $this->getText("//tr[@id='art.1']/td[8]"), "Product price is incorect in admin");
        $this->assertEquals("4,95 EUR", $this->getText("//tr[@id='art.1']/td[9]"), "Product total is incorect in admin");
        $this->assertEquals("4,95", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"), "Product total is incorect in admin");
        $this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"),"Shipping method is incorect in admin");

        //Go to basket and make an order,
        //TODO there is a bug #4501: after updating quantities in admin in table saves this info and now then user goes to
        //cart there are left 5 quantities instead of 1, then this bug will be fixed need to change selenium
        $this->open(shopURL."_cc.php");
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");

        $this->assertEquals("Grand Total: 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")),"Grand total is not displayed correctly");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isElementPresent("id=showShipAddress"), "Shipping adress is not displayed in 2nd order step");
        $this->click("id=userNextStepBottom");
        $this->waitForElementPresent("paymentNextStepBottom");
        $this->assertTrue($this->isElementPresent("name=sShipSet"),"Shipping methode dropdown is not shown");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("sShipSet"), "Wrong shipping methode is selected, should be:Test S&H set ");
        $this->click("id=paymentNextStepBottom");

        //go to last order step, check if payment method is not PayPal
        $this->waitForElementPresent("orderAddress");
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Product name is not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Art.No.: 1001"),"Product number not displayed in last order step");
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("OXID Surf and Kite Shop | Order | purchase online", $this->getTitle(), "Page tittle is incorect in last order step");
        $this->assertEquals("Surcharge Type of Payment: 7,50 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "Payment price is not displayed in carts");
        $this->assertEquals("Grand Total: 12,45 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");
        $this->assertTrue($this->isTextPresent("Test S&H set"));
       // $this->assertFalse($this->isTextPresent("PayPal"));
        $this->assertTrue($this->isTextPresent("COD"));
        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");

        // After sucsessful purchase, go to admin and check order status
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.2']/td[4]"), "Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.2']/td[5]"), "Wrong user last name is displayed in order");
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->openTab("link=3", "setfolder");
        $this->assertTextPresent("Internal Status: OK");
        $this->assertTextPresent("Order No.: 3", "Order number is not displayed in admin");
        $this->assertEquals("5 *", $this->getText("//table[2]/tbody/tr/td[1]"));
        $this->assertEquals("Test product 1", $this->getText("//td[3]"), "Purchased product name is not displayed in Admin");
        $this->assertEquals("12,45", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Products", "//input[@value='Update']");
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"), "charges of payment methode is not displayeds");
        $this->assertEquals("0,79", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"), "VAT is not displayed");
        $this->assertEquals("4,16", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"),"Product Net price is not displayeds");
        $this->frame("list");
        $this->openTab("link=Main", "setDelSet");
        $this->assertEquals("Test S&H set", $this->getSelectedLabel("setDelSet"), "Shipping method is not displayed in admin");
        $this->assertEquals("COD (Cash on Delivery)", $this->getSelectedLabel("setPayment"), "Paymenth method is not displayed in admin");

    }


    /**
     * testing different countries with shipping rules assigned to this countries 
    * @group paypal
    */
    public function testPaypalPaymentForLoginUser()
    {

        //login to sanbox
        $this->_loginToSandbox();

        $this->openShop();

        //Search for the product and add to cart
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");

        //Login to shop and go to the basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->waitForElementPresent("paypalExpressCheckoutButton", "PayPal express button not displayed in the cart");
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Purchased product name is not displayed");
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("OXID Surf and Kite Shop | Cart | purchase online", $this->getTitle());
        $this->assertEquals("Grand Total: 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->assertTrue($this->isTextPresent("Shipping cost"), "Shipping cost is not displayed correctly");
        $this->assertTrue($this->isTextPresent("exact:?"));
        $this->storeChecked("//input[@name='displayCartInPayPal' and @value='1']");
        $this->assertTrue($this->isTextPresent("Display cart in PayPal"),"Text:Display cart in PayPal for checkbox not displayed");
        $this->assertTrue($this->isElementPresent("name=displayCartInPayPal"),"Checkbox:Display cart in PayPal not displayed");

        //Go to Paypal via Paypal Express with "Display cart in PayPal"
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Item number: 1001"), "Product number not displayed in paypal ");
        $this->assertFalse($this->isTextPresent("Grand Total: €0,99"),"Grand total should not be displayed");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Warenwert€0,99"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("exact:Versandkosten:"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isElementPresent("id=showname0"),"Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("Versandmethode: Test S&H set: €0,00 EUR"), "Shipping method is not shown in Paypal");
       // $this->assertEquals("Testing user acc Äß&amp;#039;ü PayPal Äß&amp;#039;ü Musterstr. Äß&#039;ü 1 79098 Musterstadt Äß&#039;ü Deutschland Versandmethode: Test S&H set: €0,00 EUR", $this->clearString($this->getText("//div[@class='inset confidential']")));
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1001"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isTextPresent("Gesamtbetrag €0,99 EUR"), "Total price is not displayed in PayPal");

        //Cancel order and go back to the shop with uncecked option
        $this->click("name=cancel_return");
        $this->waitForElementPresent("paypalExpressCheckoutButton");
        $this->uncheck("//input[@name='displayCartInPayPal']");

        //Go to Paypal via Paypal Express without  "Display cart in PayPal"
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertFalse($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertfalse($this->isTextPresent("Item number: 1001"),"Item number should not be displayed in PayPal");
        $this->assertFalse($this->isTextPresent("Grand Total: €0,99"),"Grand total should not be displayed in PayPal");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");
        $this->assertFalse($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Warenwert€0,99"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("exact:Versandkosten:"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("Versandmethode: Test S&H set: €0,00 EUR"));
       // $this->assertEquals("Testing user acc Äß&amp;#039;ü PayPal Äß&amp;#039;ü Musterstr. Äß&#039;ü 1 79098 Musterstadt Äß&#039;ü Deutschland Versandmethode: Test S&H set: €0,00 EUR", $this->clearString($this->getText("//div[@class='inset confidential']")));
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
       //$this->assertTrue($this->isTextPresent("Artikelnummer: 1001"));
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isTextPresent("Gesamtbetrag €0,99 EUR"), "Total price is not displayed in PayPal");

        //Change delivery adress with country which has not paypal asigned as paymentmethod inside Paypal
        
        $this->click("id=changeAddressButton");
        $this->waitForItemAppear("id=addShipAddress");

        //checking if there is already Belgium address
        if (!$this->isTextPresent("Test address in Belgium 15, Antwerp, Belgien", "")) {
            // adding new address (Belgium) to address list
            $this->clickAndWait("id=addShipAddress");
            $this->select("id=country_code", "label=Belgien");
            $this->type("id=shipping_address1", "Test address in Belgium 15");
            $this->type("id=shipping_city", "Antwerp");
            //returning to address list
            $this->click("//input[@id='continueBabySlider']");
        }          
        // selecting Belgium address           
        $this->click("//input[@id='3']");
        $this->click("//input[@id='continueBabySlider']");
        
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=messageBox");
        $this->waitForTextPresent("Gesamtbetrag €0,99 EUR");
        $this->waitForTextPresent("PayPal Testshop versendet nicht an diesen Ort. Verwenden Sie eine andere Adresse.");

        //Cancel paying with paypal and back to the shop
        $this->click("name=cancel_return");
        $this->waitForElementPresent("paypalExpressCheckoutButton");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Check exist user adress
        $this->assertEquals("E-mail: birute_test@nfq.lt SeleniumTestCase Äß'ü Testing acc for Selenium Mr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Germany", $this->clearString($this->getText("//ul[@id='addressText']//li")), "User address is incorect");

        //Change to new one which has not paypal asigned as paymentmethod inside Paypal
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invCountrySelect");

        $this->select("invCountrySelect", "label=United States");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("link=1. Cart");
        $this->assertFalse($this->isElementPresent("paypalPartnerLogo"), "PayPal logo should not be displayed fot US");

        //Created additiona 3 shipping methodes with shipping cost rules for Austria
            $this->open(shopURL."/_updateDB.php?filename=newDeliveryMethod_pe.sql");

        $this->openBasket("English");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Change country to Austria
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invCountrySelect");
        $this->select("invCountrySelect", "label=Austria");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Check all available shipping methodes
        $this->assertTrue($this->isTextPresent("PayPal"));
        $this->assertTrue($this->isTextPresent("exact:Charges: 0,50 €"));
        $this->assertTrue($this->isTextPresent("Test Paypal:6 hour Test Paypal:12 hour Standard Example Set1: UPS 48 hours Example Set2: UPS Express 24 hours"), "Not all available shipping methods is displayed");

        //Go to 1st step and make an order via Paypal expresss
        $this->clickAndWait("link=1. Cart");
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Item number: 1001"), "Product number not displayed in the 1st order step ");

        //Login to paypal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");
        $this->assertTrue($this->isTextPresent("Warenwert€0,99"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("Versandkosten:€0,50"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");
        $this->waitForTextPresent("Gesamtbetrag €1,49 EUR");
        $this->select("id=shipping_method", "label=Test Paypal:12 hour Price: €0,90 EUR");
        $this->waitForTextPresent("Gesamtbetrag €1,89 EUR");
        $this->assertTrue($this->isTextPresent("Warenwert€0,99"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("Versandkosten:€0,90"),"Shippinh cost is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Gesamtbetrag €1,89 EUR"), "Total price is not displayed in PayPal");

        //Go to shop
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");

        //Check are all info in the last order step correct
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Purchased product name is not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Art.No.: 1001"),"Product number not displayed in last order step");
        $this->assertEquals("Shipping cost 0,90 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("OXID Surf and Kite Shop | Order | purchase online", $this->getTitle());
        $this->assertEquals("Grand Total: 1,89 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->assertTrue($this->isTextPresent("Test Paypal:12 hour"),"Shipping method not displayed in order ");
        $this->assertTrue($this->isTextPresent("PayPal"),"Payment method not displayed in last order step");
        $this->assertFalse($this->isTextPresent("COD"),"Wrong payment method displayed in last order step");

        //Go back to 1st order step and change product quantities to 20
        $this->clickAndWait("link=1. Cart");

        $this->assertEquals("Total Products (gross): 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")), "Total price not displayed in basket");
        $this->assertEquals("Total Products (net): 0,83 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")), "Total price not displayed in basket");
        $this->assertEquals("Grand Total: 1,89 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->type("id=am_1", "20");
        $this->click("id=basketUpdate");
        sleep(3);
        $this->assertEquals("Total Products (gross): 19,80 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")), "Total price not displayed in basket");
        $this->assertEquals("Total Products (net): 16,64 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")), "Total price not displayed in basket");
        $this->assertEquals("Grand Total: 20,60 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");

        //Go to Paypal to make an order
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Item number: 1001"), "Product number not displayed in the PayPal");

        //Login to paypal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");
        $this->assertTrue($this->isTextPresent("Warenwert€19,80"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("Versandkosten:€0,80"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isElementPresent("id=showname0"), "Product name is not shown in PayPal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 20"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");
        $this->waitForTextPresent("Gesamtbetrag €19,80 EUR");
        $this->waitForItemAppear("id=shipping_method");
        $this->select("id=shipping_method", "label=Test Paypal:6 hour Price: €0,40 EUR");
        $this->waitForTextPresent("Gesamtbetrag €20,20 EUR");
        $this->assertTrue($this->isTextPresent("Warenwert€19,80"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("Versandkosten:€0,40"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Gesamtbetrag €20,20 EUR"), "Total price is not displayed in PayPal");

        //Go to shop
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");

        //Check are all info in the last order step correct
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Purchased product name is not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Art.No.: 1001"),"Product number not displayed in last order step");
        $this->assertEquals("Shipping cost 0,40 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("OXID Surf and Kite Shop | Order | purchase online", $this->getTitle());
        $this->assertEquals("Grand Total: 20,20 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->assertTrue($this->isTextPresent("Test Paypal:6 hour"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isTextPresent("PayPal"),"Payment method not displayed in last order step");
        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");

    }


    /**
     * testing ability to change country in standart paypal
    * @group paypal
    */
    public function testPaypalStandart()
    {

        //login to sanbox
        $this->_loginToSandbox();

        //Login to shop and go standart paypal
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Germany"),"Users coutry should be Germany");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isElementPresent("//input[@value='oxidpaypal']"));
        $this->click("payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Login to standart paypal and check ability to change country
        $this->waitForElement("login.x");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->assertFalse($this->isElementPresent("id=changeAddressButton"), "In standart PayPal there should be not posibility to change adress");
        $this->click("id=continue");
        $this->assertEquals("Ihre Zahlungsinformationen auf einen Blick - PayPal", $this->getTitle());
        $this->assertTrue($this->isTextPresent("PayPal"),"Payment method not displayed in last order step");
        sleep(3);
        $this->clickAndWait("//button[text()='Purchase']");
        sleep(3);
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop"), "Order is not finished successful");

    }


    /*
    * test if payment method PAYPAL is deactivated in shop backend, the paypal express button should also disappear.
    * @group paypal
    */
    public function testPaypalActive()
    {

        $this->loginAdminForModule("Shop Settings", "Payment Methods");
        $this->clickAndWait("link=PayPal");
        $this->frame("edit");
        $this->uncheck("//input[@name='editval[oxpayments__oxactive]']");
        $this->clickAndWait("save");

        //Go to shop to check is paypal not visible in fronend
        $this->openShop();
        $this->assertFalse($this->isElementPresent("paypalPartnerLogo"), "Paypal logo not shown in frontend page");
        $this->switchLanguage("Deutsch");
        $this->assertFalse($this->isElementPresent("paypalPartnerLogo"), "Paypal logo not shown in frontend page");
        $this->switchLanguage("English");

        //Go to basket and check is express paypal not visible
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");
        $this->assertFalse($this->isElementPresent("paypalExpressCheckoutButton"), "Paypal express button should be not visible in frontend");

        //Login to shop and go to the basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertFalse($this->isElementPresent("paypalExpressCheckoutButton"), "Paypal express button should be not visible in frontend");

    }


    /*
    * test if discounts working correct with PayPal.
    * @group paypal
    */
    public function testPaypalDiscountsCategory()
    {

        //login to sanbox
        $this->_loginToSandbox();

        // Add vouchers to shop
            $this->open(shopURL."/_updateDB.php?filename=newDiscounts_pe.sql");

        //Go to shop and add product
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1000");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");

        //Login to shop and go to basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isTextPresent("Test product 0"));
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed");
        $this->assertTrue($this->isTextPresent("+1"));
//        $this->assertEquals("5,00 €", $this->getText("basketGrandTotal"),"Garnd total price chenged or did't displayed");
//        $this->assertEquals("5,00 € \n10,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"),"price with discount not shown in basket");

        $this->assertEquals("Discount  discount for category", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/th"), "Discount is not displayed in basket");
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/td"));
        $this->assertEquals("Grand Total: 5,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");


        // Go to 2nd step
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to 3rd step and select paypla as payment method
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->waitForItemAppear("id=payment_oxidpaypal");
        $this->click("id=payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to PayPal
        $this->waitForItemAppear("id=submitLogin");
        $this->assertEquals("Pay with a PayPal account - PayPal", $this->getTitle());
        $this->assertTrue($this->isTextPresent("€5,00"));
        $this->assertTrue($this->isTextPresent("€0,00"));
        $this->assertEquals("Total €5,00 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"));
        $this->assertTrue($this->isTextPresent("Total €5,00 EUR"));

        //Login to PayPal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1000"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €10,00"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1001"), "Product number not shown in Paypal");
        $this->assertEquals("Artikelpreis: €0,00", $this->getText("//li[@id='multiitem1']/ul[2]/li[3]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[2]/li[4]"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("€5,00"));
        $this->assertEquals("Gesamtbetrag €5,00 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"), "Total price is not displayed in PayPal");
        $this->click("id=continue_abovefold");

        //Go to shop to finish the order
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");
        $this->assertTrue($this->isTextPresent("Test product 0"), "Purchased product name is not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in last order step");
        $this->assertEquals("Art.No.: 1001", $this->getText("//tr[@id='cartItem_2']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertEquals("Art.No.: 1000", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertTrue($this->isTextPresent("1 +1"));
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/td"));

        $this->assertEquals("Total Products (gross): 10,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("Discount discount for category -5,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (net): 4,20 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("plus VAT 19% Amount: 0,80 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Grand Total: 5,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");
        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop"), "Order is not finished successful");

        //Go to admin and check the order
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"), "Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("10,00 EUR", $this->getText("//td[5]"));
        $this->assertEquals("Billing Address: Company SeleniumTestCase Äß'ü Testing acc for Selenium Mr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Germany E-mail: birute_test@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 5,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("4,20", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("0,80", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("PayPal", $this->getText("//table[4]/tbody/tr[1]/td[2]"),"Payment method not displayed in admin");
        $this->assertEquals("Test S&H set", $this->getText("//table[4]/tbody/tr[2]/td[2]"), "Shipping method is not displayed in admin");

    }


    /*
    * test if few different discounts working correct with PayPal.
    * @group paypal
    */
    public function testPaypalDiscountsFromTill()
    {

        //login to sanbox
        $this->_loginToSandbox();

        // Add vouchers to shop
            $this->open(shopURL."/_updateDB.php?filename=newDiscounts_pe.sql");

        //Go to shop and add product
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1004");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");

        //Login to shop and go to basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isTextPresent("Test product 4"));

        $this->assertEquals("Discount  discount from 10 till 20", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/th"));
        $this->assertEquals("-0,30 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/td"));
        $this->assertEquals("Grand Total: 14,70 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");

        // Go to 2nd step
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to 3rd step and select paypla as payment method
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->waitForItemAppear("id=payment_oxidpaypal");
        $this->click("id=payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to PayPal
        $this->waitForItemAppear("id=submitLogin");
        $this->assertEquals("Pay with a PayPal account - PayPal", $this->getTitle());
        $this->assertTrue($this->isTextPresent("€15,00"));
        $this->assertTrue($this->isTextPresent("€0,00"));
        $this->assertEquals("-€0,30", $this->getText("//div[@id='miniCart']/div[2]/ul/li[2]/span"));
        $this->assertEquals("Total €14,70 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"));

        //Login to PayPal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");

        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1004", "//li[@id='multiitem1']/ul[1]"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €15,00", "//li[@id='multiitem1']/ul[1]"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1", "//li[@id='multiitem1']/ul[1]"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1001", "//li[@id='multiitem1']/ul[2]"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,00", "//li[@id='multiitem1']/ul[2]"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1", "//li[@id='multiitem1']/ul[2]"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Warenwert€15,00"));
        $this->assertTrue($this->isTextPresent("Versandrabatt -€0,30", "//div[@id='miniCart']"));
        $this->assertTrue($this->isTextPresent("Gesamtbetrag €14,70 EUR", "//div[@id='miniCart']"));
        $this->click("id=continue_abovefold");

        //Go to last step to check the order
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");
        $this->assertTrue($this->isTextPresent("Test product 4"), "Purchased product name is not displayed");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed");
        $this->assertEquals("Art.No.: 1004", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertEquals("Art.No.: 1001", $this->getText("//tr[@id='cartItem_2']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertTrue($this->isTextPresent("1 +1"));
        $this->assertEquals("-0,30 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/td"));

        $this->assertEquals("Total Products (gross): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("Discount discount from 10 till 20 -0,30 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (net): 12,35 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("plus VAT 19% Amount: 2,35 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Grand Total: 14,70 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");

        //Go back to 1st order step and change product quantities to 3
        $this->clickAndWait("link=1. Cart");
        $this->type("id=am_1", "3");
        $this->click("id=basketUpdate");
        sleep(5);
        $this->assertEquals("Grand Total: 42,75 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");
        $this->assertEquals("Discount  discount from 20 till 50", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/th"));
        $this->assertEquals("-2,25 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/td"));
        // Go to 2nd step
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to 3rd step and select paypla as payment method
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->waitForItemAppear("id=payment_oxidpaypal");
        $this->click("id=payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to PayPal
        $this->waitForItemAppear("id=submitLogin");
        $this->assertEquals("Pay with a PayPal account - PayPal", $this->getTitle());
        $this->assertTrue($this->isTextPresent("Test product 4€45,00", "//div[@id='miniCart']"));
        $this->assertTrue($this->isTextPresent("Test product 1€0,00", "//div[@id='miniCart']"));
        $this->assertTrue($this->isTextPresent("Item total €45,00", "//div[@id='miniCart']"));
        $this->assertTrue($this->isTextPresent("Shipping discount -€2,25", "//div[@id='miniCart']"));

        //Login to PayPal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));

        $this->assertTrue($this->isTextPresent("Test product 4€45,00", "//li[@id='multiitem1']/ul[1]"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1004", "//li[@id='multiitem1']/ul[1]"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €15,00", "//li[@id='multiitem1']/ul[1]"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 3", "//li[@id='multiitem1']/ul[1]"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Test product 1€0,00", "//li[@id='multiitem1']/ul[2]"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1001", "//li[@id='multiitem1']/ul[2]"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,00", "//li[@id='multiitem1']/ul[2]"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1", "//li[@id='multiitem1']/ul[2]"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Versandrabatt -€2,25", "//div[@id='miniCart']"));
        $this->assertTrue($this->isTextPresent("Gesamtbetrag €42,75 EUR", "//div[@id='miniCart']"));
        $this->click("id=continue_abovefold");

        //Go to shop to finish the order
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");
        $this->assertTrue($this->isTextPresent("Test product 4"), "Purchased product name is not displayed");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed");
        $this->assertEquals("Art.No.: 1004", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertEquals("Art.No.: 1001", $this->getText("//tr[@id='cartItem_2']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertTrue($this->isTextPresent("1 +1"));
        $this->assertEquals("-2,25 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[2]/td"));

        $this->assertEquals("Total Products (gross): 45,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("Discount discount from 20 till 50 -2,25 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (net): 35,92 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("plus VAT 19% Amount: 6,83 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Grand Total: 42,75 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");
        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");

        //Go to admin and check the order
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"), "Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("0,00 EUR", $this->getText("//td[5]"));

        $this->assertEquals("Billing Address: Company SeleniumTestCase Äß'ü Testing acc for Selenium Mr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Germany E-mail: birute_test@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("45,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 2,25", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("35,92", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("6,83", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("42,75", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("PayPal", $this->getText("//table[4]/tbody/tr[1]/td[2]"),"Payment method not displayed in admin");
        $this->assertEquals("Test S&H set", $this->getText("//table[4]/tbody/tr[2]/td[2]"), "Shipping method is not displayed in admin");

    }


    /*
    * test if vouchers working correct with PayPal
    * @group paypal
    */
    public function testPaypalVouchers()
    {

        //login to sanbox
        $this->_loginToSandbox();

        // Add vouchers to shop
            $this->open(shopURL."/_updateDB.php?filename=newVouchers_pe.sql");

        //Go to shop and add product
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1003");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");

        //Login to shop and go to basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Grand Total: 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->type("name=voucherNr", "111111");
        $this->clickAndWait("//button[text()='Submit Coupon']");
        $this->assertTrue($this->isTextPresent("remove"));
        $this->assertTrue($this->isTextPresent("Coupon (No. 111111)"));
        $this->assertEquals("Coupon (No. 111111) remove -10,00 €", $this->getText("//div[@id='basketSummary']//tr[4]"));
        $this->assertEquals("Grand Total: 5,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");

        // Go to 2nd step
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to 3rd step and select paypla as payment method
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->waitForItemAppear("id=payment_oxidpaypal");
        $this->click("id=payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to PayPal
        $this->waitForItemAppear("id=submitLogin");
        $this->assertEquals("Pay with a PayPal account - PayPal", $this->getTitle());
        $this->assertTrue($this->isTextPresent("€15,00"));
        $this->assertEquals("-€10,00", $this->getText("//div[@id='miniCart']/div[2]/ul/li[2]/span"));
        $this->assertEquals("Total €5,00 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"));

        //Login to PayPal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1003"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €15,00"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1"), "Product quantity is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("€10,00"));
        //$this->assertEquals("-€10,00", $this->getText("//div[@id='miniCart']/div[2]/ul/li[2]/span"));
        $this->assertTrue($this->isTextPresent("-€10,00", "//div[@id='miniCart']"));
        $this->assertEquals("Gesamtbetrag €5,00 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"), "Total price is not displayed in PayPal");
        $this->click("id=continue_abovefold");

        //Go to shop to finish the order
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");

        $this->assertEquals("Total Products (gross): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("Total Products (net): 4,20 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("plus VAT 19% Amount: 0,80 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Grand Total: 5,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");
        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");

        //Go to admin and check the order
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"), "Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("15,00 EUR", $this->getText("//td[5]"));
        $this->assertEquals("Billing Address: Company SeleniumTestCase Äß'ü Testing acc for Selenium Mr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Germany E-mail: birute_test@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("15,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("4,20", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("0,80", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("5,00", $this->getText("//table[@id='order.info']/tbody/tr[8]/td[2]"));

        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("PayPal", $this->getText("//table[4]/tbody/tr[1]/td[2]"),"Payment method not displayed in admin");
        $this->assertEquals("Test S&H set", $this->getText("//table[4]/tbody/tr[2]/td[2]"), "Shipping method is not displayed in admin");

    }


    /*
    * test if VAT is calculated in PayPal corect with different VAT options setted in admins
    * @group paypal
    */
    public function testPaypaVAT()
    {

        //login to sanbox
        $this->_loginToSandbox();

        // Change price for PayPal payment methode
        $this->open(shopURL."/_updateDB.php?filename=vatOptions.sql");

        // Go to admin and set on all VAT options
        $this->loginAdminForModule("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=VAT");
        sleep(1);
        $this->check("//input[@name='confbools[blEnterNetPrice]'and @value='true']");
        $this->check("//input[@name='confbools[blShowVATForDelivery]'and @value='true']");
        $this->check("//input[@name='confbools[blDeliveryVatOnTop]'and @value='true']");
        $this->check("//input[@name='confbools[blShowVATForPayCharge]'and @value='true']");
        $this->check("//input[@name='confbools[blPaymentVatOnTop]'and @value='true']");
        $this->check("//input[@name='confbools[blWrappingVatOnTop]'and @value='true']");
        $this->clickAndWait("save");

        //Go to shop and add product
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1003");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");

        //Login to shop and go to basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Test product 3", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));

        //Added wrapping and card to basket
        $this->click("id=header");
        $this->click("link=add");
        $this->click("id=wrapping_a6840cc0ec80b3991.74884864");
        $this->click("id=chosen_81b40cf0cd383d3a9.70988998");
        $this->clickAndWait("//button[text()='Apply']");

        $this->assertEquals("Total Products (net): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("plus VAT 19% Amount: 2,85 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (gross): 17,85 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("Shipping (net): 13,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("plus VAT 19% Amount: 2,47 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Gift Wrapping/Greeting Card 7,08 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")));
        $this->assertEquals("Grand Total: 40,40 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[7]")),"Grand total is not displayed correctly");

        // Go to 2nd step
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to 3rd step and select paypla as payment method
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->waitForItemAppear("id=payment_oxidpaypal");
        $this->click("id=payment_oxidpaypal");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //Go to PayPal
        $this->waitForItemAppear("id=submitLogin");
        $this->assertEquals("Pay with a PayPal account - PayPal", $this->getTitle());
        $this->assertTrue($this->isTextPresent("€17,85"));
        $this->assertTrue($this->isTextPresent("€12,50"));
        $this->assertTrue($this->isTextPresent("€7,08"));
        $this->assertEquals("Total €52,90 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"));
        $this->assertTrue($this->isTextPresent("Item total €37,43"));
        $this->assertTrue($this->isTextPresent("Shipping and handling:"));
        $this->assertTrue($this->isTextPresent("€15,47"));

        //Login to PayPal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");

        $this->assertTrue($this->isTextPresent("Artikelnummer: 1003"), "Product number not shown in Paypal");
        $this->assertEquals("Artikelpreis: €17,85", $this->getText("//li[@id='multiitem1']/ul/li[3]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul/li[4]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Surcharge Type of Payment"));
        $this->assertEquals("Artikelpreis: €12,50", $this->getText("//li[@id='multiitem1']/ul[2]/li[2]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[2]/li[3]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Gift Wrapping/Greeting Card"));
        $this->assertEquals("Artikelpreis: €7,08", $this->getText("//li[@id='multiitem1']/ul[3]/li[2]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[3]/li[3]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Warenwert€37,43"), "Product price is not displayed in Paypal");
        $this->assertEquals("Gesamtbetrag €52,90 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"), "Total price is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));
        $this->click("id=continue_abovefold");

        //Go to shop to finish the order
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Greeting Card"));
        $this->assertEquals("3,57 €", $this->getText("id=orderCardTotalPrice"));
        $this->assertEquals("7,08 €", $this->getText("//div[@id='basketSummary']/table/tbody/tr[8]/td"));

        $this->assertEquals("Total Products (net): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("plus VAT 19% Amount: 2,85 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (gross): 17,85 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("Shipping (net): 13,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("plus VAT 19% Amount: 2,47 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Surcharge Type of Payment: 10,50 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")));
        $this->assertEquals("Surcharge VAT 19 % Amount: 2,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[7]")));
        $this->assertEquals("Gift Wrapping/Greeting Card 7,08 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[8]")));
        $this->assertEquals("Grand Total: 52,90 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[9]")),"Grand total is not displayed correctly");

        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");

        //Go to admin and check the order
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("Testing user acc Äß'ü", $this->getText("//tr[@id='row.1']/td[4]"), "Wrong user name is displayed in order");
        $this->assertEquals("PayPal Äß'ü", $this->getText("//tr[@id='row.1']/td[5]"), "Wrong user last name is displayed in order");
        $this->openTab("link=2", "setfolder");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("17,85 EUR", $this->getText("//td[5]"));
        $this->assertEquals("Billing Address: Company SeleniumTestCase Äß'ü Testing acc for Selenium Mr Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Germany E-mail: birute_test@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("17,85", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("15,00", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("2,85", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("15,47", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("12,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("7,08", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("52,90", $this->getText("//table[@id='order.info']/tbody/tr[8]/td[2]"));

        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("PayPal", $this->getText("//table[4]/tbody/tr[1]/td[2]"),"Payment method not displayed in admin");
        $this->assertEquals("Test S&H set", $this->getText("//table[4]/tbody/tr[2]/td[2]"), "Shipping method is not displayed in admin");

    }


    /*
    * test if option "Calculate default Shipping costs when User is not logged in yet" is working correct in PayPal
    * @group paypal
    */
    public function testPaypalShippingCostNotLoginUser()
    {

        //login to sanbox
        $this->_loginToSandbox();

        // Change price for PayPal payment methode
        $this->open(shopURL."/_updateDB.php?filename=vatOptions.sql");

        // Go to admin and set on all VAT options
        $this->loginAdminForModule("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Other settings");
        sleep(1);
        $this->check("//input[@name='confbools[blCalculateDelCostIfNotLoggedIn]'and @value='true']");
        $this->clickAndWait("save");

        //Go to shop and add product
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1003");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Test product 3", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));

        //Added wrapping and card to basket
        $this->click("id=header");
        $this->click("link=add");
        $this->click("id=wrapping_a6840cc0ec80b3991.74884864");
        $this->click("id=chosen_81b40cf0cd383d3a9.70988998");
        $this->clickAndWait("//button[text()='Apply']");

        $this->assertEquals("Total Products (net): 12,61 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("plus VAT 19% Amount: 2,39 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (gross): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("Shipping cost 3,90 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Gift Wrapping/Greeting Card 5,95 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Grand Total: 24,85 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")),"Grand total is not displayed correctly");

        //Go to paypal express
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertEquals("Pay with a PayPal account - PayPal", $this->getTitle());

        $this->assertTrue($this->isTextPresent("€15.00"));
        $this->assertTrue($this->isTextPresent("€10.50"));
        $this->assertTrue($this->isTextPresent("€5.95"));
        $this->assertTrue($this->isTextPresent("Item total €31.45"));

       //Login to PayPal express
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");

        $this->waitForItemAppear("id=continue_abovefold");
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1003"), "Product number not shown in Paypal");
        $this->assertEquals("Artikelpreis: €15,00", $this->getText("//li[@id='multiitem1']/ul/li[3]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul/li[4]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Surcharge Type of Payment"));
        $this->assertEquals("Artikelpreis: €10,50", $this->getText("//li[@id='multiitem1']/ul[2]/li[2]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[2]/li[3]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Gift Wrapping/Greeting Card"));
        $this->assertEquals("Artikelpreis: €5,95", $this->getText("//li[@id='multiitem1']/ul[3]/li[2]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[3]/li[3]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Warenwert€31,45"), "Product total is not displayed in Paypal");
        $this->assertEquals("Gesamtbetrag €31,45 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"), "Total price is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));
        $this->waitForTextPresent("Gesamtbetrag €44,45 EUR");

        $this->click("id=continue_abovefold");
        $this->waitForItemAppear("id=breadCrumb");

        //Now user on the 1st Cart step
       /* $this->assertTrue($this->isTextPresent("Based on your choice in PayPal Express Checkout, order total has changed. Please check your shopping cart and continue. Hint: for continuing with Express Checkout press Express Checkout button again."));
        $this->assertTrue($this->isElementPresent("paypalExpressCheckoutButton"), "PayPal express button not displayed in the cart");

        //User goes to express checkout once again
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");

        $this->assertTrue($this->isTextPresent("Artikelnummer: 1003"), "Product number not shown in Paypal");
        $this->assertEquals("Artikelpreis: €15,00", $this->getText("//li[@id='multiitem1']/ul/li[3]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul/li[4]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Surcharge Type of Payment"));
        $this->assertEquals("Artikelpreis: €10,50", $this->getText("//li[@id='multiitem1']/ul[2]/li[2]", "Product price not shown in Paypal"));
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[2]/li[3]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Gift Wrapping/Greeting Card"));
        $this->assertEquals("Artikelpreis: €5,95", $this->getText("//li[@id='multiitem1']/ul[3]/li[2]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul[3]/li[3]"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isTextPresent("Warenwert€31,45"), "Product total is not displayed in Paypal");
        $this->assertEquals("Gesamtbetrag €31,45 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"), "Total price is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Ihr Warenkorb"));
        $this->waitForTextPresent("Gesamtbetrag €44,45 EUR");
        $this->click("id=continue_abovefold");

        //Go to shop to finish the order
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");*/
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Greeting Card"));
        $this->assertEquals("3,00 €", $this->getText("id=orderCardTotalPrice"));

        $this->assertEquals("Total Products (net): 12,61 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("plus VAT 19% Amount: 2,39 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (gross): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("Shipping cost 13,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Surcharge Type of Payment: 10,50 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")));
        $this->assertEquals("Gift Wrapping/Greeting Card 5,95 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[6]")));
        $this->assertEquals("Grand Total: 44,45 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[7]")),"Grand total is not displayed correctly");

        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");

        //Go to admin and check the order
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("gerName", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("gerlastname", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->openTab("link=2", "setfolder");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("15,00 EUR", $this->getText("//td[5]"));
        $this->assertEquals("Billing Address: gerName gerlastname ESpachstr. 1 79111 Freiburg Germany E-mail: buyger_1346652948_pre@gmail.com", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("15,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("12,61", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("2,39", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("13,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("10,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("5,95", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("44,45", $this->getText("//table[@id='order.info']/tbody/tr[8]/td[2]"));

        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("PayPal", $this->getText("//table[4]/tbody/tr[1]/td[2]"),"Payment method not displayed in admin");
        $this->assertEquals("Test S&H set", $this->getText("//table[4]/tbody/tr[2]/td[2]"), "Shipping method is not displayed in admin");
    }


    /*
    * test if paypal works with new registered customer
    * @group paypal
    */
    public function testPaypalAsNewCustomer()
    {

        //login to sanbox
        $this->_loginToSandbox();

        //Go to shop and add product
        $this->openShop();
        $this->switchLanguage("English");
        $this->searchFor("1003");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Test product 3", $this->getText("//tr[@id='cartItem_1']/td[3]/div[1]"));

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Next']");

        $this->type("id=userLoginName", "birute_test2@nfq.lt");
        $this->type("name=invadr[oxuser__oxfname]", "John");
        $this->type("name=invadr[oxuser__oxlname]", "Smith");
        $this->type("name=invadr[oxuser__oxstreet]", "Street22");
        $this->type("name=invadr[oxuser__oxstreet]", "Street 22");
        $this->type("name=invadr[oxuser__oxstreetnr]", "25");
        $this->type("name=invadr[oxuser__oxzip]", "LT3265");
        $this->type("name=invadr[oxuser__oxcity]", "Melburg");
        $this->select("id=invCountrySelect", "label=Germany");
        $this->click("id=userNextStepBottom");
        $this->waitForPageToLoad("30000");
        $this->click("name=sShipSet");
        $this->select("name=sShipSet", "label=Test S&H set");

        $this->click("id=payment_oxidpaypal");
        $this->click("id=paymentNextStepBottom");
        $this->waitForPageToLoad("30000");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("id=login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        //$this->waitForItemAppear("id=displayShippingAmount");
        $this->assertEquals("Artikelpreis: €15,00", $this->getText("//li[@id='multiitem1']/ul/li[3]"), "Product price not shown in Paypal");
        $this->assertEquals("Anzahl: 1", $this->getText("//li[@id='multiitem1']/ul/li[4]"), "Product quantity is not shown in Paypal");
        $this->assertEquals("Gesamtbetrag €15,00 EUR", $this->getText("//div[@id='miniCart']/div[3]/ul/li/span"), "Total price is not displayed in PayPal");
        $this->click("id=continue");
        $this->waitForItemAppear("id=breadCrumb");
        $this->assertTrue($this->isTextPresent("Test product 3"));
        $this->assertEquals("Art.No.: 1003", $this->getText("//tr[@id='cartItem_1']/td[2]/div[2]"),"Product number not displayed in last order step");

        $this->assertEquals("Total Products (net): 12,61 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[1]")));
        $this->assertEquals("plus VAT 19% Amount: 2,39 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("Total Products (gross): 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("Grand Total: 15,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");

        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop 4"), "Order is not finished successful");
        //Go to admin and check the order
        $this->loginAdminForModule("Administer Orders", "Orders", "btn.help", "link=2");
        $this->assertEquals("John", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("Smith", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->openTab("link=2", "setfolder");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("15,00 EUR", $this->getText("//td[5]"));
        $this->assertEquals("Billing Address: Mr John Smith Street 22 25 LT3265 Melburg Germany E-mail: birute_test2@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("15,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("12,61", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("2,39", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("15,00", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));

        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[2]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("PayPal", $this->getText("//table[4]/tbody/tr[1]/td[2]"),"Payment method not displayed in admin");
        $this->assertEquals("Test S&H set", $this->getText("//table[4]/tbody/tr[2]/td[2]"), "Shipping method is not displayed in admin");

    }
    
   /*
    * test if paypal works corect when last product ir purchased.
    * @group paypal
    */
    public function testPaypalStockOne()
    {
        //$this->markTestSkipped("Temporarry skipping test");

        //login to sanbox
        $this->_loginToSandbox();

        $this->open(shopURL."/_updateDB.php?filename=changeStock.sql");
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//button");
        $this->openBasket("English");
        
        //Login to shop and go to the basket
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->waitForElementPresent("paypalExpressCheckoutButton", "PayPal express button not displayed in the cart");
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Purchased product name is not displayed");
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("OXID Surf and Kite Shop | Cart | purchase online", $this->getTitle());
        $this->assertEquals("Grand Total: 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->assertTrue($this->isTextPresent("Shipping cost"), "Shipping cost is not displayed correctly");
        $this->assertTrue($this->isTextPresent("exact:?"));
        $this->storeChecked("//input[@name='displayCartInPayPal' and @value='1']");
        $this->assertTrue($this->isTextPresent("Display cart in PayPal"),"Text:Display cart in PayPal for checkbox not displayed");
        $this->assertTrue($this->isElementPresent("name=displayCartInPayPal"),"Checkbox:Display cart in PayPal not displayed");
        
        //Go to Paypal via Paypal Express with "Display cart in PayPal"
        $this->click("name=paypalExpressCheckoutButton");
        $this->waitForItemAppear("id=submitLogin");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Item number: 1001"), "Product number not displayed in paypal ");
        $this->assertFalse($this->isTextPresent("Grand Total: €0,99"),"Grand total should not be displayed");
        $this->type("login_email", "buyger_1346652948_pre@gmail.com");
        $this->type("login_password", "xxxxxxxxx");
        $this->click("id=submitLogin");
        $this->waitForItemAppear("id=continue");
        $this->waitForItemAppear("id=displayShippingAmount");

        $this->assertTrue($this->isTextPresent("Test product 1"), "Purchased product name is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Warenwert€0,99"), "Product price is not displayed in Paypal");
        $this->assertTrue($this->isTextPresent("exact:Versandkosten:"), "Shipping cost is not calculated in PayPal");
        $this->assertTrue($this->isTextPresent("Test product 1"), "Product name is not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Versandmethode: Test S&H set: €0,00 EUR"), "Shipping method is not shown in Paypal");
        $this->assertEquals("Testing user acc Äß'ü PayPal Äß'ü Musterstr. Äß'ü 1 79098 Musterstadt Äß'ü Deutschland Versandmethode: Test S&H set: €0,00 EUR", $this->clearString($this->getText("//div[@class='inset confidential']")));
        $this->assertTrue($this->isTextPresent("buyger_1346652948_pre@gmail.com"));
        $this->assertTrue($this->isTextPresent("Artikelnummer: 1001"), "Product number not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Artikelpreis: €0,99"), "Product price not shown in Paypal");
        $this->assertTrue($this->isTextPresent("Anzahl: 1"), "Product quantity is not shown in Paypal");

        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");

        $this->assertTrue($this->isTextPresent("Gesamtbetrag €0,99 EUR"), "Total price is not displayed in PayPal");
        $this->assertTrue($this->isTextPresent("Versandkosten:€0,00"), "Total price is not displayed in PayPal");

        $this->waitForTextPresent("Gesamtbetrag €0,99 EUR");
        $this->waitForTextPresent("Versandkosten:€0,00");
        $this->assertTrue($this->isTextPresent("Versandkosten:€0,00"), "Shipping cost is not calculated in PayPal");
        $this->waitForItemAppear("id=shippingHandling");
        $this->assertTrue($this->isElementPresent("id=shippingHandling"), "Shipping cost is not calculated in PayPal");

        $this->waitForItemAppear("id=continue");
        // adding sleep to wait while "continue" button will be active
        sleep(10);
        $this->clickAndWait("id=continue");
        $this->waitForItemAppear("id=breadCrumb");

        //Check are all info in the last order step correct
        $this->assertTrue($this->isElementPresent("link=Test product 1"), "Purchased product name is not displayed in last order step");
        $this->assertTrue($this->isTextPresent("Art.No.: 1001"),"Product number not displayed in last order step");
        $this->assertEquals("Shipping cost 0,00 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")), "Shipping cost is not displayed correctly");
        $this->assertEquals("OXID Surf and Kite Shop | Order | purchase online", $this->getTitle());
        $this->assertEquals("Grand Total: 0,99 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[5]")),"Grand total is not displayed correctly");
        $this->assertTrue($this->isTextPresent("PayPal"),"Payment method not displayed in last order step");
        $this->clickAndWait("//button[text()='Purchase']");
        $this->assertTrue($this->isTextPresent("Thank you for your order in OXID eShop"), "Order is not finished successful");
    }    
 }

