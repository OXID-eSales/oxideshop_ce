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
class VoucherlistTest extends \OxidTestCase
{
    const MAX_LOOP_AMOUNT = 4;

    protected $_sOxid = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        // simulating some voucherserie
        $this->_sOxid = uniqid('test');

        // creating 100 test vouchers
        for ($i = 0; $i < self::MAX_LOOP_AMOUNT; $i++) {
            $oNewVoucher = oxNew('oxvoucher');
            $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($this->_sOxid, oxField::T_RAW);
            $oNewVoucher->oxvouchers__oxvouchernr = new oxField(uniqid('voucherNr' . $i), oxField::T_RAW);
            $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($this->_sOxid, oxField::T_RAW);

            $oNewVoucher->Save();
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();

        // removing vouchers
        $sQ = 'delete from oxvouchers where oxvouchers.oxvoucherserieid = "' . $this->_sOxid . '"';
        $myDB->Execute($sQ);

        parent::tearDown();
    }

    public function testLoadVoucherList()
    {
        $myUtils = oxRegistry::getUtils();

        $oVouchers = oxNew('oxvoucherlist');
        $oVouchers->selectString('select * from oxvouchers where oxvouchers.oxvoucherserieid = "' . $this->_sOxid . '"');

        $this->assertEquals(self::MAX_LOOP_AMOUNT, $oVouchers->count());
    }
}
