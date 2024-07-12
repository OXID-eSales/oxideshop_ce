<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use \Exception;
use \oxTestModules;

/**
 * Tests for Manufacturer_Main class
 */
class ManufacturerMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Manufacturer_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxmanufacturer', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Manufacturer_Main');
        $this->assertSame('manufacturer_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Manufacturer::class, $aViewData['edit']);
        $this->assertArrayHasKey('readonly', $aViewData);
    }

    /**
     * Statistic_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Manufacturer_Main');
        $this->assertSame('manufacturer_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Manufacturer_Main::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxmanufacturer', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Manufacturer_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Manufacturer_Main::save()");

            return;
        }

        $this->fail("error in Manufacturer_Main::save()");
    }

    /**
     * Manufacturer_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        // testing..
        oxTestModules::addFunction('oxmanufacturer', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Manufacturer_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Manufacturer_Main::saveinnlang()");

            return;
        }

        $this->fail("error in Manufacturer_Main::saveinnlang()");
    }
}
