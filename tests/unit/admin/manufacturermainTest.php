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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Manufacturer_Main class
 */
class Unit_Admin_ManufacturerMainTest extends OxidTestCase
{

    /**
     * Manufacturer_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxmanufacturer', 'isDerived', '{ return true; }');

        // testing..
        $oView = new Manufacturer_Main();
        $this->assertEquals('manufacturer_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof oxmanufacturer);
        $this->assertTrue(isset($aViewData['readonly']));
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        modConfig::setRequestParameter("oxid", "-1");

        // testing..
        $oView = new Manufacturer_Main();
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
        modConfig::getInstance()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = new Manufacturer_Main();
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
        modConfig::getInstance()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = new Manufacturer_Main();
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Manufacturer_Main::saveinnlang()");

            return;
        }
        $this->fail("error in Manufacturer_Main::saveinnlang()");
    }
}
