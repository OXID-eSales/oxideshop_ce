<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Discount;
use \Exception;
use \oxField;
use \oxTestModules;

/**
 * Tests for Discount_Main class
 */
class DiscountMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test tear down
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable("oxdiscount");

        parent::tearDown();
    }

    /**
     * Discount_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdiscount", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Discount_Main');
        $this->assertSame('discount_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Discount::class, $aViewData['edit']);
    }

    /**
     * Discount_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Discount_Main');
        $this->assertSame('discount_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Discount_Main::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxdiscount', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Discount_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Discount_Main::save()");

            return;
        }

        $this->fail("error in Discount_Main::save()");
    }

    /**
     * Discount_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        // testing..
        oxTestModules::addFunction('oxdiscount', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Discount_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Discount_Main::save()");

            return;
        }

        $this->fail("error in Discount_Main::save()");
    }

    /**
     * Discount_Main::getItemDiscountProductTitle() test case
     */
    public function testgetItemDiscountProductTitle()
    {
        $sId = '1131';
        $sTitleDe = 'Flaschenverschluss EGO';
        $sTitleEn = 'Bottle Cap EGO';

        $oTestDiscount = oxNew('oxDiscount');
        $oTestDiscount->setId("_testDiscountId");

        $oTestDiscount->oxdiscount__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oTestDiscount->oxdiscount__oxactive = new oxField(1);
        $oTestDiscount->oxdiscount__oxtitle = new oxField("Test");
        $oTestDiscount->oxdiscount__oxamount = new oxField(1);
        $oTestDiscount->oxdiscount__oxamountto = new oxField(10);
        $oTestDiscount->oxdiscount__oxitmartid = new oxField($sId);
        $oTestDiscount->oxdiscount__oxprice = new oxField(1);
        $oTestDiscount->oxdiscount__oxaddsumtype = new oxField("%");
        $oTestDiscount->oxdiscount__oxaddsum = new oxField(10);
        $oTestDiscount->save();

        $oView = $this->getProxyClass("Discount_Main");

        $oView->setNonPublicVar("_iEditLang", 0);
        $this->setRequestParameter("oxid", '-1');
        $this->assertSame(" -- ", $oView->getItemDiscountProductTitle());

        $oView->setNonPublicVar("_iEditLang", 0);
        $this->setRequestParameter("oxid", "_testDiscountId");
        $this->assertSame(sprintf('%s %s', $sId, $sTitleDe), $oView->getItemDiscountProductTitle());

        $oView->setNonPublicVar("_iEditLang", 1);
        $this->setRequestParameter("oxid", "_testDiscountId");
        $this->assertSame(sprintf('%s %s', $sId, $sTitleEn), $oView->getItemDiscountProductTitle());
    }
}
