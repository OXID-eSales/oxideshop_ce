<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\DeliverySet;
use \Exception;
use \oxTestModules;

/**
 * Tests for DeliverySet_Main class
 */
class DeliverySetMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * DeliverySet_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdeliveryset", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('DeliverySet_Main');
        $this->assertSame('deliveryset_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliverySet::class, $aViewData['edit']);
    }

    /**
     * DeliverySet_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('DeliverySet_Main');
        $this->assertSame('deliveryset_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * DeliverySet_Main::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxdeliveryset', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('DeliverySet_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in DeliverySet_Main::save()");

            return;
        }

        $this->fail("error in DeliverySet_Main::save()");
    }

    /**
     * DeliverySet_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxdeliveryset', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('DeliverySet_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in DeliverySet_Main::save()");

            return;
        }

        $this->fail("error in DeliverySet_Main::save()");
    }
}
