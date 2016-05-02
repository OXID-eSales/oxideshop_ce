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
namespace Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Discount_Users_Ajax class
 */
class DiscountUsersAjaxTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        $shopId = $this->getTestConfig()->getShopEdition() == 'EE' ? '1' : 'oxbaseshop';
        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser1', oxusername='_testUserName1', oxshopid='$shopId'");
        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser2', oxusername='_testUserName2', oxshopid='$shopId'");
        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser3', oxusername='_testUserName3', oxshopid='$shopId'");

        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testUser1', oxtype = 'oxuser'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testUser2', oxtype = 'oxuser'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testUser3', oxtype = 'oxuser'");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2discount where oxdiscountid like '_test%'");
        oxDb::getDb()->execute("delete from oxuser where oxid like '_test%'");

        parent::tearDown();
    }

    /**
     * DiscountUsersAjax::_getQuery() test case
     */
    public function testGetQuery()
    {
        $sUserTable = getViewName("oxuser");

        $oView = oxNew('discount_users_ajax');
        $sQuery = "from $sUserTable where 1  and oxshopid = '" . $this->getShopId() . "'";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountUsersAjax::_getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sUserTable = getViewName("oxuser");

        $oView = oxNew('discount_users_ajax');
        $sQuery = "from oxobject2group left join $sUserTable on $sUserTable.oxid = oxobject2group.oxobjectid";
        $sQuery .= " where oxobject2group.oxgroupsid = '_testOxid' and $sUserTable.oxshopid = '" . $this->getShopId() . "'  and";
        $sQuery .= " $sUserTable.oxid not in ( select $sUserTable.oxid from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxuser' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountUsersAjax::_getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sUserTable = getViewName("oxuser");

        $oView = oxNew('discount_users_ajax');
        $sQuery = "from $sUserTable where 1  and oxshopid = '" . $this->getShopId() . "'  and";
        $sQuery .= " $sUserTable.oxid not in ( select $sUserTable.oxid from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxuser' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountUsersAjax::removeDiscUser() test case
     */
    public function testRemoveDiscUser()
    {
        $oView = $this->getMock("discount_users_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testO2DRemove1', '_testO2DRemove2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscUser();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountUsersAjax::removeDiscUser() test case
     */
    public function testRemoveDiscUserAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_users_ajax');
        $oView->removeDiscUser();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountUsersAjax::addDiscUser() test case
     */
    public function testAddDiscUser()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock("discount_users_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testNewUser1', '_testNewUser2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscUser();
        $this->assertEquals(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountUsersAjax::addDiscUser() test case
     */
    public function testAddDiscUserAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxuser");

        $oView = oxNew('discount_users_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));

        $oView->addDiscUser();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));
    }
}
