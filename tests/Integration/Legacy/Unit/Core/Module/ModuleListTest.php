<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * @package Unit\Core
 */
#[\PHPUnit\Framework\Attributes\Group('module')]
class ModuleListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * test setup
     */
    protected function setup(): void
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxconfig');
        $this->cleanUpTable('oxconfigdisplay');
        $this->cleanUpTable('oxtplblocks');

        parent::tearDown();
    }

    /**
     * @return array
     */
    public function providerIsVendorDir()
    {
        return [
            ['module1', false],
            ['vendor1', true],
            ['notVendor', false],
            ['this_directory_does_not_exist', false]
        ];
    }

    /**
     * ModuleList::parseModuleChains() test case, empty
     */
    public function testParseModuleChainsEmpty()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = [];
        $modulesArray = [];
        $this->assertSame($modulesArray, $moduleList->parseModuleChains($modules));
    }

    /**
     * ModuleList::parseModuleChains() test case, single
     */
    public function testParseModuleChainsSingle()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = ['oxtest' => 'test/mytest'];
        $modulesArray = ['oxtest' => ['test/mytest']];
        $this->assertSame($modulesArray, $moduleList->parseModuleChains($modules));
    }

    /**
     * ModuleList::parseModuleChains() test case
     */
    public function testParseModuleChains()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = ['oxtest' => 'test/mytest&test1/mytest1'];
        $modulesArray = ['oxtest' => ['test/mytest', 'test1/mytest1']];
        $this->assertSame($modulesArray, $moduleList->parseModuleChains($modules));
    }
}
