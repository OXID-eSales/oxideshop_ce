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

class Acceptance_userRegistrationFrontendTest extends oxidAdditionalSeleniumFunctions
{
    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Frontend: user registration ----------------------------------

    /**
     * simple user account opening
     * @group user
     * @group main
     * @group registration
     */
    public function testStandardUserRegistration()
    {
        //creating user
        $this->openShop();
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Register']");
        $this->assertEquals("You are here: / Register", $this->getText("breadCrumb"));
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("userPassword", "user11");
        $this->type("userPasswordConfirm", "user11");
        $this->assertEquals("off", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->uncheck("//input[@name='blnewssubscribed' and @value='1']");
        $this->type("invadr[oxuser__oxfname]", "user1 name_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "user1 last name_šÄßüл");
        $this->type("invadr[oxuser__oxcompany]", "user1 company_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "user1 street_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "11");
        $this->type("invadr[oxuser__oxcity]", "user1 city_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user1 additional info_šÄßüл");
        $this->assertFalse($this->isVisible("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_invadr[oxuser__oxstateid]");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Berlin");
        $this->type("invadr[oxuser__oxfon]", "111-111");
        $this->type("invadr[oxuser__oxfax]", "111-111-111");
        $this->type("invadr[oxuser__oxmobfon]", "111-111111");
        $this->type("invadr[oxuser__oxprivfon]", "111111111");
        $this->type("invadr[oxuser__oxbirthdate][day]", "11");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=November");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1981");
        $this->clickAndWait("accUserSaveTop");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));
        $this->assertEquals("user1 name_šÄßüл user1 last name_šÄßüл", $this->getText("//ul[@id='topMenu']/li/a"));
        $this->assertEquals("You are here: / Register", $this->getText("breadCrumb"));

        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user1");
        $this->clickAndWait("submitit");
        $this->assertEquals("user1 last name_šÄßüл user1 name_šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user1 last name_šÄßüл user1 name_šÄßüл");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user1 name_šÄßüл", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user1 last name_šÄßüл", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user1 company_šÄßüл", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user1 street_šÄßüл", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user1 city_šÄßüл", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user1 additional info_šÄßüл", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("BE", $this->getValue("editval[oxuser__oxstateid]"));
        $this->assertEquals("111-111", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("111-111-111", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1981", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("Yes"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("111111111", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("111-111111", $this->getValue("editval[oxuser__oxmobfon]"));
    }

    /**
     * user registers for newsletter and later performs order with option 1 (no registration) and same email
     * @group user
     */
    public function testNewsletterRegOwerwriteOptionWithoutRegistration()
    {
        $this->openShop();
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "birute01@nfq.lt");
        $this->clickAndWait("//div[@id='panel']//button[text()='Subscribe']");
        $this->type("newsletterFname", "user2 name");
        $this->type("newsletterLname", "user2 last name");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTrue($this->isTextPresent("thank you for subscribing to our newsletter"));

        //override user with ordering product via option 1
        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //option 1
        $this->assertEquals("Purchase without Registration", $this->getText("//div[@id='optionNoRegistration']/h3"));
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user2 name2_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "user2 last name2_šÄßüл");
        $this->type("invadr[oxuser__oxcompany]", "user2 company_šÄßüл");
        $this->type("invadr[oxuser__oxaddinfo]", "user2 additional info_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "user2 street_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "2");
        $this->type("invadr[oxuser__oxzip]", "22");
        $this->type("invadr[oxuser__oxcity]", "user2 city_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_invadr[oxuser__oxstateid]");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Berlin");
        $this->type("invadr[oxuser__oxfon]", "222-222");
        $this->type("invadr[oxuser__oxfax]", "222-222-222");
        $this->type("invadr[oxuser__oxmobfon]", "222-222222");
        $this->type("invadr[oxuser__oxprivfon]", "222222222");
        $this->type("invadr[oxuser__oxbirthdate][day]", "2");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=February");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1982");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->type("deladr[oxaddress__oxfname]", "user2_2 name_šÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "user2_2 last name_šÄßüл");
        $this->type("deladr[oxaddress__oxcompany]", "user2_2 company_šÄßüл");
        $this->type("deladr[oxaddress__oxaddinfo]", "user2_2 additional info_šÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "user2_2 street_šÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "2_2");
        $this->type("deladr[oxaddress__oxzip]", "2_2");
        $this->type("deladr[oxaddress__oxcity]", "user2_2 city_šÄßüл");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_deladr[oxaddress__oxstateid]");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Brandenburg");
        $this->type("deladr[oxaddress__oxfon]", "222-222");
        $this->type("deladr[oxaddress__oxfax]", "222-222-222");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("E-mail: birute01@nfq.lt user2 company_šÄßüл user2 additional info_šÄßüл Mr user2 name2_šÄßüл user2 last name2_šÄßüл user2 street_šÄßüл 2 BE 22 user2 city_šÄßüл Germany Phone: 222-222 Fax: 222-222-222 Celluar Phone: 222-222222 Personal Phone: 222222222", $this->clearString($this->getText("//div[@id='orderAddress']/dl[1]/dd")));
        $this->assertEquals("user2_2 company_šÄßüл user2_2 additional info_šÄßüл Mr user2_2 name_šÄßüл user2_2 last name_šÄßüл user2_2 street_šÄßüл 2_2 BB 2_2 user2_2 city_šÄßüл Germany Phone: 222-222 Fax: 222-222-222", $this->clearString($this->getText("//div[@id='orderAddress']/dl[2]/dd")));

