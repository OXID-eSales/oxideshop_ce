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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Payment_Main_Ajax class
 */
class PaymentMainAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemove1', oxobjectid='_testPayRemove1', oxgroupsid='_testRemoveGroup1'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemove2', oxobjectid='_testPayRemove2', oxgroupsid='_testRemoveGroup2'");

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemoveAll1', oxgroupsid='_testGroup1', oxobjectid='_testPayRemoveAll'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemoveAll2', oxgroupsid='_testGroup2', oxobjectid='_testPayRemoveAll'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemoveAll3', oxgroupsid='_testGroup3', oxobjectid='_testPayRemoveAll'");

        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup1', oxtitle='_testGroup1', oxactive=1");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup2', oxtitle='_testGroup2', oxactive=1");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup3', oxtitle='_testGroup3', oxactive=1");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2group where oxid LIKE '\_testPayRemove%'");

        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup1'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup2'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup3'");

        parent::tearDown();
    }

    /**
     * PaymentMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('payment_main_ajax');
        $this->assertEquals("from oxv_oxgroups_de", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('payment_main_ajax');
        $this->assertEquals("from oxv_oxgroups_de where  oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where  oxobject2group.oxobjectid = '" . $sSynchoxid . "' and oxobject2group.oxgroupsid = oxv_oxgroups_de.oxid )", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('payment_main_ajax');
        $this->assertEquals("from oxv_oxgroups_de, oxobject2group where  oxobject2group.oxobjectid = '" . $sOxid . "' and oxobject2group.oxgroupsid = oxv_oxgroups_de.oxid", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('payment_main_ajax');
        $this->assertEquals("from oxv_oxgroups_de, oxobject2group where  oxobject2group.oxobjectid = '" . $sOxid . "' and oxobject2group.oxgroupsid = oxv_oxgroups_de.oxid and  oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where  oxobject2group.oxobjectid = '" . $sSynchoxid . "' and oxobject2group.oxgroupsid = oxv_oxgroups_de.oxid )", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentMainAjax::removePayGroup() test case
     *
     * @return null
     */
    public function testRemovePayGroup()
    {
        $oView = $this->getMock("payment_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayRemove1', '_testPayRemove2')));

        $sSql = "select count(oxid) from oxobject2group where oxid in ('_testPayRemove1', '_testPayRemove2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removePayGroup();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * PaymentMainAjax::removePayGroup() test case
     *
     * @return null
     */
    public function testRemovePayGroupAll()
    {
        $sOxid = '_testPayRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid = '" . $sOxid . "'";
        $oView = oxNew('payment_main_ajax');
        $this->assertEquals(3, oxDb::getDb()->getOne($sSql));
        $oView->removePayGroup();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * PaymentMainAjax::addPayGroup() test case
     *
     * @return null
     */
    public function testAddPayGroup()
    {
        $sSynchoxid = '_testPayAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("payment_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayAdd1', '_testPayAdd2')));

        $oView->addPayGroup();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * PaymentMainAjax::addPayGroup() test case
     *
     * @return null
     */
    public function testAddPayGroupAll()
    {
        $sSynchoxid = '_testPayAddAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxv_oxgroups_de.oxid) from oxv_oxgroups_de where  oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where  oxobject2group.oxobjectid = '" . $sSynchoxid . "' and oxobject2group.oxgroupsid = oxv_oxgroups_de.oxid )");

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("payment_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayAdd1', '_testPayAdd2')));

        $oView->addPayGroup();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
