<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for VoucherSerie_Generate class
 */
class VoucherSerieGenerateTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Cleanup
     */
    public function tearDown(): void
    {
        // cleanup
        $this->cleanUpTable("oxvouchers");
        $this->cleanUpTable("oxvoucherseries");

        parent::tearDown();
    }

    /**
     * VoucherSerie_Generate::nextTick() test case
     */
    public function testNextTick()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGenerate::class, ["generateVoucher"]);
        $oView
            ->method('generateVoucher')
            ->willReturnOnConsecutiveCalls(
                0, 1
            );

        $this->assertFalse($oView->nextTick(1));
        $this->assertEquals(1, $oView->nextTick(1));
    }

    /**
     * VoucherSerie_Generate::generateVoucher() test case
     */
    public function testGenerateVoucher()
    {
        $this->getSession()->setVariable("voucherAmount", 100);

        $oSerie = $this->getMock(\OxidEsales\Eshop\Application\Model\VoucherSerie::class, ["getId"]);
        $oSerie->expects($this->exactly(2))->method('getId')->will($this->returnValue("testId"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGenerate::class, ["getVoucherSerie"]);
        $oView->expects($this->any())->method('getVoucherSerie')->will($this->returnValue($oSerie));
        $this->assertEquals(1, $oView->generateVoucher(0));
        $this->assertEquals(2, $oView->generateVoucher(1));
    }

    /**
     * VoucherSerie_Generate::run() test case
     */
    public function testRun()
    {
        $this->setRequestParameter("iStart", 0);

        // first generation call
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGenerate::class, ["nextTick", "stop"]);
        $oView->expects($this->exactly(100))->method('nextTick')->will($this->returnValue(1));
        $oView->expects($this->never())->method('stop');

        $oView->run();

        // last generation call
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGenerate::class, ["nextTick", "stop"]);
        $oView->expects($this->once())->method('nextTick')->will($this->returnValue(false));
        $oView->expects($this->once())->method('stop');

        $oView->run();
    }
}
