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
class VoucherSerieMainTest extends \OxidTestCase
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
     * VoucherSerie_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('VoucherSerie_Main');
        $this->assertEquals('voucherserie_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof voucherserie);
    }

    /**
     * VoucherSerie_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('VoucherSerie_Main');
        $this->assertEquals('voucherserie_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertFalse(isset($aViewData['edit']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * VoucherSerie_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxvoucherserie', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('VoucherSerie_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Wrapping_Main::save()");

            return;
        }
        $this->fail("error in VoucherSerie_Main::save()");
    }

    /**
     * VoucherSerie_Main::prepareExport() test case
     *
     * @return null
     */
    public function testPrepareExport()
    {
        $oView = oxNew('VoucherSerie_Main');
        $this->assertNull($oView->prepareExport());
    }

    /**
     * VoucherSerie_Main::getStatus() test case
     *
     * @return null
     */
    public function testGetStatus()
    {
        // no series..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieMain::class, array("_getVoucherSerie"));
        $oView->expects($this->once())->method('_getVoucherSerie')->will($this->returnValue(false));
        $this->assertNull($oView->getStatus());

        // with serie..
        $oSerie = $this->getMock(\OxidEsales\Eshop\Application\Model\VoucherSerie::class, array("countVouchers"));
        $oSerie->expects($this->once())->method('countVouchers')->will($this->returnValue("testCountVouchers"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieMain::class, array("_getVoucherSerie"));
        $oView->expects($this->once())->method('_getVoucherSerie')->will($this->returnValue($oSerie));
        $this->assertEquals("testCountVouchers", $oView->getStatus());
    }

    /**
     * VoucherSerie_Main::prepareExport() test case
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetVoucherSerie()
    {
        // inserting demo voucher
        $oVoucherSerie = oxNew("oxvoucherserie");
        $oVoucherSerie->setId("_testvoucherserie");
        $oVoucherSerie->save();

        $this->getSession()->setVariable("voucherid", "_testvoucherserie");

        $oView = oxNew('VoucherSerie_Main');
        $oVoucherSerie = $oView->UNITgetVoucherSerie();

        $this->assertNotNull($oVoucherSerie);
        $this->assertEquals("_testvoucherserie", $oVoucherSerie->getId());
    }

    /**
     * VoucherSerie_Main::getViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = oxNew('VoucherSerie_Main');
        $this->assertEquals("tbclvoucherserie_main", $oView->getViewId());
    }
}
