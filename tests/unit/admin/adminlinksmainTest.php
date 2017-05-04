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
 * Tests for Adminlinks_Main class
 */
class Unit_Admin_AdminLinksMainTest extends OxidTestCase
{

    /**
     * Adminlinks_Main::render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setRequestParameter("oxid", -1);
        modConfig::setRequestParameter("saved_oxid", -1);

        // testing..
        $oView = new Adminlinks_main();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertEquals('-1', $aViewData["oxid"]);
        $this->assertEquals('adminlinks_main.tpl', $sTplName);
    }

    /**
     * Adminlinks_Main::Render() test case
     *
     * @return null
     */
    public function testRenderWithExistingLink()
    {
        modConfig::setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxlinks"));

        // testing..
        $oView = new Adminlinks_main();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertEquals("adminlinks_main.tpl", $sTplName);
    }

    /**
     * Adminlinks_Main::save() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxgbentry', 'save', '{ return true; }');
        oxTestModules::addFunction('oxgbentry', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxgbentry', 'load', '{ return true; }');

        modConfig::setRequestParameter("oxid", "xxx");

        // testing..
        $oView = new Adminlinks_main();
        $oView->saveinnlang();
        $aViewData = $oView->getViewData();

        $this->assertNotNull($aViewData["updatelist"]);
        $this->assertEquals(1, $aViewData["updatelist"]);

    }

    /**
     * Adminlinks_Main::save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxgbentry', 'save', '{ return true; }');
        oxTestModules::addFunction('oxgbentry', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxgbentry', 'load', '{ return true; }');

        modConfig::setRequestParameter("oxid", "xxx");

        // testing..
        $oView = new Adminlinks_main();
        $oView->save();
        $aViewData = $oView->getViewData();

        $this->assertNotNull($aViewData["updatelist"]);
        $this->assertEquals(1, $aViewData["updatelist"]);
    }

    /**
     * Adminlinks_Main::testGetTextEditor() test case
     *
     * @return null
     */
    public function testGetTextEditor()
    {
        $oAdminDetails = new adminlinks_main();
        $oEditor = $oAdminDetails->UNITgetTextEditor(10, 10, new oxarticle, 'oxarticles__oxtitle');

        $this->assertFalse($oEditor);
    }
}