        //check in admin if information is saved correctly
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user2");
        $this->clickAndWait("submitit");
        $this->assertEquals("user2 last name2_šÄßüл user2 name2_šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user2 last name2_šÄßüл user2 name2_šÄßüл");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user2 name2_šÄßüл", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user2 last name2_šÄßüл", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user2 company_šÄßüл", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user2 street_šÄßüл", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("2", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("22", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user2 city_šÄßüл", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user2 additional info_šÄßüл", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("BE", $this->getValue("editval[oxuser__oxstateid]"));
        $this->assertEquals("222-222", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("222-222-222", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("02", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("02", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1982", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("No"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("222222222", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("222-222222", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user2_2 name_šÄßüл user2_2 last name_šÄßüл, user2_2 street_šÄßüл, user2_2 city_šÄßüл");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user2_2 name_šÄßüл", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user2_2 last name_šÄßüл", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user2_2 company_šÄßüл", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user2_2 street_šÄßüл", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("2_2", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("2_2", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user2_2 city_šÄßüл", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user2_2 additional info_šÄßüл", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("BB", $this->getValue("editval[oxaddress__oxstateid]"));
        $this->assertEquals("222-222", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("222-222-222", $this->getValue("editval[oxaddress__oxfax]"));
    }

    /**
     * user registers for newsletter and later performs order with Registration in order step2 and same email
     * @group user
     * @group registration
     */
    public function testNewsletterRegOwerwriteOptionRegistration()
    {
        $this->openShop();
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "birute01@nfq.lt");
        $this->clickAndWait("//div[@id='panel']//button[text()='Subscribe']");
        $this->type("newsletterFname", "user3 name");
        $this->type("newsletterLname", "user3 last name");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTrue($this->isTextPresent("thank you for subscribing to our newsletter"));

        //override user with ordering product and registering in order step2
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->assertEquals("Open personal customer account", $this->getText("//div[@id='optionRegistration']/h3"));
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user3 name2_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "user3 last name2_šÄßüл");
        $this->type("invadr[oxuser__oxcompany]", "user3 company_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "user3 street_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "3");
        $this->type("invadr[oxuser__oxzip]", "33");
        $this->type("invadr[oxuser__oxcity]", "user3 city_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user3 additional info_šÄßüл");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_invadr[oxuser__oxstateid]");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Berlin");
        $this->type("invadr[oxuser__oxfon]", "333-333");
        $this->type("invadr[oxuser__oxfax]", "333-333-333");
        $this->type("invadr[oxuser__oxmobfon]", "333-333333");
        $this->type("invadr[oxuser__oxprivfon]", "333333333");
        $this->type("invadr[oxuser__oxbirthdate][day]", "3");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=March");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1983");
        $this->type("orderRemark", "remark text");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));
        $this->type("deladr[oxaddress__oxfname]", "user3_2 name_šÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "user3_2 last name_šÄßüл");
        $this->type("deladr[oxaddress__oxcompany]", "user3_2 company_šÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "user3_2 street_šÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "3_2");
        $this->type("deladr[oxaddress__oxzip]", "3_2");
        $this->type("deladr[oxaddress__oxcity]", "user3_2 city_šÄßüл");
        $this->type("deladr[oxaddress__oxaddinfo]", "user3_2 additional info_šÄßüл");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->waitForItemAppear("oxStateSelect_deladr[oxaddress__oxstateid]");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Brandenburg");
        $this->type("deladr[oxaddress__oxfon]", "333-333");
        $this->type("deladr[oxaddress__oxfax]", "333-333-333");
        $this->type("userPassword", "user33");
        $this->type("userPasswordConfirm", "user33");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("What I wanted to say remark text"));
        $this->assertEquals("E-mail: birute01@nfq.lt user3 company_šÄßüл user3 additional info_šÄßüл Mr user3 name2_šÄßüл user3 last name2_šÄßüл user3 street_šÄßüл 3 BE 33 user3 city_šÄßüл Germany Phone: 333-333 Fax: 333-333-333 Celluar Phone: 333-333333 Personal Phone: 333333333", $this->clearString($this->getText("//div[@id='orderAddress']/dl[1]/dd")));
        $this->assertEquals("user3_2 company_šÄßüл user3_2 additional info_šÄßüл Mr user3_2 name_šÄßüл user3_2 last name_šÄßüл user3_2 street_šÄßüл 3_2 BB 3_2 user3_2 city_šÄßüл Germany Phone: 333-333 Fax: 333-333-333", $this->clearString($this->getText("//div[@id='orderAddress']/dl[2]/dd")));

        //check in admin if information is saved correctly
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user3");
        $this->clickAndWait("submitit");
        $this->assertEquals("user3 last name2_šÄßüл user3 name2_šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user3 last name2_šÄßüл user3 name2_šÄßüл");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user3 name2_šÄßüл", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user3 last name2_šÄßüл", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user3 company_šÄßüл", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user3 street_šÄßüл", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("3", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("33", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user3 city_šÄßüл", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user3 additional info_šÄßüл", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("BE", $this->getValue("editval[oxuser__oxstateid]"));
        $this->assertEquals("333-333", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("333-333-333", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("03", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("03", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1983", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("Yes"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("333333333", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("333-333333", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user3_2 name_šÄßüл user3_2 last name_šÄßüл, user3_2 street_šÄßüл, user3_2 city_šÄßüл");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user3_2 name_šÄßüл", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user3_2 last name_šÄßüл", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user3_2 company_šÄßüл", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user3_2 street_šÄßüл", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("3_2", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("3_2", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user3_2 city_šÄßüл", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user3_2 additional info_šÄßüл", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("BB", $this->getValue("editval[oxaddress__oxstateid]"));
        $this->assertEquals("333-333", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("333-333-333", $this->getValue("editval[oxaddress__oxfax]"));
    }

    /**
     * user performs order with option 1 and same email twice
     * @group user
     * @group registration
     */
    public function testRegOptionNoRegistrationTwice()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //option 1
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->select("invadr[oxuser__oxsal]", "label=Mrs");
        $this->type("invadr[oxuser__oxfname]", "user4 name_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "user4 last name_šÄßüл");
        $this->type("invadr[oxuser__oxcompany]", "user4 company_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "user4 street_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "4");
        $this->type("invadr[oxuser__oxzip]", "44");
        $this->type("invadr[oxuser__oxcity]", "user4 city_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxaddinfo]", "user4 additional info_šÄßüл");
        $this->type("invadr[oxuser__oxfon]", "444-444");
        $this->type("invadr[oxuser__oxfax]", "444-444-444");
        $this->type("invadr[oxuser__oxmobfon]", "444-444444");
        $this->type("invadr[oxuser__oxprivfon]", "444444444");
        $this->type("invadr[oxuser__oxbirthdate][day]", "4");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=April");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1984");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user4_2 name_šÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "user4_2 last name_šÄßüл");
        $this->type("deladr[oxaddress__oxcompany]", "user4_2 company_šÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "user4_2 street_šÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "4_2");
        $this->type("deladr[oxaddress__oxzip]", "4_2");
        $this->type("deladr[oxaddress__oxcity]", "user4_2 city_šÄßüл");
        $this->type("deladr[oxaddress__oxaddinfo]", "user4_2 additional info_šÄßüл");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Belgium");
        $this->type("deladr[oxaddress__oxfon]", "444-444");
        $this->type("deladr[oxaddress__oxfax]", "444-444-444");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("Currently we have no shipping method set up for this country."));
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //second order with option 1
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //option 1
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");
        $this->type("//input[@id='userLoginName']", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user4 name2");
        $this->type("invadr[oxuser__oxlname]", "user4 last name2");
        $this->select("invadr[oxuser__oxsal]", "label=Mr");
        $this->type("invadr[oxuser__oxcompany]", "user4 company2");
        $this->type("invadr[oxuser__oxstreet]", "user4 street2");
        $this->type("invadr[oxuser__oxstreetnr]", "4-2");
        $this->type("invadr[oxuser__oxzip]", "44-2");
        $this->type("invadr[oxuser__oxcity]", "user4 city2");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user4 additional info2");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxfon]", "444-4442");
        $this->type("invadr[oxuser__oxfax]", "444-444-4442");
        $this->type("invadr[oxuser__oxmobfon]", "444-4444442");
        $this->type("invadr[oxuser__oxprivfon]", "4444444442");
        $this->type("invadr[oxuser__oxbirthdate][day]", "5");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=May");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1985");
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user4_2 name2");
        $this->type("deladr[oxaddress__oxlname]", "user4_2 last name2");
        $this->type("deladr[oxaddress__oxcompany]", "user4_2 company2");
        $this->type("deladr[oxaddress__oxstreet]", "user4_2 street2");
        $this->type("deladr[oxaddress__oxstreetnr]", "4_22");
        $this->type("deladr[oxaddress__oxzip]", "4_22");
        $this->type("deladr[oxaddress__oxcity]", "user4_2 city2");
        $this->type("deladr[oxaddress__oxaddinfo]", "user4_2 additional info2");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->type("deladr[oxaddress__oxfon]", "444-4442");
        $this->type("deladr[oxaddress__oxfax]", "444-444-4442");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("userLoginName"));
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"));

        //check in admin if information is saved correctly
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user4");
        $this->clickAndWait("submitit");
        $this->assertEquals("user4 last name2 user4 name2", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user4 last name2 user4 name2");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("user4 name2", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user4 last name2", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user4 company2", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user4 street2", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("4-2", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("44-2", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user4 city2", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user4 additional info2", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("444-4442", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("444-444-4442", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("05", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("05", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1985", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("No"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("4444444442", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("444-4444442", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user4_2 name2 user4_2 last name2, user4_2 street2, user4_2 city2");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user4_2 name2", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user4_2 last name2", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user4_2 company2", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user4_2 street2", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("4_22", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("4_22", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user4_2 city2", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user4_2 additional info2", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));;
        $this->assertEquals("444-4442", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("444-444-4442", $this->getValue("editval[oxaddress__oxfax]"));
    }

    /**
     * user performs order with option1 and and later with same email and option3
     * @group user
     * @group registration
     */
    public function testRegOptionNoRegOptionReg()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        //option 1
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user5 name");
        $this->type("invadr[oxuser__oxlname]", "user5 last name");
        $this->select("invadr[oxuser__oxsal]", "label=Mrs");
        $this->type("invadr[oxuser__oxcompany]", "user5 company");
        $this->type("invadr[oxuser__oxstreet]", "user5 street");
        $this->type("invadr[oxuser__oxstreetnr]", "5");
        $this->type("invadr[oxuser__oxzip]", "55");
        $this->type("invadr[oxuser__oxcity]", "user5 city");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user5 additional info");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxfon]", "555-555");
        $this->type("invadr[oxuser__oxfax]", "555-555-555");
        $this->type("invadr[oxuser__oxmobfon]", "555-555555");
        $this->type("invadr[oxuser__oxprivfon]", "555555555");
        $this->type("invadr[oxuser__oxbirthdate][day]", "5");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=May");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1985");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user5_2 name");
        $this->type("deladr[oxaddress__oxlname]", "user5_2 last name");
        $this->type("deladr[oxaddress__oxcompany]", "user5_2 company");
        $this->type("deladr[oxaddress__oxstreet]", "user5_2 street");
        $this->type("deladr[oxaddress__oxstreetnr]", "5_2");
        $this->type("deladr[oxaddress__oxzip]", "5_2");
        $this->type("deladr[oxaddress__oxcity]", "user5_2 city");
        $this->type("deladr[oxaddress__oxaddinfo]", "user5_2 additional info");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Belgium");
        $this->type("deladr[oxaddress__oxfon]", "555-555");
        $this->type("deladr[oxaddress__oxfax]", "555-555-555");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"));

        //second order with registration at step2
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");

        $this->clickAndWait("//div[@id='optionRegistration']//button");
        $this->type("//input[@id='userLoginName']", "birute01@nfq.lt");
        $this->select("invadr[oxuser__oxsal]", "label=Mr");
        $this->type("invadr[oxuser__oxfname]", "user5 name2");
        $this->type("invadr[oxuser__oxlname]", "user5 last name2");
        $this->type("invadr[oxuser__oxcompany]", "user5 company2");
        $this->type("invadr[oxuser__oxstreet]", "user5 street2");
        $this->type("invadr[oxuser__oxstreetnr]", "52");
        $this->type("invadr[oxuser__oxzip]", "552");
        $this->type("invadr[oxuser__oxcity]", "user5 city2");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user5 additional info2");
        $this->select("invadr[oxuser__oxcountryid]", "label=Finland");
        $this->type("invadr[oxuser__oxfon]", "555-5552");
        $this->type("invadr[oxuser__oxfax]", "555-555-5552");
        $this->type("invadr[oxuser__oxmobfon]", "555-5555552");
        $this->type("invadr[oxuser__oxprivfon]", "5555555552");
        $this->type("invadr[oxuser__oxbirthdate][day]", "6");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=June");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1986");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user5_2 name2");
        $this->type("deladr[oxaddress__oxlname]", "user5_2 last name2");
        $this->type("deladr[oxaddress__oxcompany]", "user5_2 company2");
        $this->type("deladr[oxaddress__oxstreet]", "user5_2 street2");
        $this->type("deladr[oxaddress__oxstreetnr]", "5_22");
        $this->type("deladr[oxaddress__oxzip]", "5_22");
        $this->type("deladr[oxaddress__oxcity]", "user5_2 city2");
        $this->type("deladr[oxaddress__oxaddinfo]", "user5_2 additional info2");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->type("deladr[oxaddress__oxfon]", "555-55552");
        $this->type("deladr[oxaddress__oxfax]", "555-555-55552");
        $this->type("//input[@id='userPassword']", "user55");
        $this->type("//input[@id='userPasswordConfirm']", "user55");

        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //check in admin if information is saved correctly
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user5");
        $this->clickAndWait("submitit");
        $this->assertEquals("user5 last name2 user5 name2", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user5 last name2 user5 name2");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("user5 name2", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user5 last name2", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user5 company2", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user5 street2", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("52", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("552", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user5 city2", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user5 additional info2", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Finland", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("555-5552", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("555-555-5552", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("06", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("06", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1986", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("Yes"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("5555555552", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("555-5555552", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user5_2 name2 user5_2 last name2, user5_2 street2, user5_2 city2");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user5_2 name2", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user5_2 last name2", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user5_2 company2", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user5_2 street2", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("5_22", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("5_22", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user5_2 city2", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user5_2 additional info2", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));;
        $this->assertEquals("555-55552", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("555-555-55552", $this->getValue("editval[oxaddress__oxfax]"));
    }

    /**
     * user performs order with option3 twice, use wrong pass second time
     * @group user
     * @group registration
     */
    public function testRegOptionRegisterTwiceWrongPass()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $this->type("//input[@id='userLoginName']", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user7 name_šÄßüл");
        $this->type("invadr[oxuser__oxlname]", "user7 last name_šÄßüл");
        $this->select("invadr[oxuser__oxsal]", "label=Mrs");
        $this->type("invadr[oxuser__oxcompany]", "user7 company_šÄßüл");
        $this->type("invadr[oxuser__oxstreet]", "user7 street_šÄßüл");
        $this->type("invadr[oxuser__oxstreetnr]", "7");
        $this->type("invadr[oxuser__oxzip]", "77");
        $this->type("invadr[oxuser__oxcity]", "user7 city_šÄßüл");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user7 additional info_šÄßüл");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxfon]", "777-777");
        $this->type("invadr[oxuser__oxfax]", "777-777-777");
        $this->type("invadr[oxuser__oxmobfon]", "777-777777");
        $this->type("invadr[oxuser__oxprivfon]", "777777777");
        $this->type("invadr[oxuser__oxbirthdate][day]", "7");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=July");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1987");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user7_2 name_šÄßüл");
        $this->type("deladr[oxaddress__oxlname]", "user7_2 last name_šÄßüл");
        $this->type("deladr[oxaddress__oxcompany]", "user7_2 company_šÄßüл");
        $this->type("deladr[oxaddress__oxstreet]", "user7_2 street_šÄßüл");
        $this->type("deladr[oxaddress__oxstreetnr]", "7_2");
        $this->type("deladr[oxaddress__oxzip]", "7_2");
        $this->type("deladr[oxaddress__oxcity]", "user7_2 city_šÄßüл");
        $this->type("deladr[oxaddress__oxaddinfo]", "user7_2 additional info_šÄßüл");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Belgium");
        $this->type("deladr[oxaddress__oxfon]", "777-7777");
        $this->type("deladr[oxaddress__oxfax]", "777-777-7777");
        $this->type("userPassword", "user77");
        $this->type("userPasswordConfirm", "user77");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"));

        //second order with registration in order step2
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $this->type("//input[@id='userLoginName']", "birute01@nfq.lt");
        $this->select("invadr[oxuser__oxsal]", "label=Mr");
        $this->type("invadr[oxuser__oxfname]", "user7 name2");
        $this->type("invadr[oxuser__oxlname]", "user7 last name2");
        $this->type("invadr[oxuser__oxcompany]", "user7 company2");
        $this->type("invadr[oxuser__oxstreet]", "user7 street2");
        $this->type("invadr[oxuser__oxstreetnr]", "72");
        $this->type("invadr[oxuser__oxzip]", "772");
        $this->type("invadr[oxuser__oxcity]", "user7 city2");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user7 additional info2");
        $this->select("invadr[oxuser__oxcountryid]", "label=Finland");
        $this->type("invadr[oxuser__oxfon]", "777-7772");
        $this->type("invadr[oxuser__oxfax]", "777-777-7772");
        $this->type("invadr[oxuser__oxmobfon]", "777-7777772");
        $this->type("invadr[oxuser__oxprivfon]", "7777777772");
        $this->type("invadr[oxuser__oxbirthdate][day]", "8");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=August");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1988");
        $this->type("//input[@id='userPassword']", "aaaaaa");
        $this->type("//input[@id='userPasswordConfirm']", "aaaaaa");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Not possible to register birute01@nfq.lt. Maybe you have registered already previously?"));
        //check in admin if information is saved correctly
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user7");
        $this->clickAndWait("submitit");
        $this->assertEquals("user7 last name_šÄßüл user7 name_šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user7 last name_šÄßüл user7 name_šÄßüл");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user7 name_šÄßüл", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user7 last name_šÄßüл", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("user7 company_šÄßüл", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user7 street_šÄßüл", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("7", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("77", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user7 city_šÄßüл", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user7 additional info_šÄßüл", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Belgium", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("777-777", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("777-777-777", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("07", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("07", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1987", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("Yes"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("777777777", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("777-777777", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user7_2 name_šÄßüл user7_2 last name_šÄßüл, user7_2 street_šÄßüл, user7_2 city_šÄßüл");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user7_2 name_šÄßüл", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user7_2 last name_šÄßüл", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user7_2 company_šÄßüл", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user7_2 street_šÄßüл", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("7_2", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("7_2", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user7_2 city_šÄßüл", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user7_2 additional info_šÄßüл", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Belgium", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));;
        $this->assertEquals("777-7777", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("777-777-7777", $this->getValue("editval[oxaddress__oxfax]"));
    }

    /**
     * user performs order with option 1 and orders newsletter later for same email
     * @group user
     * @group registration
     */
    public function testRegOptionNoRegNewsletter()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user8 name");
        $this->type("invadr[oxuser__oxlname]", "user8 last name");
        $this->type("invadr[oxuser__oxcompany]", "user8 company");
        $this->type("invadr[oxuser__oxstreet]", "user8 street");
        $this->type("invadr[oxuser__oxstreetnr]", "8");
        $this->type("invadr[oxuser__oxzip]", "88");
        $this->type("invadr[oxuser__oxcity]", "user8 city");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user8 additional info");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxfon]", "888-888");
        $this->type("invadr[oxuser__oxfax]", "888-888-888");
        $this->type("invadr[oxuser__oxmobfon]", "888-888888");
        $this->type("invadr[oxuser__oxprivfon]", "888888888");
        $this->type("invadr[oxuser__oxbirthdate][day]", "8");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=August");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1988");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user8_2 name");
        $this->type("deladr[oxaddress__oxlname]", "user8_2 last name");
        $this->type("deladr[oxaddress__oxcompany]", "user8_2 company");
        $this->type("deladr[oxaddress__oxstreet]", "user8_2 street");
        $this->type("deladr[oxaddress__oxstreetnr]", "8_2");
        $this->type("deladr[oxaddress__oxzip]", "8_2");
        $this->type("deladr[oxaddress__oxcity]", "user8_2 city");
        $this->type("deladr[oxaddress__oxaddinfo]", "user8_2 additional info");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->type("deladr[oxaddress__oxfon]", "888-888");
        $this->type("deladr[oxaddress__oxfax]", "888-888-888");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Pay", $this->getText("breadCrumb"));

        //orders newsletter for same email but changes name
        $this->openShop();
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "birute01@nfq.lt");
        $this->clickAndWait("//div[@id='panel']//button[text()='Subscribe']");
        $this->type("newsletterFname", "user8 name2_šÄßüл");
        $this->type("newsletterLname", "user8 last name2_šÄßüл");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTrue($this->isTextPresent("thank you for subscribing to our newsletter"));

        //check in admin previous entered user information is not damaged
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user8");
        $this->clickAndWait("submitit");
        $this->assertEquals("user8 last name user8 name", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user8 last name user8 name");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user8 name", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user8 last name", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user8 company", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user8 street", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("8", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("88", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user8 city", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user8 additional info", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("888-888", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("888-888-888", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("08", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("08", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1988", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("No"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("888888888", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("888-888888", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user8_2 name user8_2 last name, user8_2 street, user8_2 city");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user8_2 name", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user8_2 last name", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user8_2 company", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user8_2 street", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("8_2", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("8_2", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user8_2 city", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user8_2 additional info", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));;
        $this->assertEquals("888-888", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("888-888-888", $this->getValue("editval[oxaddress__oxfax]"));
    }

    /**
     * user registers and orders newsletter later for same email
     * @group user
     * @group registration
     */
    public function testStandardUserRegAndNewsletter()
    {
        //creating user
        $this->openShop();
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Register']");
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("userPassword", "user99");
        $this->type("userPasswordConfirm", "user99");
        $this->type("invadr[oxuser__oxfname]", "user9 name");
        $this->type("invadr[oxuser__oxlname]", "user9 last name");
        $this->type("invadr[oxuser__oxcompany]", "user9 company");
        $this->type("invadr[oxuser__oxstreet]", "user9 street");
        $this->type("invadr[oxuser__oxstreetnr]", "9");
        $this->type("invadr[oxuser__oxzip]", "99");
        $this->type("invadr[oxuser__oxcity]", "user9 city");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user9 additional info");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxfon]", "999-999");
        $this->type("invadr[oxuser__oxfax]", "999-999-999");
        $this->type("invadr[oxuser__oxmobfon]", "999-999999");
        $this->type("invadr[oxuser__oxprivfon]", "999999999");
        $this->type("invadr[oxuser__oxbirthdate][day]", "9");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=September");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1989");
        $this->clickAndWait("//button[text()='Save']");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));
        //ordering newsletter
        $this->openShop();

        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "birute01@nfq.lt");
        $this->clickAndWait("//div[@id='panel']//button[text()='Subscribe']");
        $this->type("newsletterFname", "user9 name2");
        $this->type("newsletterLname", "user9 last name2");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTrue($this->isTextPresent("thank you for subscribing to our newsletter"));

        //check in admin previous entered user information is not damaged
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user9");
        $this->clickAndWait("submitit");
        $this->assertEquals("user9 last name user9 name", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user9 last name user9 name");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user9 name", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user9 last name", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user9 company", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user9 street", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("9", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("99", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user9 city", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user9 additional info", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("999-999", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("999-999-999", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("09", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("09", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1989", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("Yes"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("999999999", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("999-999999", $this->getValue("editval[oxuser__oxmobfon]"));
    }

    /**
     * user performs order with option3 twice, both time using good email and pass
     * @group user
     * @group registration
     */
    public function testRegOptionsRegistrationTwice()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "user6 name");
        $this->type("invadr[oxuser__oxlname]", "user6 last name");
        $this->select("invadr[oxuser__oxsal]", "label=Mrs");
        $this->type("invadr[oxuser__oxcompany]", "user6 company");
        $this->type("invadr[oxuser__oxstreet]", "user6 street");
        $this->type("invadr[oxuser__oxstreetnr]", "6");
        $this->type("invadr[oxuser__oxzip]", "66");
        $this->type("invadr[oxuser__oxcity]", "user6 city");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user6 additional info");
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxfon]", "666-666");
        $this->type("invadr[oxuser__oxfax]", "666-666-666");
        $this->type("invadr[oxuser__oxmobfon]", "666-666666");
        $this->type("invadr[oxuser__oxprivfon]", "666666666");
        $this->type("invadr[oxuser__oxbirthdate][day]", "6");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=June");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1986");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user6_2 name");
        $this->type("deladr[oxaddress__oxlname]", "user6_2 last name");
        $this->type("deladr[oxaddress__oxcompany]", "user6_2 company");
        $this->type("deladr[oxaddress__oxstreet]", "user6_2 street");
        $this->type("deladr[oxaddress__oxstreetnr]", "6_2");
        $this->type("deladr[oxaddress__oxzip]", "6_2");
        $this->type("deladr[oxaddress__oxcity]", "user6_2 city");
        $this->type("deladr[oxaddress__oxaddinfo]", "user6_2 additional info");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Belgium");
        $this->type("deladr[oxaddress__oxfon]", "666-6666");
        $this->type("deladr[oxaddress__oxfax]", "666-666-6666");
        $this->type("userPassword", "user66");
        $this->type("userPasswordConfirm", "user66");

        $this->clickAndWait("//button[text()='Continue to Next Step']");

        //second order with option3
        $this->openShop();
        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->select("invadr[oxuser__oxsal]", "label=Mr");
        $this->type("invadr[oxuser__oxfname]", "user6 name2");
        $this->type("invadr[oxuser__oxlname]", "user6 last name2");
        $this->type("invadr[oxuser__oxcompany]", "user6 company2");
        $this->type("invadr[oxuser__oxstreet]", "user6 street2");
        $this->type("invadr[oxuser__oxstreetnr]", "62");
        $this->type("invadr[oxuser__oxzip]", "662");
        $this->type("invadr[oxuser__oxcity]", "user6 city2");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user6 additional info2");
        $this->select("invadr[oxuser__oxcountryid]", "label=Finland");
        $this->type("invadr[oxuser__oxfon]", "666-6662");
        $this->type("invadr[oxuser__oxfax]", "666-666-6662");
        $this->type("invadr[oxuser__oxmobfon]", "666-6666662");
        $this->type("invadr[oxuser__oxprivfon]", "6666666662");
        $this->type("invadr[oxuser__oxbirthdate][day]", "7");
        $this->select("invadr[oxuser__oxbirthdate][month]", "label=July");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1987");
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "user6_2 name2");
        $this->type("deladr[oxaddress__oxlname]", "user6_2 last name2");
        $this->type("deladr[oxaddress__oxcompany]", "user6_2 company2");
        $this->type("deladr[oxaddress__oxstreet]", "user6_2 street2");
        $this->type("deladr[oxaddress__oxstreetnr]", "6_22");
        $this->type("deladr[oxaddress__oxzip]", "6_22");
        $this->type("deladr[oxaddress__oxcity]", "user6_2 city2");
        $this->type("deladr[oxaddress__oxaddinfo]", "user6_2 additional info2");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->type("deladr[oxaddress__oxfon]", "666-66662");
        $this->type("deladr[oxaddress__oxfax]", "666-666-66662");
        $this->type("userPassword", "user66");
        $this->type("userPasswordConfirm", "user66");

        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Not possible to register birute01@nfq.lt. Maybe you have registered already previously?"));

        //check in admin if information is saved correctly
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user6");
        $this->clickAndWait("submitit");
        $this->assertEquals("user6 last name user6 name", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user6 last name user6 name");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("user6 name", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user6 last name", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user6 company", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user6 street", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("6", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("66", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user6 city", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user6 additional info", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Belgium", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("666-666", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("666-666-666", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("06", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("06", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1986", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTrue($this->isTextPresent("Yes"));
        $this->frame("list");
        $this->openTab("link=Extended");
        $this->assertEquals("666666666", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("666-666666", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user6_2 name user6_2 last name, user6_2 street, user6_2 city");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user6_2 name", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user6_2 last name", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user6_2 company", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user6_2 street", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("6_2", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("6_2", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user6_2 city", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user6_2 additional info", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Belgium", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));;
        $this->assertEquals("666-6666", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("666-666-6666", $this->getValue("editval[oxaddress__oxfax]"));
    }


}

