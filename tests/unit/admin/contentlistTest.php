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
 * Tests for Content_List class
 */
class Unit_Admin_ContentListTest extends OxidTestCase
{

    /**
     * Content_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setRequestParameter("folder", "sTestFolder");

        // testing..
        $oView = new Content_List();
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();
        $this->assertEquals(oxRegistry::getConfig()->getConfigParam('afolder'), $aViewData["CMSFOLDER_EMAILS"]);
        $this->assertEquals("sTestFolder", $aViewData["folder"]);

        $this->assertEquals('content_list.tpl', $sTplName);
    }

    /**
     * Content_List::PrepareWhereQuery() test case
     *
     * @return null
     */
    public function testPrepareWhereQueryUserDefinedFolder()
    {
        modConfig::setRequestParameter("folder", "testFolder");
        $sViewName = getviewName("oxcontents");

        // defining parameters
        $oView = new Content_List();
        $sResQ = $oView->UNITprepareWhereQuery(array(), "");

        $sQ .= " and {$sViewName}.oxfolder = 'testFolder'";

        $this->assertEquals($sQ, $sResQ);
    }
}
