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
class ManufacturerMainTest extends \OxidTestCase
{

    /**
     * Manufacturer_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxmanufacturer', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Manufacturer_Main');
        $this->assertEquals('manufacturer_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof manufacturer);
        $this->assertTrue(isset($aViewData['readonly']));
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
        $oView = oxNew('Manufacturer_Main');
        $this->assertEquals('manufacturer_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Manufacturer_Main::Save() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Manufacturer_Main::save()");

            return;
        }
        $this->fail("error in Manufacturer_Main::save()");
    }

    /**
     * Manufacturer_Main::Saveinnlang() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Manufacturer_Main::saveinnlang()");

            return;
        }
        $this->fail("error in Manufacturer_Main::saveinnlang()");
    }
}
