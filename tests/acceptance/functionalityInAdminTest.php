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

class Acceptance_functionalityInAdminTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

     // ------------------------ Admin interface functionality ----------------------------------

    /**
     * checking if order info is displayed correctly
     * @group admin
     * @group order
     * @group adminFunctionality
     */
    public function testDisplayingOrdersInfo()
    {
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=1");
        $this->assertEquals("Billing Address: Mr 1useršÄßüл 1UserSurnamešÄßüл 1 Street 1 HE 333000 2 City Germany E-mail: birute02@nfq.lt", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("Shipping Address: Mr shippingUseršÄßüл shippingSurnamešÄßüл Street 1 NI 1 City Germany", $this->clearString($this->getText("//td[2]")));
        $this->selectAndWait("setfolder", "label=Finished");
        $this->assertTrue($this->isTextPresent("Internal Status: OK"));
        $this->assertEquals("100,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("95,24", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("97,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]/td[1]"), "line with discount info is not displayed");
        $this->assertTrue($this->isElementPresent("//table[@id='order.info']/tbody/tr[4]/td[2]"), "line with discount info is not displayed");
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("//table[4]/tbody/tr[1]/td[2]"));
        $this->assertEquals("Standard", $this->getText("//table[4]/tbody/tr[2]/td[2]"));
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=Finished");
        $this->assertTrue($this->isElementPresent("link=1"));
        $this->openTab("link=Main", "editval[oxorder__oxordernr]");
        $this->assertTrue($this->isTextPresent("IP Address"));
        $this->assertEquals("1", $this->getValue("editval[oxorder__oxordernr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxbillnr]"));
        $this->assertEquals("", $this->getValue("editval[oxorder__oxtrackcode]"));
        $this->assertEquals("0", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("97,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
    }

    /**
     * checking if order info is displayed correctly
     * @group admin
     * @group order
     * @group adminFunctionality
     */
    public function testEditingOrdersMain()
    {
        $this->executeSql( "UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'" );
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("link=Main");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertTrue($this->isTextPresent("192.168.1.999"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"), "Manually edited delivery costs is not saved, if other del.cost was applied during order process");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->frame("list", "link=Overview");
        $this->openTab("link=Overview", "setfolder");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Main", "editval[oxorder__oxpaid]");
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $this->click("link=Current Date");
        sleep(1);
        $this->assertNotEquals("0000-00-00 00:00:00", $this->getValue("editval[oxorder__oxpaid]"));
        $sDate = date("Y-m-d")." 23:59:59";
        $this->type("editval[oxorder__oxpaid]", $sDate);
        $this->type("editval[oxorder__oxordernr]", "125");
        $this->type("editval[oxorder__oxbillnr]", "123");
        $this->type("editval[oxorder__oxtrackcode]", "456");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->frame("list", "link=125");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("192.168.1.999"));
        $this->assertTrue($this->isTextPresent("Order was paid ".$sDate));
        $this->assertEquals("125", $this->getValue("editval[oxorder__oxordernr]"));
        $this->assertEquals("123", $this->getValue("editval[oxorder__oxbillnr]"));
        $this->assertEquals("456", $this->getValue("editval[oxorder__oxtrackcode]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->assertEquals($sDate, $this->getValue("editval[oxorder__oxpaid]"));
        $this->frame("list");
        $this->assertTrue($this->isElementPresent("link=125"), "List frame is not refreshed after order Nr. was changed");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Main");
        $this->clickAndWaitFrame("//input[@name='save' and @value='  Ship Now  ']", "list");
        $this->assertTrue($this->isTextPresent("Shipped on ".date("Y-m-d")));
        $this->clickAndWaitFrame("//input[@name='save' and @value='Reset Shipping Date']", "list");
        $this->assertTrue($this->isTextPresent("Shipped on -"));
        $this->assertTrue($this->isTextPresent("192.168.1.999"));
    }

    /**
     * checking if order info is displayed correctly
     * @group admin
     * @group order
     * @group adminFunctionality
     */
    public function testEditingOrdersAddresses()
    {
        $this->executeSql( "UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'" );
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished", "link=1");
        $this->clickAndWaitFrame("link=1", "edit", "link=Main");
        $this->openTab("link=Main", "editval[oxorder__oxdelcost]");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->clickAndWaitFrame("saveFormButton", "list", "editval[oxorder__oxdelcost]");
        $this->frame("list", "link=Addresses");
        $this->openTab("link=Addresses");
        //billing address
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxorder__oxbillsal]"));
        $this->assertEquals("1useršÄßüл", $this->getValue("editval[oxorder__oxbillfname]"));
        $this->assertEquals("1UserSurnamešÄßüл", $this->getValue("editval[oxorder__oxbilllname]"));
        $this->assertEquals("birute02@nfq.lt", $this->getValue("editval[oxorder__oxbillemail]"));
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
        $this->type("editval[oxorder__oxbillemail]", "birute_test@nfq.com");
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
        $this->clickAndWait("save");
        $this->assertEquals($this->getSelectedLabel("editval[oxorder__oxbillsal]"), "Mrs");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillfname]"), "UserName");
        $this->assertEquals($this->getValue("editval[oxorder__oxbilllname]"), "UserSurname");
        $this->assertEquals($this->getValue("editval[oxorder__oxbillemail]"), "birute_test@nfq.com");
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
        $this->frame("list");
        $this->openTab("link=Main");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->assertEquals("NI", $this->getValue("editval[oxorder__oxbillstateid]"));
        $this->select("editval[oxorder__oxbillcountryid]", "label=Ireland");
        $this->type("editval[oxorder__oxbillstateid]", "");
        $this->clickAndWait("save");
        $this->frame("list", "link=Main");
        $this->openTab("link=Main", "editval[oxorder__oxdelcost]");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("COD (Cash on Delivery)", $this->getText("//table[4]/tbody/tr[1]/td[2]/b"));
        $this->assertEquals("Standard", $this->getText("//table[4]/tbody/tr[2]/td[2]/b"));
        $this->frame("list");
        $this->openTab("link=Addresses");
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
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("Billing Address: Company UserCompany User additional info Mrs UserName UserSurname Musterstr 10 790980 Musterstadt Ireland VAT ID: 123 E-mail: birute_test@nfq.com", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[1]")));
        $this->assertEquals("Shipping Address: Firma company add info Mrs name surname street 1 HE zip city Germany", $this->clearString($this->getText("//td[1]/table[1]/tbody/tr/td[2]")));
        $this->frame("list");
        $this->openTab("link=Main");
        $this->assertTrue($this->isTextPresent("192.168.1.999"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
    }

    /**
     * checking if order info is displayed correctly
     * @group admin
     * @group order
     * @group adminFunctionality
     */
    public function testEditingOrdersDelivery()
    {
        $this->executeSql( "UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'" );
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("link=Main", "editval[oxorder__oxdelcost]");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->select("setDelSet", "label=Standard");
        $this->clickAndWait("saveFormButton", "setPayment");
        $this->select("setPayment", "label=COD (Cash on Delivery)");
        $this->clickAndWait("saveFormButton");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        sleep(10);
        $this->frame("list");

        $this->openTab("link=Overview", "setfolder");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("100,00", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("85,71", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("4,29", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("10,00", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("7,50", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("107,50", $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertTrue($this->isTextPresent("COD (Cash on Delivery)"));
        //skonto was removed from demodata
        $this->assertFalse($this->isTextPresent("Cash in advance - 2% cash discount"));
        $this->assertTrue($this->isTextPresent("Standard"));
        $this->frame("list");
        $this->openTab("link=Main");
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->type("editval[oxorder__oxdelcost]", "9.9");
        $this->clickAndWaitFrame("saveFormButton", "list");
        $this->assertEquals("9.9", $this->getValue("editval[oxorder__oxdelcost]"));
        $this->assertEquals("10", $this->getValue("editval[oxorder__oxdiscount]"));
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertEquals("- 10,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->frame("list");
    }

    /**
     * checking if order info is displayed correctly
     * @group admin
     * @group order
     * @group adminFunctionality
     */
    public function testEditingOrdersProducts()
    {
        $this->executeSql( "UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'" );
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("link=Main", "editval[oxorder__oxdelcost]");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->select("setDelSet", "label=Example Set1: UPS 48 hours");
        $this->clickAndWait("saveFormButton");
        $this->assertEquals("----", $this->getSelectedLabel("setPayment"));
        $this->assertEquals("Example Set1: UPS 48 hours", $this->getSelectedLabel("setDelSet"));
        $this->select("setDelSet", "label=Standard");
        $this->clickAndWait("saveFormButton");
        $this->select("setPayment", "label=COD (Cash on Delivery)");
        $this->clickAndWait("saveFormButton");
        $this->frame("list");
        $this->openTab("link=Products");
        $this->type("sSearchArtNum", "1001");
        $this->clickAndWait("//input[@name='search']");
        $this->assertEquals("Test product 1 [EN] šÄßüл 100,00 EUR", $this->getText("aid"));
        $this->assertEquals("selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->getText("test_select__0"));
        $this->type("sSearchArtNum", "1002");
        $this->clickAndWait("//input[@name='search']");
        $this->assertEquals("Test product 2 [EN] šÄßüл priceFrom 55,00 EUR Test product 2 [EN] šÄßüл var1 [EN] šÄßüл 55,00 EUR Test product 2 [EN] šÄßüл var2 [EN] šÄßüл 67,00 EUR", $this->clearString($this->getText("aid")));
        $this->assertFalse($this->isElementPresent("test_select__0"));
        $this->type("sSearchArtNum", "100");
        $this->clickAndWait("//input[@name='search']");
        $this->assertFalse($this->isElementPresent("aid"));
        $this->assertFalse($this->isElementPresent("test_select__0"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        $this->type("sSearchArtNum", "1003");
        $this->clickAndWait("//input[@name='search']");
        $this->assertEquals("Test product 3 [EN] šÄßüл 75,00 EUR", $this->clearString($this->getText("aid")));
        $this->type("am", "2");
        $this->clickAndWait("add");
        $this->assertEquals("250,00",  $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]"));
        $this->assertEquals("- 25,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->assertEquals("199,16",  $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->assertEquals("4,29",    $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]"));
        $this->assertEquals("21,55",   $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]"));
        $this->assertEquals("0,00",    $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("7,50",   $this->getText("//table[@id='order.info']/tbody/tr[7]/td[2]"));
        $this->assertEquals("232,50",  $this->getText("//table[@id='order.info']/tbody/tr[8]/td[2]"));

        $this->assertEquals("VAT (5%)",    $this->getText("//table[@id='order.info']/tbody/tr[4]/td[1]"));
        $this->assertEquals("VAT (19%)",   $this->getText("//table[@id='order.info']/tbody/tr[5]/td[1]"));
        $this->frame("list");
        $this->openTab("link=Main", "editval[oxorder__oxordernr]");
        $this->frame("list");
        $this->openTab("link=Overview", "setfolder");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->assertTrue($this->isTextPresent("Order not shipped yet."));
        $this->check("sendmail");
        $this->clickAndWait("save");
        $this->assertTrue($this->isTextPresent("Shipped on ".date("Y-m-d")));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("- 25,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->clickAndWait("//input[@name='save' and @value='Reset Shipping Date']");
        $this->assertTrue($this->isTextPresent("Order not shipped yet."));
        $this->assertEquals("0,00", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]"));
        $this->assertEquals("- 25,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]"));
        $this->frame("list");
        $this->openTab("link=Main");
        $this->frame("list");
        $this->openTab("link=History");
    }

    /**
     * not registered user makes order. later someone else registers with same email.
     * already creted order is edited (added some products). #1696
     * @group admin
     * @group order
     * @group adminFunctionality
     */
    public function testEditingNotRegisteredUserOrder()
    {
        $this->openShop();
        //not registered user creates the order

        $this->searchFor("1001");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $this->type("userLoginName", "birute01@nfq.lt");
        $this->type("invadr[oxuser__oxfname]", "name");
        $this->type("invadr[oxuser__oxlname]", "surname");
        $this->type("invadr[oxuser__oxstreet]", "street");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "3000");
        $this->type("invadr[oxuser__oxcity]", "city");
        $this->select("invCountrySelect", "label=Germany");
        $this->select("oxStateSelect_invadr[oxuser__oxstateid]", "label=Brandenburg");
        $this->type("orderRemark", "remark text");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->assertTrue($this->isTextPresent("What I wanted to say ...: remark text"));
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

        //someone creates acc with same info and email
        $this->openShop();
        $this->clickAndWait("link=Register");
        $this->type("userLoginName", "birute01@nfq.lt");
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
        $this->clickAndWait("//button[text()='Save']");
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
        $this->assertEquals("Germany", $this->getSelectedLabel("bill_country_select"));

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
        $this->assertEquals("Germany", $this->getSelectedLabel("bill_country_select"));
    }

    /**
     * System settings: Currency values
     * @group admin
     * @group adminFunctionality
     */
    public function testCurrencyValues()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Other settings");
        sleep(1);
        $this->assertEquals("EUR@ 1.00@ ,@ .@ €@ 2\nGBP@ 0.8565@ .@  @ £@ 2\nCHF@ 1.4326@ ,@ .@ <small>CHF</small>@ 2\nUSD@ 1.2994@ .@  @ $@ 2", $this->getValue("confarrs[aCurrencies]"));
        $this->clickAndWait("save");
        $this->click("link=Other settings");
        sleep(1);
        $this->assertEquals("EUR@ 1.00@ ,@ .@ €@ 2\nGBP@ 0.8565@ .@  @ £@ 2\nCHF@ 1.4326@ ,@ .@ <small>CHF</small>@ 2\nUSD@ 1.2994@ .@  @ $@ 2", $this->getValue("confarrs[aCurrencies]"));
    }

    /**
     * System settings: Save automatically when changing Tabs
     * @group admin
     * @group adminFunctionality
     */
    public function testAutosaveOnChangingTabs()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=System");
        $this->assertFalse($this->isElementPresent("//input[@name='confbools[blAutoSave]']"), "Option 'Save automatically when changing Tabs' should be removed. Confirmed by Ralf");
    }

    /**
     * Service -> System info
     * @group admin
     * @group adminFunctionality
     */
    public function testSystemInfo()
    {
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->click("link=Service");
        $this->click("link=System Info");
        $this->waitForFrameToLoad("basefrm");
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");
        $this->checkForErrors();
        $this->assertTrue($this->isTextPresent("Configuration"), "System information is not loaded: Service -> System Info");
        $this->assertTrue($this->isTextPresent("PHP Version"), "System information is not loaded: Service -> System Info");
    }

    /**
     * Service -> System Requirements
     * @group admin
     * @group adminFunctionality
     */
    public function testSystemRequirements()
    {
        $this->loginAdmin("Service", "System health", "btn.help");
        $this->frame("edit");
        $this->waitForText("State of system health");
    }


    /**
     * Service -> Tools
     * @group admin
     * @group adminFunctionality
     */
    public function testTools()
    {
        $this->loginAdmin("Service", "Tools", "btn.help");
        $this->frame("edit");
        $this->assertTrue($this->isTextPresent("Update SQL "), "Tools page is not loaded: Service -> Tools");
        $this->assertTrue($this->isElementPresent("updatesql"));
    }

    /**
     * Service -> Generic Import
     * @group admin
     * @group adminFunctionality
     */
    public function testGenericImport()
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->click("link=Service");
        $this->click("link=Generic Import");
        $this->waitForFrameToLoad("basefrm");
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");
        $this->checkForErrors();
        $this->assertTrue($this->isTextPresent("Uploading CSV file"));
        $this->assertTrue($this->isElementPresent("save"));
    }

    /**
     * Service -> Product Export
     * @group admin
     * @group adminFunctionality
     */
    public function testProductExport()
    {
            $this->clearTmp();
            $this->loginAdmin("Administer Products", "Products");
            //testing export
            $this->selectFrame("relative=top");
            $this->selectFrame("navigation");
            $this->checkForErrors();
            $this->click("link=Service");
            $this->click("link=Product Export");
            $this->waitForFrameToLoad("basefrm");
            $this->selectFrame("relative=top");
            $this->selectFrame("basefrm");
            $this->selectFrame("dynexport_do");
            $this->checkForErrors();
            $this->assertTrue($this->isTextPresent("Export not yet started."));
            $this->selectFrame("relative=up");
            $this->selectFrame("dynexport_main");
            $this->checkForErrors();
            $this->addSelection("acat[]", "label=Test category 0 [EN] šÄßüл");
            $this->assertTrue($this->isElementPresent("search"));
            $this->clickAndWaitFrame("save", "dynexport_do");
            $this->selectFrame("relative=up");
            $this->selectFrame("dynexport_do");
            $this->checkForErrors();
            $this->waitForElement("link=here");
            $this->click("link=here");
            $aWindows = $this->getAllWindowNames();
            $this->selectWindow(end($aWindows));
            $this->assertTrue($this->isTextPresent(shopURL."en/Test-category-0-EN-AEssue/Test-product-0-EN-AEssue.html"));
            $this->close();

            $this->selectWindow(null);
            $this->selectMenu("Administer Products", "Products");
            $this->assertEquals("English", $this->getSelectedLabel("changelang"));
            $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
            $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
            $this->clickAndWait("link=Prod.No.");
            $this->clickAndWaitFrame("link=1000", "edit");
            $this->openTab("link=Extended");
            $this->click("//input[@value='Assign Categories']");
            $this->usePopUp();
            $this->type("_0", "*šÄßüл");
            $this->keyUp("_0", "y");
            $this->click("container2_btn");
            $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[5]/td[1]", "container2");
            $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->close();
            $this->selectWindow(null);
            $this->selectFrame("relative=top");
            $this->selectFrame("navigation");
            $this->checkForErrors();
            $this->click("link=Service");
            $this->click("link=Product Export");
            $this->waitForFrameToLoad("basefrm");
            $this->selectFrame("relative=top");
            $this->selectFrame("basefrm");
            $this->selectFrame("dynexport_main");
            $this->checkForErrors();
            $this->click("save");
            $this->selectFrame("relative=up");
            $this->selectFrame("dynexport_do");
            $this->waitForElement("link=here");
            $this->click("link=here");
            $aWindows = $this->getAllWindowNames();
            $this->selectWindow(end($aWindows));
            $this->assertTrue($this->isTextPresent(shopURL."5-DE-category-AEssue/DE-4-Test-product-0-AEssue.html"));
            $this->close();

            $this->selectWindow(null);
            //assigning other main category
            $this->selectMenu("Administer Products", "Products");
            $this->clickAndWait("link=Prod.No.");
            $this->clickAndWaitFrame("link=1000", "edit");
            $this->openTab("link=Extended");
            $this->click("//input[@value='Assign Categories']");
            $this->usePopUp();
            $this->type("_0", "*šÄßüл");
            $this->keyUp("_0", "y");
            $this->click("container2_btn");
            $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
            $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->click("makeact-button");
            sleep(1);
            $this->close();
            $this->selectWindow(null);
            $this->windowMaximize(null);
            $this->selectFrame("relative=top");
            $this->selectFrame("navigation");
            $this->checkForErrors();
            $this->click("link=Service");
            $this->click("link=Product Export");
            $this->waitForFrameToLoad("basefrm");
            $this->selectFrame("relative=top");
            $this->selectFrame("basefrm");
            $this->selectFrame("dynexport_main");
            $this->checkForErrors();
            $this->click("save");
            $this->selectFrame("relative=up");
            $this->selectFrame("dynexport_do");
            $this->waitForElement("link=here");
            $this->click("link=here");
            $aWindows = $this->getAllWindowNames();
            $this->selectWindow(end($aWindows));
            $this->assertTrue($this->isTextPresent(shopURL."1-DE-category-AEssue/DE-4-Test-product-0-AEssue.html"));
    }

    /**
     * Testing if admin interface is loaded correctly in both EN and DE lang
     * @group admin
     * @group adminFunctionality
     */
    public function testLoginToAdminInOtherLang()
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->click("//input[@type='submit']");
        $this->waitForElement("nav");
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");
        $this->waitForText("Welcome to the OXID eShop Admin");
        //$this->assertTrue($this->isTextPresent("Welcome to the OXID eShop Admin."));
        $this->checkForErrors();
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->assertTrue($this->isElementPresent("link=Master Settings"));
        $this->assertTrue($this->isElementPresent("link=Shop Settings"));
        $this->assertTrue($this->isElementPresent("link=Administer Products"));
        $this->click("link=Administer Products");
        $this->waitForItemAppear("link=Products");
        $this->clickAndWaitFrame("link=Products", "edit");
        $this->frame("list");
        $this->waitForElement("row.1");
        $this->checkForErrors();
        $this->frame("edit");
        $this->waitForElement("btn.new");
        $this->checkForErrors();
        $this->selectFrame("relative=top");
        $this->selectFrame("header");
        $this->assertTrue($this->isElementPresent("link=Logout"));
        $this->clickAndWait("link=Logout");
        $this->type("usr", "admin@myoxideshop.com");
        $this->type("pwd", "admin0303");
        $this->select("chlanguage", "label=Deutsch");
        $this->click("//input[@value='Start OXID eShop Admin']");
        $this->waitForElement("nav");
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");
        $this->waitForText("Willkommen im OXID eShop Administrationsbereich");
        $this->checkForErrors();
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->assertTrue($this->isElementPresent("link=Stammdaten"));
        $this->assertTrue($this->isElementPresent("link=Shopeinstellungen"));
        $this->selectFrame("relative=up");
        $this->selectFrame("header");
        $this->assertTrue($this->isElementPresent("link=Abmelden"));
        $this->clickAndWait("link=Abmelden");
        $this->assertTrue($this->isElementPresent("usr"));
    }

    /**
     * Master Settings -> Languages
     * @group admin
     * @group adminFunctionality
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
        $this->type("editval[sort]", "3");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectMenu("Service", "Tools");
        $this->frame("edit");
        $this->clickAndConfirm("//input[@value='Update DB Views now']", null, "list");
        $this->selectWindow(null);
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//a/span[text()='Lietuviu']"));
        $this->searchFor("1001");
        $this->assertEquals("1 Hits for \"1001\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Art.No.: 1001", $this->getText("productArtnum"));
        $this->clickAndWait("link=Home");
        //LT lang
        $this->switchLanguage("Lietuviu");
        $this->searchFor("1001");
        $this->assertEquals("1 Hits for [LT] \"1001\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Art.No. [LT]: 1001", $this->getText("productArtnum"));
    }

    /**
     * Administer Products -> Categories (price categories testing)
     * @group admin
     * @group adminFunctionality
     */
    public function testPriceCategoryCreating()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Title");
        $this->openTab("link=1 [DE] category šÄßüл");
        $this->assertTrue($this->isEditable("//input[@value='Assign Products']"));
        $this->assertEquals("0", $this->getValue("editval[oxcategories__oxpricefrom]"));
        $this->assertEquals("0", $this->getValue("editval[oxcategories__oxpriceto]"));
        $this->type("editval[oxcategories__oxpricefrom]", "5");
        $this->type("editval[oxcategories__oxpriceto]", "100");
        sleep(1);
        $this->assertFalse($this->isEditable("//input[@value='Assign Products']"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertFalse($this->isEditable("//input[@value='Assign Products']"));
        $this->type("editval[oxcategories__oxpricefrom]", "");
        $this->type("editval[oxcategories__oxpriceto]", "");
        sleep(1);
        $this->assertTrue($this->isEditable("//input[@value='Assign Products']"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertTrue($this->isEditable("//input[@value='Assign Products']"));
    }

    /**
     * Administer Products -> Products (variants should inherit parents selection lists)
     * @group admin
     * @group main
     * @group adminFunctionality
     */
    public function testVariantsInheritsSelectionLists()
    {
        //assigning selection list to parent product
        $this->loginAdmin("Administer Products", "Products");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxarticles][oxartnum]", "1002");
        $this->clickAndWait("submitit", "link=1002");
        $this->openTab("link=1002", "editval[oxarticles__oxtitle]");
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
        $this->searchFor("1002");
        $this->clickAndWait("searchList_1");

        $this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("//div[@id='productSelections']//ul")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->selectVariant("variants", "1", "var1 [EN] šÄßüл", "Selected combination: var1 [EN] šÄßüл");
        $this->selectVariant("productSelections", "1", "selvar3 [EN] šÄßüл -2,00 €", "Selected combination: var1 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->selectVariant("productSelections", "1", "selvar2 [EN] šÄßüл", "Selected combination: var1 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]/div")));
        $this->assertTrue($this->isElementPresent("cartItemSelections_1"));
        $this->assertEquals("test selection list [EN] šÄßüл: selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//p"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл", $this->clearString($this->getText("//tr[@id='cartItem_2']/td[3]/div")));
        $this->assertTrue($this->isElementPresent("cartItemSelections_2"));
        $this->assertEquals("test selection list [EN] šÄßüл: selvar2 [EN] šÄßüл", $this->getText("//div[@id='cartItemSelections_2']//p"));
    }


    /**
     * Core settings -> Settings -> Active Category at Start
     * @group admin
     * @group adminFunctionality
     */
    public function testActiveCategoryAtStart()
    {
        $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
        $this->markTestSkipped("waiting for desition from management, if this option should be in azure theme at all");

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
        $this->assertTrue($this->isElementPresent("//ul[@id='tree']/li/ul/li/a"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("//ul[@id='tree']/li/ul/li/a"));
    }

    /**
     * checking how help popups are working
     * @group admin
     * @group adminFunctionality
     */
    public function testHelpPopupsInAdmin()
    {
        //testing help popup for shop active checkbox
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->frame("edit");
        $this->checkForErrors();
        $this->assertEquals("", $this->clearString($this->getText("helpPanel")));
        $this->assertFalse($this->isElementPresent("link=Close"));
        $this->assertTrue($this->isElementPresent("helpBtn_HELP_SHOP_MAIN_PRODUCTIVE"));
        $this->click("helpBtn_HELP_SHOP_MAIN_PRODUCTIVE");
        $this->waitForItemAppear("helpPanel");
        $this->assertTrue($this->isVisible("helpPanel"));
        $this->assertEquals("As long as this setting is not active", substr($this->clearString($this->getText("helpPanel")),0,37));
        $this->click("link=Close");
        $this->waitForItemDisappear("helpPanel");
        $this->checkForErrors();
    }


    /**
     * editing main shop details
     * @group admin
     * @group create
     * @group adminFunctionality
     */
    public function testEditShopInfo()
    {
        $sShopNr = $this->getShopVersionNumber();
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->frame("edit");
        //asserting default shop values in EN lang
        $this->assertEquals($this->getSelectedLabel("subjlang"), "English");
        $this->assertEquals("Your Company Name", $this->getValue("editval[oxshops__oxcompany]"));
        $this->assertEquals("John", $this->getValue("editval[oxshops__oxfname]"));
        $this->assertEquals("Doe", $this->getValue("editval[oxshops__oxlname]"));
        $this->assertEquals("on", $this->getValue("editval[oxshops__oxproductive]"));
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
        $this->assertEquals("mail.nfq.lt", $this->getValue("editval[oxshops__oxsmtp]"));
        $this->assertEquals("", $this->getValue("editval[oxshops__oxsmtpuser]"));
        $this->assertEquals("", $this->getValue("oxsmtppwd"));
        $this->assertEquals("birute_test@nfq.lt", $this->getValue("editval[oxshops__oxinfoemail]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getValue("editval[oxshops__oxorderemail]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getValue("editval[oxshops__oxowneremail]"));
        $this->assertEquals("OXID eShop 4", $this->getValue("editval[oxshops__oxname]"));

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
        $this->type("editval[oxshops__oxsmtp]", "");
        $this->type("editval[oxshops__oxsmtpuser]", "user_šÄßüл");
        $this->type("oxsmtppwd", "pass");
        $this->type("editval[oxshops__oxinfoemail]", "");
        $this->type("editval[oxshops__oxorderemail]", "");
        $this->type("editval[oxshops__oxowneremail]", "");
        $this->clickAndWait("save");

        //changing lang to DE.
        $this->selectAndWait("subjlang", "label=Deutsch", "editval[oxshops__oxordersubject]");
        $this->assertEquals($this->getSelectedLabel("subjlang"), "Deutsch");
        $this->assertEquals("Ihre Bestellung bei OXID eSales", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Ihr Passwort im OXID eShop", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Ihre OXID eSales Bestellung wurde versandt", $this->getValue("editval[oxshops__oxsendednowsubject]"));
        $this->type("editval[oxshops__oxordersubject]", "Ihre Bestellung bei OXID eSales1_šÄßüл");
        $this->type("editval[oxshops__oxregistersubject]", "Vielen Dank fur Ihre Registrierung im OXID eShop1_šÄßüл");
        $this->type("editval[oxshops__oxforgotpwdsubject]", "Ihr Passwort im OXID eShop1_šÄßüл");
        $this->type("editval[oxshops__oxsendednowsubject]", "Ihre OXID eSales Bestellung wurde versandt1_šÄßüл");
        $this->clickAndWait("save");

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
        $this->assertEquals("", $this->getValue("editval[oxshops__oxsmtp]"));
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

        $this->selectAndWait("subjlang", "label=English");
        $this->assertEquals($this->getSelectedLabel("subjlang"), "English");
        $this->assertEquals("Your order at OXID eShop", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->type("editval[oxshops__oxordersubject]", "Your order from OXID eShop1");
        $this->type("editval[oxshops__oxregistersubject]", "Thank you for your registration in OXID eShop1");
        $this->type("editval[oxshops__oxforgotpwdsubject]", "Your OXID eShop password1");
        $this->type("editval[oxshops__oxsendednowsubject]", "Your OXID eSales Order has been shipped1");
        $this->type("oxsmtppwd", "-");
        $this->clickAndWait("save");

        $this->assertEquals("Your order from OXID eShop1", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Thank you for your registration in OXID eShop1", $this->getValue("editval[oxshops__oxregistersubject]"));
        $this->assertEquals("Your OXID eShop password1", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Your OXID eSales Order has been shipped1", $this->getValue("editval[oxshops__oxsendednowsubject]"));

        $this->selectAndWait("subjlang", "label=Deutsch");
        $this->assertEquals("Ihre Bestellung bei OXID eSales1_šÄßüл", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->assertEquals("Vielen Dank fur Ihre Registrierung im OXID eShop1_šÄßüл", $this->getValue("editval[oxshops__oxregistersubject]"));
        $this->assertEquals("Ihr Passwort im OXID eShop1_šÄßüл", $this->getValue("editval[oxshops__oxforgotpwdsubject]"));
        $this->assertEquals("Ihre OXID eSales Bestellung wurde versandt1_šÄßüл", $this->getValue("editval[oxshops__oxsendednowsubject]"));
        $this->assertEquals("", $this->getValue("oxsmtppwd"));

        //testing if other tabs are working after those changes
        $this->frame("list");
        $this->openTab("link=Settings");
        $this->frame("list");
        $this->openTab("link=System");
        $this->frame("list");
        $this->openTab("link=SEO");
        $this->frame("list");
        $this->openTab("link=Perform.");
    }


    /**
     * CMS page in top menu (frontend)
     * @group admin
     * @group create
     * @group adminFunctionality
     */
    public function testCMSpageAsTopMenu()
    {
        $this->captureScreenshotOnFailure = false; // Workaround for phpunit 3.6, disable screenshots before skip!
        $this->markTestSkipped("there is no upper menu in Azure theme. waiting for desition from management");

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
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("link=Home");
//
//        $this->assertEquals("new page", $this->getText("link=new page"));
//        $sShopId = "";
/*        $this->assertEquals(shopURL."en/new-page/".$sShopId, $this->getAttribute("//a[text()='new page']@href"));
        $this->clickAndWait("link=new page");
        $this->assertEquals("new page", $this->getText("//h1"));
        $this->assertEquals("You are here: / new page", $this->getText("breadCrumb"));
        $this->assertEquals(shopURL."en/new-page/".$sShopId, $this->getLocation());

        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->click("//ul[@id='languages']/li[2]/a");
        $this->waitForItemDisappear("languages");

        $this->clickAndWait("test_Lang_Deutsch");
        $this->assertEquals(shopURL."new-page/".$sShopId, $this->getAttribute("//a[text()='new page']@href"));
        $this->assertEquals(shopURL."new-page/".$sShopId, $this->getLocation());
        $this->clickAndWait("//ul[@id='account_menu']/li[8]/a");
        $this->assertEquals("Sie sind hier: / new page", $this->getText("breadCrumb"), "Bug from mantis #1176");
*/
    }



    /**
     * Core settings options saving. checking if saving options does not break the shop
     * @group admin
     * @group adminFunctionality
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
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//ul[@id='productList']/li[2]/form/div/div/a"));
        $this->assertEquals("100,00 €", $this->getText("//ul[@id='productList']/li[2]//span[2]"));
    }


    /**
     * My Account navigation: Order history
     * Product amounts after order and while editing order in admin
     * Also testing min order price
     * @group navigation
     * @group user
     * @group order
     * @group adminFunctionality
     */
    public function testOrdersEditingAmount()
    {
            $sShopId = "oxbaseshop";

        $sql ="INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLEMAIL`,
                                      `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`,
                                      `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`,
                                      `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXDELCOST`,
                                      `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXDISCOUNT`, `OXCURRENCY`,
                                      `OXCURRATE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`)
                              VALUES ('order1', '".$sShopId."', 'testuser', '2010-04-19 16:52:56', 12, 'birute_test@nfq.lt',
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
                                               2, 2, 'oxarticle',  '".$sShopId."')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`)
                                      VALUES ('payment1', 'testuser', 'oxidcashondel', '');";
        $this->executeSql($sql);

        //checking if product stock quantity was changed after order
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("15", $this->getValue("editval[oxarticles__oxstock]"));

        //deleting order articles to check if product amount is restored
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("link=Products");
        $this->assertTrue($this->isElementPresent("//tr[@id='art.1']"));
        $this->assertFalse($this->isElementPresent("//tr[@id='art.2']"));
        $this->clickAndConfirm("//tr[@id='art.1']/td[11]/a");
        $this->assertFalse($this->isElementPresent("//tr[@id='art.1']"));
        $this->assertFalse($this->isElementPresent("//tr[@id='art.2']"));
        $this->selectMenu("Administer Products", "Products", "btn.new", "where[oxarticles][oxartnum]");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));

        //adding order articles to check if amount is updated
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("link=Products");
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
        $this->openTab("link=Stock");
        $this->assertEquals("12.4", $this->getValue("editval[oxarticles__oxstock]"));

        //canceling both order articles to check if amount is updated
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWaitFrame("link=12", "edit");
        $this->openTab("link=Products");
        $this->clickAndConfirm("//tr[@id='art.2']/td[12]/a");
        $this->clickAndConfirm("//tr[@id='art.1']/td[12]/a");
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));
    }


    /**
     * checking if product amounts are restored after order is canceled
     * @group order
     * @group navigation
     * @group adminFunctionality
     */
    public function testDeletingOrderCheckingProductsAmount()
    {
            $sShopId = "oxbaseshop";

        $sql ="INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLEMAIL`,
                                      `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`,
                                      `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`,
                                      `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXDELCOST`,
                                      `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXDISCOUNT`, `OXCURRENCY`,
                                      `OXCURRATE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`)
                              VALUES ('order1', '".$sShopId."', 'testuser', '2010-04-19 16:52:56', 12, 'birute_test@nfq.lt',
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
                                               2, 2, 'oxarticle',  '".$sShopId."')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`)
                                      VALUES ('payment1', 'testuser', 'oxidcashondel', '');";
        $this->executeSql($sql);

        //checking product count
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("15", $this->getValue("editval[oxarticles__oxstock]"));

        //deleting order
        $this->selectMenu("Administer Orders", "Orders");
        $this->clickAndWait("link=Order No.");
        $this->clickAndConfirm("del.1");

        //checking if product count was restored
        $this->selectMenu("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("link=Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * checking if product amounts are restored after order is canceled
     * @group order
     * @group navigation
     * @group adminFunctionality
     */
    public function testCancelingOrderCheckingProductsAmount()
    {
            $sShopId = "oxbaseshop";

        $sql ="INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLEMAIL`,
                                      `OXBILLFNAME`, `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`,
                                      `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`,
                                      `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXARTVAT1`, `OXARTVATPRICE1`, `OXDELCOST`,
                                      `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`, `OXWRAPVAT`, `OXDISCOUNT`, `OXCURRENCY`,
                                      `OXCURRATE`, `OXFOLDER`, `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`)
                              VALUES ('order1', '".$sShopId."', 'testuser', '2010-04-19 16:52:56', 12, 'birute_test@nfq.lt',
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
                                               2, 2, 'oxarticle',  '".$sShopId."')";
        $this->executeSql($sql);

        $sql = "INSERT INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`)
                                      VALUES ('payment1', 'testuser', 'oxidcashondel', '');";
        $this->executeSql($sql);

        //checking product amount
        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1000");
        $this->clickAndWait("submitit");
        $this->clickAndWaitFrame("link=1000", "edit");
        $this->openTab("link=Stock");
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
        $this->openTab("link=Stock");
        $this->assertEquals("17", $this->getValue("editval[oxarticles__oxstock]"));
    }

    /**
     * Allowing negative stock values
     * @group navigation
     * @group order
     * @group adminFunctionality
     */
    public function testFrontendNegativeStockValuesOn()
    {
        //allow negative stock values
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blAllowNegativeStock" => array("type" => "bool", "value" => 'true')));
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //creating 2 orders
        for ($i=0; $i<2; $i++) {
            $this->searchFor("1003");
            $this->selectDropDown("viewOptions", "Line");
            $this->type("//ul[@id='searchList']/li[1]//input[@id='amountToBasket_searchList_1']", "4");
            $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
            $this->openBasket();
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->check("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
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
        $this->clickAndWaitFrame("link=1003", "edit");
        $this->openTab("link=Stock");
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
     * @group adminFunctionality
     */
    public function testFrontendNegativeStockValuesOff()
    {
        //disabling negative stock values
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blAllowNegativeStock" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        //creating 2 orders
        for ($i=0; $i<2; $i++) {
            $this->searchFor("1003");
            $this->selectDropDown("viewOptions", "Line");
            $this->type("//ul[@id='searchList']/li[1]//input[@id='amountToBasket_searchList_1']", "4");
            $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
            $this->openBasket();
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->check("//form[@id='orderConfirmAgbBottom']/div/input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbBottom']//button");
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
     * checking if switching themes works
     * @group admin
     * @group adminFunctionality
     */
    public function testSwitchingThemes()
    {
        $this->loginAdmin("Master Settings", "Themes", "//form[@id='transfer']", "link=Basic");
        $this->openTab("link=Basic", "//input[@value='Activate']");
        $this->assertTrue($this->isTextPresent("Basic theme by OXID"));
        $this->clickAndWaitFrame("//input[@value='Activate']", "list");
        $this->assertFalse($this->isElementPresent("//input[@value='Activate']"));
        $this->frame("list");
        $this->openTab("link=Settings", "//input[@value='Save']");
        $this->click("link=Images");
        $this->waitForItemAppear("confstrs[sIconsize]");
        $this->assertTrue($this->isTextPresent("Icon size"));
        $this->clearTmp();
        $this->openShop();
        $this->assertTrue($this->isElementPresent("path"));
        $this->assertTrue($this->isElementPresent("test_Lang_Deutsch"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("test_cntr_2_1001"));

        //azure theme on
        $this->loginAdmin("Master Settings", "Themes");
        $this->openTab("link=Azure", "//input[@value='Activate']");
        $this->assertTrue($this->isTextPresent("Azure theme by OXID"));
        $this->clickAndWaitFrame("//input[@value='Activate']", "list");
        $this->assertFalse($this->isElementPresent("//input[@value='Activate']"));
        $this->frame("list");
        $this->openTab("link=Settings", "//input[@value='Save']");
        $this->click("link=Images");
        $this->waitForItemAppear("confstrs[sIconsize]");
        $this->assertTrue($this->isTextPresent("Icon size"));
        $this->clearTmp();
        $this->openShop();
        $this->assertTrue($this->isElementPresent("topMenu"));
        $this->assertTrue($this->isElementPresent("footerServices"));
        $this->assertTrue($this->isElementPresent("footerCategories"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]//a"));
    }

    /**
     * checking if switching themes works
     * @group admin
     * @group adminFunctionality
     */
    public function testConversionRateOptions()
    {
        //basic theme on
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=System", "link=Order");
        $this->click("link=Order");
        $this->waitForText("Allow Orders from foreign Countries");
        $this->assertTrue($this->isElementPresent("confbools[blDisableNavBars]"));
        //checking in basic theme
        $this->selectMenu("Master Settings", "Themes");
        $this->openTab("link=Basic", "//input[@value='Activate']");
        $this->clickAndWaitFrame("//input[@value='Activate']", "list");
        $this->frame("list");
        $this->openTab("link=Settings", "//input[@value='Save']");

    }


    /**
     * checking if econda is loaded in frontend
     * @group admin
     * @group adminFunctionality
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
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Login\/Formular\/Login";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->type("loginUser", "birute_test@nfq.lt");
        $this->type("loginPwd", "useruser");
        $this->clickAndWait("loginButton");
        $htmlSource = $this->getHtmlSource();
        $this->assertContains('emospro.content = "Login\/Uebersicht";', $htmlSource);
        $this->assertContains('emospro.langid = 1;', $htmlSource);
        $this->assertContains('emospro.pageId', $htmlSource);
        $this->assertContains('emospro.siteid', $htmlSource);
        $this->assertContains('emospro.login = [["' . md5('birute_test@nfq.lt') . '"', $htmlSource);
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
    * checking if prices for variants in subshop can be saved. For bug#2570
    * @group admin
    * @group adminFunctionality
    */
    public function testProductVariantsInSubshopAllowCustomPrice()
    {
    }

    /**
    * checking if prices for variants in subshop can not be saved. For bug#2570
    * @group admin
    * @group adminFunctionality
    */
    public function testProductVariantsInSubshopNotAllowCustomPrice()
    {
    }

    /**
    * Customer must enable Facebook social plugins' should not affect frontend if all facebook functionality is disabled. For bug#3186
    * @group admin
    * @group adminFunctionality
    */
    public function testIsFacebookPluginEnabled()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings", "link=Facebook");
        $this->click("link=Facebook");
        $this->check("//input[@name='confbools[blFacebookConfirmEnabled]' and @value='true']");
        $this->type("confstrs[sFbAppId]", "129895460367329");
        $this->type(" confstrs[sFbSecretKey]", "1e5f848786f334becd745476c71eb6dd");
        $this->select("confstrs[blFbLikeEnabled]", "label=Enable");
        $this->clickAndWait("save");
        $this->openShop();
        $this->assertEquals('-1', $this->getEval('this.page().findElement("footerFbLike").innerHTML.search("<fb:like");'), "Facebook like should not be loaded at first visit in shop");
        $this->assertTrue($this->isElementPresent("//div[@id='footerFbLike']//a[text()='Enable']"));
        $this->assertTrue($this->isElementPresent("//div[@id='footerFbLike']//a[text()='?']"));
        $this->click("//div[@id='footerFbLike']//a[text()='Enable']");
        sleep(5);
        $this->assertEquals('1', $this->getEval('this.page().findElement("footerFbLike").innerHTML.search("<fb:like");'), "Facebook like should be loaded after enabling it");
        $this->assertFalse($this->isElementPresent("//div[@id='footerFbLike']//a[text()='Enable']"));
        $this->assertFalse($this->isElementPresent("//div[@id='footerFbLike']//a[text()='?']"));
    }


   /**
    * Testing downloadable product in admin ant frontend
    * @group admin
    * @group adminFunctionality
    */
    public function testDownloadableFiles()
    {
            if (!isSUBSHOP) {
            // Enable downloadable files
            $this->loginAdmin("Master Settings", "Core Settings");
            $this->openTab("link=Settings");
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
            $this->loginAdmin("Administer Products", "Products");
            $this->type("where[oxarticles][oxartnum]", "1002");
            $this->clickAndWait("submitit");
            $this->clickAndWaitFrame("link=1002", "edit");

            $this->Frame("list");
            $this->openTab("link=Downloads");
            $this->check("//input[@name='editval[oxarticles__oxisdownloadable]' and @value='1']");
            $this->clickAndWait("save");

            // Make purchase complete
            $this->openShop();
            $this->loginInFrontend("birute_test@nfq.lt", "useruser");
            $this->searchFor("1002");
            $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("searchList_1"));
            $this->selectVariant("variantselector_searchList_1", 1, "var1 [EN] šÄßüл");
            $this->type("amountToBasket", "10");
            $this->clickAndWait("toBasket");
            $this->openBasket();
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->clickAndWait("//button[text()='Continue to Next Step']");
            $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
            $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");

            $this->assertEquals("You are here: / Order Completed", $this->getText("breadCrumb"));

            //Check if file appears in My Downloads
            $this->click("servicesTrigger");
            $this->waitForItemAppear("services");
            $this->clickAndWAit("//ul[@id='services']/li[7]/a");

            $this->assertTrue($this->isTextPresent("Payment of the order is not yet complete."));


            //Make order complete
            $this->loginAdmin("Administer Orders", "Orders");
            $this->clickAndWaitFrame("link=12", "edit");
            $this->openTab("link=Downloads");
            $this->assertEquals("1002-1", $this->getText("//div[2]/table/tbody/tr[2]/td[1]"));
            $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//div[2]/table/tbody/tr[2]/td[2]"));
            $this->assertEquals("testFile3", $this->getText("//div[2]/table/tbody/tr[2]/td[3]"));
            $this->assertEquals("0000-00-00 00:00:00", $this->getText("//div[2]/table/tbody/tr[2]/td[4]"));
            $this->assertEquals("0000-00-00 00:00:00", $this->getText("//div[2]/table/tbody/tr[2]/td[5]"));
            $this->assertEquals("0", $this->getText("//div[2]/table/tbody/tr[2]/td[6]"));
            $this->assertEquals("20", $this->getText("//div[2]/table/tbody/tr[2]/td[7]"));
            $this->assertEquals("0", $this->getText("//div[2]/table/tbody/tr[2]/td[9]"));
            $this->frame("list");
            $this->openTab("link=Main");
            $this->click("link=Current Date");
            $this->clickAndWait("saveFormButton");
            //Check if file appears in My Downloads
            $this->openShop();
            $this->loginInFrontend("birute_test@nfq.lt", "useruser");
            $this->click("servicesTrigger");
            $this->waitForItemAppear("services");
            $this->clickAndWAit("//ul[@id='services']/li[7]/a");
            $this->assertFalse($this->isTextPresent("Payment of the order is not yet complete."));
            $this->click("link=testFile3");
            $this->click("link=testFile3");
            $this->click("link=testFile3");
            $this->loginAdmin("Administer Orders", "Orders");
            $this->clickAndWaitFrame("link=12", "edit");
            $this->openTab("link=Downloads");
            $this->assertEquals("1002-1", $this->getText("//div[2]/table/tbody/tr[2]/td[1]"));
            $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//div[2]/table/tbody/tr[2]/td[2]"));
            $this->assertEquals("testFile3", $this->getText("//div[2]/table/tbody/tr[2]/td[3]"));
            $this->assertEquals("20", $this->getText("//div[2]/table/tbody/tr[2]/td[7]"));
            $this->assertEquals("0", $this->getText("//div[2]/table/tbody/tr[2]/td[9]"));

}
    }

    /**
     * Testing administration for  GUI modules
    * @group admin
    * @group adminFunctionality
    */
        public function testGuiModulesAdministartion()
{
      if (!isSUBSHOP) {



              $this->executeSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES('testisModulesConfigId', 'oxbaseshop', 'module:test1', 'sTestInvoicePdfOption', 'str', 0x9382574762414966e5)");
              $this->executeSql("INSERT INTO `oxconfigdisplay` (`OXID`, `OXCFGMODULE`, `OXCFGVARNAME`, `OXGROUPING`, `OXVARCONSTRAINT`, `OXPOS`) VALUES('testisModulesConfigDisplayId', 'module:test1', 'sTestInvoicePdfOption', 'features', '', 1)");


            $this->loginAdmin("Extensions", "Modules");
            $this->clickAndWait("link=Test module #1");
            $this->frame("edit");
            $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
            $this->loginAdmin("Extensions", "Modules");
            $this->clickAndWait("link=Test module #2");
            $this->frame("edit");
            $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
            $this->loginAdmin("Extensions", "Modules");
            $this->clickAndWait("link=test3");
            $this->frame("edit");
            $this->type("moduleName", "test3");
            $this->type("aExtendedClasses", "info=>test3/view/myinfo3");
            $this->clickAndWait("//form[@id='myedit2']//input[@value='Save']");
            $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");

            $this->loginAdmin("Extensions", "Modules");
            $this->clickAndWait("link=Test module #1");
            $this->frame("list");
            $this->openTab("link=Settings","link=Features");
            $this->click("link=Features");
            $this->clearString("confstrs[sTestInvoicePdfOption]");
            $this->type("confstrs[sTestInvoicePdfOption]", "text1");
            $this->clickAndWait("save");
            $this->frame("list");
            $this->openTab("link=Settings","link=Features");
            $this->click("link=Features");
            $this->assertTrue($this->isElementPresent("confstrs[sTestInvoicePdfOption]"));

            $this->loginAdmin("Extensions", "Modules");
            $this->clickAndWait("link=Test module #1");
            $this->clickAndWaitFrame("link=Test module #1", "edit");
            $this->frame("list");
            $this->openTab("link=Installed Shop Modules");


        }




}


    /**
    * Testing staging mode and demo mode license functionality
    * login with admin:admin, orange banners and info in html source code
    * @group admin
    * @group adminFunctionality
    */
        public function testStagingDemoModes()
{
      if (!isSUBSHOP) {

            // skip CE edition as it has no license
        }
}




}




