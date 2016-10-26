<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \OxidEsales\EshopCommunity\Application\Model\Vendor;

use \Exception;
use \oxTestModules;

/**
 * Tests for Vendor_Main class
 */
class VendorMainTest extends \OxidTestCase
{

    /**
     * Vendor_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = $this->getMock("Vendor_Main", array("_createCategoryTree"));
        $oView->expects($this->once())->method('_createCategoryTree');
        $this->assertEquals('vendor_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof vendor);
    }

    /**
     * Vendor_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Vendor_Main');
        $this->assertEquals('vendor_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertFalse(isset($aViewData['edit']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Vendor_Main::Save() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Vendor_Main::save()");

            return;
        }
        $this->fail("Error in Vendor_Main::save()");
    }

    /**
     * Vendor_Main::Saveinnlang() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Vendor_Main::saveinnlang()");

            return;
        }
        $this->fail("Error in Vendor_Main::saveinnlang()");
    }
}
