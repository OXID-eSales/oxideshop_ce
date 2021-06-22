<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxArticle;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
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
    protected function setUp(): void
    {
        parent::setUp();
        // backuping
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
        $fake = new stdClass();
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
        $fake = new stdClass();
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
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $this->assertEquals('xxx', $tableViewNameGenerator->getViewName('xxx', 'xxx'));
    }

    public function testError_404_handler()
    {
        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->method('handlePageNotFoundError')->with($this->equalTo('asd'));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        error_404_handler('asd');
    }
}
