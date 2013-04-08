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

class Acceptance_searchAndSortingAdminTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ Search and sorting in admin ----------------------------------

    /**
     * searching countries
     * @group admin
     * @group search_sort
     */
    public function testSearchCountries()
    {
        $this->loginAdmin("Master Settings", "Countries");
        //search
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxcountry][oxtitle]", "DE");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Country");
        $this->assertTrue($this->isElementPresent("link=1 DE test Country šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Bangladesch"));
        $this->assertEquals("DE", $this->getValue("where[oxcountry][oxtitle]"));
        $this->type("where[oxcountry][oxtitle]", "DE test country šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 DE test Country šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] DE test Country šÄßüл"));
        $this->assertFalse($this->isElementPresent("row.3"));
        $this->type("where[oxcountry][oxtitle]", "");
        $this->type("where[oxcountry][oxshortdesc]", "E");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] DE test Country desc"));
        $this->assertTrue($this->isElementPresent("link=Rest Welt"));
        $this->assertTrue($this->isElementPresent("link=Rest Europa"));
        $this->assertEquals("E", $this->getValue("where[oxcountry][oxshortdesc]"));
        $this->type("where[oxcountry][oxshortdesc]", "DE");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] DE test Country desc"));
        $this->assertTrue($this->isElementPresent("link=1 DE test Country desc"));
        $this->assertFalse($this->isElementPresent("row.3"));
        $this->type("where[oxcountry][oxshortdesc]", "");
        $this->type("where[oxcountry][oxisoalpha3]", "1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=111"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("1", $this->getValue("where[oxcountry][oxisoalpha3]"));
        $this->type("where[oxcountry][oxisoalpha3]", "0");
        $this->clickAndWait("submitit");
        $this->assertEquals("000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->type("where[oxcountry][oxisoalpha3]", "");
        $this->type("where[oxcountry][oxactive]", "0");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertEquals("0", $this->getValue("where[oxcountry][oxactive]"));
        $this->type("where[oxcountry][oxactive]", "1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxcountry][oxactive]", "");
        $this->type("where[oxcountry][oxtitle]", "EN");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Short Description");
        $this->assertTrue($this->isElementPresent("link=[last] EN test Country šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Argentina"));
        $this->assertEquals("EN", $this->getValue("where[oxcountry][oxtitle]"));
        $this->type("where[oxcountry][oxtitle]", "EN test");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] EN test Country šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 EN test Country šÄßüл"));
        $this->assertFalse($this->isElementPresent("row.3"));
        $this->type("where[oxcountry][oxtitle]", "");
        $this->type("where[oxcountry][oxshortdesc]", "est");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Short Description");
        $this->assertTrue($this->isElementPresent("link=1 EN test Country desc šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Rest Europe"));
        $this->assertEquals("est", $this->getValue("where[oxcountry][oxshortdesc]"));
        $this->type("where[oxcountry][oxshortdesc]", "EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN test Country desc šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN test Country desc šÄßüл"));
        $this->type("where[oxcountry][oxshortdesc]", "");
        $this->type("where[oxcountry][oxisoalpha3]", "1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=111"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->assertEquals("1", $this->getValue("where[oxcountry][oxisoalpha3]"));
        $this->type("where[oxcountry][oxisoalpha3]", "0");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=000"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->assertEquals("000", $this->getText("//tr[@id='row.1']/td[4]/div"));
        $this->type("where[oxcountry][oxisoalpha3]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting countries
     * @group admin
     * @group search_sort
     */
    public function testSortCountries()
    {
        $this->loginAdmin("Master Settings", "Countries");
        //sorting
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->clickAndWait("link=Country");
        $this->assertEquals("1 EN test Country šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] EN test Country desc šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->clickAndWait("link=Short Description");
        //Different basic data for EE and PE versions
        $this->assertEquals("[last] EN test Country šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1 EN test Country desc šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("111", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->clickAndWait("link=ISO 3");
        //Different basic data for EE and PE versions
        $this->assertEquals("1 EN test Country šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("[last] EN test Country desc šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("000", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->clickAndWait("link=Active");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->clickAndWait("link=Country");
        $this->assertEquals("1 DE test Country šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] DE test Country desc", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("111", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("Page 1 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 25 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.25'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] DE test Country šÄßüл", $this->getText("//tr[@id='row.9']/td[2]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 24 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.24'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 DE test Country šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("link=Short Description");
        $this->assertEquals("1 DE test Country desc", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("[last] DE test Country šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("000", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->clickAndWait("link=ISO 3");
        $this->assertEquals("[last] DE test Country šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1 DE test Country desc", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("000", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("1 DE test Country šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("[last] DE test Country desc", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("111", $this->getText("//tr[@id='row.3']/td[4]"));
        //deleting items for checking navigation
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("link=Country");
        $this->assertEquals("1 DE test Country šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndConfirm("del.1");
        $this->clickAndWait("nav.last", "nav.site");
        $this->assertEquals("[last] DE test Country šÄßüл", $this->getText("//tr[@id='row.8']/td[2]"));
        $this->assertEquals("Page 25 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.25'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndConfirm("del.1");
        $this->assertEquals("Page 25 / 25", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.25'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[2]"));
    }

    /**
     * searching vendors
     * @group admin
     * @group search_sort
     */
    public function testSearchDistributors()
    {
        $this->loginAdmin("Shop Settings", "Distributors");
        //search
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->type("where[oxvendor][oxtitle]", "DE distributor šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 DE distributor šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] DE distributor šÄßüл"));
        $this->type("where[oxvendor][oxtitle]", "last] DE distributor");
        $this->clickAndWait("submitit");
        $this->assertEquals("[last] DE distributor šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 DE distributor description", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("link=1 DE distributor šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("last] DE distributor", $this->getValue("where[oxvendor][oxtitle]"));
        $this->type("where[oxvendor][oxtitle]", "");
        $this->type("where[oxvendor][oxshortdesc]", "description");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] DE distributor description"));
        $this->assertTrue($this->isElementPresent("link=1 DE distributor description"));
        $this->assertTrue($this->isElementPresent("link=Distributor description [DE]"));
        $this->assertEquals("description", $this->getValue("where[oxvendor][oxshortdesc]"));
        $this->type("where[oxvendor][oxshortdesc]", "description [DE");
        $this->clickAndWait("submitit");
        $this->assertEquals("Distributor [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Distributor description [DE]", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxvendor][oxshortdesc]", "");
        $this->clickAndWait("submitit");
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxvendor][oxtitle]", "EN distributor");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN distributor šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN distributor šÄßüл"));
        $this->type("where[oxvendor][oxtitle]", "EN] Ä");
        $this->clickAndWait("submitit");
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]/div"));
        $this->assertEquals("EN] Ä", $this->getValue("where[oxvendor][oxtitle]"));
        $this->type("where[oxvendor][oxtitle]", "");
        $this->type("where[oxvendor][oxshortdesc]", "description");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN distributor description šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Distributor description [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN distributor description šÄßüл"));
        $this->assertEquals("description", $this->getValue("where[oxvendor][oxshortdesc]"));
        $this->type("where[oxvendor][oxshortdesc]", "[EN");
        $this->clickAndWait("submitit");
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxvendor][oxshortdesc]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting vendors
     * @group admin
     * @group search_sort
     */
    public function testSortDistributors()
    {
        $this->loginAdmin("Master Settings", "Distributors");
        //sorting
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 DE distributor šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last] DE distributor description", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 DE distributor šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 DE distributor šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("www.true-fashion.com", $this->getText("//tr[@id='row.10']/td[1]"));
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] DE distributor šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 DE distributor šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("link=Short Description");
        $iRowNo = 1;
        $this->assertEquals("[last] DE distributor šÄßüл", $this->getText("//tr[@id='row.".$iRowNo."']/td[1]"));
        $this->assertEquals("1 DE distributor description", $this->getText("//tr[@id='row.".$iRowNo."']/td[2]"));
        $this->assertEquals("2 DE distributor description", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        $this->assertEquals("3 DE distributor description", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] DE distributor description", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("nav.first");
        //Different demodata for EE and PE versions
        $iRowNo = 1;
        $this->assertEquals("1 DE distributor description", $this->getText("//tr[@id='row.".$iRowNo."']/td[2]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 EN distributor šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last] EN distributor description šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 EN distributor šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 EN distributor šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("link=Short Description");
        $this->assertEquals("[last] EN distributor šÄßüл", $this->getText("//tr[@id='row.".++$iRowNo."']/td[1]"));
        $this->assertEquals("1 EN distributor description šÄßüл", $this->getText("//tr[@id='row.".$iRowNo."']/td[2]"));
        $this->assertEquals("2 EN distributor description šÄßüл", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        $this->assertEquals("3 EN distributor description šÄßüл", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        //deleting items for checking navigation
        $this->clickAndWait("nav.last");
        $this->clickAndWait("link=Title");
        //$this->assertEquals("www.true-fashion.com", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last] EN distributor šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndConfirm("del.1", "del.1");
        $this->clickAndConfirm("del.1");
        //$this->assertFalse($this->isElementPresent("nav.page.1"));
        //$this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[2]"));
    }

    /**
     * searchingManufacturers
     * @group admin
     * @group search_sort
     */
    public function testSearchManufacturers()
    {
        $this->loginAdmin("Master Settings", "Brands/Manufacturers");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxmanufacturers][oxtitle]", "DE manufacturer šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 DE manufacturer šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] DE manufacturer šÄßüл"));
        $this->type("where[oxmanufacturers][oxtitle]", "last] DE manufacturer");
        $this->clickAndWait("submitit");
        $this->assertEquals("[last] DE manufacturer šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 DE manufacturer description", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("link=1 DE manufacturer"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("last] DE manufacturer", $this->getValue("where[oxmanufacturers][oxtitle]"));
        $this->type("where[oxmanufacturers][oxtitle]", "");
        $this->type("where[oxmanufacturers][oxshortdesc]", "description");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] DE manufacturer description"));
        $this->assertTrue($this->isElementPresent("link=1 DE manufacturer description"));
        $this->assertTrue($this->isElementPresent("link=Manufacturer description [DE]"));
        $this->assertEquals("description", $this->getValue("where[oxmanufacturers][oxshortdesc]"));
        $this->type("where[oxmanufacturers][oxshortdesc]", "description [DE");
        $this->clickAndWait("submitit");
        $this->assertEquals("Manufacturer [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Manufacturer description [DE]", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxmanufacturers][oxshortdesc]", "");
        $this->clickAndWait("submitit");
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxmanufacturers][oxtitle]", "EN manufacturer");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN manufacturer šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN manufacturer šÄßüл"));
        $this->type("where[oxmanufacturers][oxtitle]", "EN] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]/div"));
        $this->assertEquals("EN] šÄßüл", $this->getValue("where[oxmanufacturers][oxtitle]"));
        $this->type("where[oxmanufacturers][oxtitle]", "");
        $this->type("where[oxmanufacturers][oxshortdesc]", "description");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN manufacturer description šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Manufacturer description [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN manufacturer description šÄßüл"));
        $this->assertEquals("description", $this->getValue("where[oxmanufacturers][oxshortdesc]"));
        $this->type("where[oxmanufacturers][oxshortdesc]", "[EN");
        $this->clickAndWait("submitit");
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxmanufacturers][oxshortdesc]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Manufacturers
     * @group admin
     * @group search_sort
     */
    public function testSortManufacturers()
    {
        $this->loginAdmin("Master Settings", "Brands/Manufacturers");
        //sorting
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 DE manufacturer šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last] DE manufacturer description", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 DE manufacturer šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 DE manufacturer šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 1 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Manufacturer [DE] šÄßüл", $this->getText("//tr[@id='row.7']/td[1]"));
        $this->assertEquals("Flysurfer", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("ION", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 2 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 1 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 DE manufacturer šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("link=Short Description");
        $this->clickAndWait("nav.next");
        $iRowNo = 5;
        $this->assertEquals("[last] DE manufacturer šÄßüл", $this->getText("//tr[@id='row.".$iRowNo."']/td[1]"));
        $this->assertEquals("1 DE manufacturer description", $this->getText("//tr[@id='row.".$iRowNo."']/td[2]"));
        $this->assertEquals("2 DE manufacturer description", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        $this->assertEquals("3 DE manufacturer description", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 3 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.3'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("Eine stilbewusste Marke", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("[last] DE manufacturer description", $this->getText("//tr[@id='row.4']/td[2]"));
        $this->clickAndWait("nav.prev");
        $iRowNo = 5;
        $this->assertEquals("1 DE manufacturer description", $this->getText("//tr[@id='row.".$iRowNo."']/td[2]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=Title");
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 EN manufacturer šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last] EN manufacturer description šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 EN manufacturer šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 EN manufacturer šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("link=Short Description");
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] EN manufacturer šÄßüл", $this->getText("//tr[@id='row.".$iRowNo."']/td[1]"));
        $this->assertEquals("1 EN manufacturer description šÄßüл", $this->getText("//tr[@id='row.".$iRowNo."']/td[2]"));
        $this->assertEquals("2 EN manufacturer description šÄßüл", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        $this->assertEquals("3 EN manufacturer description šÄßüл", $this->getText("//tr[@id='row.".++$iRowNo."']/td[2]"));
        //deleting items for checking navigation
        $this->clickAndWait("nav.last");
        $this->clickAndWait("link=Title");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//tr[@id='row.7']/td[1]"));
        //$this->assertEquals("Stewart+Brown", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] EN manufacturer šÄßüл", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("Page 3 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.3'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndConfirm("del.1");
        $this->assertTrue($this->isElementPresent("nav.page.1"));
        $this->clickAndConfirm("del.1", "del.1");
        $this->assertTrue($this->isElementPresent("nav.page.1"));
        $this->clickAndConfirm("del.1");
    }

    /**
     * searching payment methods
     * @group admin
     * @group search_sort
     */
    public function testSearchPaymentMethods()
    {
        $this->loginAdmin("Shop Settings", "Payment Methods");
        //testing search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxpayments][oxdesc]", "DE test payment");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Name");
        $this->assertTrue($this->isElementPresent("link=Test payment method [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 DE test payment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 DE test payment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 DE test payment šÄßüл"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("DE test payment", $this->getValue("where[oxpayments][oxdesc]"));
        $this->type("where[oxpayments][oxdesc]", "method [DE] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test payment method [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxpayments][oxdesc]", "EN test payment");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Name");
        $this->assertTrue($this->isElementPresent("link=2 EN test payment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 EN test payment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 EN test payment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test payment method [EN] šÄßüл"));
        $this->assertEquals("EN test payment", $this->getValue("where[oxpayments][oxdesc]"));
        $this->type("where[oxpayments][oxdesc]", "method [EN");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test payment method [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->type("where[oxpayments][oxdesc]", "noEntry");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxpayments][oxdesc]", "test");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting payment methods
     * @group admin
     * @group search_sort
     */
    public function testSortPaymentMethods()
    {
        $this->loginAdmin("Shop Settings", "Payment Methods");
        $this->type("where[oxpayments][oxdesc]", "test");
        $this->clickAndWait("submitit");
        //testing sorting and navigation between pages
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 EN test payment šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 EN test payment šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 EN test payment šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] EN test payment šÄßüл", $this->getText("//tr[@id='row.1']/td[1]/div"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("1 DE test payment šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 DE test payment šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 DE test payment šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] DE test payment šÄßüл", $this->getText("//tr[@id='row.1']/td[1]/div"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
            //deleting to check navigation
            $this->clickAndWait("nav.last");
            $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
            $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
            $this->assertEquals("[last] DE test payment šÄßüл", $this->getText("//tr[@id='row.1']/td[1]/div"));
            $this->clickAndConfirm("del.1");
            $this->assertFalse($this->isElementPresent("nav.page.1"));
            $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("1 DE test payment šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searching discounts
     * @group admin
     * @group search_sort
     */
    public function testSearchDiscounts()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertTrue($this->isElementPresent("link=1 DE test discount šÄßüл"));
        $this->type("where[oxdiscount][oxtitle]", "[de] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=discount for category [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=discount for product [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Itm discount [DE] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=1 DE test discount šÄßüл"));
        $this->assertEquals("[de] šÄßüл", $this->getValue("where[oxdiscount][oxtitle]"));
        $this->type("where[oxdiscount][oxtitle]", "itm [de");
        $this->clickAndWait("submitit");
        $this->assertEquals("Itm discount [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxdiscount][oxtitle]", "[en");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=discount for category [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=discount for product [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Itm discount [EN] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=1 EN test discount šÄßüл"));
        $this->assertEquals("[en", $this->getValue("where[oxdiscount][oxtitle]"));
        $this->type("where[oxdiscount][oxtitle]", "itm [en");
        $this->clickAndWait("submitit");
        $this->assertEquals("Itm discount [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxdiscount][oxtitle]", "noEntry");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxdiscount][oxtitle]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting discounts
     * @group admin
     * @group search_sort
     */
    public function testSortDiscounts()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //sorting
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 DE test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 DE test discount šÄßüл", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("3 DE test discount šÄßüл", $this->getText("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("4 DE test discount šÄßüл", $this->getText("//tr[@id='row.6']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] DE test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 DE test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertEquals("1 EN test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 EN test discount šÄßüл", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("3 EN test discount šÄßüл", $this->getText("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("4 EN test discount šÄßüл", $this->getText("//tr[@id='row.6']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] EN test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 EN test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        //deleting to check if navigation is correct
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] DE test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 DE test discount šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searching Shipping Methods
     * @group admin
     * @group search_sort
     */
    public function testSearchDeliverySet()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //fields have different names in PE and EE versions
        $this->type("where[oxdeliveryset][oxtitle]", "DE");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 DE test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 DE test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 DE test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=5 DE test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=4 DE test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test S&H set [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] DE test S&H set šÄßüл"));
        $this->assertEquals("DE", $this->getValue("where[oxdeliveryset][oxtitle]"));
        $this->type("where[oxdeliveryset][oxtitle]", "set [DE] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test S&H set [DE] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=1 DE test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=2 DE test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=3 DE test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=5 DE test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=4 DE test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=6 DE test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=[last] DE test S&H set šÄßüл"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxdeliveryset][oxtitle]", "EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 EN test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=6 EN test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=4 EN test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 EN test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN test S&H set šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test S&H set [EN] šÄßüл"));
        $this->assertEquals("EN", $this->getValue("where[oxdeliveryset][oxtitle]"));
        $this->type("where[oxdeliveryset][oxtitle]", "set [EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test S&H set [EN] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=1 EN test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=5 EN test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=2 EN test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=6 EN test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=4 EN test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=3 EN test S&H set šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=[last] EN test S&H set šÄßüл"));
        $this->type("where[oxdeliveryset][oxtitle]", "NoName");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxdeliveryset][oxtitle]", "");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[1]"));
    }

    /**
     * sorting Shipping Methods
     * @group admin
     * @group search_sort
     */
    public function testSortDeliverySet()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //sorting
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("4 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("5 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] DE test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 EN test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 EN test S&H set šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 EN test S&H set šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("4 EN test S&H set šÄßüл", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("6 EN test S&H set šÄßüл", $this->getText("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] EN test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 EN test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        //removing last item to check if page navigation is working correctly
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] DE test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 DE test S&H set šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searching Shipping Cost rules
     * @group admin
     * @group search_sort
     */
    public function testSearchDelivery()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //fields have different names in PE and EE versions
        $this->type("where[oxdelivery][oxtitle]", "šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 DE S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 DE S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 DE S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test delivery category [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test delivery product [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] DE S&H šÄßüл"));
        $this->assertEquals("šÄßüл", $this->getValue("where[oxdelivery][oxtitle]"));
        $this->type("where[oxdelivery][oxtitle]", "2 DE");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("link=Test delivery category [DE] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=Test delivery product [DE] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=1 DE S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 DE S&H šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=3 DE S&H šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=4 DE S&H šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=[last] DE S&H šÄßüл"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxdelivery][oxtitle]", "EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] EN S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 EN S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=4 EN S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test delivery category [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test delivery product [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 EN S&H šÄßüл"));
        $this->assertEquals("EN", $this->getValue("where[oxdelivery][oxtitle]"));
        $this->type("where[oxdelivery][oxtitle]", "4 EN");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("link=Test delivery category [EN] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=Test delivery product [EN] šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=[last] EN S&H šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=3 EN S&H šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=4 EN S&H šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=2 EN S&H šÄßüл"));
        $this->assertFalse($this->isElementPresent("link=1 EN S&H šÄßüл"));
        $this->type("where[oxdelivery][oxtitle]", "NoName");
        $this->clickAndWait("submitit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxdelivery][oxtitle]", "");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[1]"));
    }

    /**
     * sorting Shipping Cost rules
     * @group admin
     * @group search_sort
     */
    public function testSortDelivery()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //sorting
        $this->clickAndWait("link=Sorting");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("[last] DE S&H šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("4", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("999999", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("3 DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("4", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 DE S&H šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 DE S&H šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("999999", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("4", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=Sorting");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("3 EN S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("1 EN S&H šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("4", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("[last] EN S&H šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("nav.next");
        $this->assertEquals("999999", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("4 EN S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("nav.prev");
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 EN S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("3 EN S&H šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("4 EN S&H šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("999999", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] EN S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("4", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //removing last element in page to see if navigation is working correctly
        $this->assertEquals("[last] DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 DE S&H šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
    }

    /**
     * searching Coupons
     * @group admin
     * @group search_sort
     */
    public function testSearchCoupons()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        //search
        $this->type("where[oxvoucherseries][oxserienr]", "2 šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test coupon 2 šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 Coupon šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[1]"));
        $this->type("where[oxvoucherseries][oxserienr]", "2 test");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test coupon 2 šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2 test", $this->getValue("where[oxvoucherseries][oxserienr]"));
        $this->type("where[oxvoucherseries][oxserienr]", "");
        $this->type("where[oxvoucherseries][oxdiscount]", "15");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=15.00 %"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("15", $this->getValue("where[oxvoucherseries][oxdiscount]"));
        $this->type("where[oxvoucherseries][oxdiscount]", "");
        $this->type("where[oxvoucherseries][oxbegindate]", "2007");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2007-01-01 00:00:00"));
        $this->assertTrue($this->isElementPresent("link=2007-12-31 00:00:00"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("2007", $this->getValue("where[oxvoucherseries][oxbegindate]"));
        $this->type("where[oxvoucherseries][oxenddate]", "2020");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2020-12-31 00:00:00"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxvoucherseries][oxbegindate]", "");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2020-12-31 00:00:00"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.3']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.11']/td[4]"));
        $this->assertEquals("2020", $this->getValue("where[oxvoucherseries][oxenddate]"));
        $this->type("where[oxvoucherseries][oxenddate]", "");
        $this->type("where[oxvoucherseries][oxminimumvalue]", "10");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=10.00"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[6]"));
        $this->assertEquals("10", $this->getValue("where[oxvoucherseries][oxminimumvalue]"));
        $this->type("where[oxvoucherseries][oxminimumvalue]", "300");
        $this->clickAndWait("submitit");
        $this->assertEquals("300.00", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.6']/td[5]"));
        $this->type("where[oxvoucherseries][oxminimumvalue]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Coupons
     * @group admin
     * @group search_sort
     */
    public function testSortCoupons()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        //Sorting
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5.00", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2007-01-01 00:00:00", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2020-12-31 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("10.00", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("2 Coupon šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 Coupon šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("link=Discount");
        $this->assertEquals("2 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("3.00", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2009-01-01 00:00:00", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2020-10-10 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("25.00", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("5.00 %", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("5.00", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Valid from");
        $this->assertEquals("1 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5.00", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2007-01-01 00:00:00", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2020-12-31 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("10.00", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("2007-12-31 00:00:00", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->clickAndWait("link=Valid until");
        $this->assertEquals("3 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("15.00 %", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2007-12-31 00:00:00", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2019-12-31 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("100.00", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("2020-01-01 00:00:00", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("2020-01-01 00:00:00", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->clickAndWait("link=Min. Order Sum");
        $this->assertEquals("1 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5.00", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2007-01-01 00:00:00", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2020-12-31 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("10.00", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("25.00", $this->getText("//tr[@id='row.2']/td[5]"));
        $this->assertEquals("45.00", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->assertEquals("75.00", $this->getText("//tr[@id='row.4']/td[5]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        //removing last element in page to see if navigation is working correctly
        $this->clickAndWait("link=Name");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 Coupon šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searching Gift Wrapping
     * @group admin
     * @group search_sort
     */
    public function testSearchGiftWrapping()
    {
        $this->loginAdmin("Shop Settings", "Gift Wrapping");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //fields have different names in PE and EE versions
        $this->type("where[oxwrapping][oxname]", "DE");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2 DE Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 DE Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=4 DE Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test card [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test wrapping [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] DE Gift Wrapping šÄßüл"));
        $this->assertEquals("DE", $this->getValue("where[oxwrapping][oxname]"));
        $this->type("where[oxwrapping][oxname]", "2 DE gift wrapping šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("2 DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxwrapping][oxname]", "");
        $this->type("where[oxwrapping][oxpic]", "img");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=img_geschenkpapier_1_wp.gif"));
        $this->assertTrue($this->isElementPresent("link=img_ecard_03_wp.jpg"));
        $this->assertTrue($this->isElementPresent("link=img_geschenkpapier_1_gelb_wp.gif"));
        $this->assertFalse($this->isElementPresent("row.4"));
        $this->assertEquals("img", $this->getValue("where[oxwrapping][oxpic]"));
        $this->type("where[oxwrapping][oxpic]", "03");
        $this->clickAndWait("submitit");
        $this->assertEquals("img_ecard_03_wp.jpg", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxwrapping][oxpic]", "");
        $this->type("where[oxwrapping][oxname]", "EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 EN Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 EN Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=4 EN Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] EN Gift Wrapping šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test card [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test wrapping [EN] šÄßüл"));
        $this->assertEquals("EN", $this->getValue("where[oxwrapping][oxname]"));
        $this->type("where[oxwrapping][oxname]", "4 EN");
        $this->clickAndWait("submitit");
        $this->assertEquals("4 EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxwrapping][oxname]", "");
        $this->type("where[oxwrapping][oxpic]", "img");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=img_geschenkpapier_1_wp.gif"));
        $this->assertTrue($this->isElementPresent("link=img_ecard_03_wp.jpg"));
        $this->assertTrue($this->isElementPresent("link=img_geschenkpapier_1_gelb_wp.gif"));
        $this->assertFalse($this->isElementPresent("row.4"));
        $this->assertEquals("img", $this->getValue("where[oxwrapping][oxpic]"));
        $this->type("where[oxwrapping][oxpic]", "03");
        $this->clickAndWait("submitit");
        $this->assertEquals("img_ecard_03_wp.jpg", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->type("where[oxwrapping][oxpic]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Gift Wrapping
     * @group admin
     * @group search_sort
     */
    public function testSortGiftWrapping()
    {
        $this->loginAdmin("Shop Settings", "Gift Wrapping");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //sorting
        $this->clickAndWait("link=Type");
        $this->assertEquals("Greeting Card", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("desaster_wp.gif", $this->getText("//tr[@id='row.5']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 DE Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("link=Picture");
        $this->assertEquals("", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("img_ecard_03_wp.jpg", $this->getText("//tr[@id='row.9']/td[3]"));
        $this->assertEquals("img_geschenkpapier_1_gelb_wp.gif", $this->getText("//tr[@id='row.10']/td[3]"));
        $this->assertEquals("Greeting Card", $this->getText("//tr[@id='row.9']/td[1]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.10']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("img_geschenkpapier_1_wp.gif", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("img_geschenkpapier_1_gelb_wp.gif", $this->getText("//tr[@id='row.10']/td[3]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=Type");
        $this->assertEquals("Greeting Card", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("img_ecard_03_wp.jpg", $this->getText("//tr[@id='row.6']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 EN Gift Wrapping šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("link=Picture");
        $this->assertEquals("", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("img_ecard_03_wp.jpg", $this->getText("//tr[@id='row.9']/td[3]"));
        $this->assertEquals("img_geschenkpapier_1_gelb_wp.gif", $this->getText("//tr[@id='row.10']/td[3]"));
        $this->assertEquals("Greeting Card", $this->getText("//tr[@id='row.9']/td[1]"));
        $this->assertEquals("Gift Wrapping", $this->getText("//tr[@id='row.10']/td[1]"));
        //deleting last element to check if navigation works correctly
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("img_geschenkpapier_1_wp.gif", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("img_geschenkpapier_1_gelb_wp.gif", $this->getText("//tr[@id='row.10']/td[3]"));
    }

    /**
     * searching Products
     * @group admin
     * @group search_sort
     */
    public function testSearchProduct()
    {
        $this->loginAdmin("Administer Products", "Products");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxarticles][oxartnum]", "1001");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1001"));
        $this->assertTrue($this->isElementPresent("link=10012"));
        $this->assertEquals("1001", $this->getValue("where[oxarticles][oxartnum]"));
        $this->type("where[oxarticles][oxartnum]", "10015");
        $this->clickAndWait("submitit");
        $this->assertEquals("10015", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("all Products", $this->getSelectedLabel("art_category"));
        $this->assertEquals("Title", $this->getSelectedLabel("pwrsearchfld"));
        $this->type("where[oxarticles][oxartnum]", "");
        $this->type("where[oxarticles][oxtitle]", "[DE");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[DE 4] Test product 0 šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[DE 1] Test product 1 šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[DE 2] Test product 2 šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[DE 3] Test product 3 šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.5']/td[3]"));
        $this->assertEquals("[DE", $this->getValue("where[oxarticles][oxtitle]"));
        $this->type("where[oxarticles][oxtitle]", "[DE 4");
        $this->clickAndWait("submitit");
        $this->assertEquals("[DE 4] Test product 0 šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxarticles][oxtitle]", "");
        $this->type("where[oxarticles][oxshortdesc]", "DE description");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=15 DE description"));
        $this->assertTrue($this->isElementPresent("link=13 DE description"));
        $this->assertTrue($this->isElementPresent("link=1 DE description"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.8']/td[4]"));
        $this->assertEquals("DE description", $this->getValue("where[oxarticles][oxshortdesc]"));
        $this->type("where[oxarticles][oxshortdesc]", "15 DE");
        $this->clickAndWait("submitit");
        $this->assertEquals("15 DE description", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[4]"));
        $this->type("where[oxarticles][oxshortdesc]", "");
        $this->selectAndWait("art_category", "label=Test category 0 [DE] šÄßüл");
        $this->assertTrue($this->isElementPresent("link=[DE 4] Test product 0 šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[DE 1] Test product 1 šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[7]/td[3]"));
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getSelectedLabel("art_category"));
        $this->selectAndWait("art_category", "label=all Products");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.3']/td[3]"));
        $this->selectAndWait("pwrsearchfld", "label=Price");
        $this->type("where[oxarticles][oxprice]", "1.5");
        $this->clickAndWait("submitit");
        $this->assertEquals("1.5", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("1.5", $this->getValue("where[oxarticles][oxprice]"));
        $this->type("where[oxarticles][oxprice]", "1.6");
        $this->clickAndWait("submitit");
        $this->assertEquals("1.6", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxarticles][oxprice]", "");
        $this->selectAndWait("pwrsearchfld", "label=Title");
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxarticles][oxartnum]", "1001");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Prod.No.");
        $this->assertTrue($this->isElementPresent("link=10010"));
        $this->assertTrue($this->isElementPresent("link=10016"));
        $this->assertEquals("1001", $this->getValue("where[oxarticles][oxartnum]"));
        $this->type("where[oxarticles][oxartnum]", "10013");
        $this->clickAndWait("submitit");
        $this->assertEquals("10013", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxarticles][oxartnum]", "");
        $this->type("where[oxarticles][oxtitle]", "EN product 1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 EN product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=10 EN product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=15 EN product šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.9']/td[3]"));
        $this->assertEquals("EN product 1", $this->getValue("where[oxarticles][oxtitle]"));
        $this->type("where[oxarticles][oxtitle]", "10 EN šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("10 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxarticles][oxtitle]", "");
        $this->type("where[oxarticles][oxshortdesc]", "EN description");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] EN description šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=15 EN description šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=13 EN description šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.8']/td[4]"));
        $this->assertEquals("EN description", $this->getValue("where[oxarticles][oxshortdesc]"));
        $this->type("where[oxarticles][oxshortdesc]", "13 EN description šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("13 EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[4]"));
        $this->type("where[oxarticles][oxshortdesc]", "");
        $this->selectAndWait("art_category", "label=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("link=Test product 0 [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test product 1 [EN] šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[3]"));
        $this->selectAndWait("art_category", "label=all Products");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.4']/td[3]"));
        $this->selectAndWait("pwrsearchfld", "label=Price");
        $this->type("where[oxarticles][oxprice]", "1.9");
        $this->clickAndWait("submitit");
        $this->assertEquals("1.9", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("1.9", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("1.9", $this->getValue("where[oxarticles][oxprice]"));
        $this->type("where[oxarticles][oxprice]", "1.7");
        $this->clickAndWait("submitit");
        $this->assertEquals("1.7", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->selectAndWait("pwrsearchfld", "label=Title");
    }

    /**
     * sorting Products
     * @group admin
     * @group search_sort
     */
    public function testSortProduct()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //sorting
        $this->type("where[oxarticles][oxartnum]", "100");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=A");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.4']/td[@class='listitem2 active']"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.5']/td[@class='listitem active']"));
        $this->clickAndWait("link=Prod.No.");
        $this->assertEquals("1000", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1001", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("10010", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Title");
        $this->assertEquals("10 DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("12 DE product šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("10011", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("10 DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("11 DE description", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->clickAndWait("link=Short Description");
        $this->assertEquals("1 DE description", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("10 DE description", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("11 DE description", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("12 DE description", $this->getText("//tr[@id='row.4']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertEquals("10010", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=A");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.4']/td[@class='listitem2 active']"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.5']/td[@class='listitem active']"));
        $this->clickAndWait("link=Prod.No.");
        $this->assertEquals("1000", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1001", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("10010", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("10010", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("1003", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Test product 3 short desc [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndWait("link=Short Description");
        $this->assertEquals("10 EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("11 EN description šÄßüл", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("10010", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("10 EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("10011", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //removing item to check if paging is correct
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"), "Delete product does not work");
        $this->assertEquals("10 EN description šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
    }

    /**
     * searching Promotions
     * @group admin
     * @group search_sort
     */
    public function testSearchPromotions()
    {
        $this->loginAdmin("Customer Info", "Promotions");

        //checking in EN lang
        $this->type("where[oxactions][oxtitle]", "top");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Top offer in categories"));
        $this->assertTrue($this->isElementPresent("link=Top offer start page"));
        $this->assertTrue($this->isElementPresent("link=Top seller"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[2]"));
        $this->assertEquals("top", $this->getValue("where[oxactions][oxtitle]"));
        $this->type("where[oxactions][oxtitle]", "top seller");
        $this->clickAndWait("submitit");
        $this->assertEquals("Top seller", $this->getText("//tr[@id='row.1']/td[2]/div"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']"));
        $this->assertEquals("top seller", $this->getValue("where[oxactions][oxtitle]"));
        $this->type("where[oxactions][oxtitle]", "");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Name");
        $this->assertEquals("Bargain", $this->getText("//tr[@id='row.5']/td[2]/div"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("Top seller", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("nav.first");
        //checking in DE lang
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.4']/td[2]"));
        $this->type("where[oxactions][oxtitle]", "top");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Kategorien-Topangebot"));
        $this->assertTrue($this->isElementPresent("link=Topangebot Startseite"));
        $this->assertTrue($this->isElementPresent("link=Topseller"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[2]"));
        $this->assertEquals("top", $this->getValue("where[oxactions][oxtitle]"));
        $this->type("where[oxactions][oxtitle]", "topseller");
        $this->clickAndWait("submitit");
        $this->assertEquals("Topseller", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxactions][oxtitle]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Promotions
     * @group admin
     * @group search_sort
     */
    public function testSortPromotions()
    {
        $futureTime = date("Y-m-d", mktime(0, 0, 0, date("m"),date("d")+1,date("Y")));
        $this->executeSql( "UPDATE `oxactions` SET `OXACTIVETO` = '".$futureTime."' WHERE `OXTITLE_1` like 'Current%'" );
        $this->executeSql( "UPDATE `oxactions` SET `OXACTIVEFROM` = '".$futureTime."', `OXACTIVETO` = '".$futureTime."' WHERE `OXTITLE_1` like 'Upcoming%'" );
        $this->loginAdmin("Customer Info", "Promotions");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("Bargain", $this->getText("//tr[@id='row.5']/td[2]"));
        $this->assertEquals("Current Promotion", $this->getText("//tr[@id='row.6']/td[2]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("2010-01-01 00:00:00", $this->getText("//tr[@id='row.6']/td[3]"));
        $this->clickAndWait("link=Start Time");
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.1']/td[3]/div"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.2']/td[3]/div"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.3']/td[3]/div"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("Action", $this->getText("//tr[@id='row.5']/td[4]/div"));
        $this->assertEquals("Promotion", $this->getText("//tr[@id='row.6']/td[4]/div"));
        $this->assertEquals("Promotion", $this->getText("//tr[@id='row.7']/td[4]/div"));
        $this->clickAndWait("link=Type");
        $this->assertEquals("Action", $this->getText("//tr[@id='row.1']/td[4]/div"));
        $this->assertEquals("Action", $this->getText("//tr[@id='row.2']/td[4]/div"));
        $this->assertEquals("Action", $this->getText("//tr[@id='row.3']/td[4]/div"));
        $this->selectAndWait("changelang", "label=Deutsch");
        $this->clickAndWait("link=Name");
        $this->assertEquals("Current Promotion", $this->getText("//tr[@id='row.6']/td[2]/div"));
        $this->assertEquals("Expired promotion", $this->getText("//tr[@id='row.7']/td[2]/div"));
        $this->assertEquals("Frisch eingetroffen", $this->getText("//tr[@id='row.8']/td[2]/div"));
        $this->assertEquals("2010-01-01 00:00:00", $this->getText("//tr[@id='row.6']/td[3]/div"));
        $this->assertEquals("2010-01-01 00:00:00", $this->getText("//tr[@id='row.7']/td[3]/div"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.8']/td[3]/div"));
        $this->clickAndWait("link=Start Time");
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.1']/td[3]/div"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.2']/td[3]/div"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.3']/td[3]/div"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("Promotion", $this->getText("//tr[@id='row.6']/td[4]/div"));
        $this->assertEquals("Promotion", $this->getText("//tr[@id='row.7']/td[4]/div"));
        $this->assertEquals("Action", $this->getText("//tr[@id='row.8']/td[4]/div"));
        $this->clickAndWait("link=Type");
        $this->assertEquals("Action", $this->getText("//tr[@id='row.1']/td[4]/div"));
        $this->assertEquals("Action", $this->getText("//tr[@id='row.2']/td[4]/div"));
        $this->assertEquals("Action", $this->getText("//tr[@id='row.3']/td[4]/div"));

        //selectlist according type
        $this->selectAndWait("displaytype", "label=Active");
        $this->assertEquals("Current Promotion", $this->getText("//tr[@id='row.1']/td[2]/div"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->selectAndWait("displaytype", "label=Upcoming");
        $this->assertEquals("Upcoming Promotion", $this->getText("//tr[@id='row.1']/td[2]/div"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->selectAndWait("displaytype", "label=Expired");
        $this->assertEquals("Expired promotion", $this->getText("//tr[@id='row.1']/td[2]/div"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->select("displaytype", "label=All");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isElementPresent("row.2"));

    }

    /**
     * searching Attributes
     * @group admin
     * @group search_sort
     */
    public function testSearchAttributes()
    {
        $this->loginAdmin("Administer Products", "Attributes");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Page 1 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->type("where[oxattribute][oxtitle]", "[DE]");
        $this->clickAndWait("submitit");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("link=1 [DE] Attribute šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test attribute 1 [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.10']/td[1]"));
        $this->assertEquals("[DE]", $this->getValue("where[oxattribute][oxtitle]"));
        $this->type("where[oxattribute][oxtitle]", "3 [DE] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=3 [DE] Attribute šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test attribute 3 [DE] šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[1]"));
        $this->type("where[oxattribute][oxtitle]", "3 [DE] test");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test attribute 3 [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxattribute][oxtitle]", "[EN] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2 [EN] Attribute šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test attribute 2 [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.10']/td[1]"));
        $this->assertEquals("[EN] šÄßüл", $this->getValue("where[oxattribute][oxtitle]"));
        $this->type("where[oxattribute][oxtitle]", "2 [EN] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2 [EN] Attribute šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test attribute 2 [EN] šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[1]"));
        $this->type("where[oxattribute][oxtitle]", "2 [en] test šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test attribute 2 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
    }

    /**
     * sorting Attributes
     * @group admin
     * @group search_sort
     */
    public function testSortAttributes()
    {
        $this->loginAdmin("Administer Products", "Attributes");
        //sorting
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxattribute][oxtitle]", "[DE]");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 [DE] Attribute šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] [DE] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 [DE] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxattribute][oxtitle]", "[EN] šÄßüл");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //deleting last entry to check navigation
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 [EN] Attribute šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searching Categories
     * @group admin
     * @group search_sort
     */
    public function testSearchCategories()
    {
        $this->loginAdmin("Administer Products", "Categories");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.4']/td[2]"));
        //fields have different names in PE and EE versions
        $this->type("where[oxcategories][oxsort]", "6");
        $this->clickAndWait("submitit");
        $this->assertEquals("6", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("6", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[2]"));
        $this->type("where[oxcategories][oxsort]", "1");
        $this->clickAndWait("submitit");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("1", $this->getValue("where[oxcategories][oxsort]"));
        $this->type("where[oxcategories][oxsort]", "");
        $this->type("where[oxcategories][oxtitle]", "[DE] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test category 0 [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] [DE] category šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=5 [DE] category šÄßüл"));
        $this->assertEquals("[DE] šÄßüл", $this->getValue("where[oxcategories][oxtitle]"));
        $this->type("where[oxcategories][oxtitle]", "1 [DE");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test category 1 [DE] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 [DE] category šÄßüл"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxcategories][oxtitle]", "1 [DE] test");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxcategories][oxtitle]", "");
        $this->selectAndWait("where[oxcategories][oxparentid]", "label=Test category 0 [DE] šÄßüл");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getSelectedLabel("where[oxcategories][oxparentid]"), "'Show All' is allways displayed as oxparentid, no matter if other category was selected here.");
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->selectAndWait("where[oxcategories][oxparentid]", "label=Show all");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.3']/td[3]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxcategories][oxsort]", "7");
        $this->clickAndWait("submitit");
        $this->assertEquals("7", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("7", $this->getValue("where[oxcategories][oxsort]"));
        $this->type("where[oxcategories][oxsort]", "6");
        $this->clickAndWait("submitit");
        $this->assertEquals("6", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("6", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[2]"));
        $this->type("where[oxcategories][oxsort]", "");
        $this->type("where[oxcategories][oxtitle]", "[EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test category 0 [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 [EN] category šÄßüл"));
        $this->type("where[oxcategories][oxtitle]", "1 [EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=Test category 1 [EN] šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 [EN] category šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("1 [EN", $this->getValue("where[oxcategories][oxtitle]"));
        $this->type("where[oxcategories][oxtitle]", "1 [EN] TEST šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->type("where[oxcategories][oxtitle]", "");
        $this->selectAndWait("where[oxcategories][oxparentid]", "label=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->selectAndWait("where[oxcategories][oxparentid]", "label=Show all");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.2']/td[3]"));
    }

     /**
     * sorting Categories
     * @group admin
     * @group search_sort
     */
    public function testSortCategories()
    {
        $this->loginAdmin("Administer Products", "Categories");
        //sorting
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxcategories][oxtitle]", "[de");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=A");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.2']/td[@class='listitem2 active']"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.3']/td[@class='listitem active']"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[@class='listitem2 active']"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->clickAndWait("link=Sorting");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 [DE] category šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("5 [DE] category šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.4']/td[2]"));
        $this->assertEquals("Test category 1 [DE] šÄßüл", $this->getText("//tr[@id='row.4']/td[3]"));
        $this->clickAndWait("link=Title");
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("5", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("2 [DE] category šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("3 [DE] category šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] [DE] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->type("where[oxcategories][oxtitle]", "[en");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=A");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[@class='listitem active']"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.2']/td[@class='listitem2 active']"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.3']/td[@class='listitem active']"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[@class='listitem2 active']"));
        $this->clickAndWait("link=Sorting");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("4 [EN] category šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("1", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("6 [EN] category šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.4']/td[2]"));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getText("//tr[@id='row.4']/td[3]"));
        $this->clickAndWait("link=Title");
        $this->assertEquals("3", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1 [EN] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("6", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("2 [EN] category šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("5", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("3 [EN] category šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 [EN] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //deleting last element to check navigation
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 [EN] category šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
    }

    /**
     * searching Selection Lists
     * @group admin
     * @group search_sort
     */
    public function testSearchSelectionLists()
    {
        $this->loginAdmin("Administer Products", "Selection Lists");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->type("where[oxselectlist][oxtitle]", "[de šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 [DE] sellist šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 [DE] sellist šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=test selection list [DE] šÄßüл"));
        $this->assertEquals("[de šÄßüл", $this->getValue("where[oxselectlist][oxtitle]"));
        $this->type("where[oxselectlist][oxtitle]", "4 [de");
        $this->clickAndWait("submitit");
        $this->assertEquals("4 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("4 [de", $this->getValue("where[oxselectlist][oxtitle]"));
        $this->type("where[oxselectlist][oxtitle]", "[en");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 [EN] sellist šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 [EN] sellist šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=test selection list [EN] šÄßüл"));
        $this->assertEquals("[en", $this->getValue("where[oxselectlist][oxtitle]"));
        $this->type("where[oxselectlist][oxtitle]", "7 [en");
        $this->clickAndWait("submitit");
        $this->assertEquals("7 [EN] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

     /**
     * sorting Selection Lists
     * @group admin
     * @group search_sort
     */
    public function testSortSelectionLists()
    {
        $this->loginAdmin("Administer Products", "Selection Lists");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //sorting
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("7 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("link=Working Title");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 sellist šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 sellist šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("8 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 [DE] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 [EN] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [EN] sellist šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 [EN] sellist šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("nav.next");
        $this->assertEquals("[last] [EN] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 [EN] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndWait("link=Working Title");
        $this->assertEquals("1 sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] [EN] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 sellist šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 sellist šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        //deleting item to check page navigation
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("[last] [EN] sellist šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searching Review List
     * @group admin
     * @group search_sort
     */
    public function testSearchReviewList()
    {
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']", "navigation");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->click("link=Administer Products");
        $this->click("link=List All Reviews");
        $this->waitForFrameToLoad("list");
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");
        $this->checkForErrors();
        //search
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWait("changelang", "label=Deutsch");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->assertTrue($this->isElementPresent("link=2005-12-30 11:11:00"));
        $this->assertTrue($this->isElementPresent("link=2006-01-01 10:10:10"));
        $this->assertFalse($this->isElementPresent("link=3 [EN] product šÄßüл comment"));
        $this->type("where[oxreviews][oxcreate]", "2005");
        $this->clickAndWait("submitit");
        $this->assertEquals("2005-12-30 11:11:00", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2005-12-01 11:11:00", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[2]"));
        $this->type("where[oxreviews][oxcreate]", "2005-12-30 11:11:00");
        $this->clickAndWait("submitit");
        $this->assertEquals("2005-12-30 11:11:00", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2005-12-30 11:11:00", $this->getValue("where[oxreviews][oxcreate]"));
        $this->type("where[oxreviews][oxcreate]", "");
        $this->type("where[oxreviews][oxtext]", "DE]");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 [DE] comment for product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 [DE] comment for product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 [DE] comment for product šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[2]"));
        $this->type("where[oxreviews][oxtext]", "DE] 3");
        $this->clickAndWait("submitit");
        $this->assertEquals("3 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2005-12-01 11:11:00", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("DE] 3", $this->getValue("where[oxreviews][oxtext]"));
        $this->type("where[oxreviews][oxtext]", "");
        $this->type("where[oxarticles][oxtitle]", "DE");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=[last] DE product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=11 DE product šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[3]"));
        $this->type("where[oxarticles][oxtitle]", "DE 11");
        $this->clickAndWait("submitit");
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("3 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2005-12-01 11:11:00", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("DE 11", $this->getValue("where[oxarticles][oxtitle]"));
        $this->type("where[oxarticles][oxtitle]", "");
        $this->selectAndWait("changelang", "label=English");
        $this->assertFalse($this->isElementPresent("link=1 [DE] comment for product šÄßüл"));
        $this->type("where[oxreviews][oxcreate]", "2006");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2006-02-05 10:10:10"));
        $this->assertTrue($this->isElementPresent("link=2006-02-01 08:10:10"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[1]"));
        $this->type("where[oxreviews][oxcreate]", "2006-02 10:10:10");
        $this->clickAndWait("submitit");
        $this->assertEquals("2006-02-05 10:10:10", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2006-02 10:10:10", $this->getValue("where[oxreviews][oxcreate]"));
        $this->type("where[oxreviews][oxcreate]", "");
        $this->type("where[oxreviews][oxtext]", "EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=3 [EN] product comment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 [EN] product comment šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 [EN] product comment šÄßüл"));
        $this->type("where[oxreviews][oxtext]", "EN 1");
        $this->clickAndWait("submitit");
        $this->assertEquals("1 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2006-02-05 10:10:10", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("EN 1", $this->getValue("where[oxreviews][oxtext]"));
        $this->type("where[oxreviews][oxtext]", "");
        $this->type("where[oxarticles][oxtitle]", "EN");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=11 EN product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=12 EN product šÄßüл"));
        $this->type("where[oxarticles][oxtitle]", "EN 12");
        $this->clickAndWait("submitit");
        $this->assertEquals("12 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2005-12-05 10:10:10", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("EN 12", $this->getValue("where[oxarticles][oxtitle]"));
        $this->type("where[oxarticles][oxtitle]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Review List
     * @group admin
     * @group search_sort
     */
    public function testSortReviewList()
    {
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']", "navigation");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->click("link=Administer Products");
        $this->click("link=List All Reviews");
        $this->waitForFrameToLoad("list");
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");
        $this->checkForErrors();
        //sorting
        $this->clickAndWait("link=Created");
        $this->assertEquals("2005-12-05 10:10:10", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2006-02-01 08:10:10", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2006-02-05 10:10:10", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("2 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("3 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Content");
        $this->assertEquals("1 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 [EN] product comment šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("12 EN product šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->clickAndWait("link=Product");
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"), "Bug from Mantis #516");
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("12 EN product šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->selectAndWait("changelang", "label=Deutsch");
        $this->type("where[oxreviews][oxtext]", "DE]");
        $this->clickAndWait("submitit");
        $this->clickAndWait("link=Created");
        $this->assertEquals("2005-12-01 11:11:00", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2005-12-30 11:11:00", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2006-01-01 10:10:10", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("3 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Content");
        $this->assertEquals("1 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 [DE] comment for product šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->clickAndWait("link=Product");
        $this->assertEquals("11 DE product šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("[last] DE product šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));

    }

    /**
     * searching Users
     * @group admin
     * @group search_sort
     */
    public function testSearchUsers()
    {
        $this->loginAdmin("Administer Products", "Users");
        //search
        $this->type("where[oxuser][oxlname]", "user");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1UserSurnamešÄßüл 1useršÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3UserSurnamešÄßüл 3useršÄßüл"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.10']/td[1]"));
        $this->assertEquals("user", $this->getValue("where[oxuser][oxlname]"));
        $this->type("where[oxuser][oxlname]", "2UserSurnamešÄßüл 2useršÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("2UserSurnamešÄßüл 2useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("444000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("3 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("555555", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("13", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxuser][oxlname]", "");
        $this->type("where[oxuser][oxusername]", "nfq.lt");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=birute02@nfq.lt"));
        $this->assertTrue($this->isElementPresent("link=birute03@nfq.lt"));
        $this->assertEquals("nfq.lt", $this->getValue("where[oxuser][oxusername]"));
        $this->type("where[oxuser][oxusername]", "birute08@nfq.lt");
        $this->clickAndWait("submitit");
        $this->assertEquals("7UserSurnamešÄßüл 7useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute08@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("5 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("777000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("6 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("111111", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("16", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("birute08@nfq.lt", $this->getValue("where[oxuser][oxusername]"));
        $this->type("where[oxuser][oxusername]", "");
        $this->type("where[oxuser][oxstreet]", "street.šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 Street.šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=5 Street.šÄßüл"));
        $this->assertEquals("street.šÄßüл", $this->getValue("where[oxuser][oxstreet]"));
        $this->type("where[oxuser][oxstreet]", "4 street");
        $this->clickAndWait("submitit");
        $this->assertEquals("4UserSurnamešÄßüл 4useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute05@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("4 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("666000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("666000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("5 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("777777", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("15", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("4 street", $this->getValue("where[oxuser][oxstreet]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxuser][oxstreet]", "");
        $this->type("where[oxuser][oxzip]", "000");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=333000"));
        $this->assertTrue($this->isElementPresent("link=111000"));
        $this->assertEquals("000", $this->getValue("where[oxuser][oxzip]"));
        $this->type("where[oxuser][oxzip]", "666000");
        $this->clickAndWait("submitit");
        $this->assertEquals("4UserSurnamešÄßüл 4useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute05@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("4 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("666000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("5 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("777777", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("15", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("666000", $this->getValue("where[oxuser][oxzip]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxuser][oxzip]", "");
        $this->type("where[oxuser][oxcity]", "city");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=5 City šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=1 City šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 City šÄßüл"));
        $this->assertEquals("city", $this->getValue("where[oxuser][oxcity]"));
        $this->type("where[oxuser][oxcity]", "3 city");
        $this->clickAndWait("submitit");
        $this->assertEquals("2UserSurnamešÄßüл 2useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("444000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("3 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("555555", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("13", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("3 city", $this->getValue("where[oxuser][oxcity]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxuser][oxcity]", "");
        $this->type("where[oxuser][oxfon]", "11");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=111111"));
        $this->assertTrue($this->isElementPresent("link=0800 111111"));
        $this->assertEquals("11", $this->getValue("where[oxuser][oxfon]"));
        $this->type("where[oxuser][oxfon]", "0800 111111");
        $this->clickAndWait("submitit");
        $this->assertEquals("UserSurnamešÄßüл UserNamešÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Musterstr.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("79098", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("Musterstadt šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("0800 111111", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("8", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("0800 111111", $this->getValue("where[oxuser][oxfon]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxuser][oxfon]", "");
        $this->type("where[oxuser][oxcustnr]", "1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1"));
        $this->assertTrue($this->isElementPresent("link=10"));
        $this->assertTrue($this->isElementPresent("link=11"));
        $this->assertTrue($this->isElementPresent("link=16"));
        $this->assertEquals("1", $this->getValue("where[oxuser][oxcustnr]"));
        $this->type("where[oxuser][oxcustnr]", "12");
        $this->clickAndWait("submitit");
        $this->assertEquals("1UserSurnamešÄßüл 1useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("333000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("2 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("444444", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("12", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("12", $this->getValue("where[oxuser][oxcustnr]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
    }

    /**
     * sorting Users
     * @group admin
     * @group search_sort
     */
    public function testSortUsers()
    {
        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxusername]", "nfq");
        $this->clickAndWait("submitit");
        //sorting
        $this->clickAndWait("link=Name");
        $this->assertEquals("1UserSurnamešÄßüл 1useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2UserSurnamešÄßüл 2useršÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3UserSurnamešÄßüл 3useršÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("333000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("444000", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("111000", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->clickAndWait("link=ZIP");
        $this->assertEquals("111000", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("222000", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("333000", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("birute04@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("birute06@nfq.lt", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=E-mail");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("birute04@nfq.lt", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("1 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2 Street.šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("6 Street.šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->clickAndWait("link=Street");
        $this->assertEquals("1 Street.šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2 Street.šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("3 Street.šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("2 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("3 City šÄßüл", $this->getText("//tr[@id='row.2']/td[5]"));
        $this->assertEquals("4 City šÄßüл", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->clickAndWait("link=City");
        $this->assertEquals("1 City šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("2 City šÄßüл", $this->getText("//tr[@id='row.2']/td[5]"));
        $this->assertEquals("3 City šÄßüл", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->assertEquals("333333", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("444444", $this->getText("//tr[@id='row.2']/td[6]"));
        $this->assertEquals("555555", $this->getText("//tr[@id='row.3']/td[6]"));
        $this->clickAndWait("link=Phone");
        $this->assertEquals("0800 111111", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("0800 222221", $this->getText("//tr[@id='row.2']/td[6]"));
        $this->assertEquals("0800 333331", $this->getText("//tr[@id='row.3']/td[6]"));
        $this->clickAndWait("link=Street");
        $this->assertEquals("12", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("13", $this->getText("//tr[@id='row.2']/td[7]"));
        $this->assertEquals("14", $this->getText("//tr[@id='row.3']/td[7]"));
        $this->clickAndWait("link=Cust No.");
        $this->assertEquals("8", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("9", $this->getText("//tr[@id='row.2']/td[7]"));
        $this->assertEquals("10", $this->getText("//tr[@id='row.3']/td[7]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("18", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("8", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("18", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("8", $this->getText("//tr[@id='row.1']/td[7]"));
        //deleting last element to check navigation
        $this->clickAndWait("nav.last");
        $this->assertEquals("18", $this->getText("//tr[@id='row.1']/td[7]"));
        //$this->clickAndConfirm("del.1");
        //$this->assertFalse($this->isElementPresent("nav.page.1"));
        //$this->assertEquals("8", $this->getText("//tr[@id='row.1']/td[7]"));
    }

    /**
     * searching Groups
     * @group admin
     * @group search_sort
     */
    public function testSearchGroups()
    {
        $this->loginAdmin("Administer Products", "User Groups", "btn.help", "where[oxgroups][oxtitle]");
        //search
        $this->type("where[oxgroups][oxtitle]", "user šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("user šÄßüл", $this->getValue("where[oxgroups][oxtitle]"));
        $this->assertTrue($this->isElementPresent("link=1 user Group šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2 user Group šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=3 user Group šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Z user Group šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] user Group šÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.6']/td[1]"));
        $this->type("where[oxgroups][oxtitle]", "Z user");
        $this->clickAndWait("submitit");
        $this->assertEquals("Z user Group šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Z user", $this->getValue("where[oxgroups][oxtitle]"));
        $this->type("where[oxgroups][oxtitle]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Groups
     * @group admin
     * @group search_sort
     */
    public function testSortGroups()
    {
        $this->loginAdmin("Administer Products", "User Groups");
        //sorting
        $this->clickAndWait("link=Name");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2 user Group šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3 user Group šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("Page 1 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("Z user Group šÄßüл", $this->getText("//tr[@id='row.10']/td[1]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 3 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.3'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.6']/td[1]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 2 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("Z user Group šÄßüл", $this->getText("//tr[@id='row.10']/td[1]"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 3", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1 user Group šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] user Group šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndConfirm("del.1");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("Z user Group šÄßüл", $this->getText("//tr[@id='row.10']/td[1]"));
    }

    /**
     * searching user list
     * @group admin
     * @group search_sort
     */
    public function testSearchList()
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
        $this->click("link=Administer Users");
        $this->click("link=List All Users");
        $this->waitForFrameToLoad("list");
        $this->selectFrame("relative=up");
        $this->selectFrame("basefrm");
        //search
        $this->waitForElement("where[oxuser][oxfname]");
        $this->type("where[oxuser][oxfname]", "user");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1useršÄßüл"));
        $this->assertTrue($this->isElementPresent("link=UserNamešÄßüл"));
        $this->assertTrue($this->isElementPresent("link=UserCNamešÄßüл"));
        $this->type("where[oxuser][oxfname]", "5useršÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("5useršÄßüл", $this->getValue("where[oxuser][oxfname]"));
        $this->assertEquals("5useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("birute06@nfq.lt", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2007-06-20 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("5useršÄßüл", $this->getValue("where[oxuser][oxfname]"));
        $this->type("where[oxuser][oxfname]", "");
        $this->type("where[oxuser][oxlname]", "surname");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1UserSurnamešÄßüл"));
        $this->assertTrue($this->isElementPresent("link=UserSurnamešÄßüл"));
        $this->assertTrue($this->isElementPresent("link=UserBSurnamešÄßüл"));
        $this->type("where[oxuser][oxlname]", "7Usersurname");
        $this->clickAndWait("submitit");
        $this->assertEquals("7useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("7UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("birute08@nfq.lt", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2008-01-10 00:00:02", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("7Usersurname", $this->getValue("where[oxuser][oxlname]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("7Usersurname", $this->getValue("where[oxuser][oxlname]"));
        $this->type("where[oxuser][oxlname]", "");
        $this->type("where[oxuser][oxusername]", "@nfq.lt");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=birute02@nfq.lt"));
        $this->assertTrue($this->isElementPresent("link=birute_test@nfq.lt"));
        $this->assertTrue($this->isElementPresent("link=birute0b@nfq.lt"));
        $this->type("where[oxuser][oxusername]", "birute07@nfq.lt");
        $this->clickAndWait("submitit");
        $this->assertEquals("6useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("6UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("birute07@nfq.lt", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2008-01-10 11:10:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("birute07@nfq.lt", $this->getValue("where[oxuser][oxusername]"));
        $this->type("where[oxuser][oxusername]", "");
        $this->type("where[oxuser][oxregister]", "2008");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-02-01 00:00:00"));
        $this->assertTrue($this->isElementPresent("link=2008-01-10 00:00:00"));
        $this->assertTrue($this->isElementPresent("link=2008-02-05 14:42:42"));
        $this->assertTrue($this->isElementPresent("link=2008-01-10 11:10:00"));
        $this->type("where[oxuser][oxregister]", "2008-01-10");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-01-10 00:00:00"));
        $this->assertTrue($this->isElementPresent("link=2008-01-10 11:10:00"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.6']/td[4]"));
        $this->type("where[oxuser][oxregister]", "2008-01-10 11");
        $this->clickAndWait("submitit");
        $this->assertEquals("6useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("6UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("birute07@nfq.lt", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2008-01-10 11:10:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("2008-01-10 11", $this->getValue("where[oxuser][oxregister]"));
    }

    /**
     * sorting user list
     * @group admin
     * @group search_sort
     */
    public function testSortList()
    {
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->clickAndWait("//input[@type='submit']", "navigation");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->checkForErrors();
        $this->click("link=Administer Users");
        $this->click("link=List All Users");
        $this->waitForFrameToLoad("list");
        $this->selectFrame("relative=up");
        $this->selectFrame("basefrm");
        $this->type("where[oxuser][oxfname]", "user");
        $this->clickAndWait("submitit");
        //sorting
        $this->clickAndWait("link=First Name");
        $this->assertEquals("1useršÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2useršÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("2008-02-01 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("2008-01-10 00:00:00", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("2008-01-10 00:00:03", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->clickAndWait("link=Registered");
        $this->assertEquals("2007-06-20 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("2008-01-10 00:00:00", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("2008-01-10 00:00:01", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("5UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2UserSurnamešÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("4UserSurnamešÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Last Name");
        $this->assertEquals("1UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2UserSurnamešÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Registered");
        $this->assertEquals("birute06@nfq.lt", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("birute05@nfq.lt", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->clickAndWait("link=E-mail");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("birute04@nfq.lt", $this->getText("//tr[@id='row.3']/td[3]"));
    }



    /**
     * searching Orders
     * @group admin
     * @group search_sort
     */
    public function testSearchOrders()
    {
        $this->loginAdmin("Administer Orders", "Orders", "btn.help", "link=9");
        //search
        $this->assertTrue($this->isElementPresent("link=1"));
        $this->assertTrue($this->isElementPresent("link=2"));
        $this->assertTrue($this->isElementPresent("link=4"));
        $this->assertTrue($this->isElementPresent("link=5"));
        $this->assertTrue($this->isElementPresent("link=6"));
        $this->assertTrue($this->isElementPresent("link=8"));
        $this->assertTrue($this->isElementPresent("link=9"));
        $this->assertTrue($this->isElementPresent("link=10"));
        $this->assertTrue($this->isElementPresent("link=11"));
        $this->assertFalse($this->isElementPresent("link=7"));
        $this->assertFalse($this->isElementPresent("link=3"));
        $this->openTab("link=6");
        $this->assertEquals("New", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=Finished");
        $this->assertEquals("3", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->openTab("link=3");
        $this->assertEquals("Finished", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=Problems");
        $this->assertEquals("7", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->openTab("link=7");
        $this->assertEquals("Problems", $this->getSelectedLabel("setfolder"));
        $this->frame("list");
        $this->selectAndWait("folder", "label=all");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("link=2008-04-21 14:54:33"));
        $this->assertTrue($this->isElementPresent("link=2008-04-21 15:02:12"));
        $this->type("where[oxorder][oxorderdate]", "2008-04-21 15");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-04-21 15:07:46"));
        $this->assertTrue($this->isElementPresent("link=2008-04-21 15:02:54"));
        $this->assertTrue($this->isElementPresent("link=2008-04-21 15:02:12"));
        $this->assertTrue($this->isElementPresent("link=2008-04-21 15:00:38"));
        $this->assertEquals("2008-04-21 15", $this->getValue("where[oxorder][oxorderdate]"));
        $this->type("where[oxorder][oxorderdate]", "");
        $this->type("where[oxorder][oxordernr]", "1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=11"));
        $this->assertTrue($this->isElementPresent("link=10"));
        $this->assertTrue($this->isElementPresent("link=1"));
        $this->type("where[oxorder][oxordernr]", "10");
        $this->clickAndWait("submitit");
        $this->assertEquals("10", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("10", $this->getValue("where[oxorder][oxordernr]"));
        $this->type("where[oxorder][oxordernr]", "");
        $this->type("where[oxorder][oxbilllname]", "3user");
        $this->clickAndWait("submitit");
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->type("where[oxorder][oxbilllname]", "2usersurnamešÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2useršÄßüл"));
        $this->assertTrue($this->isElementPresent("link=2UserSurnamešÄßüл"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("2usersurnamešÄßüл", $this->getValue("where[oxorder][oxbilllname]"));
        $this->type("where[oxorder][oxbilllname]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Orders
     * @group admin
     * @group search_sort
     */
    public function testSortOrders()
    {
        $this->loginAdmin("Administer Orders", "Orders", "btn.help", "link=9");
        $this->selectAndWait("folder", "label=all");
        //sorting
        $this->clickAndWait("link=Order Time");
        $this->assertEquals("2008-04-21 15:07:46", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2008-04-21 15:02:54", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2008-04-21 15:02:12", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("2008-04-21 15:08:47", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("2008-04-21 15:08:11", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Payment Date");
        $this->assertEquals("2008-04-21 15:14:02", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2008-04-21 15:08:47", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("2008-04-21 15:08:26", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("2", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("11", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->clickAndWait("link=Order No.");
        $this->assertEquals("11", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("10", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("9", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("1useršÄßüл", $this->getText("//tr[@id='row.4']/td[4]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.2']/td[5]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->assertEquals("1UserSurnamešÄßüл", $this->getText("//tr[@id='row.4']/td[5]"));
        $this->clickAndWait("link=First Name");
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("3useršÄßüл", $this->getText("//tr[@id='row.4']/td[4]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.2']/td[5]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->assertEquals("3UserSurnamešÄßüл", $this->getText("//tr[@id='row.4']/td[5]"));
        $this->clickAndWait("link=Order No.");
        $this->assertEquals("11", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("11", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("11", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //testing if other tabs are working
        $this->openTab("link=6");
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->frame("list");
        $this->openTab("link=Main");
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->frame("list");
        $this->openTab("link=Products");
        $this->frame("list");
        $this->openTab("link=History");
        //checking navigation
        $this->frame("list");
        $this->clickAndWait("nav.last");
        //deleting order
        $this->assertEquals("1", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("11", $this->getText("//tr[@id='row.1']/td[3]"));
        //canceling order
        sleep(3);
        $this->openTab("link=11");
        $this->assertEquals("3,03", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]"));
        $this->frame("list");
        $this->clickAndConfirm("pau.1");
        sleep(3);
        $this->frame("edit");
        $this->assertEquals( "3,60", $this->getText("//table[@id='order.info']/tbody/tr[1]/td[2]" ) );
        $this->assertEquals( "- 0,00", $this->getText("//table[@id='order.info']/tbody/tr[2]/td[2]" ) );
        $this->assertEquals( "3,03", $this->getText("//table[@id='order.info']/tbody/tr[3]/td[2]" ) );
        $this->assertEquals( "12,90", $this->getText("//table[@id='order.info']/tbody/tr[4]/td[2]" ) );
        $this->assertEquals( "20,90", $this->getText("//table[@id='order.info']/tbody/tr[5]/td[2]" ) );
        $this->assertEquals( "37,40", $this->getText("//table[@id='order.info']/tbody/tr[6]/td[2]" ) );
        //testing if other tabs are working
        $this->frame("list");
        $this->openTab("link=Overview");
        $this->frame("list");
        $this->openTab("link=Main");
        $this->frame("list");
        $this->openTab("link=Addresses");
        $this->frame("list");
        $this->openTab("link=Products");
        $this->frame("list");
        $this->openTab("link=History");
        // canceling and deleting same product
        $this->frame("list");
        $this->clickAndConfirm("pau.2");
        $this->clickAndConfirm("del.2");
        $this->frame("edit", "btn.help");
    }

    /**
     * searchingOrder list (PE version only)
     * @group admin
     * @group search_sort
     */
    public function testSearchOrderList()
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
            $this->click("link=Administer Orders");
            $this->click("link=Order Summary");
            $this->waitForFrameToLoad("basefrm");
            $this->selectFrame("relative=top");
            $this->selectFrame("basefrm");
            $this->checkForErrors();
            $this->assertTrue($this->isElementPresent("liste"));
            $this->assertEquals("Sum: 156.30", $this->clearString($this->getText("//div[@id='liste']/span")));
            //search
            $this->type("where[oxorderarticles][oxtitle]", "en šÄßüл");
            $this->clickAndWait("submitit");
            $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
            $this->assertTrue($this->isElementPresent("link=1 EN product šÄßüл"));
            $this->assertTrue($this->isElementPresent("link=11 EN product šÄßüл"));
            $this->assertTrue($this->isElementPresent("link=12 EN product šÄßüл"));
            $this->type("where[oxorderarticles][oxtitle]", "en 12");
            $this->clickAndWait("submitit");
            $this->assertEquals("12 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
            $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[4]"));
            $this->assertEquals("en 12", $this->getValue("where[oxorderarticles][oxtitle]"));
            $this->type("where[oxorderarticles][oxtitle]", "");
            $this->type("where[oxorderarticles][oxartnum]", "100");
            $this->clickAndWait("submitit");
            $this->assertTrue($this->isElementPresent("link=10010"));
            $this->assertTrue($this->isElementPresent("link=10011"));
            $this->assertTrue($this->isElementPresent("link=10012"));
            $this->type("where[oxorderarticles][oxartnum]", "10012");
            $this->clickAndWait("submitit");
            $this->assertEquals("10012", $this->getText("//tr[@id='row.1']/td[2]"));
            $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
            $this->assertEquals("10012", $this->getValue("where[oxorderarticles][oxartnum]"));
            $this->type("where[oxorderarticles][oxartnum]", "");
            $this->type("where[oxorder][oxorderdate]", "2008-04-21");
            $this->clickAndWait("submitit");
            $this->assertTrue($this->isElementPresent("link=2008-04-21 14:54:33"));
            $this->assertTrue($this->isElementPresent("link=2008-04-21 15:07:46"));
            $this->assertTrue($this->isElementPresent("link=2008-04-21 15:02:54"));
            $this->type("where[oxorder][oxorderdate]", "2008-04-21 14");
            $this->clickAndWait("submitit");
            $this->assertTrue($this->isElementPresent("link=2008-04-21 14:54:33"));
            $this->assertTrue($this->isElementPresent("link=2008-04-21 14:54:33"));
            $this->assertTrue($this->isElementPresent("link=2008-04-21 14:59:08"));
            $this->assertEquals("2008-04-21 14", $this->getValue("where[oxorder][oxorderdate]"));
            $this->type("where[oxorder][oxorderdate]", "");
            $this->clickAndWait("submitit");
    }

    /**
     * sorting Order list (PE version only)
     * @group admin
     * @group search_sort
     */
    public function testSortOrderList()
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
            $this->click("link=Administer Orders");
            $this->click("link=Order Summary");
            $this->waitForFrameToLoad("basefrm");
            $this->selectFrame("relative=top");
            $this->selectFrame("basefrm");
            $this->checkForErrors();
            //sorting
            $this->clickAndWait("link=Date");
            $this->assertEquals("2008-04-21 15:07:46", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("2008-04-21 15:02:54", $this->getText("//tr[@id='row.2']/td[1]"));
            $this->assertEquals("2008-04-21 14:54:33", $this->getText("//tr[@id='row.3']/td[1]"));
            $this->clickAndWait("link=Date");
            $this->assertEquals("10011", $this->getText("//tr[@id='row.1']/td[2]"));
            $this->assertEquals("10012", $this->getText("//tr[@id='row.2']/td[2]"));
            $this->assertEquals("10010", $this->getText("//tr[@id='row.3']/td[2]"));
            $this->clickAndWait("link=Prod.No.");
            $this->assertEquals("1000", $this->getText("//tr[@id='row.1']/td[2]"));
            $this->assertEquals("10010", $this->getText("//tr[@id='row.2']/td[2]"));
            $this->assertEquals("10011", $this->getText("//tr[@id='row.3']/td[2]"));
            $this->clickAndWait("link=Date");
            $this->assertEquals("11", $this->getText("//tr[@id='row.1']/td[3]"));
            $this->assertEquals("16", $this->getText("//tr[@id='row.2']/td[3]"));
            $this->assertEquals("3", $this->getText("//tr[@id='row.3']/td[3]"));
            $this->clickAndWait("link=Quantity");
            $this->assertEquals("2", $this->getText("//tr[@id='row.7']/td[3]"));
            $this->assertEquals("3", $this->getText("//tr[@id='row.8']/td[3]"));
            $this->assertEquals("11", $this->getText("//tr[@id='row.9']/td[3]"));
            $this->assertEquals("16", $this->getText("//tr[@id='row.10']/td[3]"));
            $this->clickAndWait("link=Date");
            $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
            $this->assertEquals("12 EN product šÄßüл", $this->getText("//tr[@id='row.2']/td[4]"));
            $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.3']/td[4]"));
            $this->clickAndWait("link=Description");
            $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[4]"));
            $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.2']/td[4]"));
            $this->assertEquals("12 EN product šÄßüл", $this->getText("//tr[@id='row.3']/td[4]"));
            $this->assertEquals("4.50", $this->getText("//tr[@id='row.1']/td[5]"));
            $this->clickAndWait("link=Date");
            $this->assertEquals("19.80", $this->getText("//tr[@id='row.1']/td[5]"));
            $this->assertEquals("32.00", $this->getText("//tr[@id='row.2']/td[5]"));
            $this->assertEquals("4.50", $this->getText("//tr[@id='row.3']/td[5]"));
            $this->clickAndWait("link=Price");
            $this->assertEquals("4.50", $this->getText("//tr[@id='row.7']/td[5]"));
            $this->assertEquals("19.80", $this->getText("//tr[@id='row.8']/td[5]"));
            $this->assertEquals("32.00", $this->getText("//tr[@id='row.9']/td[5]"));
    }

    /**
     * searching News
     * @group admin
     * @group search_sort
     */
    public function testSearchNews()
    {
        $this->loginAdmin("Customer Info", "News");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //fields have different names in PE and EE versions
        $this->type("where[oxnews][oxdate]", "2007");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2007-12-20"));
        $this->assertTrue($this->isElementPresent("link=2007-12-02"));
        $this->assertTrue($this->isElementPresent("link=2007-11-02"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("2007", $this->getValue("where[oxnews][oxdate]"));
        $this->type("where[oxnews][oxdate]", "2007-11-02");
        $this->clickAndWait("submitit");
        $this->assertEquals("2007-11-02", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->type("where[oxnews][oxdate]", "");
        $this->type("where[oxnews][oxshortdesc]", "[DE]");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=7 [DE] Test news šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=5 [DE] Test news šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test news 2 [DE] šÄßüл"));
        $this->assertEquals("[DE]", $this->getValue("where[oxnews][oxshortdesc]"));
        $this->type("where[oxnews][oxshortdesc]", "3 [DE] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("3 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2007-12-02", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertFalse($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxnews][oxshortdesc]", "");
        $this->type("where[oxnews][oxdate]", "2008");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-02-12"));
        $this->assertTrue($this->isElementPresent("link=2008-01-05"));
        $this->assertTrue($this->isElementPresent("link=2008-01-01"));
        $this->assertEquals("2008", $this->getValue("where[oxnews][oxdate]"));
        $this->type("where[oxnews][oxdate]", "2008-02-03");
        $this->clickAndWait("submitit");
        $this->assertEquals("2008-02-03", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("4 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->type("where[oxnews][oxdate]", "");
        $this->type("where[oxnews][oxshortdesc]", "[EN] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=7 [EN] Test news šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=5 [EN] Test news šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Test news 2 [EN] šÄßüл"));
        $this->type("where[oxnews][oxshortdesc]", "4 [EN]");
        $this->clickAndWait("submitit");
        $this->assertEquals("4 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2008-02-03", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("4 [EN]", $this->getValue("where[oxnews][oxshortdesc]"));
        $this->type("where[oxnews][oxshortdesc]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting News
     * @group admin
     * @group search_sort
     */
    public function testSortNews()
    {
        $this->loginAdmin("Customer Info", "News");
        //sorting
        $this->clickAndWait("link=Date");
        $this->assertEquals("2008-02-12", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2008-02-03", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2008-02-02", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("7 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("4 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("5 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] [EN] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 [EN] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Date");
        $this->assertEquals("2008-02-12", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2008-02-03", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2008-02-02", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("[last] [DE] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("6 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("7 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last] [DE] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //deleting last item to check if navigation is correct
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last] [DE] Test news šÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->clickAndConfirm("del.1");
        $this->clickAndConfirm("del.1"); //there are 2 products in this page
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 [DE] Test news šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
    }

    /**
     * searching and sorting Newsletter
     * @group admin
     * @group search_sort
     */
    public function testSearchSortNewsletter()
    {
            $this->loginAdmin("Customer Info", "Newsletter");
            //search
            $this->type("where[oxnewsletter][oxtitle]", "newsletter 1 šÄßüл");
            $this->clickAndWait("submitit");
            $this->assertEquals("1 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
            $this->type("where[oxnewsletter][oxtitle]", "newsletter [last");
            $this->clickAndWait("submitit");
            $this->assertEquals("[last] Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
            $this->assertEquals("newsletter [last", $this->getValue("where[oxnewsletter][oxtitle]"));
            $this->type("where[oxnewsletter][oxtitle]", "");
            $this->clickAndWait("submitit");
            //sorting
            $this->clickAndWait("link=Title");
            $this->assertEquals("1 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("2 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.2']/td[1]"));
            $this->assertEquals("3 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.3']/td[1]"));
            $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
            $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
            $this->clickAndWait("nav.next");
            $this->assertEquals("[last] Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
            $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
            $this->clickAndWait("nav.prev");
            $this->assertEquals("1 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
            $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
            $this->clickAndWait("nav.last");
            $this->assertEquals("[last] Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
            $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
            $this->clickAndWait("nav.first");
            $this->assertEquals("1 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
            $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
            $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
            //removing last element to check navigation
            $this->clickAndWait("nav.last");
            $this->clickAndConfirm("del.1");
            $this->assertFalse($this->isElementPresent("nav.page.1"));
            $this->assertEquals("1 Test Newsletter šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
    }

    /**
     * searchingLinks
     * @group admin
     * @group search_sort
     */
    public function testSearchLinks()
    {
        $this->loginAdmin("Customer Info", "Links");
        //search
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        //fields have different names in PE and EE versions
        $this->type("where[oxlinks][oxinsert]", "2008");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-03-01 00:00:01"));
        $this->assertTrue($this->isElementPresent("link=2008-01-17 00:00:01"));
        $this->assertTrue($this->isElementPresent("link=2008-01-01 00:00:01"));
        $this->type("where[oxlinks][oxinsert]", "2008-02-13");
        $this->clickAndWait("submitit");
        $this->assertEquals("2008-02-13 00:00:01", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("http://www.6google.com", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2008-02-13", $this->getValue("where[oxlinks][oxinsert]"));
        $this->type("where[oxlinks][oxinsert]", "");
        $this->type("where[oxlinks][oxurl]", "google");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=exact:http://www.zzgoogle.com"));
        $this->assertTrue($this->isElementPresent("link=exact:http://www.1google.com"));
        $this->assertTrue($this->isElementPresent("link=exact:http://www.zgoogle.com"));
        $this->type("where[oxlinks][oxurl]", "5google");
        $this->clickAndWait("submitit");
        $this->assertEquals("http://www.5google.com", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2008-01-01 00:00:02", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("5google", $this->getValue("where[oxlinks][oxurl]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertEquals("5google", $this->getValue("where[oxlinks][oxurl]"));
        $this->assertEquals("http://www.5google.com", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2008-01-01 00:00:02", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->type("where[oxlinks][oxurl]", "google");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=exact:http://www.1google.com"));
        $this->assertTrue($this->isElementPresent("link=exact:http://www.5google.com"));
        $this->assertTrue($this->isElementPresent("link=exact:http://www.zzgoogle.com"));
        $this->type("where[oxlinks][oxurl]", "");
        $this->type("where[oxlinks][oxinsert]", "2004");
        $this->clickAndWait("submitit");
        $this->type("where[oxlinks][oxinsert]", "2008");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-01-17 00:00:01"));
        $this->assertTrue($this->isElementPresent("link=2008-01-01 00:00:03"));
        $this->assertTrue($this->isElementPresent("link=2008-03-01 00:00:01"));
        $this->assertEquals("2008", $this->getValue("where[oxlinks][oxinsert]"));
        $this->type("where[oxlinks][oxinsert]", "2008-01-01 00:00:03");
        $this->clickAndWait("submitit");
        $this->assertEquals("2008-01-01 00:00:03", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("http://www.zgoogle.com", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2008-01-01 00:00:03", $this->getValue("where[oxlinks][oxinsert]"));
        $this->type("where[oxlinks][oxinsert]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Links
     * @group admin
     * @group search_sort
     */
    public function testSortLinks()
    {
        $this->loginAdmin("Customer Info", "Links");
        //sorting
        $this->clickAndWait("link=Date");
        $this->assertEquals("2008-03-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2008-02-13 00:00:01", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("2008-02-01 00:00:01", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("http://www.zzgoogle.com", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("http://www.6google.com", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("http://www.2google.com", $this->getText("//tr[@id='row.4']/td[2]"));
        $this->clickAndWait("link=URL");
        $this->assertEquals("http://www.1google.com", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("http://www.2google.com", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("http://www.3google.com", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Date");
        $this->assertEquals("2008-03-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("2004-01-01 00:00:02", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("2008-03-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=URL");
        $this->assertEquals("http://www.1google.com", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("http://www.2google.com", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("http://www.3google.com", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("2008-01-01 00:00:04", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("2008-02-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2004-01-01 00:00:02", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->clickAndWait("link=Date");
        $this->assertEquals("2008-03-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("2008-02-13 00:00:01", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("2008-02-01 00:00:01", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("2004-01-01 00:00:02", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("2008-03-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //removing last element to check navigation
        $this->clickAndWait("nav.last");
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("2008-03-01 00:00:01", $this->getText("//tr[@id='row.2']/td[1]"));
    }

    /**
     * searching CMS Pages
     * @group admin
     * @group search_sort
     */
    public function testSearchCmsPages()
    {
        $this->loginAdmin("Customer Info", "CMS Pages");
        //search
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->assertEquals("Page 1 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("All", $this->getSelectedLabel("folder"));
        $this->selectAndWait("folder", "label=Customer information");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("link=Title");
        //$this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        //$this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("link=1 [DE] content šÄßüл"));
        $this->assertEquals("Customer information", $this->getSelectedLabel("folder"));
        $this->selectAndWait("folder", "label=None");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("None", $this->getSelectedLabel("folder"));
        $this->selectAndWait("folder", "label=All");
        $this->assertEquals("Page 1 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->type("where[oxcontents][oxtitle]", "[DE]");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1 [DE] content šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] [DE] content šÄßüл"));
        $this->type("where[oxcontents][oxtitle]", "1 [DE] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertEquals("1 [DE] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("[last]testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1 [DE] šÄßüл", $this->getValue("where[oxcontents][oxtitle]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxcontents][oxtitle]", "");
        $this->type("where[oxcontents][oxloadid]", "testcontent");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1testcontent"));
        $this->assertTrue($this->isElementPresent("link=[last]testcontent"));
        $this->type("where[oxcontents][oxloadid]", "1 testcontent");
        $this->clickAndWait("submitit");
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("[last] [DE] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1 testcontent", $this->getValue("where[oxcontents][oxloadid]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->selectAndWaitFrame("changelang", "label=English", "edit");
        $this->assertEquals("1 testcontent", $this->getValue("where[oxcontents][oxloadid]"));
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->type("where[oxcontents][oxloadid]", "");
        $this->type("where[oxcontents][oxtitle]", "[EN] šÄßüл");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=3 [EN] content šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=[last] [EN] content šÄßüл"));
        $this->type("where[oxcontents][oxtitle]", "3 [EN]");
        $this->clickAndWait("submitit");
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3 [EN]", $this->getValue("where[oxcontents][oxtitle]"));
        $this->type("where[oxcontents][oxtitle]", "");
        $this->type("where[oxcontents][oxloadid]", "test");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1testcontent"));
        $this->assertTrue($this->isElementPresent("link=[last]testcontent"));
        $this->type("where[oxcontents][oxloadid]", "1test");
        $this->clickAndWait("submitit");
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("1test", $this->getValue("where[oxcontents][oxloadid]"));
        $this->type("where[oxcontents][oxloadid]", "");
        $this->clickAndWait("submitit");
        $this->assertEquals("All", $this->getSelectedLabel("folder"));
        $this->selectAndWait("folder", "label=Customer information");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[1]"));
        $this->clickAndWait("link=Title", "nav.site");
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("link=3 [EN] content šÄßüл"));
        $this->assertEquals("Customer information", $this->getSelectedLabel("folder"));
        $this->selectAndWait("folder", "label=All");
        $this->assertEquals("Page 1 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
    }

    /**
     * sorting CMS Pages
     * @group admin
     * @group search_sort
     */
    public function testSortCmsPages()
    {
        $this->loginAdmin("Customer Info", "CMS Pages");
        //sorting
        $this->clickAndWait("link=Title");
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("About Us", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Bargain", $this->getText("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("oximpressum", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("oxbargain", $this->getText("//tr[@id='row.5']/td[2]"));
        $this->clickAndWait("link=Ident");
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("oxadminorderemail", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertFalse($this->isElementPresent("link=oximpressum"));
        $this->assertEquals("Page 1 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("Page 2 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("link=oximpressum"));
        $this->clickAndWait("nav.last");
        $this->assertEquals("Page 5 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.5'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("[last]testcontent", $this->getText("//tr[@id='row.9']/td[2]"));
        $this->clickAndWait("nav.prev");
        $this->assertEquals("Page 4 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.4'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("link=oxuserorderemail"));
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->selectAndWaitFrame("changelang", "label=Deutsch", "edit");
        $this->clickAndWait("link=Title");
        $this->assertEquals("1 [DE] content šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("AGB", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("Benutzer geblockt", $this->getText("//tr[@id='row.7']/td[1]"));
        $this->assertEquals("[last]testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("oxagb", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("oxblocked", $this->getText("//tr[@id='row.7']/td[2]"));
        $this->clickAndWait("link=Ident");
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("oxadminorderemail", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertFalse($this->isElementPresent("link=oximpressum"));
        $this->assertFalse($this->isElementPresent("link=oxnewstlerinfo"));
        $this->clickAndWait("nav.next");
        $this->assertTrue($this->isElementPresent("link=oximpressum"));
        $this->assertEquals("Page 2 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last]testcontent", $this->getText("//tr[@id='row.9']/td[2]"));
        $this->clickAndWait("nav.prev");
        $this->assertTrue($this->isElementPresent("link=oxuserorderemail"));
        $this->assertEquals("Page 4 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.4'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("Page 1 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->assertEquals("1testcontent", $this->getText("//tr[@id='row.1']/td[2]"));
        //deleting last element to check if navigation is correct
        $this->clickAndWait("nav.last");
        $this->assertEquals("[last]testcontent", $this->getText("//tr[@id='row.9']/td[2]"));
        $this->clickAndConfirm("del.1");
        $this->assertEquals("Page 5 / 5", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.5'][@class='pagenavigation pagenavigationactive']");
        $this->assertTrue($this->isElementPresent("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.9']/td[2]"));
    }

    /**
     * searching Price Alert
     * @group admin
     * @group main
     * @group search_sort
     */
    public function testSearchPriceAlert()
    {
        $this->loginAdmin("Customer Info", "Price Alert");
        //search
        $this->type("where[oxpricealarm][oxemail]", "birute02");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=birute02@nfq.lt"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[1]"));
        $this->type("where[oxpricealarm][oxemail]", "birute04");
        $this->clickAndWait("submitit");
        $this->assertEquals("birute04@nfq.lt", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("birute04", $this->getValue("where[oxpricealarm][oxemail]"));
        $this->type("where[oxpricealarm][oxemail]", "");
        $this->type("where[oxuser][oxlname]", "2user");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2UserSurnamešÄßüл 2useršÄßüл"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[2]"));
        $this->type("where[oxuser][oxlname]", "3user");
        $this->clickAndWait("submitit");
        $this->assertEquals("3UserSurnamešÄßüл 3useršÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("3user", $this->getValue("where[oxuser][oxlname]"));
        $this->type("where[oxuser][oxlname]", "");
        $this->clickAndWait("submitit");
        $this->type("where[oxpricealarm][oxinsert]", "2007-12");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2007-12-17 00:00:09"));
        $this->assertTrue($this->isElementPresent("link=2007-12-12 00:00:01"));
        $this->assertTrue($this->isElementPresent("link=2007-12-06 00:00:08"));
        $this->type("where[oxpricealarm][oxinsert]", "2007-12 00:00:04");
        $this->clickAndWait("submitit");
        $this->assertEquals("2007-12-13 00:00:04", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("2007-12 00:00:04", $this->getValue("where[oxpricealarm][oxinsert]"));
        $this->type("where[oxpricealarm][oxinsert]", "");
        $this->type("where[oxpricealarm][oxsended]", "2008-01-01");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=2008-01-01 00:00:01"));
        $this->assertTrue($this->isElementPresent("link=2008-01-01 00:00:05"));
        $this->assertFalse($this->isElementPresent("//tr[@id='row.3']/td[4]"));
        $this->type("where[oxpricealarm][oxsended]", "2008-01-01 05");
        $this->clickAndWait("submitit");
        $this->assertEquals("2008-01-01 00:00:05", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("2008-01-01 05", $this->getValue("where[oxpricealarm][oxsended]"));
        $this->type("where[oxpricealarm][oxsended]", "");
        $this->type("where[oxarticles][oxtitle]", "en 1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=11 EN product šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=12 EN product šÄßüл"));
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.1']/td[1]/div/a"));
        $this->type("where[oxarticles][oxtitle]", "en 15");
        $this->clickAndWait("submitit");
        $this->assertEquals("15 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertFalse($this->isElementPresent("row.2"));
        $this->assertEquals("en 15", $this->getValue("where[oxarticles][oxtitle]"));
        $this->type("where[oxarticles][oxtitle]", "");
        $this->type("where[oxpricealarm][oxprice]", "9");
        $this->clickAndWait("submitit");
        $this->assertEquals("9,00 EUR", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->type("where[oxpricealarm][oxprice]", "8");
        $this->clickAndWait("submitit");
        $this->assertEquals("8,00 EUR", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("8", $this->getValue("where[oxpricealarm][oxprice]"));
        $this->type("where[oxpricealarm][oxprice]", "");
        $this->type("where[oxarticles][oxprice]", "1");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1,50 EUR"));
        $this->assertTrue($this->isElementPresent("link=1,70 EUR"));
        $this->assertTrue($this->isElementPresent("link=1,80 EUR"));
        $this->type("where[oxarticles][oxprice]", "1.8");
        $this->clickAndWait("submitit");
        $this->assertTrue($this->isElementPresent("link=1,80 EUR"));
        $this->assertEquals("1,80 EUR", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("1,80 EUR", $this->getText("//tr[@id='row.2']/td[7]"));
        $this->assertTrue($this->isElementPresent("//tr[@id='row.2']/td[7]"));
        $this->assertEquals("1.8", $this->getValue("where[oxarticles][oxprice]"));
        $this->type("where[oxarticles][oxprice]", "");
        $this->clickAndWait("submitit");
    }

    /**
     * sorting Price Alert
     * @group admin
     * @group search_sort
     */
    public function testSortPriceAlert()
    {
        $this->loginAdmin("Customer Info", "Price Alert");
        //sorting
        $this->clickAndWait("link=E-mail");
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("birute02@nfq.lt", $this->getText("//tr[@id='row.2']/td[1]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.3']/td[1]"));
        $this->assertEquals("birute03@nfq.lt", $this->getText("//tr[@id='row.4']/td[1]"));
        $this->assertEquals("birute04@nfq.lt", $this->getText("//tr[@id='row.5']/td[1]"));
        $this->assertEquals("birute05@nfq.lt", $this->getText("//tr[@id='row.6']/td[1]"));
        $this->clickAndWait("link=Confirmation Date");
        $this->assertEquals("2007-09-14 00:00:08", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("2007-10-09 00:00:08", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("2007-11-11 00:00:06", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertEquals("6UserSurnamešÄßüл 6useršÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("2UserSurnamešÄßüл 2useršÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("6UserSurnamešÄßüл 6useršÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->clickAndWait("link=Name");
        $this->assertEquals("1UserSurnamešÄßüл 1useršÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertEquals("1UserSurnamešÄßüл 1useršÄßüл", $this->getText("//tr[@id='row.2']/td[2]"));
        $this->assertEquals("2UserSurnamešÄßüл 2useršÄßüл", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("2UserSurnamešÄßüл 2useršÄßüл", $this->getText("//tr[@id='row.4']/td[2]"));
        $this->assertEquals("3UserSurnamešÄßüл 3useršÄßüл", $this->getText("//tr[@id='row.5']/td[2]"));
        $this->clickAndWait("link=Shipping Date");
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.1']/td[4]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.2']/td[4]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getText("//tr[@id='row.3']/td[4]"));
        $this->assertEquals("2008-01-01 00:00:01", $this->getText("//tr[@id='row.4']/td[4]"));
        $this->assertEquals("2008-01-01 00:00:05", $this->getText("//tr[@id='row.5']/td[4]"));
        $this->assertEquals("2008-01-02 00:00:01", $this->getText("//tr[@id='row.6']/td[4]"));
        $this->clickAndWait("link=Product");
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.2']/td[5]"));
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.3']/td[5]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.4']/td[5]"));
        $this->assertEquals("11 EN product šÄßüл", $this->getText("//tr[@id='row.5']/td[5]"));
        //$this->assertEquals("11,00 EUR", $this->getText("//tr[@id='row.8']/td[6]"));
        $this->clickAndWait("link=Cust. Price");
        $this->assertEquals("1,00 EUR", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("2,00 EUR", $this->getText("//tr[@id='row.2']/td[6]"));
        $this->assertEquals("3,00 EUR", $this->getText("//tr[@id='row.3']/td[6]"));
        $this->assertEquals("4,00 EUR", $this->getText("//tr[@id='row.4']/td[6]"));
        $this->assertEquals("1,50 EUR", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("1,80 EUR", $this->getText("//tr[@id='row.2']/td[7]"));
        $this->assertEquals("1,70 EUR", $this->getText("//tr[@id='row.3']/td[7]"));
        $this->clickAndWait("link=Reg. Price");
        $this->assertEquals("1,50 EUR", $this->getText("//tr[@id='row.1']/td[7]"));
        $this->assertEquals("1,50 EUR", $this->getText("//tr[@id='row.2']/td[7]"));
        $this->assertEquals("1,50 EUR", $this->getText("//tr[@id='row.3']/td[7]"));
        $this->assertEquals("1,70 EUR", $this->getText("//tr[@id='row.4']/td[7]"));
        $this->assertEquals("1,70 EUR", $this->getText("//tr[@id='row.5']/td[7]"));
        $this->clickAndWait("link=Product");
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.next");
        $this->assertEquals("15 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.prev");
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.last");
        $this->assertEquals("15 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("Page 2 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.2'][@class='pagenavigation pagenavigationactive']");
        $this->clickAndWait("nav.first");
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("Page 1 / 2", $this->getText("nav.site"));
        $this->assertElementPresent("//a[@id='nav.page.1'][@class='pagenavigation pagenavigationactive']");
        //testing navigation
        $this->clickAndWait("nav.last");
        $this->clickAndConfirm("del.1");
        $this->assertFalse($this->isElementPresent("nav.page.1"));
        $this->assertEquals("1 EN product šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
    }
}
