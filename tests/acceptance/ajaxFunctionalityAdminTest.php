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

class Acceptance_ajaxFunctionalityAdminTest extends oxidAdditionalSeleniumFunctions
{
    /**
     * Enter description here...
     *
     * @param bool $skipDemoData
     *
     * @return null
     */
    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Ajax functionality ----------------------------------

    /**
     * ajax: Distributors -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDistributorAssignProducts()
    {
        $this->loginAdmin("Master Settings", "Distributors");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Title");
        $this->openTab("link=1 DE distributor šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Manufacturers -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxManufacturerAssignProducts()
    {
        $this->loginAdmin("Master Settings", "Brands/Manufacturers");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Title");
        $this->openTab("link=1 DE manufacturer šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Payment Methods -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxPaymentMethodsAssignGroups()
    {
            $this->loginAdmin("Shop Settings", "Payment Methods");
            $this->assertEquals("English", $this->getSelectedLabel("changelang"));
            $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
            $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
            $this->clickAndWait("link=Name");
            $this->openTab("link=1 DE test payment šÄßüл");
            $this->click("//input[@value='Assign User Groups']");
            $this->usePopUp();
            $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
            //assignAll btn
            $this->click("container1_btn");
            $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
            //drag and drop 1 itm from one list to another
            $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
            $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
            $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
            $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
            $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
            //unassignAll btn
            $this->click("container2_btn");
            $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
            $this->close();
    }

    /**
     * ajax: Payment Methods -> Assign Countries
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxPaymentMethodsAssignCountries()
    {
            $this->loginAdmin("Shop Settings", "Payment Methods");
            $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
            $this->clickAndWait("link=Name");
            $this->clickAndWaitFrame("link=1 DE test payment šÄßüл", "edit");
            $this->openTab("link=Country");
            $this->click("//input[@value='Assign Countries']");
            $this->usePopUp();
            $this->assertEquals("Belgien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
            $this->assertTrue($this->isTextPresent("Österreich"));
            $this->assertEquals("Österreich", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
            //assignAll btn
            $this->click("container1_btn");
            $this->waitForAjax("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
            //drag and drop 1 itm from one list to another
            $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
            $this->waitForAjax("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]"));
            $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
            $this->waitForAjax("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
            $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
            //unassignAll btn
            $this->click("container2_btn");
            $this->waitForAjax("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
            $this->close();
    }

    /**
     * ajax: Discounts -> Assign Countries
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDiscountsAssignCountries()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Name");
        $this->openTab("link=1 DE test discount šÄßüл");
        $this->click("//input[@value='Assign Countries']");
        $this->usePopUp();
        $this->assertEquals("Belgien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
        $this->assertTrue($this->isTextPresent("Österreich"));
        $this->assertEquals("Österreich", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Discounts -> Assign Categories
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDiscountsAssignCategories()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=2 DE test discount šÄßüл", "edit");
        $this->openTab("link=Products");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*category šÄßüл");
        $this->keyUp("_0", "y");
        $this->waitForAjax("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->close();
    }


    /**
     * Discounts -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDiscountsAssignProducts()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test discount šÄßüл", "edit", "link=Products");
        $this->openTab("link=Products", "//input[@value='Assign Products']");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();

        $this->assertEquals("1000", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[DE 4] Test product 0 šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]"));
        $this->assertEquals("", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[4]"));
        $this->assertEquals("", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]"));
        $this->click("//li[@id='yui-gen3']/a"); //adds price field
        $this->waitForAjax("50", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]");
        $this->click("//li[@id='yui-gen4']/a"); //adds stock field
        $this->waitForAjax("15", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[6]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("10010", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]"));
        //selecting category in dropdown list
        $this->select("artcat", "label=- Test category 1 [DE] šÄßüл");
        $this->waitForAjax("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1003", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]"));
        //drag and drop 1 item from one list to another and back
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]", "container2");
        $this->waitForAjax("1003", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1002", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->waitForAjax("1003", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertEquals("1002", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->select("artcat", "label=--");
        $this->waitForAjax("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        //search field
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[9]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));

        //sorting
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[DE 1] Test product 1 šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("10010", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[2]"); //sorting by title
        $this->waitForAjax("10 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertEquals("1.8", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]"));
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->assertEquals("2", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[5]"));
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[5]"); //sorting by price
        $this->waitForAjax("1.5", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[5]");
        $this->assertEquals("10010", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("1.6", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[5]"));
        $this->assertEquals("10014", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[1]"); //sorting by art.no.
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[DE 1] Test product 1 šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("10010", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]"));
        //searching by several fields
        $this->type("_1", "*DE product šÄßüл"); //searchy by title
        $this->keyUp("_1", "t");
        $this->waitForAjax("10 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertFalse($this->isTextPresent("[DE 1] Test product 1 šÄßüл"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("10 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[2]"));
        $this->type("_4", "2"); //searchy by price
        $this->keyUp("_4", "2");
        $this->waitForAjax("14 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[3]"));
        $this->type("_5", "1"); //searchy by stock.
        $this->keyUp("_5", "1");
        $this->waitForAjax("14 DE product šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]");
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]"));
        $this->type("_5", "5"); //searchy by stock. nothing is found
        $this->keyUp("_5", "5");
        sleep(1);
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]"));
        $this->close();
    }

    /**
     * Discounts -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDiscountsAssignAllProducts()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'blVariantsSelection'");
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test discount šÄßüл", "edit");
        $this->openTab("link=Products");
        $this->click("//input[@value='Assign Products']");

        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");

        //'assign all' and 'unassign all' buttons testing
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
    }

    /**
     * Discounts -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDiscountsAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");;
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=3 DE test discount šÄßüл", "edit");
        $this->openTab("link=Users");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * Discounts -> Assign Users
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDiscountsAssignUsers()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=3 DE test discount šÄßüл", "edit");
        $this->openTab("link=Users");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "bir");
        $this->keyUp("_0", "r");
        $this->waitForAjax("birute_test@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //drop down list for groups
        $this->assertTrue($this->isTextPresent("Preis A"));
        $this->assertTrue($this->isTextPresent("1 user Group šÄßüл"));
        $this->select("artcat", "label=Preis A");
        $this->waitForAjax("birute0a@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->close();
    }

    /**
     * ajax: S&H Sets -> Assign Shipping Costs
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliverySetsAssignDeliveryCosts()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Name");
        $this->openTab("link=2 DE test S&H set šÄßüл");
        $this->click("//input[@value='Assign Shipping Cost Rules']");
        $this->usePopUp();
        //filter
        $this->type("_0", "*H šÄßüл");
        $this->keyUp("_0", "S");
        $this->waitForAjax("[last] DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] DE S&H šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]", "container1");
        $this->waitForAjax("[last] DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("3 DE S&H šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("[last] DE S&H šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 DE S&H šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] DE S&H šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->close();
    }

    /**
     * ajax: S&H Sets -> Assign Countries
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliverySetsAssignCountries()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->openTab("link=1 DE test S&H set šÄßüл");
        $this->click("//input[@value='Assign Countries']");
        $this->usePopUp();
        $this->assertEquals("Belgien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
        $this->assertTrue($this->isTextPresent("Österreich"));
        $this->assertEquals("Österreich", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
        $this->close();
    }


    /**
     * ajax: Shipping Methods -> Assign Payment Methods
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliverySetsAssignPayment()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test S&H set šÄßüл", "edit");
        $this->openTab("link=Payment");
        $this->click("//input[@value='Assign Payment Methods']");
        $this->usePopUp();
        //filter
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "t");
        $this->waitForAjax("[last] DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("1 DE test payment šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] DE test payment šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]", "container1");
        $this->waitForAjax("4 DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 DE test payment šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] DE test payment šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("4 DE test payment šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->assertEquals("1 DE test payment šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] DE test payment šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 DE test payment šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] DE test payment šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Shipping Methods -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliverySetsAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test S&H set šÄßüл", "edit");
        $this->openTab("link=Users");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * Shipping Methods -> Assign Users
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliverySetsAssignUsers()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE test S&H set šÄßüл", "edit");
        $this->openTab("link=Users");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "bir");
        $this->keyUp("_0", "r");
        $this->waitForAjax("birute_test@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //drop down list for groups
        $this->assertTrue($this->isTextPresent("Preis A"));
        $this->assertTrue($this->isTextPresent("1 user Group šÄßüл"));
        $this->select("artcat", "label=Preis A");
        $this->waitForAjax("birute0a@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->close();
    }


    /**
     * ajax: Shipping Cost Rules -> Assign Countries
     *
     * @group admin
     * @gup ajax
     *
     * @return null
     */
    public function testAjaxDeliveryAssignCountries()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->openTab("link=1 DE S&H šÄßüл");
        $this->click("//input[@value='Assign Countries']");
        $this->usePopUp();
        $this->assertEquals("Belgien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
        $this->assertTrue($this->isTextPresent("Österreich"));
        $this->assertEquals("Österreich", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("Belgien", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("Finnland", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[14]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("Finnland", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("Belgien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Spanien", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[15]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("Belgien", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Spanien", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[15]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Shipping Cost Rules -> Assign Categories
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliveryAssignCategories()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("link=Products");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->waitForAjax("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Shipping Cost Rules -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliveryAssignProducts()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'blVariantsSelection'");
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("link=Products");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[19]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Shipping Cost Rules -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliveryAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit");
        $this->openTab("link=Users");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * Shipping Cost Rules -> Assign Users
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxDeliveryAssignUsers()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 DE S&H šÄßüл", "edit", "link=Users");
        $this->openTab("link=Users", "//input[@value='Assign Users']");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "bir");
        $this->keyUp("_0", "r");
        $this->waitForAjax("birute_test@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        //drop down list for groups
        $this->assertTrue($this->isTextPresent("Preis A"));
        $this->assertTrue($this->isTextPresent("1 user Group šÄßüл"));
        $this->select("artcat", "label=Preis A");
        $this->waitForAjax("birute0a@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Coupons -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxCouponsAssignGroups()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 Coupon šÄßüл", "edit");
        $this->openTab("link=User Groups & Products");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Coupons -> Assign Categories
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxCouponsAssignCategories()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->clickAndWait("link=Name", "link=3 Coupon šÄßüл");
        $this->clickAndWaitFrame("link=3 Coupon šÄßüл", "edit", "link=User Groups & Products");
        $this->openTab("link=User Groups & Products", "//input[@value='Assign Categories']");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->waitForAjax("[last] [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("1 [EN] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->waitForAjax("5 [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [EN] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("5 [EN] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("1 [EN] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 [EN] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        $this->close();

    }

    /**
     * ajax: Coupons -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxCouponsAssignProducts()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'blVariantsSelection'");
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=2 Coupon šÄßüл", "edit");
        $this->openTab("link=User Groups & Products");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->assertEquals("1003", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("10 EN product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("1003", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[22]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[19]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Products -> Assign Categories
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxProductsAssignCategories()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10011", "edit");
        $this->openTab("link=Extended");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*šÄßüл");
        $this->keyUp("_0", "y");
        $this->waitForAjax("[last] [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[9]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("5 [DE] category šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 [DE] category šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Products -> Assign bundled product
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxProductsAssignBundledProduct()
    {
            $this->loginAdmin("Administer Products", "Products");
            $this->assertEquals("English", $this->getSelectedLabel("changelang"));
            $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
            $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
            $this->clickAndWait("link=Prod.No.");
            $this->clickAndWaitFrame("link=10011", "edit");
            $this->openTab("link=Extended");
            $this->click("//input[@value='Assign Products']");
            $this->usePopUp();
            $this->assertEquals("1000", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
            $this->assertEquals("[DE 4] Test product 0 šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
            $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
            //sorting desc by art.nr
            $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[1]"); //sorting by title
            $this->waitForAjax("4001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->type("_1", "*šÄßüл");
            $this->keyUp("_1", "y");
            $this->waitForAjax("[DE 4] Test product 0 šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[2]");
            $this->assertEquals("[DE 3] Test product 3 šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[2]"));
            //assign product
            $this->assertFalse($this->isTextPresent("1003 [DE 3] Test product 3 šÄßüл"));
            $this->click("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
            $this->checkForErrors();
            $this->assertFalse($this->isTextPresent("1003 [DE 3] Test product 3 šÄßüл"));
            $this->click("saveBtn");
            $this->waitForText("1003 [DE 3] Test product 3 šÄßüл");
            $this->click("//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
            $this->checkForErrors();
            $this->assertTrue($this->isTextPresent("1003 [DE 3] Test product 3 šÄßüл"));
            $this->assertFalse($this->isTextPresent("1000 [DE 4] Test product 0 šÄßüл"));
            $this->click("saveBtn");
            $this->waitForText("1000 [DE 4] Test product 0 šÄßüл");
            $this->assertFalse($this->isTextPresent("1003 [DE 3] Test product 3 šÄßüл"));
            //checking if it was correctly saved #1844
            $this->close();
            $this->selectWindow(null);
            $this->frame("edit", "//input[@value='Assign Products']");
            $this->click("//input[@value='Assign Products']");
            $this->usePopUp();
            //bundled product was assigned, so its name should be displayed under ajax list
            $this->waitForText("1000 [DE 4] Test product 0 šÄßüл");
            //unassign
            $this->click("remBtn");
            sleep(1);
            $this->assertFalse($this->isTextPresent("1003 [DE 3] Test product 3 šÄßüл"));
            $this->assertFalse($this->isTextPresent("1000 [DE 4] Test product 0 šÄßüл"));
            //checkin if it was saved correctly #1844
            $this->close();
            $this->selectWindow(null);
            $this->frame("edit", "//input[@value='Assign Products']");
            $this->click("//input[@value='Assign Products']");
            $this->usePopUp();
            sleep(1);
            $this->assertFalse($this->isTextPresent("1003 [DE 3] Test product 3 šÄßüл"));
            $this->assertFalse($this->isTextPresent("1000 [DE 4] Test product 0 šÄßüл"));
            $this->close();
    }

    /**
     * ajax: Products -> Assign Attributes
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxProductsAssignAttributes()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10011", "edit");
        $this->openTab("link=Selection");
        $this->click("//input[@value='Assign Attributes']");
        $this->usePopUp();
        $this->type("_0", "*[DE] attribute šÄßüл");
        $this->keyUp("_0", "a");
        $this->waitForAjax("[last] [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] Attribute šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
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
        $this->waitForAjax("5 [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] Attribute šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("5 [DE] Attribute šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] Attribute šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //checking if removed and added attribute lost its entered value
        $this->assertFalse($this->isVisible("attr_value"));
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->waitForItemAppear("attr_value");
        $this->assertEquals("", $this->getValue("attr_value"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 [DE] Attribute šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] Attribute šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Products -> Assign Selection lists
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxProductsAssignSelectionLists()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWait("nav.page.3");
        $this->clickAndWaitFrame("link=1210", "edit");
        $this->openTab("link=Selection");
        $this->click("//input[@value='Assign Selection Lists']");
        $this->usePopUp();
        $this->type("_0", "*sellist šÄßüл");
        $this->keyUp("_0", "t");
        $this->waitForAjax("[last] [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] sellist šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]", "container1");
        $this->waitForAjax("5 [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] sellist šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[9]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("5 [DE] sellist šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] [DE] sellist šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 [DE] sellist šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] [DE] sellist šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Products -> Assign Crossselling
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxProductsAssignCrossselling()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWait("nav.page.3");
        $this->clickAndWaitFrame("link=1301", "edit");
        $this->openTab("link=Crosssell.");
        $this->click("//input[@value='Assign Crosssellings']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Products -> Assign Accessories
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxProductsAssignAccessories()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=10013", "edit");
        $this->openTab("link=Crosssell.");
        $this->click("//input[@value='Assign Accessories']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));

        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[6]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->close();
    }



    /**
     * ajax: Attributes -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxAttributesAssignProducts()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'blVariantsSelection'");
        $this->loginAdmin("Administer Products", "Attributes");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->openTab("link=1 [DE] Attribute šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[19]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Attributes -> Assign Category
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxAttributesAssignCategory()
    {
        $this->loginAdmin("Administer Products", "Attributes");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Name");
        $this->clickAndWaitFrame("link=1 [DE] Attribute šÄßüл", "edit");
        $this->openTab("link=Category");
        $this->click("//input[@value='Assign Categories']");
        $this->usePopUp();
        $this->type("_0", "*[DE] šÄßüл");
        $this->keyUp("_0", "t");
        $this->waitForAjax("Test category 1 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[3]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("Test category 0 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        //sorting attributes
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test attribute 1 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("1", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("Test attribute 3 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("2", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->assertEquals("Test attribute 2 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("3", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[3]/td[2]"));
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->assertEquals("4", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[4]/td[2]"));
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->waitForAjax("Test attribute 3 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test attribute 1 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("Test attribute 2 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->waitForAjax("Test attribute 3 [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->assertEquals("Test attribute 1 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("Test attribute 3 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("Test attribute 2 [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[4]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->waitForAjax("Test category 0 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("Test category 0 [DE] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("Test category 0 [DE] šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Categories -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxCategoriesAssignProducts()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Title");
        $this->openTab("link=1 [DE] category šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10012", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]", "container2");
        //when assigning products to category, all products are allways displayed in first list. (since dodger performance for first EE releze)
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Categories -> Assign Products (assigned products with variantas)
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxCategoriesAssignProductsWithVariants()
    {
        //variants will be shown in ajax lists
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=System", "btn.help");
        $this->click("//form[@id='myedit']/div[2]/div/a/b");
        $this->waitForItemAppear("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->check("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->clickAndWait("save");

        $this->selectMenu("Administer Products", "Categories");
        $this->openTab("link=1 [EN] category šÄßüл", "assignArticle");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->type("_0", "1002");
        $this->keyUp("_0", "2");
        $this->waitForAjax("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1002-1", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("1002-2", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[4]/td[1]"));

        //drag and drop product and its variant from one list to another
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[3]/td[1]", "container2");
        $this->waitForAjax("Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[2]");
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[3]"));
        $this->close();

        //variants wont be shown in ajax lists
        $this->selectMenu("Master Settings", "Core Settings");
        $this->openTab("link=System", "btn.help");
        $this->click("//form[@id='myedit']/div[2]/div/a/b");
        $this->waitForItemAppear("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->uncheck("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->clickAndWait("save");

        $this->selectMenu("Administer Products", "Categories");
        $this->openTab("link=1 [EN] category šÄßüл", "assignArticle");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->waitForAjax("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        //unassigning this product from category
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        sleep(1);
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]"));
        $this->close();

        //selecting other category for assigning products
        $this->selectWindow(null);
        $this->frame("list");
        $this->openTab("link=2 [EN] category šÄßüл");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->type("_0", "1002");
        $this->keyUp("_0", "2");
        $this->waitForAjax("1002", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));

        //drag and drop product and its variant from one list to another
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->close();

        //variants will be shown in ajax lists again
        $this->selectMenu("Master Settings", "Core Settings");
        $this->openTab("link=System", "btn.help");
        $this->click("//form[@id='myedit']/div[2]/div/a/b");
        $this->waitForItemAppear("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->check("//input[@name='confbools[blVariantsSelection]' and @value='true']");
        $this->clickAndWait("save");

        //checking if variants were assigned and unassigned correctly
        $this->selectMenu("Administer Products", "Categories");
        $this->openTab("link=1 [EN] category šÄßüл", "assignArticle");
        $this->click("assignArticle");

        $this->usePopUp();
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]"));
        $this->close();

        $this->selectWindow(null);
        $this->frame("list");
        $this->openTab("link=2 [EN] category šÄßüл");
        $this->click("assignArticle");
        $this->usePopUp();
        $this->waitForAjax("1002", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[2]"));
        $this->close();
    }

    /**
     * ajax: Categories -> Sorting categories products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxCategoriesSortingProducts()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Sorting");
        $this->clickAndWaitFrame("link=Test category 0 [DE] šÄßüл", "edit");
        $this->openTab("//a[contains(@href,'#category_order')]");
        $this->click("//input[@value='Sort Categories']");
        $this->usePopUp();
        $this->assertEquals("1000", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]"));
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("[DE 1] Test product 1 šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->assertEquals("0", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[3]"));
        //sorting
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]", "container2");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("1000", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]", "container1");
        $this->waitForAjax("1000", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[3]"); //sorting by position
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("1000", "//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]");
        $this->click("saveBtn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]"));
        $this->assertEquals("1000", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("1", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[3]"));
        //deleting sorting
        $this->click("//div[@id='container1_c']/table/thead[2]/tr/th[1]"); //sorting by art no
        $this->waitForAjax("1", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->click("deleteBtn");
        $this->waitForAjax("0", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]");
        $this->assertEquals("1000", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[3]"));
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[2]/td[3]"));
        $this->close();
    }



    /**
     * ajax: Selection Lists -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxSelectionListsAssignProducts()
    {
        $this->loginAdmin("Administer Products", "Selection Lists");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Title");
        $this->openTab("link=1 [DE] sellist šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[1]"));
        $this->assertEquals("15 DE product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[8]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
        //sorting selection lists for product
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("test selection list [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("1", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->waitForAjax("1 [DE] sellist šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("test selection list [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("1", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]"));
        $this->click("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->waitForAjax("test selection list [DE] šÄßüл", "//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("test selection list [DE] šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("0", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[1]/td[2]"));
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("1", $this->getText("//div[@id='container3_c']/table/tbody[2]/tr[2]/td[2]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("10011", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[7]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[8]/td[1]"));
    }

   /**
     * ajax: Selection Lists -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxSelectionListsAssignAllProducts()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'blVariantsSelection'");
        $this->loginAdmin("Administer Products", "Selection Lists");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Title");
        $this->openTab("link=1 [DE] sellist šÄßüл");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]");
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1001", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertFalse($this->isElementPresent("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Users -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxUsersAssignGroups()
    {
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Name");
        $this->openTab("link=2UserSurnamešÄßüл 2useršÄßüл");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Groups -> Assign Users
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxGroupsAssignUsers()
    {
        $this->loginAdmin("Administer Users", "User Groups");
        $this->clickAndWait("link=Name");
        $this->openTab("link=1 user Group šÄßüл");
        $this->click("//input[@value='Assign Users']");
        $this->usePopUp();
        //filter
        $this->type("_0", "bir");
        $this->keyUp("_0", "r");
        $this->waitForAjax("birute_test@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]", "container1");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[10]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute03@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[2]/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[11]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("birute02@nfq.lt", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[11]/td[1]"));
        $this->close();
    }




    /**
     * ajax: News -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxNewsAssignGroups()
    {
        $this->loginAdmin("Customer Info", "News");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Title");
        $this->openTab("link=4 [DE] Test news šÄßüл");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }

    /**
     * ajax: Promotions -> Assign Products
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxPromotionsAssignProducts()
    {
         $this->executeSql("UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'blVariantsSelection'");
        $this->loginAdmin("Customer Info", "Promotions");
        $this->clickAndWait("link=Type");
        $this->openTab("link=Week's Special");
        $this->click("//input[@value='Assign Products']");
        $this->usePopUp();
        $this->type("_0", "1001");
        $this->keyUp("_0", "1");
        $this->waitForAjax("10016", "//div[@id='container1_c']/table/tbody[2]/tr[19]/td[1]");
        $this->assertEquals("10010", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[19]/td[1]"));
        $this->assertEquals("10 EN product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[19]/td[2]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("10010", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //sorting selection lists for product
        $this->assertEquals("10010", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("10011", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->assertEquals("10012", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]"));
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->waitForItemAppear("orderdown");
        $this->click("orderdown");
        $this->waitForAjax("10011", "//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]");
        $this->assertEquals("10010", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("10012", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->assertEquals("10011", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]"));
        $this->click("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]");
        $this->waitForItemAppear("orderup");
        $this->click("orderup");
        $this->waitForAjax("10012", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("10010", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->assertEquals("10011", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]", "container1");
        $this->waitForAjax("10010", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10012", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("10011", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->assertEquals("10013", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("10010", "//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]");
        $this->assertEquals("1001", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("10012", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]"));
        $this->assertEquals("10011", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[4]/td[1]"));
        $this->assertEquals("10013", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[5]/td[1]"));
        $this->assertEquals("10016", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("10010", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1001", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("10016", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->assertEquals("10 EN product šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[20]/td[2]"));
        $this->close();
    }

    /**
     * ajax: Promotions -> Assign Groups
     *
     * @group admin
     * @group ajax
     *
     * @return null
     */
    public function testAjaxPromotionsAssignGroups()
    {
        $this->loginAdmin("Customer Info", "Promotions");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Name");
        $this->assertTrue($this->isElementPresent("link=Current Promotion"));
        $this->openTab("link=Current Promotion");
        $this->click("//input[@value='Assign User Groups']");
        $this->usePopUp();
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        //assignAll btn
        $this->click("container1_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //drag and drop 1 itm from one list to another
        $this->dragAndDrop("//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]", "container1");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[20]/td[1]"));
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->waitForAjax("3 user Group šÄßüл", "//div[@id='container2_c']/table/tbody[2]/tr[3]/td[1]");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]"));
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container2_c']/table/tbody[2]/tr[21]/td[1]"));
        //unassignAll btn
        $this->click("container2_btn");
        $this->waitForAjax("1 user Group šÄßüл", "//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//div[@id='container1_c']/table/tbody[2]/tr[21]/td[1]"));
        $this->close();
    }
}
