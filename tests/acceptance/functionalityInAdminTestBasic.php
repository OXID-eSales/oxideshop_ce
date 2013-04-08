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

class Acceptance_functionalityInAdminTestBasic extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Admin interface functionality ----------------------------------

    /**
     * not registered user makes order. later someone else registers with same email.
     * already creted order is edited (added some products). #1696
     * @group admin
     * @group order
     * @group basic
     */
    public function testEditingNotRegisteredUserOrder()
    {
        $this->openShop();
        //not registered user creates the order
        $this->clickAndWait("test_toBasket_WeekSpecial_1001");
        $this->clickAndWait("test_RightBasketOpen");
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UsrOpt1");
        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "name");
        $this->type("invadr[oxuser__oxlname]", "surname");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "3000");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invCountrySelect", "label=Germany");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Brandenburg");
        $this->uncheck("test_newsReg");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->click("test_Payment_oxidcashondel");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->check("test_OrderConfirmAGBTop");
        $this->clickAndWait("test_OrderSubmitTop");

        //someone creates acc with same info and email
        $this->openShop();
        $this->clickAndWait("test_RightLogin_Register");
        $this->type("test_lgn_usr", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "name");
        $this->type("invadr[oxuser__oxlname]", "surname");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "3000");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invCountrySelect", "label=Germany");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Brandenburg");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->clickAndWait("//input[@value='Send']");
        $this->assertTrue($this->isTextPresent("We welcome you as registered user!"));

        //editing previously created order.
        $this->loginAdmin("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit", "link=Addresses");
        $this->openTab("link=Addresses");
        $this->assertEquals("name", $this->getValue("editval[oxorder__oxbillfname]"));
        $this->assertEquals("surname", $this->getValue("editval[oxorder__oxbilllname]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxorder__oxbillemail]"));
        $this->assertEquals("street", $this->getValue("editval[oxorder__oxbillstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxbillstreetnr]"));
        $this->assertEquals("3000", $this->getValue("editval[oxorder__oxbillzip]"));
        $this->assertEquals("city", $this->getValue("editval[oxorder__oxbillcity]"));
        $this->assertEquals("BB", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxorder__oxbillcountryid]"));

        $this->frame("list");
        $this->openTab("link=Products");
        $this->type("sSearchArtNum", "1001");
        $this->clickAndWait("//input[@name='search']");
        $this->clickAndWait("add");

        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->assertEquals("name", $this->getValue("editval[oxorder__oxbillfname]"));
        $this->assertEquals("surname", $this->getValue("editval[oxorder__oxbilllname]"));
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxorder__oxbillemail]"));
        $this->assertEquals("street", $this->getValue("editval[oxorder__oxbillstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxbillstreetnr]"));
        $this->assertEquals("3000", $this->getValue("editval[oxorder__oxbillzip]"));
        $this->assertEquals("city", $this->getValue("editval[oxorder__oxbillcity]"));
        $this->assertEquals("BB", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxorder__oxbillcountryid]"));
    }

    /**
     * Master Settings -> Languages
     * @group admin
     * @group basic
     */
    public function testNewLanguageCreatingAndNavigation()
    {
        //EN lang
        $this->loginAdmin("Master Settings", "Languages");
        $this->frame("edit");
        $this->clickAndWaitFrame("btn.new", "edit");
        $this->check("editval[active]");
        $this->type("editval[abbr]", "lt");
        $this->type("editval[desc]", "Lietuviu");
        $this->type("editval[sort]", "4");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectMenu("Service", "Tools");
        $this->frame("edit");
        $this->clickAndConfirm("//input[@value='Update DB Views now']", null, "list");
        $this->selectWindow(null);
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//img[@alt='Lietuviu']"));
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("1 Hits for \"1001\"", $this->getText("test_smallHeader"));
        $this->clickAndWait("test_details_Search_1001");
        $this->assertEquals("Art.No.: 1001", $this->getText("test_product_artnum"));
        $this->clickAndWait("test_HeaderHome");
        //LT lang
        $this->clickAndWait("//img[@alt='Lietuviu']");
        $this->assertTrue($this->isElementPresent("path"));
        $this->assertEquals("You are here [LT]: / Home All prices incl. VAT, plus Shipping", $this->clearString($this->getText("path")));
        $this->type("f.search.param", "1001");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("1 Hits for [LT] \"1001\"", $this->getText("test_smallHeader"));
        $this->clickAndWait("test_details_Search_1001");
        $this->assertEquals("Art.No. [LT]: 1001", $this->getText("test_product_artnum"));
    }

    /**
     * Administer Products -> Products (variants should inherit parents selection lists)
     * @group admin
     * @group basic
     */
    public function testVariantsInheritsSelectionLists()
    {
        //assigning selection list to parent product
        $this->loginAdmin("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->type("where[oxarticles][oxartnum]", "1002");
        $this->clickAndWait("submitit");
        $this->openTab("link=1002");
        $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->Frame("list");
        $this->openTab("link=Selection");
        $this->click("//input[@value='Assign Selection Lists']");
        $this->usePopUp();
        $this->type("_0", "*test");
        $this->keyUp("_0", "t");
        $this->waitForAjax("test selection list [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("test selection list [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->close();
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->frame("list");
        $this->openTab("link=Main");
        //checking if selection list is assigned to variant also
        $this->selectAndWaitFrame( "art_variants", "label=- var1 [DE]", "list");

        $this->assertEquals("1002-1", $this->getValue("editval[oxarticles__oxartnum]"));
        $this->Frame("list");
        $this->openTab("link=Selection");
        $this->click("//input[@value='Assign Selection Lists']");
        $this->usePopUp();
        $this->assertEquals("test selection list [DE] šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->close();
        //checking if in frontend it is displayed correctly
        $this->openShop();
        $this->type("f.search.param", "1002");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_title_Search_1002");
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("test_select_1002_0")));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectList_Variant_1002-1_0")));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectList_Variant_1002-2_0")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->clickAndWait("test_title_Variant_1002-1");
        $this->assertEquals("Test product 2 [EN] šÄßüл var1 [EN] šÄßüл", $this->getText("test_product_name"));
        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("test_select_1002-1_0")));
        $this->select("test_select_1002-1_0", "label=selvar2 [EN] šÄßüл");
        $this->clickAndWait("test_toBasket");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->getText("test_basketTitle_1002-1_1"));
        $this->assertEquals("selvar2 [EN] šÄßüл", $this->getSelectedLabel("test_basketSelect_1002-1_1_0"));
    }

    /**
     * Core settings -> Settings -> Active Category at Start
     * @group admin
     * @group basic
     */
    public function testActiveCategoryAtStart()
    {
        $this->openShop();
        $this->assertFalse($this->isElementPresent("test_BoxLeft_Cat_testcategory0_sub1"));
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Shop frontend");
        sleep(1);
        $this->assertTrue($this->isElementPresent("//input[@value='---']"));
        $this->click("//input[@value='---']");
        $this->usePopUp();
        $this->assertEquals("", $this->getText("defcat_title"));
        $this->type("_0", "test");
        $this->keyUp("_0", "t");
        $this->waitForAjax("Test category 0 [EN] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->click("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->click("saveBtn");
        $this->waitForItemAppear("_defcat");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("defcat_title"));
        $this->click("remBtn");
        $this->waitForItemDisappear("_defcat");
        $this->assertEquals("", $this->getText("defcat_title"));
        $this->click("saveBtn");
        $this->waitForItemAppear("_defcat");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("defcat_title"));
        $this->close();
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->frame("list");
        $this->openTab("link=Settings");
        $this->click("link=Shop frontend");
        sleep(1);
        $this->assertTrue($this->isElementPresent("//input[@value='Test category 1 [EN] šÄßüл']"));
        //checking in frontend
        $this->openShop();
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("test_BoxLeft_Cat_testcategory0_sub1"));
    }

    /**
     * CMS page in top menu (frontend)
     * @group admin
     * @group create
     * @group basic
     */
    public function testCMSpageAsTopMenu()
    {
        $this->loginAdmin("Customer Info", "CMS Pages");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->frame("edit");
        $this->check("editval[oxcontents__oxactive]");
        $this->type("editval[oxcontents__oxtitle]", "new page");
        $this->type("editval[oxcontents__oxloadid]", "new_page");
        $this->check("oxtype1");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->clickAndWaitFrame("save", "list");
        $this->frame("list");
        $this->openTab("link=SEO");
        $this->assertEquals("en/new-page/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("new-page/", $this->getValue("aSeoData[oxseourl]"), "#1255 not fully fixed. there should be no -oxid at the seo link end.");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        $this->clickAndWait("link=Home");
        $this->assertEquals("new page", $this->getText("link=new page"));
        $sShopId = "";
        $this->assertEquals(shopURL."en/new-page/".$sShopId, $this->getAttribute("//a[text()='new page']@href"));
        $this->clickAndWait("link=new page");
        $this->assertEquals("new page", $this->getText("test_contentHeader"));
        $this->assertEquals("You are here: / new page", $this->getText("path"));
        $this->assertEquals(shopURL."en/new-page/".$sShopId, $this->getLocation());
        $this->clickAndWait("test_Lang_Deutsch");
        $this->assertEquals(shopURL."new-page/".$sShopId, $this->getAttribute("//a[text()='new page']@href"));
        $this->assertEquals(shopURL."new-page/".$sShopId, $this->getLocation());
        $this->assertEquals("Sie sind hier: / new page", $this->getText("path"), "Bug from mantis #1176");
        $this->clickAndWait("test_TopAccMyAccount");
        $this->clickAndWait("test_link_account_logout");
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

    /**
     * Core settings options saving. checking if saving options does not break the shop
     * @group admin
     * @group basic
     */
    public function testCoreSettingsSave()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->clickAndWaitFrame("save", "list");
        $this->frame("list");
        $this->openTab("link=System");
        $this->clickAndWaitFrame("save", "list");
        $this->frame("list");
        $this->openTab("link=Perform.");
        $this->clickAndWaitFrame("save", "list");
        $this->frame("list");
        $this->openTab("link=SEO");
        $this->clickAndWaitFrame("save", "list");
        $this->openShop();
        $this->assertEquals("You are here: / Home All prices incl. VAT, plus Shipping", $this->getText("path"));
        $this->assertTrue($this->isTextPresent("Notice for the shop administrator:"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("test_title_WeekSpecial_1001"));
        $this->assertEquals("100,00 €*", $this->getText("test_price_WeekSpecial_1001"));
    }

    /**
     * Allowing negative stock values
     * @group navigation
     * @group order
     * @group basic
     */
    public function testFrontendNegativeStockValuesOn()
    {
        //allow negative stock values
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218 WHERE `OXVARNAME` = 'blAllowNegativeStock';");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //creating 2 orders
        for ($i=0; $i<2; $i++) {
            $this->type("//input[@id='f.search.param']", "1003");
            $this->clickAndWait("test_searchGo");
            $this->type("test_am_Search_1003", "4");
            $this->clickAndWait("test_toBasket_Search_1003");
            $this->clickAndWait("link=Cart");
            $this->clickAndWait("test_BasketNextStepTop");
            $this->clickAndWait("test_UserNextStepTop");
            $this->click("test_Payment_oxidcashondel");
            $this->clickAndWait("test_PaymentNextStepBottom");
            $this->check("test_OrderConfirmAGBTop");
            $this->clickAndWait("test_OrderSubmitTop");
        }
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("-3", $this->getValue("editval[oxarticles__oxstock]"));
        //adding product amount to order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("link=Products");
        $this->type("//tr[@id='art.1']/td[1]/input", "2");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("2", $this->getValue("//tr[@id='art.1']/td[1]/input"));
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit", "link=Stock");
        $this->openTab("link=Stock", "editval[oxarticles__oxstock]");
        $this->assertEquals("-1", $this->getValue("editval[oxarticles__oxstock]"));
        //deleting order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("del.1");
        $this->selectMenu("Administer Products", "Products", "btn.help", "where[oxarticles][oxartnum]");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("3", $this->getValue("editval[oxarticles__oxstock]"));
        //canceling order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("pau.1");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("5", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * Disabled negative stock values
     * @group navigation
     * @group order
     * @group basic
     */
    public function testFrontendNegativeStockValuesOff()
    {
        //disabling negative stock values
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blAllowNegativeStock';");
        $this->openShop();
        $this->type("test_RightLogin_Email", "birute_test@nfq.lt");
        $this->type("test_RightLogin_Pwd", "useruser");
        $this->clickAndWait("test_RightLogin_Login");
        //creating 2 orders
        for ($i=0; $i<2; $i++) {
            $this->type("//input[@id='f.search.param']", "1003");
            $this->clickAndWait("test_searchGo");
            $this->type("test_am_Search_1003", "4");
            $this->clickAndWait("test_toBasket_Search_1003");
            $this->clickAndWait("link=Cart");
            $this->clickAndWait("test_BasketNextStepTop");
            $this->clickAndWait("test_UserNextStepTop");
            $this->click("test_Payment_oxidcashondel");
            $this->clickAndWait("test_PaymentNextStepBottom");
            $this->check("test_OrderConfirmAGBTop");
            $this->clickAndWait("test_OrderSubmitTop");
        }
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxstock]"));
        //adding product amount to order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("link=Products");
        $this->type("//tr[@id='art.1']/td[1]/input", "2");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("2", $this->getValue("//tr[@id='art.1']/td[1]/input"));
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("2", $this->getValue("editval[oxarticles__oxstock]"));
        //deleting order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("del.1");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("6", $this->getValue("editval[oxarticles__oxstock]"));
        //canceling order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("pau.1");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("8", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * checking if econda is loaded in frontend
     * @group admin
     * @group basic
     */
    public function testEconda()
    {
        //activating econda
        $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0xce92 WHERE `OXVARNAME` = 'sShopCountry'");
        $this->clearTmp();
        $this->loginAdmin("Shop controlling", "econda", "confbools[blEcondaActive]");
        $this->frame("edit");
        $this->click("//input[@name='confbools[blEcondaActive]' and @type='checkbox']");
        $this->clickAndWait("save");
        //checking in frontend
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//script[@src='".shopURL."modules/econda/out/emos2.js']"));
        $this->open(shopURL."modules/econda/out/emos2.js");
        $this->assertTrue($this->isTextPresent("function(){var URL_TRACKING_ALLOWED=true"));
        $this->goBack();
        //home page checking
        $htmlSource = $this->getHtmlSource();
        $this->assertContains("window.emosPropertiesEvent(emospro);", $htmlSource);
        $this->assertContains('emospro.content = "Start";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        //category page
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains("window.emosPropertiesEvent(emospro);", $htmlSource);
        $this->assertContains('emospro.content', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        //details page
        $this->clickAndWait("test_title_action_1000");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains("window.emosPropertiesEvent(emospro);", $htmlSource);
        $this->assertContains('emospro.content', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->assertContains('emospro.ec_Event = [["view","1000","Test', $htmlSource);
        $this->clickAndWait("test_toBasket");
        //acount page
        $this->clickAndWait("test_link_footer_account");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Login\/Formular\/Login";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->type("test_LoginEmail", "birute_test@nfq.lt");
        $this->type("test_LoginPwd", "useruser");
        $this->clickAndWait("test_Login");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Login\/Uebersicht";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->assertContains('emospro.login = [["'.md5('birute_test@nfq.lt').'"', $htmlSource);
        //basket page
        $this->clickAndWait("test_link_footer_basket");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Shop\/Kaufprozess\/Warenkorb";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->assertContains('emospro.orderProcess = "1_Warenkorb";', $htmlSource);
        //information page
        $this->clickAndWait("link=Privacy Policy");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Info\/Sicherheit";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
    }

}
