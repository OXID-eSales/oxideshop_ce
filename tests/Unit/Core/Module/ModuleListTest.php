<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxDb;
use OxidEsales\Eshop\Core\Module\ModuleMetadataValidator;
use OxidEsales\EshopCommunity\Core\Module\ModuleList;

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
     * oxmodulelist::getModulesWithExtendedClass() test case
     *
     * @return null
     */
    public function testgetModulesWithExtendedClass()
    {
        $aModules = array(
            'oxorder' => 'testExt1/module1&testExt2/module1',
            'oxnews'  => 'testExt2/module2'
        );

        $aResult = array(
            'oxorder' => array('testExt1/module1', 'testExt2/module1'),
            'oxnews'  => array('testExt2/module2')
        );

        $oModuleList = $this->getProxyClass('oxmodulelist');
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $this->assertEquals($aResult, $oModuleList->getModulesWithExtendedClass());
    }

    /**
     * oxmodulelist::extractModulePaths() test case
     *
     * @return null
     */
    public function testExtractModulePaths()
    {
        $aModules = array(
            'oxorder' => 'testExt1/module1&testExt2/module1',
            'oxnews'  => 'testExt2/module2'
        );

        $aResult = array(
            'testExt1' => 'testExt1',
            'testExt2' => 'testExt2'
        );

        $oModuleList = $this->getProxyClass('oxmodulelist');
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $this->assertEquals($aResult, $oModuleList->extractModulePaths());
    }

    /**
     * oxmodulelist::getActiveModuleInfo() test case
     *
     * @return null
     */
    public function testGetActiveModuleInfoPathsNotSet()
    {
        $aModulePaths = array(
            'testExt1' => 'testExt1',
            'testExt2' => 'testExt2'
        );

        $aResult = array(
            'testExt1' => 'testExt1'
        );

        $aDisabledModules = array(
            'testExt2'
        );

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('extractModulePaths', 'getModuleConfigParametersByKey', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('extractModulePaths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Paths')->will($this->returnValue(false));
        $oModuleList->expects($this->once())->method('getDisabledModules')->will($this->returnValue($aDisabledModules));

        $this->assertEquals($aResult, $oModuleList->getActiveModuleInfo());
    }

    /**
     * oxmodulelist::getDisabledModuleInfo() test case
     *
     * @return null
     */
    public function testGetDeisabledModuleInfoNoDisabled()
    {
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getDisabledModules'));
        $oModuleList->expects($this->once())->method('getDisabledModules')->will($this->returnValue(array()));

        $this->assertEquals(array(), $oModuleList->getDisabledModuleInfo());
    }

    /**
     * oxmodulelist::getDisabledModuleInfo() test case
     *
     * @return null
     */
    public function testGetDisabledModuleInfoPathsNotSet()
    {
        $aModulePaths = array(
            'testExt1' => 'testExt1',
            'testExt2' => 'testExt2'
        );

        $aResult = array(
            'testExt1' => 'testExt1'
        );

        $aDisabledModules = array(
            'testExt1'
        );

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('extractModulePaths', 'getModuleConfigParametersByKey', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('extractModulePaths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Paths')->will($this->returnValue(false));
        $oModuleList->expects($this->once())->method('getDisabledModules')->will($this->returnValue($aDisabledModules));

        $this->assertEquals($aResult, $oModuleList->getDisabledModuleInfo());
    }

    /**
     * oxmodulelist::getDisabledModuleInfo() test case
     *
     * @return null
     */
    public function testGetDisabledModuleInfo()
    {
        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );
        $aResult = array(
            'testExt1' => 'testExt1/testExt11',
        );
        $aDisabledModules = array(
            'testExt1'
        );

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getModuleConfigParametersByKey', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Paths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getDisabledModules')->will($this->returnValue($aDisabledModules));

        $this->assertEquals($aResult, $oModuleList->getDisabledModuleInfo());
    }

    /**
     * oxmodulelist::getDisabledModules() test case
     *
     * @return null
     */
    public function testGetDisabledModules()
    {
        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );

        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals($aDisabledModules, $oModuleList->getDisabledModules());
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
     * oxmodulelist::getDisabledModuleClasses() test case
     *
     * @return null
     */
    public function testGetDisabledModuleClasses()
    {
        $aModules = array(
            'oxorder' => 'testExt1/testExt11/module1&testExt2/module1',
            'oxnews'  => 'testExt2/module2'
        );
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);

        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);

        $aDisabledModuleClasses = array(
            'testExt1/testExt11/module1',
            'testExt2/module1',
            'testExt2/module2'
        );
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals($aDisabledModuleClasses, $oModuleList->getDisabledModuleClasses());
    }

    /**
     * oxmodulelist::getDisabledModuleClasses() test case
     *
     * @return null
     */
    public function testGetDisabledModuleClassesIfNoPath()
    {
        $aModules = array(
            'oxorder' => 'testExt1/testExt11/module1&testExt2/module1',
            'oxnews'  => 'testExt2/module2'
        );
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);

        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
        );
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);

        $aDisabledModuleClasses = array(
            'testExt1/testExt11/module1',
            'testExt2/module1',
            'testExt2/module2'
        );
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals($aDisabledModuleClasses, $oModuleList->getDisabledModuleClasses());
    }

    public function testRemoveExtensions()
    {
        $aModules = array(
            'oxarticle' => 'mod/testExtension&mod7/testExtension2/&mod3/dir3/testExtension3',
            'oxorder'   => 'mod7/testModuleOrder&mod3/myextclass',
            'oxaddress' => 'mod/testExtension4'
        );
        $aModuleIdsToRemove = array('mod', 'mod7');
        $aModuleResult = array(
            'oxarticle' => 'mod3/dir3/testExtension3',
            'oxorder'   => 'mod3/myextclass'
        );

        $this->getConfig()->saveShopConfVar('aarr', 'aModules', $aModules);
        /** @var oxModuleList $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getDeletedExtensions'));
        $oModuleList->expects($this->any())->method('getDeletedExtensions')->will($this->returnValue($aModuleIdsToRemove));
        $oModuleList->_removeExtensions($aModuleIdsToRemove);

        $this->assertSame($aModuleResult, $this->getConfigParam('aModules'));
    }

    /**
     * oxmodulelist::_removeFromDisabledModulesArray() test case
     *
     * @return null
     */
    public function testRemoveFromDisabledModulesArray()
    {
        $aModules = array(
            'testExt1',
            'testExt2'
        );

        $aDeletedExt = array(
            'testExt2'
        );

        $aResult = array(
            'testExt1'
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aDisabledModules'), $this->equalTo($aResult));

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getConfig', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getDisabledModules')->will($this->returnValue($aModules));


        $oModuleList->_removeFromDisabledModulesArray($aDeletedExt);
    }

    /**
     * oxmodulelist::_removeFromModulesPathsArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesPathsArray()
    {
        $aModulePaths = array(
            'myext1' => array("title" => "test title 1"),
            'myext2' => array("title" => "test title 2")
        );

        $aDeletedExtIds = array("myext1");

        $aResult = array(
            'myext2' => array("title" => "test title 2")
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModulePaths'), $this->equalTo($aResult));

        /** @var \oxModuleList|\PHPUnit_Framework_MockObject_MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getConfig', 'getModuleConfigParametersByKey'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Paths')->will($this->returnValue($aModulePaths));


        $oModuleList->removeFromModulesArray(ModuleList::MODULE_KEY_PATHS, $aDeletedExtIds);
    }

    /**
     * oxmodulelist::_removeFromModulesVersionsArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesVersionsArray()
    {
        $aModuleVersions = array(
            'myext1' => '1.0',
            'myext2' => '2.4'
        );

        $aDeletedExtIds = array("myext1");

        $aResult = array(
            'myext2' => '2.4'
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleVersions'), $this->equalTo($aResult));

        /** @var \oxModuleList|\PHPUnit_Framework_MockObject_MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getConfig', 'getModuleConfigParametersByKey'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Versions')->will($this->returnValue($aModuleVersions));

        $oModuleList->removeFromModulesArray(ModuleList::MODULE_KEY_VERSIONS, $aDeletedExtIds);
    }

    /**
     * oxmodulelist::_removeFromModulesEventsArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesEventsArray()
    {
        $aModuleEvents = array(
            'myext1' => array('onActivate' => 'date'),
            'myext2' => array('onActivate' => 'date'),
        );

        $aDeletedExtIds = array("myext1");

        $aResult = array(
            'myext2' => array('onActivate' => 'date'),
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleEvents'), $this->equalTo($aResult));

        /** @var \oxModuleList|\PHPUnit_Framework_MockObject_MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getConfig', 'getModuleConfigParametersByKey'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Events')->will($this->returnValue($aModuleEvents));

        $oModuleList->removeFromModulesArray(ModuleList::MODULE_KEY_EVENTS, $aDeletedExtIds);
    }

    /**
     * oxmodulelist::_removeFromModulesFilesArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesFilesArray()
    {
        $aModuleFiles = array(
            'myext1' => array("title" => "test title 1"),
            'myext2' => array("title" => "test title 2")
        );

        $aDeletedExtIds = array("myext1");

        $aResult = array(
            'myext2' => array("title" => "test title 2")
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleFiles'), $this->equalTo($aResult));

        /** @var \oxModuleList|\PHPUnit_Framework_MockObject_MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getConfig', 'getModuleConfigParametersByKey'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Files')->will($this->returnValue($aModuleFiles));

        $oModuleList->removeFromModulesArray(ModuleList::MODULE_KEY_FILES, $aDeletedExtIds);
    }

    /**
     * oxmodulelist::_removeFromModulesTemplatesArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesTemplatesArray()
    {
        $aModuleTemplates = array(
            'myext1' => array("title" => "test title 1"),
            'myext2' => array("title" => "test title 2")
        );

        $aDeletedExtIds = array("myext1");

        $aResult = array(
            'myext2' => array("title" => "test title 2")
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleTemplates'), $this->equalTo($aResult));

        /** @var \oxModuleList|\PHPUnit_Framework_MockObject_MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getConfig', 'getModuleConfigParametersByKey'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Templates')->will($this->returnValue($aModuleTemplates));

        $oModuleList->removeFromModulesArray(ModuleList::MODULE_KEY_TEMPLATES, $aDeletedExtIds);
    }

    /**
     * oxmodulelist::_removeFromDatabase() test case
     *
     * @return null
     */
    public function testRemoveFromDatabase()
    {
        $oDb = oxDb::getDb();
        $oConfig = $this->getConfig();
        $sShopId = $oConfig->getBaseShopId();

        $sQ1 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue,  oxmodule) values
                                     ('_test1', '$sShopId', 'testVar1', 'int', 1, 'module:testext')";

        $sQ2 = "insert into oxconfigdisplay (oxid, oxcfgmodule, oxcfgvarname) values
                                     ('_test1', 'module:testext', 'testVarName1')";

        $sQ3 = "insert into oxtplblocks (oxid, oxshopid, oxblockname, oxmodule) values
                                     ('_test1', 'testVarName1', 'testBlockName1', 'testext')";

        $oDb->execute($sQ1);
        $oDb->execute($sQ2);
        $oDb->execute($sQ3);

        $aDeletedExtIds = array("myext1");

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $oModuleList->_removeFromDatabase($aDeletedExtIds);
    }

    /**
     * oxmodulelist::cleanup() test case
     */
    public function testCleanupMethodsCalledWithCorrectIds()
    {
        $aModuleInformation = array(
            'moduleId'  => array(
                'extensions' => array(
                    'ClassName' => 'moduleId/classPath',
                )
            ),
            'moduleId2' => array(
                'extensions' => array(
                    'ClassName'  => 'moduleId/classPath1',
                    'ClassName2' => 'moduleId/classPath2',
                ),
                'files'      => array(
                    'metadata.php'
                )
            ),
            'moduleId3' => array(
                'extensions' => array(
                    'ClassName3'  => 'moduleId3/classPath1'
                ),
                'controllers'     => array(
                    'moduleId3_controller1' => 'moduleId3/controller1'
                )
            )
        );

        $aModuleIds = array('moduleId', 'moduleId2', 'moduleId3');

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getDeletedExtensions', '_removeExtensions', '_removeFromDisabledModulesArray', '_removeFromLegacyModulesArray', 'removeFromModulesArray', '_removeFromDatabase'));
        $oModuleList->expects($this->once())->method('getDeletedExtensions')->will($this->returnValue($aModuleInformation));
        $oModuleList->expects($this->once())->method('_removeExtensions')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromDisabledModulesArray')->with($aModuleIds);
        $oModuleList->expects($this->exactly(7))->method('removeFromModulesArray')->withConsecutive(
            ['Paths', $aModuleIds],
            ['Events', $aModuleIds],
            ['Versions', $aModuleIds],
            ['Extensions', $aModuleIds],
            ['Files', $aModuleIds],
            ['Templates', $aModuleIds],
            ['Controllers', $aModuleIds]
        );
        $oModuleList->expects($this->once())->method('_removeFromDatabase')->with($aModuleIds);

        $oModuleList->cleanup();
    }

    /**
     * oxmodulelist::cleanup() test case
     */
    public function testCleanupWithNoExtensions()
    {
        $aModuleInformation = array(
            'moduleId' => array(
                'files' => array(
                    'metadata.php'
                )
            )
        );

        $aModuleIds = array('moduleId');

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getDeletedExtensions', '_removeExtensions', '_removeFromDisabledModulesArray', '_removeFromLegacyModulesArray', 'removeFromModulesArray', '_removeFromDatabase'));
        $oModuleList->expects($this->once())->method('getDeletedExtensions')->will($this->returnValue($aModuleInformation));
        $oModuleList->expects($this->once())->method('_removeExtensions')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromDisabledModulesArray')->with($aModuleIds);
        $oModuleList->expects($this->exactly(7))->method('removeFromModulesArray')->withConsecutive(
            ['Paths', $aModuleIds],
            ['Events', $aModuleIds],
            ['Versions', $aModuleIds],
            ['Extensions', $aModuleIds],
            ['Files', $aModuleIds],
            ['Templates', $aModuleIds],
            ['Controllers', $aModuleIds]
        );
        $oModuleList->expects($this->once())->method('_removeFromDatabase')->with($aModuleIds);

        $oModuleList->cleanup();
    }

    /**
     * oxmodulelist::_extendsClasses() test case
     *
     * @return null
     */
    public function testExtendsClasses()
    {
        $aModules = array(
            'oxarticle' => 'mod/testModule&mod2/testModule2/&mod3/dir3/testModule3',
            'oxorder'   => 'mod7/testModuleOrder&myext/myextclass',
        );

        $this->getConfig()->setConfigParam("aModules", $aModules);

        $oModuleList = $this->getProxyClass('oxModuleList');
        $oModuleList->setNonPublicVar("_aModule", $aModules);

        $this->assertTrue($oModuleList->_extendsClasses("mod3/dir3"));
        $this->assertTrue($oModuleList->_extendsClasses("mod"));
        $this->assertTrue($oModuleList->_extendsClasses("myext"));
        $this->assertFalse($oModuleList->_extendsClasses("mo"));
        $this->assertFalse($oModuleList->_extendsClasses("mod4"));
        $this->assertFalse($oModuleList->_extendsClasses("mod3/dir"));
        $this->assertFalse($oModuleList->_extendsClasses("od3/dir"));
        $this->assertFalse($oModuleList->_extendsClasses("dir3/testModule3"));
    }

    /**
     * oxmodulelist::_saveModulePath() test case
     *
     * @return null
     */
    public function testSaveModulePath()
    {
        $aModulePaths = array("testId1" => "testpPath1", "testId2" => "testPath2");
        $aModulePathsRes = array_merge($aModulePaths, array("testId3" => "testPath3"));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('saveShopConfVar'));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aModulePaths"), $this->equalTo($aModulePathsRes));

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getModuleConfigParametersByKey', 'getConfig'));
        $oModuleList->expects($this->once())->method('getModuleConfigParametersByKey')->with('Paths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $oModuleList->_saveModulePath("testId3", "testPath3");
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

    public function testGetDeletedExtensionsForModuleWithNoMetadata()
    {
        $aModules = array(
            'oxAddress' => 'moduleWhichHasNoMetadata/anyExtension',
        );
        $aDeletedExt = array(
            'moduleWhichHasNoMetadata' => array(
                'files' => array('moduleWhichHasNoMetadata/metadata.php')
            ),
        );
        $this->setConfigParam("aModules", $aModules);
        $oModuleList = oxNew('oxModuleList');
        $aDeletedExtRes = $oModuleList->getDeletedExtensions();

        $this->assertEquals($aDeletedExt, $aDeletedExtRes);
    }

    public function testGetDeletedExtensionsWithMissingExtensions()
    {
        $aModules = array(
            'oxArticle' => 'mod1/testExtension1&mod2/testExtension2/',
            'oxOrder'   => 'mod2/testExtension2/models/test'
        );
        $aDeletedExt = array(
            'mod1' => array(
                'extensions' => array(
                    'oxArticle' => array(
                        'mod1/testExtension1',
                    ),
                ),
            ),
            'mod2' => array(
                'extensions' => array(
                    'oxArticle' => array(
                        'mod2/testExtension2/',
                    ),
                    'oxOrder'   => array(
                        'mod2/testExtension2/models/test'
                    )
                ),
            ),
        );
        $this->setConfigParam("aModules", $aModules);

        $oModuleMetadataValidator = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleMetadataValidator::class, array('validate'));
        $oModuleMetadataValidator->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oModuleValidatorFactory = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleValidatorFactory::class, array('getModuleMetadataValidator'));
        $oModuleValidatorFactory->expects($this->any())->method('getModuleMetadataValidator')->will($this->returnValue($oModuleMetadataValidator));

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getModuleValidatorFactory'));
        $oModuleList->expects($this->any())->method('getModuleValidatorFactory')->will($this->returnValue($oModuleValidatorFactory));

        $aDeletedExtensions = $oModuleList->getDeletedExtensions();
        $this->assertEquals($aDeletedExt, $aDeletedExtensions);
    }

    /**
     * Non loadable classes should be listed in the "PROBLEMATIC FILES" section in
     * OXID eShop Admin -> Extensions -> Modules -> "Installed Shop Modules" Tab and therefore they must form part of
     * the so called "deleted extensions"
     *
     * The return value of the method covered by this test is independent from the session variable
     * "blSkipDeletedExtChecking", the rendering of the error message is not.
     *
     * @see \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\ModuleTest::testGetDeletedExtensionsForNamespaceModuleShowErrorForNonLoadableClasses
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleList::getDeletedExtensions
     */
    public function testGetDeletedExtensionsForNonLoadableClasses()
    {
        $expectedDeletedExtensions = [
            'SomeVendor\SomeModule\Application\Model\Article' => [
                'extensions' => [
                    'OxidEsales\Eshop\Application\Model\Article' => [
                        'SomeVendor\SomeModule\Application\Model\Article'
                    ]
                ]
            ]
        ];

        /** @var array $aModules Contains a class, which is not loadable - in this case - as it does not exist */
        $aModules = [\OxidEsales\Eshop\Application\Model\Article::class => \SomeVendor\SomeModule\Application\Model\Article::class];
        $this->setConfigParam("aModules", $aModules);

        $moduleMetadataValidatorMock = $this->getMock(
            \OxidEsales\EshopCommunity\Core\Module\ModuleMetadataValidator::class,
            ['validate']
        );
        $moduleMetadataValidatorMock->expects($this->any())->method('validate')->will($this->returnValue(true));

        $moduleValidatorFactoryMock = $this->getMock(
            \OxidEsales\EshopCommunity\Core\Module\ModuleValidatorFactory::class,
            ['getModuleMetadataValidator']
        );
        $moduleValidatorFactoryMock->expects($this->any())->method('getModuleMetadataValidator')->will($this->returnValue($moduleMetadataValidatorMock));

        /** @var \OxidEsales\EshopCommunity\Core\Module\ModuleList|\PHPUnit_Framework_MockObject_MockObject $moduleListMock  */
        $moduleListMock = $this->getMock(
            \OxidEsales\EshopCommunity\Core\Module\ModuleList::class,
            ['getModuleValidatorFactory']
        );
        $moduleListMock->expects($this->any())->method('getModuleValidatorFactory')->will($this->returnValue($moduleValidatorFactoryMock));

        $actualDeletedExtensions = $moduleListMock->getDeletedExtensions();

        $this->assertEquals($expectedDeletedExtensions, $actualDeletedExtensions);
    }

    public function testGetModuleIds()
    {
        $oModuleList = oxNew('oxModuleList');
        $aModuleExtensions = array(
            'oxarticle' => 'mod/testModule&mod2/testModule2/',
            'oxorder'   => 'oe/ModuleName/models/ModuleNameoxorder'
        );
        $aModuleFiles = array(
            'module' => array(
                'moduleClass'  => 'module/moduleclass.php',
                'moduleClass2' => 'module/moduleclass2.php'
            ),
        );
        $aModuleIds = array('mod', 'mod2', 'ModuleName', 'module');
        $this->setConfigParam('aModules', $aModuleExtensions);
        $this->setConfigParam('aModuleFiles', $aModuleFiles);
        $this->setConfigParam('aModulePaths', array('ModuleName' => 'oe/ModuleName'));

        $this->assertSame($aModuleIds, $oModuleList->getModuleIds());
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

    public function testGetModuleExtensionsWithMultipleExtensions()
    {
        $oModuleList = oxNew('oxModuleList');
        $aModuleExtensions = array(
            'oxArticle' => 'mod/articleExtension1&mod/articleExtension2&mod2/articleExtension3',
            'oxOrder'   => 'mod2/oxOrder',
            'oxBasket'  => 'mod/basketExtension',
        );

        $this->getConfig()->setConfigParam('aModules', $aModuleExtensions);
        $aExtensions = array(
            'oxArticle' => array(
                'mod/articleExtension1',
                'mod/articleExtension2'
            ),
            'oxBasket'  => array(
                'mod/basketExtension'
            )
        );

        $this->assertSame($aExtensions, $oModuleList->getModuleExtensions('mod'));
    }

    public function testGetModuleExtensionsWithNoExtensions()
    {
        $oModuleList = oxNew('oxModuleList');

        $this->getConfig()->setConfigParam('aModules', array());

        $this->assertSame(array(), $oModuleList->getModuleExtensions('mod'));
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
