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

class Acceptance_openIdTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

// ------------------------ tests that were used for openId. ------------------------------------------
// -------------- this functionality will be implemented later as separate module ---------------------
// ----------------------------------------------------------------------------------------------------

    /**
     * Open Id is disabled via performance options
     * @group navigation
     */
    public function testFrontendDisabledOpenId()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showOpenId'");
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_RightSideOpenIdHeader"));
        $this->assertFalse($this->isElementPresent("test_RightLogin_OpenId"));
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertFalse($this->isElementPresent("test_RightSideOpenIdHeader"));
        $this->assertFalse($this->isElementPresent("test_RightLogin_OpenId"));
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertFalse($this->isElementPresent("test_RightSideOpenIdHeader"));
        $this->assertFalse($this->isElementPresent("test_RightLogin_OpenId"));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertFalse($this->isElementPresent("test_RightSideOpenIdHeader"));
        $this->assertFalse($this->isElementPresent("test_RightLogin_OpenId"));
        $this->assertFalse($this->isElementPresent("test_UsrOpt2_openid"));
    }

    /**
     * Open id functionality in frontend
     * @group navigation
     */
    public function testFrontendOpenId()
    {
        $this->openShop();
        $this->assertFalse($this->isVisible("openId"));
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->assertFalse($this->isVisible("openId"));
        $this->assertTrue($this->isVisible("openidTrigger"));
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        //closing with X button
        $this->click("//div[@id='openId']/img");
        $this->waitForItemDisappear("openId");
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        //closing with Cancel button
        $this->click("//div[@id='openId']/button[text()='Cancel']");
        $this->waitForItemDisappear("openId");
        //login with correct id
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "http://birute01.myopenid.com/");
        $this->clickAndWait("//div[@id='openId']/button[text()='Login']");
        $this->assertTrue($this->isTextPresent("You must sign in to"));
        $this->type("password", "test_birute01");
        $this->uncheck("stay_signed_in");
        $this->clickAndWait("signin_button");
        if ($this->isElementPresent("continue-button")) {
            $this->clickAndWait("continue-button");
        }
        $this->assertEquals("Hello, Tested Persona Logout", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]")));
        $this->assertTrue($this->isElementPresent("//h2/span[text()='Just arrived!']"));
        //disabling openId (core settings -> theme)
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showOpenId'");
        $this->openShop();
        $this->assertFalse($this->isElementPresent("openId"));
        $this->assertFalse($this->isVisible("loginBox"));
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->assertFalse($this->isElementPresent("openId"));
        $this->assertFalse($this->isElementPresent("openidTrigger"));
    }

    /**
     * simple login with openId
     * @group user
     *
     */
    public function testOpenIdSimpleLogin()
    {
        if (version_compare(phpversion(), '5.2.0', '==')) {
            $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
            $this->markTestSkipped("can not run on current php520 - curl lines are commented in src, so can not fopen(http://....)");
        }
        $this->openShop();
        //worng openId
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "wrongName");
        $this->clickAndWait("//div[@id='openId']//button[text()='Login']");
        $this->assertTrue($this->isTextPresent("Please enter a valid OpenID"));

        //correct openId
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "http://birute01.myopenid.com/ ");
        $this->clickAndWait("//div[@id='openId']//button[text()='Login']");

        $this->waitForText("You must sign in to");
        $this->type("password", "test_birute01");
        $this->uncheck("stay_signed_in");
        $this->clickAndWait("signin_button");
        if ($this->isElementPresent("continue-button")) {
            $this->clickAndWait("continue-button");
        }
        $this->waitForElement("link=Tested Persona");
        $this->clickAndWait("link=Account");
        $this->clickAndWait("link=Billings and Shipping Settings");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("invadr[oxuser__oxusername]"));
        $this->assertEquals("Mr", $this->getSelectedLabel("invadr[oxuser__oxsal]"));
        $this->assertEquals("Tested", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("Persona", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("3000", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("Austria", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
    }

    /**
     * login with openId. after that add a normal pass to this acc and check, if it is possible
     * to loging both ways: via openId and via normal login to eShop
     * @group user
     *
     */
    public function testAddingPasswordToOpenIdAcc()
    {
        if (version_compare(phpversion(), '5.2.0', '==')) {
            $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
            $this->markTestSkipped("can not run on current php520 - curl lines are commented in src, so can not fopen(http://....)");
        }
        $this->openShop();

        //TODO: login with open id is gone from order step2. why?
/*
        //login with openID
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_BasketNextStepTop");
*/
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "http://birute01.myopenid.com/");
        $this->clickAndWait("//div[@id='openId']//button[text()='Login']");

        $this->waitForText("You must sign in to");
        $this->type("password", "test_birute01");
        $this->uncheck("stay_signed_in");
        $this->clickAndWait("signin_button");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->clickAndWait("link=Billings and Shipping Settings");
        $this->assertEquals("Tested", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("Persona", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("3000", $this->getValue("invadr[oxuser__oxzip]"));
        $this->clickAndWait("link=Logout");

        //set password to created openId user
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxusername]", "birute01");
        $this->clickAndWait("submitit");
        $this->openTab("link=birute01@nfq.lt");
        $this->type("newPassword", "111111_šÄßüл");
        $this->clickAndWaitFrame("save", "list");

        //login with pass as normal user
        $this->openShop();
        $this->loginInFrontend("birute01@nfq.lt", "111111_šÄßüл");
        $this->assertTrue($this->isElementPresent("link=Tested Persona"));
        $this->clickAndWait("link=Logout");
        $this->assertFalse($this->isElementPresent("link=Tested Persona"));
        //login with openId
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "http://birute01.myopenid.com/");
        $this->clickAndWait("//div[@id='openId']//button[text()='Login']");
        $this->assertTrue($this->isElementPresent("link=Tested Persona"));
    }

    /**
     * simple login with openId when such user already exist in eshop
     * @group user
     *
     */
    public function testOpenIdForExistingAcc()
    {
        if (version_compare(phpversion(), '5.2.0', '==')) {
            $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
            $this->markTestSkipped("can not run on current php520 - curl lines are commented in src, so can not fopen(http://....)");
        }
        $this->openShop();
        //creating normal acc
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Register']");
        $this->assertEquals("You are here: / Register", $this->getText("breadCrumb"));
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->check("//input[@name='blnewssubscribed' and @value='1']");
        $this->select("invadr[oxuser__oxsal]", "label=Mrs");
        $this->type("invadr[oxuser__oxfname]", "Name");
        $this->type("invadr[oxuser__oxlname]", "Surname");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "20");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->clickAndWait("accUserSaveTop");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));
        $this->assertEquals("Name Surname", $this->getText("//ul[@id='topMenu']/li/a"));
        $this->clickAndWait("link=Logout");
        $this->assertFalse($this->isElementPresent("link=Name Surname"));

        //login using openId with already existed email as an account
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "http://birute01.myopenid.com/");
        $this->clickAndWait("//div[@id='openId']//button[text()='Login']");

        if ($this->isElementPresent("signin_button")) {
            $this->waitForText("You must sign in to");
            $this->type("password", "test_birute01");
            $this->uncheck("stay_signed_in");
            $this->clickAndWait("signin_button");
        }
        if ($this->isElementPresent("continue-button")) {
            $this->clickAndWait("continue-button");
        }
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->clickAndWait("link=Billings and Shipping Settings");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("invadr[oxuser__oxusername]"));
        $this->assertEquals("Name", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("Surname", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("street", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("10", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("20", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("city", $this->getValue("invadr[oxuser__oxcity]"));
        $this->clickAndWait("link=Logout");
        //login with normal acc again
        $this->loginInFrontend("birute01@nfq.lt", "111111");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("invadr[oxuser__oxusername]"));
        $this->assertEquals("Name", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("Surname", $this->getValue("invadr[oxuser__oxlname]"));
    }

    /**
     * request pass info for openId user
     * @group user
     *
     */
    public function testOpenIdRequestPass()
    {
        if (version_compare(phpversion(), '5.2.0', '==')) {
            $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
            $this->markTestSkipped("can not run on current php520 - curl lines are commented in src, so can not fopen(http://....)");
        }
        $this->openShop();
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->click("openidTrigger");
        $this->waitForItemAppear("openId");
        $this->type("lgn_openid", "http://birute01.myopenid.com/");
        $this->clickAndWait("//div[@id='openId']//button[text()='Login']");

        if ($this->isElementPresent("signin_button")) {
            $this->waitForText("You must sign in to");
            $this->type("password", "test_birute01");
            $this->uncheck("stay_signed_in");
            $this->clickAndWait("signin_button");
        }
        if ($this->isElementPresent("continue-button")) {
            $this->clickAndWait("continue-button");
        }
        $this->assertTrue($this->isElementPresent("link=Tested Persona"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->clickAndWait("linkAccountPassword");
        $this->clickAndWait("loginLostPwd");
        $this->assertEquals("Forgot Password ?", $this->getText("//h1"));
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->clickAndWait("//button[text()='Request Password']");
        $this->assertEquals("You are here: / Forgot Password ?", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Password was sent to: birute01@nfq.lt"));
    }

}