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
 * Tests for Tools_List class
 */
class Unit_Admin_ToolsListTest extends OxidTestCase
{

    /**
     * Tools_List::Performsql() test case
     *
     * @return null
     */
    public function testPerformsql()
    {
        // testing..
        oxRegistry::getSession()->setVariable('auth', "oxdefaultadmin");
        modConfig::setRequestParameter("updatesql", 'select * from oxvoucher');

        $oView = new Tools_List();
        $oView->performsql();
        $this->assertTrue(isset($oView->aSQLs));
    }

    /**
     * Tools_List::ProcessFiles() test case
     *
     * @return null
     */
    public function testProcessFiles()
    {
        // testing..
        $_FILES['myfile']['name'] = array("test.txt");

        $oView = new Tools_List();
        $this->assertNull($oView->UNITprocessFiles());
    }

    /**
     * Tools_List::PrepareSQL() test case
     *
     * @return null
     */
    public function testPrepareSQL()
    {
        // defining parameters
        $sSQL = 'select * from oxvoucher';
        $iSQLlen = '';

        // testing..
        $oView = new Tools_List();
        $this->assertTrue($oView->UNITprepareSQL($sSQL, $iSQLlen));
        $this->assertTrue(isset($oView->aSQLs));
    }

    /**
     * Tools_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Tools_List();
        $this->assertEquals('tools_list.tpl', $oView->render());
    }

    /**
     * Tools_List::updateViews() test case
     *
     * @return null
     */
    public function testUpdateViews()
    {
        modSession::getInstance()->setVar('malladmin', true);

        $oView = new Tools_List();
        $oView->updateViews();

        // assert that updating was successful
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData['blViewSuccess']);
    }
}
