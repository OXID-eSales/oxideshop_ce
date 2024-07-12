<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Application\Model\Order;
use \Exception;
use \oxTestModules;

/**
 * Tests for Order_Main class
 */
class OrderMainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tear down the test.
     */
    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    /**
     * Order_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxorder', 'load', '{ $this->oxorder__oxdeltype = new oxField("test"); $this->oxorder__oxtotalbrutsum = new oxField(10); $this->oxorder__oxcurrate = new oxField(10); }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Order_Main');
        $this->assertSame('order_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Order::class, $aViewData['edit']);
    }

    /**
     * Statistic_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Order_Main');
        $this->assertSame('order_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Order_Main::senddownloadlinks() test case
     */
    public function testSenddownloadlinks()
    {
        //
        oxTestModules::addFunction('oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction('oxemail', 'sendDownloadLinksMail', '{ throw new Exception( "sendDownloadLinksMail" ); }');

        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = oxNew('Order_Main');
            $oView->senddownloadlinks();
        } catch (Exception $exception) {
            $this->assertSame("sendDownloadLinksMail", $exception->getMessage(), "error in Order_Main::senddownloadlinks()");

            return;
        }

        $this->fail("error in Order_Main::senddownloadlinks()");
    }

    /**
     * Order_Main::Resetorder() test case
     */
    public function testResetorder()
    {
        //
        oxTestModules::addFunction('oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction('oxorder', 'save', '{ throw new Exception( "recalculateOrder" ); }');

        // testing..
        try {
            $oView = oxNew('Order_Main');
            $oView->resetorder();
        } catch (Exception $exception) {
            $this->assertSame("recalculateOrder", $exception->getMessage(), "error in Order_Main::resetorder()");

            return;
        }

        $this->fail("error in Order_Main::resetorder()");
    }
}
