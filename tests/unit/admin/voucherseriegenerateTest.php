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
 * Tests for VoucherSerie_Generate class
 */
class Unit_Admin_VoucherSerieGenerateTest extends OxidTestCase
{

    /**
     * Cleanup
     *
     * @return null
     */
    public function tearDown()
    {
        // cleanup
        $this->cleanUpTable("oxvouchers");
        $this->cleanUpTable("oxvoucherseries");

        parent::tearDown();
    }

    /**
     * VoucherSerie_Generate::nextTick() test case
     *
     * @return null
     */
    public function testNextTick()
    {
        $oView = $this->getMock("VoucherSerie_Generate", array("generateVoucher"));
        $oView->expects($this->at(0))->method('generateVoucher')->will($this->returnValue(0));
        $oView->expects($this->at(1))->method('generateVoucher')->will($this->returnValue(1));

        $this->assertFalse($oView->nextTick(1));
        $this->assertEquals(1, $oView->nextTick(1));
    }

    /**
     * VoucherSerie_Generate::generateVoucher() test case
     *
     * @return null
     */
    public function testGenerateVoucher()
    {
        modSession::getInstance()->setVar("voucherAmount", 100);

        $oSerie = $this->getMock("oxVoucherSerie", array("getId"));
        $oSerie->expects($this->exactly(2))->method('getId')->will($this->returnValue("testId"));

        $oView = $this->getMock("VoucherSerie_Generate", array("_getVoucherSerie"));
        $oView->expects($this->any())->method('_getVoucherSerie')->will($this->returnValue($oSerie));
        $this->assertEquals(1, $oView->generateVoucher(0));
        $this->assertEquals(2, $oView->generateVoucher(1));
    }

    /**
     * VoucherSerie_Generate::run() test case
     *
     * @return null
     */
    public function testRun()
    {
        modConfig::setRequestParameter("iStart", 0);

        // first generation call
        $oView = $this->getMock("VoucherSerie_Generate", array("nextTick", "stop"));
        $oView->expects($this->exactly(100))->method('nextTick')->will($this->returnValue(1));
        $oView->expects($this->never())->method('stop');

        $oView->run();

        // last generation call
        $oView = $this->getMock("VoucherSerie_Generate", array("nextTick", "stop"));
        $oView->expects($this->once())->method('nextTick')->will($this->returnValue(false));
        $oView->expects($this->once())->method('stop');

        $oView->run();
    }
}
