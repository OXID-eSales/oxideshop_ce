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
 * Smarty emulation class
 */
class Unit_Admin_GenExportDoTest_smarty
{

    /**
     * Instance storage
     *
     * @var Unit_Admin_GenExportDoTest_smarty
     */
    protected static $_oInst = null;

    /**
     * Call data log
     *
     * @var call data log
     */
    protected $_aCallData = array();

    /**
     * Emulated smarty instance getter
     *
     * @return Unit_Admin_GenExportDoTest_smarty
     */
    public static function getInstance()
    {
        if (self::$_oInst === null) {
            self::$_oInst = new Unit_Admin_GenExportDoTest_smarty();
        }

        return self::$_oInst;
    }

    /**
     * Logging call data
     *
     * @param string $sMethod called method
     * @param array  $aParams parameters
     *
     * @return null
     */
    public function __call($sMethod, $aParams)
    {
        $this->_aCallData[] = array($sMethod, $aParams);
    }

    /**
     * Returns call log
     *
     * @return array
     */
    public function getLog()
    {
        return $this->_aCallData;
    }
}

/**
 * Tests for GenExport_Do class
 */
class Unit_Admin_GenExportDoTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sFile = getTestsBasePath() . "/misc/test.txt";
        if (file_exists($sFile)) {
            unlink($sFile);
        }
        parent::tearDown();
    }

    /**
     * GenExport_Do::NextTick() test case
     *
     * @return null
     */
    public function testNextTickNoMoreArticleFound()
    {
        $oView = $this->getMock("GenExport_Do", array("getOneArticle", "write"));
        $oView->expects($this->once())->method('getOneArticle')->will($this->returnValue(false));
        $oView->expects($this->never())->method('write');
        $this->assertFalse($oView->nextTick(1));
    }

    /**
     * GenExport_Do::NextTick() test case
     *
     * @return null
     */
    public function testNextTick()
    {
        oxTestModules::addFunction("oxUtilsView", "getSmarty", "{return Unit_Admin_GenExportDoTest_smarty::getInstance();}");

        $oView = $this->getMock("GenExport_Do", array("getOneArticle", "write", "getViewId"));
        $oView->expects($this->once())->method('getOneArticle')->will($this->returnValue(new oxArticle));
        $oView->expects($this->once())->method('write');
        $oView->expects($this->once())->method('getViewId')->will($this->returnValue('dyn_interface'));
        $this->assertEquals(2, $oView->nextTick(1));

        $aCallLog = Unit_Admin_GenExportDoTest_smarty::getInstance()->getLog();

        //#3611
        $this->assertEquals("assign", $aCallLog[0][0]);
        $this->assertEquals("sCustomHeader", $aCallLog[0][1][0]);

        $this->assertEquals("assign_by_ref", $aCallLog[1][0]);
        $this->assertEquals("assign_by_ref", $aCallLog[2][0]);
        $this->assertEquals("assign", $aCallLog[3][0]);
        $this->assertEquals("assign", $aCallLog[4][0]);
        $this->assertEquals("fetch", $aCallLog[5][0]);
    }

    /**
     * GenExport_Do::Write() test case
     *
     * @return null
     */
    public function testWrite()
    {
        // defining parameters
        $sLine = 'TestExport';

        // testing..
        $oView = new GenExport_Do();
        $oView->fpFile = @fopen(getTestsBasePath() . "/misc/test.txt", "w");
        $oView->write($sLine);
        fclose($oView->fpFile);
        $sFileCont = file_get_contents(getTestsBasePath() . "/misc/test.txt", true);
        $this->assertEquals($sLine . "\r\n", $sFileCont);
    }

    /**
     * GenExport_Do::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new GenExport_Do();
        $this->assertEquals('dynbase_do.tpl', $oView->render());
    }
}
