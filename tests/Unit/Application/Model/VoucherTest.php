<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use oxVoucherException;
use \stdclass;
use \oxDb;
use \oxRegistry;

/**
 * Testing oxvoucherserie class
 */
class VoucherTest extends \OxidTestCase
{
    const MAX_LOOP_AMOUNT = 4;

    protected $_aSerieOxid = null;
    protected $_aVoucherOxid = array();
    protected $_sTestUserId = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myConfig = $this->getConfig();
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->oxvoucherseries__oxserienr = new oxField('Test Mod Voucher Serie', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscount = new oxField(0.99, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscounttype = new oxField('absolute', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() - (3600 * 3)), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() + (3600 * 3)), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxminimumvalue = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxshopid = new oxField($myConfig->getBaseShopId(), oxField::T_RAW);
        $oSerie->save();

        $this->_aSerieOxid[] = $oSerie->getId();
        $aGroupsToAdd = array('oxidsmallcust', 'oxidmiddlecust', 'oxidgoodcust', 'oxidadmin');
        $this->addAdditionalInfo($oSerie->getId(), $aGroupsToAdd);

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->oxvoucherseries__oxserienr = new oxField('Test Mod Voucher Serie', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscount = new oxField(9.99, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscounttype = new oxField('percent', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', (time() - (3600 * 3))), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', (time() + (3600 * 3))), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxminimumvalue = new oxField(100, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxshopid = new oxField($myConfig->getBaseShopId(), oxField::T_RAW);
        $oSerie->save();

        $this->_aSerieOxid[] = $oSerie->getId();
        $aGroupsToAdd = array('oxidsmallcust', 'oxidmiddlecust', 'oxidgoodcust', 'oxidcustomer');
        $this->addAdditionalInfo($oSerie->getId(), $aGroupsToAdd);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        if ($this->_sTestUserId) {
            $oUser = oxNew('oxuser');
            $oUser->delete($this->_sTestUserId);
        }
        $oSerie = oxNew('oxvoucherserie');

        foreach ($this->_aSerieOxid as $sOXID) {
            if ($oSerie->Load($sOXID)) {
                $oSerie->delete();
                $this->remAdditionalInfo($sOXID);
            }
        }
        $this->cleanUpTable('oxvouchers');
        $this->cleanUpTable('oxvoucherseries');

        parent::tearDown();
    }

    /**
     * Adding/removing test data
     */
    protected function addAdditionalInfo($sOXID, $aGroupsToAdd)
    {
        // assigning groups
        foreach ($aGroupsToAdd as $sGroupId) {
            $oNewGroup = oxNew('oxobject2group');
            $oNewGroup->oxobject2group__oxobjectid = new oxField($sOXID, oxField::T_RAW);
            $oNewGroup->oxobject2group__oxgroupsid = new oxField($sGroupId, oxField::T_RAW);
            $oNewGroup->save();
        }

        // loading for additional information
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($sOXID);

        // creating test vouchers
        for ($i = 0; $i < self::MAX_LOOP_AMOUNT; $i++) {
            $oNewVoucher = oxNew('oxvoucher');
            $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($sOXID, oxField::T_RAW);
            $oNewVoucher->oxvouchers__oxvouchernr = new oxField(($i + 1) . $sOXID, oxField::T_RAW);

            $oNewVoucher->Save();

            $this->_aVoucherOxid[$sOXID][] = $oNewVoucher->getId();
        }
    }

    protected function remAdditionalInfo($sOXID)
    {
        $myDB = oxDb::getDb();

        // removing groups assignment
        $sQ = 'delete from oxobject2group where oxobject2group.oxobjectid = "' . $sOXID . '"';
        $myDB->Execute($sQ);

        // removing vouchers
        $sQ = 'delete from oxvouchers where oxvouchers.oxvoucherserieid = "' . $sOXID . '"';
        $myDB->Execute($sQ);
    }

    /**
     * Test case:
     *     all vouchers allowed in same order
     */
    public function testAllVouchersAllowedInSameOrder()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(1, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(1, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(1, oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aSerieOxid[0];

        foreach ($this->_aVoucherOxid[$sOXID] as $i => $sId) {
            $aVouchers[$sId] = ($i + 1) . $sOXID;
        }

        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->load($this->_aVoucherOxid[$sOXID][0]);
        $this->assertTrue($oNewVoucher->checkBasketVoucherAvailability($aVouchers, 100));
    }

    /**
     * Test cases:
     *     first voucher fits fine
     */
    // second voucher does not fit because same series are not allowed
    public function testFirstVoucherAcceptableSecondNotAllowedWithSameSeries()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(1, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(1, oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aSerieOxid[0];
        $aVouchers[$this->_aVoucherOxid[$sOXID][0]] = '1' . $sOXID;
        $aVouchers[$this->_aVoucherOxid[$sOXID][1]] = '2' . $sOXID;

        // not allow with same series
        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->load($this->_aVoucherOxid[$sOXID][0]);

        try {
            $this->assertTrue($oNewVoucher->checkBasketVoucherAvailability($aVouchers, 100));
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES', $oEx->getMessage());

            return;
        }
        $this->fail('error in ' . $this->getName());
    }

    // second voucher does not fit because other series are not allowed
    public function testFirstVoucherAcceptableSecondNotAllowedWithOtherSeries()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[1]);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(1, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(0, oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aSerieOxid[0];
        $aVouchers[$this->_aVoucherOxid[$sOXID][0]] = '1' . $sOXID;

        // not allow with same series
        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->load($this->_aVoucherOxid[$sOXID][0]);

        $sOXID = $this->_aSerieOxid[1];
        $aVouchers[$this->_aVoucherOxid[$sOXID][0]] = '1' . $sOXID;

        try {
            $this->assertTrue($oNewVoucher->checkBasketVoucherAvailability($aVouchers, 100));
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOTALLOWEDOTHERSERIES', $oEx->getMessage());

            return;
        }
        $this->fail('error in ' . $this->getName());
    }

    /**
     * Tests for optional parameters
     */
    public function testGetVoucherByNr0()
    {
        $sNr = self::MAX_LOOP_AMOUNT + 10;
        $oNewVoucher = oxNew('oxvoucher');

        try {
            $oNewVoucher->getVoucherByNr($sNr);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oException) {
            return; //OK
        }
        $this->fail();
    }

    public function testGetVoucherByNr1()
    {
        $sNr = $this->getRandLTAmnt() . $this->_aSerieOxid[0];

        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->getVoucherByNr($sNr);
        $this->assertEquals($sNr, $oNewVoucher->oxvouchers__oxvouchernr->value);
    }

    public function testGetVoucherByNr2()
    {
        $sNr = $this->getRandLTAmnt() . $this->_aSerieOxid[0];
        $aVouchers = array($sNr => $sNr);
        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->getVoucherByNr($sNr, $aVouchers);

        $this->assertEquals($sNr, $oNewVoucher->oxvouchers__oxvouchernr->value);
    }

    public function testGetVoucherByNr3()
    {
        // There is a seria with 1 in front of $this->_aSerieOxid[0] so if use only $this->getRandLTAmnt test will faile randomly.
        $ii = '_' . $this->getRandLTAmnt();
        $sNr = $ii . $this->_aSerieOxid[0];
        $oNewVoucher = oxNew('oxvoucher');

        try {
            $oNewVoucher->getVoucherByNr($ii, array(), true);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oException) {
            return; //OK
        }
        $this->fail();
    }

    public function testGetVoucherByNrIsNull()
    {
        $oNewVoucher = oxNew('oxvoucher');
        $this->assertNull($oNewVoucher->getVoucherByNr(null, array(), true));
    }

    /**
     * Testing the correct (#2133)
     */
    public function testGetVoucherByNrUsedDateUsed()
    {
        // Create new voucher
        $sVoucherNr = '_test_testGetVoucherByNrNoDateUsed';

        $oNewVoucher = oxNew("oxvoucher");
        $oNewVoucher->oxvouchers__oxvouchernr = new oxField($sVoucherNr);
        $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($this->_aSerieOxid[0]);
        $oNewVoucher->setId('_test_testGetVoucherByNrNoDateUsed');
        $oNewVoucher->save();

        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->getVoucherByNr($sVoucherNr);

        $oNewVoucher->oxvouchers__oxdateused = new oxField(date('Y-m-d'));
        $oNewVoucher->save();

        $oNewVoucher = oxNew('oxvoucher');
        try {
            $oNewVoucher->getVoucherByNr($sVoucherNr);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; // OK
        }
        $this->fail();
    }

    /**
     * Testing the correct (#2133)
     */
    public function testGetVoucherByNrUsedOrderId()
    {
        // Create new voucher
        $sVoucherNr = '_test_testGetVoucherByNrUsedOrderId';

        $oNewVoucher = oxNew("oxvoucher");
        $oNewVoucher->oxvouchers__oxvouchernr = new oxField($sVoucherNr);
        $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($this->_aSerieOxid[0]);
        $oNewVoucher->setId('_test_testGetVoucherByNrUsedOrderId');
        $oNewVoucher->save();

        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->getVoucherByNr($sVoucherNr);

        $oNewVoucher->oxvouchers__oxorderid = new oxField(oxRegistry::getUtilsObject()->generateUID());
        $oNewVoucher->save();

        $oNewVoucher = oxNew('oxvoucher');
        try {
            $oNewVoucher->getVoucherByNr($sVoucherNr);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; // OK
        }
        $this->fail();
    }

    /**
     * checking reservations
     */
    public function testMarkAsReserved()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }
        $oNewVoucher->markAsReserved();

        // checking ...
        $iTime = time() - 3600 * 3;
        $sQ = 'select count(*) from oxvouchers where oxreserved < ' . $iTime . ' and oxvouchernr = "' . $sOXID . '"';
        if ($myDB->GetOne($sQ) > 0) {
            $this->fail('voucherserie was not marked as reserved');
        }
    }

    public function testUnMarkAsReserved()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $sQ = 'update oxvouchers set oxreserved = ' . time() . ' where oxid = "' . $sOXID . '"';
        oxDb::getDb()->Execute($sQ);

        // unmarking ...
        $oNewVoucher->unMarkAsReserved();

        // checking ...
        $iTime = time() - 3600 * 3;
        $sQ = 'select count(*) from oxvouchers where oxreserved != 0 and oxvouchernr = "' . $sOXID . '"';
        if ($myDB->GetOne($sQ) > 0) {
            $this->fail('voucherserie was not marked as unreserved');
        }
    }

    /**
     * Checking discount values
     */
    public function testGetDiscountValueABS0()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(0.99, $oNewVoucher->getDiscountValue(100));
    }

    public function testGetDiscountValuePERC()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(9.99, $oNewVoucher->getDiscountValue(100));
    }

    /**
     * Straight loaded discount does not contain any discount value,
     * so oxvoucher::getDiscountValue() usually returns 0
     */
    public function testGetDiscountValue0()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(0, $oNewVoucher->getDiscountValue(null));
    }

