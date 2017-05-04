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
 * Tests for Attribute_Main class
 */
class Unit_Admin_AttributeMainTest extends OxidTestCase
{

    /**
     * Attribute_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxattribute", "isDerived", "{return true;}");
        modConfig::setRequestParameter("oxid", "testId");

        // testing..
        $oView = new Attribute_Main();
        $this->assertEquals('attribute_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof oxattribute);
    }

    /**
     * Attribute_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        modConfig::setRequestParameter("oxid", "-1");

        // testing..
        $oView = new Attribute_Main();
        $this->assertEquals('attribute_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Attribute_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = new Attribute_Main();
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Attribute_Main::save()");

            return;
        }
        $this->fail("error in Attribute_Main::save()");
    }

    /**
     * Attribute_Main::Save() test case
     *
     * @return null
     */
    public function testSaveDefaultOxid()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ $this->oxattribute__oxid = new oxField("testId"); return true; }');
        modConfig::setRequestParameter("oxid", "-1");

        // testing..
        $oView = new Attribute_Main();
        $oView->save();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Attribute_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = new Attribute_Main();
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Attribute_Main::Saveinnlang()");

            return;
        }
        $this->fail("error in Attribute_Main::Saveinnlang()");
    }

    /**
     * Attribute_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlangDefaultOxid()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ $this->oxattribute__oxid = new oxField("testId"); return true; }');
        modConfig::setRequestParameter("oxid", "-1");
        modConfig::setRequestParameter("new_lang", "999");

        // testing..
        $oView = new Attribute_Main();
        $oView->saveinnlang();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
        $this->assertEquals(999, oxRegistry::getConfig()->getRequestParameter("new_lang"));
    }
}
