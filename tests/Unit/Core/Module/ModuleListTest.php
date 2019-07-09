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
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxconfig');
        $this->cleanUpTable('oxconfigdisplay');
        $this->cleanUpTable('oxtplblocks');

        parent::tearDown();
    }

    /**
     * oxmodulelist::buildModuleChains() test case, empty
     *
     * @return null
     */
    public function testBuildModuleChainsEmpty()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $aModules = array();
        $aModulesArray = array();
        $this->assertEquals($aModules, $oModuleList->buildModuleChains($aModulesArray));
    }

    /**
     * oxmodulelist::buildModuleChains() test case, single
     *
     * @return null
     */
    public function testBuildModuleChainsSingle()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $aModules = array('oxtest' => 'test/mytest');
        $aModulesArray = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aModules, $oModuleList->buildModuleChains($aModulesArray));
    }

    /**
     * oxmodulelist::buildModuleChains() test case
     *
     * @return null
     */
    public function testBuildModuleChains()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $aModules = array('oxtest' => 'test/mytest&test1/mytest1');
        $aModulesArray = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $this->assertEquals($aModules, $oModuleList->buildModuleChains($aModulesArray));
    }

    /**
     * oxmodulelist::diffModuleArrays() test case, empty
     *
     * @return null
     */
    public function testDiffModuleArraysEmpty()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $aAllModules = array();
        $aRemModules = array();
        $this->assertEquals($aAllModules, $oModuleList->diffModuleArrays($aAllModules, $aRemModules));
    }

    /**
     * oxmodulelist::diffModuleArrays() test case, remove single
     *
     * @return null
     */
    public function testDiffModuleArraysRemoveSingle()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aRemModules = array('oxtest' => 'test/mytest');
        $aMrgModules = array();
        $this->assertEquals($aMrgModules, $oModuleList->diffModuleArrays($aAllModules, $aRemModules));
    }

    /**
     * oxmodulelist::diffModuleArrays() test case, remove
     *
     * @return null
     */
    public function testDiffModuleArraysRemove()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aRemModules = array('oxtest' => array('test/mytest'));
        $aMrgModules = array();
        $this->assertEquals($aMrgModules, $oModuleList->diffModuleArrays($aAllModules, $aRemModules));
    }

    /**
     * oxmodulelist::diffModuleArrays() test case, remove from chain
     *
     * @return null
     */
    public function testDiffModuleArraysRemoveChain()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');
        $aAllModules = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $aRemModules = array('oxtest' => array('test1/mytest1'));
        $aMrgModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aMrgModules, $oModuleList->diffModuleArrays($aAllModules, $aRemModules));
    }

    /**
     * oxmodulelist::diffModuleArrays() test case, remove from chain and unused key
     *
     * @return null
     */
    public function testDiffModuleArraysRemoveChainAndKey()
    {
        $oModuleList = $this->getProxyClass('oxmodulelist');
        $aAllModules = array('oxtest' => array('test/mytest', 'test1/mytest1'), 'oxtest2' => array('test2/mytest2'));
        $aRemModules = array('oxtest' => array('test/mytest'), 'oxtest2' => array('test2/mytest2'));
        $aMrgModules = array('oxtest' => array('test1/mytest1'));
        $this->assertEquals($aMrgModules, $oModuleList->diffModuleArrays($aAllModules, $aRemModules));
    }

    /**
     * oxmodulelist::getActiveModuleInfo() test case
     *
     * @return null
     */
    public function testGetActiveModuleInfoPathsNotSet()
    {
        $modulePaths = array(
            'testExt1' => 'testExt1',
            'testExt2' => 'testExt2'
        );

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getModuleConfigParametersByKey'));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Paths')->willReturn($modulePaths);

        $this->assertEquals($modulePaths, $oModuleList->getActiveModuleInfo());
    }

    /**
     * oxmodulelist::getDisabledModules() test case
     *
     * @return null
     */
    public function testGetModulePaths()
    {
        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );

        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals($aModulePaths, $oModuleList->getModulePaths());
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
     * @param string $vendorDirectoryName
     * @param bool   $isVendor
     * @dataProvider providerIsVendorDir
     */
    public function testIsVendorDir($vendorDirectoryName, $isVendor)
    {
        $structure = [
            'modules' => [
                'module1' => [
                    'metadata.php' => '<?php'
                ],
                'vendor1' => [
                    'module2' => [
                        'metadata.php' => '<?php'
                    ]
                ],
                'notVendor' => [
                    'someDirectory' => [
                        'file.php' => '<?php'
                    ]
                ]
            ]
        ];
        $vfsStream = $this->getVfsStreamWrapper();
        $vfsStream->createStructure($structure);

        $this->getConfig()->setConfigParam('sShopDir', $vfsStream->getRootPath());
        $modulesDir = $this->getConfig()->getModulesDir();
        $moduleList = oxNew('oxModuleList');

        $this->assertSame($isVendor, $moduleList->_isVendorDir($modulesDir . "/$vendorDirectoryName"));
    }

    public function testGetModuleFilesWhenFileWasSet()
    {
        $aModuleFiles = array(
            'myext1' => array("title" => "test title 1")
        );
        $this->getConfig()->setConfigParam('aModuleFiles', $aModuleFiles);
        $oModuleList = oxNew('oxModuleList');

        $this->assertSame($aModuleFiles, $oModuleList->getModuleFiles());
    }

    public function testGetModuleFilesWhenFileWasNotSet()
    {
        $this->getConfig()->setConfigParam('aModuleFiles', array());

        $oModuleList = oxNew('oxModuleList');

        $this->assertSame(array(), $oModuleList->getModuleFiles());
    }

    /**
     * ModuleList::parseModuleChains() test case, empty
     *
     * @return null
     */
    public function testParseModuleChainsEmpty()
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = array();
        $modulesArray = array();
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
        $modules = array('oxtest' => 'test/mytest');
        $modulesArray = array('oxtest' => array('test/mytest'));
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
        $modules = array('oxtest' => 'test/mytest&test1/mytest1');
        $modulesArray = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $this->assertEquals($modulesArray, $moduleList->parseModuleChains($modules));
    }
}
