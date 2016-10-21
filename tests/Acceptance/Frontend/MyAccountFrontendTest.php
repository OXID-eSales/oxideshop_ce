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

/** My account related tests */
class MyAccountFrontendTest extends FrontendTestCase
{
    /**
     * Login to eshop (popup in top of the page)
     *
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
        $this->assertElementNotPresent("errorBadLogin");
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->type("//div[@id='loginBox']//input[@name='lgn_usr']", "non-existing-user@oxid-esales.dev");
        $this->clickAndWait("//div[@id='loginBox']//button[@type='submit']");
        $this->assertTrue($this->isVisible("errorBadLogin"));
        $this->assertEquals("%ERROR_MESSAGE_USER_NOVALIDLOGIN%", $this->clearString($this->getText("errorBadLogin")));

        //login with correct user name/pass
        $this->type("//div[@id='loginBox']//input[@name='lgn_usr']", "example_test@oxid-esales.dev");
        $this->type("//div[@id='loginBox']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='loginBox']//button[@type='submit']");
        $this->assertElementNotPresent("loginBox");
        $this->assertEquals("%GREETING%UserNamešÄßüл UserSurnamešÄßüл %LOGOUT%", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]")));
        $this->assertTextPresent("%JUST_ARRIVED%");

        $this->clickAndWait("//ul[@id='topMenu']/li[1]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% - example_test@oxid-esales.dev", $this->getText("breadCrumb"));
        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("breadCrumb");
        $this->clickAndWait("//ul[@id='topMenu']/li[1]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% - example_test@oxid-esales.dev", $this->getText("breadCrumb"));
        $this->assertElementPresent("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
    }


    /**
     * Login to eshop (popup in top of the page)
     *
     * @group myAccount
     */
    public function testFrontendUserTopMenu()
    {
        $this->openShop();
        //Register link
        $this->clickAndWait("//ul[@id='topMenu']/li[2]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_REGISTER%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_REGISTER%", $this->getText("//h1"));
        $this->assertTextPresent("%ACCOUNT_INFORMATION%");
        $this->assertTextPresent("%BILLING_ADDRESS%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_REGISTER%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_REGISTER%", $this->getText("//h1"));

        $this->openArticle( 1000 );

        $this->assertEquals("%MY_PRODUCT_COMPARISON%", $this->clearString($this->getText("//ul[@id='services']/li[4]")));
        $this->assertEquals("%MY_WISH_LIST%", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->assertEquals("%MY_GIFT_REGISTRY%", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
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
        $this->assertEquals("%MY_PRODUCT_COMPARISON%1", $this->clearString($this->getText("//ul[@id='services']/li[4]")));
        $this->assertEquals("%MY_WISH_LIST%1", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->assertEquals("%MY_GIFT_REGISTRY%1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT% - \"example_test@oxid-esales.dev\"", $this->getText("//h1"));
        $this->assertEquals("%MY_WISH_LIST%", $this->getText("//section[@id='content']//div[2]/dl[1]/dt"));
        $this->assertEquals("%PRODUCT%: 1", $this->getText("//section[@id='content']//div[2]/dl[1]/dd"));
        $this->assertEquals("%MY_GIFT_REGISTRY%", $this->getText("//section[@id='content']//div[2]/dl[2]/dt"));
        $this->assertEquals("%PRODUCT%: 1", $this->getText("//section[@id='content']//div[2]/dl[2]/dd"));
        $this->assertEquals("%MY_PRODUCT_COMPARISON%", $this->getText("//section[@id='content']//div[2]/dl[3]/dt"));
        $this->assertEquals("%PRODUCT%: 1", $this->getText("//section[@id='content']//div[2]/dl[3]/dd"));
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %LOGIN%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT%", $this->getText("//h1"));
        $this->type("//section[@id='content']//input[@name='lgn_usr']", "example_test@oxid-esales.dev");
        $this->type("//section[@id='content']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//section[@id='content']//button[text()='%LOGIN%']");
        $this->assertEquals("%GREETING%UserNamešÄßüл UserSurnamešÄßüл %LOGOUT%", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]")));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT% - \"example_test@oxid-esales.dev\"", $this->getText("//h1"));
        $this->assertEquals("%MY_WISH_LIST%", $this->getText("//section[@id='content']//div[2]/dl[1]/dt"));
        $this->assertEquals("%PRODUCT%: 1", $this->getText("//section[@id='content']//div[2]/dl[1]/dd"));
        $this->assertEquals("%MY_GIFT_REGISTRY%", $this->getText("//section[@id='content']//div[2]/dl[2]/dt"));
        $this->assertEquals("%PRODUCT%: 1", $this->getText("//section[@id='content']//div[2]/dl[2]/dd"));
        $this->assertEquals("%MY_PRODUCT_COMPARISON%", $this->getText("//section[@id='content']//div[2]/dl[3]/dt"));
        $this->assertEquals("%PRODUCT%: 1", $this->getText("//section[@id='content']//div[2]/dl[3]/dd"));
    }

    /**
     * My account navigation: changing password\
     *
     * @group myAccount
     */
    public function testFrontendMyAccountPass()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");

        //changing password
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% - example_test@oxid-esales.dev", $this->getText("breadCrumb"));
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->clearString($this->getText("//ul[@id='topMenu']/li/a")));
        $this->clickAndWait("//aside[@id='sidebar']//li/a[text()='%CHANGE_PASSWORD%']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_PASSWORD%", $this->getText("//h1"));
        $this->assertFalse($this->isVisible("//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']"));
        $this->assertFalse($this->isVisible('//span[text()="%ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH%"]'));

        //entered diff new passwords
        $this->typeKeys("passwordOld", "useruser");
        $this->type("password_new", "user1user");
        $this->fireEvent("password_new", "blur");
        $this->assertFalse($this->isVisible("//section[@id='content']//li[2]//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']"));
        $this->type("password_new_confirm", "useruser");
        $this->fireEvent("password_new_confirm", "blur");
        $this->waitForItemAppear('//section[@id="content"]//li[3]//span[text()="%ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH%"]');

        //new pass is too short
        $this->clickAndWait("//aside[@id='sidebar']//li/a[text()='%CHANGE_PASSWORD%']");
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user");
        $this->fireEvent("password_new", "blur");
        $this->waitForItemAppear("//section[@id='content']//li[2]//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']");
        $this->type("password_new_confirm", "user");
        $this->fireEvent("password_new_confirm", "blur");
        $this->waitForItemAppear("//section[@id='content']//li[3]//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']");
        $this->assertFalse($this->isVisible('//section[@id="content"]//li[2]//span[text()="%ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH%"]'));

        //correct new pass
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user1user");
        $this->fireEvent("password_new", "blur");
        $this->type("password_new_confirm", "user1user");
        $this->fireEvent("password_new_confirm", "blur");
        $this->clickAndWait("savePass");
        $this->assertFalse($this->isVisible("//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']"));
        $this->assertFalse($this->isVisible('//span[text()="%ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH%"]'));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_PASSWORD%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_PASSWORD_CHANGED%");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
        $this->assertTextNotPresent("%ERROR_MESSAGE_USER_NOVALIDLOGIN%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser", false);
        $this->assertTextPresent("%ERROR_MESSAGE_USER_NOVALIDLOGIN%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "user1user");
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]/a[1]")));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->assertEquals("%CHANGE_PASSWORD%", $this->getText("//section[@id='content']//dl[1]/dt"));
        $this->clickAndWait("//section[@id='content']//dl[1]/dt/a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_PASSWORD%", $this->getText("//h1"));
    }

