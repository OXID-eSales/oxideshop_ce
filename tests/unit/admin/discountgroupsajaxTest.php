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
 * Tests for Discount_Groups_Ajax class
 */
class Unit_Admin_DiscountGroupsAjaxTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = 'oxidsmallcust', oxtype = 'oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = 'oxidmiddlecust', oxtype = 'oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = 'oxidgoodcust', oxtype = 'oxgroups'");
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
     * DiscountGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sGroupTable = getViewName("oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $sQuery = "from $sGroupTable where 1";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam("oxid", $sOxid);
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $sGroupTable = getViewName("oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $sQuery = "from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxgroups'  and";
        $sQuery .= " $sGroupTable.oxid not in ( select $sGroupTable.oxid from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxgroups' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $sGroupTable = getViewName("oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $sQuery = "from $sGroupTable where 1  and";
        $sQuery .= " $sGroupTable.oxid not in ( select $sGroupTable.oxid from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxgroups' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountGroupsAjax::removeDiscGroup() test case
     *
     * @return null
     */
    public function testRemoveDiscGroup()
    {
        $oView = $this->getMock("discount_groups_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testO2DRemove1', '_testO2DRemove2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscGroup();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountGroupsAjax::removeDiscGroup() test case
     *
     * @return null
     */
    public function testRemoveDiscGroupAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParam("oxid", $sOxid);
        $this->setRequestParam("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_groups_ajax');
        $oView->removeDiscGroup();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountGroupsAjax::addDiscGroup() test case
     *
     * @return null
     */
    public function testAddDiscGroup()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $oView = $this->getMock("discount_groups_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('oxidmiddlecust', 'oxidgoodcust')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscGroup();
        $this->assertEquals(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountGroupsAjax::addDiscGroup() test case
     *
     * @return null
     */
    public function testAddDiscGroupAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $this->setRequestParam("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));

        $oView->addDiscGroup();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));
    }

}