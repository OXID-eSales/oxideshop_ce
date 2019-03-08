<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use oxRegistry;

/**
 * @group module
 * @package Integration\Modules
 */
class ModuleWithNoMetadataTest extends \OxidTestCase
{
    /**
     * Tests if module was activated.
     */
    public function testGetDisabledModules()
    {
        $this->markTestSkipped('We don not use aDisabledModules anymore, no sense to test.');

        $this->getConfig()->setConfigParam("aDisabledModules", []);

        $sShopDir = realpath(dirname(__FILE__)) . '/TestData/';

        oxRegistry::getConfig()->setConfigParam('sShopDir', $sShopDir);

        $oModuleList = oxNew('oxModuleList');

        $this->assertEquals(array(), $oModuleList->getDisabledModules());

        $oModuleList->getModulesFromDir($sShopDir . 'modules/');

        $this->assertFalse(in_array(null, $oModuleList->getDisabledModules()), 'Module id with value null was found in disabled modules list');
    }
}
