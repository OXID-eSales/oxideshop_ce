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

class AcceptanceInternational_internationalTestBasic extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

 // -------------------------- Selenium tests for UTF-8 shop version ---------------------------


    /**
     * simple user account opening
     * @group user
     * @group internationalBasic
     */
    public function testStandardUserRegistrationInternational()
    {
        //creating user
        $this->openShop();
        $this->clickAndWait("test_RightLogin_Register");
        $this->type("test_lgn_usr", "birute01@nfq.lt");
        $this->type("userPassword", "user11");
        $this->type("userPasswordConfirm", "user11");
        $this->assertEquals("off", $this->getValue("document.order.blnewssubscribed[1]"));
        $this->uncheck("document.order.blnewssubscribed[1]");
        $this->type("invadr[oxuser__oxfname]", "user1 name_šųößлы");
        $this->type("invadr[oxuser__oxlname]", "user1 last name_šųößлы");
        $this->type("invadr[oxuser__oxcompany]", "user1 company_šųößлы");
        $this->type("invadr[oxuser__oxstreet]", "user1 street_šųößлы");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "11");
        $this->type("invadr[oxuser__oxcity]", "user1 city_šųößлы");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user1 additional info_šųößлы");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxfon]", "111-111");
        $this->type("invadr[oxuser__oxfax]", "111-111-111");
        $this->type("invadr[oxuser__oxmobfon]", "111-111111");
        $this->type("invadr[oxuser__oxprivfon]", "111111111");
        $this->type("invadr[oxuser__oxbirthdate][day]", "11");
        $this->type("invadr[oxuser__oxbirthdate][month]", "11");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1981");
        $this->clickAndWait("blshowshipaddress");
        $this->assertTrue($this->isElementPresent("test_lgn_usr"), "form fields for delivery address is not shown");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("test_lgn_usr"));
        $this->assertEquals("", $this->getValue("userPassword"));
        $this->assertEquals("", $this->getValue("userPasswordConfirm"));
        $this->assertEquals("off", $this->getValue("document.order.blnewssubscribed[1]"));
        $this->assertEquals("user1 name_šųößлы", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("user1 last name_šųößлы", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("user1 company_šųößлы", $this->getValue("invadr[oxuser__oxcompany]"));
        $this->assertEquals("user1 street_šųößлы", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("11", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("user1 city_šųößлы", $this->getValue("invadr[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("invadr[oxuser__oxustid]"));
        $this->assertEquals("user1 additional info_šųößлы", $this->getValue("invadr[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("111-111", $this->getValue("invadr[oxuser__oxfon]"));
        $this->assertEquals("111-111-111", $this->getValue("invadr[oxuser__oxfax]"));
        $this->assertEquals("111-111111", $this->getValue("invadr[oxuser__oxmobfon]"));
        $this->assertEquals("111111111", $this->getValue("invadr[oxuser__oxprivfon]"));
        $this->assertEquals("11", $this->getValue("invadr[oxuser__oxbirthdate][day]"));
        $this->assertEquals("11", $this->getValue("invadr[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1981", $this->getValue("invadr[oxuser__oxbirthdate][year]"));
        $this->type("userPassword", "user11");
        $this->type("userPasswordConfirm", "user11");
        $this->assertTrue($this->isVisible("deladr[oxaddress__oxfname]"));
        $this->type("deladr[oxaddress__oxfname]", "user1_2 name_šųößлы");
        $this->type("deladr[oxaddress__oxlname]", "user1_2 last name_šųößлы");
        $this->type("deladr[oxaddress__oxcompany]", "user1_2 company_šųößлы");
        $this->type("deladr[oxaddress__oxstreet]", "user1_2 street_šųößлы");
        $this->type("deladr[oxaddress__oxstreetnr]", "1_2");
        $this->type("deladr[oxaddress__oxzip]", "1_2");
        $this->type("deladr[oxaddress__oxcity]", "user1_2 city_šųößлы");
        $this->type("deladr[oxaddress__oxaddinfo]", "user1_2 additional info_šųößлы");
        $this->select("deladr[oxaddress__oxcountryid]", "label=Germany");
        $this->type("deladr[oxaddress__oxfon]", "111-222");
        $this->type("deladr[oxaddress__oxfax]", "111-111-222");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));
        $this->assertTrue($this->isTextPresent("You're logged in as:"));
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user1");
        $this->clickAndWait("submitit");
        $this->assertEquals("user1 last name_šųößлы user1 name_šųößлы", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->openTab("link=user1 last name_šųößлы user1 name_šųößлы");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user1 name_šųößлы", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user1 last name_šųößлы", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user1 company_šųößлы", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user1 street_šųößлы", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user1 city_šųößлы", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user1 additional info_šųößлы", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
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
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->selectAndWait("oxaddressid", "label=user1_2 name_šųößлы user1_2 last name_šųößлы, user1_2 street_šųößлы, user1_2 city_šųößлы");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("user1_2 name_šųößлы", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("user1_2 last name_šųößлы", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("user1_2 company_šųößлы", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("user1_2 street_šųößлы", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("1_2", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("1_2", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("user1_2 city_šųößлы", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("user1_2 additional info_šųößлы", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("111-222", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("111-111-222", $this->getValue("editval[oxaddress__oxfax]"));
    }



    /**
     * Search in frontend
     * @group navigation
     * @group internationalBasic
     */
    public function testFrontendSearchInternational()
    {
        $this->openShop();
        //searching for 1 product (using product search field value)
        $this->type("//input[@id='f.search.param']", "šųößлы1000");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertTrue($this->isElementPresent("//a[@id='rssSearchProducts']"));
        $this->assertEquals("1 Hits for \"šųößлы1000\"", $this->getText("test_smallHeader"));
        $this->assertEquals("Test product 0 short desc [EN] šųößлы", $this->getText("test_shortDesc_Search_1000"));
        $this->assertEquals("Test product 0 [EN] šųößлы", $this->getText("test_title_Search_1000"));
        $this->clickAndWait("test_title_Search_1000");
        $this->assertEquals("You are here: / Search result for \"šųößлы1000\"", $this->getText("path"));
        $this->assertEquals("Test product 0 [EN] šųößлы", $this->getText("test_product_name"));
        $this->clickAndWait("test_BackOverviewTop");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertFalse($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_toCmp_Search_1000");
        $this->assertEquals("remove from compare list", $this->getText("test_removeCmp_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_AccComparison"));
        $this->clickAndWait("test_removeCmp_Search_1000");
        $this->assertFalse($this->isElementPresent("test_AccComparison"));

        $this->type("//input[@id='f.search.param']", "100");
        $this->select("searchcnid", "index=7");
        $this->select("test_searchManufacturerSelect", "index=0");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("2 Hits for \"100\"", $this->getText("test_smallHeader"));
        $this->assertFalse($this->isElementPresent("test_title_Search_1000"));
        $this->assertFalse($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));
        //search by vendor
        $this->type("//input[@id='f.search.param']", "100");
        $this->select("searchcnid", "index=0");
        $this->select("test_searchManufacturerSelect", "label=Manufacturer [EN] šųößлы");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("4 Hits for \"100\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));

        //search in language
        $this->type("//input[@id='f.search.param']", "[EN] šųößлы");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("You are here: / Search", $this->getText("path"));
        $this->assertEquals("4 Hits for \"[EN] šųößлы\"", $this->getText("test_smallHeader"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1000"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1001"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1002"));
        $this->assertTrue($this->isElementPresent("test_title_Search_1003"));

    }

    /**
     * Checking Top Menu Navigation
     * @group navigation
     * @group internationalBasic
     */
    public function testFrontendTopMenuInternational()
    {
        $this->openShop();
        $this->assertFalse($this->isElementPresent("root2"));
        $this->assertTrue($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        //activating top menu navigation
        $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME` = 'iTopNaviCatCount';");
        $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME` = 'blTopNaviLayout';");
            $this->executeSql("INSERT INTO `oxconfig` VALUES ('05ac5e8c1ed0309d1e1e9fe346', 'oxbaseshop', '', 'iTopNaviCatCount', 'str', 0xb6);");
            $this->executeSql("INSERT INTO `oxconfig` VALUES ('05acidyc1e85609d1e1e9qw346', 'oxbaseshop', '', 'blTopNaviLayout', 'bool', 0x93ea1218);");
        $this->openShop();
        $this->assertTrue($this->isElementPresent("root2"));
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_2"));
        $this->assertEquals("more", $this->getText("root3"));
        $this->assertEquals("Test category 0 [EN] šųößлы", $this->getText("root2"));
        $this->clickAndWait("root2");
        $this->assertEquals("Test category 0 [EN] šųößлы", $this->getText("test_BoxLeft_Cat_testcategory0_1"));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->getText("test_BoxLeft_Cat_testcategory0_sub1"));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->getText("test_Top_root2_SubCat_1"));
        $this->assertEquals("Test category 0 [EN] šųößлы", $this->getText("test_catTitle"));
        $this->clickAndWait("test_Top_root2_SubCat_1"); //new templates
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->getText("test_catTitle"));
        $this->clickAndWait("root3");
        $this->assertEquals("Test category 0 [EN] šųößлы", $this->getText("test_CatRoot_2"));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->getText("test_CatRoot_2_SubCat_1"));
    }


}