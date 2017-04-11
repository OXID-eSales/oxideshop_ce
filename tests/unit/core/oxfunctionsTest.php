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

class Unit_Core_oxfunctionsTest extends OxidTestCase
{

    protected $_sRequestMethod = null;
    protected $_sRequestUri = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        // backuping
        $this->sRequestMethod = $_SERVER["REQUEST_METHOD"];
        $this->_sRequestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // restoring
        $_SERVER["REQUEST_METHOD"] = $this->_sRequestMethod;
        $_SERVER['REQUEST_URI'] = $this->_sRequestUri;
        parent::tearDown();
    }

    // testing request uri getter
    public function testGetRequestUrl()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'test.php?param1=value1&param2=value2';

        $this->assertEquals(str_replace(array('&', 'test.php'), array('&amp;', 'index.php'), $sUri), getRequestUrl());
    }

    // testing request uri getter
    public function testGetRequestUrlEmptyParams()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = '/shop/';

        $this->assertNull(getRequestUrl());
    }

    // testing request uri getter
    public function testGetRequestUrlSubfolder()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['SCRIPT_URI'] = $sUri = '/shop/?cl=details';

        $this->assertEquals('index.php?cl=details', getRequestUrl());
    }

    // testing request removing sid from link
    public function testGetRequestUrl_removingSID()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = 'test.php?param1=value1&sid=zzz&sysid=vvv&param2=ttt';
        $this->assertEquals('index.php?param1=value1&amp;sysid=vvv&amp;param2=ttt', getRequestUrl());

        $_SERVER['REQUEST_URI'] = 'test.php?sid=zzz&param1=value1&sysid=vvv&param2=ttt';
        $this->assertEquals('index.php?param1=value1&amp;sysid=vvv&amp;param2=ttt', getRequestUrl());

        $_SERVER['REQUEST_URI'] = 'test.php?param1=value1&sysid=vvv&param2=ttt&sid=zzz';
        $this->assertEquals('index.php?param1=value1&amp;sysid=vvv&amp;param2=ttt', getRequestUrl());
    }

    public function test_isAdmin()
    {
        $this->assertEquals(false, isAdmin());
    }

    public function test_dumpVar()
    {
        $myConfig = oxRegistry::getConfig();
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
        $oNew = oxnew('oxarticle');
        $this->assertTrue($oNew instanceof oxarticle);

        try {
            $oNew = oxnew('oxxxx');
        } catch (oxSystemComponentException $oExcp) {
            return;
        }
        $this->fail('error testing oxnew()');
    }

    public function testOx_get_template()
    {
        $fake = new stdClass;
        $fake->oxidcache = new oxField('test', oxField::T_RAW);
        $sRes = 'aa';
        $this->assertEquals(true, ox_get_template('blah', $sRes, $fake));
        $this->assertEquals('test', $sRes);
        if (oxRegistry::getConfig()->isDemoShop()) {
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
