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

use \oxField;
use oxGbEntry;
use \oxTestModules;

/**
 * Tests for Adminguestbook_Main class
 */
class AdminguestbookMainTest extends \OxidTestCase
{

    /**
     * Adminguestbook_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "xxx");
        oxTestModules::addFunction('oxgbentry', 'save', '{ return true; }');

        // testing..
        $oView = oxNew('Adminguestbook_Main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertTrue($aViewData["edit"] instanceof oxGbEntry);
        $this->assertEquals($this->getConfig()->getConfigParam("blGBModerate"), $aViewData["blShowActBox"]);

        $this->assertEquals('adminguestbook_main.tpl', $sTplName);
    }

    /**
     * Adminguestbook_Main::Render() test case
     *
     * @return null
     */
    public function testRenderDefaultOxid()
    {
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("saved_oxid", "-1");

        // testing..
        $oView = oxNew('Adminguestbook_Main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertFalse(isset($aViewData["edit"]));
        $this->assertTrue(isset($aViewData["oxid"]));
        $this->assertEquals("-1", $aViewData["oxid"]);
        $this->assertEquals($this->getConfig()->getConfigParam("blGBModerate"), $aViewData["blShowActBox"]);

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

        $this->setRequestParameter("oxid", "xxx");
        $this->setRequestParameter("editval", array("xxx"));

        $oView = oxNew('Adminguestbook_Main');
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

        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", array("xxx"));

        $oView = oxNew('Adminguestbook_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["updatelist"]));
        $this->assertEquals(1, $aViewData["updatelist"]);
    }
}
