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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Discount_Main_Ajax class
 */
class Unit_Admin_DiscountMainAjaxTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = 'a7c40f631fc920687.20179984', oxtype = 'oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = 'a7c40f6320aeb2ec2.72885259', oxtype = 'oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = 'a7c40f6321c6f6109.43859248', oxtype = 'oxcountry'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2discount where oxdiscountid like '_test%'");

        parent::tearDown();
    }

    /**
     * DiscountMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sTable = getViewName("oxcountry");

        $oView = oxNew('discount_main_ajax');
        $sQuery = "from $sTable where $sTable.oxactive = '1'";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $this->setRequestParam("oxid", $sOxid);
        $sTable = getViewName("oxcountry");

        $oView = oxNew('discount_main_ajax');
        $sQuery = "from oxobject2discount, $sTable where $sTable.oxid=oxobject2discount.oxobjectid";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxcountry'";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $sTable = getViewName("oxcountry");

        $oView = oxNew('discount_main_ajax');
        $sQuery = "from $sTable where $sTable.oxactive = '1' and";
        $sQuery .= " $sTable.oxid not in ( select $sTable.oxid from oxobject2discount, $sTable where $sTable.oxid=oxobject2discount.oxobjectid";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcountry' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountMainAjax::removeDiscCountry() test case
     *
     * @return null
     */
    public function testRemoveDiscCountry()
    {
        $oView = $this->getMock("discount_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testO2DRemove1', '_testO2DRemove2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscCountry();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountMainAjax::removeDiscCountry() test case
     *
     * @return null
     */
    public function testRemoveDiscCountryAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParam("oxid", $sOxid);
        $this->setRequestParam("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_main_ajax');
        $oView->removeDiscCountry();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountMainAjax::addDiscCountry() test case
     *
     * @return null
     */
    public function testAddDiscCountry()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $oView = $this->getMock("discount_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('oxidmiddlecust', 'oxidgoodcust')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscCountry();
        $this->assertEquals(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountMainAjax::addDiscCountry() test case
     *
     * @return null
     */
    public function testAddDiscCountryAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $this->setRequestParam("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxcountry where oxactive='1'");

        $oView = oxNew('discount_main_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));

        $oView->addDiscCountry();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));
    }

}