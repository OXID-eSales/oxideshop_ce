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

class Acceptance_myAccountFrontendTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ My account related tests ----------------------------------

    /**
     * Login to eshop (popup in top of the page)
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendLoginBox()
    {
        $this->openShop();
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        //closing this popup with esc button
        $this->keyDown("//div[@id='loginBox']//input[@name='lgn_usr']", "\\27");
        $this->keyUp("//div[@id='loginBox']//input[@name='lgn_usr']", "\\27");
        $this->waitForItemDisappear("loginBox");

        //login when username/pass are incorrect. error msg should be in place etc.
        $this->assertFalse($this->isElementPresent("errorBadLogin"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->type("//div[@id='loginBox']//input[@name='lgn_usr']", "anything");
        $this->clickAndWait("//div[@id='loginBox']//button[@type='submit']");
        $this->assertTrue($this->isVisible("errorBadLogin"));
        $this->assertEquals("Wrong e-mail or password!", $this->clearString($this->getText("errorBadLogin")));

        //login with correct user name/pass
        $this->type("//div[@id='loginBox']//input[@name='lgn_usr']", "birute_test@nfq.lt");
        $this->type("//div[@id='loginBox']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='loginBox']//button[@type='submit']");
        $this->assertFalse($this->isElementPresent("loginBox"));
        $this->assertEquals("Hello, UserNamešÄßüл UserSurnamešÄßüл Logout", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]")));
        $this->assertTrue($this->isTextPresent("Just arrived!"));

        $this->clickAndWait("//ul[@id='topMenu']/li[1]/a");
        $this->assertEquals("You are here: / My Account - birute_test@nfq.lt", $this->getText("breadCrumb"));
        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isElementPresent("breadCrumb"));
        $this->clickAndWait("//ul[@id='topMenu']/li[1]/a");
        $this->assertEquals("You are here: / My Account - birute_test@nfq.lt", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//ul[@id='topMenu']//a[text()='Logout']"));
    }


    /**
     * Login to eshop (popup in top of the page)
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendUserTopMenu()
    {

        $this->openShop();
        //Register link
        $this->clickAndWait("//ul[@id='topMenu']/li[2]/a");
        $this->assertEquals("You are here: / Register", $this->getText("breadCrumb"));
        $this->assertEquals("Open account", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Account information"));
        $this->assertTrue($this->isTextPresent("Billing Address"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("You are here: / Register", $this->getText("breadCrumb"));
        $this->assertEquals("Open account", $this->getText("//h1"));
        $this->clickAndWait("link=Home");
        $this->clickAndWait("//ul[@id='newItems']/li[1]//a");

        $this->assertEquals("My Product Compare", $this->clearString($this->getText("//ul[@id='services']/li[4]")));
        $this->assertEquals("My Wish List", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->assertEquals("My Gift Registry", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Product Compare1", $this->clearString($this->getText("//ul[@id='services']/li[4]")));
        $this->assertEquals("My Wish List1", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->assertEquals("My Gift Registry1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->assertEquals("My Account - \"birute_test@nfq.lt\"", $this->getText("//h1"));
        $this->assertEquals("My Wish List", $this->getText("//div[@id='content']//div[2]/dl[1]/dt"));
        $this->assertEquals("Product: 1", $this->getText("//div[@id='content']//div[2]/dl[1]/dd"));
        $this->assertEquals("My Gift Registry", $this->getText("//div[@id='content']//div[2]/dl[2]/dt"));
        $this->assertEquals("Product: 1", $this->getText("//div[@id='content']//div[2]/dl[2]/dd"));
        $this->assertEquals("My Product Comparison", $this->getText("//div[@id='content']//div[2]/dl[3]/dt"));
        $this->assertEquals("Product: 1", $this->getText("//div[@id='content']//div[2]/dl[3]/dd"));
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->assertEquals("You are here: / Login", $this->getText("breadCrumb"));
        $this->assertEquals("Login", $this->getText("//h1"));
        $this->type("//div[@id='content']//input[@name='lgn_usr']", "birute_test@nfq.lt");
        $this->type("//div[@id='content']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='content']//button[text()='Login']");
        $this->assertEquals("Hello, UserNamešÄßüл UserSurnamešÄßüл Logout", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]")));
        $this->assertEquals("My Account - \"birute_test@nfq.lt\"", $this->getText("//h1"));
        $this->assertEquals("My Wish List", $this->getText("//div[@id='content']//div[2]/dl[1]/dt"));
        $this->assertEquals("Product: 1", $this->getText("//div[@id='content']//div[2]/dl[1]/dd"));
        $this->assertEquals("My Gift Registry", $this->getText("//div[@id='content']//div[2]/dl[2]/dt"));
        $this->assertEquals("Product: 1", $this->getText("//div[@id='content']//div[2]/dl[2]/dd"));
        $this->assertEquals("My Product Comparison", $this->getText("//div[@id='content']//div[2]/dl[3]/dt"));
        $this->assertEquals("Product: 1", $this->getText("//div[@id='content']//div[2]/dl[3]/dd"));
    }

    /**
     * My Account navigation: changing password
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountPass()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");

        //changing password
        $this->assertEquals("You are here: / My Account - birute_test@nfq.lt", $this->getText("breadCrumb"));
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->clearString($this->getText("//ul[@id='topMenu']/li/a")));
        $this->clickAndWait("//div[@id='sidebar']//li/a[text()='Change Password']");
        $this->assertEquals("Change Password", $this->getText("//h1"));
        $this->assertFalse($this->isVisible("//span[text()='Error: your password is too short.']"));
        $this->assertFalse($this->isVisible('//span[text()="Passwords don\'t match!"]'));

        //entered diff new passwords
        $this->typeKeys("passwordOld", "useruser");
        $this->type("password_new", "user1user");
        $this->fireEvent("password_new", "blur");
        $this->assertFalse($this->isVisible("//div[@id='content']//li[2]//span[text()='Error: your password is too short.']"));
        $this->type("password_new_confirm", "useruser");
        $this->fireEvent("password_new_confirm", "blur");
        $this->waitForItemAppear('//div[@id="content"]//li[3]//span[text()="Passwords don\'t match!"]');

        //new pass is too short
        $this->clickAndWait("//div[@id='sidebar']//li/a[text()='Change Password']");
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user");
        $this->fireEvent("password_new", "blur");
        $this->waitForItemAppear("//div[@id='content']//li[2]//span[text()='Error: your password is too short.']");
        $this->type("password_new_confirm", "user");
        $this->fireEvent("password_new_confirm", "blur");
        $this->waitForItemAppear("//div[@id='content']//li[3]//span[text()='Error: your password is too short.']");
        $this->assertFalse($this->isVisible('//div[@id="content"]//li[2]//span[text()="Passwords don\'t match!"]'));

        //correct new pass
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user1user");
        $this->fireEvent("password_new", "blur");
        $this->type("password_new_confirm", "user1user");
        $this->fireEvent("password_new_confirm", "blur");
        $this->clickAndWait("savePass");
        $this->assertFalse($this->isVisible("//span[text()='Error: your password is too short.']"));
        $this->assertFalse($this->isVisible('//span[text()="Passwords don\'t match!"]'));
        $this->assertEquals("Change Password", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Your Password has changed."));
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->assertFalse($this->isTextPresent("Wrong e-Mail or password!"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser", false);
        $this->assertTrue($this->isTextPresent("Wrong e-mail or password!"));
        $this->loginInFrontend("birute_test@nfq.lt", "user1user");
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]/a[1]")));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->assertEquals("Change Password", $this->getText("//div[@id='content']//dl[1]/dt"));
        $this->clickAndWait("//div[@id='content']//dl[1]/dt/a");
        $this->assertEquals("Change Password", $this->getText("//h1"));
    }

    /**
     * Right side box My account. Password reminding
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendRightMyAccountRemindPass()
    {
        $this->openShop();
        //page for reminding pass
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->clickAndWait("//div[@id='content']//a[text()='Forgot password?']");
        $this->assertEquals("You are here: / Forgot password?", $this->getText("breadCrumb"));
        $this->assertEquals("Forgot password?", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Have you forgotten your password? "));
        $this->assertEquals("", $this->getValue("forgotPasswordUserLoginName"));
        $this->type("forgotPasswordUserLoginName", "not_existing_user@nfq.lt");
        $this->clickAndWait("//div[@id='content']//button[text()='Request Password']");
        $this->assertTrue($this->isTextPresent("The e-mail address you have entered is invalid. Please enter a valid e-mail address."));
        $this->assertFalse($this->isTextPresent("Password was sent to: not_existing_user@nfq.lt"));
        $this->assertEquals("You are here: / Forgot password?", $this->getText("breadCrumb"));
        $this->assertEquals("Forgot password?", $this->getText("//h1"));
        $this->assertEquals("not_existing_user@nfq.lt", $this->getValue("forgotPasswordUserLoginName"));
        $this->type("forgotPasswordUserLoginName", "birute_test@nfq.lt");
        $this->clickAndWait("//div[@id='content']//button[text()='Request Password']");
        $this->assertFalse($this->isTextPresent("The e-mail address you have entered is invalid. Please enter a valid e-mail address."));
        $this->assertTrue($this->isTextPresent("Password was sent to: birute_test@nfq.lt"));
        //pasword reminder opened via login popup
        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isVisible("forgotPassword"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("//a[@class='forgotPasswordOpener']");
        $this->click("//a[@class='forgotPasswordOpener']");
        $this->waitForItemAppear("forgotPassword");
        $this->assertTrue($this->isTextPresent("Have you forgotten your password?"));
        $this->assertEquals("", $this->getValue("forgotPasswordUserLoginNamePopup"));
        $this->type("forgotPasswordUserLoginNamePopup", "not_existing_user@nfq.lt");
        $this->clickAndWait("//button[text()='Request Password']");
        $this->assertEquals("You are here: / Forgot password?", $this->getText("breadCrumb"));
        $this->assertEquals("Forgot password?", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("The e-mail address you have entered is invalid. Please enter a valid e-mail address."));
        $this->assertFalse($this->isTextPresent("Password was sent to: not_existing_user@nfq.lt"));
        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isVisible("forgotPassword"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("//a[@class='forgotPasswordOpener']");
        $this->click("//a[@class='forgotPasswordOpener']");
        $this->waitForItemAppear("forgotPassword");
        $this->assertTrue($this->isTextPresent("Have you forgotten your password?"));
        $this->type("forgotPasswordUserLoginNamePopup", "birute_test@nfq.lt");
        $this->clickAndWait("//button[text()='Request Password']");
        $this->assertFalse($this->isTextPresent("The e-mail address you have entered is invalid. Please enter a valid e-mail address."));
        $this->assertTrue($this->isTextPresent("Password was sent to: birute_test@nfq.lt"));
    }


    /**
     * My Account: newsletter settings
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountNewsletter()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");

        //newsletter settings
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Newsletter Settings']");
        $this->assertEquals("Newsletter Settings", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("It's possible to cancel newsletter at any time."));
        $this->assertFalse($this->isTextPresent("The Newsletter subscription was successful."));
        $this->assertEquals("No", $this->getSelectedLabel("status"));
        $this->select("status", "label=Yes");
        $this->clickAndWait("newsletterSettingsSave");
        $this->assertEquals("Newsletter Settings", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("The Newsletter subscription was successful."));
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Newsletter Settings']");
        $this->assertFalse($this->isTextPresent("The Newsletter subscription has been canceled."));
        $this->assertEquals("Yes", $this->getSelectedLabel("status"));
        $this->select("status", "label=No");
        $this->clickAndWait("newsletterSettingsSave");
        $this->assertTrue($this->isTextPresent("The Newsletter subscription has been canceled."));
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Newsletter Settings']");
        $this->assertEquals("No", $this->getSelectedLabel("status"));
    }

    /**
     * My Account navigation: billing address
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountAddressBilling()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        //Addresses testing
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Billing and Shipping Settings']");
        $this->assertEquals("Billing and Shipping Settings", $this->getText("//h1"));
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxcountryid]");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertTrue($this->isVisible("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->assertEquals("Please select a state", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));

        //changing billing address
        $this->select("invadr[oxuser__oxsal]", "label=Mrs");
        $this->type("invadr[oxuser__oxfname]", "UserName1_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "UserSurname1_šÄßüл");
        $this->type("invadr[oxuser__oxcompany]", "UserCompany1_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "Musterstr.1_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "11");
        $this->type("invadr[oxuser__oxzip]", "790981");
        $this->type("invadr[oxuser__oxcity]", "Musterstadt1_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "BE0876797054");
        $this->type("invadr[oxuser__oxaddinfo]", "User additional info1_šÄßüл");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->waitForItemDisappear("oxStateSelect_invadr[oxuser__oxstateid]");
        $this->type("invadr[oxuser__oxfon]", "0800 1111111");
        $this->type("invadr[oxuser__oxfax]", "0800 1111121");
        $this->type("invadr[oxuser__oxmobfon]", "0800 1111141");
        $this->type("invadr[oxuser__oxprivfon]", "0800 1111131");
        $this->type("invadr[oxuser__oxbirthdate][day]", "02");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=February");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1982");

        $this->clickAndWait("accUserSaveTop");
        $this->assertFalse($this->isElementPresent("div[@class='errorbox inbox']"));
        $this->assertEquals("Billing and Shipping Settings", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Billing and Shipping Settings']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxcountryid]");
        $this->assertEquals("Mrs", $this->getSelectedLabel("invadr[oxuser__oxsal]"));
        $this->assertEquals("UserName1_šÄßüл", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("UserSurname1_šÄßüл", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("UserCompany1_šÄßüл", $this->getValue("invadr[oxuser__oxcompany]"));
        $this->assertEquals("Musterstr.1_šÄßüл", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("11", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("790981", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("Musterstadt1_šÄßüл", $this->getValue("invadr[oxuser__oxcity]"));
        $this->assertEquals("BE0876797054", $this->getValue("invadr[oxuser__oxustid]"));
        $this->assertEquals("User additional info1_šÄßüл", $this->getValue("invadr[oxuser__oxaddinfo]"));
        $this->assertEquals("Belgium", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertFalse($this->isVisible("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->assertEquals("0800 1111111", $this->getValue("invadr[oxuser__oxfon]"));
        $this->assertEquals("0800 1111121", $this->getValue("invadr[oxuser__oxfax]"));
        $this->assertEquals("0800 1111141", $this->getValue("invadr[oxuser__oxmobfon]"));
        $this->assertEquals("0800 1111131", $this->getValue("invadr[oxuser__oxprivfon]"));
        $this->assertEquals("02", $this->getValue("invadr[oxuser__oxbirthdate][day]"));
        $this->assertEquals("02", $this->getValue("invadr[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1982", $this->getValue("invadr[oxuser__oxbirthdate][year]"));
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_invadr[oxuser__oxstateid]");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Saxony");

        $this->clickAndWait("accUserSaveTop");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("Saxony", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
    }

    /**
     * My Account navigation: changing shipping address and pass
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountAddressShipping()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        //Addresses testing
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Billing and Shipping Settings']");
        $this->assertEquals("Billing and Shipping Settings", $this->getText("//h1"));

        //changing email
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxusername]");
        $this->type("invadr[oxuser__oxusername]", "birute01@nfq.lt");
        $this->keyUp("invadr[oxuser__oxusername]", "t");
        sleep(1);
        $this->assertTrue($this->isVisible("user_password"));
        $this->type("user_password", "useruser");
        $this->clickAndWait("accUserSaveTop");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser", false);
        $this->assertTrue($this->isTextPresent("Wrong e-mail or password!"));
        $this->loginInFrontend("birute01@nfq.lt", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Billing and Shipping Settings']");
        $this->assertEquals("Billing and Shipping Settings", $this->getText("//h1"));

        //delivery address
        $this->assertEquals("on", $this->getValue("showShipAddress"));
        $this->click("showShipAddress");
        $this->waitForItemAppear("addressId");
        $this->select("addressId", "label=New Address");
        $this->select("deladr[oxaddress__oxsal]", "label=Mrs");
        $this->type("deladr[oxaddress__oxfname]", "First name_šÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "Last name_šÄßüл");
        $this->type("deladr[oxaddress__oxcompany]", "company_šÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "street_šÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "1");
        $this->type("deladr[oxaddress__oxzip]", "111");
        $this->type("deladr[oxaddress__oxcity]", "city_šÄßüл");
        $this->type("deladr[oxaddress__oxaddinfo]", "additional Info_šÄßüл");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_deladr[oxaddress__oxstateid]");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Berlin");
        $this->type("deladr[oxaddress__oxfon]", "111-111222");
        $this->type("deladr[oxaddress__oxfax]", "222-222111");

        $this->clickAndWait("accUserSaveTop");
        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Billing and Shipping Settings']");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_deladr[oxaddress__oxstateid]"));
        $this->select("addressId", "label=New Address");
        sleep(1);

        $this->select("addressId", "label=First name_šÄßüл Last name_šÄßüл, street_šÄßüл 1 city_šÄßüл");
        $this->waitForPageToLoad("30000");
        $this->select("deladr[oxaddress__oxsal]", "label=Mr");
        $this->type("deladr[oxaddress__oxfname]", "First name1");
        $this->type("deladr[oxaddress__oxlname]", "Last name1");
        $this->type("deladr[oxaddress__oxcompany]", "company1");
        $this->type("deladr[oxaddress__oxstreet]", "street1");
        $this->type("deladr[oxaddress__oxstreetnr]", "11");
        $this->type("deladr[oxaddress__oxzip]", "1111");
        $this->type("deladr[oxaddress__oxcity]", "city1");
        $this->type("deladr[oxaddress__oxaddinfo]", "additional Info1");
        $this->select("deladr[oxaddress__oxcountryid]", "label=France");
        $this->assertFalse($this->isVisible("oxStateSelect_deladr[oxaddress__oxstateid]"));
        $this->type("deladr[oxaddress__oxfon]", "111-1112221");
        $this->type("deladr[oxaddress__oxfax]", "222-2221111");
        $this->clickAndWait("accUserSaveTop");

        $this->clickAndWait("//div[@id='sidebar']/ul//a[text()='Billing and Shipping Settings']");
        $this->assertEquals("off", $this->getValue("showShipAddress"));
        $this->assertEquals("Mr", $this->getSelectedLabel("deladr[oxaddress__oxsal]"));
        $this->assertEquals("First name1", $this->getValue("deladr[oxaddress__oxfname]"));
        $this->assertEquals("Last name1", $this->getValue("deladr[oxaddress__oxlname]"));
        $this->assertEquals("company1", $this->getValue("deladr[oxaddress__oxcompany]"));
        $this->assertEquals("street1", $this->getValue("deladr[oxaddress__oxstreet]"));
        $this->assertEquals("11", $this->getValue("deladr[oxaddress__oxstreetnr]"));
        $this->assertEquals("1111", $this->getValue("deladr[oxaddress__oxzip]"));
        $this->assertEquals("city1", $this->getValue("deladr[oxaddress__oxcity]"));
        $this->assertEquals("additional Info1", $this->getValue("deladr[oxaddress__oxaddinfo]"));
        $this->assertEquals("France", $this->getSelectedLabel("deladr[oxaddress__oxcountryid]"));
        $this->assertEquals("111-1112221", $this->getValue("deladr[oxaddress__oxfon]"));
        $this->assertEquals("222-2221111", $this->getValue("deladr[oxaddress__oxfax]"));
    }


    /**
     * My Account navigation: My Wish List
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountWishList()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1003");

        $this->clickAndWait("searchList_1");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("//h1")));
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");

        //wish list testing
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Wish List1", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->clickAndWAit("//ul[@id='services']/li[5]/a");
        $this->assertEquals("My Wish List", $this->getText("//h1"));
        $this->assertEquals("You are here: / My Account / My Wish List", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//ul[@id='noticelistProductList']/li/form[@name='tobasket.noticelistProductList_1']/div[2]/div/a"));
        //$this->assertEquals("Art.No. 1003", $this->getText("//form[@name='tobasket.noticelistProductList_1']/div[2]/div/span"));
        $this->assertEquals("Test product 3 short desc [EN] šÄßüл", $this->getText("//form[@name='tobasket.noticelistProductList_1']/div[2]/div[2]"));
        $this->assertEquals("75,00 € *",$this->getText("productPrice_noticelistProductList_1"));
        $this->clickAndWait("//form[@name='tobasket.noticelistProductList_1']/div/a");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[5]/a");
        $this->clickAndWait("//form[@name='tobasket.noticelistProductList_1']/div[2]/div/a");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[5]/a");
        $this->type("amountToBasket_noticelistProductList_1", "2");
        $this->clickAndWait("toBasket_noticelistProductList_1");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("//button[@triggerform='remove_tonoticelistnoticelistProductList_1']");
        $this->assertEquals("My Wish List", $this->getText("//h1"));
        $this->assertEquals("You are here: / My Account / My Wish List", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("noticelistProductList"));
        $this->assertTrue($this->isTextPresent("Your Wish List is empty."));
    }


    /**
     * My Account navigation: My Gift Registry. setting gift registry as searchable
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendGiftRegistrySearchable()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");

        //gift registry
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Gift Registry1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("breadCrumb"));
        $this->assertEquals("My Gift Registry", $this->getText("//h1"));

        //making gift registry not searchable
        $this->assertTrue($this->isTextPresent("Everyone shall be able to search and display my gift registry:"));
        $this->assertEquals("Yes", $this->getSelectedLabel("blpublic"));
        $this->select("blpublic", "label=No");
        $this->clickAndWait("//form[@name='wishlist_wishlist_status']//button");

        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertTrue($this->isTextPresent("Sorry, no Gift Registry found!"));
        $this->assertEquals("No", $this->getSelectedLabel("blpublic"));

        //making gift registry searchable
        $this->select("blpublic", "label=Yes");
        $this->clickAndWait("//form[@name='wishlist_wishlist_status']//button");

        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertTrue($this->isElementPresent("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл"));

        $this->clickAndWait("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл");
        $this->assertEquals("You are here: / Public Gift Registries", $this->getText("breadCrumb"));
        $this->assertEquals("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("wishlistProductList_1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->assertTrue($this->isTextPresent("Click here to send your gift registry to your friends"));

        $this->clickAndWait("link=Click here to send your gift registry to your friends.");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//label[text()='Send Gift Registry']"));
        $this->type("editval[rec_email]", "birute@nfq.lt");
        $this->type("editval[rec_name]", "recipient");
        $this->type("editval[send_message]", "Hi, I created a Gift Registry at OXID.");
        $this->clickAndWait("//button[text()='Send']");
        $this->assertTrue($this->isTextPresent("Your Gift Registry was sent successfully to: birute@nfq.lt"));
        $this->assertEquals("recipient", $this->getValue("editval[rec_name]"));
        $this->assertEquals("birute@nfq.lt", $this->getValue("editval[rec_email]"));
        $this->assertEquals("Hi, I created a Gift Registry at OXID.", $this->getValue("editval[send_message]"));
    }

    /**
     * My Account navigation: My Gift Registry
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountGiftRegistry()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //gift registry
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Gift Registry1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("breadCrumb"));

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//ul[@id='wishlistProductList']/li/form[@name='tobasket.wishlistProductList_1']/div[2]/div[1]/a"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_wishlistProductList_1"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("//form[@name='tobasket.wishlistProductList_1']/div[2]/div[2]"));
        $this->assertEquals("50,00 € *", $this->getText("productPrice_wishlistProductList_1"));

        $this->clickAndWait("//form[@name='tobasket.wishlistProductList_1']/div[1]/a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->clickAndWait("//form[@name='tobasket.wishlistProductList_1']/div[2]/div[1]/a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->type("amountToBasket_wishlistProductList_1", "2");
        $this->clickAndWait("toBasket_wishlistProductList_1");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));
        $this->clickAndWait("//button[@triggerform='remove_towishlistwishlistProductList_1']");
        $this->assertTrue($this->isTextPresent("The Gift Registry is empty."));

    }

    /**
     * My Account navigation: My Gift Registry
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendSearchForGiftRegistry()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //creating gift registry
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");

        //logging in as other user for searching this gift registry
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Logout']");
        $this->loginInFrontend("admin@myoxideshop.com", "admin0303");

        //search for gift registry
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->clickAndWait("link=My Gift Registry");
        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertTrue($this->isElementPresent("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл"));

        $this->clickAndWait("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл");
        $this->assertEquals("You are here: / Public Gift Registries", $this->getText("breadCrumb"));
        $this->assertEquals("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("wishlistProductList_1"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_wishlistProductList_1"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("//form[@name='tobasket.wishlistProductList_1']/div[2]/div[2]"));
        $this->assertEquals("50,00 € *", $this->getText("productPrice_wishlistProductList_1"));

        $this->clickAndWait("//form[@name='tobasket.wishlistProductList_1']//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));

        $this->clickAndWait("link=Public Gift Registries");
        $this->assertEquals("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//form[@name='tobasket.wishlistProductList_1']/div[2]//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->clickAndWait("link=Public Gift Registries");
        $this->assertEquals("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("wishlistProductList_1"));
        $this->type("amountToBasket_wishlistProductList_1", "2");
        $this->clickAndWait("toBasket_wishlistProductList_1");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        //deleting gift registry
        $this->clickAndWait("link=Logout");
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->clickAndWait("//button[@triggerform='remove_towishlistwishlistProductList_1']");
        $this->assertTrue($this->isTextPresent("The Gift Registry is empty."));

        //searching for gift registry again. now gift registry wil not be found, couse its empty
        $this->clickAndWait("link=Logout");
        $this->loginInFrontend("admin@myoxideshop.com", "admin0303");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->clickAndWait("link=My Gift Registry");
        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertTrue($this->isTextPresent("Sorry, no Gift Registry found!"));
    }

    /**
     * Gift Registry is disabled via performance options
     * @group navigation
     * @group myAccount
     */
    public function testFrontendDisabledGiftRegistry()
    {
        //(Use gift registry) is disabled
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showWishlist" => array("type" => "bool", "value" => "false",  "module" => "theme:azure")));
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1000");
        $this->clickAndWait("searchList_1");

        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");

        $this->assertFalse($this->isElementPresent("linkToWishList"));
        $this->assertTrue($this->isElementPresent("linkToNoticeList"));
        $this->assertTrue($this->isElementPresent("recommList"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->assertTrue($this->isElementPresent("//div[@id='sidebar']//a[text()='My Wish List']"));
        $this->assertFalse($this->isElementPresent("//div[@id='sidebar']//a[text()='My Gift Registry']"));
        $this->assertTrue($this->isElementPresent("//div[@id='sidebar']//a[text()='My Listmania List']"));
    }

    /**
     * My Account navigation: Product Comparison
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendMyAccountCompare()
    {
        $this->clearTmp();
        $this->openShop();
        //compare list testing
        $this->searchFor("100");
        $this->clickAndWait("searchList_1");
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");

        $this->clickAndWait("linkNextArticle");
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");

        $this->clickAndWait("linkNextArticle");
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");

        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Product Compare3", $this->clearString($this->getText("//ul[@id='services']/li[4]")));
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->assertEquals("You are here: / My Account / Product Comparison", $this->getText("breadCrumb"));
        $this->assertEquals("Product Comparison", $this->getText("//h1"));

        $this->assertTrue($this->isElementPresent("compareRight_1000"));
        $this->assertTrue($this->isElementPresent("compareRight_1001"));
        $this->assertTrue($this->isElementPresent("compareLeft_1002"));

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[1]/div[2]/strong")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[2]/div[2]/strong")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[3]/div[2]/strong")));
        $this->assertEquals("Weight: 2 kg Art.No. 1000", $this->clearString($this->getText("//tr[@id='firstTr']/td[1]/div[2]/span")));
        $this->assertEquals("Art.No. 1001", $this->getText("//tr[@id='firstTr']/td[2]/div[2]/span"));
        $this->assertEquals("Art.No. 1002", $this->getText("//tr[@id='firstTr']/td[3]/div[2]/span"));

        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("//div[@id='compareSelections_2']//ul")));
        $this->assertEquals("var1 [EN] šÄßüл var2 [EN] šÄßüл", $this->clearString($this->getText("//div[@id='compareVariantSelections_3']//ul")));
        $this->clickAndWait("//tr[@id='firstTr']/td[1]//a/img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->clickAndWait("//tr[@id='firstTr']/td[2]/div[2]/strong/a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->clickAndWait("//tr[@id='firstTr']/td[1]//button");
        $this->clickAndWait("//tr[@id='firstTr']/td[1]//button");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл:", $this->getText("cmpAttrTitle_1"));
        $this->assertEquals("attr value 1 [EN] šÄßüл", $this->getText("cmpAttr_1_1000"));
        $this->assertEquals("attr value 11 [EN] šÄßüл", $this->getText("cmpAttr_1_1001"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл:", $this->getText("cmpAttrTitle_2"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("cmpAttr_2_1000"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("cmpAttr_2_1001"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл:", $this->getText("cmpAttrTitle_3"));
        $this->assertEquals("attr value 2 [EN] šÄßüл", $this->getText("cmpAttr_3_1000"));
        $this->assertEquals("attr value 12 [EN] šÄßüл", $this->getText("cmpAttr_3_1001"));
        $this->clickAndWait("compareRight_1000");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[1]/div[2]/strong")));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[2]/div[2]/strong")));
        $this->clickAndWait("compareLeft_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[1]/div[2]/strong")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[2]/div[2]/strong")));
        $this->clickAndWait("remove_cmp_1000");
        $this->clickAndWait("remove_cmp_1002");
        $this->assertEquals("Product Comparison", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Please select at least two products to be compared."));
    }

    /**
     * creating listmania
     * @group navigation
     * @group myAccount
     */
    public function testListmaniaCreating()
    {
        //deleting existing recommlists for better possibility to test creating of new recomlist
        $aRecommListParams = array("OXTITLE" => 'Kite-Equipment');
        $this->callShopSC("oxRecommList", "delete", "e7a0b1906e0d94e05693f06b0b6fcc32", $aRecommListParams);
        $aRecommListParams = array("OXTITLE" => 'recomm title');
        $this->callShopSC("oxRecommList", "delete", "testrecomm", $aRecommListParams);
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("There is no Listmania lists at the moment. To create new, click here."));
        $this->clickAndWait("//div[@id='content']/a[text()='here']");
        //creating listmania in MyAccount
        $this->assertEquals("My Listmania List", $this->getText("//div[@id='sidebar']/ul/li[8]"));
        $this->assertEquals("Listmania", $this->getText("//h1"));
        $this->assertEquals("", $this->getValue("recomm_title"));
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->getValue("recomm_author"));
        $this->assertEquals("", $this->getValue("recomm_desc"));
        $this->assertTrue($this->isTextPresent("No Listmania Lists found"));
        $this->type("recomm_title", "recomm title1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_desc", "recom introduction1");
        $this->clickAndWait("//div[@id='content']//button[text()='Save']");
        $this->assertTrue($this->isTextPresent("Recommendation list changes saved"));
        $this->clickAndWait("//div[@id='sidebar']//a[text()='My Listmania List']");
        $this->assertEquals("recomm title1", $this->getText("//div[@id='content']//ul[@id='recommendationsLists']/li[1]/div/div/a"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//ul[@id='recommendationsLists']/li[1]//button[@name='deleteList']"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//ul[@id='recommendationsLists']/li[1]//button[@name='editList']"));
        $this->assertEquals("recom introduction1", $this->getText("//ul[@id='recommendationsLists']/li[1]/div/div[2]"));
        $this->assertTrue($this->isTextPresent("recomm title1 : A List by recomm author1"));
        $this->clickAndWait("link=recomm title1");

        $this->assertTrue($this->isElementPresent("breadCrumb"));
        $this->assertEquals("You are here: / My Account / Listmania", $this->getText("breadCrumb"));
        $this->assertEquals("recomm title1", $this->getHeadingText("//h1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[2]/a");
        $this->assertEquals("My Account - \"birute_test@nfq.lt\"", $this->getText("//h1"));
        $this->assertEquals("My Listmania List", $this->getText("//div[@id='content']//div[2]/dl[4]/dt"));
        $this->assertEquals("Lists: 1", $this->getText("//div[@id='content']//div[2]/dl[4]/dd"));
    }

    /**
     * Checking Listmania
     * @group navigation
     * @group myAccount
     */
    public function testFrontendListmaniaInfo()
    {
        $aWrappingParams = array("OXTYPE" => 'oxrecommlist');
        $this->callShopSC("oxReview", "delete", $sOxid ="testrecomreview" , $aWrappingParams,1);
        $aRatingParams = array("OXTYPE" => 'oxrecommlist');
        $this->callShopSC("oxRating", "delete", $sOxid ="testrecomrating" , $aRatingParams,1);
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //checking small listmania box
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");

        $this->assertEquals("Listmania", $this->getText("//div[@id='recommendationsBox']/h3"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("Listmania", $this->clearString($this->getHeadingText("//div[@id='recommendationsBox']/h3")));
        $this->clickAndWait("//div[@id='recommendationsBox']//ul/li[1]/a");
        $this->assertEquals("recomm title (A List by recomm author)", $this->getHeadingText("//h1"));
        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->clickAndWait("//ul[@id='languages']/li[2]/a");
        $this->assertEquals("recomm title (eine Liste von recomm author)", $this->getHeadingText("//h1"));
        $this->click("languageTrigger");

        $this->waitForItemAppear("languages");
        $this->clickAndWait("//ul[@id='languages']/li[3]/a");
        $this->assertEquals("recomm title (A List by recomm author)", $this->getHeadingText("//h1"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("Listmania", $this->getText("//div[@id='recommendationsBox']/h3"));
        $this->clickAndWait("//div[@id='recommendationsBox']/div/a/img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Listmania", $this->clearString($this->getHeadingText("//div[@id='recommendationsBox']/h3")));
        $this->assertEquals("recomm title", $this->getText("//div[@id='recommendationsBox']//ul/li[1]/a"));
        $this->assertEquals("A List by: recomm author", $this->getText("//div[@id='recommendationsBox']//ul/li[1]/div"));

        //writing recommendation for listmania
        $this->clickAndWait("//div[@id='recommendationsBox']//ul/li[1]/a");
        $this->assertEquals("You are here: / Listmania", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssRecommListProducts']"));
        $this->assertEquals("recomm title (A List by recomm author)", $this->getHeadingText("//h1"));
        $this->assertTrue($this->isTextPresent("recom introduction"));
        $this->assertTrue($this->isTextPresent("recom introduction"));
        $this->assertTrue($this->isTextPresent("No review available for this item."));
        $this->assertEquals("No ratings.", $this->getText("itemRatingText"));
        $this->click("writeNewReview");
        sleep(1);
        $this->click("//ul[@id='reviewRating']/li[@class='s3']/a");
        sleep(1);
        $this->type("rvw_txt", "recommendation for this list");
        $this->clickAndWait("reviewSave");
        $this->assertEquals("You are here: / Listmania", $this->getText("breadCrumb"));
        $this->assertEquals("UserNamešÄßüл writes: ".date("d.m.Y"), $this->clearString($this->getText("reviewName_1")));
        $this->assertEquals("recommendation for this list", $this->getText("reviewText_1"));
        $this->assertEquals("(1)", $this->getText("itemRatingText"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_productList_1"));
        $this->assertTrue($this->isElementPresent("//form[@name='tobasket.productList_1']//div[text()='comment for product 1000']"));
        $this->assertEquals("50,00 € *", $this->getText("productPrice_productList_1"));
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("You are here: / Listmania", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=recomm title");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//form[@name='tobasket.productList_1']/div[2]//a"));
        $this->clickAndWait("//ul[@id='productList']/li[1]/form/div[2]//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=recomm title");

        $this->type("amountToBasket_productList_1", "2");
        $this->clickAndWait("toBasket_productList_1");
        $this->assertEquals("You are here: / Listmania", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//form[@name='tobasket.productList_1']/div[2]//a"));
    }

    /**
     * Checking Listmania
     * @group navigation
     * @group myAccount
     */
    public function testFrontendListmaniaAddSearch()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //adding other products to listmania
        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1001");
        $this->clickAndWait("//button[text()='Add to List']");

        $this->searchFor("1002");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1002");
        $this->clickAndWait("//button[text()='Add to List']");

        //search in listmania
        $this->type("searchRecomm", "title");
        $this->clickAndWait("//div[@id='recommendationsBox']//button");
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->assertEquals("1 Hits for \"title\"", $this->getText("//h1"));
        $this->assertEquals("recomm title", $this->getText("//ul[@id='recommendationsLists']/li[1]//a"));
        $this->assertEquals("recom introduction", $this->getText("//ul[@id='recommendationsLists']/li[1]//div[2]"));
        $this->assertTrue($this->isTextPresent("recomm title : A List by recomm author"));
        $this->clickAndWait("link=recomm title");
        $this->assertEquals("recomm title (A List by recomm author)", $this->getHeadingText("//h1"));
        $this->assertTrue($this->isTextPresent("recom introduction"));
        $this->assertEquals("Write a review.", $this->getText("writeNewReview"));
        $this->assertEquals("recommendation for this list", $this->getText("reviewText_1"));

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("productList_2"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_3"));
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->type("searchRecomm", "no entry");
        $this->clickAndWait("//div[@id='recommendationsBox']//button");
        $this->assertEquals("no entry", $this->getValue("searchRecomm"));
        $this->assertEquals("You are here: / Listmania", $this->getText("breadCrumb"));
        $this->assertEquals("0 Hits for \"no entry\"", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("No Listmania Lists found"));

        //editing listmania (with articles)
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[2]/a");
        $this->clickAndWait("link=My Listmania List");
        $this->clickAndWait("//ul[@id='recommendationsLists']/li[1]//button[text()='edit']");
        $this->assertEquals("recomm title", $this->getText("//h1"));
        $this->assertEquals("recomm title", $this->getValue("recomm_title"));
        $this->assertEquals("recomm author", $this->getValue("recomm_author"));
        $this->assertEquals("recom introduction", $this->getValue("recomm_desc"));
        $this->type("recomm_desc", "recom introduction1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_title", "recomm title1");
        $this->clickAndWait("//form[@name='saverecommlist']//button[text()='Save']");
        $this->assertTrue($this->isTextPresent("Recommendation list changes saved"));
        $this->assertEquals("recomm title1", $this->getText("//h1"));

    }

    /**
     * Checking Listmania
     * @group navigation
     * @group myAccount
     */
    public function testFrontendListmaniaDelete()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //adding other products to listmania
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1001");
        $this->clickAndWait("//button[text()='Add to List']");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[2]/a");
        $this->assertEquals("My Account - \"birute_test@nfq.lt\"", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='content']//div[2]/dl[4]/dt/a");
        $this->clickAndWait("//div[@id='content']//ul/li[1]//button[@name='editList']");

        //removing articles from list
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("recommendProductList_2"));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("//div[@id='selectlistsselector_recommendProductList_2']//ul")));
        $this->clickAndWait("//button[@triggerform='remove_removeArticlerecommendProductList_2']");
        $this->assertEquals("recomm title", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("recommendProductList_1"));
        $this->assertFalse($this->isElementPresent("recommendProductList_2"));
        $this->clickAndWait("//div[@id='sidebar']//a[text()='My Listmania List']");
        $this->assertEquals("recomm title", $this->getText("//ul[@id='recommendationsLists']/li[1]//a"));
        $this->assertTrue($this->isTextPresent("recomm title : A List by recomm author"));
        $this->assertTrue($this->isTextPresent("recom introduction"));

        //deleting recom list
        $this->clickAndWait("//ul[@id='recommendationsLists']/li[1]//button[@name='deleteList']");
        $this->assertTrue($this->isTextPresent("No Listmania Lists found"));
    }

    /**
     * Product details. test for checking main product details available only for logged in user
     * @group navigation
     * @group user
     * @group myAccount
     */
    public function testFrontendDetailsForLoggedInUsers()
    {
        if ( isSUBSHOP ) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        //review
        $this->assertEquals("You have to be logged in to write a review.", $this->clearString($this->getText("reviewsLogin")));
        $this->clickAndWait("reviewsLogin");
        $this->assertEquals("You are here: / Login", $this->getText("breadCrumb"));
        $this->assertEquals("Login", $this->getText("//h1"));
        $this->type("//div[@id='content']//form[@name='login']//input[@name='lgn_usr']", "birute_test@nfq.lt");
        $this->type("//div[@id='content']//form[@name='login']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='content']//form[@name='login']//button");
        $this->assertFalse($this->isVisible("rvw_txt"));
        $this->click("writeNewReview");
        $this->waitForItemAppear("rvw_txt");
        //selecting rating near product img
        $this->click("//ul[@id='reviewRating']/li[5]/a");
        sleep(1);
        $this->assertEquals("4", $this->getValue("artrating"));
        $this->click("//ul[@id='reviewRating']/li[4]/a");
        sleep(1);
        $this->assertEquals("3", $this->getValue("artrating"));
        $this->type("rvw_txt", "user review [EN] šÄßüл for product 1001");
        $this->clickAndWait("reviewSave");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals(0, strpos($this->getText("reviewName_1"), "UserNamešÄßüл"));
        $this->assertEquals("user review [EN] šÄßüл for product 1001", $this->getText("reviewText_1"));
        $this->assertEquals("(1)", $this->getText("itemRatingText"));

        //wish list and gift registry
        $this->assertTrue($this->isElementPresent("//p[@id='servicesTrigger']/span"));
    
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");
        $this->assertEquals("2",$this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");
        $this->assertEquals("3",$this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Wish List1", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->assertEquals("My Gift Registry1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWait("//ul[@id='services']/li[5]/a");
        $this->clickAndWait("//button[@triggerform='remove_tonoticelistnoticelistProductList_1']");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Wish List", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->clickAndWait("//ul[@id='services']/li[6]/a");
        $this->clickAndWait("//button[@triggerform='remove_towishlistwishlistProductList_1']");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("My Gift Registry", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
    }

}