    /**
     * Checking availability
     */
    public function testCheckVoucherAvailability0()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->save();

        $aVouchers = null;
        $dInitPrice = 100;
        $aErrors = $oNewVoucher->checkVoucherAvailability($aVouchers, $dInitPrice);

        $this->assertEquals(true, $aErrors);
    }

    public function testCheckVoucherAvailabilityValidTime()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() - 600), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() + 600), oxField::T_RAW);
        $oSerie->save();

        $aVouchers = null;
        $dInitPrice = 100;
        $aErrors = $oNewVoucher->checkVoucherAvailability($aVouchers, $dInitPrice);

        $this->assertEquals(true, $aErrors);
    }

    public function testCheckVoucherAvailabilityInvalidTime()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() + 600), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() + 900), oxField::T_RAW);
        $oSerie->save();

        $aVouchers = null;
        $dInitPrice = 100;

        try {
            $aErrors = $oNewVoucher->checkVoucherAvailability($aVouchers, $dInitPrice);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $e) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOVOUCHER', $e->getMessage());

            return;
        }
        $this->fail();
    }

    public function testCheckVoucherAvailability1()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][0];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $sOx1 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][1];
        $sOx3 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][2];
        $aVouchers = array($sOx1 => $sOx1, $sOx3 => $sOx3);
        $dInitPrice = 100;
        try {
            $oNewVoucher->checkVoucherAvailability($aVouchers, $dInitPrice);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES', $oEx->getMessage());

            return;
        }
        $this->fail();
    }

    public function testCheckVoucherAvailability2()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $sOx1 = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $aVouchers = array($sOx1 => $sOx1);
        $dInitPrice = 100;

        try {
            $oNewVoucher->checkVoucherAvailability($aVouchers, $dInitPrice);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOTALLOWEDOTHERSERIES', $oEx->getMessage());

            return;
        }
        $this->fail();
    }

    public function testCheckBasketVoucherAvailability()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $dInitPrice = 100;

        try {
            $this->assertTrue($oNewVoucher->checkBasketVoucherAvailability($oNewVoucher, $dInitPrice), 'Basket level voucher availability check failed');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oException) {
            $this->fail('Basket level voucher availability check failed');
        }
    }

    /**
     * Checking prices when price discount is not correct
     */
    public function testIsAvailablePriceWhenPriceDiscountIsNotCorrect()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxdiscount = new oxField(100, oxField::T_RAW);
        $oSerie->save();

        $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($this->_aSerieOxid[0], oxField::T_RAW);

        $iErrorMsgId = null;
        $dPrice = 99;
        //  discount is greater than price
        try {
            $aErrors = $oNewVoucher->UNITisAvailablePrice($dPrice);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->fail();

            return;
        }
    }

    /**
     * Checking prices when price is below minimum value
     */
    public function testIsAvailablePriceWhenPriceIsBelowMinVal()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getActShopCurrencyObject'), array(), '', false);
        $myCurr = new stdclass();
        $myCurr->rate = 1000;
        $oConfig->expects($this->once())->method('getActShopCurrencyObject')->will($this->returnValue($myCurr));
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxminimumvalue = new oxField(0.01, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscounttype = new oxField(10, oxField::T_RAW);
        $oSerie->save();

        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($this->_aSerieOxid[0], oxField::T_RAW);
        $iErrorMsgId = null;
        $dPrice = 9;

        try {
            $oNewVoucher->setConfig($oConfig);
            $aErrors = $oNewVoucher->UNITisAvailablePrice($dPrice);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $sErrorMsg = $oEx->getMessage();
        }

        $this->assertEquals('ERROR_MESSAGE_VOUCHER_INCORRECTPRICE', $sErrorMsg);
    }

    /**
     * Checking availability with same series
     */
    public function testIsAvailableWithSameSeries0()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $aVouchers = null;
        $blAvailable = $oNewVoucher->UNITisAvailableWithSameSeries($aVouchers);
        $this->assertEquals(true, $blAvailable);
        $blAvailable = $oNewVoucher->UNITisAvailableWithSameSeries(array($sOXID => 'ss'));
        $this->assertEquals(true, $blAvailable);
    }

    public function testIsAvailableWithSameSeries1()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][0];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $sOx1 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][0];
        $sOx2 = $this->_aVoucherOxid[$this->_aSerieOxid[1]][1];
        $sOx3 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][1];
        $aVouchers = array($sOx1 => $sOx1, $sOx2 => $sOx2, $sOx3 => $sOx3);

        try {
            $oNewVoucher->UNITisAvailableWithSameSeries($aVouchers);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; //OK
        }
        $this->fail();
    }

    /**
     * Checking availability with other series
     */
    public function testIsAvailableWithOtherSeries0()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $aVouchers = null;
        $blAvailable = $oNewVoucher->UNITisAvailableWithOtherSeries($aVouchers);
        $this->assertEquals(true, $blAvailable);
    }

    public function testIsAvailableWithOtherSeries1()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][0];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }
        $oNewVoucher->oxvoucherseries__oxallowotherseries = new oxField(1);
        $oNewVoucher->save();
        $sOx1 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][0];
        $sOx2 = $this->_aVoucherOxid[$this->_aSerieOxid[1]][1];
        $sOx3 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][1];
        $aVouchers = array($sOx1 => $sOx1, $sOx2 => $sOx2, $sOx3 => $sOx3);

        try {
            $oNewVoucher->UNITisAvailableWithOtherSeries($aVouchers);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; //OK
        }
        $this->fail();
    }

    public function testIsAvailableWithOtherSeries2()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = $this->getProxyClass("oxvoucher");
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[1]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(true);
        $oSerie->save();
        $oNewVoucher->setNonPublicVar("_oSerie", $oSerie);
        $sOx1 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][0];
        $sOx2 = $this->_aVoucherOxid[$this->_aSerieOxid[1]][1];
        $sOx3 = $this->_aVoucherOxid[$this->_aSerieOxid[0]][1];
        $aVouchers = array($sOx1 => $sOx1, $sOx2 => $sOx2, $sOx3 => $sOx3);
        $blAvailable = $oNewVoucher->UNITisAvailableWithOtherSeries($aVouchers);
        $this->assertEquals(true, $blAvailable);
    }

    /**
     * Validating date
     */
    public function testIsValidDateCustomDateWasNotSet()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[1]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisValidDate());
    }

    public function testIsValidDate1()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxenddate = new oxField(null, oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisValidDate());
    }

    public function testIsValidDate2()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisValidDate());
    }

    public function testIsValidDate_WhenDateIsInFuture()
    {
        $this->expectException('oxVoucherException');
        $this->expectExceptionMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() + 3600), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() + 8600), oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oNewVoucher->UNITisValidDate();
    }

    public function testIsValidDate_WhenEndDateIsAutoSetInFuture()
    {
        $this->expectException('oxVoucherException');
        $this->expectExceptionMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() + 3600), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oNewVoucher->UNITisValidDate();
    }

    public function testIsValidDate3_2()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() - 8600), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() - 3600), oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        try {
            $oNewVoucher->UNITisValidDate();
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oException) {
            $this->assertEquals('MESSAGE_COUPON_EXPIRED', $oException->getMessage());

            return;
        }
        $this->fail('Expected MESSAGE_COUPON_EXPIRED');
    }

    public function testIsValidDate3_3()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[0]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() - 3600), oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        try {
            $oNewVoucher->UNITisValidDate();
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oException) {
            $this->assertEquals('MESSAGE_COUPON_EXPIRED', $oException->getMessage());

            return;
        }
        $this->fail('Expected MESSAGE_COUPON_EXPIRED');
    }

    public function testIsValidDate4()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[1]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() + 3700), oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisValidDate());
    }

    public function testIsValidDate4_1()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[1]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() - 3700), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->save();

        $this->assertEquals(true, $oNewVoucher->UNITisValidDate());
    }

    public function testIsValidDate5()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($this->_aSerieOxid[1]);
        $oSerie->oxvoucherseries__oxbegindate = new oxField(date('Y-m-d H:i:s', time() - 3700), oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField(date('Y-m-d H:i:s', time() + 3700), oxField::T_RAW);
        $oSerie->save();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisValidDate());
    }

    /**
     * Checking reservations
     */
    public function testIsNotReserved0()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $sQ = 'update oxvouchers set oxreserved = ' . time() . ' where oxid = "' . $sOXID . '"';
        $myDB->Execute($sQ);

        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        try {
            $oNewVoucher->UNITisNotReserved();
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('EXCEPTION_VOUCHER_ISRESERVED', $oEx->getMessage());

            return;
        }
        $this->fail();
    }

    public function testIsNotReserved1()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $sQ = 'update oxvouchers set oxreserved = 0 where oxid = "' . $sOXID . '"';
        $myDB->Execute($sQ);

        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisNotReserved());
    }

    /**
     * Checking for user
     */
    public function testCheckUserAvailabilityIfValid()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load('oxdefaultadmin')) {
            $this->fail('user is not available - cannot test');

            return;
        }

        $this->assertTrue($oNewVoucher->checkUserAvailability($oUser));
    }

    public function testCheckUserAvailabilityIfNotValidUserGroup()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[1]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $sUserId = 'oxdefaultadmin';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sQ = 'select oxid from oxuser where oxid != "oxdefaultadmin" ';
            $sUserId = $myDB->getOne($sQ);
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load($sUserId)) {
            $this->fail('user is not available - cannot test');

            return;
        }

        try {
            $oNewVoucher->checkUserAvailability($oUser);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP', $oEx->getMessage());

            return;
        }
        $this->fail();
    }

    public function testCheckUserAvailabilityNotValidInOtherOrder()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $sQ = 'update oxvouchers set oxuserid = "oxdefaultadmin", oxorderid = "testorder" where oxid = "' . $sOXID . '"';
        $myDB->Execute($sQ);

        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load('oxdefaultadmin')) {
            $this->fail('user is not available - cannot test');

            return;
        }

        try {
            $oNewVoucher->checkUserAvailability($oUser);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->assertEquals('ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES', $oEx->getMessage());

            return;
        }
        $this->fail();
    }

    /**
     * Checking availability on order
     */
    public function testIsAvailableInOtherOrder0()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oNewVoucher->oxvouchers__oxallowuseanother = new oxField(1, oxField::T_RAW);
        $this->assertEquals(true, $oNewVoucher->UNITisAvailableInOtherOrder(null));
    }

    public function testIsAvailableInOtherOrder1()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load('oxdefaultadmin')) {
            $this->fail('user is not available - cannot test');

            return;
        }

        $this->assertEquals(true, $oNewVoucher->UNITisAvailableInOtherOrder($oUser));
    }

    public function testIsAvailableInOtherOrder2()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $sQ = 'update oxvouchers set oxuserid = "oxdefaultadmin", oxorderid = "testorder" where oxid = "' . $sOXID . '"';
        $myDB->Execute($sQ);

        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load('oxdefaultadmin')) {
            $this->fail('user is not available - cannot test');

            return;
        }

        try {
            $oNewVoucher->UNITisAvailableInOtherOrder($oUser);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; //OK
        }
        $this->fail();
    }

    /**
     * Testing the correct (#2133)
     */
    public function testIsAvailableInOtherOrderUsedDateUsed()
    {
        $myDB = oxDb::getDb();

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $sQ = 'update oxvouchers set oxuserid = "oxdefaultadmin", oxdateused = NOW() where oxid = "' . $sOXID . '"';
        $myDB->Execute($sQ);

        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load('oxdefaultadmin')) {
            $this->fail('user is not available - cannot test');

            return;
        }

        try {
            $oNewVoucher->UNITisAvailableInOtherOrder($oUser);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; //OK
        }
        $this->fail();
    }

    /**
     * Checking for user group
     */
    public function testIsValidUserGroupNotValidGroup()
    {
        $myDB = oxDb::getDb();
        $myConfig = $this->getConfig();

        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxshopid = new oxField($myConfig->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('test', oxField::T_RAW);
        $oUser->save();
        $this->_sTestUserId = $oUser->getId();
        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . $oUser->getId() . '", "' . $myConfig->getBaseShopId() . '", "' . $oUser->getId() . '", "oxidpricec" )';
        $myDB->Execute($sQ);

        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        try {
            $this->assertEquals(false, $oNewVoucher->UNITisValidUserGroup($oUser));
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return; //OK
        }
        $this->fail();
    }

    public function testIsValidUserGroupIfValidGroup()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->Load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }

        $oUser = oxNew('oxuser');
        if (!$oUser->Load('oxdefaultadmin')) {
            $this->fail('user is not available - cannot test');

            return;
        }

        $this->assertTrue($oNewVoucher->UNITisValidUserGroup($oUser));
    }

    // user is not loaded, should throw an exception
    public function testIsValidUserGroupNoUser()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->Load($sOXID);

        try {
            $oNewVoucher->UNITisValidUserGroup(null);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            return;
        }
        $this->fail("failed testIsValidUserGroupNoUser test");
    }

    // user is not loaded, user group is not assigned,
    // returns true
    public function testIsValidUserGroupNoUserGroupSet()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $myDB = oxDb::getDb();

        // removing groups assignment
        $sQ = 'delete from oxobject2group where oxobject2group.oxobjectid = "' . $this->_aSerieOxid[0] . '"';
        $myDB->Execute($sQ);

        $oNewVoucher = oxNew('oxvoucher');
        $oNewVoucher->Load($sOXID);

        $this->assertTrue($oNewVoucher->UNITisValidUserGroup(null));
    }

    /**
     * Testing simple voucher getter
     */
    public function testGetSimpleVoucher()
    {
        $sVoucherSerie = current($this->_aSerieOxid);
        $sVoucher = current($this->_aVoucherOxid[$sVoucherSerie]);
        $oVoucher = oxNew('oxvoucher');
        $oVoucher->load($sVoucher);

        $oSimpleVoucher = new stdClass();
        $oSimpleVoucher->sVoucherId = $oVoucher->getId();
        $oSimpleVoucher->sVoucherNr = $oVoucher->oxvouchers__oxvouchernr->value;
        //$oSimpleVoucher->fVoucherdiscount = oxRegistry::getLang()->formatCurrency( $oVoucher->oxvouchers__oxdiscount->value );

        $this->assertEquals($oSimpleVoucher, $oVoucher->getSimpleVoucher());
    }

    /**
     * Testing 'used' marker
     */
    // balnk voucher will not be marked
    public function testMarkAsUsedBlankMarking()
    {
        $oVoucher = oxNew('oxvoucher');
        $oVoucher->markAsUsed('xxx', 'yyy', '');

        $this->assertNull($oVoucher->oxvouchers__oxorderid->value);
        $this->assertNull($oVoucher->oxvouchers__oxuserid->value);
        $this->assertNull($oVoucher->oxvouchers__oxdateused->value);
    }

    // barking existing voucher
    public function testMarkAsUsedExistingMarking()
    {
        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->UNITSetTime(0);

        $sVoucherSerie = current($this->_aSerieOxid);
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load($sVoucherSerie);
        $sVoucher = current($this->_aVoucherOxid[$sVoucherSerie]);

        $oVoucher = oxNew('oxvoucher');
        $oVoucher->load($sVoucher);
        $oVoucher->markAsUsed('xxx', 'yyy', $oSerie->oxvoucherseries__oxdiscount->value);

        $this->assertEquals('xxx', $oVoucher->oxvouchers__oxorderid->value);
        $this->assertEquals('yyy', $oVoucher->oxvouchers__oxuserid->value);
        $this->assertEquals('1970-01-01', $oVoucher->oxvouchers__oxdateused->value);
        $this->assertEquals($oSerie->oxvoucherseries__oxdiscount->value, $oVoucher->oxvouchers__oxdiscount->value);
    }

    /**
     * Testing get serie
     */
    public function testGetSerie()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->load($sOXID)) {
            $this->fail('can not load voucher');

            return;
        }
        $this->assertEquals($this->_aSerieOxid[0], $oNewVoucher->getSerie()->getId());
    }

    public function testGetSerieNotLoaded()
    {
        $sOXID = $this->_aVoucherOxid[$this->_aSerieOxid[0]][$this->getRandLTAmnt()];
        $oNewVoucher = oxNew('oxvoucher');
        if (!$oNewVoucher->load($sOXID)) {
            $this->fail('can not load voucher');
        }
        $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField('aaa');
        $oNewVoucher->save();
        $this->expectException('oxObjectException');
        $oNewVoucher->getSerie();
    }

    /**
     * Test case for #0002662: Wrong voucher calculation for articles selected by categorie
     *
     * @return null
     */
    public function testFor0002662()
    {
        $dPrice = 100;

        $oBasketItem1['price'] = 5.45;
        $oBasketItem1['amount'] = 100;

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxshopid = new oxField($this->getConfig()->getShopId());
        $oDiscount->oxdiscount__oxactive = new oxField(1);
        $oDiscount->oxdiscount__oxactivefrom = new oxField("0000-00-00 00:00:00");
        $oDiscount->oxdiscount__oxactiveto = new oxField("0000-00-00 00:00:00");
        $oDiscount->oxdiscount__oxtitle = new oxField("test");
        $oDiscount->oxdiscount__oxamount = new oxField(1);
        $oDiscount->oxdiscount__oxprice = new oxField(0);
        $oDiscount->oxdiscount__oxamountto = new oxField(MAX_64BIT_INTEGER);
        $oDiscount->oxdiscount__oxpriceto = new oxField(MAX_64BIT_INTEGER);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%');
        $oDiscount->oxdiscount__oxaddsum = new oxField(10);
        $oDiscount->oxdiscount__oxitmartid = new oxField();
        $oDiscount->oxdiscount__oxitmamount = new oxField();
        $oDiscount->oxdiscount__oxitmmultiple = new oxField();

        $oVoucher = $this->getMock(\OxidEsales\Eshop\Application\Model\Voucher::class, array("_getSerieDiscount", "_getBasketItems", "isAdmin"));
        $oVoucher->expects($this->once())->method('_getSerieDiscount')->will($this->returnValue($oDiscount));
        $oVoucher->expects($this->once())->method('_getBasketItems')->will($this->returnValue(array($oBasketItem1)));
        $oVoucher->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertEquals(54.5, $oVoucher->UNITgetCategoryDiscoutValue($dPrice));
    }

    /**
     * Returns random LT Amount.
     *
     * @return int
     */
    protected function getRandLTAmnt()
    {
        return rand(1, self::MAX_LOOP_AMOUNT - 1);
    }
}
