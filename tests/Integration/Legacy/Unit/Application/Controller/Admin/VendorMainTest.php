<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Vendor;
use \Exception;
use \oxTestModules;

/**
 * Tests for Vendor_Main class
 */
class VendorMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Vendor_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMain::class, ["createCategoryTree"]);
        $oView->expects($this->once())->method('createCategoryTree');
        $this->assertSame('vendor_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Vendor::class, $aViewData['edit']);
    }

    /**
     * Vendor_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Vendor_Main');
        $this->assertSame('vendor_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayNotHasKey('edit', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Vendor_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxvendor', 'save', '{ throw new Exception("save"); }');
        oxTestModules::addFunction('oxvendor', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxvendor', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxvendor', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction('oxvendor', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxvendor', 'setLanguage', '{ return true; }');

        // testing..
        try {
            $oView = oxNew('Vendor_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "Error in Vendor_Main::save()");

            return;
        }

        $this->fail("Error in Vendor_Main::save()");
    }

    /**
     * Vendor_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxvendor', 'save', '{ throw new Exception("save"); }');
        oxTestModules::addFunction('oxvendor', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxvendor', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxvendor', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction('oxvendor', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxvendor', 'setLanguage', '{ return true; }');

        // testing..
        try {
            $oView = oxNew('Vendor_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "Error in Vendor_Main::saveinnlang()");

            return;
        }

        $this->fail("Error in Vendor_Main::saveinnlang()");
    }
}
