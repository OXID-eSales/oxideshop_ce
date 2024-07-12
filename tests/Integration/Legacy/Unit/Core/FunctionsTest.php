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

    public function testGetViewName()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $this->assertEquals('xxx', $tableViewNameGenerator->getViewName('xxx', 'xxx'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testError_404_handler()
    {
        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['handlePageNotFoundError']);
        $oUtils->method('handlePageNotFoundError')->with($this->equalTo('asd'));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        error_404_handler('asd');
    }
}
