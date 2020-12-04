<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Payment_Country_Ajax class
 */
class PaymentCountryAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testPayRemove1', oxobjectid='_testPayRemove'");
        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testPayRemove2', oxobjectid='_testPayRemove'");

        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testPayRemoveAll1', oxpaymentid='_testPayRemoveAll', oxobjectid='_testCountry1', oxtype = 'oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testPayRemoveAll2', oxpaymentid='_testPayRemoveAll', oxobjectid='_testCountry2', oxtype = 'oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testPayRemoveAll3', oxpaymentid='_testPayRemoveAll', oxobjectid='_testCountry3', oxtype = 'oxcountry'");

        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry1', oxtitle='_testCountry1', oxactive=1");
        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry2', oxtitle='_testCountry2', oxactive=1");
        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry3', oxtitle='_testCountry3', oxactive=1");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxobject2payment where oxobjectid='_testPayRemove'");
        oxDb::getDb()->execute("delete from oxobject2payment where oxpaymentid='_testPayRemoveAll'");
        oxDb::getDb()->execute("delete from oxobject2payment where oxpaymentid='_testPayAdd'");
        oxDb::getDb()->execute("delete from oxobject2payment where oxpaymentid='_testPayAddAll'");

        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry1'");
        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry2'");
        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry3'");

        parent::tearDown();
    }

    public function testGetQuery()
    {
        $oView = oxNew('payment_country_ajax');
        $this->assertEquals("from oxv_oxcountry_de where oxv_oxcountry_de.oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('payment_country_ajax');
        $this->assertEquals("from oxv_oxcountry_de where oxv_oxcountry_de.oxactive = '1' and oxv_oxcountry_de.oxid not in ( select oxv_oxcountry_de.oxid from oxobject2payment left join oxv_oxcountry_de on oxv_oxcountry_de.oxid=oxobject2payment.oxobjectid where oxobject2payment.oxpaymentid = '" . $sSynchoxid . "' and oxobject2payment.oxtype = 'oxcountry' )", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('payment_country_ajax');
        $this->assertEquals("from oxobject2payment left join oxv_oxcountry_de on oxv_oxcountry_de.oxid=oxobject2payment.oxobjectid where oxv_oxcountry_de.oxactive = '1' and oxobject2payment.oxpaymentid = '" . $sOxid . "' and oxobject2payment.oxtype = 'oxcountry'", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('payment_country_ajax');
        $this->assertEquals("from oxobject2payment left join oxv_oxcountry_de on oxv_oxcountry_de.oxid=oxobject2payment.oxobjectid where oxv_oxcountry_de.oxactive = '1' and oxobject2payment.oxpaymentid = '" . $sOxid . "' and oxobject2payment.oxtype = 'oxcountry' and oxv_oxcountry_de.oxid not in ( select oxv_oxcountry_de.oxid from oxobject2payment left join oxv_oxcountry_de on oxv_oxcountry_de.oxid=oxobject2payment.oxobjectid where oxobject2payment.oxpaymentid = '" . $sSynchoxid . "' and oxobject2payment.oxtype = 'oxcountry' )", trim($oView->UNITgetQuery()));
    }

    /**
     * PaymentCountryAjax::removePayCountry() test case
     *
     * @return null
     */
    public function testRemovePayFromCountry()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PaymentCountryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayRemove1', '_testPayRemove2')));

        $sSql = "select count(oxid) from oxobject2payment where oxid in ('_testPayRemove1', '_testPayRemove2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removePayCountry();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * PaymentCountryAjax::removePayCountry() test case
     *
     * @return null
     */
    public function testRemovePayFromCountryAll()
    {
        $sOxid = '_testPayRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxid) from oxobject2payment where oxpaymentid = '" . $sOxid . "'";
        $oView = oxNew('payment_country_ajax');
        $this->assertEquals(3, oxDb::getDb()->getOne($sSql));
        $oView->removePayCountry();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * PaymentCountryAjax::addPayCountry() test case
     *
     * @return null
     */
    public function testAddPayToCountry()
    {
        $sSynchoxid = '_testPayAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2payment where oxpaymentid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PaymentCountryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayAdd1', '_testPayAdd2')));

        $oView->addPayCountry();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * PaymentCountryAjax::addPayCountry() test case
     *
     * @return null
     */
    public function testAddPayToCountryAll()
    {
        $sSynchoxid = '_testPayAddAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxv_oxcountry_de.oxid) from oxv_oxcountry_de where oxv_oxcountry_de.oxactive = '1' and oxv_oxcountry_de.oxid not in ( select oxv_oxcountry_de.oxid from oxobject2payment left join oxv_oxcountry_de on oxv_oxcountry_de.oxid=oxobject2payment.oxobjectid where oxobject2payment.oxpaymentid = '" . $sSynchoxid . "' and oxobject2payment.oxtype = 'oxcountry')");

        $sSql = "select count(oxid) from oxobject2payment where oxpaymentid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PaymentCountryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayAdd1', '_testPayAdd2')));

        $oView->addPayCountry();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
