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
 * Tests for Adminguestbook_Main class
 */
class Unit_Admin_AdminguestbookMainTest extends OxidTestCase
{

    /**
     * Adminguestbook_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setRequestParameter("oxid", "xxx");
        oxTestModules::addFunction('oxgbentry', 'save', '{ return true; }');

        // testing..
        $oView = new Adminguestbook_Main();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertTrue($aViewData["edit"] instanceof oxgbentry);
        $this->assertEquals(modConfig::getInstance()->getConfigParam("blGBModerate"), $aViewData["blShowActBox"]);

        $this->assertEquals('adminguestbook_main.tpl', $sTplName);
    }

    /**
     * Adminguestbook_Main::Render() test case
     *
     * @return null
     */
    public function testRenderDefaultOxid()
    {
        modConfig::setRequestParameter("oxid", "-1");
        modConfig::setRequestParameter("saved_oxid", "-1");

        // testing..
        $oView = new Adminguestbook_Main();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertFalse(isset($aViewData["edit"]));
        $this->assertTrue(isset($aViewData["oxid"]));
        $this->assertEquals("-1", $aViewData["oxid"]);
        $this->assertEquals(modConfig::getInstance()->getConfigParam("blGBModerate"), $aViewData["blShowActBox"]);

        $this->assertEquals('adminguestbook_main.tpl', $sTplName);
    }

    /**
     * Adminguestbook_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxgbentry', 'load', '{ return true; }');
        oxTestModules::addFunction('oxgbentry', 'save', '{ return true; }');

        modConfig::setRequestParameter("oxid", "xxx");
        modConfig::setRequestParameter("editval", array("xxx"));

        $oView = new Adminguestbook_Main();
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["updatelist"]));
        $this->assertEquals(1, $aViewData["updatelist"]);
    }

    /**
     * Adminguestbook_Main::Save() test case
     *
     * @return null
     */
    public function testSaveDefaultOxid()
    {
        oxTestModules::addFunction('oxgbentry', 'save', '{ $this->oxgbentries__oxid = new oxField( "testId" ); return true; }');

        modConfig::setRequestParameter("oxid", "-1");
        modConfig::setRequestParameter("editval", array("xxx"));

        $oView = new Adminguestbook_Main();
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["updatelist"]));
        $this->assertEquals(1, $aViewData["updatelist"]);
    }
}
