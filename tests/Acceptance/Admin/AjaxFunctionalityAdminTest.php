<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** Ajax functionality */
class AjaxFunctionalityAdminTest extends AdminTestCase
{
    /**
     * ajax: Distributors -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxDistributorAssignProducts()
    {
        $this->loginAdmin("Master Settings", "Distributors");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 DE distributor šÄßüл");

        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->close();
    }

    /**
     * ajax: Manufacturers -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxManufacturerAssignProducts()
    {
        $this->loginAdmin("Master Settings", "Brands/Manufacturers");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 DE manufacturer šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->close();
    }

    /**
     * ajax: Payment Methods -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxPaymentMethodsAssignGroups()
    {
        $testConfig = $this->getTestConfig();
        if (!$testConfig->isSubShop()) {
            $this->loginAdmin("Shop Settings", "Payment Methods");
            $this->assertEquals("English", $this->getSelectedLabel("changelang"));
            $this->changeAdminListLanguage('Deutsch');
            $this->clickAndWait("link=Name");
            $this->openListItem("link=1 DE test payment šÄßüл");
            $this->click("//input[@value='Assign User Groups']");
            $this->usePopUp();
            $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
            //assignAll btn
            $this->click("container1_btn");
            $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
            //drag and drop 1 itm from one list to another
            $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
            $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
            $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
            $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
            $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
            //unassignAll btn
            $this->click("container2_btn");
            $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
            $this->close();
        }
    }

    /**
     * ajax: Payment Methods -> Assign Countries
     *
     * @group ajax
     */
    public function testAjaxPaymentMethodsAssignCountries()
    {
        $testConfig = $this->getTestConfig();
        if (!$testConfig->isSubShop()) {
            $this->loginAdmin("Shop Settings", "Payment Methods");
            $this->changeAdminListLanguage('Deutsch');
            $this->changeListSorting("link=Name");
            $this->openListItem("link=1 DE test payment šÄßüл", "edit");
            $this->openTab("Country");
            $this->click("//input[@value='Assign Countries']");
            $this->usePopUp();
            $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
            $this->assertTextPresent("Österreich");
            $this->assertElementText("Österreich", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
            //assignAll btn
            $this->click("container1_btn");
            $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
            //drag and drop 1 itm from one list to another
            $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
            $this->assertElementText("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]");
            $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
            $this->assertElementText("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
            $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
            //unassignAll btn
            $this->click("container2_btn");
            $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
            $this->close();
        }
    }

    /**
     * ajax: Discounts -> Assign Countries
     *
     * @group ajax
     */
    public function testAjaxDiscountsAssignCountries()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->openListItem("link=1 DE test discount šÄßüл");
        $this->click("//input[@value='Assign Countries']");
        $this->usePopUp();
        $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
        $this->assertTextPresent("Österreich");
        $this->assertElementText("Österreich", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
        $this->close();
    }

    /**
     * ajax: Discounts -> Assign Categories
     *
     * @group ajax
     */
    public function testAjaxDiscountsAssignCategories()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=2 DE test discount šÄßüл", "edit");
        $this->openTab("Products");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*category šÄßüл");
        $this->keyUp("_0", "y");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->close();
    }


    /**
     * Discounts -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxDiscountsAssignProducts()
    {
        $this->callShopSC("oxConfig", null, null, array("blVariantParentBuyable" => array("type" => "bool", "value" => 'true')));

        $this->loginAdmin("Shop Settings", "Discounts");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test discount šÄßüл", "edit");
        $this->openTab("Products");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();

        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[DE 4] Test product 0 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[4]");
        $this->assertElementText("", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]");
        $this->click("//li[@id='yui-gen12']/a"); //adds price field
        $this->assertElementText("50", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]");
        $this->click("//li[@id='yui-gen13']/a"); //adds stock field
        $this->assertElementText("15", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[6]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("10010", "//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]");
        //selecting category in dropdown list
        $this->select("artcat", "label=- Test category 1 [DE] šÄßüл");
        $this->assertElementText("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1003", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]");
        //drag and drop 1 item from one list to another and back
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]", "container2");
        $this->assertElementText("1003", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->assertElementText("1003", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->select("artcat", "label=--");
        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //search field
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[9]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");

        //sorting
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[DE 1] Test product 1 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("10010", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("[last] DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[2]"); //sorting by title
        $this->assertElementText("10 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("1.8", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]");
        $this->assertElementText("11 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertElementText("2", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[5]");
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[5]"); //sorting by price
        $this->assertElementText("1.5", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]");
        $this->assertElementText("10010", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1.6", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[5]");
        $this->assertElementText("10014", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[1]"); //sorting by art.no.
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[DE 1] Test product 1 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("10010", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("[last] DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        //searching by several fields
        $this->type("_1", "*DE product šÄßüл"); //searchy by title
        $this->keyUp("_1", "t");
        $this->assertElementText("10 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertTextNotPresent("[DE 1] Test product 1 šÄßüл");
        $this->assertElementText("[last] DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("10 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertElementText("11 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[3]/td[2]");
        $this->type("_4", "2"); //searchy by price
        $this->keyUp("_4", "2");
        $this->assertElementText("14 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertElementText("11 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[3]");
        $this->type("_5", "1"); //searchy by stock.
        $this->keyUp("_5", "1");
        $this->assertElementText("14 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]");
        $this->type("_5", "5"); //searchy by stock. nothing is found
        $this->keyUp("_5", "5");
        sleep(1);
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]");
        $this->close();
    }

    /**
     * Discounts -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxDiscountsAssignAllProducts()
    {
        $this->callShopSC("oxConfig", null, null, array("blVariantParentBuyable" => array("type" => "bool", "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("blVariantsSelection" => array("type" => "bool", "value" => 'true')));

        $this->loginAdmin("Shop Settings", "Discounts");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test discount šÄßüл", "edit");
        $this->openTab("Products");
        $this->click("//input[@value='Assign Products']");

        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");

        //'assign all' and 'unassign all' buttons testing
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
    }

    /**
     * Discounts -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxDiscountsAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=3 DE test discount šÄßüл", "edit");
        $this->openTab("Users");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * Discounts -> Assign Users
     *
     * @group ajax
     */
    public function testAjaxDiscountsAssignUsers()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=3 DE test discount šÄßüл", "edit");
        $this->openTab("Users");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "exa");
        $this->keyUp("_0", "r");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //drop down list for groups
        $this->assertTextPresent("Preis A");
        $this->assertTextPresent("1 user Group šÄßüл");
        $this->select("artcat", "label=Preis A");
        $this->assertElementText("example0a@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->close();
    }

