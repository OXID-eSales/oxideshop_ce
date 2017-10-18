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
class OrderMainTest extends \OxidTestCase
{
    /**
     * tear down the test.
     */
    protected function tearDown()
    {
        $_POST = array();
        parent::tearDown();
    }

    /**
     * Order_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxorder', 'load', '{ $this->oxorder__oxdeltype = new oxField("test"); $this->oxorder__oxtotalbrutsum = new oxField(10); $this->oxorder__oxcurrate = new oxField(10); }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Order_Main');
        $this->assertEquals('order_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof order);
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Order_Main');
        $this->assertEquals('order_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Order_Main::Save() test case
     *
     * @return null
     */
    public function testSaveRecalculate()
    {
        //
        if ($this->getConfig()->getEdition() === 'EE') {
            oxTestModules::addFunction('oxorder', 'load', '{}');
        } else {
            oxTestModules::addFunction('oxorder', 'load', '{$this->oxorder__oxshopid = new oxField('.ShopIdCalculator::BASE_SHOP_ID.');}');
        }
        oxTestModules::addFunction('oxorder', 'save', '{}');
        oxTestModules::addFunction('oxorder', 'assign', '{}');
        oxTestModules::addFunction('oxorder', 'reloadDelivery', '{}');
        oxTestModules::addFunction('oxorder', 'reloadDiscount', '{}');
        oxTestModules::addFunction('oxorder', 'recalculateOrder', '{ throw new Exception( "recalculateOrder" ); }');

        // testing..
        try {
            $_POST = array('editval' => array('oxorder__oxdiscount' => 10.0));
            $oView = oxNew('Order_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("recalculateOrder", $oExcp->getMessage(), "error in Order_Main::save()");

            return;
        }
        $this->fail("error in Order_Main::save()");
    }

    /**
     * Order_Main::Save() test case
     *
     * @return null
     */
    public function testSaveNoRecalculate()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            oxTestModules::addFunction('oxorder', 'load', '{}');
        } else {
            oxTestModules::addFunction('oxorder', 'load', '{$this->oxorder__oxshopid = new oxField('.ShopIdCalculator::BASE_SHOP_ID.');}');
        }
        oxTestModules::addFunction('oxorder', 'save', '{ throw new Exception( "saveOrder" ); }');
        oxTestModules::addFunction('oxorder', 'assign', '{}');
        oxTestModules::addFunction('oxorder', 'reloadDelivery', '{}');
        oxTestModules::addFunction('oxorder', 'reloadDiscount', '{}');
        oxTestModules::addFunction('oxorder', 'recalculateOrder', '{ throw new Exception( "recalculateOrder" ); }');

        try {
            $oView = oxNew('Order_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("saveOrder", $oExcp->getMessage(), "error in Order_Main::save()");

            return;
        }
        $this->fail("error in Order_Main::save()");
    }


    /**
     * Order_Main::Sendorder() test case
     *
     * @return null
     */
    public function testSendorder()
    {
        //
        oxTestModules::addFunction('oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction('oxorder', 'save', '{}; }');
        oxTestModules::addFunction('oxorder', 'getOrderArticles', '{ return array(); }');
        oxTestModules::addFunction('oxemail', 'sendSendedNowMail', '{ throw new Exception( "sendSendedNowMail" ); }');

        $this->setRequestParameter("sendmail", 1);
        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = oxNew('Order_Main');
            $oView->sendorder();
        } catch (Exception $oExcp) {
            $this->assertEquals("sendSendedNowMail", $oExcp->getMessage(), "error in Order_Main::sendorder()");

            return;
        }
        $this->fail("error in Order_Main::sendorder()");
    }

    /**
     * Order_Main::senddownloadlinks() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("sendDownloadLinksMail", $oExcp->getMessage(), "error in Order_Main::senddownloadlinks()");

            return;
        }
        $this->fail("error in Order_Main::senddownloadlinks()");
    }

    /**
     * Order_Main::Resetorder() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("recalculateOrder", $oExcp->getMessage(), "error in Order_Main::resetorder()");

            return;
        }
        $this->fail("error in Order_Main::resetorder()");
    }

}
