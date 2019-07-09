<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleInstaller;
use OxidEsales\EshopCommunity\Core\Exception\ModuleValidationException;
use OxidEsales\EshopCommunity\Core\Exception\StandardException;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleInstallerTest extends \OxidTestCase
{
    /**
     * oxModuleInstaller::buildModuleChains() test case, empty
     */
    public function testBuildModuleChainsEmpty()
    {
        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aModules = array();
        $aModulesArray = array();
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case, single
     */
    public function testBuildModuleChainsSingle()
    {
        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aModules = array('oxtest' => 'test/mytest');
        $aModulesArray = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case
     */
    public function testBuildModuleChains()
    {
        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aModules = array('oxtest' => 'test/mytest&test1/mytest1');
        $aModulesArray = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }
}
