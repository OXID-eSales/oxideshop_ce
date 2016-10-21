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

/** Private sales related tests. */
class PrivateSalesFrontendTest extends FrontendTestCase
{
    /**
     * Basket exclusion: situation 1
     *
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
        $this->assertEquals("%YOU_ARE_HERE%: / Kiteboarding / Kites", $this->getText("breadCrumb"));

        //enabling basket exclusion
        $this->callShopSC('oxConfig', null, null, array('blBasketExcludeEnabled' => array("type" => "bool",  "value" => 'true' ) ));

        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->assertTextNotPresent("%ROOT_CATEGORY_CHANGED%");
        $this->clickAndWait("link=Kiteboarding");
        $this->assertElementPresent("scRootCatChanged");
        $this->assertTextPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementPresent("tobasket");
        $this->assertElementPresent("//button[text()='%CONTINUE_SHOPPING%']");
        $this->clickAndWait("tobasket");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']");
        $this->clickAndWait("link=%HOME%");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("scRootCatChanged");
        $this->clickAndWait("moreSubCat_1");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->clickAndWait("//form[@name='tobasketproductList_1']//button");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("link=Kiteboarding");
        $this->assertElementPresent("scRootCatChanged");
        $this->assertTextPresent("%ROOT_CATEGORY_CHANGED%");
        $this->clickAndWait("//button[text()='%CONTINUE_SHOPPING%']");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait("//ul[@id='productList']/li[1]//button");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementPresent("scRootCatChanged");
        $this->assertTextPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementPresent("tobasket");
        $this->assertElementPresent("//button[text()='%CONTINUE_SHOPPING%']");

    }

    /**
     * Basket exclusion: situation 2
     *
     * @group privateSales
     */
    public function testBasketExclusionCase2()
    {
        //enabling basket exclusion
       $this->callShopSC("oxConfig", null, null, array("blBasketExcludeEnabled" => array("type" => "bool", "value" => 'true')));
        //checking in frontend
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("//ul[@id='productList']/li//button");
        $this->clickAndWait("link=Test category 1 [EN] šÄßüл");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));

        $this->clickAndWait("link=Kiteboarding");
        $this->assertElementPresent("scRootCatChanged");
        $this->clickAndWait("tobasket");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));

        $this->click("checkAll");
        $this->clickAndWait("basketRemove");

        $this->assertTextPresent("%BASKET_EMPTY%");
        $this->clickAndWait("link=%HOME%");
        $this->clickAndWait("link=Kiteboarding");
        $this->assertTextNotPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementNotPresent("scRootCatChanged");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTextNotPresent("%ROOT_CATEGORY_CHANGED%");
        $this->assertElementNotPresent("scRootCatChanged");

    }

    /**
     * Private sales: basket expiration
     *
     * @group privateSales
     */
    public function testPrivateShoppingBasketExpiration()
    {
        //products are offline, if bought out
        $this->callShopSC("oxArticle", "save", "1000", array("oxstock" => 2, "oxstockflag" => 2), null, 1);

        //enabling functionality to set basket expiration for 20 sek.
        $this->callShopSC("oxConfig", null, null, array("blPsBasketReservationEnabled" => array("type" => "bool",  "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("iPsBasketReservationTimeout" => array("type" => "str",  "value" => '20')));

        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//ul[@id='newItems']//input[@name='aid' and @value='1000']");
        $this->assertElementPresent("priceBargain_1");
        $this->searchFor("1000");
        $this->assertEquals("1 %HITS_FOR% \"1000\"", $this->getHeadingText("//h1"));
        $this->assertTextNotPresent("%EXPIRES_IN%:");
        $this->selectDropDown("viewOptions", "%line%");

        //adding product to basket
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertTextPresent("%EXPIRES_IN%:");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertTextPresent("%EXPIRES_IN%:");

        //checking if product is reserved
        $this->searchFor("1000");
        $this->assertTextPresent("%NO_ITEMS_FOUND%");
        $this->assertTextPresent("%YOU_ARE_HERE%: / %SEARCH%");
        sleep(21); //waiting till basket will expire
        $this->assertElementNotPresent("basketFlyout", "expired products are still visible in basket popup...");
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");

        $this->searchFor("1000");
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->assertTextNotPresent("%EXPIRES_IN%:");
        $this->assertTextPresent("1 %HITS_FOR% \"1000\"");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));

       //adding to basket again and finishing order
        $this->assertElementPresent("//ul[@id='searchList']/li//button");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->clickAndWait("//ul[@id='searchList']/li//button");
        $this->openBasket();
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_usr']", "example_test@oxid-esales.dev");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='optionLogin']//button");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->assertElementPresent("orderConfirmAgbTop");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));
        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("//ul[@id='newItems']//input[@name='aid' and @value='1000']");
    }

    /**
     * Invitations functionality. checking enable/disable in admin and email sending in frontend
     *
     * @group privateSales
     */
    public function testPrivateShoppingInvitations()
    {
      //Installed GDLib Version with empty value
        $this->callShopSC("oxConfig", null, null, array("iUseGDVersion" => array("type" => "str", "value" => 0)));
        //checking if functionality is disabled in frontend
        $this->openShop();
        $this->assertElementNotPresent("test_link_service_invite");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertElementNotPresent("test_link_service_invite");

        //enabling functionality
        $this->callShopSC("oxConfig", null, null, array("blInvitationsEnabled" => array("type" => "bool",  "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("dPointsForInvitation" => array("type" => "str",  "value" => '5')));
        $this->callShopSC("oxConfig", null, null, array("dPointsForRegistration" => array("type" => "str",  "value" => '5')));

        //checking functionality in frontend, when user is logged in
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//dl[@id='footerServices']//a[text()='%INVITE_YOUR_FRIENDS%']");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%INVITE_YOUR_FRIENDS%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %INVITE_YOUR_FRIENDS%", $this->getText("breadCrumb"));
        $this->assertEquals("%INVITE_YOUR_FRIENDS%", $this->getText("//h1"));
        $this->assertElementPresent("editval[rec_email][1]");
        $this->type("editval[rec_email][1]", "example01@oxid-esales.dev");
        $this->assertElementPresent("editval[rec_email][2]");
        $this->assertElementPresent("editval[rec_email][3]");
        $this->assertElementPresent("editval[rec_email][4]");
        $this->assertElementPresent("editval[rec_email][5]");
        $this->type("editval[send_name]", "example_test");
        $this->type("editval[send_email]", "example_test@oxid-esales.dev");
        $this->assertEquals("%HAVE_A_LOOK%:", $this->getValue("editval[send_subject]"));
        $this->type("editval[send_message]", "Invitation to shop");
        $this->clickAndWait("//button[text()='%SEND%']");


        $this->assertEquals("%YOU_ARE_HERE%: / %INVITE_YOUR_FRIENDS%", $this->getText("breadCrumb"));
        $this->assertEquals("%INVITE_YOUR_FRIENDS%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_INVITE_YOUR_FRIENDS_INVITATION_SENT%");
        //testing functionality in frontend, when user is not logged in
        $this->clickAndWait("//a[text()='%LOGOUT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %INVITE_YOUR_FRIENDS%", $this->getText("breadCrumb"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%INVITE_YOUR_FRIENDS%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %INVITE_YOUR_FRIENDS%", $this->getText("breadCrumb"));
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->type("editval[rec_email][1]", "example01@oxid-esales.dev");
        $this->assertElementPresent("editval[rec_email][2]");
        $this->assertElementPresent("editval[rec_email][3]");
        $this->assertElementPresent("editval[rec_email][4]");
        $this->assertElementPresent("editval[rec_email][5]");
        $this->type("editval[send_name]", "example_test");
        $this->type("editval[send_email]", "example_test@oxid-esales.dev");
        $this->assertEquals("%HAVE_A_LOOK%:", $this->getValue("editval[send_subject]"));
        $this->type("editval[send_message]", "Invitation to shop");
        $this->clickAndWait("//button[text()='%SEND%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %INVITE_YOUR_FRIENDS%", $this->getText("breadCrumb"));
        $this->assertEquals("%INVITE_YOUR_FRIENDS%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_INVITE_YOUR_FRIENDS_INVITATION_SENT%");


    }

    /**
     * Private sales: login
     *
     * @group privateSales
     */
    public function testPrivateShoppingLoginX()
    {
        $this->openShop();
        $this->assertTextPresent("%JUST_ARRIVED%");

        //turning functionality on
        $this->callShopSC("oxConfig", null, null, array("blPsLoginEnabled" => array("type" => "bool",  "value" => 'true')));

        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertTextNotPresent("%YOU_ARE_HERE%: / %MY_ACCOUNT%");
        $this->assertTextNotPresent("%JUST_ARRIVED%");
        $this->assertElementNotPresent("breadCrumb");
        $this->assertElementNotPresent("topMenu");
        $this->assertElementPresent("loginUser");
        $this->assertElementPresent("lgn_cook");

        //forgot pwd link
        $this->clickAndWait("link=%FORGOT_PASSWORD%");
        $this->assertElementPresent("forgotPasswordUserLoginName");
        $this->type("forgotPasswordUserLoginName", "example_test@oxid-esales.dev");
        $this->clickAndWait("//button[text()='%REQUEST_PASSWORD%']");
        $this->assertTextPresent("%FORGOT_PASSWORD%");
        $this->assertTextPresent("%PASSWORD_WAS_SEND_TO%: example_test@oxid-esales.dev");
        $this->clickAndWait("backToShop");
        $this->assertTextNotPresent("%YOU_ARE_HERE%: / %MY_ACCOUNT%");
        $this->assertTextNotPresent("%JUST_ARRIVED%");
        $this->assertElementNotPresent("breadCrumb");
        $this->assertElementNotPresent("topMenu");
        $this->assertElementPresent("loginUser");
        $this->assertElementPresent("lgn_cook");

        //register as new user
        $this->clickAndWait("link=%OPEN_ACCOUNT%");
        $this->type("userLoginName", "example01@oxid-esales.dev");
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

        $this->clickAndWait("accUserSaveTop");
        $this->assertEquals("userName", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertTextPresent("%READ_AND_CONFIRM_TERMS%");
        $this->check("orderConfirmAgbBottom");
        $this->check("//input[@name='blnewssubscribed' and @value='1']");
        $this->click("accUserSaveTop");
        $this->waitForText("%ERROR_MESSAGE_INPUT_NOTALLFIELDS%");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->assertEquals("on", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->assertEquals("on", $this->getValue("orderConfirmAgbBottom"));

        $this->clickAndWait("accUserSaveTop");
        $this->assertTextPresent("%REGISTER%");
        $this->assertEquals("%REGISTER% %MESSAGE_CONFIRMING_REGISTRATION% %THANK_YOU%.", $this->clearString($this->getText("content")));
        $this->assertTextNotPresent("%YOU_ARE_HERE%: / %MY_ACCOUNT%");
        $this->assertTextNotPresent("You're logged in as");
        $this->assertElementNotPresent("header");
        $this->assertElementNotPresent("languageTrigger");
        $this->assertElementNotPresent("currencyTrigger");
        $this->assertElementNotPresent("footer");

        $this->clearCache();
        $this->openShop();
        $this->assertEquals("", $this->getValue("loginUser"));
        $this->assertEquals("", $this->getText("loginPwd"));
        $this->assertTextNotPresent("%YOU_ARE_HERE%: / %MY_ACCOUNT%");
        $this->assertTextNotPresent("You're logged in as");
        $this->assertElementNotPresent("header");
        $this->assertElementPresent("languageTrigger");
        $this->assertElementNotPresent("currencyTrigger");
        $this->assertElementNotPresent("footer");

        //login as existed user
        $this->type("loginUser", "example_test@oxid-esales.dev");
        $this->type("loginPwd", "useruser");
        $this->clickAndWait("loginButton");
        $this->assertEquals("off", $this->getValue("orderConfirmAgb"));
        $this->assertEquals("I agree to the Terms and Conditions. I have been informed about my Right of Withdrawal.", $this->clearString($this->getText("confirmLabel")));
        $this->clickAndWait("confirmButton");
        $this->assertEquals("I agree to the Terms and Conditions. I have been informed about my Right of Withdrawal.", $this->clearString($this->getText("confirmLabel")));
        $this->check("orderConfirmAgb");
        $this->clickAndWait("confirmButton");
        $this->assertTextPresent("%YOU_ARE_HERE%: / %MY_ACCOUNT%");
        $this->assertTextPresent("%GREETING%");
        $this->assertElementPresent("header");
        $this->assertElementPresent("languageTrigger");
        $this->assertElementPresent("currencyTrigger");
        $this->assertElementPresent("footer");

        //logout
        $this->clickAndWait("logoutLink");
        $this->assertEquals("", $this->getValue("loginUser"));
        $this->assertEquals("", $this->getText("loginPwd"));
        $this->assertTextNotPresent("%YOU_ARE_HERE%: / %MY_ACCOUNT%");
        $this->assertTextNotPresent("You're logged in as");
        $this->assertElementNotPresent("header");
        $this->assertElementPresent("languageTrigger");
        $this->assertElementNotPresent("currencyTrigger");
        $this->assertElementNotPresent("footer");

        //checking if module works together with basket exclusion and basket expiration
        $this->callShopSC("oxConfig", null, null, array("blBasketExcludeEnabled" => array("type" => "bool",  "value" => 'true')));

        //register as new user (when other shopping club modules are on)
        $this->callShopSC("oxConfig", null, null, array("blPsBasketReservationEnabled" => array("type" => "bool",  "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("iPsBasketReservationTimeout" => array("type" => "str",  "value" => '5')));

        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("openAccountLink");
        $this->assertEquals("%REGISTER%", $this->getText("openAccHeader"));
        $this->assertElementNotPresent("breadCrumb");
    }
}
