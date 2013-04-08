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

class Acceptance_frontendTestBasic extends oxidAdditionalSeleniumFunctions
{
    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

 // -------------------------- Selenium tests for frontend navigation---- -----------------------

    /**
     * Private sales: login
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPrivateShoppingLogin()
    {
        $this->openShop();
        $this->assertTrue($this->isTextPresent("admin menu: Customer Info -> CMS Pages -> start.tpl welcome text"));

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
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("test_HeaderHome"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_Curr_EUR"));
        $this->assertFalse($this->isElementPresent("test_link_footer_home"));
        $this->assertFalse($this->isTextPresent("admin menu: Customer Info -> CMS Pages -> start.tpl welcome text"));
        $this->assertTrue($this->isElementPresent("test_LoginEmail"));
        $this->assertTrue($this->isElementPresent("lgn_pwd"));
        $this->assertTrue($this->isElementPresent("test_LoginKeepLoggedIn"));

        //forgot pwd link
        $this->clickAndWait("test_LoginLostPwd");
        $this->assertTrue($this->isElementPresent("lgn_usr"));
        $this->type("lgn_usr", "birute_test@nfq.lt");
        $this->clickAndWait("save");
        $this->assertTrue($this->isTextPresent("Forgot Password?"));
        $this->assertTrue($this->isTextPresent("Password was sent to: birute_test@nfq.lt"));
        $this->clickAndWait("test_BackToShop");
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("test_HeaderHome"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_Curr_EUR"));
        $this->assertFalse($this->isElementPresent("test_link_footer_home"));

        //register as new user
        $this->clickAndWait("test_LoginRegister");
        $this->type("lgn_usr", "birute01@nfq.lt");
        $this->type("lgn_pwd", "111111");
        $this->type("lgn_pwd2", "111111");
        $this->assertEquals("off", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->assertEquals("off", $this->getValue("test_OrderConfirmAGBBottom"));
        $this->type("invadr[oxuser__oxfname]", "userName");
        $this->type("invadr[oxuser__oxlname]", "userLastName");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "3000");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invCountrySelect", "label=Germany");
        sleep(1);
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("Please read and confirm our terms and conditions."));
        $this->check("test_OrderConfirmAGBBottom");
        $this->check("//input[@name='blnewssubscribed' and @value='1']");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("Please enter a password."));
        $this->type("lgn_pwd", "111111");
        $this->type("lgn_pwd2", "111111");
        $this->assertEquals("on", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->assertEquals("off", $this->getValue("test_OrderConfirmAGBBottom"));
        $this->check("test_OrderConfirmAGBBottom");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));
        $this->assertEquals("We welcome you as registered user! We sent you an e-mail confirming your registration. Thank you.", $this->clearString($this->getText("//div[@id='body']/div[1]")));
        $this->clickAndWait("test_BackToShop");
        $this->assertEquals("", $this->getValue("test_LoginEmail"));
        $this->assertEquals("", $this->getText("lgn_pwd"));
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("test_HeaderHome"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_Curr_EUR"));
        $this->assertFalse($this->isElementPresent("test_link_footer_home"));

        //login as existed user
        $this->type("test_LoginEmail", "birute_test@nfq.lt");
        $this->type("lgn_pwd", "useruser");
        $this->clickAndWait("test_Login");
        $this->assertEquals("off", $this->getValue("test_OrderConfirmAGBTop"));
        $this->assertEquals("I agree to the Terms and Conditions , I have been informed about my Right of Withdrawal", $this->clearString($this->getText("//div[2]")));
        $this->clickAndWait("test_Login");
        $this->assertEquals("I agree to the Terms and Conditions , I have been informed about my Right of Withdrawal", $this->clearString($this->getText("//div[2]")));
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_Login");
        $this->assertTrue($this->isTextPresent("You are here: / My Account"));
        $this->assertTrue($this->isTextPresent("You're logged in as"));
        $this->assertTrue($this->isElementPresent("test_HeaderHome"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));

        //logout
        $this->clickAndWait("test_RightLogout");
        $this->assertEquals("", $this->getValue("test_LoginEmail"));
        $this->assertEquals("", $this->getText("lgn_pwd"));
        $this->assertFalse($this->isTextPresent("You are here: / My Account"));
        $this->assertFalse($this->isTextPresent("You're logged in as"));
        $this->assertFalse($this->isElementPresent("test_HeaderHome"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_Curr_EUR"));
        $this->assertFalse($this->isElementPresent("test_link_footer_home"));

        //checking if module works together with basket exclusion and basket expiration
         $this->executeSql(" DELETE FROM `oxconfig` WHERE `OXVARNAME`='blBasketExcludeEnabled';");
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('ee548ijuytrf95dea855be2d1e', 'oxbaseshop', 'blBasketExcludeEnabled', 'str', 0x07);" );
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
        $this->clearTmp();
        $this->openShop();
        $this->clickAndWait("test_LoginRegister");
        $this->assertEquals("Open account", $this->getText("test_openAccHeader"));
        $this->assertFalse($this->isElementPresent("path"));
    }

    /**
     * site bottom navigation links
     * @group navigation
     * @group basic
     */
    public function testFrontendBottomLinks()
    {
        $this->openShop();
        //contact link
        $this->clickAndWait("test_link_footer_contact");
        $this->assertEquals("You are here: / Contact", $this->getText("path"));
        $this->assertEquals("Your Company Name", $this->getText("test_companyName"));
        $this->assertEquals("Contact", $this->getText("test_contactHeader"));
        //help link
        $this->clickAndWait("test_link_footer_help");
        $this->assertEquals("You are here: / Help - Main", $this->getText("path"));
        $this->assertEquals("Help - Main", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Here, you can insert additional information"));
        //guestbook link
        $this->clickAndWait("test_link_footer_guestbook");
        $this->assertEquals("You are here: / Guestbook", $this->getText("path"));
        $this->assertEquals("Write entry", $this->getText("test_guestbookWriteHeader"));
        $this->assertEquals("Guestbook", $this->getText("test_guestbookHeader"));
        $this->assertTrue($this->isTextPresent("Demo guestbook entry [EN] šÄßüл"));
        //links page link
        $this->clickAndWait("test_link_footer_links");
        $this->assertEquals("You are here: / Links", $this->getText("path"));
        $this->assertEquals("Links", $this->getText("test_linksHeader"));
        $this->assertTrue($this->isTextPresent("01.01.2008 - http://www.google.com"));
        $this->assertTrue($this->isTextPresent("Demo link description [EN] šÄßüл"));
        //about us link
        $this->clickAndWait("test_link_footer_impressum");
        $this->assertEquals("You are here: / About Us", $this->getText("path"));
        $this->assertEquals("About Us", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Add provider identification here."));
        //terms link
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isElementPresent("test_RightLogout"));
        $this->clickAndWait("test_link_footer_terms");
        $this->assertEquals("You are here: / Terms and Conditions", $this->getText("path"));
        $this->assertEquals("Terms and Conditions", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Insert your terms and conditions here."));
        $this->clickAndWait("test_RightLogout");
        $this->assertTrue($this->isElementPresent("test_RightLogin_Login"));
        $this->assertEquals("You are here: / Terms and Conditions", $this->getText("path"));
        $this->assertEquals("Terms and Conditions", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Insert your terms and conditions here."));
        //basket link
        $this->clickAndWait("test_link_footer_basket");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."));
        //My account linkas
        $this->clickAndWait("test_link_footer_account");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertEquals("Login", $this->getText("test_LoginHeader"));
        //My wish list link
        $this->clickAndWait("test_link_footer_noticelist");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertEquals("Login", $this->getText("test_LoginHeader"));
        //My wish list link
        $this->clickAndWait("test_link_footer_wishlist");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertEquals("Login", $this->getText("test_LoginHeader"));
        //home link
        $this->clickAndWait("test_link_footer_home");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        $this->assertTrue($this->isTextPresent("admin menu: Customer Info -> CMS Pages -> start.tpl welcome text"));
    }
    /**
     * header menu links
     * @group navigation
     * @group basic
     */
    public function testFrontendHeaderMenuLinks()
    {
        $this->openShop();
        //terms link
        $this->clickAndWait("test_HeaderTerms");
        $this->assertEquals("You are here: / Terms and Conditions", $this->getText("path"));
        $this->assertEquals("Terms and Conditions", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Insert your terms and conditions here."));
        //about us link
        $this->clickAndWait("test_HeaderImpressum");
        $this->assertEquals("You are here: / About Us", $this->getText("path"));
        $this->assertEquals("About Us", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Add provider identification here."));
        //home link
        $this->clickAndWait("test_HeaderHome");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        $this->assertTrue($this->isTextPresent("admin menu: Customer Info -> CMS Pages -> start.tpl welcome text"));
        //contact link
        $this->clickAndWait("test_link_service_contact");
        $this->assertEquals("You are here: / Contact", $this->getText("path"));
        $this->assertEquals("Your Company Name", $this->getText("test_companyName"));
        $this->assertEquals("Contact", $this->getText("test_contactHeader"));
        //help link
        $this->clickAndWait("test_link_service_help");
        $this->assertEquals("You are here: / Help - Main", $this->getText("path"));
        $this->assertEquals("Help - Main", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Here, you can insert additional information"));
        //links page link
        $this->clickAndWait("test_link_service_links");
        $this->assertEquals("You are here: / Links", $this->getText("path"));
        $this->assertEquals("Links", $this->getText("test_linksHeader"));
        $this->assertTrue($this->isTextPresent("01.01.2008 -"));
        $this->assertTrue($this->isTextPresent("Demo link description [EN] šÄßüл"));
        //guestbook link
        $this->clickAndWait("test_link_service_guestbook");
        $this->assertEquals("You are here: / Guestbook", $this->getText("path"));
        $this->assertEquals("Write entry", $this->getText("test_guestbookWriteHeader"));
        $this->assertEquals("Guestbook", $this->getText("test_guestbookHeader"));
        $this->assertTrue($this->isTextPresent("Demo guestbook entry [EN] šÄßüл"));
    }
    /**
     * Distributors navigation and all elements checking
     * @group navigation
     * @group basic
     */
    public function testFrontendDistributors()
    {
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='bl_perfLoadVendorTree'");
         $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('9d1ee3af23795yytr96terf2d1e', 'oxbaseshop', 'bl_perfLoadVendorTree', 'bool', 0x93ea1218);" );
        $this->openShop();
        $this->clickAndWait("test_leftRootVendor");
        $this->assertEquals("You are here: / By Distributor", $this->getText("path"));
        $this->assertEquals("By Distributor", $this->getText("test_catTitle"));
        $this->clickAndWait("test_MoreSubCat_1");
        $this->assertEquals("You are here: / By Distributor / Distributor [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));
        $this->assertTrue($this->isElementPresent("test_title_action_1001"));
        $this->assertTrue($this->isElementPresent("test_title_action_1002"));
        $this->assertFalse($this->isElementPresent("test_title_action_1003"));
        //going to vendor root by path link (you are here)
        $this->clickAndWait("//div[@id='path']/a[1]");
        $this->assertEquals("You are here: / By Distributor", $this->getText("path"));
        $this->assertEquals("By Distributor", $this->getText("test_catTitle"));
        //going to vendor via menu link
        $this->clickAndWait("test_MoreSubCat_1");
        $this->assertEquals("You are here: / By Distributor / Distributor [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));
        $this->assertTrue($this->isElementPresent("test_title_action_1001"));
        $this->assertTrue($this->isElementPresent("test_title_action_1002"));
        $this->assertFalse($this->isElementPresent("test_title_action_1003"));
    }

    /**
     * Checking Tags functionality
     * @group navigation
     * @group basic
     */
    public function testFrontendTags()
    {
        $this->clearTmp();
        $this->openShop();
        $this->assertEquals("Tags", $this->getText("tags"));
        $this->clickAndWait("link=More...");
        $this->assertEquals("You are here: / Tags", $this->getText("path"));
        $this->assertEquals("Tags", $this->getText("tags"));
        $this->assertTrue($this->isElementPresent("link=[EN]"));
        $this->assertTrue($this->isElementPresent("link=šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=tag"));
        $this->assertTrue($this->isElementPresent("link=1"));
        $this->assertTrue($this->isElementPresent("link=2"));
        $this->assertTrue($this->isElementPresent("link=3"));

        $this->clickAndWait("link=Home");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));

        $this->clickAndWait("link=tag");
        $this->assertEquals("You are here: / Tags / Tag", $this->getText("path"));
        $this->assertEquals("Tag", $this->getText("test_catTitle"));

        // go to product 1002 details
        $this->clickAndWait("test_title_action_1002");
        $this->assertTrue($this->isElementPresent("test_product_name"));
        $this->assertEquals("You are here: / Tags / Tag", $this->getText("path"));
        $this->assertEquals("Tags", $this->getText("tags"));
        $this->assertTrue($this->isElementPresent("link=tag"));

        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");

        //adding new tag
        $this->clickAndWait("test_editTag");
        $this->assertTrue($this->isTextPresent("Add tags:"));

        $this->type("newTags", "new_tag");
        $this->clickAndWait("test_saveTag");
        $this->assertTrue($this->isElementPresent("link=new_tag"));

        $this->clickAndWait("link=new_tag");
        $this->assertEquals("You are here: / Tags / New_tag", $this->getText("path"));
        $this->assertEquals("New_tag", $this->getText("test_catTitle"));
        $this->assertTrue($this->isElementPresent("test_title_action_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_1"));
        $this->assertFalse($this->isElementPresent("test_cntr_2"));
    }

    /**
     * Checking when prices are entered in NETTO
     * @group order
     * @group basic
     */
    public function testFrontendNettoPrices()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blDeliveryVatOnTop'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blWrappingVatOnTop'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='blEnterNetPrice'");
        $this->clearTmp();
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("52,50 €*", $this->getText("test_price_Search_1000"));
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("52,50 €", $this->getText("test_basket_Price_1000_1"));
        $this->type("test_basketAm_1000_1", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->clickAndWait("test_orderWrapp_1000_1");
        $this->click("test_WrapItem_1000_3");
        $this->check("test_CardItem_3");
        $this->clickAndWait("test_BackToOrder");
        $this->assertEquals("You are here: / Complete Order", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_orderCardPrice"));
        $this->assertEquals("0,24 €", $this->getText("test_orderCardPrice"));
        $this->assertEquals("3,05 €", $this->getText("test_orderWrappNet"));
    }

    /**
     * Checking VAT displaying
     * @group order
     * @group basic
     */
    public function testFrontendVATOptions()
    {
         $this->executeSql("INSERT INTO `oxconfig` VALUES ('8563fba1965a11df3dd4244997', 'oxbaseshop', '', 'blShowVATForDelivery', 'bool', 0x93ea1218)");
         $this->executeSql("INSERT INTO `oxconfig` VALUES ('8563fba1965a11df3xx4244997', 'oxbaseshop', '', 'blShowVATForPayCharge', 'bool', 0x93ea1218)");
         $this->executeSql("INSERT INTO `oxconfig` VALUES ('8563fba1965a11df3zz4244997', 'oxbaseshop', '', 'blShowVATForWrapping', 'bool', 0x93ea1218)");
        $this->clearTmp();
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1000_1", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->clickAndWait("test_orderWrapp_1000_1");
        $this->click("test_WrapItem_1000_3");
        $this->check("test_CardItem_3");
        $this->clickAndWait("test_BackToOrder");
        $this->assertEquals("You are here: / Complete Order", $this->getText("path"));
        $this->assertEquals("150,00 €", $this->getText("test_orderGrossPrice"));
        $this->assertEquals("-15,00 €", $this->getText("test_orderDiscount_1"));
        $this->assertEquals("128,57 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("6,43 €", $this->getText("test_orderVat_5"));
        $this->assertEquals("0,00 €", $this->getText("test_orderShippingNet"));
        $this->assertEquals("7,14 €", $this->getText("test_orderPaymentNet"));
        $this->assertEquals("0,36 €", $this->getText("test_orderPaymentVat"));
        $this->assertEquals("2,76 €", $this->getText("test_orderWrappNet"));
        $this->assertEquals("0,14 €", $this->getText("test_orderWrappVat"));
    }

    /**
     * Checking Tags functionality
     * @group navigation
     * @group basic
     */
    public function testFrontendTagsNavigation()
    {
        $this->clearTmp();
        $this->openShop();
        $this->assertEquals("Tags", $this->getText("tags"));

        $this->clickAndWait("link=tag");
        $this->assertEquals("You are here: / Tags / Tag", $this->getText("path"));
        $this->assertEquals("Tag", $this->getText("test_catTitle"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));
        $this->assertTrue($this->isElementPresent("test_title_action_1001"));
        $this->assertTrue($this->isElementPresent("test_title_action_1002"));
        $this->assertTrue($this->isElementPresent("test_title_action_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_4"));
        $this->assertFalse($this->isElementPresent("test_cntr_5"));

        $this->clickAndWait("test_ArtPerPageTop_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_action_1000"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_action_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));

        $this->clickAndWait("test_PageNrTop_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_title_action_1002"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_title_action_1003"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));

        $this->clickAndWait("test_sortTop_oxvarminprice_asc");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_action_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_title_action_1002"));
        $this->assertEquals("from 55,00 €*", $this->getText("test_price_action_1002"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));

        $this->clickAndWait("test_PageNrTop_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_title_action_1003"));
        $this->assertEquals("75,00 €*", $this->getText("test_price_action_1003"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_action_1001"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_action_1001"));

        $this->clickAndWait("test_ArtPerPageTop_10");
        $this->clickAndWait("test_sortTop_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_4"));
        $this->assertFalse($this->isElementPresent("test_cntr_5"));

    }

    /**
     * switching languages and currencies
     * @group navigation
     * @group basic
     */
    public function testFrontendLanguagesAndCurrencies()
    {
        $this->openShop();
        // ------------ testing EN and DE languages -------------
        $this->assertTrue($this->isTextPresent("admin menu: Customer Info -> CMS Pages -> start.tpl welcome text"));

        $this->clickAndWait("test_Lang_Deutsch");
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getText("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertEquals("Nach Marke/Hersteller", $this->getText("test_leftRootManufacturer"));
        $this->assertEquals("Kontakt", $this->getText("test_link_footer_contact"));
        $this->assertEquals("Hilfe", $this->getText("test_link_footer_help"));
        $this->assertEquals("[DE 1] Test product 1 šÄßüл", $this->getText("test_title_WeekSpecial_1001"));
        $this->assertTrue($this->isTextPresent("Willkommen im OXID"));

        $this->clickAndWait("test_Lang_English");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertEquals("By Brand/Manufacturer", $this->getText("test_leftRootManufacturer"));
        $this->assertEquals("Contact", $this->getText("test_link_footer_contact"));
        $this->assertEquals("Help", $this->getText("test_link_footer_help"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_WeekSpecial_1001"));
        // --------------------- currency ----------------------------
        //For PE right currency box, for EE - top one
        $this->clickAndWait("test_Curr_GBP");
        $this->assertEquals("42.83 £*", $this->getText("test_price_FreshIn_1000"));
        $this->clickAndWait("test_Curr_CHF");
        $this->assertEquals("71,63 CHF*", $this->getText("test_price_FreshIn_1000"));
        $this->clickAndWait("test_Curr_EUR");
        $this->assertEquals("50,00 €*", $this->getText("test_price_FreshIn_1000"));
        #1739
        //currency switch in vendors
        $this->clickAndWait("test_leftRootManufacturer");
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_action_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
        $this->clickAndWait("test_Curr_GBP");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_action_1000"));
        $this->assertEquals("42.83 £*", $this->getText("test_price_action_1000"));
        $this->clickAndWait("test_Curr_EUR");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_action_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
    }

    /**
     * Information links (left side of home page)
     * @group navigation
     * @group basic
     */
    public function testFrontendInformationLinks()
    {
        $this->openShop();
        $this->assertEquals("Information", $this->getText("test_LeftSideInfoHeader"));
        //Privacy Policy
        $this->clickAndWait("test_infoProtection");
        $this->assertEquals("You are here: / Privacy Policy", $this->getText("path"));
        $this->assertEquals("Privacy Policy", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Enter your privacy policy here."));
        //Shipping and Charges
        $this->clickAndWait("test_infoShipping");
        $this->assertEquals("You are here: / Shipping and Charges", $this->getText("path"));
        $this->assertEquals("Shipping and Charges", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Add your shipping information and costs here."));
        //Right of Withdrawal
        $this->clickAndWait("test_infoRights");
        $this->assertEquals("You are here: / Right of Withdrawal", $this->getText("path"));
        $this->assertEquals("Right of Withdrawal", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("Insert here the Right of Withdrawal policy"));
        //How to order ?
        $this->clickAndWait("test_infoHowToOrder");
        $this->assertEquals("You are here: / How to order?", $this->getText("path"));
        $this->assertEquals("How to order?", $this->getText("test_contentHeader"));

        //Credits
        $this->clickAndWait("test_infoCredits");
        $this->assertTrue($this->isTextPresent("You are here: / Credits"));
        $this->assertEquals("Credits", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("What is OXID eShop?"));
        $this->clickAndWait("test_Lang_Deutsch");
        //Bugfix of #3479: CMS page "Credits" doesn't appear under
        //Bugfix of #4540: Credits link can be disabled, but credits should still be accessible via link.
        $this->assertEquals("Credits", $this->getText("test_contentHeader"));
        $this->assertTrue($this->isTextPresent("What is OXID eShop?"));
        $this->clickAndWait("test_HeaderHome");
        $this->clickAndWait("test_Lang_English");
        //Newsletter
        $this->clickAndWait("test_infoNewsletter");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertEquals("Stay informed!", $this->getText("test_stayInformedHeader"));
        $this->assertTrue($this->isTextPresent("You can unsubscribe any time from the newsletter."));
        // ------------ partners logo place ----------------------
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
    }

    /**
     * News small box in main page and news page
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendNews()
    {
        $this->openShop();
        //there are news visible for not logged in users
        $this->assertTrue($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertEquals("The latest news...", $this->getText("test_newsTitle_1"));
        $this->executeSql("DELETE FROM `oxnews` WHERE `OXSHORTDESC` = 'News'");
        $this->clickAndWait("link=Home");

        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertTrue($this->isElementPresent("test_newsTitle_1"));
        $this->assertEquals("Test news text 2 [EN] šÄßüл", $this->getText("test_newsTitle_1"));
        $this->assertTrue($this->isElementPresent("test_newsContinue_1"));

        //going to news page by clicking continue link
        $this->clickAndWait("test_newsContinue_1");
        $this->assertEquals("You are here: / Latest News and Updates", trim($this->getText("path")));
        $this->assertTrue($this->isTextPresent("Latest News and Updates at"));
        $this->assertTrue($this->isTextPresent("02.01.2008 - Test news 2 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("Test news text 2 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("01.01.2008 - Test news 1 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("Test news text 1 [EN] šÄßüл"));
        $this->clickAndWait("test_link_footer_home");

        //going to news page by clicking on news text link
        $this->clickAndWait("test_newsTitle_1");
        $this->assertEquals("You are here: / Latest News and Updates", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Latest News and Updates at $sShopName"));
        $this->assertTrue($this->isTextPresent("02.01.2008 - Test news 2 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("Test news text 2 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("01.01.2008 - Test news 1 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("Test news text 1 [EN] šÄßüл"));
    }

    /**
     * Category filters testing
     * @group navigation
     * @group basic
     */
    public function testFrontendCategoryFilters()
    {
        $this->openShop();
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));

        //checking if paging with filters works correctly
        $this->clickAndWait("test_ArtPerPageTop_1");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertEquals("Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute1]")));
        $this->assertEquals("Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute3]")));
        $this->assertEquals("Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute2]")));

        $this->selectAndWait("attrfilter[testattribute1]", "label=attr value 1 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("test_listXofY_Top"));
        $this->assertFalse($this->isElementPresent("test_PageNrTop_1"));
        $this->assertFalse($this->isElementPresent("test_PageNrTop_2"));
        $this->assertEquals("Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute3]")));
        $this->assertEquals("Please choose attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute2]")));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));

        $this->selectAndWait("attrfilter[testattribute1]", "label=Please choose");
        $this->assertTrue($this->isElementPresent("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));

        $this->selectAndWait("attrfilter[testattribute2]", "label=attr value 12 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("test_title_action_1001"));
        $this->assertFalse($this->isElementPresent("test_listXofY_Top"));
        $this->assertFalse($this->isElementPresent("test_PageNrTop_1"));
        $this->assertFalse($this->isElementPresent("test_PageNrTop_2"));
        $this->assertEquals("Please choose attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute1]")));
        $this->assertEquals("Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute3]")));
        $this->assertEquals("Please choose attr value 12 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute2]")));

        $this->selectAndWait("attrfilter[testattribute2]", "label=Please choose");
        $this->selectAndWait("attrfilter[testattribute3]", "label=attr value 3 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));
        $this->assertTrue($this->isElementPresent("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertEquals("Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute1]")));
        $this->assertEquals("Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute3]")));
        $this->assertEquals("Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute2]")));
    }

    /**
     * Category navigation and all elements checking
     * @group navigation
     * @group basic
     */
    public function testFrontendCategoryNavigation()
    {
        $this->openShop();
        //parent category testing
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssActiveCategory']"));
        $this->assertEquals("Test category 0 desc [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertEquals("Test attribute 1 [EN] šÄßüл:", $this->getText("test_attrfilterTitle_testattribute1_1"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл:", $this->getText("test_attrfilterTitle_testattribute3_2"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл:", $this->getText("test_attrfilterTitle_testattribute2_3"));
        $this->assertEquals("Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute1]")));
        $this->assertEquals("Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute3]")));
        $this->assertEquals("Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute2]")));
        $this->assertTrue($this->isTextPresent("Select other categories - Test category 0 [EN] šÄßüл and:"));
        $this->assertTrue($this->isElementPresent("test_MoreSubCat_1"));
        $this->assertEquals("Category 0 long desc [EN] šÄßüл", $this->getText("test_catLongDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));

        $this->clickAndWait("test_sortBottom_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));

        //subcategory testing
        $this->clickAndWait("test_MoreSubCat_1");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Test category 1 desc [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertEquals("Category 1 long desc [EN] šÄßüл", $this->getText("test_catLongDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));

        $this->clickAndWait("test_sortBottom_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));

        //back to parent cat via menu in category path (you are here)
        $this->clickAndWait("//div[@id='path']/a[1]");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Test category 0 desc [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertEquals("Test attribute 1 [EN] šÄßüл:", $this->getText("test_attrfilterTitle_testattribute1_1"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл:", $this->getText("test_attrfilterTitle_testattribute3_2"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл:", $this->getText("test_attrfilterTitle_testattribute2_3"));
        $this->assertEquals("Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute1]")));
        $this->assertEquals("Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute3]")));
        $this->assertEquals("Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attrfilter[testattribute2]")));
        $this->assertTrue($this->isTextPresent("Select other categories - Test category 0 [EN] šÄßüл and:"));
        $this->assertTrue($this->isElementPresent("test_MoreSubCat_1"));
        $this->assertEquals("Category 0 long desc [EN] šÄßüл", $this->getText("test_catLongDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_desc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_desc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));

        //going to subcategory via menu in the left
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_sub1");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Test category 1 desc [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertEquals("Category 1 long desc [EN] šÄßüл", $this->getText("test_catLongDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_desc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_desc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));
    }

    /**
     * Manufacturer navigation and all elements checking
     * @group navigation
     * @group basic
     */
    public function testFrontendManufacturer()
    {
        $this->openShop();
        $this->clickAndWait("test_leftRootManufacturer");
        $this->assertEquals("You are here: / By Brand/Manufacturer", $this->getText("path"));
        $this->assertEquals("By Brand/Manufacturer", $this->getText("test_catTitle"));
            $this->clickAndWait("test_MoreSubCat_8");
        $this->assertEquals("You are here: / By Brand/Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));
        $this->assertTrue($this->isElementPresent("test_title_action_1001"));
        $this->assertTrue($this->isElementPresent("test_title_action_1002"));
        $this->assertTrue($this->isElementPresent("test_title_action_1003"));
        //going to vendor root by path link (you are here)
        $this->clickAndWait("//div[@id='path']/a[1]");
        $this->assertEquals("You are here: / By Brand/Manufacturer", $this->getText("path"));
        $this->assertEquals("By Brand/Manufacturer", $this->getText("test_catTitle"));
        //going to vendor via menu link
            $this->clickAndWait("test_BoxLeft_SubVend_8");
        $this->assertEquals("You are here: / By Brand/Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageTop_100"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_10"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_20"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_50"));
        $this->assertTrue($this->isElementPresent("test_ArtPerPageBottom_100"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxtitle_asc"));
        $this->assertTrue($this->isElementPresent("test_sortBottom_oxvarminprice_asc"));
        $this->assertTrue($this->isElementPresent("test_title_action_1000"));
        $this->assertTrue($this->isElementPresent("test_title_action_1001"));
        $this->assertTrue($this->isElementPresent("test_title_action_1002"));
        $this->assertTrue($this->isElementPresent("test_title_action_1003"));
    }

    /**
     * Cart boxes on right and left corners. also on top for EE version
     * @group navigation
     * @group order
     * @group basic
     */
    public function testFrontendLeftRightTopBaskets()
    {
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_TopBasketHeader"));

        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->assertTrue($this->isElementPresent("test_RightBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertFalse($this->isElementPresent("test_RightBasketShipping"));
        $this->assertEquals("50,00 €", $this->getText("test_RightBasketTotal"));
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blCalculateDelCostIfNotLoggedIn'");
        $this->openShop();

        $this->clickAndWait("test_toBasket_FreshIn_1000");
        //right basket testing
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("3,90 €", $this->getText("test_RightBasketShipping"));
        $this->assertEquals("50,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_RightBasketTitleLink_1000_1")); //new basic templates
        $this->clickAndWait("test_RightBasketPic_1000_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_RightBasketTitleLink_1000_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1000_1"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_RightBasketHeader");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1000_1"));
        $this->clickAndWait("test_link_footer_home");

        //left basket testing
        $this->assertEquals("1", $this->getText("test_LeftBasketProducts"));
        $this->assertEquals("1", $this->getText("test_LeftBasketItems"));
        $this->assertEquals("3,90 €", $this->getText("test_LeftBasketShipping"));
        $this->assertEquals("50,00 €", $this->getText("test_LeftBasketTotal"));
        $this->clickAndWait("test_LeftBasketHeader");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1000_1"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_LeftBasketOpen");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1000_1"));
        $this->clickAndWait("test_link_footer_home");

        //top basket testing
        $this->assertEquals("1", $this->getText("test_TopBasketProducts"));
        $this->assertEquals("1", $this->getText("test_TopBasketItems"));
        $this->assertEquals("3,90 €", $this->getText("test_TopBasketShipping"));
        $this->assertEquals("50,00 €", $this->getText("test_TopBasketTotal"));
        $this->clickAndWait("test_TopBasketHeader");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1000_1"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("link=Display Cart");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1000_1"));
    }

    /**
     * Right side box My account. Password reminding
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendRightMyAccountRemindPass()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));

        $this->clickAndWait("test_RightLogin_LostPwd");
        $this->assertEquals("You are here: / Forgot Password?", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Forgot Password?"));
        $this->assertTrue($this->isTextPresent("Have you forgotten your Password? "));
        $this->assertFalse($this->isTextPresent("Sollten Sie innerhalb der"));
        $this->assertEquals("", $this->getValue("test_lgn_usr"));
        $this->type("test_lgn_usr", "not_existing_user@nfq.lt");

        $this->clickAndWait("save");
        $this->assertTrue($this->isTextPresent("The e-mail address you have entered is invalid. Please enter a valid e-mail address."));
        $this->assertFalse($this->isTextPresent("Password was sent to: not_existing_user@nfq.lt"));

        $this->type("test_lgn_usr", "birute_test@nfq.lt");
        $this->clickAndWait("save");
        $this->assertFalse($this->isTextPresent("The e-mail address you have entered is invalid. Please enter a valid e-mail address."));
        $this->assertTrue($this->isTextPresent("Password was sent to: birute_test@nfq.lt"));
    }



    /**
     * Right side box My account
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendRightMyAccount()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));

        //clicking link My account
        $this->clickAndWait("test_RightSideAccountHeader");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertEquals("Login", $this->getText("test_LoginHeader"));
        $this->assertTrue($this->isElementPresent("test_LoginEmail"));
        $this->assertTrue($this->isElementPresent("test_LoginPwd"));
        $this->assertTrue($this->isElementPresent("test_Login"));
        $this->assertTrue($this->isElementPresent("test_LoginRegister"));
        $this->assertTrue($this->isElementPresent("test_LoginLostPwd"));
        $this->clickAndWait("test_BackToShop");

        //Open account
        $this->clickAndWait("test_RightLogin_Register");
        $this->assertEquals("You are here: / My Account", $this->getText("path"));
        $this->assertEquals("Open account", $this->getText("test_openAccHeader"));
        $this->assertTrue($this->isTextPresent("Account information"));
        $this->assertTrue($this->isTextPresent("Billing Address"));

        //login
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("You're logged in as: \"birute_test@nfq.lt\" (UserNamešÄßüл UserSurn...)", $this->clearString($this->getText("test_LoginUser")));
        $this->assertTrue($this->isElementPresent("test_RightLogout"));
        $this->clickAndWait("link=Home");

        //adding products to gift registry/wishlist/compare and checking if links are woking
        $this->clickAndWait("test_title_WeekSpecial_1001");
        $this->clickAndWait("test_toCmp");
        $this->clickAndWait("linkToNoticeList");
        $this->clickAndWait("linkToWishList");
        $this->assertEquals("1", $this->getText("test_AccNoticeListAm"));
        $this->assertEquals("1", $this->getText("test_AccWishListAm"));
        $this->assertEquals("1", $this->getText("test_AccComparisonAm"));

        $this->clickAndWait("test_AccNoticeList");
        $this->assertEquals("You are here: / My Account / My Wish List", $this->getText("path"));
        $this->assertEquals("My Wish List", $this->getText("test_smallHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_NoticeList_1"));

        $this->clickAndWait("test_remove_NoticeList_1");
        $this->clickAndWait("test_BackToShop");
        $this->clickAndWait("test_AccWishList");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("path"));
        $this->assertEquals("Search Gift Registry", $this->getText("test_wishlistSearchHeader"));
        $this->assertEquals("My Gift Registry", $this->getText("test_wishlistHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_WishList_1"));

        $this->clickAndWait("test_remove_WishList_1");
        $this->clickAndWait("test_BackToShop");
        $this->clickAndWait("test_AccComparison");
        $this->assertEquals("You are here: / Product Comparison", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("//input[@value='Print Preview']"), "Missing button for showing compared products in popup");
        $this->assertEquals("Product Comparison", $this->getText("test_productComparisonHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_cmp_1001_1"));

        $this->clickAndWait("test_BackToShop");
        $this->assertTrue($this->isElementPresent("test_RightLogout"));
    }

    /**
     * Newsletter ordering
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendNewsletter()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("test_RightSideNewsLetterHeader"));

        //clicking link newsletter
        $this->clickAndWait("test_RightSideNewsLetterHeader");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertEquals("", $this->getValue("test_newsletterFname"));
        $this->assertEquals("", $this->getValue("test_newsletterLname"));
        $this->assertEquals("", $this->getValue("test_newsletterUserName"));
        $this->clickAndWait("test_link_footer_home");

        //subscribing by entering email
        $this->type("test_RightNewsLetterUsername", "birute01@nfq.lt");
        $this->clickAndWait("test_RightNewsLetterSubmit");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertEquals("Stay informed!", $this->getText("test_stayInformedHeader"));
        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("test_newsletterFname", "name_šÄßüл");
        $this->type("test_newsletterLname", "surname_šÄßüл");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("test_newsletterUserName"));
        $this->assertEquals("off", $this->getValue("test_newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("test_newsletterSubscribeOn"));

        //skipping newsletter username
        $this->type("test_newsletterUserName", "");
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTrue($this->isTextPresent("Please complete all fields correctly!"));
        $this->assertEquals("off", $this->getValue("test_newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("test_newsletterSubscribeOn"));

        //incorrect user name
        $this->type("test_newsletterUserName", "aaa");
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTrue($this->isTextPresent("Invalid e-mail Address!"));
        $this->assertEquals("off", $this->getValue("test_newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("test_newsletterSubscribeOn"));

        //correct user name
        $this->type("test_newsletterUserName", "birute01@nfq.lt");
        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("thank you for subscribing to our newsletter."));
        $this->assertTrue($this->isTextPresent("You have just been sent a confirmation e-mail, with which you can activate your subscription."));
        //checking if user was created
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Cust No.");
        $this->clickAndWait("nav.last");
        $this->clickAndWaitFrame("link=birute01@nfq.lt", "edit");
        $this->openTab("link=Extended");
        //because user did not confirm newsletter by email, it is off. setting it on for testing unsubscribe option
        $this->assertEquals("off", $this->getValue("//input[@name='editnews' and @value='1']"));
        $this->check("//input[@name='editnews' and @value='1']");
        $this->clickAndWait("save");
        $this->assertEquals("on", $this->getValue("//input[@name='editnews' and @value='1']"));

        //unsubscibing newsletter
        $this->openShop();
        $this->clickAndWait("test_RightNewsLetterSubmit");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertEquals("Stay informed!", $this->getText("test_stayInformedHeader"));
        $this->assertEquals("", $this->getValue("test_newsletterFname"));
        $this->assertEquals("", $this->getValue("test_newsletterLname"));
        $this->assertEquals("", $this->getValue("test_newsletterUserName"));

        $this->type("test_newsletterUserName", "birute01@nfq.lt");
        $this->assertEquals("off", $this->getValue("test_newsletterSubscribeOff"));
        $this->check("test_newsletterSubscribeOff");

        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Success!"));
        $this->assertTrue($this->isTextPresent("Your subscription to our Newsletter has been canceled."));

        //checking if newsletter was canceled
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Cust No.");
        $this->clickAndWait("nav.last");
        $this->clickAndWaitFrame("link=birute01@nfq.lt", "edit");
        $this->openTab("link=Extended");
        $this->assertEquals("off", $this->getValue("//input[@name='editnews' and @value='1']"));
    }

    /**
     * Newsletter ordering. Double-opt-in is off, so no email confirmation will be sent
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendNewsletterDoubleOptInOff()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '0x07' WHERE `OXVARNAME` = 'blOrderOptInEmail'");

        $this->openShop();
        $this->assertTrue($this->isElementPresent("test_RightSideNewsLetterHeader"));

        //subscribing by entering email
        $this->type("test_RightNewsLetterUsername", "birute01@nfq.lt");
        $this->clickAndWait("test_RightNewsLetterSubmit");
        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertEquals("Stay informed!", $this->getText("test_stayInformedHeader"));
        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("test_newsletterFname", "name_šÄßüл");
        $this->type("test_newsletterLname", "surname_šÄßüл");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("test_newsletterUserName"));
        $this->assertEquals("off", $this->getValue("test_newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("test_newsletterSubscribeOn"));
        $this->clickAndWait("newsLetterSubmit");

        $this->assertEquals("You are here: / Latest News and Updates at", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Congratulations and Welcome!"));
        $this->assertTrue($this->isTextPresent("Your subscription to our Newsletter has now been activated."));
        //checking if user was created
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Cust No.");
        $this->clickAndWait("nav.last");
        $this->clickAndWaitFrame("link=birute01@nfq.lt", "edit");
        $this->openTab("link=Extended");
        //because double-opt-in is off, setting is set to ON
        $this->assertEquals("on", $this->getValue("//input[@name='editnews' and @value='1']"));
    }




    /**
     * Promotions in frontend. Top of the shop & bargain
     * @group navigation
     * @group basic
     */
    public function testFrontendPromotionsTopAndBargain()
    {
        $this->openShop();
        //TOP of the Shop
        $this->assertEquals("TOP of the Shop", $this->getText("test_RightSideTop5Header"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssTopProducts']"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_Top5Title_1000"));
        $this->assertEquals("50,00 €*",  $this->getText("test_Top5Price_1000"));
        $this->clickAndWait("test_Top5Title_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_Top5Pic_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        //Bargain
        $this->assertEquals("Bargain", $this->getText("test_RightSideBarGainHeader"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssBargainProducts']"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_titleBargain_1"));
        $this->clickAndWait("test_picBargain_1");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_titleBargain_1");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
    }

    /**
     * Promotions in frontend. week's special
     * @group navigation
     * @group basic
     */
    public function testFrontendPromotionsWeekSpecial()
    {
        $this->openShop();
        //Week's Special
        $this->assertEquals("Week's Special", $this->getText("test_smallHeader_WeekSpecial_1"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_no_WeekSpecial_1001"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_WeekSpecial_1001"));
        $this->clickAndWait("test_pic_WeekSpecial_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_title_WeekSpecial_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_details_WeekSpecial_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->select("selectList_WeekSpecial_1001_0", "label=selvar2 [EN] šÄßüл");
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл ( 1 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001_1")));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("100,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
        $this->select("selectList_WeekSpecial_1001_0", "label=selvar1 [EN] šÄßüл +1,00 €");
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл ( 1 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001_1")));
        $this->assertEquals("Test product 1 [EN] šÄßüл ( 1 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001_2")));
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("201,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_WeekSpecial_1001");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_WeekSpecial_1001"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_WeekSpecial_1001");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
    }

    /**
     * Promotions in frontend. Our bargain
     * @group navigation
     * @group basic
     */
    public function testFrontendPromotionsOurBargain()
    {
        $this->openShop();
        //Our Bargain!
        $this->assertEquals("Our Bargain! As long as on stock", $this->clearString($this->getText("test_smallHeader_FirstArticle")));
        $this->assertEquals("As long as on stock", $this->getText("test_headerDesc_FirstArticle_1001"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_no_FirstArticle_1001"));
        $this->assertEquals("Test product 1 short desc [EN] šÄßüл", $this->getText("test_shortDesc_FirstArticle_1001"));
        $this->assertEquals("Reduced from 150,00 € (Our normal price.) now only 100,00 €*", $this->clearString($this->getText("test_price_FirstArticle_1001")));
        $this->clickAndWait("test_pic_FirstArticle_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_title_FirstArticle_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_details_FirstArticle_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_FirstArticle_1001");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_FirstArticle_1001"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_FirstArticle_1001");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->select("selectList_FirstArticle_1001_0", "index=2");
        $this->clickAndWait("test_toBasket_FirstArticle_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл ( 1 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001_1")));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
        $this->assertEquals("98,00 €", $this->getText("test_RightBasketTotal"));
        $this->select("selectList_FirstArticle_1001_0", "index=0");
        $this->clickAndWait("test_toBasket_FirstArticle_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл ( 1 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001_1")));
        $this->assertEquals("Test product 1 [EN] šÄßüл ( 1 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001_2")));
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("199,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
    }

    /**
     * Promotions in frontend. Long running hits
     * @group navigation
     * @group basic
     */
    public function testFrontendPromotionsHits()
    {
        $this->executeSql("UPDATE `oxactions` SET `OXACTIVE` = 1 WHERE `OXID` = 'oxstart';");
        $this->openShop();
        //Long-running Hits
        $this->assertEquals("Long-running Hits", $this->getText("test_LongRunHeader"));
        $this->assertEquals("Art.No.: 1002", $this->getText("test_no_LongRun_1002"));
        $this->assertEquals("Test product 2 short desc [EN] šÄßüл", $this->getText("test_shortDesc_LongRun_1002"));
        $this->assertEquals("from 55,00 €*", $this->getText("test_price_LongRun_1002"));
        $this->clickAndWait("test_pic_LongRun_1002");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertEquals("Test product 2 short desc [EN] šÄßüл", $this->getText("test_shortDesc_LongRun_1002"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("test_title_LongRun_1002")));
        $this->clickAndWait("test_title_LongRun_1002");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_details_LongRun_1002");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_LongRun_1002");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_LongRun_1002"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_LongRun_1002");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toBasket_LongRun_1002");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("55,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
        $this->select("varSelect_LongRun_1002", "label=var2 [EN] šÄßüл 67,00 €*");
        $this->clickAndWait("test_toBasket_LongRun_1002");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("122,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
    }

    /**
     * Promotions in frontend. Just arrived
     * @group navigation
     * @group basic
     */
    public function testFrontendPromotionsJustArrived()
    {
        $this->openShop();
        //Just arrived!
        $this->assertEquals("Just arrived!", $this->getText("test_FreshInHeader"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssNewestProducts']"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_FreshIn_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_FreshIn_1000"));
        $this->clickAndWait("test_pic_FreshIn_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("test_title_FreshIn_1000")));
        $this->clickAndWait("test_title_FreshIn_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_details_FreshIn_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_FreshIn_1000");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_FreshIn_1000"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_FreshIn_1000");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("50,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("100,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
    }

    /**
     * Promotions in frontend. Categories
     * @group navigation
     * @group basic
     */
    public function testFrontendPromotionsCategories()
    {
        $this->openShop();
        //Categories
        $this->assertEquals("Categories", $this->getText("test_CategoriesHeader"));
        $this->assertEquals("Art.No.: 1003", $this->getText("test_no_CatArticle_1003"));
        $this->assertEquals("Test product 3 short desc [EN] šÄßüл", $this->getText("test_shortDesc_CatArticle_1003"));
        $this->assertEquals("75,00 €*", $this->getText("test_price_CatArticle_1003"));
        $this->clickAndWait("test_pic_CatArticle_1003");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("test_title_CatArticle_1003")));
        $this->clickAndWait("test_title_CatArticle_1003");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_details_CatArticle_1003");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_footer_home");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_CatArticle_1003");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_CatArticle_1003"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_CatArticle_1003");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toBasket_CatArticle_1003");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("75,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
        $this->clickAndWait("test_toBasket_CatArticle_1003");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("150,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isTextPresent("[DE")); //checking, in nowhere any title or smth is changed into DE lang
    }

    /**
     * Search in frontend
     * @group navigation
     * @group basic
     */
    public function testFrontendSearch()
    {
        $this->openShop();
        //searching for 1 product (using product search field value)
        $this->type("//input[@id='f.search.param']", "šÄßüл1000");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssSearchProducts']"));
        $this->assertEquals("1 Hits for \"šÄßüл1000\"", $this->getText("test_smallHeader"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_Search_1000"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("test_shortDesc_Search_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_Search_1000"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_Search_1000"));

        //checking if all product links in relusts are working
        $this->clickAndWait("test_title_Search_1000");
        $this->assertEquals("You are here: / Search result for \"šÄßüл1000\"", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->clickAndWait("test_pic_Search_1000");
        $this->assertEquals("You are here: / Search result for \"šÄßüл1000\"", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->clickAndWait("test_details_Search_1000");
        $this->assertEquals("You are here: / Search result for \"šÄßüл1000\"", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_Search_1000");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_Search_1000");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->type("test_am_Search_1000", "3");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->assertEquals("150,00 €", $this->getText("test_RightBasketTotal"));
        $this->type("test_am_Search_1000", "1");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("4", $this->getText("test_RightBasketItems"));
        $this->assertEquals("200,00 €", $this->getText("test_RightBasketTotal"));

        //not existing search
        $this->type("//input[@id='f.search.param']", "noExisting");
        $this->select("searchcnid", "index=0");
        $this->select("test_searchManufacturerSelect", "index=0");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));

        //special chars search
        $this->type("//input[@id='f.search.param']", "[EN] šÄßüл");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("4 Hits for \"[EN] šÄßüл\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));

        //navigation between search results
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->assertEquals("Delivery time: 1 Day", $this->getText("test_product_deltime"));
        $this->clickAndWait("test_link_nextArticleTop");
        $this->assertTrue($this->isTextPresent("Delivery time: 1 Week"));
        $this->clickAndWait("test_link_nextArticleTop");
        $this->assertFalse($this->isTextPresent("Delivery time: 1 Month"), "This is parent product. It is not buyable, so no delivery time should be shown");
        $this->clickAndWait("test_link_nextArticleTop");
        $this->assertEquals("Delivery time: 4 - 9 Days", $this->getText("test_product_deltime"));

        //testing #1582
         $this->executeSql( "UPDATE `oxcategories` SET `OXACTIVE`='0', `OXACTIVE_1`='0' WHERE `OXID` = 'testcategory0';" );
        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isElementPresent("link=Test category 0 [EN] šÄßüл"));
        $this->type("f.search.param", "1002");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1002");
        $this->assertTrue($this->isTextPresent("You are here: / Search result for \"1002\""));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_category_testcategory1"));
        $this->clickAndWait("test_category_testcategory1");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_catTitle"));
    }

    /**
     * Search in frontend. Checking option: Fields to be considered in Search
     * @group navigation
     * @group basic
     */
    public function testFrontendSearchConsideredFields()
    {
        $this->openShop();
        //art num is not considered in search
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba852e75e64cf5ccd4ae621339e6050ec87b19ce6db38ed423f15be38d4577f34fedf3f652aeac5b74f9499d5db396220d12940b184d723995e5101b2481c7 WHERE `OXVARNAME` = 'aSearchCols'");

        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));

        //art num is considered in search
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba682873e04af3cad2a864153fe00308ce7d1fc86bb588d225f75de58b4371f549ebf5f054a8aa5d72ff4f9b5bb590240b14921d5f21962f67c7bd29417e61149f025b96cdf815d975cc85278913ee4b505bdfea13af328807c5ddd68d655b20d74de1e812236ebd97ee WHERE `OXVARNAME` = 'aSearchCols'");
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("4 Hits for \"100\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));

        $this->clickAndWait("test_title_Search_1002");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_Variant_1002-1");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("4 Hits for \"100\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));
    }


    /**
     * Search in frontend. Search by selecting Vendor or category in search form.
     * @group navigation
     * @group basic
     */
    public function testFrontendSearchCategoryVendor()
    {
        $this->openShop();
        //search with selecting category
        $this->type("//input[@id='f.search.param']", "100");
        $this->select("searchcnid", "index=7");
        //checking if automatic search is not performed
        sleep(3);
        $this->assertFalse($this->isElementPresent("test_title_Search_1002"));
        $this->select("test_searchManufacturerSelect", "index=0");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("2 Hits for \"100\"", $this->getText("test_smallHeader"));
        $this->assertFalse($this->isElementPresent("test_title_Search_1000"));
        $this->assertFalse($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));
        $this->clickAndWait("link=Home");
        //search by vendor
        $this->type("//input[@id='f.search.param']", "100");
        $this->select("searchcnid", "index=0");
        $this->select("test_searchManufacturerSelect", "label=Manufacturer [EN] šÄßüл");
        //checking if automatic search is not performed
        sleep(3);
        $this->assertFalse($this->isElementPresent("test_title_Search_1002"));
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("4 Hits for \"100\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));
        //search automatically, if category is selected
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='blAutoSearchOnCat'");
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('9d1ee3af23795dea96terf2d1e', 'oxbaseshop', 'blAutoSearchOnCat', 'bool', 0x93ea1218);" );
        $this->clickAndWait("link=Home");
        $this->type("//input[@id='f.search.param']", "1001");
        $this->selectAndWait("searchcnid", "index=6");
        $this->assertEquals("1 Hits for \"1001\"", $this->getText("test_smallHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_Search_1001"));
    }

    /**
     * Search in frontend. OR and AND separators
     * @group navigation
     * @group basic
     */
    public function testFrontendSearchOrAnd()
    {
        //AND is used for search keys
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218 WHERE `OXVARNAME` = 'blSearchUseAND'");
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1000 1001");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        //OR is used for search keys
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blSearchUseAND'");
        $this->type("//input[@id='f.search.param']", "1000 1001");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("2 Hits for \"1000 1001\"", $this->getText("test_smallHeader"));
    }


    /**
     * Product details. test for checking main product details as info, prices, buying etc
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetails()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_sortTop_oxtitle_asc");

        //navigation between products (in details page)
        $this->clickAndWait("test_title_Search_1001");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("Product 2 / 4", $this->getText("test_prodXofY_Top"));
        $this->assertEquals("Product Details", $this->getText("test_detailsHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_nextArticleTop");
        $this->assertEquals("Product 3 / 4", $this->getText("test_prodXofY_Top"));
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_link_prevArticleTop");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("Product 2 / 4", $this->getText("test_prodXofY_Top"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));

        //product info
        $this->clickAndWait("test_title_Search_1001");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_product_artnum"));
        $this->assertEquals("Test product 1 short desc [EN] šÄßüл", $this->getText("test_product_shortdesc"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("test_manufacturer_testmanufacturer"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_category_testcategory0"));
        $this->assertTrue($this->isTextPresent("This item is not in stock and must be reordered"));
        $this->assertTrue($this->isTextPresent("Available on 2008-01-01"));
        $this->assertTrue($this->isElementPresent("test_select_1001_0"));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->getText("test_select_1001_0"));
        $this->assertTrue($this->isTextPresent("Reduced from 150,00 €"));
        $this->assertEquals("100,00 €", $this->getText("test_product_price"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_productFullTitle"));
        $this->assertEquals("Test product 1 long description [EN] šÄßüл", $this->getText("test_product_longdesc"));
        $this->assertEquals("Specification", $this->getText("test_specsHeader"));
        $this->assertEquals("Test attribute 1 [EN] šÄßüл", $this->getText("test_attrTitle_1"));
        $this->assertEquals("attr value 11 [EN] šÄßüл", $this->getText("test_attrValue_1"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл", $this->getText("test_attrTitle_2"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("test_attrValue_2"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл", $this->getText("test_attrTitle_3"));
        $this->assertEquals("attr value 12 [EN] šÄßüл", $this->getText("test_attrValue_3"));
        $this->assertEquals("Please login to access Wish List.", $this->getText("test_LoginToNotice"));
        $this->assertEquals("Please login to access your Gift Registry.", $this->getText("test_LoginToWish"));

        //review when user not logged in
        $this->assertEquals("Write Product Review", $this->getText("test_reviewHeader"));
        $this->assertTrue($this->isTextPresent("No review available for this item."));
        $this->assertEquals("You have to be logged in to write a review.", $this->getText("test_Reviews_login"));

        //compare link
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp"));
        $this->clickAndWait("test_removeCmp");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("compare", $this->getText("test_toCmp"));

        //buying product
        $this->select("test_select_1001_0", "index=1");
        $this->type("test_AmountToBasket", "2");
        $this->clickAndWait("test_toBasket");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("200,00 €", $this->getText("test_RightBasketTotal"));
        $this->select("test_select_1001_0", "index=0");
        $this->type("test_AmountToBasket", "1");
        $this->clickAndWait("test_toBasket");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->assertEquals("301,00 €", $this->getText("test_RightBasketTotal"));

        //current product
        $this->assertEquals("Current product", $this->getText("test_smallHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_current"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_current"));
        $this->type("test_am_current", "1");
        $this->clickAndWait("test_toBasket_current");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("4", $this->getText("test_RightBasketItems"));
        $this->assertEquals("402,00 €", $this->getText("test_RightBasketTotal"));
        $this->type("test_am_current", "2");
        $this->clickAndWait("test_toBasket_current");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("6", $this->getText("test_RightBasketItems"));
        $this->assertEquals("604,00 €", $this->getText("test_RightBasketTotal"));

        //staffelpreis
        $this->type("//input[@id='f.search.param']", "1003");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1003");
        $this->assertEquals("You are here: / Search result for \"1003\"", $this->getText("path"));
            $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
            $this->assertEquals("75,00 €", $this->getText("test_product_price"));
            $this->assertEquals("- 75,00 €", $this->getText("test_amprice_2_5"));
            $this->assertEquals("- 20 % Discount", $this->getText("test_amprice_6_9999999"));
    }

    /**
     * Product details. test for checking main product details available only for logged in user
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetailsForLoggedInUsers()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");

        //review
        $this->clickAndWait("test_Reviews_login");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertEquals("Login", $this->getText("test_LoginHeader"));
        $this->type("test_LoginEmail", "birute_test@nfq.lt");
        $this->type("test_LoginPwd", "useruser");
        $this->clickAndWait("test_Login");
        $this->assertEquals("You are here: / Search result for \"1001\"", $this->getText("path"));
        $this->click("write_new_review");
        sleep(1);
        //selecting rating near product img
        $this->click("//ul[@id='star_rate_top']/li[@class='s4']/a");
        sleep(1);
        $this->assertEquals("4", $this->getValue("artrating"));
        $this->click("//ul[@id='star_rate']/li[@class='s3']/a");
        sleep(1);
        $this->assertEquals("3", $this->getValue("artrating"));
        $this->type("rvw_txt", "user review [EN] šÄßüл for product 1001");
        $this->clickAndWait("test_reviewSave");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("UserNamešÄßüл", $this->getText("test_ReviewName_1"));
        $this->assertEquals("user review [EN] šÄßüл for product 1001", $this->getText("test_ReviewText_1"));
        $this->assertEquals("1 Rating", $this->getText("star_rating_text"));

        //wish list and gift registry
        $this->assertFalse($this->isElementPresent("test_AccNoticeList"));
        $this->assertFalse($this->isElementPresent("test_AccWishList"));
        $this->clickAndWait("linkToNoticeList");
        $this->clickAndWait("linkToWishList");
        $this->assertTrue($this->isElementPresent("test_AccNoticeList"));
        $this->assertTrue($this->isElementPresent("test_AccWishList"));
        $this->clickAndWait("test_AccNoticeList");
        $this->assertTrue($this->isElementPresent("test_title_NoticeList_1"));
        $this->clickAndWait("test_remove_NoticeList_1");
        $this->clickAndWait("test_AccWishList");
        $this->assertTrue($this->isElementPresent("test_title_WishList_1"));
        $this->clickAndWait("test_remove_WishList_1");
    }

    /**
     * Product details. test for checking main product vendor, distributors and category links
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetailsVendorCategory()
    {
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='bl_perfLoadVendorTree'");
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('9d1ee3af23795yytr96terf2d1e', 'oxbaseshop', 'bl_perfLoadVendorTree', 'bool', 0x93ea1218);" );
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        //Testing vendor link
        $this->clickAndWait("test_manufacturer_testmanufacturer");
        $this->assertEquals("You are here: / By Brand/Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("path"));
        //if product dont have manufacturer, link to its distributor is displayed
         $this->executeSql("UPDATE `oxarticles` SET `OXMANUFACTURERID` = '' WHERE `OXID` = '1001'");
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->assertFalse($this->isElementPresent("test_manufacturer_testmanufacturer"));
        $this->clickAndWait("test_vendor_testdistributor");
        $this->assertEquals("You are here: / By Distributor / Distributor [EN] šÄßüл", $this->getText("path"));
        //product has distributor, but they are not displayed in frontend (dont show link to it)
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='bl_perfLoadVendorTree'");
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->assertTrue($this->isTextPresent("Distributor [EN] šÄßüл "));
        $this->assertFalse($this->isElementPresent("test_manufacturer_testmanufacturer"));
        $this->assertFalse($this->isElementPresent("test_vendor_testdistributor"));
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        //Test category Link
        $this->clickAndWait("test_category_testcategory0");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));
    }

    /**
     * Product details. checking product price A
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPriceA()
    {
        $this->openShop();
        //option "Use normal article price instead of zero A, B, C price" is ON
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_action_1001"));
        $this->type("test_RightLogin_Email", "birute0a@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userAuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("35,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_action_1001"));
        $this->clickAndWait("test_title_action_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("35,00 €", $this->getText("test_product_price"));
        $this->assertEquals("(17,50 €/kg)", $this->getText("test_product_price_unit"));

        $this->clickAndWait("test_BackOverviewTop");
        $this->clickAndWait("test_title_action_1001");
        $this->clickAndWait("test_toBasket");

        $this->type("f.search.param", "1002");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("from 45,00 €*", $this->getText("test_price_Search_1002"));
        $this->clickAndWait("test_details_Search_1002");
        $this->assertEquals("from 45,00 €", $this->getText("test_product_price"));
        $this->assertEquals("45,00 €*", $this->getText("test_price_Variant_1002-1"));
        $this->assertEquals("47,00 €*", $this->getText("test_price_Variant_1002-2"));

        $this->clickAndWait("test_RightLogout");
        $this->type("//input[@id='f.search.param']", "1003");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1003");
        $this->assertEquals("75,00 €", $this->getText("test_product_price"));
        $this->type("test_RightLogin_Email", "birute0a@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userAuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("70,00 €", $this->getText("test_product_price"));
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1003_2", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("101,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->assertEquals("70,00 €", $this->getText("test_basket_Price_1003_2"));
        //option "Use normal article price instead of zero A, B, C price" is OFF
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blOverrideZeroABCPrices'");
        $this->openShop();
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_action_1001"));
        $this->type("test_RightLogin_Email", "birute0a@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userAuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("35,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("0,00 €*", $this->getText("test_price_action_1001"));
    }

     /**
     * Product details. checking price B
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPriceB()
    {
        $this->openShop();
        //option "Use normal article price instead of zero A, B, C price" is ON
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_action_1001"));

        $this->type("test_RightLogin_Email", "birute0b@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userBuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("45,00 €*", $this->getText("test_price_action_1000"));
        $this->clickAndWait("test_title_action_1000");
        $this->assertEquals("45,00 €", $this->getText("test_product_price"));
        $this->assertEquals("(22,50 €/kg)", $this->getText("test_product_price_unit"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->clickAndWait("test_title_action_1001");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("test_BackOverviewTop");
        $this->clickAndWait("test_RightLogout");
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));

        $this->type("//input[@id='f.search.param']", "1003");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1003");
        $this->assertEquals("75,00 €", $this->getText("test_product_price"));
        $this->type("test_RightLogin_Email", "birute0b@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userBuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("85,00 €", $this->getText("test_product_price"));
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("101,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->type("test_basketAm_1003_2", "7");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("68,00 €", $this->getText("test_basket_Price_1003_2"));
        $this->type("test_basketAm_1003_2", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("75,00 €", $this->getText("test_basket_Price_1003_2"));
        $this->type("test_basketAm_1003_2", "1");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("85,00 €", $this->getText("test_basket_Price_1003_2"));
    }

    /**
     * Product details. checking prices A, B, C
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPriceC()
    {
        $this->openShop();
        //option "Use normal article price instead of zero A, B, C price" is ON
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_action_1001"));
        $this->clickAndWait("test_title_action_1001");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("test_BackOverviewTop");

        $this->type("test_RightLogin_Email", "birute0c@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userCuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("55,00 €*", $this->getText("test_price_action_1000"));
        $this->clickAndWait("test_title_action_1000");
        $this->assertEquals("55,00 €", $this->getText("test_product_price"));
        $this->assertEquals("(27,50 €/kg)", $this->getText("test_product_price_unit"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->clickAndWait("test_RightLogout");
        $this->assertEquals("50,00 €*", $this->getText("test_price_action_1000"));

        $this->type("//input[@id='f.search.param']", "1003");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_sortTop_oxtitle_asc");
        $this->clickAndWait("test_title_Search_1003");
        $this->assertEquals("75,00 €", $this->getText("test_product_price"));

        $this->clickAndWait("test_toBasket");
        $this->type("test_RightLogin_Email", "birute0c@nfq.lt");
        $this->type("test_RightLogin_Pwd", "userCuser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("75,00 €", $this->getText("test_product_price"));
        $this->clickAndWait("link=Cart");
        $this->assertEquals("101,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->assertEquals("75,00 €", $this->getText("test_basket_Price_1003_2"));
        $this->type("test_basketAm_1003_2", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("75,00 €", $this->getText("test_basket_Price_1003_2"));
    }

    /**
     * Product details. Sending remommendation of product
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetailsRecommend()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='iUseGDVersion'");
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->assertEquals("You are here: / Search result for \"1001\"", $this->getText("path"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        //recommend link
        $this->clickAndWait("test_suggest");
        $this->assertEquals("You are here: / Recommend Product", $this->getText("path"));
        $this->assertEquals("Recommend Product", $this->getText("test_recommendHeader"));
        $this->assertTrue($this->isTextPresent("Enter your address data and personal message."));
        $this->assertEquals("", $this->getValue("editval[rec_name]"));
        $this->assertEquals("", $this->getValue("editval[rec_email]"));
        $this->assertEquals("", $this->getValue("editval[send_name]"));
        $this->assertEquals("", $this->getValue("editval[send_email]"));
        if (isSUBSHOP) {
            $sShopName ="subshop";
        } else {
            $sShopName ="OXID eShop 4";
        }
        $this->assertEquals("Hello, I was looking at $sShopName today and found something that might be interesting for you. Just click on the link below and you will be directed to the shop.", $this->clearString($this->getValue("editval[send_message]")));
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("Please complete all fields marked with a"));
        $this->type("editval[rec_name]", "Test User");
        $this->type("editval[rec_email]", "birute@nfq.lt");
        $this->type("editval[send_name]", "user");
        $this->type("editval[send_email]", "birute_test@nfq.lt");
        $this->type("editval[send_subject]", "Have a look at: Test product 1 [EN] šÄßüл");
        $this->type("c_mac", $this->getText("test_verificationCode"));
        $this->clickAndWait("//input[@value='Send']");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("You are here: / Search result for \"1001\"", $this->getText("path"));
    }

    /**
     * Product details. Testing price alert
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetailsPriceAlert()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='iUseGDVersion'");
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->assertEquals("You are here: / Search result for \"1001\"", $this->getText("path"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $sSecurityCode = $this->getText("//div[@class='verification_code']");
        $this->type("c_mac", $sSecurityCode);
        $this->type("pa[email]", "birute_test@nfq.lt");
        $this->type("pa[price]", "99.99");
        $this->clickAndWait("test_PriceAlarmSubmit");
        $this->assertEquals("You are here: / Price Alert", $this->getText("path"));
        $this->assertEquals("Price Alert", $this->getText("test_priceAlarmHeader"));
        $this->assertTrue($this->isTextPresent("We will inform you as soon as the price of product Test product 1 [EN] šÄßüл falls below 99,99"));
        //disabling price alert
         $this->executeSql("UPDATE `oxarticles` SET `OXBLFIXEDPRICE` = 1 WHERE `OXID` = '1001'" );

        $this->clickAndWait("link=Back to Product");
        $this->assertEquals("You are here: / Search result for \"1001\"", $this->getText("path"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertFalse($this->isElementPresent("c_mac"));
        $this->assertFalse($this->isElementPresent("pa[email]"));
        $this->assertFalse($this->isElementPresent("pa[price]"));
        $this->assertFalse($this->isElementPresent("test_PriceAlarmSubmit"));
        //verifying if price alert is saved in shop
        $this->loginAdmin("Customer Info", "Price Alert");
        $this->type("where[oxpricealarm][oxemail]", "birute_test@nfq.lt");
        $this->clickAndWait("submitit");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("99,99 EUR", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("100,00 EUR", $this->getText("//tr[@id='row.1']/td[7]"));
    }

    /**
     * Product details. testing variants
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetailsVariants()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1002");
        $this->clickAndWait("test_searchGo");

        $this->clickAndWait("test_title_Search_1002");
        $this->assertEquals("You are here: / Search result for \"1002\"", $this->getText("path"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("from 55,00 €", $this->getText("test_product_price"));
        $this->assertFalse($this->isElementPresent("test_toBasket")); //parent article is not buyable
        $this->assertEquals("review for parent product šÄßüл", $this->getText("test_ReviewText_1"));
        $this->assertFalse($this->isElementPresent("test_ReviewText_2"));
        $this->assertEquals("Variant Selection of Test product 2 [EN] šÄßüл", $this->getText("test_variantHeader"));
        $this->assertEquals("Art.No.: 1002-1", $this->getText("test_no_Variant_1002-1"));
        $this->assertEquals("55,00 €*", $this->getText("test_price_Variant_1002-1"));
        $this->assertEquals("Art.No.: 1002-2", $this->getText("test_no_Variant_1002-2"));
        $this->assertEquals("67,00 €*", $this->getText("test_price_Variant_1002-2"));

        $this->type("test_am_Variant_1002-1", "2");
        $this->clickAndWait("test_toBasket_Variant_1002-1");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("110,00 €", $this->getText("test_RightBasketTotal"));

        $this->type("test_am_Variant_1002-1", "1");
        $this->clickAndWait("test_toBasket_Variant_1002-1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->assertEquals("165,00 €", $this->getText("test_RightBasketTotal"));

        $this->type("test_am_Variant_1002-2", "2");
        $this->clickAndWait("test_toBasket_Variant_1002-2");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("5", $this->getText("test_RightBasketItems"));
        $this->assertEquals("299,00 €", $this->getText("test_RightBasketTotal"));

        $this->type("test_am_Variant_1002-2", "1");
        $this->clickAndWait("test_toBasket_Variant_1002-2");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("6", $this->getText("test_RightBasketItems"));
        $this->assertEquals("366,00 €", $this->getText("test_RightBasketTotal"));

        $this->clickAndWait("test_pic_Variant_1002-1");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("55,00 €", $this->getText("test_product_price"));
        $this->assertEquals("review for var1 šÄßüл", $this->getText("test_ReviewText_1"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("test_ReviewText_2"));
        $this->assertFalse($this->isElementPresent("test_ReviewText_3"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_title_current"));
        $this->assertEquals("back to main product Test product 2 [EN] šÄßüл", $this->getText("test_backToParent"));
        $this->assertEquals("Other variants of: Test product 2 [EN] šÄßüл", $this->getText("test_variantHeader1"));

        $this->clickAndWait("test_pic_Variant_1002-2");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("67,00 €", $this->getText("test_product_price"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("test_title_current"));
        $this->assertEquals("back to main product Test product 2 [EN] šÄßüл", $this->getText("test_backToParent"));
        $this->assertEquals("Other variants of: Test product 2 [EN] šÄßüл", $this->getText("test_variantHeader1"));

        $this->clickAndWait("test_title_Variant_1002-1");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_Variant_1002-2");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_details_Variant_1002-1");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_details_Variant_1002-2");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("test_product_name"));
    }

    /**
     * Product details. testing variants. testing ptions related to parent product
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendDetailsVariantsParent()
    {
        //variants reviews will be shown for parent product
        //setting article parent as buyable
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='blShowVariantReviews'");
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='blVariantParentBuyable'");
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('9d1ee3af0f823795dea96huyterf2d1e', 'oxbaseshop', 'blShowVariantReviews', 'bool', 0x93ea1218);" );
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('aa4fae796c9d9ecjghty6243355', 'oxbaseshop', 'blVariantParentBuyable', 'bool', 0x93ea1218);" );

        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1002");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1002");

        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("55,00 €", $this->getText("test_product_price"));
        $this->assertTrue($this->isElementPresent("test_toBasket")); //parent article is buyable
        $this->assertEquals("review for var2 šÄßüл", $this->getText("test_ReviewText_1"));
        $this->assertEquals("review for var1 šÄßüл", $this->getText("test_ReviewText_2"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("test_ReviewText_3"));
        $this->assertFalse($this->isElementPresent("test_ReviewText_4"));

        $this->clickAndWait("test_toBasket");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("55,00 €", $this->getText("test_RightBasketTotal"));

        $this->clickAndWait("test_pic_Variant_1002-1");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("review for var1 šÄßüл", $this->getText("test_ReviewText_1"));
        $this->assertEquals("review for parent product šÄßüл", $this->getText("test_ReviewText_2"));
        $this->assertFalse($this->isElementPresent("test_ReviewText_3"));

        //checking if variant parent is correctly displayed in basket
        $this->clickAndWait("test_link_footer_basket");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_basketTitle_1002_1"));
        $this->assertEquals("Art.No.: 1002", $this->getText("test_basketNo_1002_1"));
        $this->assertEquals("55,00 €", $this->getText("test_basket_Price_1002_1"));
        $this->assertEquals("55,00 €", $this->getText("test_basketGrandTotal"));
    }


    /**
     * My Account navigation: changing password
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountPass()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=My Account");

        //changing password
        $this->assertEquals("You are here: / My Account", $this->getText("path"));
        $this->assertEquals("You're logged in as: \"birute_test@nfq.lt\" (UserNamešÄßüл UserSurn...)", $this->clearString($this->getText("test_LoginUser")));
        $this->assertEquals("Change Account Password", $this->getText("test_link_account_passwordDesc"));
        $this->clickAndWait("test_link_account_password");
        $this->assertEquals("Personal Settings", $this->getText("test_personalSettingsHeader"));
        $this->assertTrue($this->isTextPresent("Change Password"));
        $this->assertTrue($this->isTextPresent("Note:The Password minimum length is 6 characters."));

        //entered diff new passwords
        $this->type("password_old", "useruser");
        $this->type("password_new", "user1user");
        $this->type("password_new_confirm", "useruser");
        $this->clickAndWait("test_savePass");
        $this->assertTrue($this->isTextPresent("Error: Passwords don't match."));

        //new pass is too short
        $this->type("password_old", "useruser");
        $this->type("password_new", "user");
        $this->type("password_new_confirm", "user");
        $this->clickAndWait("test_savePass");
        $this->assertTrue($this->isTextPresent("Error: Your Password is too short."));

        //correct new pass
        $this->type("password_old", "useruser");
        $this->type("password_new", "user1user");
        $this->type("password_new_confirm", "user1user");
        $this->clickAndWait("test_savePass");
        $this->assertEquals("Personal Settings", $this->getText("test_personalSettingsHeader"));
        $this->assertTrue($this->isTextPresent("Your Password has changed."));
        $this->clickAndWait("test_link_account_logout");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isTextPresent("Wrong e-mail or password!"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "user1user");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("You're logged in as: \"birute_test@nfq.lt\" (UserNamešÄßüл UserSurn...)", $this->clearString($this->getText("test_LoginUser")));
    }

    /**
     * My Account navigation: newsletter settings
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountNewsletter()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=My Account");

        //newsletter settings
        $this->assertEquals("Subscribe/cancel Newsletter", $this->getText("test_link_account_newsletterDesc"));
        $this->clickAndWait("test_link_account_newsletter");
        $this->assertEquals("Newsletter Settings", $this->getText("test_newsletterSettingsHeader"));
        $this->assertEquals("No", $this->getSelectedLabel("status"));
        $this->select("status", "label=Yes");
        $this->clickAndWait("test_newsletterSettingsSave");
        $this->assertEquals("Newsletter Settings", $this->getText("test_newsletterSettingsHeader"));
        $this->assertTrue($this->isTextPresent("The Newsletter subscription was successful."));
        $this->clickAndWait("test_link_account_newsletter");
        $this->assertEquals("Yes", $this->getSelectedLabel("status"));
        $this->select("status", "label=No");
        $this->clickAndWait("test_newsletterSettingsSave");
        $this->assertTrue($this->isTextPresent("The Newsletter subscription has been canceled."));
        $this->clickAndWait("test_link_account_newsletter");
        $this->assertEquals("No", $this->getSelectedLabel("status"));
        $this->clickAndWait("test_BackToShop");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
    }

    /**
     * My Account navigation: billing address
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountAddressBilling()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=My Account");

        //Addresses testing
        $this->assertEquals("Update your billing and delivery settings", $this->getText("test_link_account_billshipDesc"));
        $this->clickAndWait("test_link_account_billship");
        $this->assertEquals("Billing and Shipping Settings", $this->getText("test_addressSettingsHeader"));
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
        $this->type("invadr[oxuser__oxbirthdate][month]", "02");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1982");

        $this->clickAndWait("test_accUserSaveTop");
        $this->assertFalse($this->isElementPresent("div[@class='errorbox inbox']"));

        $this->clickAndWait("test_link_account_billship");
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

        $this->clickAndWait("test_accUserSaveTop");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("Saxony", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
    }

    /**
     * My Account navigation: changing shipping address and pass
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountAddressShipping()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_billship");

        //changing email
        $this->type("invadr[oxuser__oxusername]", "birute01@nfq.lt");
        sleep(1);
        $this->assertTrue($this->isVisible("user_password"));
        $this->type("user_password", "useruser");
        $this->clickAndWait("test_accUserSaveTop");
        $this->clickAndWait("test_RightLogout");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isTextPresent("Wrong e-mail or password!"));
        $this->type("test_RightLogin_Email", "birute01@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");

        //delivery address
        $this->select("oxaddressid", "New Address");
        sleep(1);
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

        $this->clickAndWait("test_accUserSaveBottom", "test_link_account_billship");
        $this->clickAndWait("test_link_account_billship");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_deladr[oxaddress__oxstateid]"));
        $this->select("oxaddressid", "label=New Address");
        sleep(1);

        $this->select("oxaddressid", "label=First name_šÄßüл Last name_šÄßüл, street_šÄßüл 1 city_šÄßüл");
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

        $this->clickAndWait("test_accUserSaveBottom");
        $this->clickAndWait("test_link_account_billship");
        $this->select("oxaddressid", "label=New Address");
        sleep(1);

        $this->select("oxaddressid", "label=First name1 Last name1, street1 11 city1");
        $this->waitForPageToLoad("30000");
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
     * Testing min order sum
     * @group navigation
     * @group user
     * @group order
     * @group basic
     */
    public function testFrontendMinOrderSum()
    {
         $this->executeSql( "UPDATE `oxdelivery` SET `OXTITLE_1` = `OXTITLE` WHERE `OXTITLE_1` = '';" );
        $this->openShop();

        //creating order
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");

        //min order sum is 49 €
        //when user is not logged in, default s&h are calculated and no discount applied. sum total is > 49 €
        $this->assertTrue($this->isElementPresent("test_BasketNextStepTop"));
        $this->assertFalse($this->isTextPresent("Minimum order price 49,00 €"));

        //when user logs in, discount is applied and sum total is < 49. order not allowed
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertFalse($this->isElementPresent("test_BasketNextStepTop"));
        $this->assertTrue($this->isTextPresent("Minimum order price 49,00"));

        //when buying 2 items, and amount is > 49 and order is allowed
        $this->type("test_basketAm_1000_1", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertFalse($this->isTextPresent("Minimum order price 49,00 €"));

        //voucher affects order min.sum calculation
        $this->type("voucherNr", "123123");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertTrue($this->isTextPresent("Minimum order price 49,00 €"));

        //removing voucher
        $this->clickAndWait("test_basketVoucherRemove_1");
        $this->assertFalse($this->isTextPresent("Minimum order price 49,00 €"));

        //checking if order step2 is loaded correctly
        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertEquals("Please select a state", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
    }


    /**
     * My Account navigation: Order history
     * Testing min order price
     * @group navigation
     * @group user
     * @group order
     * @group basic
     */
    public function testFrontendMyAccountOrdersHistory()
    {
         $this->executeSql( "UPDATE `oxdelivery` SET `OXTITLE_1` = `OXTITLE` WHERE `OXTITLE_1` = '';" );
        $this->openShop();

        //checking if its ok with no history
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");

        $this->clickAndWait("test_TopAccMyAccount");
        $this->assertEquals("Orders: 0", $this->getText("test_link_account_orderDesc"));
        $this->clickAndWait("test_link_account_order");
        $this->assertEquals("Order History", $this->getText("test_accOrderHistoryHeader"));
        $this->assertTrue($this->isTextPresent("Order History is empty"));
        $this->clickAndWait("link=Home");

        //creating order
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1000_1", "2");
        $this->clickAndWait("test_basketUpdate");

        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertEquals("Please select a state", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Hesse");
        $this->clickAndWait("blshowshipaddress");
        $this->assertEquals("Hesse", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->type("deladr[oxaddress__oxfname]", "deliveryNamešÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "deliverySurnamešÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "deliveryStreetšÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "2");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "deliveryCityšÄßüл");
        $this->assertEquals("-", $this->getSelectedLabel("delCountrySelect"));
        $this->select("delCountrySelect", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_deladr[oxaddress__oxstateid]");
        $this->assertEquals("Please select a state", $this->getSelectedLabel("oxStateSelect_deladr[oxaddress__oxstateid]"));
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Lower Saxony");

        $this->clickAndWait("test_UserNextStepTop");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");

        $this->assertEquals("Billing Address E-mail: birute_test@nfq.lt UserCompany šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл User additional info šÄßüл Musterstr.šÄßüл 1 HE 79098 Musterstadt šÄßüл Germany Phone: 0800 111111", $this->clearString($this->getText("test_orderBillAdress")));
        $this->assertEquals("Shipping Address Mr deliveryNamešÄßüл deliverySurnamešÄßüл deliveryStreetšÄßüл 2 NI 3000 deliveryCityšÄßüл Germany", $this->clearString($this->getText("test_orderShipAdress")));
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");

        //orders history
        $this->clickAndWait("link=My Account");
        $this->assertEquals("Orders: 1", $this->getText("test_link_account_orderDesc"));

        $this->clickAndWait("test_link_account_order");
        $this->assertEquals("Order History", $this->getText("test_accOrderHistoryHeader"));
        $this->assertEquals("Not yet shipped.", $this->getText("test_accOrderStatus_12"));
        $this->assertEquals("12", $this->getText("test_accOrderNo_12"));
        $this->assertEquals("deliveryNamešÄßüл deliverySurnamešÄßüл", $this->clearString($this->getText("test_accOrderName_12")));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_accOrderLink_12_1"));
        $this->assertEquals("2", $this->getText("test_accOrderAmount_12_1"));

        $this->clickAndWait("test_accOrderLink_12_1");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_order");
        $this->assertFalse($this->isElementPresent("test_RightBasketTitle_1000_1"));

        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_logout");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertEquals("", $this->getValue("test_RightLogin_Email"));
        $this->assertEquals("", $this->getValue("test_RightLogin_Pwd"));
    }

    /**
     * orders with fraction order quantities.
     * @group order
     * @group navigation
     * @group basic
     */
    public function testFrontendOrdersFractionQuantities()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");

        //ordering fraction quantities
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "3.4");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("3.4", $this->getValue("test_basketAm_1000_1"));
        $this->assertEquals("170,00 €", $this->getText("test_basket_TotalPrice_1000_1"));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");

        //checking fraction quantities in admin
        //checking if product amount can be changed correctly
            $this->loginAdmin("Administer Products", "Products");
            $this->type("where[oxarticles][oxartnum]", "1000");
            $this->clickAndWait("submitit");
            $this->clickAndWaitFrame("link=1000", "edit");
            $this->openTab("link=Stock");
            $this->assertEquals("11.6", $this->getValue("editval[oxarticles__oxstock]"));
            $this->type("editval[oxarticles__oxstock]", "13.5");
            $this->clickAndWait("save");
            $this->assertEquals("13.5", $this->getValue("editval[oxarticles__oxstock]"));

        //checking when disabled fraction quantity
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blAllowUnevenAmounts'");
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "3.4");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->type("test_am_Search_1001", "0.3");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->type("test_am_Search_1003", "1.5");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("3", $this->getValue("test_basketAm_1000_1"));
        $this->assertEquals("2", $this->getValue("test_basketAm_1003_2"));
        //product 1001 was not added, because 0.3 is rounded to 0
        $this->assertFalse($this->isElementPresent("test_basketAm_1001_1"));
        $this->assertFalse($this->isElementPresent("test_basketAm_1001_2"));
        $this->assertFalse($this->isElementPresent("test_basketAm_1001_3"));
    }


    /**
     * My Account navigation: My Wish List
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountWishList()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("//input[@id='f.search.param']", "1003");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1003");
        $this->clickAndWait("linkToNoticeList");

        //wish list testing
        $this->clickAndWait("link=My Account");
        $this->assertEquals("Product: 1", $this->getText("test_link_account_noticelistDesc"));
        $this->clickAndWait("test_link_account_noticelist");
        $this->assertEquals("My Wish List", $this->getText("test_smallHeader"));
        $this->assertEquals("Art.No.: 1003", $this->getText("test_no_NoticeList_1"));
        $this->assertEquals("Test product 3 short desc [EN] šÄßüл", $this->getText("test_shortDesc_NoticeList_1"));
        $this->assertEquals("75,00 €*",$this->getText("test_price_NoticeList_1"));
        $this->clickAndWait("test_pic_NoticeList_1");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("path"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_AccNoticeList");
        $this->clickAndWait("test_title_NoticeList_1");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_AccNoticeList");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_NoticeList_1");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_NoticeList_1"));
        $this->clickAndWait("test_removeCmp_NoticeList_1");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));


        $this->clickAndWait("test_details_NoticeList_1");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_AccNoticeList");
        $this->type("test_am_NoticeList_1", "2");
        $this->clickAndWait("test_toBasket_NoticeList_1");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1003_1"));
        $this->assertEquals("Test product 3 [EN] šÄßüл ( 2 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1003_1")));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->type("test_am_NoticeList_1", "1");
        $this->clickAndWait("test_toBasket_NoticeList_1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->clickAndWait("test_remove_NoticeList_1");
        $this->assertFalse($this->isElementPresent("test_AccNoticeList"));
        $this->assertTrue($this->isTextPresent("Your Wish List is empty."));
        $this->assertEquals("My Wish List", $this->getText("test_smallHeader"));
        $this->clickAndWait("test_BackToShop");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        $this->type("//input[@id='f.search.param']", "1003");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1003");
        $this->clickAndWait("linkToNoticeList");
        $this->clickAndWait("test_AccNoticeList");
        $this->clickAndWait("test_remove_NoticeList_1");
        $this->assertFalse($this->isElementPresent("test_AccNoticeList"));
        $this->assertTrue($this->isTextPresent("Your Wish List is empty."));
    }

    /**
     * My Account navigation: Product Comparison
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountCompare()
    {
        $this->executeSql("UPDATE `oxactions` SET `OXACTIVE` = 1 WHERE `OXID` = 'oxstart';");
        $this->clearTmp();
        $this->openShop();
        //compare list testing
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->clickAndWait("test_toCmp_action_1000");
        $this->clickAndWait("test_toCmp_action_1001");
        $this->clickAndWait("test_link_footer_home");
        $this->clickAndWait("test_toCmp_LongRun_1002");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=My Account");
        $this->assertEquals("Product: 3", $this->getText("test_link_account_comparelistDesc"));
        $this->clickAndWait("test_link_account_comparelist");
        $this->assertEquals("You are here: / Product Comparison", $this->getText("path"));
        $this->assertEquals("Product Comparison", $this->getText("test_productComparisonHeader"));
        $this->assertTrue($this->isElementPresent("test_title_cmp_1000_1"));
        $this->assertTrue($this->isElementPresent("test_title_cmp_1001_2"));
        $this->assertTrue($this->isElementPresent("test_title_cmp_1002_3"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_cmp_1000_1"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_no_cmp_1001_2"));
        $this->assertEquals("Art.No.: 1002", $this->getText("test_no_cmp_1002_3"));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectList_cmp_1001_2_0")));
        $this->assertEquals("var1 [EN] šÄßüл 55,00 €* var2 [EN] šÄßüл 67,00 €*", $this->clearString($this->getText("varSelect_cmp_1002_3")));
        $this->clickAndWait("test_pic_cmp_1000_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_AccComparison");
        $this->clickAndWait("test_title_cmp_1001_2");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_AccComparison");
        $this->clickAndWait("test_details_cmp_1000_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_AccComparison");
        $this->clickAndWait("test_toBasket_cmp_1000_1");
        $this->clickAndWait("test_toBasket_cmp_1001_2");
        $this->assertEquals("2", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("151,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isElementPresent("test_AccNoticeList"));
        $this->clickAndWait("test_tonotice_cmp_1000_1");
        $this->assertTrue($this->isElementPresent("test_AccNoticeList"));
        $this->assertFalse($this->isElementPresent("test_AccWishList"));
        $this->clickAndWait("test_towish_cmp_1001_2");
        $this->assertTrue($this->isElementPresent("test_AccWishList"));
        $this->assertEquals("Test attribute 1 [EN] šÄßüл:", $this->getText("test_cmpAttrTitle_1"));
        $this->assertEquals("attr value 1 [EN] šÄßüл", $this->getText("test_cmpAttr_1_1000"));
        $this->assertEquals("attr value 11 [EN] šÄßüл", $this->getText("test_cmpAttr_1_1001"));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл:", $this->getText("test_cmpAttrTitle_2"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("test_cmpAttr_2_1000"));
        $this->assertEquals("attr value 3 [EN] šÄßüл", $this->getText("test_cmpAttr_2_1001"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл:", $this->getText("test_cmpAttrTitle_3"));
        $this->assertEquals("attr value 2 [EN] šÄßüл", $this->getText("test_cmpAttr_3_1000"));
        $this->assertEquals("attr value 12 [EN] šÄßüл", $this->getText("test_cmpAttr_3_1001"));
        $this->clickAndWait("compareRight_1000");
        $this->assertTrue($this->isElementPresent("test_title_cmp_1001_1"));
        $this->assertTrue($this->isElementPresent("test_title_cmp_1000_2"));
        $this->clickAndWait("compareLeft_1000");
        $this->assertTrue($this->isElementPresent("test_title_cmp_1000_1"));
        $this->assertTrue($this->isElementPresent("test_title_cmp_1001_2"));
        $this->clickAndWait("test_RightLogout");
        $this->assertFalse($this->isElementPresent("test_tonotice_cmp_1000_1"));
        $this->assertFalse($this->isElementPresent("test_towish_cmp_1001_2"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_remove_cmp_1000");
        $this->assertFalse($this->isElementPresent("test_no_cmp_1000_1"));
        $this->assertTrue($this->isElementPresent("test_no_cmp_1001_1"));
        $this->clickAndWait("test_remove_cmp_1001");
        $this->clickAndWait("test_remove_cmp_1002");
        $this->assertEquals("Product Comparison", $this->getText("test_productComparisonHeader"));
        $this->assertTrue($this->isTextPresent("Please select at least two products to be compared."));
        $this->clickAndWait("test_BackToShop");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
    }

    /**
     * Navigation in compare list
     * @group navigation
     * @group basic
     */
    public function testFrontendCompareNavigation()
    {
        $this->openShop();
        $this->type("f.search.param", "board");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toCmp_Search_058de8224773a1d5fd54d523f0c823e0");
        $this->clickAndWait("test_toCmp_Search_058e613db53d782adfc9f2ccb43c45fe");
        $this->clickAndWait("test_toCmp_Search_adc920f4cbfa739803058c663a4a00b9");
        $this->clickAndWait("test_toCmp_Search_b56369b1fc9d7b97f9c5fc343b349ece");
        $this->clickAndWait("test_AccComparison");
        $this->assertEquals("Page 1 / 2", $this->getText("test_ComparePageXofY"));
        $this->clickAndWait("compareRight_adc920f4cbfa739803058c663a4a00b9");
        $this->clickAndWait("test_link_nextPageTop");
        $this->assertEquals("Page 2 / 2", $this->getText("test_ComparePageXofY"));
        $this->clickAndWait("compareLeft_adc920f4cbfa739803058c663a4a00b9");
        $this->clickAndWait("test_PageNr_1");
        $this->assertTrue($this->isElementPresent("test_ComparePageXofY"));
        $this->assertEquals("Page 1 / 2", $this->getText("test_ComparePageXofY"));
        $this->clickAndWait("compareRight_adc920f4cbfa739803058c663a4a00b9");
        $this->assertEquals("Page 1 / 2", $this->getText("test_ComparePageXofY"));
        $this->assertTrue($this->isElementPresent("test_PageNr_1"));
        $this->assertTrue($this->isElementPresent("test_PageNr_2"));
        $this->assertTrue($this->isElementPresent("test_link_nextPageTop"));
        $this->clickAndWait("compareRight_058e613db53d782adfc9f2ccb43c45fe");
        $this->clickAndWait("compareRight_058e613db53d782adfc9f2ccb43c45fe");
        $this->assertEquals("Page 1 / 2", $this->getText("test_ComparePageXofY"));
        $this->assertTrue($this->isElementPresent("test_PageNr_1"));
        $this->assertTrue($this->isElementPresent("test_PageNr_2"));
        $this->assertTrue($this->isElementPresent("test_link_nextPageTop"));
    }

    /**
     * Performance option "Show compare list" is disabled
     * @group navigation
     * @group basic
     */
    public function testFrontendDisabledCompare()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showCompareList'");
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_toCmp_WeekSpecial_1001"));
        $this->assertFalse($this->isElementPresent("test_toCmp_FirstArticle_1001"));
        $this->assertFalse($this->isElementPresent("test_toCmp_LongRun_1002"));
        $this->assertFalse($this->isElementPresent("test_toCmp_FreshIn_1000"));
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertFalse($this->isElementPresent("test_toCmp_Search_1000"));
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_title_Search_1000");
        $this->assertFalse($this->isElementPresent("test_toCmp"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertFalse($this->isElementPresent("test_toCmp_Search_1001"));
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_TopAccMyAccount");
        $this->assertFalse($this->isElementPresent("test_link_account_comparelist"));
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertFalse($this->isElementPresent("test_toCmp_AlsoBought_1001"));
        $this->assertFalse($this->isElementPresent("test_toCmp_customerwho_1001"));
    }

    /**
     * My Account navigation: My Gift Registry. setting gift registry as searchable
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendGiftRegistrySearchable()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");

        //gift registry
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->clickAndWait("linkToWishList");

        $this->clickAndWait("link=My Account");
        $this->assertEquals("Product: 1", $this->getText("test_link_account_wishlistDesc"));
        $this->clickAndWait("test_link_account_wishlist");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("path"));
        $this->assertEquals("My Gift Registry", $this->getText("test_wishlistHeader"));

        //making gift registry not searchable
        $this->assertTrue($this->isTextPresent("Everyone shall be able to search and display my gift registry:"));
        $this->assertEquals("Yes", $this->getSelectedLabel("blpublic"));
        $this->select("blpublic", "label=No");
        $this->clickAndWait("test_Wishlist_save");

        $this->type("//form[@name='wishlist_searchbox']//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("test_WishlistSearch");
        $this->assertTrue($this->isTextPresent("Sorry, no Gift Registry found!"));
        $this->assertEquals("No", $this->getSelectedLabel("blpublic"));

        //making gift registry searchable
        $this->select("blpublic", "label=Yes");
        $this->clickAndWait("test_Wishlist_save");

        $this->type("//form[@name='wishlist_searchbox']//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("test_WishlistSearch");
        $this->assertTrue($this->isElementPresent("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл"));

        $this->clickAndWait("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл");
        $this->assertEquals("You are here: / Gift Registry", $this->getText("path"));
        $this->assertEquals("Gift Registry", $this->getText("test_giftRegistryHeader"));
        $this->assertTrue($this->isTextPresent("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_WishList_1"));

        $this->clickAndWait("test_AccWishList");
        $this->assertTrue($this->isTextPresent("Click here to send your gift registry to your friends:"));

        $this->clickAndWait("test_Wishlist_send");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("path"));
        $this->assertEquals("Send Gift Registry", $this->getText("test_WishlistSendHeader"));
        $this->type("editval[rec_email]", "birute@nfq.lt");
        $this->type("editval[rec_name]", "recipient");
        if (isSUBSHOP) {
            $sShopName ="subshop";
        } else {
            $sShopName ="OXID eShop 4";
        }
        $this->assertEquals("Hi, I created a Gift Registry at $sShopName . Great if you could buy something for me.", $this->getValue("editval[send_message]"));
        $this->type("editval[send_message]", "Hi, I created a Gift Registry at OXID.");

        $this->clickAndWait("test_WishList_SendMsg");
        $this->assertTrue($this->isTextPresent("Your Gift Registry was sent successfully to: birute@nfq.lt"));
        $this->assertEquals("recipient", $this->getValue("editval[rec_name]"));
        $this->assertEquals("birute@nfq.lt", $this->getValue("editval[rec_email]"));
        $this->assertEquals("Hi, I created a Gift Registry at OXID.", $this->getValue("editval[send_message]"));
    }

    /**
     * My Account navigation: My Gift Registry
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendMyAccountGiftRegistry()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //gift registry
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->clickAndWait("linkToWishList");
        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_wishlist");
        //checking gift registry info
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_WishList_1"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("test_shortDesc_WishList_1"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_WishList_1"));

        $this->clickAndWait("test_pic_WishList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_AccWishList");
        $this->clickAndWait("test_title_WishList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_AccWishList");
        $this->clickAndWait("test_details_WishList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_AccWishList");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));

        $this->clickAndWait("test_toCmp_WishList_1");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_WishList_1"));

        $this->clickAndWait("test_removeCmp_WishList_1");
        $this->assertEquals("compare", $this->getText("test_toCmp_WishList_1"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));

        $this->type("test_am_WishList_1", "2");
        $this->clickAndWait("test_toBasket_WishList_1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("100,00 €", $this->getText("test_RightBasketTotal"));

        $this->clickAndWait("test_toBasket_WishList_1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->assertEquals("150,00 €", $this->getText("test_RightBasketTotal"));

        $this->clickAndWait("test_remove_WishList_1");
        $this->assertTrue($this->isTextPresent("The Gift Registry is empty."));

        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->clickAndWait("linkToWishList");
    }

    /**
     * My Account navigation: My Gift Registry
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendSearchForGiftRegistry()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //creating gift registry
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->clickAndWait("linkToWishList");

        //logging in as other user for searching this gift registry
        $this->clickAndWait("test_RightLogout");
        $this->type("test_RightLogin_Email", "admin@myoxideshop.com");
        $this->type("test_RightLogin_Pwd", "admin0303");
        $this->clickAndWait("test_RightLogin_Login");

        //search for gift registry
        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_wishlist");
        $this->type("//form[@name='wishlist_searchbox']//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("test_WishlistSearch");
        $this->assertTrue($this->isElementPresent("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл"));

        $this->clickAndWait("link=Gift Registry of UserNamešÄßüл UserSurnamešÄßüл");
        $this->assertEquals("You are here: / Gift Registry", $this->getText("path"));
        $this->assertEquals("Gift Registry", $this->getText("test_giftRegistryHeader"));
        $this->assertTrue($this->isTextPresent("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл"));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_WishList_1"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("test_shortDesc_WishList_1"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_WishList_1"));

        $this->clickAndWait("test_pic_WishList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        //after visiting other persons gift registry, link to it appears in top menu
        $this->assertTrue($this->isElementPresent("link=UserNamešÄßüл's Gift Registry"));

        $this->clickAndWait("link=UserNamešÄßüл's Gift Registry");
        $this->assertTrue($this->isTextPresent("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл"));

        $this->clickAndWait("test_title_WishList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Public Gift Registries", $this->getText("link=Public Gift Registries"));

        $this->clickAndWait("link=Public Gift Registries");
        $this->clickAndWait("test_details_WishList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("link=Public Gift Registries");
        $this->assertTrue($this->isTextPresent("Welcome to the gift registry of UserNamešÄßüл UserSurnamešÄßüл"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));

        $this->clickAndWait("test_toCmp_WishList_1");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_WishList_1"));

        $this->clickAndWait("test_removeCmp_WishList_1");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("compare", $this->getText("test_toCmp_WishList_1"));

        $this->type("test_am_WishList_1", "2");
        $this->clickAndWait("test_toBasket_WishList_1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("100,00 €", $this->getText("test_RightBasketTotal"));

        $this->clickAndWait("//input[@value='add to Cart']");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->assertEquals("150,00 €", $this->getText("test_RightBasketTotal"));

        //deleting gift registry
        $this->clickAndWait("test_RightLogout");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_AccWishList");
        $this->clickAndWait("test_remove_WishList_1");
        $this->assertTrue($this->isTextPresent("The Gift Registry is empty."));
        $this->clickAndWait("test_BackToShop");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));

        //searching for gift registry again. now gift registry wil not be found, couse its empty
        $this->clickAndWait("test_RightLogout");
        $this->type("test_RightLogin_Email", "admin@myoxideshop.com");
        $this->type("test_RightLogin_Pwd", "admin0303");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_wishlist");
        $this->type("//form[@name='wishlist_searchbox']//input[@name='search']", "birute_test@nfq.lt");
        $this->clickAndWait("//input[@value='Search']");
        $this->assertTrue($this->isTextPresent("Sorry, no Gift Registry found!"));

        //logout
        $this->clickAndWait("link=My Account");
        $this->clickAndWait("test_link_account_logout");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->clickAndWait("test_BackToShop");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
    }

    /**
     * Gift Registry is disabled via performance options
     * @group navigation
     * @group basic
     */
    public function testFrontendDisabledGiftRegistry()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showWishlist'");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->assertFalse($this->isElementPresent("linkToWishList"));
        $this->assertTrue($this->isElementPresent("linkToNoticeList"));
        $this->assertTrue($this->isElementPresent("test_Recommlist"));
        $this->clickAndWait("test_TopAccMyAccount");
        $this->assertTrue($this->isElementPresent("test_link_account_noticelist"));
        $this->assertFalse($this->isElementPresent("test_link_account_wishlist"));
        $this->assertTrue($this->isElementPresent("test_link_account_recommlist"));
    }

    /**
     * Last seen product in porducts details page
     * @group navigation
     * @group basic
     */
    public function testFrontendLastSeenProduct()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->clickAndWait("test_title_Search_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Last seen products", $this->getText("test_LastSeenHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_LastSeen_1001"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_no_LastSeen_1001"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_LastSeen_1001"));
        $this->clickAndWait("test_pic_LastSeen_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_LastSeen_1000"));
        $this->clickAndWait("test_title_LastSeen_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_details_LastSeen_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_details_LastSeen_1000");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_LastSeen_1001");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_LastSeen_1001"));
        $this->clickAndWait("test_removeCmp_LastSeen_1001");
        $this->assertEquals("compare", $this->getText("test_toCmp_LastSeen_1001"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toBasket_LastSeen_1001");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("101,00 €", $this->getText("test_RightBasketTotal"));
        $this->clickAndWait("test_toBasket_LastSeen_1001");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("202,00 €", $this->getText("test_RightBasketTotal"));
    }

    /**
     * Vats for products (category, product and personal product vat)
     * @group vat
     * @group basic
     */
    public function testFrontendVAT()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("5%", $this->getText("test_basket_Vat_1000_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_2"));
        $this->assertEquals("19%", $this->getText("test_basket_Vat_1003_3"));
        $this->assertEquals("11,97 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("9,18 €", $this->getText("test_basketVAT_10"));
        $this->assertEquals("2,38 €", $this->getText("test_basketVAT_5"));
        $this->assertEquals("202,47 €", $this->getText("test_basketNet"));
        $this->assertEquals("226,00 €", $this->getText("test_basketGross"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //for austria vat is 0% without vatID checking
        $this->clickAndWait("test_BasketNextStepTop");
        $this->select("invadr[oxuser__oxcountryid]", "label=Austria");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_Step1");
        $this->assertEquals("0%", $this->getText("test_basket_Vat_1000_1"));
        $this->assertEquals("47,62 €", $this->getText("test_basket_Price_1000_1"));
        $this->assertEquals("0%", $this->getText("test_basket_Vat_1001_2"));
        $this->assertEquals("91,82 €", $this->getText("test_basket_Price_1001_2"));
        $this->assertEquals("0%", $this->getText("test_basket_Vat_1003_3"));
        $this->assertEquals("63,03 €", $this->getText("test_basket_Price_1003_3"));
        $this->assertEquals("0,00 €", $this->getText("test_basketVAT_0"));
        $this->assertEquals("202,47 €", $this->getText("test_basketNet"));
        $this->assertEquals("202,47 €", $this->getText("test_basketGross"));
        //for Belgium vat 0% only with valid VATID
        $this->clickAndWait("test_BasketNextStepTop");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_Step1");
        $this->assertEquals("5%", $this->getText("test_basket_Vat_1000_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_2"));
        $this->assertEquals("19%", $this->getText("test_basket_Vat_1003_3"));
        $this->assertEquals("11,97 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("9,18 €", $this->getText("test_basketVAT_10"));
        $this->assertEquals("2,38 €", $this->getText("test_basketVAT_5"));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxustid]", "BE0876797054");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_Step1");
        $this->assertEquals("0%", $this->getText("test_basket_Vat_1000_1"));
        $this->assertEquals("47,62 €", $this->getText("test_basket_Price_1000_1"));
        $this->assertEquals("0%", $this->getText("test_basket_Vat_1001_2"));
        $this->assertEquals("91,82 €", $this->getText("test_basket_Price_1001_2"));
        $this->assertEquals("0%", $this->getText("test_basket_Vat_1003_3"));
        $this->assertEquals("63,03 €", $this->getText("test_basket_Price_1003_3"));
        $this->assertEquals("0,00 €", $this->getText("test_basketVAT_0"));
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->clickAndWait("test_UserNextStepTop");
        $this->clickAndWait("test_Step1");
        $this->assertEquals("5%", $this->getText("test_basket_Vat_1000_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_2"));
        $this->assertEquals("19%", $this->getText("test_basket_Vat_1003_3"));
        $this->assertEquals("226,00 €", $this->getText("test_basketGross"));
        //vat is lover than before, becouse discount is applied for category products (1000, 1001) for Germany user
        $this->assertEquals("193,50 €", $this->getText("test_basketNet"));
        $this->assertEquals("11,44 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("8,78 €", $this->getText("test_basketVAT_10"));
        $this->assertEquals("2,28 €", $this->getText("test_basketVAT_5"));
    }

    /**
     * Product's accessories
     * @group navigation
     * @group basic
     */
    public function testFrontendAccessories()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");

        $this->assertEquals("Accessories", $this->getText("test_RightSideAccessoiresHeader"));
        $this->assertEquals("Art.No.: 1002", $this->getText("test_accessoire_No_1002"));
        $this->assertEquals("from 55,00 €", $this->getText("test_accessoire_Price_1002"));
        $this->clickAndWait("test_accessoire_pic_1002");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_accessoire_Title_1002");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_accessoire_details_1002");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_title_LastSeen_1000");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("compare", $this->getText("test_toCmp_accessoire_1002"));
        $this->clickAndWait("test_toCmp_accessoire_1002");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_accessoire_1002"));
        $this->clickAndWait("test_removeCmp_accessoire_1002");
        $this->assertEquals("compare", $this->getText("test_toCmp_accessoire_1002"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
    }

    /**
     * Product's similar products
     * @group navigation
     * @group basic
     */
    public function testFrontendSimilarProducts()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");

        $this->assertEquals("Similar Products", $this->getText("test_RightSideSimilListHeader"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_similarlist_No_1001"));
        $this->assertEquals("100,00 €", $this->getText("test_similarlist_Price_1001"));

        $this->clickAndWait("test_similarlist_pic_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_similarlist_Title_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_similarlist_details_1001");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_LastSeen_1000");
        $this->assertFalse($this->isElementPresent("test_RightBasketTitle_1001_1"));

        $this->clickAndWait("test_similarlist_toBasket_1001");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1001_1"));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("101,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("compare", $this->getText("test_toCmp_similarlist_1001"));

        $this->clickAndWait("test_toCmp_similarlist_1001");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_similarlist_1001"));

        $this->clickAndWait("test_removeCmp_similarlist_1001");
        $this->assertEquals("compare", $this->getText("test_toCmp_similarlist_1001"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
    }

    /**
     * Product's crossselling
     * @group navigation
     * @group basic
     */
    public function testFrontendCrossselling()
    {
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");

        $this->assertEquals("Have you seen ...?", $this->getText("test_RightSideCrossListHeader"));
        $this->assertEquals("Art.No.: 1003", $this->getText("test_cross_No_1003"));
        $this->assertEquals("75,00 €", $this->getText("test_cross_Price_1003"));

        $this->clickAndWait("test_cross_pic_1003");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_cross_Title_1003");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_cross_details_1003");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_title_LastSeen_1000");
        $this->clickAndWait("test_cross_toBasket_1003");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1003_1"));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("75,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("compare", $this->getText("test_toCmp_cross_1003"));

        $this->clickAndWait("test_toCmp_cross_1003");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_cross_1003"));

        $this->clickAndWait("test_removeCmp_cross_1003");
        $this->assertEquals("compare", $this->getText("test_toCmp_cross_1003"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
    }


    /**
     * Discounts for products (category, product and itm discounts)
     * @group discount
     * @group basic
     */
    public function testFrontendDiscounts()
    {
             $this->executeSql("UPDATE `oxshops` SET `OXDEFCAT` = '8a142c3e60a535f16.78077188' WHERE `OXID` = 'oxbaseshop';");
        $this->openShop();
        $this->clickAndWait("link=Wakeboarding");
        $this->clickAndWait("link=Bindings");
        $this->assertEquals("359,00 €*",$this->getText("test_price_action_058e613db53d782adfc9f2ccb43c45fe"));
        $this->assertEquals("259,00 €*",$this->getText("test_price_action_b56164c54701f07df14b141da197c207"));
        $this->assertEquals("159,00 €*",$this->getText("test_price_action_b5685a5230f5050475f214b4bb0e239b"));
        $this->clickAndWait("test_toBasket_action_058e613db53d782adfc9f2ccb43c45fe");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("359,00 €", $this->getText("test_basket_Price_058e613db53d782adfc9f2ccb43c45fe_1"));
        $this->assertEquals("19%", $this->getText("test_basket_Vat_058e613db53d782adfc9f2ccb43c45fe_1"));
        $this->assertEquals("359,00 €", $this->getText("test_basket_TotalPrice_058e613db53d782adfc9f2ccb43c45fe_1"));
        $this->assertFalse($this->isElementPresent("test_basketDiscount_1"));
        $this->check("test_removeCheck_058e613db53d782adfc9f2ccb43c45fe_1");
        $this->clickAndWait("test_basket_Remove");
        $this->clickAndWait("link=Home");
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("link=Cart");
        $this->assertFalse($this->isElementPresent("test_basketDiscount_1"));
        $this->assertFalse($this->isElementPresent("test_basketDiscount_2"));
        $this->assertFalse($this->isElementPresent("test_basketDiscount_3"));
        $this->assertFalse($this->isElementPresent("test_basketTitle_1003_3"));
        $this->type("lgn_usr", "birute_test@nfq.lt");
        $this->type("lgn_pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-5,00 €", $this->getText("test_basketDiscount_1"));
        $this->assertFalse($this->isElementPresent("test_basketDiscount_2"));
        $this->assertFalse($this->isElementPresent("test_basketDiscount_3"));
        $this->assertFalse($this->isElementPresent("test_basketTitle_1003_3"));
        $this->type("test_basketAm_1002-1_2", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-5,00 €", $this->getText("test_basketDiscount_1"));
        $this->assertTrue($this->isTextPresent("discount for product [EN] šÄßüл"));
        $this->assertEquals("-11,00 €", $this->getText("test_basketDiscount_2"));
        $this->assertFalse($this->isElementPresent("test_basketDiscount_3"));
        $this->assertFalse($this->isElementPresent("test_basketTitle_1003_3"));
        $this->type("test_basketAm_1000_1", "5");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_basketTitle_1003_3"));
        $this->assertEquals("Art.No.: 1003", $this->getText("test_basketNo_1003_3"));
        $this->assertEquals("1", $this->getText("test_basketAmount_1003_3"));
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-25,00 €", $this->getText("test_basketDiscount_1"));
        $this->assertTrue($this->isTextPresent("discount for product [EN] šÄßüл"));
        $this->assertEquals("-11,00 €", $this->getText("test_basketDiscount_2"));
        $this->assertEquals("1,50 €", $this->getText("test_basketDeliveryNet"));
        $this->assertEquals("360,00 €", $this->getText("test_basketGross"));
        $this->assertEquals("297,48 €", $this->getText("test_basketNet"));
        $this->assertEquals("15,81 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("10,71 €", $this->getText("test_basketVAT_5"));
        $this->assertEquals("325,50 €", $this->getText("test_basketGrandTotal"));
        //test for #1822
         $this->executeSql("UPDATE `oxdiscount` SET `OXACTIVE` = 1 WHERE `OXID` = 'testdiscount5';");
        $this->clickAndWait("test_Step1_Text");
        $this->clickAndWait("test_RightLogout");
        $this->check("test_removeCheck_1002-1_2");
        $this->clickAndWait("test_basket_Remove");
        $this->type("test_basketAm_1000_1", "1");
        $this->clickAndWait("test_basketUpdate");
        $this->assertTrue($this->isTextPresent("1 EN test discount šÄßüл:"));
        $this->assertEquals("-10,00 €", $this->getText("test_basketDiscount_1"));
        $this->type("test_basketAm_1000_1", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("-10,00 €", $this->getText("test_basketDiscount_1"));
    }

    /**
     * Itm discount for products (special case according Mantis#320)
     * @group discount
     * @group basic
     */
    public function testFrontendItmDiscounts()
    {
        $this->executeSql("DELETE FROM `oxobject2discount` WHERE `OXDISCOUNTID` = 'testitmdiscount'");
        $this->executeSql("UPDATE `oxdiscount` SET `OXAMOUNT` = 1 WHERE `OXID` = 'testitmdiscount'");
        $this->executeSql("UPDATE `oxdiscount` SET `OXACTIVE` = 1 WHERE `OXPRICE` = 200 AND `OXPRICETO` = 999999");
        $this->openShop();
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "5");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitleLink_1000_1"));
        $this->assertTrue($this->isElementPresent("test_RightBasketTitleLink_1003_2"));
        $this->assertEquals("250,00 €", $this->getText("test_RightBasketTotal"));
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_1"));
        $this->assertEquals("Art.No.: 1000", $this->getText("test_basketNo_1000_1"));
        $this->assertEquals("5", $this->getValue("test_basketAm_1000_1"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_basketTitle_1003_2"));
        $this->assertEquals("Art.No.: 1003", $this->getText("test_basketNo_1003_2"));
        $this->assertEquals("+1", $this->getText("test_basketAmount_1003_2"));
        $this->assertEquals("250,00 €", $this->getText("test_basketGross"));
        $this->assertTrue($this->isElementPresent("test_basketDiscount_1"), " Mantis #320. discount '10% ab 200 €o Einkaufswert' is active, but ignored");
        $this->assertEquals("-25,00 €", $this->getText("test_basketDiscount_1"), "test for Mantis #320");
        $this->assertEquals("225,00 €", $this->getText("test_basketGrandTotal"), "test for Mantis #320");
    }

    /**
     * sorting, paging and navigation in lists. Sorting is not available for lists
     * @group navigation
     * @group basic
     */
    public function testFrontendDisabledSorting()
    {
        //sorting is not available for list
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blShowSorting'");
        $this->openShop();
        //testing navigation in categories
        $this->clickAndWait("link=Gear");
        $this->clickAndWait("link=Fashion");
        $this->clickAndWait("link=For Him");
        $this->assertEquals("You are here: / Gear / Fashion / For Him", $this->getText("path"));
        $this->assertFalse($this->isElementPresent("test_sortTop_oxvarminprice_asc"));
        $this->assertFalse($this->isElementPresent("test_sortTop_oxtitle_asc"));
    }


    /**
     * sorting, paging and navigation in category
     * @group navigation
     * @group basic
     */
    public function testFrontendPagingAndNavigationCategory()
    {
        $this->openShop();
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kiteboards");

        $this->clickAndWait("test_ArtPerPageTop_10");
        $this->assertFalse($this->isElementPresent("test_PageNrTop_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        $this->clickAndWait("test_sortTop_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1301"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1303"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1304"));
        $this->clickAndWait("test_sortTop_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1304"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1303"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1301"));
        $this->clickAndWait("test_ArtPerPageTop_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='locatorlink_active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageTop"));
        $this->assertFalse($this->isElementPresent("test_PrevPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1304"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_NextPageTop");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertFalse($this->isElementPresent("test_PageNrTop_3"));
        $this->assertTrue($this->isElementPresent("test_PrevPageTop"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1303"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1301"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_PrevPageTop");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));


        //bottom navigation
        $this->clickAndWait("test_ArtPerPageBottom_10");
        $this->assertFalse($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->clickAndWait("test_sortBottom_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1301"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1303"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1304"));
        $this->clickAndWait("test_sortBottom_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1304"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1303"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1301"));
        $this->clickAndWait("test_ArtPerPageBottom_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrBottom_3' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_2"));
        $this->assertFalse($this->isElementPresent("test_PageNrBottom_3"));
        $this->assertTrue($this->isElementPresent("test_NextPageBottom"));
        $this->assertFalse($this->isElementPresent("test_PrevPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1304"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_NextPageBottom");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertFalse($this->isElementPresent("test_PageNrBottom_3"));
        $this->assertTrue($this->isElementPresent("test_PrevPageBottom"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1303"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1301"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_PrevPageBottom");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1302"));
    }

    /**
     * sorting, paging and navigation in manufacturers
     * @group navigation
     * @group basic
     */
    public function testFrontendPagingAndNavigationManufacturers()
    {
        $this->openShop();
        $this->clickAndWait("test_leftRootManufacturer");
            $this->clickAndWait("test_MoreSubCat_8");
        $this->assertEquals("You are here: / By Brand/Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("path"));
        $this->clickAndWait("test_ArtPerPageTop_10");
        $this->assertFalse($this->isElementPresent("test_PageNrTop_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));

        //top navigation
        $this->clickAndWait("test_sortTop_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1001"));
        $this->clickAndWait("test_sortTop_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1003"));
        $this->clickAndWait("test_ArtPerPageTop_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageTop"));
        $this->assertFalse($this->isElementPresent("test_PrevPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_NextPageTop");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PrevPageTop"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_PrevPageTop");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->clickAndWait("test_PageNrTop_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));

        //bottom navigation
        $this->clickAndWait("test_ArtPerPageBottom_10");
        $this->assertFalse($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->clickAndWait("test_sortBottom_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1001"));
        $this->clickAndWait("test_sortBottom_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1003"));
        $this->clickAndWait("test_ArtPerPageBottom_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageBottom"));
        $this->assertFalse($this->isElementPresent("test_PrevPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_"));
        $this->clickAndWait("test_NextPageBottom");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PrevPageBottom"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_PrevPageBottom");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->clickAndWait("test_PageNrBottom_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
    }

    /**
     * sorting, paging and navigation in distributors
     * @group navigation
     * @group basic
     */
    public function testFrontendPagingAndNavigationDistributors()
    {
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='bl_perfLoadVendorTree'");
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('9d1ee3af23795yytr96terf2d1e', 'oxbaseshop', 'bl_perfLoadVendorTree', 'bool', 0x93ea1218);" );
             $this->executeSql("UPDATE `oxshops` SET `OXDEFCAT` = '8a142c3e60a535f16.78077188' WHERE `OXID` = 'oxbaseshop';");
        $this->openShop();
        //testing navigation in Distributors
        $this->clickAndWait("test_leftRootVendor");
        $this->clickAndWait("test_MoreSubCat_1");
        $this->assertEquals("You are here: / By Distributor / Distributor [EN] šÄßüл", $this->getText("path"));
        $this->clickAndWait("test_ArtPerPageTop_10");
        $this->assertFalse($this->isElementPresent("test_PageNrTop_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        //top navigation
        $this->clickAndWait("test_sortTop_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1001"));
        $this->clickAndWait("test_sortTop_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->clickAndWait("test_ArtPerPageTop_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageTop"));
        $this->assertFalse($this->isElementPresent("test_PrevPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_NextPageTop");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PrevPageTop"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertFalse($this->isElementPresent("test_cntr_2"));
        $this->clickAndWait("test_PrevPageTop");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->clickAndWait("test_PageNrTop_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        //bottom navigation
        $this->clickAndWait("test_ArtPerPageBottom_10");
        $this->assertFalse($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->clickAndWait("test_sortBottom_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1001"));
        $this->clickAndWait("test_sortBottom_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->clickAndWait("test_ArtPerPageBottom_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageBottom"));
        $this->assertFalse($this->isElementPresent("test_PrevPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));
        $this->clickAndWait("test_NextPageBottom");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PrevPageBottom"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertFalse($this->isElementPresent("test_cntr_2"));
        $this->clickAndWait("test_PrevPageBottom");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->clickAndWait("test_PageNrBottom_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
    }

    /**
     * sorting, paging and navigation in search
     * @group navigation
     * @group basic
     */
    public function testFrontendPagingAndNavigationSearch()
    {
        $this->openShop();
        //testing navigation in search
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->clickAndWait("test_ArtPerPageTop_10");
        $this->assertFalse($this->isElementPresent("test_PageNrTop_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        //top navigation testing
        $this->assertFalse($this->isElementPresent("test_sortTop_oxartnum_asc"));
        $this->clickAndWait("test_sortTop_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1001"));
        $this->clickAndWait("test_sortTop_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1003"));
        //adding additional column for sorting
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba852e75e64cf5ccd4ae621339e6050ec87b19ce6db38ed423f15be38df275f14defb6bc896fb00bc26aa96142bfb1477086a97e1757f7c67c8360983dc14ab69d60d7 WHERE `OXVARNAME` = 'aSortCols'");
        $this->clickAndWait("test_Lang_Deutsch");
        $this->clickAndWait("test_sortTop_oxtitle_desc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1001"));
        $this->clickAndWait("test_sortTop_oxartnum_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1003"));
        // removing added sorting field
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba832f74e74df4cdd5af631238e7040fc97a18cf6cb28fd522f05ae28cf374f04ceeb7bd886eb10ac36ba86043beb02e WHERE `OXVARNAME` = 'aSortCols'");
        $this->clickAndWait("test_Lang_English");
        $this->clickAndWait("test_ArtPerPageTop_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageTop"));
        $this->assertFalse($this->isElementPresent("test_PrevPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1002"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1003"));
        $this->clickAndWait("test_NextPageTop");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PrevPageTop"));
        $this->assertFalse($this->isElementPresent("test_NextPageTop"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1000"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1001"));
        $this->clickAndWait("test_PrevPageTop");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->clickAndWait("test_PageNrTop_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Top"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        //bottom navigation
        $this->clickAndWait("test_ArtPerPageBottom_10");
        $this->assertFalse($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->clickAndWait("test_sortBottom_oxvarminprice_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1003"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1001"));
        $this->clickAndWait("test_sortBottom_oxtitle_asc");
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertTrue($this->isElementPresent("test_cntr_3_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_4_1003"));
        $this->clickAndWait("test_ArtPerPageBottom_2");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrTop_1' and @class='active']"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_1' and @class='active']"));
        $this->assertFalse($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_2"));
        $this->assertTrue($this->isElementPresent("test_NextPageBottom"));
        $this->assertFalse($this->isElementPresent("test_PrevPageBottomp"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1002"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1003"));
        $this->clickAndWait("test_NextPageBottom");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_PageNrBottom_1"));
        $this->assertTrue($this->isElementPresent("//a[@id='test_PageNrBottom_2' and @class='active']"));
        $this->assertTrue($this->isElementPresent("test_PrevPageBottom"));
        $this->assertFalse($this->isElementPresent("test_NextPageBottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
        $this->assertTrue($this->isElementPresent("test_cntr_2_1003"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1000"));
        $this->assertFalse($this->isElementPresent("test_cntr_3_1001"));
        $this->clickAndWait("test_PrevPageBottom");
        $this->assertEquals("Page 1 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1000"));
        $this->clickAndWait("test_PageNrBottom_2");
        $this->assertEquals("Page 2 / 2", $this->getText("test_listXofY_Bottom"));
        $this->assertTrue($this->isElementPresent("test_cntr_1_1002"));
    }

    /**
     * performing order when delivery country does not have any of payment methods
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderToOtherCountries()
    {
        $this->openShop();
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1000_1", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertEquals("Please select your shipping method", $this->getText("test_DeliveryHeader"));
        $this->assertTrue($this->isElementPresent("sShipSet"));
        $this->clickAndWait("test_Step2");
        $this->select("invadr[oxuser__oxcountryid]", "label=Spain");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertEquals("Payment Information", $this->getText("test_PaymentHeader"));
        $this->assertTrue($this->isTextPresent("Currently we have no shipping method set up for this country."));
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("Shipping carrier and payment method", $this->getText("test_ShipPaymentHeader"));
        $this->assertEquals("", $this->getText("test_orderShipping"));
        $this->assertEquals("Empty", $this->getText("test_orderPayment"));
        $this->click("test_OrderConfirmAGBBottom");
        sleep(1);
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
    }

    /**
     * testing conversion rate in order steps
     * @group navigation
     * @group order
     * @group basic
     */
    public function testFrontendConversionRateOn()
    {
        //option "Increase conversion rate: Don't display navigation bars in order process" is on in testing demodata
        $this->openShop();
        //making order
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1000_1", "3");
        $this->clickAndWait("test_basketUpdate");
        //order step 1
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertFalse($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertFalse($this->isElementPresent("//input[@id='f.search.param']"));
        $this->assertFalse($this->isElementPresent("//input[@id='searchParam']"));
        $this->assertFalse($this->isElementPresent("test_searchCategorySelect"));
        $this->assertFalse($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertFalse($this->isElementPresent("test_searchGo"));
        $this->assertFalse($this->isElementPresent("test_RightSideNewsLetterHeader"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_HeaderTerms"));
        $this->assertTrue($this->isElementPresent("test_HeaderImpressum"));
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftSideInfoHeader"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_RootCat_1"));
        $this->assertTrue($this->isElementPresent("test_link_service_contact"));
        $this->assertTrue($this->isElementPresent("test_TopAccMyAccount"));
        //order step 2
        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertFalse($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertFalse($this->isElementPresent("//input[@id='f.search.param']"));
        $this->assertFalse($this->isElementPresent("//input[@id='searchParam']"));
        $this->assertFalse($this->isElementPresent("test_searchCategorySelect"));
        $this->assertFalse($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertFalse($this->isElementPresent("test_searchGo"));
        $this->assertFalse($this->isElementPresent("test_RightSideNewsLetterHeader"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_HeaderTerms"));
        $this->assertTrue($this->isElementPresent("test_HeaderImpressum"));
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftSideInfoHeader"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_RootCat_1"));
        $this->assertTrue($this->isElementPresent("test_link_service_contact"));
        $this->assertTrue($this->isElementPresent("test_TopAccMyAccount"));
        //order step 3
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertFalse($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertFalse($this->isElementPresent("//input[@id='f.search.param']"));
        $this->assertFalse($this->isElementPresent("//input[@id='searchParam']"));
        $this->assertFalse($this->isElementPresent("test_searchCategorySelect"));
        $this->assertFalse($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertFalse($this->isElementPresent("test_searchGo"));
        $this->assertFalse($this->isElementPresent("test_RightSideNewsLetterHeader"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_HeaderTerms"));
        $this->assertTrue($this->isElementPresent("test_HeaderImpressum"));
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftSideInfoHeader"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_RootCat_1"));
        $this->assertTrue($this->isElementPresent("test_link_service_contact"));
        $this->assertTrue($this->isElementPresent("test_TopAccMyAccount"));
        $this->click("test_Payment_oxidcashondel");
        //order step4
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertFalse($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertFalse($this->isElementPresent("//input[@id='f.search.param']"));
        $this->assertFalse($this->isElementPresent("//input[@id='searchParam']"));
        $this->assertFalse($this->isElementPresent("test_searchCategorySelect"));
        $this->assertFalse($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertFalse($this->isElementPresent("test_searchGo"));
        $this->assertFalse($this->isElementPresent("test_RightSideNewsLetterHeader"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_HeaderTerms"));
        $this->assertTrue($this->isElementPresent("test_HeaderImpressum"));
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftSideInfoHeader"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_RootCat_1"));
        $this->assertTrue($this->isElementPresent("test_link_service_contact"));
        $this->assertTrue($this->isElementPresent("test_TopAccMyAccount"));
        //5th step has all info box again
        $this->click("test_OrderConfirmAGBBottom");
        sleep(1);
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertTrue($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertTrue($this->isElementPresent("//input[@id='f.search.param']")); //new templates
        $this->assertTrue($this->isElementPresent("test_searchCategorySelect"));
        $this->assertTrue($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertTrue($this->isElementPresent("test_searchGo"));
        $this->assertTrue($this->isElementPresent("test_RightSideNewsLetterHeader"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_HeaderTerms"));
        $this->assertTrue($this->isElementPresent("test_HeaderImpressum"));
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftSideInfoHeader"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertTrue($this->isElementPresent("test_link_service_contact"));
        $this->assertTrue($this->isElementPresent("test_TopAccMyAccount"));
    }

    /**
     * testing conversion rate in order steps
     * @group navigation
     * @group order
     * @group basic
     */
    public function testFrontendConversionRateOff()
    {
        //switching option "Increase conversion rate: Don't display navigation bars in order process" off
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blDisableNavBars'");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("//input[@id='f.search.param']", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");

        $this->clickAndWait("link=Cart");
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->assertTrue($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertTrue($this->isElementPresent("//input[@id='f.search.param']")); //new templates
        $this->assertTrue($this->isElementPresent("test_searchCategorySelect"));
        $this->assertTrue($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertTrue($this->isElementPresent("test_searchGo"));
        $this->assertTrue($this->isElementPresent("test_RightSideNewsLetterHeader"));
        $this->assertTrue($this->isElementPresent("test_RightBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_Curr_EUR"));
        $this->assertTrue($this->isElementPresent("test_HeaderTerms"));
        $this->assertTrue($this->isElementPresent("test_HeaderImpressum"));
        $this->assertTrue($this->isElementPresent("test_LeftSidePartnersHeader"));
        $this->assertTrue($this->isElementPresent("test_LeftSideInfoHeader"));
        $this->assertTrue($this->isElementPresent("test_link_footer_home"));
        $this->assertTrue($this->isElementPresent("test_RightSideAccountHeader"));
        $this->assertTrue($this->isElementPresent("test_Lang_English"));
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertTrue($this->isElementPresent("test_link_service_contact"));
        $this->assertTrue($this->isElementPresent("test_TopAccMyAccount"));
    }

    /**
     * Order steps: Step1. checking navigation and other additional info
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderStep1Navigation()
    {
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->select("selectList_Search_1001_0", "index=2");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->select("varSelect_Search_1002", "index=1");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));

        //Order Step1
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_basketTitle_1001_1"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл", $this->getText("test_basketTitle_1002-2_2"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_basketTitle_1003_3"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_4"));
        $this->assertEquals("Art.No.: 1000", $this->getText("test_basketNo_1000_4"));
        $this->assertEquals("Art.No.: 1001", $this->getText("test_basketNo_1001_1"));
        $this->assertEquals("Art.No.: 1002-2", $this->getText("test_basketNo_1002-2_2"));
        $this->assertEquals("Art.No.: 1003", $this->getText("test_basketNo_1003_3"));

        //testing navigation to details page
        $this->clickAndWait("test_basketPic_1000_4");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_basketTitle_1002-2_2");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("link=Cart");

        //removing some products
        $this->check("test_removeCheck_1000_4");
        $this->clickAndWait("test_basket_Remove");

        //navigation between order step1 and step2
        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->clickAndWait("test_Step1");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->clickAndWait("test_Step1");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));

        //testing "Other products that may interest you:" (bottom of basket step 1 page)
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_similar_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_similar_1000"));
        $this->clickAndWait("test_pic_similar_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_title_similar_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_details_similar_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_toBasket_similar_1000");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_4"));
        $this->check("test_removeCheck_1000_4");
        $this->clickAndWait("test_basket_Remove");
        $this->assertFalse($this->isElementPresent("test_basketTitle_1000_4"));
    }

    /**
     * Order steps: Step1. Calculation and Vouchers
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderStep1()
    {
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->select("selectList_Search_1001_0", "index=2");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->select("varSelect_Search_1002", "index=1");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->type("test_am_Search_1003", "6");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("link=Cart");

        //voucher
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->type("voucherNr", "222222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertTrue($this->isTextPresent("Your Coupon 222222 couldn't be accepted."));
        $this->assertTrue($this->isTextPresent("The coupon is not valid for your user group!"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertEquals("Redeem Coupon", $this->getText("test_VoucherHeader"));
        $this->type("voucherNr", "111111");
        $this->clickAndWait("test_basketVoucherAdd");

        //testing product with selection list
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getSelectedLabel("test_basketSelect_1001_1_0"));
        $this->assertEquals("98,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_1"));
        $this->assertEquals("98,00 €", $this->getText("test_basket_TotalPrice_1001_1"));
        $this->select("test_basketSelect_1001_1_0", "index=1");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("100,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_1"));
        $this->assertEquals("100,00 €", $this->getText("test_basket_TotalPrice_1001_1"));
        $this->select("test_basketSelect_1001_1_0", "index=3");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("102,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_1"));
        $this->assertEquals("102,00 €", $this->getText("test_basket_TotalPrice_1001_1"));
        $this->select("test_basketSelect_1001_1_0", "index=0");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("101,00 €", $this->getText("test_basket_Price_1001_1"));
        $this->assertEquals("10%", $this->getText("test_basket_Vat_1001_1"));
        $this->assertEquals("101,00 €", $this->getText("test_basket_TotalPrice_1001_1"));

        //testing product with staffelpreis
            $this->assertEquals("60,00 €", $this->getText("test_basket_Price_1003_3"));
            $this->assertEquals("19%", $this->getText("test_basket_Vat_1003_3"));
            $this->assertEquals("360,00 €", $this->getText("test_basket_TotalPrice_1003_3"));
            $this->assertEquals("6", $this->getValue("test_basketAm_1003_3"));
            $this->type("test_basketAm_1003_3", "1");
            $this->clickAndWait("test_basketUpdate");
            $this->assertEquals("75,00 €", $this->getText("test_basket_Price_1003_3"));
            $this->assertEquals("19%", $this->getText("test_basket_Vat_1003_3"));
            $this->assertEquals("75,00 €", $this->getText("test_basket_TotalPrice_1003_3"));
            $this->type("test_basketAm_1003_3", "6");
            $this->clickAndWait("test_basketUpdate");

        //discounts
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-10,00 €", $this->clearString($this->getText("test_basketDiscount_1")));
        $this->assertTrue($this->isTextPresent("discount for product [EN] šÄßüл"));
        $this->assertEquals("(No. 111111)", $this->getText("test_basketVoucher_1"));
        $this->assertEquals("- 10,00 €", $this->clearString($this->getText("test_basketVoucherDiscount_1")));
        $this->assertEquals("1,50 €", $this->getText("test_basketDeliveryNet"));
            $this->assertEquals("-42,70 €", $this->getText("test_basketDiscount_2"));
            $this->assertEquals("578,00 €", $this->getText("test_basketGross"));
            $this->assertEquals("516,80 €", $this->getText("test_basketGrandTotal"));
            $this->assertEquals("444,21 €", $this->getText("test_basketNet"));
            $this->assertEquals("8,19 €", $this->getText("test_basketVAT_10"));
            $this->assertEquals("60,78 €", $this->getText("test_basketVAT_19"));
            $this->assertEquals("2,12 €", $this->getText("test_basketVAT_5"));
        //voucher testing
        $this->type("voucherNr", "222222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertTrue($this->isTextPresent("Your Coupon 222222 couldn't be accepted."));
        $this->assertTrue($this->isTextPresent("Accumulation with coupons of other series is not allowed!"));
        $this->type("voucherNr", "111111");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertEquals("(No. 111111)", $this->clearString($this->getText("test_basketVoucher_1")));
        $this->assertEquals("- 10,00 €", $this->clearString($this->getText("test_basketVoucherDiscount_1")));
        $this->assertEquals("(No. 111111)", $this->clearString($this->getText("test_basketVoucher_2")));
        $this->assertEquals("- 10,00 €", $this->clearString($this->getText("test_basketVoucherDiscount_2")));
        $this->clickAndWait("test_basketVoucherRemove_1");
        $this->assertFalse($this->isElementPresent("test_basketVoucher_2"));
        $this->assertTrue($this->isElementPresent("test_basketVoucher_1"));
        $this->clickAndWait("test_basketVoucherRemove_1");
        $this->assertFalse($this->isElementPresent("test_basketVoucher_1"));
        $this->assertFalse($this->isElementPresent("test_basketVoucher_2"));
        $this->type("voucherNr", "222222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertEquals("(No. 222222)", $this->getText("test_basketVoucher_1"));
        $this->assertEquals("- 26,27 €", $this->clearString($this->getText("test_basketVoucherDiscount_1")));

        //removing few articles
        $this->check("test_removeCheck_1000_4");
        $this->check("test_removeCheck_1003_3");
        $this->clickAndWait("test_basket_Remove");
        $this->assertTrue($this->isElementPresent("test_basketTitle_1001_1"));
        $this->assertTrue($this->isElementPresent("test_basketTitle_1002-2_2"));
        $this->assertFalse($this->isElementPresent("test_basketTitle_1003_3"));
        $this->assertFalse($this->isElementPresent("test_basketTitle_1000_4"));

        //basket calculation
        $this->assertEquals("168,00 €", $this->getText("test_basketGross"));
        $this->assertTrue($this->isTextPresent("discount for category [EN] šÄßüл"));
        $this->assertEquals("-5,00 €", $this->getText("test_basketDiscount_1"));
        $this->assertEquals("136,53 €", $this->getText("test_basketNet"));
        $this->assertEquals("8,46 €", $this->getText("test_basketVAT_10"));
        $this->assertEquals("9,86 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("- 8,15 €", $this->clearString($this->getText("test_basketVoucherDiscount_1")));
        $this->assertEquals("1,50 €", $this->getText("test_basketDeliveryNet"));
        $this->assertEquals("156,35 €", $this->getText("test_basketGrandTotal"));
    }

    /**
     * Order steps: Step2 and Step3
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderStep2And3()
    {
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->select("varSelect_Search_1002", "index=0");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->select("varSelect_Search_1002", "index=1");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("link=Cart");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //Order Step1
        $this->type("voucherNr", "222222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->clickAndWait("test_BasketNextStepTop");
        //Order step2
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Billing Address"));
        $this->assertEquals("Mr", $this->getSelectedLabel("invadr[oxuser__oxsal]"));
        $this->assertEquals("UserNamešÄßüл", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("UserSurnamešÄßüл", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("UserCompany šÄßüл", $this->getValue("invadr[oxuser__oxcompany]"));
        $this->assertEquals("Musterstr.šÄßüл", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("79098", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("Musterstadt šÄßüл", $this->getValue("invadr[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("invadr[oxuser__oxustid]"));
        $this->assertEquals("User additional info šÄßüл", $this->getValue("invadr[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("0800 111111", $this->getValue("invadr[oxuser__oxfon]"));
        $this->assertEquals("0800 111112", $this->getValue("invadr[oxuser__oxfax]"));
        $this->assertEquals("0800 111114", $this->getValue("invadr[oxuser__oxmobfon]"));
        $this->assertEquals("0800 111113", $this->getValue("invadr[oxuser__oxprivfon]"));
        $this->assertEquals("01", $this->getValue("invadr[oxuser__oxbirthdate][day]"));
        $this->assertEquals("01", $this->getValue("invadr[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1980", $this->getValue("invadr[oxuser__oxbirthdate][year]"));
        $this->assertEquals("off", $this->getValue("test_newsReg"));
        $this->assertEquals("Here you can enter an optional message.", $this->getValue("order_remark"));
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertEquals("You are here: / Pay", $this->getText("path"));
        $this->clickAndWait("test_Step2");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->clickAndWait("test_UserNextStepBottom");
        //Order Step3
        $this->assertEquals("You are here: / Pay", $this->getText("path"));
        $this->assertEquals("Please select your shipping method", $this->getText("test_DeliveryHeader"));
        $this->assertEquals("Payment Method", $this->getText("test_PaymentHeader"));
        $this->assertEquals("Charges: 1,50 €", $this->getText("test_shipSetCost"));
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getSelectedLabel("sShipSet"));
        $this->assertEquals("Test payment method [EN] šÄßüл (0,70 €)", $this->getText("test_PaymentDesc_1"));
        $this->assertEquals("Short payment description [EN] šÄßüл", $this->getText("test_PaymentLongDesc_testpayment"));
        $this->assertEquals("off", $this->getValue("test_Payment_testpayment"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidcashondel"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidcreditcard"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxidpayadvance"));
        $this->assertFalse($this->isElementPresent("test_Payment_oxiddebitnote"));
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->assertEquals("", $this->getText("test_shipSetCost"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxidpayadvance"));
        $this->assertTrue($this->isElementPresent("test_Payment_oxiddebitnote"));
        $this->assertFalse($this->isElementPresent("test_Payment_testpayment"));
        $this->selectAndWait("sShipSet", "label=Test S&H set [EN] šÄßüл");
        $this->assertEquals("Charges: 1,50 €", $this->getText("test_shipSetCost"));
        $this->click("test_Payment_testpayment");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("You are here: / Complete Order", $this->getText("path"));
        $this->clickAndWait("test_Step3");
        $this->assertEquals("You are here: / Pay", $this->getText("path"));
    }

    /**
     * Order step4 (without any special checking for discounts, various VATs and user registration)
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderStep4()
    {
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->select("selectList_Search_1001_0", "index=0");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->select("varSelect_Search_1002", "index=1");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("link=Cart");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //Order Step1
        $this->type("voucherNr", "222222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->clickAndWait("test_BasketNextStepTop");
        //Order step2
        $this->clickAndWait("test_UserNextStepTop");
        //Order Step3
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getSelectedLabel("sShipSet"));
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        //Order Step4
        $this->assertTrue($this->isTextPresent("Please verify your input!"));
        $this->clickAndWait("test_orderWrapp_1001_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("test_WrapItemName_1001_3"));
        $this->assertEquals("Test card [EN] šÄßüл (0,20 €)", $this->getText("test_CardItemNamePrice_3"));
        $this->check("test_WrapItem_1001_3");
        $this->check("test_WrapItem_1002-2_3");
        $this->check("test_WrapItem_1001_NONE");
        $this->check("test_CardItem_3");
        $this->type("giftmessage", "Greeting card text");
        $this->clickAndWait("test_BackToOrder");
        //link to billing address
        $this->assertEquals("Billing Address E-mail: birute_test@nfq.lt UserCompany šÄßüл Mr UserNamešÄßüл UserSurnamešÄßüл User additional info šÄßüл Musterstr.šÄßüл 1 79098 Musterstadt šÄßüл Germany Phone: 0800 111111", $this->clearString($this->getText("test_orderBillAdress")));
        $this->assertTrue($this->isTextPresent("What I wanted to say ...:"));
        $this->clickAndWait("test_orderChangeBillAdress");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->type("order_remark", "what i wanted to say");
        $this->clickAndWait("test_UserNextStepTop");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertTrue($this->isTextPresent("what i wanted to say"));
        //link to shipping address
        $this->assertEquals("Shipping Address", $this->getText("test_orderShipAdress"));
        $this->clickAndWait("test_orderChangeShipAdress");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->clickAndWait("blshowshipaddress");
        $this->select("oxaddressid", "label=New Address");
        sleep(1);
        $this->checkForErrors();
        $this->type("deladr[oxaddress__oxfname]", "first");
        $this->type("deladr[oxaddress__oxlname]", "last");
        $this->type("deladr[oxaddress__oxcompany]", "company");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "1");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("Shipping Address company Mr first last street 1 3000 city Germany", $this->clearString($this->getText("test_orderShipAdress")));
        //link to payment method
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getText("test_orderShipping"));
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("test_orderPayment"));
        $this->clickAndWait("test_orderChangeShipping");
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->click("test_Payment_oxidpayadvance");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("Standard", $this->getText("test_orderShipping"));
        $this->assertEquals("Cash in advance", $this->getText("test_orderPayment"));
        $this->clickAndWait("test_orderChangePayment");
        $this->select("sShipSet", "label=Standard");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("Standard", $this->getText("test_orderShipping"));
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("test_orderPayment"));
        //testing displayed information
        $this->assertEquals("168,00 €", $this->getText("test_orderGrossPrice"));
        $this->assertEquals("Discount discount for category [EN] šÄßüл:", $this->clearString($this->getText("test_orderDiscountTitle_1")));
        $this->assertEquals("-5,00 €", $this->getText("test_orderDiscount_1"));
        $this->assertEquals("136,53 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("8,46 €", $this->getText("test_orderVat_10"));
        $this->assertEquals("9,86 €", $this->getText("test_orderVat_19"));
        $this->assertEquals("Coupon (No. 222222):", $this->clearString($this->getText("test_orderVoucherNr_1")));
        $this->assertEquals("-8,15 €", $this->getText("test_orderVoucher_1"));
        $this->assertEquals("0,00 €", $this->getText("test_orderShippingNet"));
        $this->assertEquals("7,50 €", $this->getText("test_orderPaymentNet"));
        $this->assertEquals("1,10 €", $this->getText("test_orderWrappNet"));
        $this->assertEquals("163,45 €", $this->getText("test_orderGrandTotal"));
    }

    /**
     * Order step5 (without any special checking for discounts, various VATs and user registration)
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderStep5()
    {
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->select("selectList_Search_1001_0", "index=0");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->select("varSelect_Search_1002", "index=1");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("link=Cart");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //Order Step1
        $this->type("voucherNr", "222222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->clickAndWait("test_BasketNextStepTop");
        //Order step2
        $this->clickAndWait("test_UserNextStepTop");
        //Order Step3
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getSelectedLabel("sShipSet"));
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        //Order Step4
        //rights of withdrawal
        $this->assertTrue($this->isElementPresent("test_OrderOpenWithdrawalBottom"));
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_PaymentNextStepBottom");
        //testing links to products
        $this->clickAndWait("test_orderPic_1001_1");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->clickAndWait("test_orderUrl_1002-2_2");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_PaymentNextStepBottom");
        //submit without checkbox
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertTrue($this->isTextPresent("Please read and confirm our terms and conditions."));
        //successful submit
        $this->check("test_OrderConfirmAGBBottom");
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertEquals("You are here: / Order completed", $this->getText("path"));
        //testing info in 5th page
        $this->assertTrue($this->isTextPresent("We registered your order under the number: 12"));
        $this->assertTrue($this->isElementPresent("test_BackToShop"));
         if (isSUBSHOP) {
            $sShopName ="subshop";
        } else {
            $sShopName ="OXID eShop 4";
        }
        $this->assertEquals("Back to Shop $sShopName.", $this->getText("test_BackToShop"));
        $this->clickAndWait("test_OrderHistory");
        $this->assertEquals("You are here: / My Account / Order History", $this->getText("path"));
        $this->assertEquals("Order History", $this->getText("test_accOrderHistoryHeader"));
        $this->assertEquals("Test product 1 [EN] šÄßüл test selection list [EN] šÄßüл : selvar1 [EN] šÄßüл +1,00 €", $this->clearString($this->getText("test_accOrderLink_12_1")));
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->clearString($this->getText("test_accOrderLink_12_2")));
    }

    /**
     * Testing giftWrapping selection
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderGiftWrapping()
    {
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->select("selectList_Search_1001_0", "index=0");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->select("varSelect_Search_1002", "index=1");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("link=Cart");
        //Order Step1
        $this->clickAndWait("test_BasketNextStepTop");
        //option 1 is available
        $this->assertTrue($this->isTextPresent("Option 1"));
        $this->assertTrue($this->isTextPresent("Option 2"));
        $this->assertTrue($this->isTextPresent("Option 3"));
        $this->assertTrue($this->isElementPresent("test_UsrOpt1"));
        //checking on option 'Disable order without registration.'
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='blOrderDisWithoutReg'");
             $this->executeSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('aa52bfdcd22416313ba', 'oxbaseshop', 'blOrderDisWithoutReg', 'bool', 0x93ea1218)");
        $this->clickAndWait("test_Step1");
        $this->clickAndWait("test_BasketNextStepTop");
        //Order step2
        $this->assertTrue($this->isTextPresent("Option 1"));
        $this->assertTrue($this->isTextPresent("Option 2"));
        $this->assertFalse($this->isTextPresent("Option 3"));
        $this->assertFalse($this->isElementPresent("test_UsrOpt1"));
        $this->type("test_UsrOpt2_usr", "birute_test@nfq.lt");
        $this->type("test_UsrOpt2_pwd", "useruser");
        $this->clickAndWait("test_UsrOpt2");
        $this->clickAndWait("test_UserNextStepTop");
        //Order Step3
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        //Order Step4
        //both wrapping and greeting cart exist
        $this->clickAndWait("test_orderWrapp_1001_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("test_WrapItemName_1001_3"));
        $this->assertEquals("Test card [EN] šÄßüл (0,20 €)", $this->getText("test_CardItemNamePrice_3"));
        $this->assertTrue($this->isElementPresent("test_CardItemNamePrice_1"));
        $this->assertTrue($this->isElementPresent("test_CardItemNamePrice_2"));
        $this->assertTrue($this->isElementPresent("giftmessage"));
        $this->clickAndWait("test_BackToOrder");
        $this->assertEquals("You are here: / Complete Order", $this->getText("path"));
        //only giftWrapping exist (none of greeging cards)
         $this->executeSql("DELETE FROM `oxwrapping` WHERE `OXTYPE` = 'CARD'");
        $this->clickAndWait("test_Step3");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->clickAndWait("test_orderWrapp_1001_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("test_WrapItemName_1001_3"));
        $this->assertFalse($this->isElementPresent("test_CardItemNamePrice_1"));
        $this->assertFalse($this->isElementPresent("test_CardItemNamePrice_2"));
        $this->assertFalse($this->isElementPresent("test_CardItemNamePrice_3"));
        $this->assertFalse($this->isElementPresent("giftmessage"));
        $this->clickAndWait("test_BackToOrder");
        //also removing wrapping. gift wrapping selection now is not accessible
         $this->executeSql("DELETE FROM `oxwrapping` WHERE `OXTYPE` = 'WRAP'");
        $this->clickAndWait("test_Step3");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertFalse($this->isElementPresent("test_orderWrapp_1001_1"));
    }


    /**
     * price category testing. Note, there is no functionality which would allow filters use in price category.
     * @group navigation
     * @group basic
     */
    public function testFrontendPriceCategory()
    {
        $this->openShop();
        $this->clickAndWait("link=price [EN] šÄßüл");
        $this->assertEquals("price [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("price category [EN] šÄßüл", $this->getText("test_catDesc"));
        $this->assertFalse($this->isElementPresent("test_catArtCnt")); //this now depends on option 'Display Number of contained Products behind Category Names'

        $this->clickAndWait("test_ArtPerPageTop_2");

        $this->assertTrue($this->isElementPresent("test_PageNrTop_1"));
        $this->clickAndWait("test_PageNrTop_1");
        $this->assertTrue($this->isElementPresent("test_cntr_1"));
        $this->assertTrue($this->isElementPresent("test_cntr_2"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));

        $this->assertTrue($this->isElementPresent("test_PageNrTop_2"));
        $this->clickAndWait("test_PageNrTop_2");
        $this->assertTrue($this->isElementPresent("test_cntr_1"));
        $this->assertTrue($this->isElementPresent("test_cntr_2"));
        $this->assertFalse($this->isElementPresent("test_cntr_3"));

        $this->assertTrue($this->isElementPresent("test_PageNrTop_3"));
        $this->clickAndWait("test_PageNrTop_3");
        $this->assertTrue($this->isElementPresent("test_cntr_1"));
        $this->assertFalse($this->isElementPresent("test_cntr_2"));

        $this->clickAndWait("test_ArtPerPageTop_10");
        $this->assertTrue($this->isElementPresent("test_cntr_1"));
        $this->assertTrue($this->isElementPresent("test_cntr_2"));
        $this->assertTrue($this->isElementPresent("test_cntr_3"));
        $this->assertTrue($this->isElementPresent("test_cntr_4"));
        $this->assertTrue($this->isElementPresent("test_cntr_5"));
        $this->assertFalse($this->isElementPresent("test_cntr_6"));
    }

    /**
     * Checking Performance options
     * option: Load "Customers who bought this product also purchased..."
     * @group navigation
     * @group basic
     */
    public function testFrontendPerfOptionsAlsoBought()
    {
        $this->openShop();
        //creating order
        $this->assertTrue($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertTrue($this->isElementPresent("test_leftRootVendor"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->clickAndWait("link=Cart");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");

        //Load "Customers who bought this product also purchased..."  is ON
        $this->clickAndWait("link=Home");
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->clickAndWait("test_title_FreshIn_1000");
        $this->assertTrue($this->isElementPresent("test_customerwho_Title_1001"));
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1000_1", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertTrue($this->isElementPresent("test_title_AlsoBought_1001"));

        //turning Load "Customers who bought this product also purchased..." OFF
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadCustomerWhoBoughtThis'");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->clickAndWait("test_title_FreshIn_1000");
        $this->assertFalse($this->isElementPresent("test_customerwho_Title_1001"));
        $this->clickAndWait("link=Cart");
        $this->type("test_basketAm_1000_1", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertFalse($this->isElementPresent("test_title_AlsoBought_1001"));
    }

    /**
     * Checking Performance options
     * option: Load Selection Lists in Product Lists
     * option: Support Price Modifications by Selection Lists
     * option: Load Selection Lists
     *
     * @group navigation
     * @group basic
     */
    public function testFrontendPerfOptionsSelectionLists()
    {
        $this->openShop();
        //page details. selection lists are with prices
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectList_WeekSpecial_1001_0")));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectList_action_1001_0")));
        //turning prices off
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfUseSelectlistPrice'");
        $this->openShop();
        $this->assertEquals("selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("selectList_WeekSpecial_1001_0")));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("selectList_action_1001_0")));

        // loading selection lists in product lists is OFF
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadSelectListsInAList'");
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("selectList_action_1001_0"));
        $this->clickAndWait("test_title_action_1001");
        $this->assertEquals("selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("test_select_1001_0")));

        //loading selection lists is OFF
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadSelectLists'");
        $this->openShop();
        $this->assertFalse($this->isElementPresent("selectList_WeekSpecial_1001_0"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("selectList_action_1001_0"));
        $this->clickAndWait("test_title_action_1001");
        $this->assertFalse($this->isElementPresent("test_select_1001_0"));
    }

    /**
     * Checking Performance options
     * @group navigation
     * @group basic
     */
    public function testFrontendPerfOptions()
    {
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x93ea1218 WHERE `OXVARNAME`='bl_perfShowActionCatArticleCnt'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadNews'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadNewsOnlyStart'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfShowTopBasket'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfShowLeftBasket'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfShowRightBasket'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadManufacturerTree'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadTreeForSearch'");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadDelivery'");
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_leftRootManufacturer"));
        $this->assertFalse($this->isElementPresent("test_searchCategorySelect"));
        $this->assertFalse($this->isElementPresent("test_searchManufacturerSelect"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_TopBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftSideNewsHeader"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл (2)");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->assertEquals("(2)", $this->getText("test_catArtCnt"));
        $this->assertEquals("Test category 0 [EN] šÄßüл (2)", $this->getText("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertEquals("Test category 1 [EN] šÄßüл (2)", $this->getText("test_BoxLeft_Cat_testcategory0_sub1"));
        $this->clickAndWait("test_title_action_1001");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("link=Cart");
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->selectAndWait("sShipSet", "label=Example Set1: UPS 48 hours");
        $this->assertEquals("", $this->getText("test_shipSetCost"));

        //option 'Show Prices in "Top of the Shop" and "Just arrived!" ' is OFF
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadPriceForAddList'");
        $this->openShop();
        $this->assertEquals("", $this->getText("test_price_FreshIn_1000"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_title_WeekSpecial_1001");
        $this->click("write_new_review");
        sleep(1);
        $this->type("rvw_txt", "review for article");
        $this->clickAndWait("test_reviewSave");
        $this->assertEquals("review for article", $this->getText("test_ReviewText_1"));
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadReviews'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadPrice'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadCrossselling'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadAccessoires'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadSimilar'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_showCompareList'");
        $this->openShop();
        $this->assertFalse($this->isTextPresent("review for article"));
        $this->assertFalse($this->isElementPresent("test_product_price"));
        $this->clickAndWait("link=Home");
        $this->clickAndWait("test_title_FreshIn_1000");
        $this->assertFalse($this->isElementPresent("test_RightSideAccessoiresHeader"));
        $this->assertFalse($this->isElementPresent("test_RightSideCrossListHeader"));
        $this->assertFalse($this->isElementPresent("test_RightSideSimilListHeader"));
        $this->assertFalse($this->isElementPresent("test_toCmp"));
        $this->clickAndWait("link=My Account");
        $this->assertFalse($this->isElementPresent("test_link_account_comparelist"));
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadAktion'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadCatTree'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadCurrency'");
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x7900fdf51e WHERE `OXVARNAME`='bl_perfLoadLanguages'");
        $this->open(shopURL."/_cc.php");
        $this->checkForErrors();
        $this->assertFalse($this->isElementPresent("test_Lang_English"));
        $this->assertFalse($this->isElementPresent("link=€"));
        $this->assertFalse($this->isElementPresent("test_Curr_EUR"));
        $this->assertFalse($this->isElementPresent("link=Test category 0 [EN] šÄßüл (2)"));
        $this->assertFalse($this->isElementPresent("link=Test category 0 [EN] šÄßüл(2)"));
        $this->assertFalse($this->isElementPresent("test_ArtSubHeader_WeekSpecial_1001"));
        $this->assertFalse($this->isElementPresent("test_FreshIn"));
        $this->assertFalse($this->isElementPresent("test_ArtSubHeaderTitleLink_CatArticle_1003"));
    }

    /**
     * creating listmania
     * @group navigation
     * @group basic
     */
    public function testFrontendListmaniaCreating()
    {
        //deleting exsisting recommlists for better possibility to test creating of new recomlist
         $this->executeSql( "delete from oxrecommlists" );
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("//input[@id='f.search.param']", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->clickAndWait("test_Recommlist");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_recommlistAddTitle"));
        $this->assertEquals("There is no Listmania lists at the moment. To create new, click here.", $this->getText("test_recommlistAdd"));
        $this->clickAndWait("test_recommlistAddHere");
        //creating listmania in MyAccount
        $this->assertEquals("My Listmania List", $this->getText("test_link_account_recommlist"));
        $this->assertEquals("Lists: 0", $this->getText("test_link_account_recommlistDesc"));
        $this->clickAndWait("test_link_account_recommlist");
        $this->assertEquals("New Listmania list", $this->getText("test_recomListHeader1"));
        $this->assertEquals("", $this->getValue("recomm_title"));
        $this->assertEquals("UserNamešÄßüл UserSurnamešÄßüл", $this->getValue("recomm_author"));
        $this->assertEquals("", $this->getValue("recomm_desc"));
        $this->assertEquals("My Listmania lists", $this->getText("test_recomListHeader2"));
        $this->assertEquals("No Listmania Lists found", $this->getText("test_recommlists"));
        $this->type("recomm_title", "recomm title1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_desc", "recom introduction1");
        $this->clickAndWait("test_recomListSave");
        $this->assertEquals("Lists: 1", $this->getText("test_link_account_recommlistDesc"));
        $this->assertEquals("recomm title1", $this->getText("test_recomListTitle_1"));
        $this->assertTrue($this->isTextPresent("recomm title1: A List by recomm author1"));
        $this->assertTrue($this->isTextPresent("recom introduction1"));
    }

    /**
     * Checking Listmania
     * @group navigation
     * @group basic
     */
    public function testFrontendListmaniaInfo()
    {
         $this->executeSql( "DELETE FROM `oxreviews` WHERE `OXID` = 'testrecomreview'" );
         $this->executeSql( "DELETE FROM `oxratings` WHERE `OXID` = 'testrecomrating'" );
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //checking small right listmania box
        $this->clickAndWait("test_leftRootManufacturer");
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");

        $this->assertEquals("Listmania", $this->getText("test_RightSideRecommlistHeader"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("Listmania", $this->getText("test_RightSideRecommlistHeader"));
        $this->clickAndWait("test_RightSideRecommlistTitle_1");
        $this->assertEquals("recomm title (A List by recomm author)", $this->getText("test_recommlistHeaderAuthor"));
        $this->clickAndWait("test_Lang_Deutsch");
        $this->assertEquals("recomm title (eine Liste von recomm author)", $this->getText("test_recommlistHeaderAuthor"));
        $this->clickAndWait("test_Lang_English");
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->clickAndWait("test_title_action_1000");
        $this->assertEquals("Listmania", $this->getText("test_RightSideRecommlistHeader"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->clickAndWait("test_RightSideRecommlistPic_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Listmania", $this->getText("test_RightSideRecommlistHeader"));
        $this->assertEquals("recomm title", $this->getText("test_RightSideRecommlistTitle_1"));
        $this->assertEquals("A List by: recomm author", $this->getText("test_RightSideRecommlistNo_1"));
        //writing recommendation for listmania
        $this->clickAndWait("test_RightSideRecommlistTitle_1");
        $this->assertEquals("You are here: / Recomendation List", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssRecommListProducts']"));
        $this->assertEquals("recomm title (A List by recomm author)", $this->getText("test_recommlistHeaderAuthor"));
        $this->assertEquals("recom introduction", $this->getText("test_recommlistDesc"));
        $this->assertTrue($this->isTextPresent("No review available for this item."));
        $this->assertEquals("No ratings.", $this->getText("star_rating_text"));
        $this->click("write_new_review");
        sleep(1);
        $this->click("//ul[@id='star_rate']/li[@class='s3']/a");
        sleep(1);
        $this->type("rvw_txt", "recommendation for this list");
        $this->clickAndWait("test_reviewSave");
        $this->assertEquals("You are here: / Recomendation List", $this->getText("path"));
        $this->assertEquals("UserNamešÄßüл writes:", $this->getText("test_ReviewName_1"));
        $this->assertEquals("recommendation for this list", $this->getText("test_ReviewText_1"));
        $this->assertEquals("Rating: 3", $this->getText("test_ReviewRating_1"));
        $this->assertEquals("Date: ".date("d.m.Y"), $this->getText("test_ReviewDate_1"));
        $this->assertEquals("1 Rating", $this->getText("star_rating_text"));
        //checking product links in recommendation list
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("test_no_1"));
        $this->assertEquals("comment for product 1000", $this->getText("test_text_1"));
        $this->assertEquals("50,00 €*", $this->getText("test_price_1"));
        $this->clickAndWait("test_pic_1");
        $this->assertEquals("You are here: / Recomendation List", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_RightSideRecommlistTitle_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->clickAndWait("test_title_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_RightSideRecommlistTitle_1");
        $this->clickAndWait("test_details_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_RightSideRecommlistTitle_1");
        $this->type("test_am_1", "2");
        $this->clickAndWait("test_toBasket_1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("You are here: / Recomendation List", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->type("test_am_1", "1");
        $this->clickAndWait("test_toBasket_1");
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("3", $this->getText("test_RightBasketItems"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("compare", $this->getText("test_toCmp_1"));
        $this->clickAndWait("test_toCmp_1");
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("You are here: / Recomendation List", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_1"));
        $this->clickAndWait("test_removeCmp_1");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->assertEquals("You are here: / Recomendation List", $this->getText("path"));
        $this->assertEquals("compare", $this->getText("test_toCmp_1"));
    }

    /**
     * Checking Listmania
     * @group navigation
     * @group basic
     */
    public function testFrontendListmaniaAddSearch()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //adding other products to listmania
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->clickAndWait("test_Recommlist");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_recommlistAddTitle"));
        $this->select("test_recomListAddSelect", "label=recomm title");
        $this->type("test_recommlistAddText", "comment for product 1001");
        $this->clickAndWait("test_recommlistAddToList");
        $this->type("//input[@id='f.search.param']", "1002");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1002");
        $this->clickAndWait("test_Recommlist");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_recommlistAddTitle"));
        $this->select("test_recomListAddSelect", "label=recomm title");
        $this->type("test_recommlistAddText", "comment for product 1002");
        $this->clickAndWait("test_recommlistAddToList");

        //search in listmania
        $this->type("searchRecomm", "title");
        $this->clickAndWait("test_searchRecommlist");
        $this->assertEquals("title", $this->getValue("searchrecomm"));
        $this->assertEquals("You are here: / Recomendation List / Search for \"title\"", $this->getText("path"));
        $this->assertEquals("1 Hits for \"title\"", $this->getText("test_recomListHeader2"));
        $this->assertEquals("recomm title", $this->getText("test_recomListTitle_1"));
        $this->assertTrue($this->isTextPresent("recomm title: A List by recomm author"));
        $this->assertEquals("More...", $this->getText("test_recomListMore_1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_recomListArtTitle_1_1000"));
        $this->assertEquals("50,00 €*", $this->getText("test_recomListArtPrice_1_1000"));
        $this->clickAndWait("test_recomListPic_1_1000");
        $this->assertEquals("You are here: / Recomendation List / Search for \"title\"", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->type("searchRecomm", "title");
        $this->clickAndWait("test_searchRecommlist");
        $this->clickAndWait("test_recomListArtTitle_1_1001");
        $this->assertEquals("You are here: / Recomendation List / Search for \"title\"", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->type("searchRecomm", "title");
        $this->clickAndWait("test_searchRecommlist");
        $this->clickAndWait("test_recomListMore_1");
        $this->assertEquals("You are here: / Recomendation List / Search for \"title\"", $this->getText("path"));
        $this->assertEquals("recomm title (A List by recomm author)", $this->getText("test_recommlistHeaderAuthor"));
        $this->assertEquals("recom introduction", $this->getText("test_recommlistDesc"));
        $this->assertEquals("Write a Review", $this->getText("test_reviewHeader"));
        $this->assertEquals("recommendation for this list", $this->getText("test_ReviewText_1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_2"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_title_3"));
        $this->assertEquals("title", $this->getValue("searchRecomm"));
        $this->type("searchRecomm", "no entry");
        $this->clickAndWait("test_searchRecommlist");
        $this->assertEquals("no entry", $this->getValue("searchrecomm"));
        $this->assertEquals("You are here: / Recomendation List / Search for \"no entry\"", $this->getText("path"));
        $this->assertEquals("0 Hits for \"no entry\"", $this->getText("test_recomListHeader2"));
        $this->assertEquals("No Listmania Lists found", $this->getText("test_recommlists"));

        //editing listmania (with articles)
        $this->clickAndWait("test_link_footer_account");
        $this->clickAndWait("test_link_account_recommlist");
        $this->clickAndWait("test_recomListEdit_1");
        $this->assertEquals("recomm title", $this->getText("test_recomListHeader1"));
        $this->assertEquals("recomm title", $this->getValue("recomm_title"));
        $this->assertEquals("recomm author", $this->getValue("recomm_author"));
        $this->assertEquals("recom introduction", $this->getValue("recomm_desc"));
        $this->type("recomm_desc", "recom introduction1");
        $this->type("recomm_author", "recomm author1");
        $this->type("recomm_title", "recomm title1");
        $this->clickAndWait("test_recomListSave");
        $this->assertTrue($this->isTextPresent("Recommendation list changes saved"));
    }

    /**
     * Checking Listmania
     * @group navigation
     * @group basic
     */
    public function testFrontendListmaniaDelete()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //adding other products to listmania
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1001");
        $this->clickAndWait("test_Recommlist");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_recommlistAddTitle"));
        $this->select("test_recomListAddSelect", "label=recomm title");
        $this->type("test_recommlistAddText", "comment for product 1001");
        $this->clickAndWait("test_recommlistAddToList");
        $this->clickAndWait("test_link_footer_account");
        $this->clickAndWait("test_link_account_recommlist");
        $this->clickAndWait("test_recomListEdit_1");

        //removing articles from list
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_2"));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectList_2_0")));
        $this->clickAndWait("test_remove_2");
        $this->assertEquals("recomm title", $this->getText("test_recomListHeader1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_title_1"));
        $this->assertFalse($this->isElementPresent("test_title_2"));
        $this->clickAndWait("test_link_account_recommlist");
        $this->assertEquals("recomm title", $this->getText("test_recomListTitle_1"));
        $this->assertTrue($this->isTextPresent("recomm title: A List by recomm author"));
        $this->assertTrue($this->isTextPresent("recom introduction"));

        //deleting recom list (creating one more before)
        $this->type("recomm_title", "second title");
        $this->type("recomm_author", "second author");
        $this->type("recomm_desc", "second desc");
        $this->clickAndWait("test_recomListSave");
        $this->assertEquals("Lists: 2", $this->getText("test_link_account_recommlistDesc"));
        $this->assertEquals("recomm title", $this->getText("test_recomListTitle_1"));
        $this->assertTrue($this->isTextPresent("recomm title: A List by recomm author"));
        $this->assertTrue($this->isTextPresent("recom introduction"));
        $this->assertEquals("second title", $this->getText("test_recomListTitle_2"));
        $this->assertTrue($this->isTextPresent("second title: A List by second author"));
        $this->assertTrue($this->isTextPresent("second desc"));
        $this->clickAndWait("test_recomListDelete_1");
        $this->assertFalse($this->isTextPresent("recomm title: A List by recomm author"));
        $this->assertFalse($this->isTextPresent("recom introduction"));
        $this->assertEquals("Lists: 1", $this->getText("test_link_account_recommlistDesc"));
        $this->assertFalse($this->isElementPresent("test_recomListTitle_2"));
        $this->assertEquals("second title", $this->getText("test_recomListTitle_1"));
        $this->assertTrue($this->isTextPresent("second title: A List by second author"));
        $this->assertTrue($this->isTextPresent("second desc"));
    }

    /**
     * Listmania is disabled via performance options
     * @group navigation
     * @group basic
     */
    public function testFrontendDisabledListmania()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showListmania'");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->assertFalse($this->isElementPresent("test_RightSideRecommlistHeader"));
        $this->assertFalse($this->isElementPresent("test_RightSideRecommlistTitle_1"));
        $this->clickAndWait("test_title_Search_1000");
        $this->assertFalse($this->isElementPresent("test_RightSideRecommlistHeader"));
        $this->assertFalse($this->isElementPresent("test_RightSideRecommlistTitle_1"));
        $this->assertFalse($this->isElementPresent("searchRecomm"));
        $this->assertTrue($this->isElementPresent("linkToWishList"));
        $this->assertTrue($this->isElementPresent("linkToNoticeList"));
        $this->assertFalse($this->isElementPresent("test_Recommlist"));
        $this->clickAndWait("test_TopAccMyAccount");
        $this->assertTrue($this->isElementPresent("test_link_account_noticelist"));
        $this->assertTrue($this->isElementPresent("test_link_account_wishlist"));
        $this->assertFalse($this->isElementPresent("test_link_account_recommlist"));
    }

    /**
     * Checking Top Menu Navigation
     * @group navigation
     * @group basic
     */
    public function testFrontendTopMenu()
    {
        $this->openShop();
        $this->assertFalse($this->isElementPresent("root1"));
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0xb6   WHERE `OXVARNAME` = 'iTopNaviCatCount';");
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blTopNaviLayout';");
        $this->openShop();
        $this->assertTrue($this->isElementPresent("root2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertEquals("more", $this->getText("root3"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("root2"));
        $this->clickAndWait("root2");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_BoxLeft_Cat_testcategory0_1"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_BoxLeft_Cat_testcategory0_sub1"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_Top_root2_SubCat_1"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->clickAndWait("test_Top_root2_SubCat_1"); //new templates
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_catTitle"));
        $this->clickAndWait("root3");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_CatRoot_2"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_CatRoot_2_SubCat_1"));
    }

    /**
     * Checking contact sending
     * @group navigation
     * @group basic
     */
    public function testFrontendContact()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='iUseGDVersion'");
        $this->openShop();
        $this->clickAndWait("test_link_footer_contact");
        $this->assertTrue($this->isTextPresent("You are here: / Contact"));
        $this->assertEquals("Contact", $this->getText("test_contactHeader"));
        $this->assertEquals("Mr Mrs", $this->clearString($this->getText("editval[oxuser__oxsal]")));
        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("editval[oxuser__oxfname]", "first name");
        $this->type("editval[oxuser__oxlname]", "last name");
        $this->type("test_contactEmail", "birute_test@nfq.lt");
        $this->type("c_subject", "subject");
        $this->type("c_message", "message text");
        $this->type("c_mac", "");
        $this->clickAndWait("test_contactSend");
        $this->assertTrue($this->isTextPresent("Please complete all fields marked with a"));
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("first name", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("last name", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getValue("test_contactEmail"));
        $this->assertEquals("subject", $this->getValue("c_subject"));
        $this->assertEquals("message text", $this->getValue("c_message"));
        $this->type("c_mac", $this->getText("test_verificationCode"));
        $this->clickAndWait("test_contactSend");
        $this->assertTrue($this->isTextPresent("Thank you."));
        $this->assertTrue($this->isTextPresent("You are here: / Contact"));
    }

    /**
     * Checking CMS pages marked as categories
     * @group navigation
     * @group basic
     */
    public function testFrontendCmsAsCategories()
    {
        //activating CMS pages as categories
         $this->executeSql("UPDATE `oxcontents` SET `OXACTIVE`=1, `OXACTIVE_1`=1 WHERE `OXID` = 'testcontent1' OR `OXID` = 'testcontent2' OR `OXID` = 'oxsubshopcontent1' OR `OXID` = 'oxsubshopcontent2'");
        //testing when side menu is on
        $this->openShop();
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        //cms as subcategory
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("test_BoxLeft_Cat_testcategory0_2"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cms_testcategory0_sub1"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_BoxLeft_Cms_testcategory0_sub1"));
        $this->clickAndWait("test_BoxLeft_Cms_testcategory0_sub1");
        $this->assertEquals("You are here: / 3 [EN] content šÄßüл", $this->getText("path"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_contentHeader"));
        $this->assertEquals("content [EN] last šÄßüл", $this->getText("test_contentBody"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_MoreSubCms_1_1"));
        $this->clickAndWait("test_MoreSubCms_1_1");
        $this->assertEquals("You are here: / 3 [EN] content šÄßüл", $this->getText("path"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_contentHeader"));
        $this->assertEquals("content [EN] last šÄßüл", $this->getText("test_contentBody"));
        //cms as root category
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cms_testcategory0_1"), "bug from mantis #494");
        $this->clickAndWait("test_BoxLeft_Cms_testcategory0_1");
        $this->assertEquals("You are here: / [last] [EN] content šÄßüл", $this->getText("path"));
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getText("test_contentHeader"));
        $this->assertEquals("content [EN] 1 šÄßüл", $this->getText("test_contentBody"));
        //activating top menu navigation
         $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME` = 'blTopNaviLayout';");
             $this->executeSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('05acidyc1e85609d1e1e9qw346', 'oxbaseshop', 'blTopNaviLayout', 'bool', 0x93ea1218);");
        $this->openShop();
        $this->assertTrue($this->isElementPresent("root2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        //cms as root category
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getText("root2"));
        $this->clickAndWait("root2");
        $this->assertEquals("You are here: / [last] [EN] content šÄßüл", $this->getText("path"));
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getText("test_contentHeader"));
        $this->assertEquals("content [EN] 1 šÄßüл", $this->getText("test_contentBody"));
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getText("test_BoxLeft_Cms_0"));
        //cms as subcategory
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_Top_root3_Cms_1_1"));
        $this->clickAndWait("test_Top_root3_Cms_1_1");
        $this->assertEquals("You are here: / 3 [EN] content šÄßüл", $this->getText("path"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_contentHeader"));
        $this->assertEquals("content [EN] last šÄßüл", $this->getText("test_contentBody"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_BoxLeft_Cms_testcategory0_sub1"));
        //selecting parent category
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_1");
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("test_MoreSubCms_1_1"), "bug from Mantis #495");
    }

    /**
     * Checking option 'Display Message when Product is added to Cart ' from Core settings -> System
     * @group navigation
     * @group basic
     */
    public function testFrontendMessageWhenProductIsAddedToCart()
    {
        $this->openShop();
        //nothing hapens, when product is added to basket
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertFalse($this->isTextPresent("Product Test product 1 [EN] šÄßüл was added to the Cart."));
        $this->assertFalse($this->isElementPresent("popup"));
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        //displaying message, when product is added to basket
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'iNewBasketItemMessage';");
        $this->openShop();
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertTrue($this->isTextPresent("Product Test product 1 [EN] šÄßüл was added to the Cart."));
        $this->assertFalse($this->isElementPresent("popup"));
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        //redirecting to basket
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` =  0xb0 WHERE `OXVARNAME` = 'iNewBasketItemMessage';");
        $this->openShop();
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->assertFalse($this->isTextPresent("Product Test product 1 [EN] šÄßüл was added to the Cart."));
        $this->assertFalse($this->isElementPresent("popup"));
        $this->assertEquals("You are here: / View Cart", $this->getText("path"), "bug from mantis #517");
        $this->clickAndWait("test_HeaderHome");
        $this->assertEquals("1", $this->getText("test_RightBasketItems"));
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        //opening pop up, when product is added to basket
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` =  0xb6 WHERE `OXVARNAME` = 'iNewBasketItemMessage';");
        $this->openShop();
        $this->click("test_toBasket_WeekSpecial_1001");
        sleep(3);
        $this->assertFalse($this->isTextPresent("Product Test product 1 [EN] šÄßüл was added to the Cart."));
        $this->assertTrue($this->isElementPresent("test_popupCart"));
        $this->openShop();
        $this->click("test_toBasket_WeekSpecial_1001");
        sleep(3);
        $this->assertFalse($this->isTextPresent("Product Test product 1 [EN] šÄßüл was added to the Cart."));
        $this->assertTrue($this->isElementPresent("test_popupContinue"));
    }

    /**
     * Guestbook spam control
     * @group navigation
     * @group basic
     */
    public function testFrontendGuestbookSpamProtection()
    {
        //setting spam protection 2 entries per day
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0xb6 WHERE `OXVARNAME` = 'iMaxGBEntriesPerDay';");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_link_footer_guestbook");
        $this->assertEquals("Write entry", $this->getText("test_guestbookWriteHeader"));
        $this->assertTrue($this->isElementPresent("//input[@value='Click here to write an entry']"));
        $this->clickAndWait("//input[@value='Click here to write an entry']");
        $this->type("rvw_txt", "guestbook entry No. 1");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 1"));
        $this->assertTrue($this->isElementPresent("//input[@value='Click here to write an entry']"));
        $this->clickAndWait("//input[@value='Click here to write an entry']");
        $this->type("rvw_txt", "guestbook entry No. 2");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 2"));
        $this->assertFalse($this->isElementPresent("//input[@value='Click here to write an entry']"));
        $this->clickAndWait("test_link_footer_guestbook");
        $this->assertFalse($this->isElementPresent("//input[@value='Click here to write an entry']"));
        //increasing guestbook entries limit
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07c4 WHERE `OXVARNAME` = 'iMaxGBEntriesPerDay';");
        $this->clickAndWait("test_link_footer_guestbook");
        $this->assertEquals("Write entry", $this->getText("test_guestbookWriteHeader"));
        $this->assertTrue($this->isElementPresent("//input[@value='Click here to write an entry']"));
        $this->clickAndWait("//input[@value='Click here to write an entry']");
        $this->type("rvw_txt", "guestbook entry No. 3");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 3"));
        $this->assertTrue($this->isElementPresent("//input[@value='Click here to write an entry']"));
        $this->clickAndWait("//input[@value='Click here to write an entry']");
        $this->type("rvw_txt", "guestbook entry No. 4");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 4"));
        $this->assertTrue($this->isElementPresent("//input[@value='Click here to write an entry']"));
    }



    /**
     * Orders: buying more items than available
     * @group order
     * @group user
     * @group navigation
     * @group basic
     */
    public function testFrontendOrderStep1BuyingLimit()
    {
            $this->loginAdmin("Administer Products", "Products");
            $this->assertEquals("English", $this->getSelectedLabel("changelang"));
            $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
            $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
            $this->type("where[oxarticles][oxartnum]", "1002");
            $this->clickAndWait("submitit");
            $this->openTab("link=1002");
            $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
            $this->type("editval[oxarticles__oxtitle]", "[DE 2] Test product 2 šÄßüл €");
            $this->clickAndWait("saveArticle");
            $this->assertEquals("[DE 2] Test product 2 šÄßüл €", $this->getValue("editval[oxarticles__oxtitle]"));
         $this->executeSql( "UPDATE `oxarticles` SET `OXSTOCKFLAG` = 3 WHERE `OXID` LIKE '1002%'" );
        $this->openShop();
        //adding products to the basket
        $this->type("//input[@id='f.search.param']", "1002");
        $this->clickAndWait("test_searchGo");
            $this->clickAndWait("test_Lang_Deutsch");
            $this->assertEquals("[DE 2] Test product 2 šÄßüл €", $this->getText("test_title_Search_1002"));
            $this->clickAndWait("test_Lang_English");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_toBasket_Search_1002"), "Button Add to basket is gone for variant product");
        $this->type("test_am_Search_1002", "10");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->assertEquals("5", $this->getText("test_RightBasketItems"));
        $this->clickAndWait("link=Cart");
        $this->assertFalse($this->isTextPresent("No enought items of this article in stock! Available: 5"));
        $this->assertEquals("5", $this->getValue("test_basketAm_1002-1_1"));
        $this->assertEquals("275,00 €", $this->getText("test_basketGrandTotal"));
        $this->type("test_basketAm_1002-1_1", "10");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("5", $this->getValue("test_basketAm_1002-1_1"));
        $this->assertEquals("Not enough items of this product in stock! Available: 5", $this->getText("test_basket_StockError_1002-1_0"));
        $this->assertEquals("275,00 €", $this->getText("test_basketGrandTotal"));
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("5", $this->getValue("test_basketAm_1002-1_1"));
        $this->assertFalse($this->isTextPresent("Not enough items of this product in stock! Available: 5"));
        $this->assertEquals("275,00 €", $this->getText("test_basketGrandTotal"));
        $this->type("test_basketAm_1002-1_1", "1");
        $this->clickAndWait("test_basketUpdate");
        $this->assertFalse($this->isTextPresent("Not enough items of this product in stock! Available: 5"));
        $this->assertEquals("1", $this->getValue("test_basketAm_1002-1_1"));
        $this->assertEquals("55,00 €", $this->getText("test_basketGrandTotal"));
    }

    /**
     * Frontend: various possible errors (expired license, exceeded etc)
     * @group navigation
     * @group basic
     */
    public function testFrontendPossibleErrors()
    {
    }

    /**
     * Frontend: product is sold out by other user during order process.
     * testing if no fatal errors or exceptions are thrown
     * @group order
     * @group basic
     */
    public function testFrontendOutOfStockOfflineProductDuringOrder()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->clickAndWait("test_TopBasketHeader");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_1"));
        $this->type("test_basketAm_1000_1", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("90,00 €", $this->getText("test_basketGrandTotal"));
        //product is already in the basket. making product out of stock now
         $this->executeSql("UPDATE `oxarticles` SET `OXSTOCK` = '0', `OXSTOCKFLAG` = '2' WHERE `OXID` = '1000';");
        //in second step, now it is checked, if product is still available
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        //in 3rd step, if continued, it will be redirected to home page
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));
        $this->assertTrue($this->isTextPresent("You are here: / Home "));
        //product is in stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '2' WHERE `OXID` = '1000';");
        $this->type("f.search.param", "1000");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "2");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_TopBasketHeader");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));
        $this->click("test_Payment_oxidpayadvance");
        //making product out of stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '0' WHERE `OXID` = '1000';");
        //in 4rd step it should be checked, if product is still in stock
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));
        $this->assertTrue($this->isTextPresent("You are here: / Complete Order"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."));
        //product is in stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '2' WHERE `OXID` = '1000';");
        $this->clickAndWait("test_HeaderHome");
        $this->type("f.search.param", "1000");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "2");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_TopBasketHeader");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));
        $this->clickAndWait("test_PaymentNextStepBottom");
        //making product out of stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '0' WHERE `OXID` = '1000';");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_orderUrl_1000_1"));
        $this->check("test_OrderConfirmAGBTop");
        //before order submit it should be checked, if product is still in stock
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertTrue($this->isTextPresent("Unfortunately the product \"1000\" is no longer available!"));
        $this->assertEquals("The Shopping Cart is empty.", $this->getText("//div[@id='body']/div[4]"));
        $this->assertTrue($this->isTextPresent("You are here: / Complete Order"));
    }

    /**
     * Frontend: product is sold out by other user during order process.
     * testing if no fatal errors or exceptions are thrown
     * @group order
     * @group basic
     */
    public function testFrontendOutOfStockNotOrderableProductDuringOrder()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "2");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->type("test_am_Search_1001", "1");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_TopBasketHeader");
        $this->assertEquals("186,00 €", $this->getText("test_basketGrandTotal"));
        //product is already in the basket. making product out of stock now
        $this->executeSql("UPDATE `oxarticles` SET `OXSTOCK` = '0', `OXSTOCKFLAG` = '3' WHERE `OXID` = '1000';");
        //in second step, product availability is now checked.
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertTrue($this->isTextPresent("Product is not buyable"));
        //in 3rd step it should be checked, if product is still in stock
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));
        $this->select("sShipSet", "label=Test S&H set [EN] šÄßüл");
        $this->waitForText("Charges: 1,01 €");
        $this->assertEquals("Charges: 1,01 €", $this->getText("test_shipSetCost"));
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertTrue($this->isElementPresent("test_orderArtNo_1001_1"));
        $this->assertEquals("104,51 €", $this->getText("test_orderGrandTotal"));
        //product is in stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '2' WHERE `OXID` = '1000';");
        $this->clickAndWait("test_HeaderHome");
        $this->type("f.search.param", "1000");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "2");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_TopBasketHeader");
        $this->check("test_removeCheck_1001_1");
        $this->clickAndWait("test_basket_Remove");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));
        $this->click("test_Payment_oxidpayadvance");
        //making product out of stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '0' WHERE `OXID` = '1000';");
        //in 4rd step it should be checked, if product is still in stock
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertTrue($this->isTextPresent("Product is not buyable"));
        $this->assertTrue($this->isTextPresent("You are here: / Complete Order"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."));
        //product is in stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '2' WHERE `OXID` = '1000';");
        $this->clickAndWait("test_HeaderHome");
        $this->type("f.search.param", "1000");
        $this->clickAndWait("test_searchGo");
        $this->type("test_am_Search_1000", "2");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_TopBasketHeader");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->assertTrue($this->isTextPresent("Please select your shipping method"));
        $this->clickAndWait("test_PaymentNextStepBottom");
        //making product out of stock again
         $this->executeSql(" UPDATE `oxarticles` SET `OXSTOCK` = '0' WHERE `OXID` = '1000';");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_orderUrl_1000_1"));
        $this->check("test_OrderConfirmAGBTop");
        //before order submit it should be checked, if product is still in stock
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertTrue($this->isTextPresent("Product is not buyable"));
        $this->assertEquals("The Shopping Cart is empty.", $this->getText("//div[@id='body']/div[4]"));
        $this->assertTrue($this->isTextPresent("You are here: / Complete Order"));
    }

    /**
     * Checking Multidimensional variants functionality
     * @group navigation
     * @group basic
     */
    public function testFrontendMultidimensionalVariantsOn()
    {
        $this->executeSql("UPDATE `oxarticles` SET `OXACTIVE`=1 WHERE `OXID`='10014';");
        //multidimensional variants on
        $this->openShop();
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_details_Search_1002");
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("size[EN] | color | type:", $this->getText("//form[@name='tobasket.Search_10014']/div/label"));
        $this->assertEquals("S from 15,00 €* M 15,00 €* L 15,00 €*", $this->clearString($this->getText("mdVariant_Search_10014")));
        $this->assertEquals("14 EN product šÄßüл", $this->getText("test_title_Search_10014"));
        $this->assertEquals("Art.No.: 10014", $this->getText("test_no_Search_10014"));
        $this->assertEquals("from 15,00 €", $this->getText("test_price_Search_10014"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("test_shortDesc_Search_10014"));

        $this->clickAndWait("test_details_Search_10014");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("14 EN product šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("Art.No.: 10014", $this->getText("test_product_artnum"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("test_product_shortdesc"));
        $this->assertEquals("from 15,00 €", $this->getText("test_product_price"));
        $this->assertEquals("Variant Selection of 14 EN product šÄßüл", $this->getText("test_variantHeader"));
        $this->assertEquals("S M L", $this->clearString($this->getText("//div[@class='box variantslist']/select")));
        $this->assertEquals("black white red", $this->clearString($this->getText("//div[@class='box variantslist']/select[2]")));
        $this->assertEquals("lether material", $this->clearString($this->getText("//div[@class='box variantslist']/select[3]")));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[5]"));
        $this->assertEquals("14 EN product šÄßüл S | black | lether", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-1", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("25,00 €*", $this->getText("//div[@id='mdVariantBox']/div/form/div[2]"));

        $this->select("//div[@class='box variantslist']/select[2]", "label=white");
        $this->waitForItemDisappear("//div[@class='box variantslist']/select[3]");
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[5]"));
        $this->assertEquals("14 EN product šÄßüл S | white", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-3", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']/div/form/div[2]"));

        $this->select("//div[@class='box variantslist']/select[2]", "label=black");
        $this->waitForItemAppear("//div[@class='box variantslist']/select[3]");
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[5]"));

        $this->select("//div[@class='box variantslist']/select[3]", "label=material");
        sleep(1);
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[5]"));
        $this->assertEquals("14 EN product šÄßüл S | black | material", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-2", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']/div/form/div[2]"));

        $this->select("//div[@class='box variantslist']/select[3]", "label=lether");
        sleep(1);
        $this->assertEquals("14 EN product šÄßüл S | black | lether", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-1", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("25,00 €*", $this->getText("//div[@id='mdVariantBox']/div/form/div[2]"));

        $this->select("//div[@class='box variantslist']/select", "label=M");
        $this->waitForItemDisappear("//div[@class='box variantslist']/select[3]");
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[2]"));
        $this->assertTrue($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertEquals("white", $this->clearString($this->getSelectedLabel("//div[@class='box variantslist']/select[4]")));

        $this->select("//div[@class='box variantslist']/select[4]", "label=red");
        sleep(1);
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[5]"));
        $this->assertEquals("red", $this->clearString($this->getSelectedLabel("//div[@class='box variantslist']/select[4]")));
        $this->assertEquals("14 EN product šÄßüл M | red", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-2-4", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']/div/form/div[2]"));

        $this->select("//div[@class='box variantslist']/select", "label=L");
        sleep(1);
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[2]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[3]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertTrue($this->isVisible("//div[@class='box variantslist']/select[5]"));
        $this->assertTrue($this->isVisible("//div[@class='box variantslist']/select[6]"));
        $this->assertEquals("black", $this->clearString($this->getSelectedLabel("//div[@class='box variantslist']/select[5]")));
        $this->assertEquals("lether", $this->clearString($this->getSelectedLabel("//div[@class='box variantslist']/select[6]")));
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']/div/form/div[2]"));

        $this->type("//div[@id='mdVariantBox']/div/form/div[3]/input", "2");
        $this->clickAndWait("//div[@id='mdVariantBox']/div/form/div[4]/input");
        $this->assertEquals("14 EN product šÄßüл, L | black | lether ( 2 Stk )", $this->clearString($this->getText("test_RightBasketTitle_1001431_1")));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("2", $this->getText("test_RightBasketItems"));
        $this->assertEquals("30,00 €", $this->getText("test_RightBasketTotal"));
        $this->assertEquals("14 EN product šÄßüл", $this->getText("test_product_name"));

        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));

        $this->select("mdVariant_Search_10014", "label=M 15,00 €*");
        $this->clickAndWait("test_variantMoreInfo_Search_10014");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("path"));
        $this->assertEquals("14 EN product šÄßüл M | white", $this->getText("test_product_name"));
        $this->assertEquals("Art.No.: 10014-2-3", $this->getText("test_product_artnum"));
        $this->assertEquals("13 EN description šÄßüл", $this->getText("test_product_shortdesc"));
        $this->assertEquals("15,00 €", $this->getText("test_product_price"));
        $this->assertEquals("back to main product 14 EN product šÄßüл", $this->getText("test_backToParent"));
        $this->assertEquals("Other variants of: 14 EN product šÄßüл", $this->getText("test_variantHeader1"));
        $this->assertEquals("14 EN product šÄßüл M | white", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-2-3", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']//form/div[2]"));
        $this->assertTrue($this->isVisible("//div[@class='box variantslist']/select[1]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[2]"));
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[3]"));
        $this->assertTrue($this->isVisible("//div[@class='box variantslist']/select[4]"));
        $this->assertEquals("M", $this->clearString($this->getSelectedLabel("//div[@class='box variantslist']/select[1]")));
        $this->assertEquals("white", $this->clearString($this->getSelectedLabel("//div[@class='box variantslist']/select[4]")));

        $this->select("//div[@class='box variantslist']/select[1]", "label=S");
        $this->waitForItemAppear("//div[@class='box variantslist']/select[2]");
        $this->assertFalse($this->isVisible("//div[@class='box variantslist']/select[4]"));

        $this->select("//div[@class='box variantslist']/select[2]", "label=white");
        $this->waitForItemDisappear("//div[@class='box variantslist']/select[3]");
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']//form/div[2]"));
        $this->assertEquals("14 EN product šÄßüл S | white", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-3", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));

        $this->select("//div[@class='box variantslist']/select[2]", "label=black");
        $this->waitForItemAppear("//div[@class='box variantslist']/select[3]");
        $this->assertEquals("14 EN product šÄßüл S | black | lether", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-1", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
        $this->assertEquals("25,00 €*", $this->getText("//div[@id='mdVariantBox']//form/div[2]"));

        $this->select("//div[@class='box variantslist']/select[3]", "label=material");
        $this->assertEquals("15,00 €*", $this->getText("//div[@id='mdVariantBox']//form/div[2]"));
        $this->assertEquals("14 EN product šÄßüл S | black | material", $this->getText("//div[@id='mdVariantBox']/div/strong/a"));
        $this->assertEquals("Art.No.: 10014-1-2", $this->getText("//div[@id='mdVariantBox']/div/strong/tt"));
    }

    /**
     * Checking Multidimensional variants functionality
     * @group navigation
     * @group basic
     */
    public function testFrontendMultidimensionalVariantsOff()
    {
        //multidimensional variants off
        //selenium for bug #1427
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='blUseMultidimensionVariants';");
         $this->executeSql("UPDATE `oxarticles` SET `OXACTIVE`=1 WHERE `OXID`='10014';");

        $this->openShop();
        $this->type("f.search.param", "10014");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals($this->clearString($this->getText("varSelect_Search_10014")), "S | black | lether 25,00 €* S | black | material 15,00 €* S | white 15,00 €* S | red 15,00 €* M | white 15,00 €* M | red 15,00 €* L | black | lether 15,00 €* L | black | material 15,00 €* L | white 15,00 €*");

        $this->clickAndWait("test_title_Search_10014");
        $this->assertEquals("Variant Selection of 14 EN product šÄßüл", $this->getText("test_variantHeader"));
        $this->assertEquals("14 EN product šÄßüл S | black | lether", $this->getText("test_title_Variant_1001411"));
        //10014-2-1: out of stock - offline
        $this->assertEquals("14 EN product šÄßüл S | black | material", $this->getText("test_title_Variant_1001412"));
        $this->assertFalse($this->isElementPresent("test_title_Variant_1001421"));

        $this->clickAndWait("test_title_Variant_1001412");
        $this->assertEquals("You are here: / Search result for \"10014\"", $this->getText("path"));
        $this->assertEquals("14 EN product šÄßüл S | black | material", $this->getText("test_product_name"));
        $this->assertEquals("back to main product 14 EN product šÄßüл", $this->getText("test_backToParent"));
        $this->assertEquals("Other variants of: 14 EN product šÄßüл", $this->getText("test_variantHeader1"));
        $this->assertTrue($this->isElementPresent("test_toBasket"));

        //10014-2-2: out of stock - not orderable
        $this->clickAndWait("test_title_Variant_1001422");
        $this->assertEquals("14 EN product šÄßüл M | black | material", $this->getText("test_product_name"));
        $this->assertFalse($this->isElementPresent("test_toBasket"));
    }

    /**
     * Vouchers for specific products and categories
     * @group navigation
     * @group basic
     */
    public function testFrontendVouchersForSpecificCategoriesAndProducts()
    {
        $this->openShop();
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_toBasket_Search_1002");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("test_RightBasketOpen");
        $this->type("voucherNr", "test111");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertTrue($this->isTextPresent("Reason: The coupon is not valid for your user group!"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("voucherNr", "test111");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertEquals("(No. test111)", $this->getText("test_basketVoucher_1"));
        $this->type("voucherNr", "test222");
        $this->clickAndWait("test_basketVoucherAdd");
        $this->assertEquals("(No. test222)", $this->getText("test_basketVoucher_2"));
        $this->assertEquals("- 10,00  €", $this->getText("test_basketVoucherDiscount_1"));
        $this->assertEquals("- 9,00  €", $this->getText("test_basketVoucherDiscount_2"));
        $this->type("test_basketAm_1003_4", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("- 10,00  €", $this->getText("test_basketVoucherDiscount_1"));
        $this->assertEquals("- 15,00  €", $this->getText("test_basketVoucherDiscount_2"));
        $this->check("test_removeCheck_1003_4");
        $this->check("test_removeCheck_1000_1");
        $this->clickAndWait("test_basket_Remove");
        $this->assertEquals("- 5,00  €", $this->getText("test_basketVoucherDiscount_1"));
        $this->assertEquals("- 3,00  €", $this->getText("test_basketVoucherDiscount_2"));
    }

    /**
     * Checking VAT functionality, when it is calculated for Billing country
     * @group navigation
     * @group basic
     */
    public function testFrontendVatForBillingCountry()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("226,00 €", $this->getText("test_basketGross"));
        $this->assertEquals("193,50 €", $this->getText("test_basketNet"));
        $this->assertEquals("2,28 €", $this->getText("test_basketVAT_5"));
        $this->assertEquals("8,78 €", $this->getText("test_basketVAT_10"));
        $this->assertEquals("11,44 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("-10,00 €", $this->getText("test_basketDiscount_1"));

        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertEquals("Germany", $this->getSelectedLabel("invCountrySelect"));
        $this->clickAndWait("blshowshipaddress");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Switzerland");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->check("test_Payment_oxidpayadvance");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("5%", $this->getText("test_orderUnitVat_1000_1"));
        $this->assertEquals("10%", $this->getText("test_orderUnitVat_1001_2"));
        $this->assertEquals("19%", $this->getText("test_orderUnitVat_1003_3"));
        $this->assertEquals("202,47 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("2,38 €", $this->getText("test_orderVat_5"));
        $this->assertEquals("9,18 €", $this->getText("test_orderVat_10"));
        $this->assertEquals("11,97 €", $this->getText("test_orderVat_19"));
        $this->assertEquals("226,00 €", $this->getText("test_orderGrossPrice"));

        $this->clickAndWait("test_Step2_Text");
        $this->select("invCountrySelect", "label=Switzerland");
        $this->select("delCountrySelect", "label=Germany");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Baden-Wurttemberg");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->check("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("0%", $this->getText("test_orderUnitVat_1000_1"));
        $this->assertEquals("0%", $this->getText("test_orderUnitVat_1001_2"));
        $this->assertEquals("0%", $this->getText("test_orderUnitVat_1003_3"));
        $this->assertEquals("202,47 €", $this->getText("test_orderGrossPrice"));
        $this->assertEquals("-10,00 €", $this->getText("test_orderDiscount_1"));
        $this->assertEquals("192,47 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("0,00 €", $this->getText("test_orderVat_0"));
        $this->assertEquals("192,47 €", $this->getText("test_orderNetPrice"));
    }

    /**
     * Checking VAT functionality, when it is calculated for Shipping country
     * @group navigation
     * @group basic
     */
    public function testFrontendVatForShippingCountry()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`=0x07 WHERE `OXVARNAME`='blShippingCountryVat';");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_toBasket_Search_1003");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("226,00 €", $this->getText("test_basketGross"));
        $this->assertEquals("193,50 €", $this->getText("test_basketNet"));
        $this->assertEquals("2,28 €", $this->getText("test_basketVAT_5"));
        $this->assertEquals("8,78 €", $this->getText("test_basketVAT_10"));
        $this->assertEquals("11,44 €", $this->getText("test_basketVAT_19"));
        $this->assertEquals("-10,00 €", $this->getText("test_basketDiscount_1"));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepBottom");
        //no shipping address
        $this->check("test_Payment_oxidpayadvance");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("5%", $this->getText("test_orderUnitVat_1000_1"));
        $this->assertEquals("10%", $this->getText("test_orderUnitVat_1001_2"));
        $this->assertEquals("19%", $this->getText("test_orderUnitVat_1003_3"));
        $this->assertEquals("193,50 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("2,28 €", $this->getText("test_orderVat_5"));
        $this->assertEquals("8,78 €", $this->getText("test_orderVat_10"));
        $this->assertEquals("11,44 €", $this->getText("test_orderVat_19"));
        $this->assertEquals("-10,00 €", $this->getText("test_orderDiscount_1"));
        $this->clickAndWait("test_Step2_Text");
        //billing germany, shipping Switzerland
        $this->assertEquals("Germany", $this->getSelectedLabel("invCountrySelect"));
        $this->clickAndWait("blshowshipaddress");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Switzerland");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->check("test_Payment_oxidpayadvance");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("0%", $this->getText("test_orderUnitVat_1000_1"));
        $this->assertEquals("0%", $this->getText("test_orderUnitVat_1001_2"));
        $this->assertEquals("0%", $this->getText("test_orderUnitVat_1003_3"));
        $this->assertEquals("202,47 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("0,00 €", $this->getText("test_orderVat_0"));
        $this->assertEquals("202,47 €", $this->getText("test_orderGrossPrice"));
        $this->clickAndWait("test_Step2_Text");
        //billing switzerland, shipping germany
        $this->select("invCountrySelect", "label=Switzerland");
        $this->select("delCountrySelect", "label=Germany");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Baden-Wurttemberg");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->check("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("5%", $this->getText("test_orderUnitVat_1000_1"));
        $this->assertEquals("10%", $this->getText("test_orderUnitVat_1001_2"));
        $this->assertEquals("19%", $this->getText("test_orderUnitVat_1003_3"));
        $this->assertEquals("193,50 €", $this->getText("test_orderNetPrice"));
        $this->assertEquals("2,28 €", $this->getText("test_orderVat_5"));
        $this->assertEquals("8,78 €", $this->getText("test_orderVat_10"));
        $this->assertEquals("11,44 €", $this->getText("test_orderVat_19"));
        $this->assertEquals("-10,00 €", $this->getText("test_orderDiscount_1"));
    }


    /**
     * Vouchers is disabled via performance options
     * @group navigation
     * @group basic
     */
    public function testFrontendDisabledVouchers()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showVouchers'");
        $this->openShop();
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertFalse($this->isElementPresent("voucherNr"));
        $this->assertFalse($this->isElementPresent("test_VoucherHeader"));
        $this->assertFalse($this->isElementPresent("test_basketVoucherAdd"));
    }

    /**
     * Gift wrapping is disabled via performance options
     * @group navigation
     * @group basic
     */
    public function testFrontendDisabledGiftWrapping()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'bl_showGiftWrapping'");
        $this->openShop();
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->type("test_UsrOpt2_usr", "birute_test@nfq.lt");
        $this->type("test_UsrOpt2_pwd", "useruser");
        $this->clickAndWait("test_UsrOpt2");
        $this->clickAndWait("test_UserNextStepTop");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertFalse($this->isElementPresent("test_orderWrapp_1001_1"));
        $this->assertFalse($this->isTextPresent("NONE"));
    }

    /**
     * testing option 'Product can be customized' from Administer products -> Extend tab
     * @group navigation
     * @group basic
     */
    public function testFrontendCustomizedProduct()
    {
        //enabling field
         $this->executeSql("UPDATE `oxarticles` SET `OXISCONFIGURABLE` = 1 WHERE `OXID` = '1000'");
        $this->openShop();
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->assertTrue($this->isElementPresent("persparam[details]"));
        $this->type("persparam[details]", "test label šÄßüл");
        $this->type("test_AmountToBasket", "2");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_1"));
        $this->assertEquals("test label šÄßüл", $this->getValue("persparamInput_1000_details"));
        $this->clickAndWait("test_BasketNextStepBottom");
        $this->type("test_UsrOpt2_usr", "birute_test@nfq.lt");
        $this->type("test_UsrOpt2_pwd", "useruser");
        $this->clickAndWait("test_UsrOpt2");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_orderUrl_1000_1"));
        $this->assertEquals("Details:", $this->clearString($this->getText("test_orderPersParam_1000_1")));
        $this->assertEquals("test label šÄßüл", $this->getValue("//td[@id='test_orderPersParam_1000_1']/input"));
        $this->assertFalse($this->isEditable("//td[@id='test_orderPersParam_1000_1']/input"));
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");
        //checking in Admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=12");
        $this->assertTrue($this->isTextPresent("details : test label šÄßüл"));
        //disabling field
         $this->executeSql("UPDATE `oxarticles` SET `OXISCONFIGURABLE` = 0 WHERE `OXID` = '1000'");
        $this->openShop();
        $this->type("f.search.param", "100");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1000");
        $this->assertFalse($this->isElementPresent("persparam[details]"));
    }

    /**
     * News small box in main page and news page
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendBundledProduct()
    {
             $this->executeSql("UPDATE `oxarticles` SET  `OXBUNDLEID` = '1003' WHERE `OXID` = '1000';");
            $this->openShop();
            $this->type("f.search.param", "1000");
            $this->clickAndWait("test_searchGo");
            $this->assertFalse($this->isElementPresent("test_RightBasketTitleLink_1003_1"));
            $this->assertFalse($this->isElementPresent("test_RightBasketTitleLink_1003_2"));
            $this->clickAndWait("test_toBasket_Search_1000");
            $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_RightBasketTitleLink_1000_1"));
            $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_RightBasketTitleLink_1003_2"));
            $this->clickAndWait("test_TopBasketHeader");
            $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_1"));
            $this->assertEquals("Art.No.: 1000", $this->getText("test_basketNo_1000_1"));
            $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_basketTitle_1003_2"));
            $this->assertEquals("Art.No.: 1003", $this->getText("test_basketNo_1003_2"));
            $this->assertEquals("+1", $this->getText("test_basketAmount_1003_2"));
            $this->assertEquals("50,00 €", $this->getText("test_basketGrandTotal"));
    }

    /**
     * Invitations functionality. checking enable/disable in admin and email sending in frontend
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPrivateShoppingInvitations()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='iUseGDVersion'");
        //checking if functionality is disabled in frontend
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_link_service_invite"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isTextPresent("You're logged in as"));
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
        $this->assertTrue($this->isElementPresent("test_link_service_invite"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isTextPresent("You're logged in as"));
        $this->clickAndWait("test_link_service_invite");
        $this->type("editval[rec_email][1]", "birute01@nfq.lt");
        $this->assertTrue($this->isElementPresent("editval[rec_email][2]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][3]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][4]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][5]"));
        $this->type("editval[send_name]", "birute_test");
        $this->type("editval[send_email]", "birute_test@nfq.lt");
        $this->assertEquals("Have a look!", $this->getValue("editval[send_subject]"));
        $this->type("editval[send_message]", "Invitation to shop");
        $this->type("c_mac", $this->getText("test_verificationCode"));
        $this->clickAndWait("//input[@value='Send']");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("path"));
        $this->assertEquals("Invite your friends", $this->getText("test_recommendHeader"));
        $this->assertTrue($this->isTextPresent("Invitation e-mail was sent to your friends. Thank you for inviting your friends."));
        //testing functionality in frontend, when user is not logged in
        $this->clickAndWait("test_RightLogout");
        $this->assertEquals("You are here: / Login", $this->getText("path"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("test_link_service_invite");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("path"));
        $this->type("editval[rec_email][1]", "birute01@nfq.lt");
        $this->assertTrue($this->isElementPresent("editval[rec_email][2]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][3]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][4]"));
        $this->assertTrue($this->isElementPresent("editval[rec_email][5]"));
        $this->type("editval[send_name]", "birute_test");
        $this->type("editval[send_email]", "birute_test@nfq.lt");
        $this->assertEquals("Have a look!", $this->getValue("editval[send_subject]"));
        $this->type("editval[send_message]", "Invitation to shop");
        $this->type("c_mac", $this->getText("test_verificationCode"));
        $this->clickAndWait("//input[@value='Send']");
        $this->assertEquals("You are here: / Invite your friends", $this->getText("path"));
        $this->assertEquals("Invite your friends", $this->getText("test_recommendHeader"));
        $this->assertTrue($this->isTextPresent("Invitation e-mail was sent to your friends. Thank you for inviting your friends."));
    }

        //$this->assertTrue($this->isElementPresent("test_OrderConfirmAGBTop"), "popup for confirming agb is missing");


    /**
     * Private sales: promotions
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPrivateShoppingPromotions()
    {
         $this->executeSql( "UPDATE `oxactions` SET `OXACTIVE` = '1';" );
         $this->executeSql( "UPDATE `oxactions` SET `OXID` = 'oxcurrentpromotion' WHERE `OXTITLE_1` = 'Current Promotion';" );
         $this->executeSql( "INSERT INTO `oxobject2action` (`OXID`, `OXACTIONID`, `OXOBJECTID`, `OXCLASS`) VALUES ('87cd6022c0537ae4fac73ba1f2243cf8', 'oxcurrentpromotion', 'oxidnewcustomer', 'oxgroups');" );
        $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME` = 'bl_perfParseLongDescinSmarty'");
            $shopId = 'oxbaseshop';
        $this->executeSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('mcd5654964c9', '$shopId', '', 'bl_perfParseLongDescinSmarty', 'bool', 0x07)");

        $this->openShop();

        //checking promotions in main shop
        $this->assertFalse($this->isElementPresent("promooxcurrentpromotion"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isElementPresent("promooxcurrentpromotion"));

        //deactivating promotion
         $this->executeSql( "UPDATE `oxactions` SET `OXACTIVE` = 0 WHERE `OXID` = 'oxcurrentpromotion'" );
        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isElementPresent("promooxcurrentpromotion"));
    }

    /**
     * Private sales: basket expiration
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendPrivateShoppingBasketExpiration()
    {
        //products are offline, if bought out
         $this->executeSql("UPDATE `oxarticles` SET `OXSTOCK` = '1', `OXSTOCKFLAG` = '2' WHERE `OXID` = '1001';");

        //enabling functionality
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
        $this->click("link=Private Sales");
        sleep(1);
        $this->assertEquals("Enable", $this->getSelectedLabel("basketreserved"));
        $this->assertEquals("10", $this->getValue("confstrs[iPsBasketReservationTimeout]"));

        //checking in frontend
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//input[@id='f.search.param']"));
        $this->type("//input[@id='f.search.param']", "1001");
        $this->clickAndWait("test_searchGo");
        $this->assertTrue($this->isElementPresent("test_smallHeader"));
        $this->assertEquals("1 Hits for \"1001\"", $this->getText("test_smallHeader"));

        //adding product to basket
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->assertTrue($this->isTextPresent("Expires in:"));
        $this->assertEquals("1", $this->getText("test_TopBasketProducts"));
        $this->assertEquals("1", $this->getText("test_LeftBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("Cart", $this->getText("test_TopBasketHeader"));
        $this->assertEquals("Cart", $this->getText("test_RightBasketHeader"));
        $this->assertEquals("Cart", $this->getText("test_LeftBasketHeader"));
        //checking if product is reserved
        $this->clickAndWait("test_searchGo");
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        $this->assertTrue($this->isTextPresent("You are here: / Search"));
        //waiting till basket will expire
        sleep(10);
        $this->clickAndWait("test_searchGo");
        $this->assertFalse($this->isTextPresent("Expires in:"));
        $this->assertFalse($this->isElementPresent("test_TopBasketProducts"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketProducts"));
        $this->assertFalse($this->isElementPresent("test_RightBasketProducts"));
        $this->assertFalse($this->isElementPresent("test_TopBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_RightBasketHeader"));
        $this->assertFalse($this->isElementPresent("test_LeftBasketHeader"));
        $this->assertTrue($this->isElementPresent("test_toBasket_Search_1001"));
        $this->assertTrue($this->isTextPresent("You are here: / Search"));
        $this->assertEquals("1 Hits for \"1001\"", $this->getText("test_smallHeader"));
        //adding to basket again and finishing order
        $this->assertTrue($this->isElementPresent("test_toBasket_Search_1001"));
        $this->clickAndWait("test_toBasket_Search_1001");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("1", $this->getText("test_TopBasketProducts"));
        $this->assertEquals("1", $this->getText("test_LeftBasketProducts"));
        $this->assertEquals("1", $this->getText("test_RightBasketProducts"));
        $this->assertEquals("Cart", $this->getText("test_TopBasketHeader"));
        $this->assertEquals("Cart", $this->getText("test_RightBasketHeader"));
        $this->assertEquals("Cart", $this->getText("test_LeftBasketHeader"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        $this->assertTrue($this->isTextPresent("You are here: / Search"));
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->type("test_UsrOpt2_usr", "birute_test@nfq.lt");
        $this->type("test_UsrOpt2_pwd", "useruser");
        $this->clickAndWait("test_UsrOpt2");
        $this->clickAndWait("test_UserNextStepTop");
        $this->check("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");
        $this->assertEquals("You are here: / Order completed", $this->getText("path"));
        $this->clickAndWait("test_HeaderHome");
        $this->assertFalse($this->isElementPresent("test_no_WeekSpecial_1001"));
        $this->assertFalse($this->isElementPresent("test_no_FirstArticle_1001"));
    }

    /**
     * Basket exclusion: situation 1
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendBasketExclusionCase1()
    {
        //basket exclusion is off
        $this->openShop();
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->clickAndWait("test_toBasket_action_1000");
        $this->clickAndWait("link=Gear");
        $this->clickAndWait("link=Fashion");
        $this->assertEquals("You are here: / Gear / Fashion", $this->getText("path"));

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
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->clickAndWait("test_toBasket_action_1000");
        $this->assertFalse($this->isElementPresent("popup"));
        $this->assertTrue($this->isElementPresent("test_RightBasketTitleLink_1000_1"));
        $this->clickAndWait("link=Gear");
        $this->clickAndWait("link=Fashion");
        $this->assertEquals("You are here: / Gear / Fashion", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("popup"));
        $this->assertTrue($this->isTextPresent("Root category changed"));
        $this->assertTrue($this->isElementPresent("tobasket"));
        $this->assertTrue($this->isElementPresent("//input[@value='Continue Shopping']"));
        $this->clickAndWait("tobasket");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketNo_1000_1"));
        $this->clickAndWait("test_HeaderHome");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1000_1"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));
        $this->assertFalse($this->isElementPresent("popup"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_sub1");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("path"));
        $this->assertFalse($this->isElementPresent("popup"));
        $this->clickAndWait("test_toBasket_action_1003");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1003_2"));
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1000_1"));
        $this->clickAndWait("link=Gear");
        $this->clickAndWait("link=Fashion");
        $this->assertEquals("You are here: / Gear / Fashion", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("popup"));
        $this->assertTrue($this->isTextPresent("Root category changed"));
        $this->clickAndWait("//input[@value='Continue Shopping']");
        $this->clickAndWait("link=Gear");
        $this->clickAndWait("link=Fashion");
        $this->clickAndWait("link=Accessories");
        $this->assertEquals("You are here: / Gear / Fashion / Accessories", $this->getText("path"));
        $this->clickAndWait("test_toBasket_action_f4f981b0d9e34d2aeda82d79412480a4");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_f4f981b0d9e34d2aeda82d79412480a4_1"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertTrue($this->isElementPresent("popup"));
        $this->assertTrue($this->isTextPresent("Root category changed"));
        $this->assertTrue($this->isElementPresent("tobasket"));
        $this->assertTrue($this->isElementPresent("//input[@value='Continue Shopping']"));
    }

    /**
     * Basket exclusion: situation 2
     * @group navigation
     * @group user
     * @group basic
     */
    public function testFrontendBasketExclusionCase2()
    {
        //enabling basket exclusion
         $this->executeSql(" DELETE FROM `oxconfig` WHERE `OXVARNAME`='blBasketExcludeEnabled';");
             $this->executeSql( "INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('ee548ijuytrf95dea855be2d1e', 'oxbaseshop', 'blBasketExcludeEnabled', 'str', 0x07);" );

        //checking in frontend
        $this->openShop();
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->clickAndWait("test_toBasket_action_1000");
        $this->assertTrue($this->isElementPresent("test_RightBasketTitle_1000_1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("path"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_sub1");
        $this->assertFalse($this->isElementPresent("popup"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("path"));
        $this->clickAndWait("link=Gear");
        $this->assertTrue($this->isElementPresent("popup"));
        $this->clickAndWait("tobasket");
        $this->assertEquals("You are here: / View Cart", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("test_basketNo_1000_1"));
        $this->click("checkAll");
        $this->clickAndWait("test_basket_Remove");
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."));
        $this->clickAndWait("test_HeaderHome");
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->getText("path"));
        $this->clickAndWait("link=Gear");
        $this->assertFalse($this->isTextPresent("Root category changed"));
        $this->assertFalse($this->isElementPresent("popup"));
        $this->clickAndWait("test_BoxLeft_Cat_testcategory0_2");
        $this->assertFalse($this->isTextPresent("Root category changed"));
        $this->assertFalse($this->isElementPresent("popup"));
    }

    /**
     * PersParam functionality
     * @group navigation
     * @group order
     * @group basic
     * @group main
     */
    public function testFrontendPersParamSaveBasket()
    {
        // test data disables basket saving, enabling it
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE`='' WHERE `OXVARNAME`='blPerfNoBasketSaving'");

        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertFalse($this->isElementPresent("test_RightBasketOpen"));
        $this->select("selectList_FirstArticle_1001_0", "label=selvar2 [EN] šÄßüл");
        $this->clickAndWait("test_toBasket_FirstArticle_1001");
        $this->select("selectList_FirstArticle_1001_0", "label=selvar4 [EN] šÄßüл +2%");
        $this->clickAndWait("test_toBasket_FirstArticle_1001");
        $this->type("f.search.param", "perspara");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_details_Search_20016");
        $this->clickAndWait("test_toBasket");
        $this->type("persparam[details]", "test");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_basketTitle_1001_1"));
        $this->assertEquals("selvar2 [EN] šÄßüл", $this->getSelectedLabel("test_basketSelect_1001_1_0"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_basketTitle_1001_2"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getSelectedLabel("test_basketSelect_1001_2_0"));
        $this->assertEquals("perspara EN_prod", $this->getText("test_basketTitle_20016_3"));
        $this->assertEquals("perspara EN_prod", $this->getText("test_basketTitle_20016_4"));
        $this->assertEquals("test", $this->getValue("persparamInput_20016_details"));
        $this->assertEquals("test", $this->getValue("aproducts[90f46b78b07846e0dd893acf83816d48][persparam][details]"));
        $this->select("test_basketSelect_1001_1_0", "label=selvar3 [EN] šÄßüл -2,00 €");
        $this->type("test_basketAm_20016_3", "2");
        $this->type("aproducts[90f46b78b07846e0dd893acf83816d48][persparam][details]", "test1");
        $this->clickAndWait("test_basketUpdate");

        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getSelectedLabel("test_basketSelect_1001_1_0"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getSelectedLabel("test_basketSelect_1001_2_0"));
        $this->assertEquals("test1", $this->getValue("persparamInput_20016_details"));
        $this->assertEquals("2", $this->getValue("test_basketAm_20016_3"));
        $this->assertEquals("test1", $this->getValue("aproducts[4e3e8ab9dd59769acbf3e96d2fc5e513][persparam][details]"));
        $this->assertEquals("1", $this->getValue("test_basketAm_20016_4"));

        //checking if this basket was saved
        $this->clearTmp();
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_RightBasketOpen"));
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->assertTrue($this->isElementPresent("test_TopBasketHeader"));
        $this->clickAndWait("test_TopBasketHeader");

        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getSelectedLabel("test_basketSelect_1001_1_0"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getSelectedLabel("test_basketSelect_1001_2_0"));
        $this->assertEquals("test1", $this->getValue("persparamInput_20016_details"));
        $this->assertEquals("2", $this->getValue("test_basketAm_20016_3"));
        $this->assertEquals("test1", $this->getValue("aproducts[4e3e8ab9dd59769acbf3e96d2fc5e513][persparam][details]"));
        $this->assertEquals("1", $this->getValue("test_basketAm_20016_4"));

        //submitting order
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertTrue($this->isTextPresent("Test product 1 [EN] šÄßüл\n Art.No.: 1001\n test selection list [EN] šÄßüл : selvar3 [EN] šÄßüл -2,00 €"));
        $this->assertTrue($this->isTextPresent("Test product 1 [EN] šÄßüл\n Art.No.: 1001\n test selection list [EN] šÄßüл : selvar4 [EN] šÄßüл +2%"));
        $this->assertEquals("perspara EN_prod", $this->getText("test_orderUrl_20016_3"));
        $this->assertEquals("perspara EN_prod", $this->getText("test_orderUrl_20016_4"));
        $this->assertEquals("205,20 €", $this->getText("test_orderGrandTotal"));
        $this->check("test_OrderConfirmAGBBottom");
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("Thank you"));
        $this->assertEquals("You are here: / Order completed", $this->getText("path"));
    }

    /**
     * PersParam functionality
     * @group navigation
     * @group order
     * @group basic
     */
    public function testFrontendPersParamOrder()
    {
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "perspara");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_details_Search_20016");
        $this->type("persparam[details]", "test");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("test_RightBasketOpen");
        $this->type("test_basketAm_20016_1", "100");
        $this->clickAndWait("test_basketUpdate");

        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBBottom");
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("Thank you"));
        $this->assertEquals("You are here: / Order completed", $this->getText("path"));

        //checking in admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=12", "save");
        $this->assertEquals("100 *", $this->getText("//table[2]/tbody/tr/td[1]"));
        $this->assertEquals("perspara EN_prod", $this->getText("//td[3]"));
        $this->assertEquals("190,00 EUR", $this->getText("//td[5]"));
        $this->assertEquals(", details : test", $this->getText("//td[6]"));
        $this->assertEquals("197,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Products", "//input[@value='Update']");
        $this->assertEquals("100", $this->getValue("//tr[@id='art.1']/td[1]/input"));
        $this->assertEquals(", details : test", $this->getText("//tr[@id='art.1']/td[5]"));
        $this->assertEquals("1,90 EUR", $this->getText("//tr[@id='art.1']/td[8]"));
        $this->assertEquals("190,00 EUR", $this->getText("//tr[@id='art.1']/td[9]"));
        $this->type("//tr[@id='art.1']/td[1]/input", "5");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals(", details : test", $this->getText("//tr[@id='art.1']/td[5]"));
        $this->assertEquals("1,90 EUR", $this->getText("//tr[@id='art.1']/td[8]"));
        $this->assertEquals("9,50 EUR", $this->getText("//tr[@id='art.1']/td[9]"));
        $this->assertEquals("20,90", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
    }

    /**
     * several itm discounts in basket
     * @group order
     * @group basic
     */
    public function testFewItmDiscounts()
    {
             $this->executeSql("INSERT INTO `oxdiscount` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXTITLE`, `OXTITLE_1`, `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`) VALUES ('6a58b47', 'oxbaseshop', 1, 'test_discount_1', 'test_discount_1', 0, 0, 9999, 30, 'itm', 0, '1003', 1, 0);");
             $this->executeSql("INSERT INTO `oxdiscount` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXTITLE`, `OXTITLE_1`, `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`) VALUES ('6282d39', 'oxbaseshop', 1, 'test_discount_2', 'test_discount_2', 0, 0, 9999, 30, 'itm', 0, '1002-1', 1, 0);");

        $this->openShop();
        $this->clickAndWait("test_toBasket_FreshIn_1000");
        $this->assertEquals("3", $this->getText("test_RightBasketProducts"));
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("test_basketTitle_1000_1"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_basketTitle_1003_2"));
        $this->assertEquals("+1", $this->getText("test_basketAmount_1003_2"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->getText("test_basketTitle_1002-1_3"));
        $this->assertEquals("+1", $this->getText("test_basketAmount_1002-1_3"));
        $this->assertEquals("50,00 €", $this->getText("//td[@id='test_basketGrandTotal']/b"));
        $this->type("test_basketAm_1000_1", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("+1", $this->getText("test_basketAmount_1003_2"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("test_basketTitle_1003_2"));
        $this->assertEquals("+1", $this->getText("test_basketAmount_1002-1_3"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->getText("test_basketTitle_1002-1_3"));
        $this->assertEquals("100,00 €", $this->getText("//td[@id='test_basketGrandTotal']/b"));
        $this->assertEquals("2", $this->getValue("test_basketAm_1000_1"));
    }

/**
     * checking on weight depending delivery costs
     * @group admin
     * @group order
     * @group basic
     */
    public function testDeliveryByWeight()
    {
        //calculating delivery for every product in basket
         $this->executeSql( "UPDATE `oxarticles` SET `OXACTIVE` = 1 WHERE `OXID` = '10011'" );
         $this->executeSql( "UPDATE `oxarticles` SET `OXACTIVE` = 1 WHERE `OXID` = '10012'" );
         $this->executeSql( "UPDATE `oxarticles` SET `OXACTIVE` = 1 WHERE `OXID` = '10013'" );
         $this->executeSql( "UPDATE `oxdeliveryset` SET `OXACTIVE` = 1 WHERE `OXID` = 'testshset7'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXACTIVE` = 1 WHERE `OXID` = 'testsh1'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXACTIVE` = 1 WHERE `OXID` = 'testsh2'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXACTIVE` = 1 WHERE `OXID` = 'testsh5'" );

        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_10011");
        $this->clickAndWait("test_toBasket_Search_10012");
        $this->clickAndWait("test_toBasket_Search_10013");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("12,00 €", $this->getText("test_basketDeliveryNet"));
        $this->type("test_basketAm_10012_2", "3");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("14,00 €", $this->getText("test_basketDeliveryNet"));
        $this->check("test_removeCheck_10013_3");
        $this->clickAndWait("test_basket_Remove");
        $this->assertEquals("4,00 €", $this->getText("test_basketDeliveryNet"));
        $this->type("test_basketAm_10012_2", "1");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("2,00 €", $this->getText("test_basketDeliveryNet"));
        //delivery once a cart
         $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 0 WHERE `OXID` = 'testsh1'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 0 WHERE `OXID` = 'testsh2'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 0 WHERE `OXID` = 'testsh5'" );
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("5,00 €", $this->getText("test_basketDeliveryNet"));
        //delivery once every product
         $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 1 WHERE `OXID` = 'testsh1'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 1 WHERE `OXID` = 'testsh2'" );
         $this->executeSql( "UPDATE `oxdelivery` SET `OXFIXED` = 1 WHERE `OXID` = 'testsh5'" );
        $this->type("test_basketAm_10011_1", "2");
        $this->type("test_basketAm_10012_2", "2");
        $this->clickAndWait("test_basketUpdate");
        $this->assertEquals("6,00 €", $this->getText("test_basketDeliveryNet"));
    }

    //------------------ trusted shops tests ----------------------------------
    /**
     * testing trusted shops. seagel activation
     * @group admin
     * @group trustedShopsBasic
     * @group basic
     */
    public function testTsSeagel()
    {
            //trusted shops are disabled
            $this->openShop();
            $this->clickAndWait("test_Lang_Deutsch");
            $this->assertFalse($this->isElementPresent("tsProfile"));
            $this->assertFalse($this->isTextPresent("ist ein von Trusted Shops"));

             //trusted shops setup in admin
            $this->loginAdminTs();
            $this->assertTrue($this->isElementPresent("aShopID_TrustedShops[0]"));
            $this->type("aShopID_TrustedShops[0]", "XA2A8D35838AF5F63E5EB0E05847B1CB8");
            $this->check("//input[@name='tsTestMode' and @value='true']");
            $this->check("//input[@name='tsSealActive' and @value='true']");
            $this->assertEquals("Lastschrift/Bankeinzug Kreditkarte Rechnung Nachnahme Vorauskasse / Überweisung Verrechnungsscheck Paybox PayPal Zahlung bei Abholung Finanzierung Leasing T-Pay Click&Buy (Firstgate) Giropay Google Checkout Online Shop Zahlungskarte Sofortüberweisung.de Andere Zahlungsart", $this->getText("paymentids[oxidcashondel]"));
            $this->assertTrue($this->isTextPresent("Test payment method [EN] šÄßüл"));
            $this->assertEquals("Lastschrift/Bankeinzug Kreditkarte Rechnung Nachnahme Vorauskasse / Überweisung Verrechnungsscheck Paybox PayPal Zahlung bei Abholung Finanzierung Leasing T-Pay Click&Buy (Firstgate) Giropay Google Checkout Online Shop Zahlungskarte Sofortüberweisung.de Andere Zahlungsart", $this->getText("paymentids[testpayment]"));
            $this->select("paymentids[testpayment]", "label=Kreditkarte");
            $this->clickAndWait("save");
            $this->assertEquals("Kreditkarte", $this->getSelectedLabel("paymentids[testpayment]"));
            $this->assertEquals("on", $this->getValue("//input[@name='tsSealActive' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='tsTestMode' and @value='true']"));
            $this->assertEquals("XA2A8D35838AF5F63E5EB0E05847B1CB8", $this->getValue("aShopID_TrustedShops[0]"));
            //$this->assertTrue($this->isElementPresent("//div[@id='liste']/table/tbody/tr[2]/td[@class='active']"));
            $this->type("aShopID_TrustedShops[0]", "XA2A8D35838AF5F63E5EB0E05847B1CB4");
            $this->assertFalse($this->isTextPresent("The certificate does not exist"));
            $this->clickAndWait("save");
            $this->assertTrue($this->isTextPresent("The certificate does not exist"));
            $this->assertEquals("XA2A8D35838AF5F63E5EB0E05847B1CB8", $this->getValue("aShopID_TrustedShops[0]"));

            //checking in frontend
            $this->openShop();
            $this->clickAndWait("test_Lang_Deutsch");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->assertTrue($this->isTextPresent("ist ein von Trusted Shops geprüfter"));
            $this->clickAndWait("test_title_WeekSpecial_1001");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("test_toBasket");
            $this->clickAndWait("test_RightBasketOpen");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
            $this->type("test_RightLogin_Pwd", "useruser");
            $this->clickAndWait("test_RightLogin_Login");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("test_BasketNextStepTop");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->clickAndWait("test_UserNextStepTop");
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->click("test_Payment_testpayment");
            $this->clickAndWait("test_PaymentNextStepBottom");
            $this->assertEquals("Sie sind hier: / Bestellung abschliessen", $this->getText("path"));
            $this->assertTrue($this->isElementPresent("tsProfile"));
            $this->check("test_OrderConfirmAGBTop");
            $this->clickAndWait("test_OrderSubmitTop");
            $this->assertTrue($this->isElementPresent("formTsShops"));
            $this->assertTrue($this->isElementPresent("tsProfile"));
    }

    /**
     * testing trusted shops. excellence Ts. functionality depends on order price
     * @group admin
     * @group trustedShopsBasic
     * @group basic
     */
    public function testTsExcellence()
    {
            //setupping ts
            $this->loginAdminTs();
            $this->type("aShopID_TrustedShops[0]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->type("aTsUser[0]", "testExcellencePartner");
            $this->type("aTsPassword[0]", "test12345678");
            $this->check("//input[@name='tsTestMode' and @value='true']");
            $this->clickAndWait("save");

            $this->assertEquals("X41495A6E65ECDDCD554A02C0601D1C97", $this->getValue("aShopID_TrustedShops[0]"));
            $this->assertEquals("testExcellencePartner", $this->getValue("aTsUser[0]"));
            $this->assertEquals("test12345678", $this->getValue("aTsPassword[0]"));
            //$this->assertTrue($this->isElementPresent("//div[@id='liste']/table/tbody/tr[2]/td[@class='active']"));
            $this->check("//input[@name='tsSealActive' and @value='true']");
            $this->check("//input[@name='tsTestMode' and @value='true']");
            $this->clickAndWait("save");

            //checking in frontend. order < 500eur
            $this->openShop();
            $this->clickAndWait("test_Lang_Deutsch");
            $this->clickAndWait("test_toBasket_WeekSpecial_1001");
            $this->clickAndWait("test_RightBasketOpen");
            $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
            $this->type("test_RightLogin_Pwd", "useruser");
            $this->clickAndWait("test_RightLogin_Login");
            $this->clickAndWait("test_BasketNextStepTop");
            $this->clickAndWait("test_UserNextStepTop");
            $this->assertTrue($this->isTextPresent("Sie sind hier: / Bezahlen"));
            $this->click("test_Payment_oxidcashondel");
            $this->assertTrue($this->isElementPresent("bltsprotection"));
            $this->assertFalse($this->isVisible("stsprotection"));
            $this->assertTrue($this->isTextPresent("Käuferschutz von 500 € (0,98 € inkl. MwSt.)"));
            $this->check("bltsprotection");
            $this->clickAndWait("test_PaymentNextStepBottom");
            $this->assertEquals("0,98 €", $this->getText("test_orderTsProtectionNet"));
            $this->assertTrue($this->isTextPresent("Trusted Shops Käuferschutz"));
            $this->check("test_OrderConfirmAGBBottom");
            $this->clickAndWait("test_OrderSubmitBottom");

            //order > 500eur
            $this->clickAndWait("test_link_footer_home");
            $this->clickAndWait("test_toBasket_WeekSpecial_1001");
            $this->clickAndWait("test_RightBasketOpen");
            $this->type("test_basketAm_1001_1", "6");
            $this->clickAndWait("test_basketUpdate");
            $this->clickAndWait("test_BasketNextStepTop");
            $this->clickAndWait("test_UserNextStepTop");
            $this->assertTrue($this->isElementPresent("bltsprotection"));
            $this->assertTrue($this->isElementPresent("stsprotection"));
            $this->assertEquals("Käuferschutz von 500 € (0,98 € inkl. MwSt.) Käuferschutz von 1500 € (2,94 € inkl. MwSt.)", $this->getText("stsprotection"));
            $this->select("stsprotection", "label=Käuferschutz von 1500 € (2,94 € inkl. MwSt.)");
            $this->check("bltsprotection");
            $this->clickAndWait("test_PaymentNextStepBottom");
            $this->assertEquals("2,94 €", $this->getText("test_orderTsProtectionNet"));
            $this->assertTrue($this->isTextPresent("Trusted Shops Käuferschutz"));
            $this->check("test_OrderConfirmAGBBottom");
            $this->clickAndWait("test_OrderSubmitBottom");

            //checking orders in admin
            $this->loginAdmin("Administer Orders", "Orders");
            $this->waitForElement("link=12");
            $this->openTab("link=12");
            $this->assertTrue($this->isTextPresent("0,98"));
            $this->frame("list");
            $this->openTab("link=13");
            $this->assertTrue($this->isTextPresent("2,94"));
    }

     /**
     * testing trusted shops. Raiting of eShop
     * @group admin
     * @group trustedShopsBasic
     * @group basic
     */
    public function testTsRatings()
    {
            //trusted shops are disabled
            $this->openShop();
            $this->clickAndWait("test_Lang_Deutsch");
            $this->assertFalse($this->isElementPresent("test_RightSideTsWidgetBox"));

             //setupping ts
            $this->loginAdminTs("link=Customer ratings", "//li[@id='nav-2-10-1']/a/b");
            $this->frame("list");
            $this->waitForElement("link=Interface");
            $this->clickAndWaitFrame("link=Interface", "edit");
            $this->frame("edit");
            $this->waitForElement("confaarrs[aTsLangIds][de]");
            $this->assertTrue($this->isElementPresent("confaarrs[aTsLangIds][de]"));
            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->type("confaarrs[aTsLangIds][en]", "XCDD9234E25B44A2119C3967A77A6EDBE");
            $this->check("//input[@name='confbools[blTsWidget]' and @value='true']");
            $this->check("//input[@name='confbools[blTsThankyouReview]' and @value='true']");
            $this->check("//input[@name='confbools[blTsOrderEmailReview]' and @value='true']");
            $this->check("//input[@name='confbools[blTsOrderSendEmailReview]' and @value='true']");
            $this->clickAndWait("save");
            $this->assertEquals("X41495A6E65ECDDCD554A02C0601D1C97", $this->getValue("confaarrs[aTsLangIds][de]"));
            $this->assertEquals("XCDD9234E25B44A2119C3967A77A6EDBE", $this->getValue("confaarrs[aTsLangIds][en]"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsWidget]' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsThankyouReview]' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsOrderEmailReview]' and @value='true']"));
            $this->assertEquals("on", $this->getValue("//input[@name='confbools[blTsOrderSendEmailReview]' and @value='true']"));
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact"));

            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C9a");
            $this->clickAndWait("save");
            $this->assertTrue($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact"));
            $this->assertEquals("X41495A6E65ECDDCD554A02C0601D1C9a", $this->getValue("confaarrs[aTsLangIds][de]"));

            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->clickAndWait("save");
            $this->type("confaarrs[aTsLangIds][de]", "X41495A6E65ECDDCD554A02C0601D1C97");
            $this->assertFalse($this->isTextPresent("Invalid Trusted Shops ID. Please sign up here or contact"));

            //checking in frontend
            $this->openShop();
            $this->clickAndWait("test_Lang_Deutsch");
            $this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->clickAndWait("test_toBasket_WeekSpecial_1001");
            $this->clickAndWait("test_RightBasketOpen");
            $this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->clickAndWait("test_BasketNextStepTop");
            $this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
            $this->type("test_RightLogin_Pwd", "useruser");
            $this->clickAndWait("test_RightLogin_Login");
            $this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->clickAndWait("test_UserNextStepTop");
            $this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->check("test_Payment_oxidcashondel");
            $this->clickAndWait("test_PaymentNextStepBottom");
            $this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
            $this->check("test_OrderConfirmAGBTop");
            $this->clickAndWait("test_OrderSubmitTop");$this->assertTrue($this->isElementPresent("test_RightSideTsWidgetBox"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Bewerten Sie unseren Shop!']"));
            $this->assertTrue($this->isElementPresent("//img[@alt='Trusted Shops Kundenbewertungen']"));
    }
}