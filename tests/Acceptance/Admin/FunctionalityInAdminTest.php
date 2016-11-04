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

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use oxDb;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** Admin interface functionality. */
class FunctionalityInAdminTest extends AdminTestCase
{
    /**
     * Testing downloadable product in admin ant frontend
     *
     * @group adminFunctionality
     */
    public function testDownloadableFiles()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for subshops');
        }
        // Enable downloadable files
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("Settings");
        $this->click("link=Downloadable products");
        $this->check("//input[@name='confbools[blEnableDownloads]' and @value='true']");
        $this->clearString("confstrs[iMaxDownloadsCount]");
        $this->type("confstrs[iMaxDownloadsCount]", "2");
        $this->clearString("confstrs[iLinkExpirationTime]");
        $this->type("confstrs[iLinkExpirationTime]", "240");
        $this->clearString("confstrs[iDownloadExpirationTime]");
        $this->type("confstrs[iDownloadExpirationTime]", "24");
        $this->clearString("confstrs[iMaxDownloadsCountUnregistered]");
        $this->type("confstrs[iMaxDownloadsCountUnregistered]", "2");
        $this->clickAndWait("save");

        // Select product with downloadable file
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1002");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1002", "edit");
        $this->openTab("Downloads");
        $this->check("//input[@name='editval[oxarticles__oxisdownloadable]' and @value='1']");
        $this->clickAndWait("save");

        // Make purchase complete
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->addToBasket("1002-1", 10);

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='oxdownloadableproductsagreement' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));

        //Check if file appears in My Downloads
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[8]/a");
        $this->assertTextPresent("%DOWNLOADS_PAYMENT_PENDING%");

        //Make order complete
        $this->loginAdmin("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Downloads");
        $this->assertEquals("1002-1", $this->getText("//div[2]/table/tbody/tr[2]/td[1]"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//div[2]/table/tbody/tr[2]/td[2]"));
        $this->assertEquals("testFile3", $this->getText("//div[2]/table/tbody/tr[2]/td[3]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//div[2]/table/tbody/tr[2]/td[4]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//div[2]/table/tbody/tr[2]/td[5]"));
        $this->assertEquals("0", $this->getText("//div[2]/table/tbody/tr[2]/td[6]"));
        $this->assertEquals("20", $this->getText("//div[2]/table/tbody/tr[2]/td[7]"));
        $this->assertEquals("0", $this->getText("//div[2]/table/tbody/tr[2]/td[9]"));
        $this->openTab("Main");
        $this->click("link=Current Date");
        $this->clickAndWait("saveFormButton");

        //Check if file appears in My Downloads
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[8]/a");
        $this->assertTextNotPresent("%DOWNLOADS_PAYMENT_PENDING%");

        $oTestFileList = $this->getElement('link=testFile3');
        $oTestFileList->click();
        $oTestFileList->click();
        $oTestFileList->click();

        $this->loginAdmin("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Downloads");
        $this->assertEquals("1002-1", $this->getText("//div[2]/table/tbody/tr[2]/td[1]"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//div[2]/table/tbody/tr[2]/td[2]"));
        $this->assertEquals("testFile3", $this->getText("//div[2]/table/tbody/tr[2]/td[3]"));
        $this->assertEquals("20", $this->getText("//div[2]/table/tbody/tr[2]/td[7]"));
        $this->assertEquals("0", $this->getText("//div[2]/table/tbody/tr[2]/td[9]"));
    }

    /**
     * CMS page in ident place (frontend)
     *
     * @group adminFunctionality
     */
    public function testCMSpageChangeIdent()
    {
        // Information about CMS page we use in this test.
        $sCMSPageName = "standard footer";
        $sCMSPageLink = "link=" . $sCMSPageName;
        $sCMSPageDemoIdent = "oxstdfooter";
        $sCMSPageNewIdent = "_test_oxstdfooter";

        /// Check if data corectly prepeared.
        // Check if data corectly prepeared in admin.
        $this->loginAdmin("Customer Info", "CMS Pages");
        $this->type("where[oxcontents][oxtitle]", "");
        $this->type("where[oxcontents][oxloadid]", $sCMSPageDemoIdent);
        $this->clickAndWaitFrame("submitit");
        $this->assertElementPresent($sCMSPageLink, "There should be CMS page with title '" . $sCMSPageName . "' prepeared with demo data. Trying to find it with link: '" . $sCMSPageLink . "'.");
        $this->openListItem($sCMSPageLink);
        $this->assertEquals("on", $this->getValue("editval[oxcontents__oxactive]"), "CMS page with title '" . $sCMSPageName . "' should be turned on as active with demo data.");
        $this->assertEquals($sCMSPageDemoIdent, $this->getValue("editval[oxcontents__oxloadid]"), "CMS page with title '" . $sCMSPageName . "' should have such ident so it will be visible in frontend footer.");

        // Get CMS page content from textarea.
        $sCMSPageContent = $this->getEditorValue("oxcontents__oxcontent");

        // Check if data corectly prepeared in frontend - CMS page content is visible.
        $this->clearCache();
        $this->openShop();
        // Strip HTML elements as we look only for text.
        $this->assertTextPresent(strip_tags($sCMSPageContent), "CMS page with title '" . $sCMSPageName . "' should be visible in frontend footer. Trying to find it with text: '" . strip_tags($sCMSPageContent) . "' This should be prepeared with demo data.");

        /// Turning off CMS page by changing ident. Check if not visible in frontend.
        $this->loginAdmin("Customer Info", "CMS Pages");
        $this->type("where[oxcontents][oxtitle]", "");
        $this->type("where[oxcontents][oxloadid]", $sCMSPageDemoIdent);
        $this->clickAndWaitFrame("submitit");
        $this->openListItem($sCMSPageLink);
        $this->assertEquals($sCMSPageDemoIdent, $this->getValue("editval[oxcontents__oxloadid]"), "CMS page with title '" . $sCMSPageName . "' should have such ident so it will be visible in frontend footer.");
        $this->type("editval[oxcontents__oxloadid]", $sCMSPageNewIdent);
        $this->assertEquals($sCMSPageNewIdent, $this->getValue("editval[oxcontents__oxloadid]"), "CMS page with title '" . $sCMSPageName . "' should have new ident as we just chane it in this selenium test.");
        $this->clickAndWait("//input[@value='Save']");

        $this->clearCache();
        $this->openShop();
        // Strip HTML elements as we look only for text.
        $this->assertTextNotPresent(strip_tags($sCMSPageContent), "CMS page with title '" . $sCMSPageName . "' should not be visible in frontend footer. Trying to find it with text: '" . strip_tags($sCMSPageContent) . "' This is because we change ident to not existing one in this selenium test.");

        /// Turning on CMS page by changing ident. Check if visible in frontend.
        $this->loginAdmin("Customer Info", "CMS Pages");
        $this->type("where[oxcontents][oxtitle]", $sCMSPageName);
        $this->type("where[oxcontents][oxloadid]", "");
        $this->clickAndWaitFrame("submitit");
        $this->openListItem($sCMSPageLink);
        $this->assertEquals($sCMSPageNewIdent, $this->getValue("editval[oxcontents__oxloadid]"), "CMS page with title '" . $sCMSPageName . "' should have new ident as we previously chane it in this selenium test.");
        $this->type("editval[oxcontents__oxloadid]", $sCMSPageDemoIdent);
        $this->assertEquals($sCMSPageDemoIdent, $this->getValue("editval[oxcontents__oxloadid]"), "CMS page with title '" . $sCMSPageName . "' should have demo ident as we just chane it in this selenium test.");
        $this->clickAndWait("//input[@value='Save']");

        $this->clearCache();
        $this->openShop();
        // Strip HTML elements as we look only for text.
        $this->assertTextPresent(strip_tags($sCMSPageContent), "CMS page with title '" . $sCMSPageName . "' should be visible in frontend footer. Trying to find it with text: '" . strip_tags($sCMSPageContent) . "' This is because we change ident to demo one in this selenium test.");
    }

    /**
     * checking if order info is displayed correctly
     *
     * @group adminFunctionality
     */
    public function testDisplayingOrdersInfo()
    {
        $this->updateSubshopOrders();

        $this->loginAdmin("Administer Orders", "Orders");
        $this->openListItem("link=1");
        $this->assertEquals("Billing Address: Mr 1useršÄßüл 1UserSurnamešÄßüл 1 Street 1 HE 333000 2 City Germany E-mail: example02@oxid-esales.dev", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("Shipping Address: Mr shippingUseršÄßüл shippingSurnamešÄßüл Street 1 NI 1 City Germany", $this->clearString($this->getText("//td[2]")));
        $this->selectAndWait("setfolder", "label=Finished");
        $this->assertTextPresent("Internal Status: OK");
        $this->assertEquals("100,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("95,24", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("97,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertElementPresent("//table[@id='order.info']/tbody/tr[4]", "line with discount info is not displayed");
        $this->assertElementPresent("//table[@id='order.info']/tbody/tr[4]/td[1]", "line with discount info is not displayed");
        $this->assertElementPresent("//table[@id='order.info']/tbody/tr[4]/td[2]", "line with discount info is not displayed");
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("//table[4]/tbody/tr[1]/td[2]"));
        $this->assertEquals("Standard", $this->getText("//table[4]/tbody/tr[2]/td[2]"));
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=Finished");
        $this->assertElementPresent("link=1");
        $this->openTab("Main");
        $this->assertTextPresent("IP Address");
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxordernr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxbillnr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxtrackcode]"));
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("97,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
    }

    /**
     * checking if order info is displayed correctly
     *
     * @group adminFunctionality
     */
    public function testEditingOrdersMain()
    {
        $this->updateSubshopOrders();

        $this->callShopSC("oxOrder", "save", 'testorder7', array('OXFOLDER' => 'ORDERFOLDER_FINISHED'));
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("Main");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertTextPresent("192.168.1.999");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"), "Manually edited delivery costs is not saved, if other del.cost was applied during order process");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->openTab("Main");
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $this->click("link=Current Date");
        $this->assertNotEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));

        $sDate = date("Y-m-d") . " 23:59:59";
        $this->type("editval[oxorder__oxpaid]", $sDate);
        $this->type("editval[oxorder__oxordernr]", "125");
        $this->type("editval[oxorder__oxbillnr]", "123");
        $this->type("editval[oxorder__oxtrackcode]", "456");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->frame("list");
        $this->frame("edit");
        $this->assertTextPresent("192.168.1.999");
        $this->assertTextPresent("Order was paid " . $sDate);
        $this->assertTextPresent("Tracking code");
        $this->assertEquals("125", $this->getValue("editval[oxorder__oxordernr]"));
        $this->assertEquals("123", $this->getValue("editval[oxorder__oxbillnr]"));
        $this->assertEquals("456", $this->getValue("editval[oxorder__oxtrackcode]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->assertEquals($sDate, $this->getValue("editval[oxorder__oxpaid]"));
        $this->frame("list");
        $this->assertElementPresent("link=125", "List frame is not refreshed after order Nr. was changed");
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->openTab("Main");
        $this->clickAndWaitFrame("//input[@name='save' and @value='  Ship Now  ']", "list");
        $this->assertTextPresent("Shipped on " . date("Y-m-d"));
        $this->clickAndWaitFrame("//input[@name='save' and @value='Reset Shipping Date']", "list");
        $this->assertTextPresent("Shipped on -");
        $this->assertTextPresent("192.168.1.999");
    }

    /**
     * checking if order info is displayed correctly
     *
     * @group adminFunctionality
     */
    public function testEditingOrdersAddresses()
    {
        $this->updateSubshopOrders();

        $this->executeSql("UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'");
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished", "link=1");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("Addresses");
        //billing address
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxorder__oxbillsal]"));
        $this->assertEquals("1useršÄßüл", $this->getValue("editval[oxorder__oxbillfname]"));
        $this->assertEquals("1UserSurnamešÄßüл", $this->getValue("editval[oxorder__oxbilllname]"));
        $this->assertEquals("example02@oxid-esales.dev", $this->getValue("editval[oxorder__oxbillemail]"));
        $this->assertEquals("1 Street", $this->getValue("editval[oxorder__oxbillstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxbillstreetnr]"));
        $this->assertEquals("333000", $this->getValue("editval[oxorder__oxbillzip]"));
        $this->assertEquals("2 City", $this->getValue("editval[oxorder__oxbillcity]"));
        $this->assertEquals("HE", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("del_country_select"));
        //shipping address
        $this->assertEquals("444444", $this->getValue("editval[oxorder__oxbillfon]"));
        $this->assertEquals("MR", $this->getValue("editval[oxorder__oxdelsal]"));
        $this->assertEquals("shippingUseršÄßüл", $this->getValue("editval[oxorder__oxdelfname]"));
        $this->assertEquals("shippingSurnamešÄßüл", $this->getValue("editval[oxorder__oxdellname]"));
        $this->assertEquals("Street", $this->getValue("editval[oxorder__oxdelstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxdelstreetnr]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxdelzip]"));
        $this->assertEquals("City", $this->getValue("editval[oxorder__oxdelcity]"));
        $this->assertEquals("NI", $this->getValue("editval[oxorder__oxdelstateid]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxorder__oxdelcountryid]"));
        //editing addresses
        $this->select("editval[oxorder__oxbillsal]", "label=Mrs");
        $this->type("editval[oxorder__oxbillfname]", "UserName");
        $this->type("editval[oxorder__oxbilllname]", "UserSurname");
        $this->type("editval[oxorder__oxbillemail]", "example_test@oxid-esales.dev");
        $this->type("editval[oxorder__oxbillcompany]", "UserCompany");
        $this->type("editval[oxorder__oxbillstreet]", "Musterstr");
        $this->type("editval[oxorder__oxbillstreetnr]", "10");
        $this->type("editval[oxorder__oxbillzip]", "790980");
        $this->type("editval[oxorder__oxbillcity]", "Musterstadt");
        $this->type("editval[oxorder__oxbillustid]", "123");
        $this->type("editval[oxorder__oxbilladdinfo]", "User additional info");
        $this->type("editval[oxorder__oxbillfon]", "0800 1111110");
        $this->type("editval[oxorder__oxbillfax]", "0800 1111120");
        $this->type("editval[oxorder__oxbillstateid]", "NI");
        $this->clickAndWaitFrame("save", 'list');
        $this->assertEquals($this->getSelectedLabel("editval[oxorder__oxbillsal]"), "Mrs");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillfname]"), "UserName");
        $this->assertEquals($this->getValue("editval[oxorder__oxbilllname]"), "UserSurname");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillemail]"), "example_test@oxid-esales.dev");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillcompany]"), "UserCompany");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillstreet]"), "Musterstr");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillstreetnr]"), "10");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillzip]"), "790980");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillcity]"), "Musterstadt");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillustid]"), "123");
        $this->assertEquals($this->getValue("editval[oxorder__oxbilladdinfo]"), "User additional info");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillfon]"), "0800 1111110");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillfax]"), "0800 1111120");
        $this->assertEquals("NI", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->openTab("Main");
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("97,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->openTab("Addresses");
        $this->assertEquals("NI", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->select("editval[oxorder__oxbillcountryid]", "label=Ireland");
        $this->type("editval[oxorder__oxbillstateid]", "");
        $this->clickAndWaitFrame("save", 'list');
        $this->openTab("Main");
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("//table[4]/tbody/tr[1]/td[2]/b"));
        $this->assertEquals("Standard", $this->getText("//table[4]/tbody/tr[2]/td[2]/b"));
        $this->openTab("Addresses");
        $this->assertEquals($this->getSelectedLabel("editval[oxorder__oxbillcountryid]"), "Ireland");
        $this->select("editval[oxorder__oxdelsal]", "label=Mrs");
        $this->type("editval[oxorder__oxdelfname]", "name");
        $this->type("editval[oxorder__oxdellname]", "surname");
        $this->type("editval[oxorder__oxdelcompany]", "company");
        $this->type("editval[oxorder__oxdelstreet]", "street");
        $this->type("editval[oxorder__oxdelstreetnr]", "1");
        $this->type("editval[oxorder__oxdelzip]", "zip");
        $this->type("editval[oxorder__oxdelcity]", "city");
        $this->type("editval[oxorder__oxdeladdinfo]", "add info");
        $this->select("editval[oxorder__oxdelcountryid]", "label=Germany");
        $this->type("editval[oxorder__oxdelstateid]", "HE");
        $this->type("editval[oxorder__oxdelfon]", "123");
        $this->type("editval[oxorder__oxdelfax]", "456");
        $this->clickAndWait("save");
        $this->assertEquals($this->getSelectedLabel("editval[oxorder__oxdelsal]"), "Mrs");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelfname]"), "name");
        $this->assertEquals($this->getValue("editval[oxorder__oxdellname]"), "surname");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelcompany]"), "company");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelstreet]"), "street");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelstreetnr]"), "1");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelzip]"), "zip");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelcity]"), "city");
        $this->assertEquals($this->getValue("editval[oxorder__oxdeladdinfo]"), "add info");
        $this->assertEquals($this->getSelectedLabel("editval[oxorder__oxdelcountryid]"), "Germany");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelfon]"), "123");
        $this->assertEquals($this->getValue("editval[oxorder__oxdelfax]"), "456");
        $this->assertEquals("HE", $this->getValue("editval[oxorder__oxdelstateid]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("97,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("Billing Address: Company UserCompany User additional info Mrs UserName UserSurname Musterstr 10 790980 Musterstadt Ireland VAT ID: 123 E-mail: example_test@oxid-esales.dev", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("Shipping Address: Company company add info Mrs name surname street 1 HE zip city Germany", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[2]")));
        $this->openTab("Main");
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
    }

    /**
     * checking if order info is displayed correctly
     *
     * @group adminFunctionality
     */
    public function testEditingOrdersDelivery()
    {
        $this->updateSubshopOrders();

        $this->executeSql("UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'");
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("Main");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->select("setDelSet", "label=Standard");
        $this->clickAndWait("saveFormButton");
        $this->waitForElement("setPayment");
        $this->select("setPayment", "label=COD (Cash on Delivery)");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("100,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("85,71", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("4,29", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertTextPresent("COD (Cash on Delivery)");
        //skonto was removed from demodata
        $this->assertTextNotPresent("Cash in advance - 2% cash discount");
        $this->assertTextPresent("Standard");
        $this->openTab("Main");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->type("editval[oxorder__oxdelcost]", "9.9");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertEquals("9.9", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("9,90", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("107,40", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
    }

    /**
     * checking if order info is displayed correctly
     *
     * @group adminFunctionality
     */
    public function testEditingOrdersProducts()
    {
        $this->markTestSkipped(" fix test after bug 0004624 fix , so we need to move test to integration test with correct information");

        $this->updateSubshopOrders();

        $this->executeSql("UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'");
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("Main");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->select("setDelSet", "label=Example Set1: UPS 48 hours");
        $this->clickAndWait("saveFormButton");
        $this->assertEquals("----", $this->getSelectedLabel("setPayment"));
        $this->assertEquals("Example Set1: UPS 48 hours", $this->getSelectedLabel("setDelSet"));
        $this->select("setDelSet", "label=Standard");
        $this->clickAndWait("saveFormButton");
        $this->select("setPayment", "label=COD (Cash on Delivery)");
        $this->clickAndWait("saveFormButton");
        $this->openTab("Products");
        $this->type("sSearchArtNum", "1001");
        $this->clickAndWait("//input[@name='search']");
        $this->assertEquals("Test product 1 [EN] šÄßüл 100,00 EUR", $this->getText("aid"));
        $this->assertEquals("selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->getText("test_select__0"));
        $this->type("sSearchArtNum", "1002");
        $this->clickAndWait("//input[@name='search']");
        $this->assertEquals("Test product 2 [EN] šÄßüл 55,00 EUR Test product 2 [EN] šÄßüл var1 [EN] šÄßüл 55,00 EUR Test product 2 [EN] šÄßüл var2 [EN] šÄßüл 67,00 EUR", $this->clearString($this->getText("aid")));
        $this->assertElementNotPresent("test_select__0");
        $this->type("sSearchArtNum", "100");
        $this->clickAndWait("//input[@name='search']");
        $this->assertElementNotPresent("aid");
        $this->assertElementNotPresent("test_select__0");
        $this->assertTextPresent("Sorry, no items found.");
        $this->type("sSearchArtNum", "1003");
        $this->clickAndWait("//input[@name='search']");
        $this->assertEquals("Test product 3 [EN] šÄßüл 75,00 EUR", $this->clearString($this->getText("aid")));
        $this->type("am", "2");
        $this->clickAndWait("add");
        $this->assertEquals("225,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("199,16", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("4,29", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("21,55", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("232,50", $this->getText("//table[@id='order.info']/tbody/tr[8]/td[2]"));

        $this->assertEquals("VAT (5%)", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[1]"));
        $this->assertEquals("VAT (19%)", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[1]"));
        $this->openTab("Main");
        $this->openTab("Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertTextPresent("Order not shipped yet.");
        $this->check("sendmail");
        $this->clickAndWait("save");
        $this->assertTextPresent("Shipped on " . date("Y-m-d"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->clickAndWait("//input[@name='save' and @value='Reset Shipping Date']");
        $this->assertTextPresent("Order not shipped yet.");
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->openTab("Main");
        $this->openTab("History");
    }

    /**
     * not registered user makes order. later someone else registers with same email.
     * already creted order is edited (added some products). #1696
     *
     * @group adminFunctionality
     */
    public function testEditingNotRegisteredUserOrder()
    {
        //not registered user creates the order
        $this->addToBasket("1001");

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $this->type("userLoginName", "example01@oxid-esales.dev");
        $this->type("invadr[oxuser__oxfname]", "name");
        $this->type("invadr[oxuser__oxlname]", "surname");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "3000");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invCountrySelect", "label=Germany");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Brandenburg");
        $this->type("orderRemark", "remark text");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertTextPresent("%WHAT_I_WANTED_TO_SAY% remark text");
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        //someone creates acc with same info and email
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=%PAGE_TITLE_REGISTER%");
        $this->type("userLoginName", "example01@oxid-esales.dev");
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
        $this->clickAndWait("//button[text()='%SAVE%']");
        $this->assertTextPresent("%PAGE_TITLE_REGISTER%");

        //editing previously created order.
        $this->loginAdmin("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Addresses");

        $this->assertEquals("name", $this->getValue("editval[oxorder__oxbillfname]"));
        $this->assertEquals("surname", $this->getValue("editval[oxorder__oxbilllname]"));
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("editval[oxorder__oxbillemail]"));
        $this->assertEquals("street", $this->getValue("editval[oxorder__oxbillstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxbillstreetnr]"));
        $this->assertEquals("3000", $this->getValue("editval[oxorder__oxbillzip]"));
        $this->assertEquals("city", $this->getValue("editval[oxorder__oxbillcity]"));
        $this->assertEquals("BB", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("bill_country_select"));

        $this->openTab("Products");
        $this->type("sSearchArtNum", "1001");
        $this->clickAndWait("//input[@name='search']");
        $this->clickAndWait("add");

        $this->openTab("Addresses");
        $this->assertEquals("name", $this->getValue("editval[oxorder__oxbillfname]"));
        $this->assertEquals("surname", $this->getValue("editval[oxorder__oxbilllname]"));
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("editval[oxorder__oxbillemail]"));
        $this->assertEquals("street", $this->getValue("editval[oxorder__oxbillstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxbillstreetnr]"));
        $this->assertEquals("3000", $this->getValue("editval[oxorder__oxbillzip]"));
        $this->assertEquals("city", $this->getValue("editval[oxorder__oxbillcity]"));
        $this->assertEquals("BB", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("bill_country_select"));
    }

    /**
     * System settings: Currency values
     *
     * @group adminFunctionality
     */
    public function testCurrencyValues()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("Settings");
        $this->click("link=Other settings");
        $this->assertEquals("EUR@ 1.00@ ,@ .@ €@ 2\nGBP@ 0.8565@ .@  @ £@ 2\nCHF@ 1.4326@ ,@ .@ <small>CHF</small>@ 2\nUSD@ 1.2994@ .@  @ $@ 2", $this->getValue("confarrs[aCurrencies]"));
        $this->clickAndWait("save");
        $this->click("link=Other settings");
        $this->assertEquals("EUR@ 1.00@ ,@ .@ €@ 2\nGBP@ 0.8565@ .@  @ £@ 2\nCHF@ 1.4326@ ,@ .@ <small>CHF</small>@ 2\nUSD@ 1.2994@ .@  @ $@ 2", $this->getValue("confarrs[aCurrencies]"));
    }

    /**
     * System settings: Save automatically when changing Tabs
     *
     * @group adminFunctionality
     */
    public function testAutosaveOnChangingTabs()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("System");
        $this->assertElementNotPresent("//input[@name='confbools[blAutoSave]']", "Option 'Save automatically when changing Tabs' should be removed. Confirmed by Ralf");
    }

    /**
     * Service -> System info
     *
     * @group adminFunctionality
     */
    public function testSystemInfo()
    {
        $this->loginAdmin("Service", "System Info");
        $this->assertTextPresent("Configuration", "System information is not loaded: Service -> System Info");
        $this->assertTextPresent("PHP Version", "System information is not loaded: Service -> System Info");
    }

    /**
     * Service -> System Requirements
     *
     * @group adminFunctionality
     */
    public function testSystemRequirements()
    {
        $this->loginAdmin("Service", "System health");
        $this->frame("edit");
        $this->waitForText("State of system health");
    }

    /**
     * Service -> Tools
     *
     * @group adminFunctionality
     */
    public function testTools()
    {
        $this->loginAdmin("Service", "Tools", "btn.help");
        $this->frame("edit");
        $this->assertTextPresent("Update SQL ", "Tools page is not loaded: Service -> Tools");
        $this->assertElementPresent("updatesql");
    }

    /**
     * Service -> Generic Import
     *
     * @group adminFunctionality
     */
    public function testGenericImport()
    {
        $this->loginAdmin("Service", "Generic Import");
        $this->assertTextPresent("Uploading CSV file");
        $this->assertElementPresent("save");
    }

    /**
     * Service -> Product Export
     *
     * @group adminFunctionality
     */
    public function testProductExport()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for subshops');
        }

        $this->loginAdmin("Administer Products", "Products");
        //testing export
        $this->frame("navigation");
        $this->click("link=Service");
        $this->click("link=Product Export");
        $this->waitForFrameToLoad("dynexport_main", 5000, true);
        $this->frame("dynexport_do");
        $this->assertTextPresent("Export not yet started.");
        $this->frame("dynexport_main");
        $this->addSelection("acat[]", "label=Test category 0 [EN] šÄßüл");
        $this->assertElementPresent("search");
        $this->clickAndWaitFrame("save", "dynexport_do");
        $this->frame("dynexport_do");
        $this->waitForElement("link=here");
        $this->click("link=here");
        $aWindows = $this->getAllWindowNames();
        $this->selectWindow(end($aWindows));
        $this->waitForPageToLoad(5000, true);
        $this->assertTextPresent(shopURL . "en/Test-category-0-EN-Aessue/Test-product-0-EN-Aessue.html");
        $this->close();

        $this->selectWindow(null);
        $this->selectMenu("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Extended");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[5]/td[1]", "container2");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->close();
        $this->selectWindow(null);
        $this->frame("navigation");
        $this->checkForErrors();
        $this->click("link=Service");
        $this->click("link=Product Export");
        $this->waitForFrameToLoad("basefrm");
        $this->frame("relative=top");
        $this->frame("basefrm");
        $this->frame("dynexport_main");
        $this->checkForErrors();
        $this->click("save");
        $this->frame("dynexport_do");
        $this->waitForElement("link=here");
        $this->click("link=here");
        $aWindows = $this->getAllWindowNames();
        $this->selectWindow(end($aWindows));
        $this->waitForPageToLoad(5000, true);
        $this->assertTextPresent(shopURL . "5-DE-category-Aessue/DE-4-Test-product-0-Aessue.html");
        $this->close();

        $this->selectWindow(null);
        //assigning other main category
        $this->selectMenu("Administer Products", "Products");
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Extended");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->click("makeact-button");
        $this->close();
        $this->selectWindow(null);
        $this->windowMaximize();
        $this->frame("navigation");
        $this->checkForErrors();
        $this->click("link=Service");
        $this->click("link=Product Export");
        $this->waitForFrameToLoad("basefrm");
        $this->frame("relative=top");
        $this->frame("basefrm");
        $this->frame("dynexport_main");
        $this->checkForErrors();
        $this->click("save");
        $this->frame("dynexport_do");
        $this->waitForElement("link=here");
        $this->click("link=here");
        $aWindows = $this->getAllWindowNames();
        $this->selectWindow(end($aWindows));
        $this->waitForPageToLoad(5000, true);
        $this->assertTextPresent(shopURL . "1-DE-category-Aessue/DE-4-Test-product-0-Aessue.html");
    }

    /**
     * Testing if admin interface is loaded correctly in both EN and DE lang
     *
     * @group adminFunctionality
     */
    public function testLoginToAdminInOtherLang()
    {
        $this->loginAdmin();
        $this->waitForText("Welcome to the OXID eShop Admin");
        $this->frame("navigation");
        $this->checkForErrors();
        $this->assertElementPresent("link=Master Settings");
        $this->assertElementPresent("link=Shop Settings");
        $this->assertElementPresent("link=Administer Products");
        $this->click("link=Administer Products");
        $this->waitForItemAppear("link=Products");
        $this->clickAndWaitFrame("link=Products", "edit");
        $this->frame("list");
        $this->waitForElement("row.1");
        $this->checkForErrors();
        $this->frame("edit");
        $this->waitForElement("btn.new");
        $this->checkForErrors();
        $this->logoutAdmin("link=Logout");

        $this->loginAdmin(null, null, false, "admin@myoxideshop.com", "admin0303", "Deutsch");
        $this->waitForText("Willkommen im OXID eShop Administrationsbereich");
        $this->checkForErrors();
        $this->frame("navigation");
        $this->checkForErrors();
        $this->assertElementPresent("link=Stammdaten");
        $this->assertElementPresent("link=Shopeinstellungen");
        $this->logoutAdmin("link=Abmelden");
        $this->assertElementPresent("usr");
    }

    /**
     * Master Settings -> Languages
     *
     * @group adminFunctionality
     */
    public function testNewLanguageCreatingAndNavigation()
    {
        //EN lang
        $this->loginAdmin("Master Settings", "Languages");
        $this->clickCreateNewItem();
        $this->check("editval[active]");
        $this->type("editval[abbr]", "lt");
        $this->type("editval[desc]", "Lietuviu");
        $this->type("editval[sort]", "3");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectMenu("Service", "Tools");
        $this->frame("edit");
        $this->clickAndConfirm("//input[@value='Update DB Views now']", "list");
        $this->selectWindow(null);
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//a/span[text()='Lietuviu']");
        $this->searchFor("1001");
        $this->assertEquals("1 %HITS_FOR% \"1001\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("%PRODUCT_NO%: 1001", $this->getText("productArtnum"));
        $this->clickAndWait("link=%HOME%");
        //LT lang
        $this->switchLanguage("Lietuviu");
        $this->getTranslator()->setLanguage(2);
        $this->searchFor("1001");
        $this->assertEquals("1 Hits for [LT] \"1001\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Item #: 1001", $this->getText("productArtnum"));
    }

    /**
     * Administer Products -> Categories (price categories testing)
     *
     * @group adminFunctionality
     */
    public function testPriceCategoryCreating()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 [DE] category šÄßüл");
        $this->assertTrue($this->isEditable("//input[@value='Assign Products']"));
        $this->assertEquals("0", $this->getValue("editval[oxcategories__oxpricefrom]"));
        $this->assertEquals("0", $this->getValue("editval[oxcategories__oxpriceto]"));
        $this->type("editval[oxcategories__oxpricefrom]", "5");
        $this->type("editval[oxcategories__oxpriceto]", "100");
        $this->assertElementNotEditable("//input[@value='Assign Products']");
        $this->clickAndWaitFrame("save", "list");
        $this->assertElementNotEditable("//input[@value='Assign Products']");
        $this->type("editval[oxcategories__oxpricefrom]", "");
        $this->type("editval[oxcategories__oxpriceto]", "");
        $this->assertElementEditable("//input[@value='Assign Products']");
        $this->clickAndWaitFrame("save", "list");
        $this->assertElementEditable("//input[@value='Assign Products']");
    }

    /**
     * Administer Products -> Products (variants should inherit parents selection lists)
     *
     * @group main
     */
    public function testVariantsInheritsSelectionLists()
    {
        //assigning selection list to parent product
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage("Deutsch");
        $this->openListItem("1002", '[oxarticles][oxartnum]');
        $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->openTab("Selection");
        $this->click("//input[@value='Assign Selection Lists']");
        $this->usePopUp();
        $this->type("_0", "*test");
        $this->keyUp("_0", "t");
        $this->assertElementText("test selection list [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("test selection list [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->close();
        $this->selectWindow(null);
        $this->windowMaximize();
        $this->openTab("Main");
        //checking if selection list is assigned to variant also
        $this->selectAndWaitFrame("art_variants", "label=- var1 [DE]", "list");
        $this->assertEquals("1002-1", $this->getValue("editval[oxarticles__oxartnum]"));
        $this->openTab("Selection");
        $this->click("//input[@value='Assign Selection Lists']");
        $this->usePopUp();
        $this->assertEquals("test selection list [DE] šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->close();
        //checking if in frontend it is displayed correctly
        $this->selectWindow(null);
        $this->clearCache();
        $this->openShop();
        $this->searchFor("1002");
        $this->clickAndWait("searchList_1");

        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("//div[@id='productSelections']//ul")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->selectVariant("variants", "1", "var1 [EN] šÄßüл", "var1 [EN] šÄßüл");
        $this->selectVariant("productSelections", "1", "selvar3 [EN] šÄßüл -2,00 €", "var1 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->selectVariant("productSelections", "1", "selvar2 [EN] šÄßüл", "var1 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]/div")));
        $this->assertElementPresent("cartItemSelections_1");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//p"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_2']/td[3]/div")));
        $this->assertElementPresent("cartItemSelections_2");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar2 [EN] šÄßüл", $this->getText("//div[@id='cartItemSelections_2']//p"));
    }

    /**
     * Core settings -> Settings -> Active Category at Start
     *
     * @group adminFunctionality
     */
    public function testActiveCategoryAtStart()
    {
        $this->markTestSkipped("waiting for desition from management, if this option should be in azure theme at all");

        $this->openShop();
        $this->assertElementNotPresent("test_BoxLeft_Cat_testcategory0_sub1");
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("Settings");
        $this->click("link=Shop frontend");
        $this->assertElementPresent("//input[@value='---']");
        $this->click("//input[@value='---']");
        $this->usePopUp();
        $this->assertEquals("", $this->getText("defcat_title"));
        $this->type("_0", "test");
        $this->keyUp("_0", "t");
        $this->assertElementText("Test category 0 [EN] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
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
        $this->windowMaximize();
        $this->frame("relative=top");
        $this->openTab("Settings");
        $this->click("link=Shop frontend");
        $this->assertElementPresent("//input[@value='Test category 1 [EN] šÄßüл']");
        //checking in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//ul[@id='tree']/li/ul/li/a");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("//ul[@id='tree']/li/ul/li/a"));
    }

    /**
     * checking how help popups are working
     *
     * @group adminFunctionality
     */
    public function testHelpPopupsInAdmin()
    {
        //testing help popup for shop active checkbox
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->frame("edit");
        $this->checkForErrors();
        $this->assertEquals("", $this->clearString($this->getText("helpPanel")));
        $this->assertElementNotPresent("link=Close");
        $this->assertElementPresent("helpBtn_HELP_SHOP_MAIN_PRODUCTIVE");
        $this->click("helpBtn_HELP_SHOP_MAIN_PRODUCTIVE");
        $this->waitForItemAppear("helpPanel");
        $this->assertTrue($this->isVisible("helpPanel"));
        $this->assertEquals("Non-productive eShop mode is intended", substr($this->clearString($this->getText("helpPanel")), 0, 37));
        $this->click("link=Close");
        $this->waitForItemDisappear("helpPanel");
        $this->checkForErrors();
    }

    /**
     * check main shop details in shop edit page
     *
     * @group adminFunctionality
     */
    public function testEditShopName()
    {
        $shopVersionNumber = $this->getShopVersionNumber();

        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('Skip CE/PE related tests for EE edition');
        }

        $this->loginAdmin("Master Settings", "Core Settings");
        $this->frame("edit");

        //asserting default shop values in EN lang
        $this->assertEquals("OXID eShop $shopVersionNumber", $this->getValue("editval[oxshops__oxname]"));
    }

    /**
     * check main shop details edit
     *
     * @group adminFunctionality
     */
    public function testEditShopSave()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->frame("edit");

        //asserting default shop values in EN lang
        $this->assertEquals($this->getSelectedLabel("subjlang"), "English");
        $this->assertEquals("Your Company Name", $this->getValue("editval[oxshops__oxcompany]"));
        $this->assertEquals("John", $this->getValue("editval[oxshops__oxfname]"));
        $this->assertEquals("Doe", $this->getValue("editval[oxshops__oxlname]"));
        $this->assertEquals("off", $this->getValue("editval[oxshops__oxproductive]"));
        $this->assertEquals("on", $this->getValue("editval[oxshops__oxactive]"));
        $this->assertEquals("Your Company Name", $this->getValue("editval[oxshops__oxcompany]"));
        $this->assertEquals("John", $this->getValue("editval[oxshops__oxfname]"));
        $this->assertEquals("Doe", $this->getValue("editval[oxshops__oxlname]"));
        $this->assertEquals("9041", $this->getValue("editval[oxshops__oxzip]"));
        $this->assertEquals("Any City, CA", $this->getValue("editval[oxshops__oxcity]"));
        $this->assertEquals("United States", $this->getValue("editval[oxshops__oxcountry]"));
        $this->assertEquals("217-8918712", $this->getValue("editval[oxshops__oxtelefon]"));
        $this->assertEquals("217-8918713", $this->getValue("editval[oxshops__oxtelefax]"));
        $this->assertEquals("www.myoxideshop.com", $this->getValue("editval[oxshops__oxurl]"));
        $this->assertEquals("Bank of America", $this->getValue("editval[oxshops__oxbankname]"));
        $this->assertEquals("900 1234567", $this->getValue("editval[oxshops__oxbankcode]"));
        $this->assertEquals("1234567890", $this->getValue("editval[oxshops__oxbanknumber]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxvatnumber]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxbiccode]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxibannumber]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxhrbnr]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxcourt]"));
        $this->assertEquals("localhost", $this->getValue("editval[oxshops__oxsmtp]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxsmtpuser]"));
        $this->assertEquals("", $this->getValue("oxsmtppwd"));
        $this->assertEquals("example_test@oxid-esales.dev", $this->getValue("editval[oxshops__oxinfoemail]"));
        $this->assertEquals("example_test@oxid-esales.dev", $this->getValue("editval[oxshops__oxorderemail]"));
        $this->assertEquals("example_test@oxid-esales.dev", $this->getValue("editval[oxshops__oxowneremail]"));

        //editing shop values in EN lang
        $this->uncheck("editval[oxshops__oxproductive]");
        $this->uncheck("editval[oxshops__oxactive]");
        $this->type("editval[oxshops__oxcompany]", "Ihr Firmenname1_šÄßüл");
        $this->type("editval[oxshops__oxfname]", "Hans1_šÄßüл");
        $this->type("editval[oxshops__oxlname]", "Mustermann1_šÄßüл");
        $this->type("editval[oxshops__oxstreet]", "Musterstr. 101_šÄßüл");
        $this->type("editval[oxshops__oxzip]", "790981_šÄßüл");
        $this->type("editval[oxshops__oxcity]", "Musterstadt1_šÄßüл");
        $this->type("editval[oxshops__oxcountry]", "Deutschland1_šÄßüл");
        $this->type("editval[oxshops__oxtelefon]", "0800 12345671_šÄßüл");
        $this->type("editval[oxshops__oxtelefax]", "0800 12345671_šÄßüл");
        $this->type("editval[oxshops__oxurl]", "www.meineshopurl1.com_šÄßüл");
        $this->type("editval[oxshops__oxbankname]", "Volksbank Musterstadt1_šÄßüл");
        $this->type("editval[oxshops__oxbankcode]", "900 12345671_šÄßüл");
        $this->type("editval[oxshops__oxbanknumber]", "12345678901_šÄßüл");
        $this->type("editval[oxshops__oxvatnumber]", "111_šÄßüл");
        $this->type("editval[oxshops__oxbiccode]", "1111_šÄßüл");
        $this->type("editval[oxshops__oxibannumber]", "11111_šÄßüл");
        $this->type("editval[oxshops__oxhrbnr]", "111111_šÄßüл");
        $this->type("editval[oxshops__oxcourt]", "1111111_šÄßüл");
        $this->type("editval[oxshops__oxname]", "OXID eShop šÄßüл");
        $this->type("editval[oxshops__oxsmtpuser]", "user_šÄßüл");
        $this->type("oxsmtppwd", "pass");
        $this->type("editval[oxshops__oxinfoemail]", "");
        $this->type("editval[oxshops__oxorderemail]", "");
        $this->type("editval[oxshops__oxowneremail]", "");
        $this->clickAndWaitFrame("save", 'list');

        //changing lang to DE.
        $this->changeAdminEditLanguage('Deutsch');
        $this->assertEquals($this->getSelectedLabel("subjlang"), "Deutsch");
        $this->assertEquals("Ihre Bestellung bei OXID eSales", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Ihr Passwort im OXID eShop", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Ihre OXID eSales Bestellung wurde versandt", $this->getValue("editval[oxshops__oxsendednowsubject]"));
        $this->type("editval[oxshops__oxordersubject]", "Ihre Bestellung bei OXID eSales1_šÄßüл");
        $this->type("editval[oxshops__oxregistersubject]", "Vielen Dank fur Ihre Registrierung im OXID eShop1_šÄßüл");
        $this->type("editval[oxshops__oxforgotpwdsubject]", "Ihr Passwort im OXID eShop1_šÄßüл");
        $this->type("editval[oxshops__oxsendednowsubject]", "Ihre OXID eSales Bestellung wurde versandt1_šÄßüл");
        $this->clickAndWaitFrame("save", 'list');

        //checking if main info was not corruped in DE lang
        $this->assertEquals("off", $this->getValue("editval[oxshops__oxproductive]"));
        $this->assertEquals("off", $this->getValue("editval[oxshops__oxactive]"));
        $this->assertEquals("Ihr Firmenname1_šÄßüл", $this->getValue("editval[oxshops__oxcompany]"));
        $this->assertEquals("Hans1_šÄßüл", $this->getValue("editval[oxshops__oxfname]"));
        $this->assertEquals("Mustermann1_šÄßüл", $this->getValue("editval[oxshops__oxlname]"));
        $this->assertEquals("Musterstr. 101_šÄßüл", $this->getValue("editval[oxshops__oxstreet]"));
        $this->assertEquals("790981_šÄßüл", $this->getValue("editval[oxshops__oxzip]"));
        $this->assertEquals("Musterstadt1_šÄßüл", $this->getValue("editval[oxshops__oxcity]"));
        $this->assertEquals("Deutschland1_šÄßüл", $this->getValue("editval[oxshops__oxcountry]"));
        $this->assertEquals("0800 12345671_šÄßüл", $this->getValue("editval[oxshops__oxtelefon]"));
        $this->assertEquals("0800 12345671_šÄßüл", $this->getValue("editval[oxshops__oxtelefax]"));
        $this->assertEquals("www.meineshopurl1.com_šÄßüл", $this->getValue("editval[oxshops__oxurl]"));
        $this->assertEquals("Volksbank Musterstadt1_šÄßüл", $this->getValue("editval[oxshops__oxbankname]"));
        $this->assertEquals("900 12345671_šÄßüл", $this->getValue("editval[oxshops__oxbankcode]"));
        $this->assertEquals("12345678901_šÄßüл", $this->getValue("editval[oxshops__oxbanknumber]"));
        $this->assertEquals("111_šÄßüл", $this->getValue("editval[oxshops__oxvatnumber]"));
        $this->assertEquals("1111_šÄßüл", $this->getValue("editval[oxshops__oxbiccode]"));
        $this->assertEquals("11111_šÄßüл", $this->getValue("editval[oxshops__oxibannumber]"));
        $this->assertEquals("111111_šÄßüл", $this->getValue("editval[oxshops__oxhrbnr]"));
        $this->assertEquals("1111111_šÄßüл", $this->getValue("editval[oxshops__oxcourt]"));
        $this->assertEquals("OXID eShop šÄßüл", $this->getValue("editval[oxshops__oxname]"));
        $this->assertEquals("localhost", $this->getValue("editval[oxshops__oxsmtp]"));
        $this->assertEquals("user_šÄßüл", $this->getValue("editval[oxshops__oxsmtpuser]"));
        $this->assertEquals("", $this->getValue("oxsmtppwd"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxinfoemail]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxorderemail]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxowneremail]"));
        $this->assertEquals("Ihre Bestellung bei OXID eSales1_šÄßüл", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Vielen Dank fur Ihre Registrierung im OXID eShop1_šÄßüл", $this->getValue("editval[oxshops__oxregistersubject]"));
        $this->assertEquals("Ihr Passwort im OXID eShop1_šÄßüл", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Ihre OXID eSales Bestellung wurde versandt1_šÄßüл", $this->getValue("editval[oxshops__oxsendednowsubject]"));
        $this->assertEquals($this->getSelectedLabel("subjlang"), "Deutsch");

        $this->changeAdminEditLanguage('English');
        $this->assertEquals($this->getSelectedLabel("subjlang"), "English");
        $this->assertEquals("Your order at OXID eShop", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->type("editval[oxshops__oxordersubject]", "Your order from OXID eShop1");
        $this->type("editval[oxshops__oxregistersubject]", "Thank you for your registration in OXID eShop1");
        $this->type("editval[oxshops__oxforgotpwdsubject]", "Your OXID eShop password1");
        $this->type("editval[oxshops__oxsendednowsubject]", "Your OXID eSales Order has been shipped1");
        $this->type("oxsmtppwd", "-");
        $this->clickAndWaitFrame("save", 'list');

        $this->assertEquals("Your order from OXID eShop1", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Thank you for your registration in OXID eShop1", $this->getValue("editval[oxshops__oxregistersubject]"));
        $this->assertEquals("Your OXID eShop password1", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Your OXID eSales Order has been shipped1", $this->getValue("editval[oxshops__oxsendednowsubject]"));

        $this->changeAdminEditLanguage('Deutsch');
        $this->assertEquals("Ihre Bestellung bei OXID eSales1_šÄßüл", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Vielen Dank fur Ihre Registrierung im OXID eShop1_šÄßüл", $this->getValue("editval[oxshops__oxregistersubject]"));
        $this->assertEquals("Ihr Passwort im OXID eShop1_šÄßüл", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Ihre OXID eSales Bestellung wurde versandt1_šÄßüл", $this->getValue("editval[oxshops__oxsendednowsubject]"));
        $this->assertEquals("", $this->getValue("oxsmtppwd"));

        $testConfig = $this->getTestConfig();
        $edition = $testConfig->getShopEdition();
        //testing if other tabs are working after those changes
        $this->openTab("Settings");
        $this->openTab("System");
        if ($edition === 'EE') {
            $this->openTab("Caching");
        }
        $this->openTab("SEO");
        if ($edition !== 'CE') {
            $this->openTab("License");
        }
        $this->openTab("Perform.");
    }

    /**
     * My account navigation: Order history
     * Product amounts after order and while editing order in admin
     * Also testing min order price
     *
     * @group adminFunctionality
     */
    public function testOrdersEditingAmount()
    {
        $testConfig = $this->getTestConfig();
        $sShopId = $testConfig->getShopId();

        $sql = "INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLEMAIL`,
                                      `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`,
                                      `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`,
                                      `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXDELCOST`,
                                      `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXDISCOUNT`, `OXCURRENCY`,
                                      `OXCURRATE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`)
                              VALUES ('order1', '" . $sShopId . "', 'testuser', '2010-04-19 16:52:56', 12, 'example_test@oxid-esales.dev',
                                      'UserName', 'UserSurname', 'Musterstr.', '1', 'Musterstadt',
                                      'a7c40f631fc920687.20179984', 'HE', '79098', 'MR', 'payment1', 'oxidcashondel',
                                      85.71, 100, 97.5, 5, 4.29, 0,
                                      0, 7.5, 0, 0, 0, 10, 'EUR',
                                      1, 'ORDERFOLDER_NEW', 'OK', 1, 'oxidstandard')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`,
                                               `OXSHORTDESC`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`,
                                               `OXBPRICE`, `OXNPRICE`, `OXWEIGHT`, `OXSTOCK`,  `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`,
                                               `OXWIDTH`, `OXHEIGHT`, `OXSUBCLASS`, `OXORDERSHOPID`)
                                       VALUES ('product1', 'order1', 2, '1000', '1000', 'Test product 0 [EN]',
                                               'Test product 0 short desc [EN]',  95.2380952381, 100, 4.7619047619, 5, '', 50,
                                               50, 47.619047619, 24, 15, '2008-02-04', '2008-02-04 17:07:29', 1,
                                               2, 2, 'oxarticle',  '" . $sShopId . "')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`)
                                      VALUES ('payment1', 'testuser', 'oxidcashondel', '');";
        $this->executeSql($sql);

        //checking if product stock quantity was changed after order
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("15", $this->getValue("editval[oxarticles__oxstock]"));

        //deleting order articles to check if product amount is restored
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Products");
        $this->assertElementPresent("//tr[@id='art.1']");
        $this->assertElementNotPresent("//tr[@id='art.2']");
        $this->clickAndConfirm("//tr[@id='art.1']/td/a[@class='delete']");
        //$this->clickAndConfirm("//tr[@id='art.1']/td[11]/a[@class='delete']");
        $this->assertElementNotPresent("//tr[@id='art.1']");
        $this->assertElementNotPresent("//tr[@id='art.2']");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));

        //adding order articles to check if amount is updated
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Products");
        $this->type("sSearchArtNum", "1000");
        $this->clickAndWait("//input[@name='search']");
        $this->type("am", "3.4");
        $this->clickAndWait("add");
        //adding once more same product. it will be displayed in second line
        $this->type("sSearchArtNum", "1000");
        $this->clickAndWait("//input[@name='search']");
        $this->type("am", "1.2");
        $this->clickAndWait("add");
        //those 2 lines not allways are displayed in same order. this is quick workaround
        if ("1.2" == $this->getValue("//tr[@id='art.1']/td[1]/input")) {
            $this->assertEquals("1.2", $this->getValue("//tr[@id='art.1']/td[1]/input"));
            $this->assertEquals("3.4", $this->getValue("//tr[@id='art.2']/td[1]/input"));
        } else {
            $this->assertEquals("3.4", $this->getValue("//tr[@id='art.1']/td[1]/input"));
            $this->assertEquals("1.2", $this->getValue("//tr[@id='art.2']/td[1]/input"));
        }

        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("12.4", $this->getValue("editval[oxarticles__oxstock]"));

        //canceling both order articles to check if amount is updated
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Products");
        //$this->clickAndConfirm("//tr[@id='art.2']/td[12]/a");
        //$this->clickAndConfirm("//tr[@id='art.1']/td[12]/a");
        $this->clickAndConfirm("//tr[@id='art.2']/td/a[@class='pause']");
        $this->clickAndConfirm("//tr[@id='art.1']/td/a[@class='pause']");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));
    }


    /**
     * checking if product amounts are restored after order is canceled
     *
     * @group adminFunctionality
     */
    public function testDeletingOrderCheckingProductsAmount()
    {
        $testConfig = $this->getTestConfig();
        $sShopId = $testConfig->getShopId();

        $sql = "INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLEMAIL`,
                                      `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`,
                                      `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`,
                                      `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXDELCOST`,
                                      `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXDISCOUNT`, `OXCURRENCY`,
                                      `OXCURRATE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`)
                              VALUES ('order1', '" . $sShopId . "', 'testuser', '2010-04-19 16:52:56', 12, 'example_test@oxid-esales.dev',
                                      'UserName', 'UserSurname', 'Musterstr.', '1', 'Musterstadt',
                                      'a7c40f631fc920687.20179984', 'HE', '79098', 'MR', 'payment1', 'oxidcashondel',
                                      85.71, 100, 97.5, 5, 4.29, 0,
                                      0, 7.5, 0, 0, 0, 10, 'EUR',
                                      1, 'ORDERFOLDER_NEW', 'OK', 1, 'oxidstandard')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`,
                                               `OXSHORTDESC`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`,
                                               `OXBPRICE`, `OXNPRICE`, `OXWEIGHT`, `OXSTOCK`,  `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`,
                                               `OXWIDTH`, `OXHEIGHT`, `OXSUBCLASS`, `OXORDERSHOPID`)
                                       VALUES ('product1', 'order1', 2, '1000', '1000', 'Test product 0 [EN]',
                                               'Test product 0 short desc [EN]',  95.2380952381, 100, 4.7619047619, 5, '', 50,
                                               50, 47.619047619, 24, 15, '2008-02-04', '2008-02-04 17:07:29', 1,
                                               2, 2, 'oxarticle',  '" . $sShopId . "')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`)
                                      VALUES ('payment1', 'testuser', 'oxidcashondel', '');";
        $this->executeSql($sql);

        //checking product count
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("15", $this->getValue("editval[oxarticles__oxstock]"));

        //deleting order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickDeleteListItem(1);

        //checking if product count was restored
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * checking if product amounts are restored after order is canceled
     *
     * @group adminFunctionality
     */
    public function testCancelingOrderCheckingProductsAmount()
    {
        $testConfig = $this->getTestConfig();
        $sShopId = $testConfig->getShopId();

        $sql = "INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLEMAIL`,
                                      `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`,
                                      `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`,
                                      `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXDELCOST`,
                                      `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXDISCOUNT`, `OXCURRENCY`,
                                      `OXCURRATE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`)
                              VALUES ('order1', '" . $sShopId . "', 'testuser', '2010-04-19 16:52:56', 12, 'example_test@oxid-esales.dev',
                                      'UserName', 'UserSurname', 'Musterstr.', '1', 'Musterstadt',
                                      'a7c40f631fc920687.20179984', 'HE', '79098', 'MR', 'payment1', 'oxidcashondel',
                                      85.71, 100, 97.5, 5, 4.29, 0,
                                      0, 7.5, 0, 0, 0, 10, 'EUR',
                                      1, 'ORDERFOLDER_NEW', 'OK', 1, 'oxidstandard')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`,
                                               `OXSHORTDESC`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`,
                                               `OXBPRICE`, `OXNPRICE`, `OXWEIGHT`, `OXSTOCK`,  `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`,
                                               `OXWIDTH`, `OXHEIGHT`, `OXSUBCLASS`, `OXORDERSHOPID`)
                                       VALUES ('product1', 'order1', 2, '1000', '1000', 'Test product 0 [EN]',
                                               'Test product 0 short desc [EN]',  95.2380952381, 100, 4.7619047619, 5, '', 50,
                                               50, 47.619047619, 24, 15, '2008-02-04', '2008-02-04 17:07:29', 1,
                                               2, 2, 'oxarticle',  '" . $sShopId . "')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`)
                                      VALUES ('payment1', 'testuser', 'oxidcashondel', '');";
        $this->executeSql($sql);

        //checking product amount
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("15", $this->getValue("editval[oxarticles__oxstock]"));

        //canceling order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("pau.1");

        //checking product amount
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * Allowing negative stock values
     *
     * @group adminFunctionality
     */
    public function testFrontendNegativeStockValuesOn()
    {
        //allow negative stock values
        $this->callShopSC("oxConfig", null, null, array("blAllowNegativeStock" => array("type" => "bool", "value" => 'true')));
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //creating 2 orders
        for ($i = 0; $i < 2; $i++) {
            $this->searchFor("1003");
            $this->selectDropDown("viewOptions", "%line%");
            $this->type("//ul[@id='searchList']/li[1]//input[@id='amountToBasket_searchList_1']", "4");
            $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
            $this->openBasket();
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
            $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        }
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("-3", $this->getValue("editval[oxarticles__oxstock]"));
        //adding product amount to order

        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Products");
        $this->type("//tr[@id='art.1']/td[1]/input", "2");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("2", $this->getValue("//tr[@id='art.1']/td[1]/input"));
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("-1", $this->getValue("editval[oxarticles__oxstock]"));
        //deleting order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickDeleteListItem(1);
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("3", $this->getValue("editval[oxarticles__oxstock]"));
        //canceling order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("pau.1");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("5", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * Disabled negative stock values
     *
     * @group adminFunctionality
     */
    public function testFrontendNegativeStockValuesOff()
    {
        //disabling negative stock values
        $this->callShopSC("oxConfig", null, null, array("blAllowNegativeStock" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //creating 2 orders
        for ($i = 0; $i < 2; $i++) {
            $this->searchFor("1003");
            $this->selectDropDown("viewOptions", "%line%");
            $this->type("//ul[@id='searchList']/li[1]//input[@id='amountToBasket_searchList_1']", "4");
            $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
            $this->openBasket();
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
            $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        }
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxstock]"));
        //adding product amount to order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("Products");
        $this->type("//tr[@id='art.1']/td[1]/input", "2");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("2", $this->getValue("//tr[@id='art.1']/td[1]/input"));
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("2", $this->getValue("editval[oxarticles__oxstock]"));
        //deleting order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickDeleteListItem(1);
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("6", $this->getValue("editval[oxarticles__oxstock]"));
        //canceling order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("pau.1");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1003");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("Stock");
        $this->assertEquals("8", $this->getValue("editval[oxarticles__oxstock]"));
    }


    /**
     * checking does work in frontend multidimensional variants which have stock prices
     *
     * @group adminFunctionality
     */
    public function testMultidimensionalVariantsWhichHaveStockPrices()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "3570");
        $this->clickAndWait("//input[@name='submitit']");
        $this->clickAndWaitFrame("link=3570", "edit");
        $this->openTab("Variants");
        $this->waitForElement("test_variant.4");
        $this->clickAndWaitFrame("//tr[@id='test_variant.4']/td[1]/a", 'list');
        $this->openTab("Stock");
        $this->waitForElement("test_editlanguage");
        $this->type("editval[oxprice2article__oxamount]", "1");
        $this->type("editval[oxprice2article__oxamountto]", "5");
        $this->type("editval[price]", "10");
        $this->clickAndWait("/descendant::input[@name='save'][2]");
        $this->clearCache();
        $this->openShop();
        $this->searchFor("3570");
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));
        $this->selectVariant("variants", 1, "W 30/L 30", "W 30/L 30");
        $this->selectVariant("variants", 2, "Blue", "W 30/L 30, Blue");
        $this->assertEquals("10,00 € *", $this->getText("//label[@id='productPrice']"));
        $this->selectVariant("variants", 2, "Smoke Gray", "W 30/L 30, Smoke Gray");
        $this->assertEquals("99,90 € *", $this->getText("//label[@id='productPrice']"));
    }


    /**
     * checking if Display attribute's value for products in checkout is on
     *
     * @group adminFunctionality
     */
    public function testDisplayAttributesValueForProductInCheckout()
    {
        $this->loginAdmin("Administer Products", "Attributes");
        $this->openListItem("Color", '[oxattribute][oxtitle]');
        $this->check("//input[@name='editval[oxattribute__oxdisplayinbasket]' and @value='1']");
        $this->clickAndWait("//input[@value='Save']");
        $this->selectMenu("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->openListItem("1402", '[oxarticles][oxartnum]');
        $this->openTab("Selection");

        //creating attribute's value for products
        $this->click("//input[@value='Assign Attributes']");
        $this->usePopUp();
        $this->type("_0", "Color");
        $this->keyUp("_0", "t");
        $this->assertElementText("Color", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("Color", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]/div");
        $this->type("//input[@id='attr_value']", "Black");
        $this->click("//input[@value='Save']");
        $this->close();
        $this->selectWindow(null);
        $this->windowMaximize();

        //open frontend and adding products to the basket
        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //adding article 1402 to basket
        $this->addToBasket("05848170643ab0deb9914566391c0c63", 2);

        //Display attribute's value for products in checkout
        $this->assertEquals("Harness MADTRIXX", $this->getText("//tr[@id='cartItem_1']/td/div[1]"));
        $this->assertEquals("%PRODUCT_NO%: 1402", $this->getText("//tr[@id='cartItem_1']/td/div[2]"));
        $this->assertEquals("Black", $this->getText("//tr[@id='cartItem_1']/td/div[3]"));
        $this->assertTextPresent("Black");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("Harness MADTRIXX", $this->getText("//tr[@id='cartItem_1']/td/div[1]"));
        $this->assertEquals("%PRODUCT_NO%: 1402", $this->getText("//tr[@id='cartItem_1']/td/div[2]"));
        $this->assertEquals("Black", $this->getText("//tr[@id='cartItem_1']/td/div[3]"));
        $this->assertTextPresent("Black");
        $this->clickAndWait("//button[text()='%SUBMIT_ORDER%']");
        $this->loginAdmin("Administer Products", "Attributes");
        $this->openListItem("Color", '[oxattribute][oxtitle]');
        $this->uncheck("//input[@name='editval[oxattribute__oxdisplayinbasket]' and @value='1']");
        $this->clickAndWait("//input[@value='Save']");

        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //adding article 1402 to basket
        $this->addToBasket("05848170643ab0deb9914566391c0c63");

        //Checking if does not display attribute's value for products in checkout
        $this->assertEquals("Harness MADTRIXX", $this->getText("//tr[@id='cartItem_1']/td/div[1]"));
        $this->assertEquals("%PRODUCT_NO%: 1402", $this->getText("//tr[@id='cartItem_1']/td/div[2]"));
        $this->assertTextNotPresent("Black");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("Harness MADTRIXX", $this->getText("//tr[@id='cartItem_1']/td/div[1]"));
        $this->assertEquals("%PRODUCT_NO%: 1402", $this->getText("//tr[@id='cartItem_1']/td/div[2]"));
        $this->assertTextNotPresent("Black");
        $this->clickAndWait("//button[@type='submit']");
    }

    /**
     * Testing modules in vendor directory. Checking when any file with source code class of module is deleted.
     *
     * @group adminFunctionality
     */
    public function testModuleSettings()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped("Test is not for SubShop");
        }

        $this->loginAdmin("Extensions", "Modules");

        // checking If the same class extend two modules
        $this->openListItem("Test module #1");
        $this->openTab("Settings");
        $this->assertTextPresent("Empty Settings Group");
        $this->assertTextPresent("Filled Settings Group");

        // Asserting module settings values when default values are not set
        $this->click("link=Empty Settings Group");

        $this->assertNotChecked("//input[@name='confbools[testEmptyBoolConfig]' and @type='checkbox']");
        $this->assertElementValue("confstrs[testEmptyStrConfig]", '', 'Without default value text input (str) should be empty');
        $this->assertElementValue("confarrs[testEmptyArrConfig]", '', 'Without default value text area array (arr) should be empty');
        $this->assertElementValue("confaarrs[testEmptyAArrConfig]", '', 'Without default value text area assoc array (aarr) should be empty');
        $this->assertElementValue("confselects[testEmptySelectConfig]", '0', 'Without default value first option should be selected for selects');

        $oPassword = $this->getElement("confpassword[testEmptyPasswordConfig]");
        $this->assertEquals('', $oPassword->getValue(), 'Without default value password should be empty');
        $this->assertTrue($oPassword->isVisible(), 'Password confirm field should be visible when default value is not set');

        // Asserting module settings values when default values are set
        $this->click("link=Filled Settings Group");

        $this->assertChecked("//input[@name='confbools[testFilledBoolConfig]' and @type='checkbox']");
        $this->assertElementValue("confstrs[testFilledStrConfig]", 'testStr', 'Default value of text input (str) should be taken from metadata');
        $this->assertElementValue("confarrs[testFilledArrConfig]", "option1\noption2", 'Default value of text area array (arr) should be taken from metadata');
        $this->assertElementValue("confaarrs[testFilledAArrConfig]", "key1 => option1\nkey2 => option2", 'Default value of text area assoc array (aarr) should be taken from metadata');
        $this->assertElementValue("confselects[testFilledSelectConfig]", '2', 'Default value of select should be taken from metadata');

        $oPassword = $this->getElement("confpassword[testFilledPasswordConfig]");
        $this->assertEquals('', $oPassword->getValue(), 'Default value of password should be empty');
        $this->assertFalse($oPassword->isVisible(), 'Password confirm field should be invisible when default value is set');

        // Add some information to the input fields
        $this->check("//input[@name='confbools[testEmptyBoolConfig]' and @type='checkbox']");
        $this->type("confstrs[testEmptyStrConfig]", 'testString');
        $this->type("confarrs[testEmptyArrConfig]", "option1\noption2\noption3");
        $this->type("confaarrs[testEmptyAArrConfig]", "key1 => option1\nkey2 => option2");
        $this->select("confselects[testEmptySelectConfig]", "2");
        $this->type("css=.password_input", "testPassword");
        $this->type("confpassword[testEmptyPasswordConfig]", "testPassword");

        $this->clickAndWait('save');

        // Assert that added information appeared.
        $this->assertChecked("//input[@name='confbools[testEmptyBoolConfig]' and @type='checkbox']");
        $this->assertElementValue("confstrs[testEmptyStrConfig]", 'testString', 'Without default value text input (str) should be empty');
        $this->assertElementValue("confarrs[testEmptyArrConfig]", "option1\noption2\noption3", 'Without default value text area array (arr) should be empty');
        $this->assertElementValue("confaarrs[testEmptyAArrConfig]", "key1 => option1\nkey2 => option2", 'Without default value text area assoc array (aarr) should be empty');
        $this->assertElementValue("confselects[testEmptySelectConfig]", '2', 'Without default value first option should be selected for selects');

        $oPassword = $this->getElement("confpassword[testEmptyPasswordConfig]");
        $this->assertEquals('', $oPassword->getValue(), 'With saved value password should be empty');
        $this->assertFalse($oPassword->isVisible(), 'Password confirm field should be invisible when value is saved');
    }

    /**
     * Testing modules in vendor directory. Checking when any file with source code class of module is deleted.
     *
     * @group adminFunctionality
     */
    public function testModulesHandlingExtendingClass()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped("Test is not for SubShop");
        }

        $this->loginAdmin("Extensions", "Modules");

        // checking If the same class extend two modules
        $this->openListItem("Test module #1");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']", "list");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertTextPresent("1.0");
        $this->assertTextPresent("OXID");
        $this->assertTextPresent("-");
        $this->assertTextPresent("-");

        // activated seconds modules
        $this->openListItem("Test module #2");
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']", "list");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertTextPresent("Test module #2");
        $this->assertTextPresent("1.0");
        $this->assertTextPresent("OXID");
        $this->assertTextPresent("-");
        $this->assertTextPresent("-");

        // activated modules test7
        $this->openListItem("Test module #7");
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']", "list");
        $this->waitForFrameToLoad('list');
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertTextPresent("Test module #7");
        $this->assertTextPresent("1.0");
        $this->assertTextPresent("OXID");
        $this->assertTextPresent("-");
        $this->assertTextPresent("-");

        //checking if module all entry is displayed
        $this->openTab("Installed Shop Modules");
        $this->assertTextPresent("Drag items to change modules order. After changing order press Save button to save current modules order.");
        $this->assertEquals('test1/controllers/test1content', $this->getText("//li[@id='test1/controllers/test1content']/span"));
        $this->assertEquals('test2/view/myinfo2', $this->getText("//li[@id='test2/view/myinfo2']/span"));
        $this->assertEquals('oxid/test7/view/myinfo7', $this->getText("//li[@id='oxid/test7/view/myinfo7']/span"));

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextPresent("About Us + info1 + info2 + info7");

        $aModules = array('content' => 'test1_/view/myinfo1&test2/view/myinfo2&oxid/test7/view/myinfo7');
        $aModules = serialize($aModules);
        $this->callShopSC("oxConfig", null, null, array('aModules' => array("type" => "aarr", "value" => $aModules)));

        $this->loginAdmin("Extensions", "Modules");
        $this->frame("edit");
        $this->assertTextPresent("Problematic Files");
        $this->assertTextPresent("test1_/metadata.php");
        $this->clickAndWait("yesButton");

        $this->clearCache();
        $this->openShop();
        $this->open(shopURL."en/About-Us/");
        $this->assertTextPresent("About Us + info2 + info7");
        $this->assertTextNotPresent("About Us + info1 + info2 + info7");
    }

    /**
     * Test, that the module deactivation works in the non demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleActivationWorksInNormalMode()
    {
        $this->loginAdmin("Extensions", "Modules");
        $this->openListItem("Test module #6 (in vendor dir)");
        $this->assertActivationButtonIsPresent();
        $this->assertDeactivationButtonIsNotPresent();
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertDeactivationButtonIsPresent();
        $this->assertActivationButtonIsNotPresent();
    }

    /**
     * Test, that the module deactivation works in the non demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleDeactivationWorksInNormalMode()
    {
        $this->testModuleActivationWorksInNormalMode();

        $this->clickAndWait("//form[@id='myedit']//input[@value='Deactivate']");
        $this->assertActivationButtonIsPresent();
        $this->assertDeactivationButtonIsNotPresent();
    }

    /**
     * Test, that the module activation won't work in the demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleActivationIsSwitchedOffInDemoMode()
    {
        $this->loginAdmin("Extensions", "Modules");
        $this->switchToDemoMode();

        $this->openListItem("Test module #6 (in vendor dir)");
        $this->assertActivationButtonIsNotPresent();
        $this->assertDeactivationButtonIsNotPresent();
        $this->assertTextPresent('Please note: modules can\'t be activated or deactivated in demo shop mode.', "N");
    }

    /**
     * Test, that the module deactivation won't work in the demo mode.
     *
     * @group adminFunctionality
     * @group adminModules
     */
    public function testModuleDeactivationIsSwitchedOffInDemoMode()
    {
        $this->testModuleActivationWorksInNormalMode();
        $this->switchToDemoMode();

        $this->openListItem("Test module #6 (in vendor dir)");
        $this->assertActivationButtonIsNotPresent();
        $this->assertDeactivationButtonIsNotPresent();
        $this->assertTextPresent('Please note: modules can\'t be activated or deactivated in demo shop mode.', "N");
    }

    protected function assertActivationButtonIsPresent()
    {
        $this->assertButtonIsPresent('Activate');
    }

    protected function assertDeactivationButtonIsPresent()
    {
        $this->assertButtonIsPresent('Deactivate');
    }

    protected function assertButtonIsPresent($buttonValue)
    {
        $this->assertElementPresent("//form[@id='myedit']//input[@value='{$buttonValue}']");
    }

    protected function assertActivationButtonIsNotPresent()
    {
        $this->assertButtonIsNotPresent('Activate');
    }

    protected function assertDeactivationButtonIsNotPresent()
    {
        $this->assertButtonIsNotPresent('Deactivate');
    }

    protected function assertButtonIsNotPresent($buttonValue)
    {
        $this->assertElementNotPresent("//form[@id='myedit']//input[@value='{$buttonValue}']");
    }

    protected function switchToDemoMode()
    {
        $this->callShopSC("oxConfig", null, null, array("blDemoShop" => array("type" => "bool", "value" => "true")));
    }

    protected function switchDemoModeOff()
    {
        $this->callShopSC("oxConfig", null, null, array("blDemoShop" => array("type" => "bool", "value" => "false")));
    }

    /**
     * checking if switching themes works
     *
     * @group adminFunctionality
     */
    public function testConversionRateOptions()
    {
        $this->markTestSkipped("There is not done test, so we need to decide  finish or  delete test ");

        //basic theme on
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("System");
        $this->click("link=Order");
        $this->waitForText("Allow Orders from foreign Countries");
        $this->assertElementPresent("confbools[blDisableNavBars]");
        //checking in basic theme
        $this->selectMenu("Master Settings", "Themes");
        $this->openListItem("link=testtheme");
        $this->clickAndWaitFrame("//input[@value='Activate']", "list");
        $this->openTab("Settings");
    }

    /**
     * This simple test runs in all editions for subshop and varnish groups.
     * In this way CI generates test results for CE/PE editions.
     *
     * @group subshop
     * @group varnish
     */
    public function testChecksIfThereAreNoSubshops()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->frame('edit');
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->assertElementPresent("btn.new", "Subshop link should exist.");
        } else {
            $this->assertElementNotPresent("btn.new", "Subshop link should not exist.");
        }
    }

    /**
     * checking if econda is loaded in frontend
     *
     * @group adminFunctionality
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     */
    public function testEconda()
    {
        $this->markTestSkipped('deprecated sinve v5.3 (2016-11-04) Dynpages will be removed.');
        //activating econda
        $aConfigs = array("sShopCountry"      => array("type" => "str", "value" => 'de'),
                          "blLoadDynContents" => array("type" => "bool", "value" => 'true'));

        $this->callShopSC("oxConfig", null, null, $aConfigs);
        $this->loginAdmin("Shop controlling", "econda");

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            $this->openTab('Interface');
        }

        $this->frame("edit");
        $this->click("//input[@name='confbools[blEcondaActive]' and @type='checkbox']");
        $this->clickAndWait("save");

        //checking in frontend
        $this->clearCache();
        $this->openShop();

        $sUrl = "//script[@src='" . $this->getTestConfig()->getShopUrl() . "modules/econda/out/emos2.js']";

        $this->assertElementPresent($sUrl);
        $this->open(shopURL . "modules/econda/out/emos2.js");
        $this->assertTextPresent("function(){var URL_TRACKING_ALLOWED=true");
        $this->goBack();
        //home page checking
        $this->clearCache();
        $this->openShop();
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
        $this->clickAndWait("productList_1");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains("window.emosPropertiesEvent(emospro);", $htmlSource);
        $this->assertContains('emospro.content', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->assertContains('emospro.ec_Event = [["view","1000","Test', $htmlSource);
        $this->clickAndWait("toBasket");
        //acount page
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Login\/Formular\/Login";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->type("loginUser", "example_test@oxid-esales.dev");
        $this->type("loginPwd", "useruser");
        $this->clickAndWait("loginButton");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Login\/Uebersicht";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->assertContains('emospro.login = [["' . md5('example_test@oxid-esales.dev') . '"', $htmlSource);
        //basket page
        $this->openBasket();
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

    /**
     * Reset all orders shop id to current shop
     */
    private function updateSubshopOrders()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $orders = oxDb::getDb(oxDb::FETCH_MODE_NUM)->getAll('SELECT OXID FROM oxorder');
            foreach ($orders as $order) {
                $this->callShopSC("oxOrder", "save", $order[0], array("oxshopid" => $testConfig->getShopId()), null, 1);
            }
        }
    }

    /**
     * Regression test for bug #6500
     *
     * Administer Products -> Categories (categories tree display)
     * If two parent categories have an OXID that would result in the same integer value on a normal == comparison,
     * their subcategories should nevertheless display the correct value for "Subcategory of"
     *
     * @group adminFunctionality
     */
    public function testCategoryTreeDisplay()
    {
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for subshops');
        }

        /**
         * Insert fixtures. OXID for parent category is '003' resp. '03'
         */
        $query = <<<EOT
            INSERT IGNORE INTO `oxcategories` (`OXID`, `OXPARENTID`, `OXLEFT`, `OXRIGHT`, `OXROOTID`, `OXSORT`, `OXACTIVE`, `OXHIDDEN`, `OXSHOPID`, `OXTITLE`) VALUES
              ('003', 'oxrootid', 1, 4, '003', 1, 1, 0, 1, 'test 003'),
              ('003SUB', '003', 2, 3, '003', 0, 1, 0, 1, 'test sub 003'),
              ('03', 'oxrootid', 1, 4, '03', 1, 1, 0, 1, 'test 03'),
              ('03SUB', '03', 2, 3, '03', 0, 1, 0, 1, 'test sub 03');
EOT;
        oxDb::getDb()->execute($query);

        /**
         * Special fixture for OXID eShop Enterprise Edition
         */
        if ($testConfig->getShopEdition() == 'EE') {
            $query = <<<EOT
            INSERT IGNORE INTO `oxcategories2shop`
            (`OXSHOPID`, `OXMAPOBJECTID`)
              SELECT 1, `OXMAPID`
              FROM `oxcategories` WHERE `oxcategories`.`OXTITLE` LIKE 'test%03';
EOT;
            oxDb::getDb()->execute($query);
        }


        $this->loginAdmin("Administer Products", "Categories");
        $this->changeAdminListLanguage('Deutsch');
        $this->type('where[oxcategories][oxtitle]', 'test sub 03');
        $this->clickAndWaitFrame("submitit");
        $this->openListItem("test sub 03");
        $this->assertSame("03", $this->getValue("editval[oxcategories__oxparentid]"));
    }
}
