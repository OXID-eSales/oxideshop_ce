<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use \oxRegistry;

/**
 * Testing oxvoucherserie class
 */
class VoucherserieTest extends \OxidTestCase
{
    protected $_aIds = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myConfig = $this->getConfig();
        $this->_aIds = array();

        // percental
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->oxvoucherseries__oxserienr = new oxField('Test Mod Voucher Serie', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscount = new oxField(50, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscounttype = new oxField('percent', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxshopid = new oxField($myConfig->getBaseShopId(), oxField::T_RAW);
        $oSerie->save();
        $this->_aIds[] = $oSerie->getId();

        // abs
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->oxvoucherseries__oxserienr = new oxField('Test Mod Voucher Serie', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscount = new oxField(0.999, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxdiscounttype = new oxField('absolute', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxbegindate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxenddate = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowsameseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowotherseries = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxallowuseanother = new oxField(0, oxField::T_RAW);
        $oSerie->oxvoucherseries__oxshopid = new oxField($myConfig->getBaseShopId(), oxField::T_RAW);
        $oSerie->save();

        $this->_aIds[] = $oSerie->getId();

        $this->_addAdditionalInfo();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oSerie = oxNew('oxvoucherserie');
        foreach ($this->_aIds as $sOxid) {
            $oSerie->delete($sOxid);
        }

        $this->_remAdditionalInfo();

        $this->cleanUpTable('oxvouchers');

        parent::tearDown();
    }

    /**
     * Adding/removing test data
     */
    protected function _addAdditionalInfo()
    {
        $myUtils = oxRegistry::getUtils();
        $aGroupsToAdd = array('oxidsmallcust', 'oxidmiddlecust', 'oxidgoodcust');

        foreach ($this->_aIds as $sOxid) {
            // assigning groups
            foreach ($aGroupsToAdd as $sGroupId) {
                $oNewGroup = oxNew('oxobject2group');
                $oNewGroup->oxobject2group__oxobjectid = new oxField($sOxid, oxField::T_RAW);
                $oNewGroup->oxobject2group__oxgroupsid = new oxField($sGroupId, oxField::T_RAW);
                $oNewGroup->save();
            }

            // loading for additional information
            $oSerie = oxNew('oxvoucherserie');
            $oSerie->load($sOxid);

            $blReserved = false;

            // creating 100 test vouchers
            for ($i = 0; $i < 10; $i++) {
                $oNewVoucher = oxNew('oxvoucher');
                $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($sOxid, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxvouchernr = new oxField(uniqid('voucherNr' . $i), oxField::T_RAW);

                $oNewVoucher->oxvouchers__oxdiscount = new oxField($oSerie->oxvoucherseries__oxdiscount->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxdiscounttype = new oxField($oSerie->oxvoucherseries__oxdiscounttype->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxbegindate = new oxField($oSerie->oxvoucherseries__oxbegindate->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxenddate = new oxField($oSerie->oxvoucherseries__oxenddate->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxminimumvalue = new oxField($oSerie->oxvoucherseries__oxminimumvalue->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxallowsameseries = new oxField($oSerie->oxvoucherseries__oxallowsameseries->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxallowotherseries = new oxField($oSerie->oxvoucherseries__oxallowotherseries->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxallowuseanother = new oxField($oSerie->oxvoucherseries__oxallowuseanother->value, oxField::T_RAW);
                $oNewVoucher->oxvouchers__oxreserver = new oxField((int) $blReserved, oxField::T_RAW);

                $oNewVoucher->oxvouchers__oxshopid = new oxField($oSerie->oxvoucherseries__oxshopid->value, oxField::T_RAW);

                $oNewVoucher->save();
                $blReserved = !$blReserved;
            }
        }
    }

    protected function _remAdditionalInfo()
    {
        $myDB = oxDb::getDb();

        foreach ($this->_aIds as $sOxid) {
            // removing groups assignment
            $sQ = "delete from oxobject2group where oxobject2group.oxobjectid = '{$sOxid}'";
            $myDB->Execute($sQ);

            // removing vouchers
            $sQ = "delete from oxvouchers where oxvouchers.oxvoucherserieid = '$sOxid'";
            $myDB->Execute($sQ);
        }
    }

    /*
     * Test delete removes user groups relations
     */
    public function testDeleteRemovesUserGroups()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');

            if (!$oSerie->load($sOxid)) {
                $this->fail('can not load voucherserie');
            }

            // default deletion
            $oSerie->delete();

            $sQ = 'select count(*) from oxobject2group where oxobject2group.oxobjectid = "' . $sOxid . '"';
            $this->assertEquals(0, (int) $myDB->GetOne($sQ), 'oxobject2group (voucherserie) was not removed');
        }
    }

    /*
     * Test delete removes vouchers assigned to voucherserie
     */
    public function testDeleteRemovesAssignedVouchers()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        $oSerie = oxNew('oxvoucherserie');

        foreach ($this->_aIds as $sOxid) {
            if (!$oSerie->load($sOxid)) {
                $this->fail('can not load voucherserie');
            }

            // default deletion
            $oSerie->delete();

            $sQ = "select count(*) from oxvouchers where oxvouchers.oxvoucherserieid = '$sOxid'";
            $this->assertEquals(0, (int) $myDB->GetOne($sQ), 'oxvouchers (voucherserie) was not removed');
        }
    }

    /*
     * Test delete removes voucherserie
     */
    public function testDeleteRemovesVoucherSerie()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        $oSerie = oxNew('oxvoucherserie');

        foreach ($this->_aIds as $sOxid) {
            if (!$oSerie->load($sOxid)) {
                $this->fail('can not load voucherserie');
            }

            // default deletion
            $oSerie->delete();

            $sQ = "select count(*) from oxvoucherseries where oxvoucherseries.oxid = '$sOxid'";
            $this->assertEquals(0, (int) $myDB->GetOne($sQ), 'voucherserie was not removed');
        }
    }

    /**
     * Test getting user group list.
     */
    public function testSetUserGroups()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');

                return;
            }

            $aGroups = $oSerie->setUserGroups();

            // by default we assigned 3 user groups - lets check the size of list
            $this->assertEquals(3, $aGroups->count(), 'Incorect number of assigned user groups to oxvoucherserie');
        }
    }

    /**
     * Test getting user group list returns all assigned to voucherserie groups.
     */
    public function testSetUserGroupsReturnsCorrectGroups()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            $aGroups = $oSerie->setUserGroups();

            $aGroupsToAdd = array('oxidsmallcust' => 0, 'oxidmiddlecust' => 1, 'oxidgoodcust' => 2);

            // same groups as assigned initially ?
            foreach ($aGroups as $oGroup) {
                if (!isset($aGroupsToAdd[$oGroup->oxgroups__oxid->value])) {
                    $this->fail('loaded not assigned group');
                } else {
                    unset($aGroupsToAdd[$oGroup->oxgroups__oxid->value]);
                }
            }

            // all groups was assigned/loaded ?
            if (count($aGroupsToAdd) != 0) {
                $this->fail('not all assigned groups were loaded in list');
            }
        }
    }


    /**
     * Test removing user groups relations
     */
    public function testUnsetUserGroups()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            // unsetting groups
            $oSerie->unsetUserGroups();

            $sQ = "select count(*) from oxobject2group where oxobject2group.oxobjectid = '$sOxid'";
            $this->assertEquals(0, $myDB->GetOne($sQ), 'oxobject2group (voucherserie) was not removed');
        }
    }

    /**
     * Test getting vouchers assigned to voucherserie
     */
    public function testGetVoucherList()
    {
        $myUtils = oxRegistry::getUtils();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            $oVoucherList = $oSerie->getVoucherList();
            $this->assertEquals(10, $oVoucherList->count(), 'Incorect numer of loaded vouchers');
        }
    }

    /*
     * Test deleting vouchers assigned to voucherserie
     */
    public function testDeleteVoucherList()
    {
        $myUtils = oxRegistry::getUtils();
        $myDB = oxDb::getDb();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            $oSerie->deleteVoucherList();

            $sQ = "select count(*) from oxvouchers where oxvouchers.oxvoucherserieid = '$sOxid'";
            $this->assertEquals(0, $myDB->GetOne($sQ), 'Not all vouchers was removed by deleteVoucherList() function ');
        }
    }

