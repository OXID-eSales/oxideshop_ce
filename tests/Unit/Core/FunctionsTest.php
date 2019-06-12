<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxArticle;

use OxidEsales\EshopCommunity\Core\Exception\SystemComponentException;
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

    public function testOxNewWithExistingClassName()
    {
        $article = oxNew('oxArticle');

        $this->assertTrue($article instanceof \OxidEsales\EshopCommunity\Application\Model\Article);
    }

    public function testOxNewWithNonExistingClassName()
    {
        $this->expectException(SystemComponentException::class);
        $this->expectExceptionMessage('non_existing_class');

        oxNew("non_existing_class");
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
        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->at(0))->method('handlePageNotFoundError')->with($this->equalTo(''));
        $oUtils->expects($this->at(1))->method('handlePageNotFoundError')->with($this->equalTo('asd'));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        error_404_handler();
        error_404_handler('asd');
    }
}
