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
 * Testing oxvoucherserie class
 */
class Unit_Core_oxvoucherlistTest extends OxidTestCase
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
