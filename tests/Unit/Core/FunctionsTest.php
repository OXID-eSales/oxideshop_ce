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
namespace Unit\Core;

use \oxArticle;

use \stdClass;
use \oxField;
use \oxTestModules;

/**
 * Tests for functions in source/oxfunctions.php file.
 */
class FunctionsTest extends \OxidTestCase
{
    /** @var string */
    protected $requestMethod = null;

    /** @var string */
    protected $requestUri = null;

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        // backuping
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        // restoring
        $_SERVER["REQUEST_METHOD"] = $this->requestMethod;
        $_SERVER['REQUEST_URI'] = $this->requestUri;
        parent::tearDown();
    }

    public function test_isAdmin()
    {
        $this->assertEquals(false, isAdmin());
    }

    public function test_dumpVar()
    {
        $myConfig = $this->getConfig();
        @unlink($myConfig->getConfigParam('sCompileDir') . "/vardump.txt");
        dumpVar("bobo", true);
        $file = file_get_contents($myConfig->getConfigParam('sCompileDir') . "/vardump.txt");
        $file = str_replace("\r", "", $file);
        @unlink($myConfig->getConfigParam('sCompileDir') . "/vardump.txt");
        $this->assertEquals($file, "'bobo'", $file);
    }

    public function testIsSearchEngineUrl()
    {
        $this->assertFalse(isSearchEngineUrl());
    }

    /**
     * Testing sorting utility function
     */
    public function testCmpart()
    {
        $oA = new stdClass();
        $oA->cnt = 10;

        $oB = new stdClass();
        $oB->cnt = 10;

        $this->assertTrue(cmpart($oA, $oB) == 0);

        $oA->cnt = 10;
        $oB->cnt = 20;

        $this->assertTrue(cmpart($oA, $oB) == -1);
    }

    public function testOxNew()
    {
        $oNew = oxNew('oxArticle');
        $this->assertTrue($oNew instanceof \OxidEsales\EshopCommunity\Application\Model\Article);

        $this->setExpectedException('oxSystemComponentException');
        oxNew('oxxxx');
    }

    public function testOx_get_template()
    {
        $fake = new stdClass;
        $fake->oxidcache = new oxField('test', oxField::T_RAW);
        $sRes = 'aa';
        $this->assertEquals(true, ox_get_template('blah', $sRes, $fake));
        $this->assertEquals('test', $sRes);
        if ($this->getConfig()->isDemoShop()) {
            $this->assertEquals($fake->security, true);
        }
    }

    public function testOx_get_timestamp()
    {
        $fake = new stdClass;
        $this->assertEquals(true, ox_get_timestamp('blah', $res, $fake));
        $this->assertEquals(true, is_numeric($res));
        $tm = time() - $res;
        $this->assertEquals(true, ($tm >= 0) && ($tm < 2));
        $fake->oxidtimecache = new oxField('test', oxField::T_RAW);
        $this->assertEquals(true, ox_get_timestamp('blah', $res, $fake));
        $this->assertEquals('test', $res);
    }

    public function testOx_get_secure()
    {
        $o = null;
        $this->assertEquals(true, ox_get_secure("s", $o));
    }

    public function testOx_get_trusted()
    {
        $o = null;
        // in php void functions also return - null
        $this->assertEquals(null, ox_get_trusted("s", $o));
    }

    public function testGetViewName()
    {
        $this->assertEquals('xxx', getViewName('xxx', 'xxx'));
    }

    public function testError_404_handler()
    {
        $oUtils = $this->getMock('oxutils', array('handlePageNotFoundError'));
        $oUtils->expects($this->at(0))->method('handlePageNotFoundError')->with($this->equalTo(''));
        $oUtils->expects($this->at(1))->method('handlePageNotFoundError')->with($this->equalTo('asd'));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        error_404_handler();
        error_404_handler('asd');
    }
}