    /**
     * Test counting vouchers
     */
    public function testCountVouchers()
    {
        $myUtils = oxRegistry::getUtils();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            $aStatus = $oSerie->countVouchers();

            // checking
            $this->assertEquals(10, $aStatus['total'], 'Incorect number of total vouchers');
            $this->assertEquals(0, $aStatus['used'], 'Incorect number of used vouchers');
            $this->assertEquals(10, $aStatus['available'], 'Incorect number of available vouchers');
        }
    }

    /**
     * Test counting vouchers. Check if dataused makes voucher used. #2133
     */
    public function testCountVouchersUsedDateUsed()
    {
        $myUtils = oxRegistry::getUtils();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            // Create new used voucher
            $oNewVoucher = oxNew("oxvoucher");
            $oNewVoucher->oxvouchers__oxvouchernr = new oxField('_test_' . $sOxid);
            $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($sOxid);
            $oNewVoucher->oxvouchers__oxdateused = new oxField(date('Y-m-d'));
            $oNewVoucher->setId('_test_' . $sOxid);
            $oNewVoucher->save();

            $aStatus = $oSerie->countVouchers();
            // checking
            $this->assertEquals(11, $aStatus['total'], 'Incorect number of total vouchers');
            $this->assertEquals(1, $aStatus['used'], 'Incorect number of used vouchers');
            $this->assertEquals(10, $aStatus['available'], 'Incorect number of available vouchers');
        }
    }

    /**
     * Test counting vouchers. Check if orderid makes voucher used. #2133
     */
    public function testCountVouchersUsedOrderId()
    {
        $myUtils = oxRegistry::getUtils();

        foreach ($this->_aIds as $sOxid) {
            $oSerie = oxNew('oxvoucherserie');
            if (!$oSerie->Load($sOxid)) {
                $this->fail('can not load oxvoucherserie');
            }

            // Create new used voucher
            $oNewVoucher = oxNew("oxvoucher");
            $oNewVoucher->oxvouchers__oxvouchernr = new oxField('_test_' . $sOxid);
            $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($sOxid);
            $oNewVoucher->oxvouchers__oxorderid = new oxField(oxRegistry::getUtilsObject()->generateUID());
            $oNewVoucher->setId('_test_' . $sOxid);
            $oNewVoucher->save();

            $aStatus = $oSerie->countVouchers();
            // checking
            $this->assertEquals(11, $aStatus['total'], 'Incorect number of total vouchers');
            $this->assertEquals(1, $aStatus['used'], 'Incorect number of used vouchers');
            $this->assertEquals(10, $aStatus['available'], 'Incorect number of available vouchers');
        }
    }
}