    /**
     * Right side box My account. Password reminding
     *
     * @group myAccount
     */
    public function testFrontendRightMyAccountRemindPass()
    {
        $this->openShop();
        //page for reminding pass
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->clickAndWait("//section[@id='content']//a[text()='%FORGOT_PASSWORD%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %FORGOT_PASSWORD%", $this->getText("breadCrumb"));
        $this->assertEquals("%FORGOT_PASSWORD%", $this->getText("//h1"));

        $this->assertTextPresent("%HAVE_YOU_FORGOTTEN_PASSWORD%");
        $this->assertEquals("", $this->getValue("forgotPasswordUserLoginName"));
        $this->type("forgotPasswordUserLoginName", "not_existing_user@oxid-esales.dev");
        $this->clickAndWait("//section[@id='content']//button[text()='%REQUEST_PASSWORD%']");
        $this->assertTextPresent("%ERROR_MESSAGE_PASSWORD_EMAIL_INVALID%");
        $this->assertTextNotPresent("%PASSWORD_WAS_SEND_TO%: not_existing_user@oxid-esales.dev");
        $this->assertEquals("%YOU_ARE_HERE%: / %FORGOT_PASSWORD%", $this->getText("breadCrumb"));
        $this->assertEquals("%FORGOT_PASSWORD%", $this->getText("//h1"));
        $this->assertEquals("not_existing_user@oxid-esales.dev", $this->getValue("forgotPasswordUserLoginName"));
        $this->type("forgotPasswordUserLoginName", "example_test@oxid-esales.dev");
        $this->clickAndWait("//section[@id='content']//button[text()='%REQUEST_PASSWORD%']");
        $this->assertTextNotPresent("%ERROR_MESSAGE_PASSWORD_EMAIL_INVALID%");
        $this->assertTextPresent("%PASSWORD_WAS_SEND_TO%: example_test@oxid-esales.dev");
        //pasword reminder opened via login popup
        $this->clickAndWait("link=%HOME%");
        $this->assertFalse($this->isVisible("forgotPassword"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("//a[@class='forgotPasswordOpener']");
        $this->click("//a[@class='forgotPasswordOpener']");
        $this->waitForItemAppear("forgotPassword");
        $this->assertTextPresent("%HAVE_YOU_FORGOTTEN_PASSWORD%");
        $this->assertEquals("", $this->getValue("forgotPasswordUserLoginNamePopup"));
        $this->type("forgotPasswordUserLoginNamePopup", "not_existing_user@oxid-esales.dev");
        $this->clickAndWait("//button[text()='%REQUEST_PASSWORD%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %FORGOT_PASSWORD%", $this->getText("breadCrumb"));
        $this->assertEquals("%FORGOT_PASSWORD%", $this->getText("//h1"));
        $this->assertTextPresent("%ERROR_MESSAGE_PASSWORD_EMAIL_INVALID%");
        $this->assertTextNotPresent("%PASSWORD_WAS_SEND_TO%: not_existing_user@oxid-esales.dev");
        $this->clickAndWait("link=%HOME%");
        $this->assertFalse($this->isVisible("forgotPassword"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("//a[@class='forgotPasswordOpener']");
        $this->click("//a[@class='forgotPasswordOpener']");
        $this->waitForItemAppear("forgotPassword");
        $this->assertTextPresent("%HAVE_YOU_FORGOTTEN_PASSWORD%");
        $this->type("forgotPasswordUserLoginNamePopup", "example_test@oxid-esales.dev");
        $this->clickAndWait("//button[text()='%REQUEST_PASSWORD%']");
        $this->assertTextNotPresent("%ERROR_MESSAGE_PASSWORD_EMAIL_INVALID%");
        $this->assertTextPresent("%PASSWORD_WAS_SEND_TO%: example_test@oxid-esales.dev");
    }


    /**
     * My account: newsletter settings
     *
     * @group myAccount
     */
    public function testFrontendMyAccountNewsletter()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");

        //newsletter settings
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%NEWSLETTER_SETTINGS%']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_NEWSLETTER%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION%");
        $this->assertTextNotPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION_SUCCESS%");
        $this->assertEquals("%NO%", $this->getSelectedLabel("status"));
        $this->select("status", "label=%YES%");
        $this->clickAndWait("newsletterSettingsSave");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_NEWSLETTER%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION_SUCCESS%");
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%NEWSLETTER_SETTINGS%']");
        $this->assertTextNotPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED%");
        $this->assertEquals("%YES%", $this->getSelectedLabel("status"));
        $this->select("status", "label=%NO%");
        $this->clickAndWait("newsletterSettingsSave");
        $this->assertTextPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED%");
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%NEWSLETTER_SETTINGS%']");
        $this->assertEquals("%NO%", $this->getSelectedLabel("status"));
    }

    /**
     * My account navigation: billing address
     *
     * @group myAccount
     */
    public function testFrontendMyAccountAddressBilling()
    {
        /** Change Germany and Belgium to non EU country to skip online VAT validation. */
        $this->callShopSC('oxCountry', 'save', 'a7c40f632e04633c9.47194042', array('oxcountry__oxvatstatus' => 0));
        $this->callShopSC('oxCountry', 'save', 'a7c40f631fc920687.20179984', array('oxcountry__oxvatstatus' => 0));

        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        //Addresses testing
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%BILLING_SHIPPING_SETTINGS%']");
        $this->assertEquals("%BILLING_SHIPPING_SETTINGS%", $this->getText("//h1"));
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxcountryid]");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertTrue($this->isVisible("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->assertEquals("%PLEASE_SELECT_STATE%", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));

        //changing billing address
        $this->select("invadr[oxuser__oxsal]", "label=%MRS%");
        $this->type("invadr[oxuser__oxfname]", "UserName1_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "UserSurname1_šÄßüл");
        $this->type("invadr[oxuser__oxcompany]", "UserCompany1_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "Musterstr.1_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "11");
        $this->type("invadr[oxuser__oxzip]", "790981");
        $this->type("invadr[oxuser__oxcity]", "Musterstadt1_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "BE0410521222");
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
        $this->assertElementNotPresent("//div[@class='errorbox inbox']");
        if ($this->isElementPresent("//li[@class='oxInValid']")) {
            // Let's repeat once more.
            $this->clickAndWait("accUserSaveTop");
        }

        $this->assertEquals("%BILLING_SHIPPING_SETTINGS%", $this->getText("//h1"));
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%BILLING_SHIPPING_SETTINGS%']");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxcountryid]");
        $this->assertEquals(
            "%MRS%",
            $this->getSelectedLabel("invadr[oxuser__oxsal]"),
            "Field was not updated. Maybe VAT id check failed?"
        );
        $this->assertEquals("UserName1_šÄßüл", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("UserSurname1_šÄßüл", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("UserCompany1_šÄßüл", $this->getValue("invadr[oxuser__oxcompany]"));
        $this->assertEquals("Musterstr.1_šÄßüл", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("11", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("790981", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("Musterstadt1_šÄßüл", $this->getValue("invadr[oxuser__oxcity]"));
        //$this->assertEquals("BE0876797054", $this->getValue("invadr[oxuser__oxustid]"));
        $this->assertEquals("BE0410521222", $this->getValue("invadr[oxuser__oxustid]"));
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
     * My account navigation: changing shipping address and pass
     *
     * @group myAccount
     */
    public function testFrontendMyAccountAddressShipping()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        //Addresses testing
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%BILLING_SHIPPING_SETTINGS%']");
        $this->assertEquals("%BILLING_SHIPPING_SETTINGS%", $this->getText("//h1"));

        //changing email
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invadr[oxuser__oxusername]");
        $this->type("invadr[oxuser__oxusername]", "example01@oxid-esales.dev");
        $this->keyUp("invadr[oxuser__oxusername]", "t");
        $this->assertTrue($this->isVisible("user_password"));
        $this->type("user_password", "useruser");
        $this->clickAndWait("accUserSaveTop");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser", false);
        $this->assertTextPresent("%ERROR_MESSAGE_USER_NOVALIDLOGIN%");
        $this->loginInFrontend("example01@oxid-esales.dev", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%BILLING_SHIPPING_SETTINGS%']");
        $this->assertEquals("%BILLING_SHIPPING_SETTINGS%", $this->getText("//h1"));

        //delivery address
        $this->assertEquals("on", $this->getValue("showShipAddress"));
        $this->click("showShipAddress");
        $this->waitForItemAppear("addressId");
        $this->select("addressId", "label=%NEW_ADDRESS%");
        $this->select("deladr[oxaddress__oxsal]", "label=%MRS%");
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
        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%BILLING_SHIPPING_SETTINGS%']");
        $this->assertEquals("Germany", $this->getSelectedLabel("deladr[oxaddress__oxcountryid]"));
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_deladr[oxaddress__oxstateid]"));
        $this->select("addressId", "label=%NEW_ADDRESS%");

        $this->select("addressId", "label=First name_šÄßüл Last name_šÄßüл, street_šÄßüл 1, city_šÄßüл");
        $this->waitForPageToLoad("30000");
        $this->select("deladr[oxaddress__oxsal]", "label=%MR%");
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

        $this->clickAndWait("//aside[@id='sidebar']/ul//a[text()='%BILLING_SHIPPING_SETTINGS%']");
        $this->assertEquals("off", $this->getValue("showShipAddress"));
        $this->assertEquals("%MR%", $this->getSelectedLabel("deladr[oxaddress__oxsal]"));
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
     * My account navigation: My Wish List
     *
     * @group myAccount
     */
    public function testFrontendMyAccountWishList()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->openArticle( 1003 );

        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("//h1")));
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");

        //wish list testing
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_WISH_LIST%1", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->clickAndWAit("//ul[@id='services']/li[5]/a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_NOTICELIST%", $this->getText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_WISH_LIST%", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//ul[@id='noticelistProductList']/li/form[@name='tobasket.noticelistProductList_1']/div[2]/div/a"));
        //$this->assertEquals("Art.No. 1003", $this->getText("//form[@name='tobasket.noticelistProductList_1']/div[2]/div/span"));
        $this->assertEquals("Test product 3 short desc [EN] šÄßüл", $this->getText("//form[@name='tobasket.noticelistProductList_1']/div[2]/div[2]"));
        $this->assertEquals("75,00 € *",$this->getText("productPrice_noticelistProductList_1"));
        $this->clickAndWait("//form[@name='tobasket.noticelistProductList_1']/div/a");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));
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
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_NOTICELIST%", $this->getText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_WISH_LIST%", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("noticelistProductList");
        $this->assertTextPresent("%WISH_LIST_EMPTY%");
    }

    /**
     * My account navigation: My Wish List for variants
     *
     * @group myAccount
     */
    public function testFrontendMyAccountWishListVariant()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->selectVariant("variantselector_searchList_3", 1, "var1 [EN] šÄßüл");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");
        $this->click("link=%RESET_SELECTION%");
        $this->waitForTextDisappear("%SELECTED_COMBINATION%");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");
        //wish list testing
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_WISH_LIST%2", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->clickAndWAit("//ul[@id='services']/li[5]/a");
        $this->assertEquals("%MY_WISH_LIST%", $this->getText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_WISH_LIST%", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//ul[@id='noticelistProductList']/li/form[@name='tobasket.noticelistProductList_1']/div[2]/div/a"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("//ul[@id='noticelistProductList']/li/form[@name='tobasket.noticelistProductList_2']/div[2]/div/a"));
        $this->clickAndWait("toBasket_noticelistProductList_2");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//ul[@id='noticelistProductList']/li/form[@name='tobasket.noticelistProductList_1']/div[2]/div/a"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("//ul[@id='noticelistProductList']/li/form[@name='tobasket.noticelistProductList_2']/div[2]/div/a"));
        $this->clickAndWait("//button[@triggerform='remove_tonoticelistnoticelistProductList_2']");
        $this->clickAndWait("//button[@triggerform='remove_tonoticelistnoticelistProductList_1']");
        $this->assertEquals("%MY_WISH_LIST%", $this->getText("//h1"));
        $this->assertElementNotPresent("noticelistProductList");
        $this->assertTextPresent("%WISH_LIST_EMPTY%");
    }

    /**
     * My account navigation: My Gift Registry. setting gift registry as searchable
     *
     * @group myAccount
     */
    public function testFrontendGiftRegistrySearchable()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //gift registry
        $this->openArticle( 1000 );
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_GIFT_REGISTRY%1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_GIFT_REGISTRY%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_WISHLIST%", $this->getText("//h1"));

        //making gift registry not searchable
        $this->assertTextPresent("%MESSAGE_MAKE_GIFT_REGISTRY_PUBLISH%:");
        $this->assertEquals("%YES%", $this->getSelectedLabel("blpublic"));
        $this->select("blpublic", "label=%NO%");
        $this->clickAndWait("//form[@name='wishlist_wishlist_status']//button");

        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "example_test@oxid-esales.dev");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertTextPresent("%MESSAGE_SORRY_NO_GIFT_REGISTRY%");
        $this->assertEquals("%NO%", $this->getSelectedLabel("blpublic"));

        //making gift registry searchable
        $this->select("blpublic", "label=%YES%");
        $this->clickAndWait("//form[@name='wishlist_wishlist_status']//button");

        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "example_test@oxid-esales.dev");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertElementPresent("link=%GIFT_REGISTRY_OF% UserNamešÄßüл UserSurnamešÄßüл");

        $this->clickAndWait("link=%GIFT_REGISTRY_OF% UserNamešÄßüл UserSurnamešÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / %PUBLIC_GIFT_REGISTRIES%", $this->getText("breadCrumb"));
        $this->assertEquals("%GIFT_REGISTRY_OF_3% UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("wishlistProductList_1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->assertTextPresent("%MESSAGE_SEND_GIFT_REGISTRY%");

        $this->clickAndWait("link=%MESSAGE_SEND_GIFT_REGISTRY%");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_GIFT_REGISTRY%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//label[text()='%SEND_GIFT_REGISTRY%']");
        $this->type("editval[rec_email]", "example@oxid-esales.dev");
        $this->type("editval[rec_name]", "recipient");
        $this->type("editval[send_message]", "Hi, I created a Gift Registry at OXID.");
        $this->clickAndWait("//button[text()='%SUBMIT%']");
        $this->assertTextPresent("Your gift registry was sent successfully to example@oxid-esales.dev");
        $this->assertEquals("recipient", $this->getValue("editval[rec_name]"));
        $this->assertEquals("example@oxid-esales.dev", $this->getValue("editval[rec_email]"));
        $this->assertEquals("Hi, I created a Gift Registry at OXID.", $this->getValue("editval[send_message]"));
    }

    /**
     * My account navigation: My Gift Registry
     *
     * @group myAccount
     */
    public function testFrontendMyAccountGiftRegistry()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //gift registry
        $this->openArticle( 1000 );
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_GIFT_REGISTRY%1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_GIFT_REGISTRY%", $this->getText("breadCrumb"));

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
        $this->assertTextPresent("%GIFT_REGISTRY_EMPTY%");

    }

    /**
     * My account navigation: My Gift Registry
     *
     * @group myAccount
     */
    public function testFrontendSearchForGiftRegistry()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //creating gift registry
        $this->openArticle( 1000 );
        $this->click("productLinks");
        $this->waitForItemAppear("linkToWishList");
        $this->clickAndWait("linkToWishList");

        //logging in as other user for searching this gift registry
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
        $this->loginInFrontend("admin@myoxideshop.com", "admin0303");

        //search for gift registry
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->clickAndWait("link=%MY_GIFT_REGISTRY%");
        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "example_test@oxid-esales.dev");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertElementPresent("link=%GIFT_REGISTRY_OF% UserNamešÄßüл UserSurnamešÄßüл");

