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
 * Tests for VoucherSerie_Export class
 */
class Unit_Admin_VoucherSerieExportTest extends OxidTestCase
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
     * VoucherSerie_Export::getDownloadUrl() test case
     *
     * @return null
     */
    public function testGetDownloadUrl()
    {
        $myConfig = modConfig::getInstance();

        $myConfig->setConfigParam("sAdminSSLURL", "sAdminSSLURL");
        oxRegistry::get("oxUtilsUrl")->setAdminMode(true);
        $sUrl = oxRegistry::get("oxUtilsUrl")->processUrl('sAdminSSLURL/index.php');

        // ssl
        $oView = new VoucherSerie_Export();
        $this->assertEquals($sUrl . '&amp;cl=voucherserie_export&amp;fnc=download', $oView->getDownloadUrl());

        $myConfig->setConfigParam("sAdminSSLURL", null);
        $sUrl = $myConfig->getConfigParam('sShopURL') . $myConfig->getConfigParam('sAdminDir');
        $sUrl = oxRegistry::get("oxUtilsUrl")->processUrl($sUrl . '/index.php');

        // non ssl
        $oView = new VoucherSerie_Export();
        $this->assertEquals($sUrl . '&amp;cl=voucherserie_export&amp;fnc=download', $oView->getDownloadUrl());
    }

    /**
     * VoucherSerie_Export::_getExportFileName() test case
     *
     * @return null
     */
    public function testGetExportFileName()
    {
        $oView = new VoucherSerie_Export();
        $oView->UNITgetExportFileName();

        $this->assertNotNull(oxRegistry::getSession()->getVariable("sExportFileName"));
    }

    /**
     * VoucherSerie_Export::_getExportFilePath() test case
     *
     * @return null
     */
    public function testGetExportFilePath()
    {
        $oView = $this->getMock("VoucherSerie_Export", array("_getExportFileName"));
        $oView->expects($this->once())->method('_getExportFileName')->will($this->returnValue("testName"));

        $this->assertEquals(oxRegistry::getConfig()->getConfigParam('sShopDir') . "/export/" . "testName", $oView->UNITgetExportFilePath());
    }

    /**
     * VoucherSerie_Export::exportVouchers() test case
     *
     * @return null
     */
    public function testExportVouchers()
    {
        // test voucherserie
        $oVoucherSerie = oxNew("oxvoucherserie");
        $oVoucherSerie->setId("_testvoucherserie");

        // inserting test voucher
        $oVoucher = oxNew("oxbase");
        $oVoucher->init("oxvouchers");
        $oVoucher->setId("_testvoucher");
        $oVoucher->oxvouchers__oxvoucherserieid = new oxField("_testvoucherserie");
        $oVoucher->oxvouchers__oxvouchernr = new oxField("_testvoucher");
        $oVoucher->save();

        $oView = $this->getMock("VoucherSerie_Export", array("write", "_getVoucherSerie"));
        $oView->expects($this->at(0))->method('_getVoucherSerie')->will($this->returnValue($oVoucherSerie));
        $oView->expects($this->at(1))->method('write')->with($this->equalTo("Gutscheine"));
        $oView->expects($this->at(2))->method('write')->with($this->equalTo("_testvoucher"));
        $this->assertEquals(1, $oView->exportVouchers(0));
    }
}
