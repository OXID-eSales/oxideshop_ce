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

class Acceptance_privateSalesTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Private sales related tests ----------------------------------

    /**
     * Basket exclusion: situation 1
     * @group navigation
     * @group user
     * @group privateSales
     */
    public function testBasketExclusionCase1()
    {
        //basket exclusion is off
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->assertEquals("You are here: / Kiteboarding / Kites", $this->getText("breadCrumb"));

        //enabling basket exclusion
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Disable", $this->getSelectedLabel("confstrs[blBasketExcludeEnabled]"));
        $this->select("confstrs[blBasketExcludeEnabled]", "label=Enable");
        $this->clickAndWait("save");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Enable", $this->getSelectedLabel("confstrs[blBasketExcludeEnabled]"));

        //checking in frontend
        $this->openShop();
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->assertFalse($this->isElementPresent("scRootCatChanged"));
        $this->assertFalse($this->isTextPresent("Root category changed"));
        $this->clickAndWait("link=Kiteboarding");
        $this->assertTrue($this->isElementPresent("scRootCatChanged"));
        $this->assertTrue($this->isTextPresent("Root category changed"));
        $this->assertTrue($this->isElementPresent("tobasket"));
        $this->assertTrue($this->isElementPresent("//button[text()='Continue Shopping']"));
        $this->clickAndWait("tobasket");
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']"));
        $this->clickAndWait("link=Home");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("scRootCatChanged"));
        $this->clickAndWait("moreSubCat_1");
        $this->assertFalse($this->isElementPresent("scRootCatChanged"));
        $this->clickAndWait("//form[@name='tobasketproductList_1']//button");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("link=Kiteboarding");
        $this->assertTrue($this->isElementPresent("scRootCatChanged"));
        $this->assertTrue($this->isTextPresent("Root category changed"));
        $this->clickAndWait("//button[text()='Continue Shopping']");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait("//ul[@id='productList']/li[1]//button");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("scRootCatChanged"));
        $this->assertTrue($this->isTextPresent("Root category changed"));
        $this->assertTrue($this->isElementPresent("tobasket"));
        $this->assertTrue($this->isElementPresent("//button[text()='Continue Shopping']"));

    }

   /**
     * Basket exclusion: situation 2
     * @group navigation
     * @group user
    * @group privateSales
     */
    public function testBasketExclusionCase2()
    {
        if (isSUBSHOP) {
            $this->executeSql("INSERT INTO `oxconfig` ( OXID, OXSHOPID, OXVARNAME, OXVARTYPE, OXVARVALUE ) VALUES ( MD5('blBasketExcludeEnabled'), 2, 'blBasketExcludeEnabled', 'bool', '')" );
        }

        //enabling basket exclusion
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blBasketExcludeEnabled" => array("type" => "bool", "value" => 'true')));
        //checking in frontend
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->clickAndWait("link=Test category 1 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("scRootCatChanged"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));

        $this->clickAndWait("link=Kiteboarding");
        $this->assertTrue($this->isElementPresent("scRootCatChanged"));
        $this->clickAndWait("tobasket");
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));

        $this->click("checkAll");
        $this->clickAndWait("basketRemove");

        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."));
        $this->clickAndWait("link=Home");
        $this->clickAndWait("link=Kiteboarding");
        $this->assertFalse($this->isTextPresent("Root category changed"));
        $this->assertFalse($this->isElementPresent("scRootCatChanged"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertFalse($this->isTextPresent("Root category changed"));
        $this->assertFalse($this->isElementPresent("scRootCatChanged"));

    }

    /**
     * Private sales: basket expiration
     * @group navigation
     * @group user
     * @group privateSales
     */
    public function testPrivateShoppingBasketExpiration()
    {
        //products are offline, if bought out
        $this->callShopSC("oxArticle", "save", "1000", array("oxstock" => 2, "oxstockflag" => 2));

        //enabling functionality
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Disable", $this->getSelectedLabel("basketreserved"));
        $this->assertFalse($this->isVisible("confstrs[iPsBasketReservationTimeout]"));
        $this->select("basketreserved", "label=Enable");
        $this->waitForItemAppear("confstrs[iPsBasketReservationTimeout]");
        $this->type("confstrs[iPsBasketReservationTimeout]", "20");
        $this->clickAndWait("save");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Enable", $this->getSelectedLabel("basketreserved"));
        $this->assertEquals("20", $this->getValue("confstrs[iPsBasketReservationTimeout]"));

        //checking in frontend
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//ul[@id='newItems']//input[@name='aid' and @value='1000']"));
        $this->assertTrue($this->isElementPresent("priceBargain_1"));
        $this->searchFor("1000");
        $this->assertEquals("1 Hits for \"1000\"", $this->getHeadingText("//h1"));
        $this->assertFalse($this->isTextPresent("Expires in:"));

        $this->selectDropDown("viewOptions", "Line");
        //adding product to basket
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertTrue($this->isTextPresent("Expires in:"));
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertTrue($this->isTextPresent("Expires in:"));

        //checking if product is reserved
        $this->searchFor("1000");
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        $this->assertTrue($this->isTextPresent("You are here: / Search"));
        sleep(21); //waiting till basket will expire
        $this->assertFalse($this->isElementPresent("basketFlyout"), "expired products are still visible in basket popup...");
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));

        $this->searchFor("1000");
        $this->assertFalse($this->isElementPresent("//div[@id='miniBasket']/span"));
        $this->assertFalse($this->isTextPresent("Expires in:"));
        $this->assertTrue($this->isTextPresent("1 Hits for \"1000\""));
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        //adding to basket again and finishing order
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li//button"));
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_usr']", "birute_test@nfq.lt");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='optionLogin']//button");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->assertTrue($this->isElementPresent("orderConfirmAgbTop"));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));
        $this->clickAndWait("link=Home");
        // $this->assertEquals("Kuyichi leather belt JEVER", $this->getText("//a[@id='titleBargain_1']"));
        $this->assertFalse($this->isElementPresent("//ul[@id='newItems']//input[@name='aid' and @value='1000']"));

    }

    /**
     * Invitations functionality. checking enable/disable in admin and email sending in frontend
     * @group navigation
     * @group user
     * @group privateSales
     */
    public function testPrivateShoppingInvitations()
    {
      //Installed GDLib Version with empty value
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iUseGDVersion" => array("type" => "str", "value" => "")));
        //checking if functionality is disabled in frontend
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_link_service_invite"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertFalse($this->isElementPresent("test_link_service_invite"));

        //enabling functionality
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Invitations");
        sleep(1);
        $this->assertEquals("Disable", $this->getSelectedLabel("invitations"));
        $this->assertFalse($this->isVisible("confstrs[dPointsForInvitation]"));
        $this->assertFalse($this->isVisible("confstrs[dPointsForRegistration]"));
        $this->select("invitations", "label=Enable");
        sleep(1);
        $this->assertTrue($this->isVisible("confstrs[dPointsForInvitation]"));
        $this->assertTrue($this->isVisible("confstrs[dPointsForRegistration]"));
        $this->type("confstrs[dPointsForInvitation]", "5");
        $this->type("confstrs[dPointsForRegistration]", "5");
        $this->clickAndWait("save");
        $this->click("link=Invitations");
        sleep(1);
        $this->assertEquals("Enable", $this->getSelectedLabel("invitations"));
        $this->assertEquals("5", $this->getValue("confstrs[dPointsForInvitation]"));
        $this->assertEquals("5", $this->getValue("confstrs[dPointsForRegistration]"));

        //checking functionality in frontend, when user is logged in
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//dl[@id='footerServices']//a[text()='Invite your friends']"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Invite your friends']");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("breadCrumb"));
        $this->assertEquals("Invite your friends", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][1]"));
        $this->type("editval[rec_email][1]", "birute01@nfq.lt");
        $this->assertTrue($this->isElementPresent("editval[rec_email][2]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][3]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][4]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][5]"));
        $this->type("editval[send_name]", "birute_test");
        $this->type("editval[send_email]", "birute_test@nfq.lt");
        $this->assertEquals("Have a look!", $this->getValue("editval[send_subject]"));
        $this->type("editval[send_message]", "Invitation to shop");
        $this->type("c_mac", $this->getText("verifyTextCode"));
        $this->clickAndWait("//button[text()='Send']");


        $this->assertEquals("You are here: / Invite your friends", $this->getText("breadCrumb"));
        $this->assertEquals("Invite your friends", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Invitation e-mail was sent to your friends. Thank you for inviting your friends."));
        //testing functionality in frontend, when user is not logged in
        $this->clickAndWait("//a[text()='Logout']");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("breadCrumb"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Invite your friends']");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("breadCrumb"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->type("editval[rec_email][1]", "birute01@nfq.lt");
        $this->assertTrue($this->isElementPresent("editval[rec_email][2]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][3]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][4]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][5]"));
        $this->type("editval[send_name]", "birute_test");
        $this->type("editval[send_email]", "birute_test@nfq.lt");
        $this->assertEquals("Have a look!", $this->getValue("editval[send_subject]"));
        $this->type("editval[send_message]", "Invitation to shop");
        $this->type("c_mac", $this->getText("verifyTextCode"));
        $this->clickAndWait("//button[text()='Send']");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("breadCrumb"));
        $this->assertEquals("Invite your friends", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Invitation e-mail was sent to your friends. Thank you for inviting your friends."));


    }

    /**
     * Private sales: login
     * @group navigation
     * @group user
     * @group privateSales
     */
    public function testPrivateShoppingLogin()
    {
        $this->openShop();
        $this->assertTrue($this->isTextPresent("Just arrived!"));

        //turning functionality on
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Disable", $this->getSelectedLabel("confstrs[blPsLoginEnabled]"));
        $this->select("confstrs[blPsLoginEnabled]", "label=Enable");
        $this->clickAndWait("save");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Enable", $this->getSelectedLabel("confstrs[blPsLoginEnabled]"));

        //checking in frontend
        $this->openShop();
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("Just arrived!"));
        $this->assertFalse($this->isElementPresent("breadCrumb"));
        $this->assertFalse($this->isElementPresent("topMenu"));
        $this->assertTrue($this->isElementPresent("loginUser"));
        $this->assertTrue($this->isElementPresent("lgn_cook"));

        //forgot pwd link
        $this->clickAndWait("link=Forgot password?");
        $this->assertTrue($this->isElementPresent("forgotPasswordUserLoginName"));
        $this->type("forgotPasswordUserLoginName", "birute_test@nfq.lt");
        $this->clickAndWait("//button[text()='Request Password']");
        $this->assertTrue($this->isTextPresent("Forgot password?"));
        $this->assertTrue($this->isTextPresent("Password was sent to: birute_test@nfq.lt"));
        $this->clickAndWait("backToShop");
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("Just arrived!"));
        $this->assertFalse($this->isElementPresent("breadCrumb"));
        $this->assertFalse($this->isElementPresent("topMenu"));
        $this->assertTrue($this->isElementPresent("loginUser"));
        $this->assertTrue($this->isElementPresent("lgn_cook"));

        //register as new user
        $this->clickAndWait("link=Open account");
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->assertEquals("off", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->assertEquals("off", $this->getValue("orderConfirmAgbBottom"));
        $this->type("invadr[oxuser__oxfname]", "userName");
        $this->type("invadr[oxuser__oxlname]", "userLastName");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "3000");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invCountrySelect", "label=Germany");
        sleep(1);

        $this->clickAndWait("accUserSaveTop");
        $this->assertEquals("userName", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertTrue($this->isTextPresent("Please read and confirm our terms and conditions."));
        $this->check("orderConfirmAgbBottom");
        $this->check("//input[@name='blnewssubscribed' and @value='1']");
        $this->click("accUserSaveTop");
        $this->waitForText("Specify a value for this required field.");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->assertEquals("on", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->assertEquals("on", $this->getValue("orderConfirmAgbBottom"));

        $this->clickAndWait("accUserSaveTop");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));
        $this->assertEquals("We welcome you as registered user! We sent you an e-mail confirming your registration. Thank you.", $this->clearString($this->getText("content")));
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("header"));
        $this->assertFalse($this->isElementPresent("languageTrigger"));
        $this->assertFalse($this->isElementPresent("currencyTrigger"));
        $this->assertFalse($this->isElementPresent("footer"));

        $this->openShop();
        $this->assertEquals("", $this->getValue("loginUser"));
        $this->assertEquals("", $this->getText("loginPwd"));
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("header"));
        $this->assertTrue($this->isElementPresent("languageTrigger"));
        $this->assertFalse($this->isElementPresent("currencyTrigger"));
        $this->assertFalse($this->isElementPresent("footer"));

        //login as existed user
        $this->type("loginUser", "birute_test@nfq.lt");
        $this->type("loginPwd", "useruser");
        $this->clickAndWait("loginButton");
        $this->assertEquals("off", $this->getValue("orderConfirmAgb"));
        $this->assertEquals("I agree to the Terms and Conditions. I have been informed about my Right of Withdrawal.", $this->clearString($this->getText("confirmLabel")));
        $this->clickAndWait("confirmButton");
        $this->assertEquals("I agree to the Terms and Conditions. I have been informed about my Right of Withdrawal.", $this->clearString($this->getText("confirmLabel")));
        $this->check("orderConfirmAgb");
        $this->clickAndWait("confirmButton");
        $this->assertTrue($this->isTextPresent("You are here: / My Account"));
        $this->assertTrue($this->isTextPresent("Hello,"));
        $this->assertTrue($this->isElementPresent("header"));
        $this->assertTrue($this->isElementPresent("languageTrigger"));
        $this->assertTrue($this->isElementPresent("currencyTrigger"));
        $this->assertTrue($this->isElementPresent("footer"));

        //logout
        $this->clickAndWait("logoutLink");
        $this->assertEquals("", $this->getValue("loginUser"));
        $this->assertEquals("", $this->getText("loginPwd"));
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("header"));
        $this->assertTrue($this->isElementPresent("languageTrigger"));
        $this->assertFalse($this->isElementPresent("currencyTrigger"));
        $this->assertFalse($this->isElementPresent("footer"));

        //checking if module works together with basket exclusion and basket expiration
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blBasketExcludeEnabled" => array("type" => "bool", "value" => 'true')));
        //register as new user (when other shopping club modules are on)
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Disable", $this->getSelectedLabel("basketreserved"));
        $this->assertFalse($this->isVisible("confstrs[iPsBasketReservationTimeout]"));
        $this->select("basketreserved", "label=Enable");
        $this->waitForItemAppear("confstrs[iPsBasketReservationTimeout]");
        $this->type("confstrs[iPsBasketReservationTimeout]", "10");
        $this->clickAndWait("save");
        $this->openShop();
        $this->clickAndWait("openAccountLink");
        $this->assertEquals("Open account", $this->getText("openAccHeader"));
        $this->assertFalse($this->isElementPresent("breadCrumb"));
    }


}