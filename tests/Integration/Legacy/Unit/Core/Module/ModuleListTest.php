<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleListTest extends \OxidTestCase
{

    /**
     * test setup
     *
     * @return null
     */
    public function setup(): void
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
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
     *
     * @return null
     */
    public function testParseModuleChainsEmpty()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = [];
        $modulesArray = [];
        $this->assertEquals($modulesArray, $moduleList->parseModuleChains($modules));
    }

    /**
     * ModuleList::parseModuleChains() test case, single
     *
     * @return null
     */
    public function testParseModuleChainsSingle()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = ['oxtest' => 'test/mytest'];
        $modulesArray = ['oxtest' => ['test/mytest']];
        $this->assertEquals($modulesArray, $moduleList->parseModuleChains($modules));
    }

    /**
     * ModuleList::parseModuleChains() test case
     *
     * @return null
     */
    public function testParseModuleChains()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = ['oxtest' => 'test/mytest&test1/mytest1'];
        $modulesArray = ['oxtest' => ['test/mytest', 'test1/mytest1']];
        $this->assertEquals($modulesArray, $moduleList->parseModuleChains($modules));
    }
}
