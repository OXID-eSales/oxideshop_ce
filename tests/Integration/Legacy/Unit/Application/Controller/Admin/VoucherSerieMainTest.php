<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\VoucherSerie;
use \Exception;
use \oxTestModules;

/**
 * Tests for VoucherSerie_Main class
 */
class VoucherSerieMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Cleanup
     */
    protected function tearDown(): void
    {
        // cleanup
        $this->cleanUpTable("oxvouchers");
        $this->cleanUpTable("oxvoucherseries");

        parent::tearDown();
    }

    /**
     * VoucherSerie_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('VoucherSerie_Main');
        $this->assertSame('voucherserie_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\VoucherSerie::class, $aViewData['edit']);
    }

    /**
     * VoucherSerie_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('VoucherSerie_Main');
        $this->assertSame('voucherserie_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayNotHasKey('edit', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * VoucherSerie_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxvoucherserie', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('VoucherSerie_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Wrapping_Main::save()");

            return;
        }

        $this->fail("error in VoucherSerie_Main::save()");
    }

    /**
     * VoucherSerie_Main::prepareExport() test case
     */
    public function testPrepareExport()
    {
        $oView = oxNew('VoucherSerie_Main');
        $this->assertNull($oView->prepareExport());
    }

    /**
     * VoucherSerie_Main::getStatus() test case
     */
    public function testGetStatus()
    {
        // no series..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieMain::class, ["getVoucherSerie"]);
        $oView->expects($this->once())->method('getVoucherSerie')->willReturn(false);
        $this->assertNull($oView->getStatus());

        // with serie..
        $oSerie = $this->getMock(\OxidEsales\Eshop\Application\Model\VoucherSerie::class, ["countVouchers"]);
        $oSerie->expects($this->once())->method('countVouchers')->willReturn("testCountVouchers");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieMain::class, ["getVoucherSerie"]);
        $oView->expects($this->once())->method('getVoucherSerie')->willReturn($oSerie);
        $this->assertSame("testCountVouchers", $oView->getStatus());
    }

    /**
     * VoucherSerie_Main::prepareExport() test case
     */
    public function testStart()
    {
        $this->setRequestParameter("voucherid", "testvoucherid");
        $this->setRequestParameter("voucherAmount", "testvoucherAmount");
        $this->setRequestParameter("randomVoucherNr", "testrandomVoucherNr");
        $this->setRequestParameter("voucherNr", "testvoucherNr");
        $this->setRequestParameter("cl", "voucherserie_generate");

        $oView = oxNew('VoucherSerie_Main');
        $oView->start();

        $oSession = $this->getSession();

        $this->assertEquals($oSession->getVariable("voucherid"), "testvoucherid");
        $this->assertEquals($oSession->getVariable("voucherAmount"), 0);
        $this->assertEquals($oSession->getVariable("randomVoucherNr"), "testrandomVoucherNr");
        $this->assertEquals($oSession->getVariable("voucherNr"), "testvoucherNr");
    }

    /**
     * Test covers #0006284
     */
    public function testNotAllowEmptyVoucherGeneration()
    {
        $this->setRequestParameter("voucherid", "testvoucherid");
        $this->setRequestParameter("voucherAmount", "testvoucherAmount");
        $this->setRequestParameter("randomVoucherNr", "");
        $this->setRequestParameter("voucherNr", "");
        $this->setRequestParameter("cl", "voucherserie_generate");

        $oView = oxNew('VoucherSerie_Main');
        $oView->start();

        $oSession = $this->getSession();

        $this->assertNull($oSession->getVariable("voucherid"), "testvoucherid");
        $this->assertNull($oSession->getVariable("voucherAmount"), 0);
        $this->assertNull($oSession->getVariable("randomVoucherNr"));
        $this->assertNull($oSession->getVariable("voucherNr"));
    }

    /**
     * VoucherSerie_Main::prepareExport() test case
     */
    public function testGetVoucherSerie()
    {
        // inserting demo voucher
        $oVoucherSerie = oxNew("oxvoucherserie");
        $oVoucherSerie->setId("_testvoucherserie");
        $oVoucherSerie->save();

        $this->getSession()->setVariable("voucherid", "_testvoucherserie");

        $oView = oxNew('VoucherSerie_Main');
        $oVoucherSerie = $oView->getVoucherSerie();

        $this->assertNotNull($oVoucherSerie);
        $this->assertSame("_testvoucherserie", $oVoucherSerie->getId());
    }

    /**
     * VoucherSerie_Main::getViewId() test case
     */
    public function testGetViewId()
    {
        $oView = oxNew('VoucherSerie_Main');
        $this->assertSame("tbclvoucherserie_main", $oView->getViewId());
    }
}