        $this->clickAndWait("link=%GIFT_REGISTRY_OF% UserNamešÄßüл UserSurnamešÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / %PUBLIC_GIFT_REGISTRIES%", $this->getText("breadCrumb"));
        $this->assertEquals("%GIFT_REGISTRY_OF_3% UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("wishlistProductList_1"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_wishlistProductList_1"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("//form[@name='tobasket.wishlistProductList_1']/div[2]/div[2]"));
        $this->assertEquals("50,00 € *", $this->getText("productPrice_wishlistProductList_1"));

        $this->clickAndWait("//form[@name='tobasket.wishlistProductList_1']//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));

        $this->clickAndWait("link=%PUBLIC_GIFT_REGISTRIES%");
        $this->assertEquals("%GIFT_REGISTRY_OF_3% UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//form[@name='tobasket.wishlistProductList_1']/div[2]//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->clickAndWait("link=%PUBLIC_GIFT_REGISTRIES%");
        $this->assertEquals("%GIFT_REGISTRY_OF_3% UserNamešÄßüл UserSurnamešÄßüл", $this->getText("//h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("wishlistProductList_1"));
        $this->type("amountToBasket_wishlistProductList_1", "2");
        $this->clickAndWait("toBasket_wishlistProductList_1");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        //deleting gift registry
        $this->clickAndWait("link=%LOGOUT%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[6]/a");
        $this->clickAndWait("//button[@triggerform='remove_towishlistwishlistProductList_1']");
        $this->assertTextPresent("%GIFT_REGISTRY_EMPTY%");

        //searching for gift registry again. now gift registry wil not be found, couse its empty
        $this->clickAndWait("link=%LOGOUT%");
        $this->loginInFrontend("admin@myoxideshop.com", "admin0303");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->clickAndWait("link=%MY_GIFT_REGISTRY%");
        $this->type("//form[@name='wishlist_searchbox']/ul//input[@name='search']", "example_test@oxid-esales.dev");
        $this->clickAndWait("//form[@name='wishlist_searchbox']/ul//button");
        $this->assertTextPresent("%MESSAGE_SORRY_NO_GIFT_REGISTRY%");
    }

    /**
     * Gift Registry is disabled via performance options
     *
     * @group myAccount
     */
    public function testFrontendDisabledGiftRegistry()
    {
        //(Use gift registry) is disabled
        $this->callShopSC("oxConfig", null, null, array("bl_showWishlist" => array("type" => "bool", "value" => "false",  "module" => "theme:azure")));
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->openArticle( 1000 );
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");

        $this->assertElementNotPresent("linkToWishList");
        $this->assertElementPresent("linkToNoticeList");
        $this->assertElementPresent("recommList");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->assertElementPresent("//aside[@id='sidebar']//a[text()='%MY_WISH_LIST%']");
        $this->assertElementNotPresent("//aside[@id='sidebar']//a[text()='%MY_GIFT_REGISTRY%']");
        $this->assertElementPresent("//aside[@id='sidebar']//a[text()='%MY_LISTMANIA%']");
    }

    /**
     * My account navigation: Product Comparison
     *
     * @group myAccount
     */
    public function testFrontendMyAccountCompare()
    {
        $this->openArticle( 1000, true );
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");

        $this->openArticle( 1001 );
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");

        $this->openArticle( 1002 );
        $this->click("productLinks");
        $this->waitForItemAppear("addToCompare");
        $this->clickAndWait("addToCompare");

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_PRODUCT_COMPARISON%3", $this->clearString($this->getText("//ul[@id='services']/li[4]")));
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %PRODUCT_COMPARISON%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_COMPARE%", $this->getText("//h1"));

        $this->assertElementPresent("compareRight_1000");
        $this->assertElementPresent("compareRight_1001");
        $this->assertElementPresent("compareLeft_1002");

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[1]/div[2]/strong")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[2]/div[2]/strong")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='firstTr']/td[3]/div[2]/strong")));
        $this->assertEquals("Weight: 2 kg %PRODUCT_NO%: 1000", $this->clearString($this->getText("//tr[@id='firstTr']/td[1]/div[2]/span")));
        $this->assertEquals("%PRODUCT_NO%: 1001", $this->getText("//tr[@id='firstTr']/td[2]/div[2]/span"));
        $this->assertEquals("%PRODUCT_NO%: 1002", $this->getText("//tr[@id='firstTr']/td[3]/div[2]/span"));

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
        $this->assertEquals("%PAGE_TITLE_COMPARE%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_SELECT_MORE_PRODUCTS%");
    }

    /**
     * creating listmania
     *
     * @group myAccount
     */
    public function testListmaniaCreating()
    {
        //deleting existing recommlists for better possibility to test creating of new recomlist
        $aRecommListParams = array("OXTITLE" => 'Kite-Equipment');
        $this->callShopSC("oxRecommList", "delete", "e7a0b1906e0d94e05693f06b0b6fcc32", $aRecommListParams);
        $aRecommListParams = array("OXTITLE" => 'recomm title');
        $this->callShopSC("oxRecommList", "delete", "testrecomm", $aRecommListParams);
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->openArticle( 1000 );
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");

        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertTextPresent("%NO_LISTMANIA_LIST%click here.");
        $this->clickAndWait("//section[@id='content']/a[text()='%CLICK_HERE%']");
        //creating listmania in MyAccount
        $this->assertEquals("%MY_LISTMANIA%", $this->getText("//aside[@id='sidebar']/ul/li[8]"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));
        $this->assertEquals("", $this->getValue("recomm_title"));
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->getValue("recomm_author"));
        $this->assertEquals("", $this->getValue("recomm_desc"));
        $this->assertTextPresent("%NO_LISTMANIA_LIST_FOUND%");
        $this->type("recomm_title", "recomm title1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_desc", "recom introduction1");
        $this->clickAndWait("//section[@id='content']//button[text()='%SAVE%']");
        $this->assertTextPresent("%LISTMANIA_LIST_SAVED%");
        $this->clickAndWait("//aside[@id='sidebar']//a[text()='%MY_LISTMANIA%']");
        $this->assertEquals("recomm title1", $this->getText("//section[@id='content']//ul[@id='recommendationsLists']/li[1]/div/div/a"));
        $this->assertElementPresent("//section[@id='content']//ul[@id='recommendationsLists']/li[1]//button[@name='deleteList']");
        $this->assertElementPresent("//section[@id='content']//ul[@id='recommendationsLists']/li[1]//button[@name='editList']");
        $this->assertEquals("recom introduction1", $this->getText("//ul[@id='recommendationsLists']/li[1]/div/div[2]"));
        $this->assertTextPresent("recomm title1 : %LIST_BY% recomm author1");
        $this->clickAndWait("link=recomm title1");

        $this->assertElementPresent("breadCrumb");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getHeadingText("//h1"));

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[2]/a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT% - \"example_test@oxid-esales.dev\"", $this->getText("//h1"));
        $this->assertEquals("%MY_LISTMANIA%", $this->getText("//section[@id='content']//div[2]/dl[4]/dt"));
        $this->assertEquals("%LISTS%: 1", $this->getText("//section[@id='content']//div[2]/dl[4]/dd"));
    }

    /**
     * Checking Listmania
     *
     * @group myAccount
     */
    public function testFrontendListmaniaInfo()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }

        $aWrappingParams = array("OXTYPE" => 'oxrecommlist');
        $this->callShopSC("oxReview", "delete", $sOxid = "testrecomreview", $aWrappingParams, null, 1);
        $aRatingParams = array("OXTYPE" => 'oxrecommlist');
        $this->callShopSC("oxRating", "delete", $sOxid = "testrecomrating", $aRatingParams, null, 1);
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //checking small listmania box
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");

        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//article[@id='recommendationsBox']/h3"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->clearString($this->getHeadingText("//article[@id='recommendationsBox']/h3")));
        $this->clickAndWait("//article[@id='recommendationsBox']//ul/li[1]/a");
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->switchLanguage('Deutsch');
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->switchLanguage('English');
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//article[@id='recommendationsBox']/h3"));
        $this->clickAndWait("//article[@id='recommendationsBox']/a/img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->clearString($this->getHeadingText("//article[@id='recommendationsBox']/h3")));
        $this->assertEquals("recomm title", $this->getText("//article[@id='recommendationsBox']//ul/li[1]/a"));
        $this->assertEquals("%LIST_BY%: recomm author", $this->getText("//article[@id='recommendationsBox']//ul/li[1]/div"));

        //writing recommendation for listmania
        $this->clickAndWait("//article[@id='recommendationsBox']//ul/li[1]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//a[@id='rssRecommListProducts']");
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->assertTextPresent("recom introduction");
        $this->assertTextPresent("recom introduction");
        $this->assertTextPresent("%NO_REVIEW_AVAILABLE%");
        $this->assertEquals("%NO_RATINGS%", $this->getText("itemRatingText"));
        $this->click("writeNewReview");
        $this->click("//ul[@id='reviewRating']/li[@class='s3']/a");
        $this->type("rvw_txt", "recommendation for this list");
        $this->clickAndWait("reviewSave");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("UserNamešÄßüл writes: ".date("d.m.Y"), $this->clearString($this->getText("reviewName_1")));
        $this->assertEquals("recommendation for this list", $this->getText("reviewText_1"));
        $this->assertEquals("(1)", $this->getText("itemRatingText"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_productList_1"));
        $this->assertElementPresent("//form[@name='tobasket.productList_1']//div[text()='comment for product 1000']");
        $this->assertEquals("50,00 € *", $this->getText("productPrice_productList_1"));
        $this->clickAndWait("//ul[@id='productList']/li[1]//a");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=recomm title");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//form[@name='tobasket.productList_1']/div[2]//a"));
        $this->clickAndWait("//ul[@id='productList']/li[1]/form/div[2]//a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=recomm title");

        $this->type("amountToBasket_productList_1", "2");
        $this->clickAndWait("toBasket_productList_1");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//form[@name='tobasket.productList_1']/div[2]//a"));
    }

    /**
     * Checking Listmania
     *
     * @group myAccount
     */
    public function testFrontendListmaniaAddSearch()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //adding other products to listmania

        $this->openArticle(1001);
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1001");
        $this->clickAndWait("//button[text()='%ADD_TO_LISTMANIA_LIST%']");

        $this->openArticle(1002);
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1002");
        $this->clickAndWait("//button[text()='%ADD_TO_LISTMANIA_LIST%']");

        //search in listmania
        $this->type("searchRecomm", "title");
        $this->clickAndWait("//article[@id='recommendationsBox']//button");
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->assertEquals("1 %HITS_FOR% \"title\"", $this->getText("//h1"));
        $this->assertEquals("recomm title", $this->getText("//ul[@id='recommendationsLists']/li[1]//a"));
        $this->assertEquals("recom introduction", $this->getText("//ul[@id='recommendationsLists']/li[1]//div[2]"));
        $this->assertTextPresent("recomm title : %LIST_BY% recomm author");
        $this->clickAndWait("link=recomm title");
        $this->assertEquals("recomm title (%LIST_BY% recomm author)", $this->getHeadingText("//h1"));
        $this->assertTextPresent("recom introduction");
        $this->assertEquals("%WRITE_REVIEW%", $this->getText("writeNewReview"));
        $this->assertEquals("recommendation for this list", $this->getText("reviewText_1"));

        $expected = array("Test product 0 [EN] šÄßüл",
                          "Test product 1 [EN] šÄßüл",
                          "Test product 2 [EN] šÄßüл");

        $check = array($this->getText("productList_1"),
                       $this->getText("productList_2"),
                       $this->getText("productList_3"));
        sort($check);

        $this->assertEquals($expected, $check);
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->type("searchRecomm", "no entry");
        $this->clickAndWait("//article[@id='recommendationsBox']//button");
        $this->assertEquals("no entry", $this->getValue("searchRecomm"));
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("breadCrumb"));
        $this->assertEquals("0 %HITS_FOR% \"no entry\"", $this->getText("//h1"));
        $this->assertTextPresent("%NO_LISTMANIA_LIST_FOUND%");

        //editing listmania (with articles)
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[2]/a");
        $this->clickAndWait("link=%MY_LISTMANIA%");
        $this->clickAndWait("//ul[@id='recommendationsLists']/li[1]//button[text()='%EDIT%']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));
        $this->assertEquals("recomm title", $this->getValue("recomm_title"));
        $this->assertEquals("recomm author", $this->getValue("recomm_author"));
        $this->assertEquals("recom introduction", $this->getValue("recomm_desc"));
        $this->type("recomm_desc", "recom introduction1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_title", "recomm title1");
        $this->clickAndWait("//form[@name='saverecommlist']//button[text()='%SAVE%']");
        $this->assertTextPresent("%LISTMANIA_LIST_SAVED%");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));

    }

    /**
     * Checking Listmania
     *
     * @group myAccount
     */
    public function testFrontendListmaniaDelete()
    {
        if ( isSUBSHOP ) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->openArticle(1001);

        //adding other products to listmania
        $this->waitForItemDisappear("recommList");
        $this->click("productLinks");
        $this->waitForItemAppear("recommList");
        $this->clickAndWait("recommList");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->select("recomm", "label=recomm title");
        $this->type("recomm_txt", "comment for product 1001");
        $this->clickAndWait("//button[text()='%ADD_TO_LISTMANIA_LIST%']");

        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[2]/a");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT% - \"example_test@oxid-esales.dev\"", $this->getText("//h1"));
        $this->clickAndWait("//section[@id='content']//div[2]/dl[4]/dt/a");
        $this->clickAndWait("//section[@id='content']//ul/li[1]//button[@name='editList']");

        $first = $this->getText("recommendProductList_1");
        $check = 2;
        if (false !== strpos($first, 'product 1 [EN]')) {
            $check = 1;
        }
        $expected = "selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%";
        $this->assertEquals($expected, $this->clearString($this->getText("//div[@id='selectlistsselector_recommendProductList_{$check}']//ul")));

        //removing articles from list
        $this->clickAndWait("//button[@triggerform='remove_removeArticlerecommendProductList_2']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_RECOMMLIST%", $this->getText("//h1"));
        $this->assertEquals($first, $this->getText("recommendProductList_1"));
        $this->assertElementNotPresent("recommendProductList_2");
        $this->clickAndWait("//aside[@id='sidebar']//a[text()='%MY_LISTMANIA%']");
        $this->assertEquals("recomm title", $this->getText("//ul[@id='recommendationsLists']/li[1]//a"));
        $this->assertTextPresent("recomm title : %LIST_BY% recomm author");
        $this->assertTextPresent("recom introduction");

        //deleting recom list
        $this->clickAndWait("//ul[@id='recommendationsLists']/li[1]//button[@name='deleteList']");
        $this->assertTextPresent("%NO_LISTMANIA_LIST_FOUND%");
    }

    /**
     * Product details. test for checking main product details available only for logged in user
     *
     * @group myAccount
     */
    public function testFrontendDetailsForLoggedInUsers()
    {
        if (isSUBSHOP) {
            $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
            $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        }
        $this->openArticle(1001, true);

        //review
        $this->assertEquals("%MESSAGE_LOGIN_TO_WRITE_REVIEW%", $this->clearString($this->getText("reviewsLogin")));
        $this->clickAndWait("reviewsLogin");
        $this->assertEquals("%YOU_ARE_HERE%: / %LOGIN%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT%", $this->getText("//h1"));
        $this->type("//section[@id='content']//form[@name='login']//input[@name='lgn_usr']", "example_test@oxid-esales.dev");
        $this->type("//section[@id='content']//form[@name='login']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//section[@id='content']//form[@name='login']//button");
        $this->assertFalse($this->isVisible("rvw_txt"));
        $this->click("writeNewReview");
        $this->waitForItemAppear("rvw_txt");
        //selecting rating near product img
        $this->click("//ul[@id='reviewRating']/li[5]/a");
        $this->assertEquals("4", $this->getValue("artrating"));
        $this->click("//ul[@id='reviewRating']/li[4]/a");
        $this->assertEquals("3", $this->getValue("artrating"));
        $this->type("rvw_txt", "user review [EN] šÄßüл for product 1001");
        $this->clickAndWait("reviewSave");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals(0, strpos($this->getText("reviewName_1"), "UserNamešÄßüл"));
        $this->assertEquals("user review [EN] šÄßüл for product 1001", $this->getText("reviewText_1"));
        $this->assertEquals("(1)", $this->getText("itemRatingText"));

        //wish list and gift registry
        $this->assertElementPresent("//p[@id='servicesTrigger']/span");
        $this->click("productLinks");
        $this->waitForItemAppear("linkToNoticeList");
        $this->clickAndWait("linkToNoticeList");
        $this->assertEquals("2", $this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        if (!$this->isElementPresent('linkToWishList')) {
            $this->click("productLinks");
            $this->waitForItemAppear("linkToWishList");
        }
        $this->clickAndWait("linkToWishList");
        $this->assertEquals("3", $this->clearString($this->getText("//p[@id='servicesTrigger']/span")));
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_WISH_LIST%1", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->assertEquals("%MY_GIFT_REGISTRY%1", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
        $this->clickAndWait("//ul[@id='services']/li[5]/a");
        $this->clickAndWait("//button[@triggerform='remove_tonoticelistnoticelistProductList_1']");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_WISH_LIST%", $this->clearString($this->getText("//ul[@id='services']/li[5]")));
        $this->clickAndWait("//ul[@id='services']/li[6]/a");
        $this->clickAndWait("//button[@triggerform='remove_towishlistwishlistProductList_1']");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->assertEquals("%MY_GIFT_REGISTRY%", $this->clearString($this->getText("//ul[@id='services']/li[6]")));
    }

}
