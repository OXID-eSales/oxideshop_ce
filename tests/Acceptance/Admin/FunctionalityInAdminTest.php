<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use oxDb;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** Admin interface functionality. */
class FunctionalityInAdminTest extends AdminTestCase
{
    /** @var string To store translation error value. */
    private $translationError = '';

    /**
     * Restore translation error value as some case might change it.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->errorsInPage["ERROR: Tran"] = $this->translationError;
    }

    /**
     * Skip check for error when translation does not exist.
     * Some translations might be missing as tests add new language.
     * In these cases no need to check if everything is translated.
     */
    private function skipTranslationCheck(): void
    {
        $this->translationError = !empty($this->errorsInPage["ERROR: Tran"])
            ? $this->errorsInPage["ERROR: Tran"]
            : $this->translationError;
        unset($this->errorsInPage["ERROR: Tran"]);
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
     * TODO: revisit test after bug 0004624 is fixed
     *
     * @group adminFunctionality
     * @group quarantine
     */
    public function testEditingOrdersProducts()
    {
        $this->updateSubshopOrders();

        $this->executeSql("UPDATE `oxorder` SET `OXFOLDER` = 'ORDERFOLDER_FINISHED' WHERE `OXID` = 'testorder7'");
        $this->loginAdmin("Administer Orders", "Orders");
        $this->selectAndWait("folder", "label=Finished");
        $this->clickAndWaitFrame("link=1", "edit");
        $this->openTab("Main");
        $this->type("editval[oxorder__oxdelcost]", "10");
        $this->select("setDelSet", "label=Example Set1: UPS 48 hours");
        $this->clickAndWait("saveFormButton");
        $this->openTab("Main");
        $this->assertEquals("----", $this->getSelectedLabel("setPayment"));
        $this->assertEquals("Example Set1: UPS 48 hours", $this->getSelectedLabel("setDelSet"));
        $this->select("setDelSet", "label=Standard");
        $this->clickAndWait("saveFormButton");
        $this->waitForElement("setPayment");
        $this->openTab("Main");
        $this->assertEquals("Standard", $this->getSelectedLabel("setDelSet"));
        $this->assertEquals("----", $this->getSelectedLabel("setPayment"));
        $this->select("setPayment", "label=COD (Cash on Delivery)");
        $this->clickAndWait("saveFormButton");
        $this->openTab("Main");
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
     * @group quarantine
     */
    public function testProductExport()
    {
        if ($this->getTestConfig()->isSubShop()) {
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
        $shopUrl = $this->getTestConfig()->getShopUrl();

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
        $this->assertElementPresent("usr");

        $this->loginAdmin(null, null, false, "admin@myoxideshop.com", "admin0303", "Deutsch");
        $this->waitForText("Willkommen im OXID eShop Administrationsbereich");
        $this->checkForErrors();
        $this->frame("navigation");
        $this->checkForErrors();
        $this->assertElementPresent("link=Stammdaten");
        $this->assertElementPresent("link=Shopeinstellungen");

        $this->getMinkSession()->restart();
        $this->openNewWindow($shopUrl . "admin");
        $this->clickAndWait("//input[@type='submit']");
        $url = $this->getLocation();

        $this->getMinkSession()->restart();
        $this->openNewWindow($shopUrl . "admin");
        $this->open($url);
        $this->type("usr", "admin@myoxideshop.com");
        $this->type("pwd", "admin0303");
        $this->select("lng", "English");
        $this->select("prf", "Standard");
        $this->clickAndWait("//input[@type='submit']");
        $this->assertElementPresent("//frame[@id='navigation']");

        $this->getMinkSession()->restart();
        $this->openNewWindow($shopUrl . "admin");
        $this->open($url);
        $this->assertElementNotPresent("//frame[@id='navigation']");
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
        $this->assertEquals("Close Non-productive eShop mode is intended", substr($this->clearString($this->getText("helpPanel")), 0, 43));
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