    /**
     * ajax: S&H Sets -> Assign Shipping Costs
     *
     * @group ajax
     */
    public function testAjaxDeliverySetsAssignDeliveryCosts()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->openListItem("link=2 DE test S&H set šÄßüл");
        $this->click("//input[@value='Assign Shipping Cost Rules']");
        $this->usePopUp();
        //filter
        $this->type("_0", "*H šÄßüл");
        $this->keyUp("_0", "S");
        $this->assertElementText("[last] DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("1 DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]", "container1");
        $this->assertElementText("[last] DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("3 DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("[last] DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("1 DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[4]/td[1]");
        $this->close();
    }

    /**
     * ajax: S&H Sets -> Assign Countries
     *
     * @group ajax
     */
    public function testAjaxDeliverySetsAssignCountries()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->openListItem("link=1 DE test S&H set šÄßüл");
        $this->click("//input[@value='Assign Countries']");
        $this->usePopUp();
        $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
        $this->assertTextPresent("Österreich");
        $this->assertElementText("Österreich", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
        $this->close();
    }


    /**
     * ajax: Shipping Methods -> Assign Payment Methods
     *
     * @group ajax
     */
    public function testAjaxDeliverySetsAssignPayment()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test S&H set šÄßüл", "edit");
        $this->openTab("Payment");
        $this->click("//input[@value='Assign Payment Methods']");
        $this->usePopUp();
        //filter
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "t");
        $this->assertElementText("[last] DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("1 DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]", "container1");
        $this->assertElementText("4 DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("4 DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("1 DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->close();
    }

    /**
     * ajax: Shipping Methods -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxDeliverySetsAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test S&H set šÄßüл", "edit");
        $this->openTab("Users");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * Shipping Methods -> Assign Users
     *
     * @group ajax
     */
    public function testAjaxDeliverySetsAssignUsers()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->changeAdminListLanguage("Deutsch");
        $this->changeListSorting("link=Name");
        $this->openListItem("link=1 DE test S&H set šÄßüл");
        $this->openTab("Users");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "exa");
        $this->keyUp("_0", "r");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //drop down list for groups
        $this->assertTextPresent("Preis A");
        $this->assertTextPresent("1 user Group šÄßüл");
        $this->select("artcat", "label=Preis A");
        $this->assertElementText("example0a@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->close();
    }


