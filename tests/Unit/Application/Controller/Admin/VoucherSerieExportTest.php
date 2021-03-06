<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \oxRegistry;

/**
 * Tests for VoucherSerie_Export class
 */
class VoucherSerieExportTest extends \OxidTestCase
{

    /**
     * Cleanup
     *
     * @return null
     */
    public function tearDown(): void
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
        $myConfig = $this->getConfig();

        $myConfig->setConfigParam("sAdminSSLURL", "sAdminSSLURL");
        \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->setAdminMode(true);
        $sUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl('sAdminSSLURL/index.php');

        // ssl
        $oView = oxNew('VoucherSerie_Export');
        $this->assertEquals($sUrl . '&amp;cl=voucherserie_export&amp;fnc=download', $oView->getDownloadUrl());

        $myConfig->setConfigParam("sAdminSSLURL", null);
        $sUrl = $myConfig->getConfigParam('sShopURL') . $myConfig->getConfigParam('sAdminDir');
        $sUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($sUrl . '/index.php');

        // non ssl
        $oView = oxNew('VoucherSerie_Export');
        $this->assertEquals($sUrl . '&amp;cl=voucherserie_export&amp;fnc=download', $oView->getDownloadUrl());
    }

    /**
     * VoucherSerie_Export::getExportFileName() test case
     *
     * @return null
     */
    public function testGetExportFileName()
    {
        $oView = oxNew('VoucherSerie_Export');
        $oView->getExportFileName();

        $this->assertNotNull(oxRegistry::getSession()->getVariable("sExportFileName"));
    }

    /**
     * VoucherSerie_Export::getExportFilePath() test case
     *
     * @return null
     */
    public function testGetExportFilePath()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieExport::class, array("getExportFileName"));
        $oView->expects($this->once())->method('getExportFileName')->will($this->returnValue("testName"));

        $this->assertEquals($this->getConfig()->getConfigParam('sShopDir') . "/export/" . "testName", $oView->getExportFilePath());
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
        $oVoucher = oxNew("oxBase");
        $oVoucher->init("oxvouchers");
        $oVoucher->setId("_testvoucher");
        $oVoucher->oxvouchers__oxvoucherserieid = new oxField("_testvoucherserie");
        $oVoucher->oxvouchers__oxvouchernr = new oxField("_testvoucher");
        $oVoucher->save();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieExport::class, array("write", "getVoucherSerie"));
        $oView->expects($this->atLeastOnce())->method('getVoucherSerie')->will($this->returnValue($oVoucherSerie));
        $oView
            ->method('write')
            ->withConsecutive(['Gutscheine'], ['_testvoucher']);

        $this->assertEquals(1, $oView->exportVouchers(0));
    }
}