    /**
     * ajax: Shipping Cost Rules -> Assign Countries
     *
     * @gup ajax
     */
    public function testAjaxDeliveryAssignCountries()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->openListItem("link=1 DE S&H šÄßüл");
        $this->click("//input[@value='Assign Countries']");
        $this->usePopUp();
        $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
        $this->assertTextPresent("Österreich");
        $this->assertElementText("Österreich", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Spanien", "//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]");
        $this->close();
    }

    /**
     * ajax: Shipping Cost Rules -> Assign Categories
     *
     * @group ajax
     */
    public function testAjaxDeliveryAssignCategories()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("Products");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->close();
    }

    /**
     * ajax: Shipping Cost Rules -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxDeliveryAssignProducts()
    {
        // active config option blVariantsSelection
        $this->callShopSC("oxConfig", null, null, array("blVariantsSelection" => array("type" => "bool", "value" => 'true')));
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("Products");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[19]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->close();
    }

    /**
     * ajax: Shipping Cost Rules -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxDeliveryAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("Users");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * Shipping Cost Rules -> Assign Users
     *
     * @group ajax
     */
    public function testAjaxDeliveryAssignUsers()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("Users");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "exa");
        $this->keyUp("_0", "r");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        //drop down list for groups
        $this->assertTextPresent("Preis A");
        $this->assertTextPresent("1 user Group šÄßüл");
        $this->select("artcat", "label=Preis A");
        $this->assertElementText("example0a@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->close();
    }

    /**
     * ajax: Coupons -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxCouponsAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 Coupon šÄßüл", "edit");
        $this->openTab("User Groups & Products");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * ajax: Coupons -> Assign Categories
     *
     * @group ajax
     */
    public function testAjaxCouponsAssignCategories()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->openListItem("3 Coupon šÄßüл", '[oxvoucherseries][oxserienr]');
        $this->openTab("User Groups & Products");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->assertElementText("[last] [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("1 [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->assertElementText("5 [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("5 [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("1 [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->close();
    }

    /**
     * ajax: Coupons -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxCouponsAssignProducts()
    {
        //active config option blVariantsSelection
        $this->callShopSC("oxConfig", null, null, array("blVariantsSelection" => array("type" => "bool", "value" => 'true')));
        $this->callShopSC("oxConfig", null, null, array("blVariantParentBuyable" => array("type" => "bool", "value" => 'true')));
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=2 Coupon šÄßüл", "edit");
        $this->openTab("User Groups & Products");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->assertElementText("1003", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("10 EN product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("1003", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[22]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[19]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->close();
    }

    /**
     * ajax: Products -> Assign Categories
     *
     * @group ajax
     */
    public function testAjaxProductsAssignCategories()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10011", "edit");
        $this->openTab("Extended");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[9]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        $this->close();
    }

    /**
     * ajax: Products -> Assign bundled product
     *
     * @group ajax
     */
    public function testAjaxProductsAssignBundledProduct()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10011", "edit");
        $this->openTab("Extended");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[DE 4] Test product 0 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        //sorting desc by art.nr
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[1]"); //sorting by title
        $this->assertElementText("4001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->type("_1", "*šÄßüл");
        $this->keyUp("_1", "y");
        $this->assertElementText("[DE 4] Test product 0 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[2]");
        $this->assertElementText("[DE 3] Test product 3 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        //assign product
        $this->assertElementText('', "bundle_artnum");
        $this->assertElementText('', "bundle_title");
        $this->click("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->checkForErrors();
        $this->assertElementText('', "bundle_artnum");
        $this->assertElementText('', "bundle_title");
        $this->click("saveBtn");
        $this->assertElementText('1003', 'bundle_artnum');
        $this->assertElementText('[DE 3] Test product 3 šÄßüл', 'bundle_title');
        $this->click("//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        $this->checkForErrors();
        $this->assertElementText('1003', 'bundle_artnum');
        $this->assertElementText('[DE 3] Test product 3 šÄßüл', 'bundle_title');
        $this->click("saveBtn");
        $this->assertElementText('1000', 'bundle_artnum');
        $this->assertElementText('[DE 4] Test product 0 šÄßüл', 'bundle_title');
        //checking if it was correctly saved #1844
        $this->close();
        $this->selectWindow(null);
        $this->frame("edit");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        //bundled product was assigned, so its name should be displayed under ajax list
        $this->waitForText("1000 [DE 4] Test product 0 šÄßüл");
        //unassign
        $this->click("remBtn");
        $this->assertElementText('', 'bundle_artnum');
        $this->assertElementText('', 'bundle_title');
        //checkin if it was saved correctly #1844
        $this->close();
        $this->selectWindow(null);
        $this->frame("edit");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->assertElementText('', 'bundle_artnum');
        $this->assertElementText('', 'bundle_title');
        $this->close();
    }

    /**
     * ajax: Products -> Assign Attributes
     *
     * @group ajax
     */
    public function testAjaxProductsAssignAttributes()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10011", "edit");
        $this->openTab("Selection");
        $this->click("//input[@value='Assign Attributes']");
        $this->usePopUp();
        $this->type("_0", "*[DE] attribute šÄßüл");
        $this->keyUp("_0", "a");
        $this->assertElementText("[last] [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //adding value to assigned attribute
        $this->assertFalse($this->isVisible("attr_value"));
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->waitForItemAppear("attr_value");
        $this->type("attr_value", "#attribute value");
        $this->click("saveBtn");
        $this->waitForItemDisappear("attr_value");
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->waitForItemAppear("attr_value");
        $this->assertEquals("#attribute value", $this->getValue("attr_value"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->assertElementText("5 [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("5 [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //checking if removed and added attribute lost its entered value
        $this->assertFalse($this->isVisible("attr_value"));
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->waitForItemAppear("attr_value");
        $this->assertEquals("", $this->getValue("attr_value"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->close();
    }

    /**
     * ajax: Products -> Assign Selection lists
     *
     * @group ajax
     */
    public function testAjaxProductsAssignSelectionLists()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWait("nav.page.3");
        $this->clickAndWaitFrame("link=1210", "edit");
        $this->openTab("Selection");
        $this->click("//input[@value='Assign Selection Lists']");
        $this->usePopUp();
        $this->type("_0", "*sellist šÄßüл");
        $this->keyUp("_0", "t");
        $this->assertElementText("[last] [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->assertElementText("5 [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[9]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("5 [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        $this->close();
    }

    /**
     * ajax: Products -> Assign Crossselling
     *
     * @group ajax
     */
    public function testAjaxProductsAssignCrossselling()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWait("nav.page.3");
        $this->clickAndWaitFrame("link=1301", "edit");
        $this->openTab("Crosssell.");
        $this->click("//input[@value='Assign Crosssellings']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->close();
    }

    /**
     * ajax: Products -> Assign Accessories and sort Accessories
     *
     * @group ajax
     */
    public function testAjaxProductsAssignAndSortAccessories()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10013", "edit");
        $this->openTab("Crosssell.");
        $this->click("//input[@value='Assign Accessories']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[7]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[7]/td");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[7]/td[2]");

        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");

        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[6]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");

        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[7]/td[1]");

        // check sort
        $this->click("container1_btn");
        $this->assertElementText("0", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("0", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[3]");
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->assertElementText("0", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("6", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[3]");
        $firstRow = $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $secondRow = $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->assertElementText($secondRow, "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText($firstRow, "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("0", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("1", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[3]");
        $this->close();
    }

    /**
     * ajax: Attributes -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxAttributesAssignProducts()
    {
        // turn on config blVariantsSelection
        $this->callShopSC("oxConfig", null, null, array("blVariantsSelection" => array("type" => "bool", "value" => 'true')));
        $this->loginAdmin("Administer Products", "Attributes");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->openListItem("link=1 [DE] Attribute šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[19]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->close();
    }

    /**
     * ajax: Attributes -> Assign Category
     *
     * @group ajax
     */
    public function testAjaxAttributesAssignCategory()
    {
        $this->loginAdmin("Administer Products", "Attributes");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 [DE] Attribute šÄßüл", "edit");
        $this->openTab("Category");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*[DE] šÄßüл");
        $this->keyUp("_0", "t");
        $this->assertElementText("Test category 1 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("Test category 0 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[3]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("Test category 0 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test category 1 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        //sorting attributes
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test attribute 1 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("Test attribute 3 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("2", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertElementText("Test attribute 2 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("3", "//div[@id='container3_c']/table/tbody[2]/tr[3]/td[2]");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("4", "//div[@id='container3_c']/table/tbody[2]/tr[4]/td[2]");
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->assertElementText("Test attribute 3 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test attribute 1 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("Test attribute 2 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[4]/td[1]");
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->assertElementText("Test attribute 3 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("Test attribute 1 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test attribute 3 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("Test attribute 2 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 [DE] Attribute šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[4]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->assertElementText("Test category 0 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test category 1 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("Test category 0 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test category 1 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("Test category 0 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test category 1 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->close();
    }

    /**
     * ajax: Categories -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxCategoriesAssignProducts()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 [DE] category šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10012", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]", "container2");
        //when assigning products to category, all products are allways displayed in first list. (since dodger performance for first EE releze)
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->close();
    }

    /**
     * ajax: Categories -> Assign Products (assigned products with variantas)
     *
     * @group ajax
     */
    public function testAjaxCategoriesAssignProductsWithVariants()
    {
        $sShopNr = $this->getShopVersionNumber();
        //variants will be shown in ajax lists
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("System");
        $this->click("//form[@id='myedit']/div[2]/div/a/b");
        $this->waitForItemAppear("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->check("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->clickAndWait("save");

        $this->selectMenu("Administer Products", "Categories");
        $this->openListItem("link=1 [EN] category šÄßüл");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->type("_0", "1002");
        $this->keyUp("_0", "2");
        $this->assertElementText("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1002-1", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("1002-2", "//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[4]/td[1]");

        //drag and drop product and its variant from one list to another
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("Test product 2 [EN] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]", "container2");
        $this->assertElementText("Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[3]");
        $this->close();

        //variants wont be shown in ajax lists
        $this->selectMenu("Master Settings", "Core Settings");
        $this->openTab("System");
        $this->click("//form[@id='myedit']/div[2]/div/a/b");
        $this->waitForItemAppear("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->uncheck("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->clickAndWait("save");

        $this->selectMenu("Administer Products", "Categories");
        $this->openListItem("link=1 [EN] category šÄßüл");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->assertElementText("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        //unassigning this product from category
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        sleep(1);
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]");
        $this->close();

        //selecting other category for assigning products
        $this->selectWindow(null);
        $this->frame("list");
        $this->openListItem("link=2 [EN] category šÄßüл");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->type("_0", "1002");
        $this->keyUp("_0", "2");
        $this->assertElementText("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");

        //drag and drop product and its variant from one list to another
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->close();

        //variants will be shown in ajax lists again
        $this->selectMenu("Master Settings", "Core Settings");
        $this->openTab("System");
        $this->click("//form[@id='myedit']/div[2]/div/a/b");
        $this->waitForItemAppear("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->check("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->clickAndWait("save");

        //checking if variants were assigned and unassigned correctly
        $this->selectMenu("Administer Products", "Categories");
        $this->openListItem("link=1 [EN] category šÄßüл");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]");
        $this->close();

        $this->selectWindow(null);
        $this->frame("list");
        $this->openListItem("link=2 [EN] category šÄßüл");
        $this->click("assignArticle");
        $this->usePopUp();
        $this->assertElementText("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]");
        $this->close();
    }

    /**
     * ajax: Categories -> Sorting categories products
     *
     * @group ajax
     */
    public function testAjaxCategoriesSortingProducts()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->changeAdminListLanguage("Deutsch");
        $this->clickAndWait("link=Sorting");
        $this->openListItem("link=Test category 0 [DE] šÄßüл");
        $this->openTab("Sorting");
        $this->click("//input[@value='Sort Categories']");
        $this->usePopUp();
        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("0", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("[DE 1] Test product 1 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertElementText("0", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[3]");
        //sorting
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]", "container2");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("1000", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]", "container1");
        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[3]"); //sorting by position
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("1000", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->click("saveBtn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("0", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("1", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[3]");
        //deleting sorting
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[1]"); //sorting by art no
        $this->assertElementText("1", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->click("deleteBtn");
        $this->assertElementText("0", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("0", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("0", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[3]");
        $this->close();
    }

    /**
     * ajax: Selection Lists -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxSelectionListsAssignProducts()
    {
        $this->loginAdmin("Administer Products", "Selection Lists");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 [DE] sellist šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertElementText("15 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
        //sorting selection lists for product
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("test selection list [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("0", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("1", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]");
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("0", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("test selection list [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("1", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]");
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->assertElementText("test selection list [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("test selection list [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("0", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertElementText("1 [DE] sellist šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("1", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]");
    }

    /**
     * ajax: Selection Lists -> Assign Products
     *
     * @group ajax
     */
    public function testAjaxSelectionListsAssignAllProducts()
    {
        //active config option blVariantsSelection
        $this->callShopSC("oxConfig", null, null, array("blVariantsSelection" => array("type" => "bool", "value" => 'true')));
        $this->loginAdmin("Administer Products", "Selection Lists");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 [DE] sellist šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        // Browser sees elements on both containers. Need some wait time to continue test execution.
        $this->assertElementText("", "//div[@id='container1_c']/table/tbody[2]", "Error: elements were not moved to other container.");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementNotPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementNotPresent("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * ajax: Users -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxUsersAssignGroups()
    {
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Name");
        $this->openListItem("link=2UserSurnamešÄßüл 2useršÄßüл");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassigne all buttons
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * ajax: Groups -> Assign Users
     *
     * @group ajax
     */
    public function testAjaxGroupsAssignUsers()
    {
        $this->loginAdmin("Administer Users", "User Groups");
        $this->clickAndWait("link=Name");
        $this->openListItem("link=1 user Group šÄßüл");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "exa");
        $this->keyUp("_0", "r");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example03@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("example02@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("example_test@oxid-esales.dev", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->close();
    }

    /**
     * ajax: News -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxNewsAssignGroups()
    {
        $this->loginAdmin("Customer Info", "News");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=4 [DE] Test news šÄßüл");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * ajax: Promotions -> Assign Products.
     *
     * @group ajax
     */
    public function testAjaxPromotionsAssignProducts()
    {
        //active config option blVariantsSelection
        $this->callShopSC("oxConfig", null, null, array("blVariantsSelection" => array("type" => "bool", "value" => 'true')));
        $this->loginAdmin("Customer Info", "Promotions");
        $this->clickAndWait("link=Type");
        $this->openListItem("link=Week's Special");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[19]/td[1]");
        $this->assertElementText("10010", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[19]/td[1]");
        $this->assertElementText("10 EN product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[19]/td[2]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("10010", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //sorting selection lists for product
        $this->assertElementText("10010", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("10012", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("10010", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("10012", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->assertElementText("10012", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("10010", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]", "container1");
        $this->assertElementText("10010", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10012", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("10013", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("10010", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        $this->assertElementText("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10012", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("10011", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertElementText("10013", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertElementText("10016", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("10010", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertElementText("10 EN product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]");
        $this->close();
    }

    /**
     * ajax: Promotions -> Assign Groups
     *
     * @group ajax
     */
    public function testAjaxPromotionsAssignGroups()
    {
        $this->loginAdmin("Customer Info", "Promotions");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->changeListSorting("link=Name");
        $this->assertElementPresent("link=Current Promotion");
        $this->openListItem("link=Current Promotion");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        //unassignAll btn
        $this->click("container2_btn");
        $this->assertElementText("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertElementText("[last] user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]");
        $this->close();
    }

    /**
     * Test case for bugfix 0006711 and 0006668
     *
     * Activates module with ajax functionality.
     * Checks that ajax call succeed.
     */
    public function testOxAjaxContainerClassResolution()
    {
        $this->installModule('oxid/test11');

        $this->loginAdmin("Extensions", "Modules");

        $this->activateModule("Test module #11");

        $this->frame("list");
        $this->clickAndWait('link=test_11_tab');
        $this->frame("edit");
        $this->clickAndWait("//input[@value='CLICK_HERE']");

        $this->selectWindow("ajaxpopup");
        $this->assertTextPresent('POPUP_HERE');
        $this->close();

        $this->frame("list");
        $this->clickAndWait('link=test_11_tab');
        $this->frame("edit");
        $this->clickAndWait("//input[@value='CLICK_HERE']");

        $this->selectWindow("ajaxpopup");
        $this->assertTextPresent('test_11_ajax_controller successfully called');
        $this->close();
    }

    /**
     * Test case for bugfix 0006711 and 0006668
     *
     * Activates module with ajax functionality.
     * Checks that ajax call succeed.
     */
    public function testOxAjaxContainerClassResolutionMetadata1Module()
    {
        $this->installModule('oxid/test12');
        $this->loginAdmin("Extensions", "Modules");

        $this->activateModule("Test module #12");

        $this->frame("list");
        $this->clickAndWait('link=test_12_tab');
        $this->frame("edit");
        $this->clickAndWait("//input[@value='CLICK_HERE']");

        $this->selectWindow("ajaxpopup");
        $this->assertTextPresent('POPUP_HERE');
        $this->close();

        $this->frame("list");
        $this->clickAndWait('link=test_12_tab');
        $this->frame("edit");
        $this->clickAndWait("//input[@value='CLICK_HERE']");

        $this->selectWindow("ajaxpopup");
        $this->assertTextPresent('test_12_ajax_controller successfully called');
        $this->close();
    }

    private function activateModule($moduleName)
    {
        $this->clickAndWait("link={$moduleName}");
        $this->frame("edit");
        $this->clickAndWait("//form[@id='myedit']//input[@value='Activate']");
        $this->assertElementPresent("//form[@id='myedit']//input[@value='Deactivate']");
    }

    private function installModule(string $path)
    {
        $moduleConfigurationInstaller = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleConfigurationInstallerInterface::class);

        $moduleConfigurationInstaller->install(
            __DIR__ . '/testData/modules/' . $path,
            $path
        );
    }
}
