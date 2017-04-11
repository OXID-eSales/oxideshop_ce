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

class Unit_Core_oxmodulelistTest extends OxidTestCase
{

    /**
     * test setup
     *
     * @return null
     */
    public function setup()
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
        modConfig::getInstance()->setConfigParam("aModules", $aModules);

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
        modConfig::getInstance()->setConfigParam("aModules", $aModules);

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

        $oModuleList = $this->getMock('oxmodulelist', array('extractModulePaths', 'getModulePaths', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('extractModulePaths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getModulePaths')->will($this->returnValue(false));
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
        $oModuleList = $this->getMock('oxmodulelist', array('getDisabledModules'));;
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

        $oModuleList = $this->getMock('oxmodulelist', array('extractModulePaths', 'getModulePaths', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('extractModulePaths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getModulePaths')->will($this->returnValue(false));
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

        $oModuleList = $this->getMock('oxmodulelist', array('getModulePaths', 'getDisabledModules'));
        $oModuleList->expects($this->once())->method('getModulePaths')->will($this->returnValue($aModulePaths));
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

        modConfig::getInstance()->setConfigParam("aDisabledModules", $aDisabledModules);

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

        modConfig::getInstance()->setConfigParam("aModulePaths", $aModulePaths);

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
        modConfig::getInstance()->setConfigParam("aModules", $aModules);

        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );
        modConfig::getInstance()->setConfigParam("aDisabledModules", $aDisabledModules);

        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );
        modConfig::getInstance()->setConfigParam("aModulePaths", $aModulePaths);

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
        modConfig::getInstance()->setConfigParam("aModules", $aModules);

        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );
        modConfig::getInstance()->setConfigParam("aDisabledModules", $aDisabledModules);

        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
        );
        modConfig::getInstance()->setConfigParam("aModulePaths", $aModulePaths);

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
        $oModuleList = $this->getMock('oxModuleList', array('getDeletedExtensions'));
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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aDisabledModules'), $this->equalTo($aResult));

        $oModuleList = $this->getMock('oxmodulelist', array('getConfig', 'getDisabledModules'));
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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModulePaths'), $this->equalTo($aResult));

        $oModuleList = $this->getMock('oxmodulelist', array('getConfig', 'getModulePaths'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModulePaths')->will($this->returnValue($aModulePaths));


        $oModuleList->_removeFromModulesPathsArray($aDeletedExtIds);
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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleVersions'), $this->equalTo($aResult));

        $oModuleList = $this->getMock('oxmodulelist', array('getConfig', 'getModuleVersions'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleVersions')->will($this->returnValue($aModuleVersions));


        $oModuleList->_removeFromModulesVersionsArray($aDeletedExtIds);
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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleEvents'), $this->equalTo($aResult));

        $oModuleList = $this->getMock('oxmodulelist', array('getConfig', 'getModuleEvents'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleEvents')->will($this->returnValue($aModuleEvents));


        $oModuleList->_removeFromModulesEventsArray($aDeletedExtIds);
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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleFiles'), $this->equalTo($aResult));

        $oModuleList = $this->getMock('oxmodulelist', array('getConfig', 'getModuleFiles'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleFiles')->will($this->returnValue($aModuleFiles));


        $oModuleList->_removeFromModulesFilesArray($aDeletedExtIds);
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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aModuleTemplates'), $this->equalTo($aResult));

        $oModuleList = $this->getMock('oxmodulelist', array('getConfig', 'getModuleTemplates'));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oModuleList->expects($this->once())->method('getModuleTemplates')->will($this->returnValue($aModuleTemplates));


        $oModuleList->_removeFromModulesTemplatesArray($aDeletedExtIds);
    }

    /**
     * oxmodulelist::_removeFromDatabase() test case
     *
     * @return null
     */
    public function testRemoveFromDatabase()
    {
        $oDb = oxDb::getDb();
        $oConfig = new oxConfig();
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
            )
        );

        $aModuleIds = array('moduleId', 'moduleId2');

        $oModuleList = $this->getMock('oxmodulelist', array('getDeletedExtensions', '_removeExtensions', '_removeFromDisabledModulesArray', '_removeFromLegacyModulesArray', '_removeFromModulesPathsArray', '_removeFromModulesTemplatesArray', '_removeFromModulesVersionsArray', '_removeFromModulesEventsArray', '_removeFromModulesFilesArray', '_removeFromDatabase'));
        $oModuleList->expects($this->once())->method('getDeletedExtensions')->will($this->returnValue($aModuleInformation));
        $oModuleList->expects($this->once())->method('_removeExtensions')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromDisabledModulesArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesPathsArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesVersionsArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesEventsArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesFilesArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesTemplatesArray')->with($aModuleIds);
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

        $oModuleList = $this->getMock('oxmodulelist', array('getDeletedExtensions', '_removeExtensions', '_removeFromDisabledModulesArray', '_removeFromLegacyModulesArray', '_removeFromModulesPathsArray', '_removeFromModulesTemplatesArray', '_removeFromModulesVersionsArray', '_removeFromModulesEventsArray', '_removeFromModulesFilesArray', '_removeFromDatabase'));
        $oModuleList->expects($this->once())->method('getDeletedExtensions')->will($this->returnValue($aModuleInformation));
        $oModuleList->expects($this->once())->method('_removeExtensions')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromDisabledModulesArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesPathsArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesVersionsArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesEventsArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesFilesArray')->with($aModuleIds);
        $oModuleList->expects($this->once())->method('_removeFromModulesTemplatesArray')->with($aModuleIds);
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

        modConfig::getInstance()->setConfigParam("aModules", $aModules);

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

        $oConfig = $this->getMock('oxConfig', array('saveShopConfVar'));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aModulePaths"), $this->equalTo($aModulePathsRes));

        $oModuleList = $this->getMock('oxModuleList', array('getModulePaths', 'getConfig'));
        $oModuleList->expects($this->once())->method('getModulePaths')->will($this->returnValue($aModulePaths));
        $oModuleList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $oModuleList->_saveModulePath("testId3", "testPath3");
    }

    /**
     * oxmodulelist::getModulesFromDir() test case
     *
     * @return null
     */
    public function testGetModulesFromDir()
    {
        $sModulesDir = oxRegistry::getConfig()->getModulesDir();

        $oModuleList = new oxModuleList;
        $aModules = $oModuleList->getModulesFromDir($sModulesDir);

        $oModule = $aModules["invoicepdf"];
        $this->assertEquals("invoicepdf", $oModule->getId());
    }

    /**
     * oxmodulelist::_isVendorDir() test case
     *
     * @return null
     */
    public function testIsVendorDir()
    {
        $sModulesDir = oxRegistry::getConfig()->getModulesDir();

        $oModuleList = new oxModuleList;

        $this->assertFalse($oModuleList->_isVendorDir($sModulesDir . "/invoicepdf"));
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
        $oModuleList = new oxModuleList();
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

        $oModuleMetadataValidator = $this->getMock('oxModuleMetadataValidator', array('validate'));
        $oModuleMetadataValidator->expects($this->any())->method('validate')->will($this->returnValue(true));

        $oModuleValidatorFactory = $this->getMock('oxModuleValidatorFactory', array('getModuleMetadataValidator'));
        $oModuleValidatorFactory->expects($this->any())->method('getModuleMetadataValidator')->will($this->returnValue($oModuleMetadataValidator));

        $oModuleList = $this->getMock('oxModuleList', array('getModuleValidatorFactory'));
        $oModuleList->expects($this->any())->method('getModuleValidatorFactory')->will($this->returnValue($oModuleValidatorFactory));

        $aDeletedExtensions = $oModuleList->getDeletedExtensions();
        $this->assertEquals($aDeletedExt, $aDeletedExtensions);
    }

    public function testGetModuleIds()
    {
        $oModuleList = new oxModuleList();
        $aModuleExtensions = array(
            'oxarticle' => 'mod/testModule&mod2/testModule2/',
            'oxorder'   => 'oe/invoicepdf/models/invoicepdfoxorder'
        );
        $aModuleFiles = array(
            'module' => array(
                'moduleClass'  => 'module/moduleclass.php',
                'moduleClass2' => 'module/moduleclass2.php'
            ),
        );
        $aModuleIds = array('mod', 'mod2', 'invoicepdf', 'module');
        $this->setConfigParam('aModules', $aModuleExtensions);
        $this->setConfigParam('aModuleFiles', $aModuleFiles);
        $this->setConfigParam('aModulePaths', array('invoicepdf' => 'oe/invoicepdf'));

        $this->assertSame($aModuleIds, $oModuleList->getModuleIds());
    }

    public function testGetModuleFilesWhenFileWasSet()
    {
        $aModuleFiles = array(
            'myext1' => array("title" => "test title 1")
        );
        $this->getConfig()->setConfigParam('aModuleFiles', $aModuleFiles);
        $oModuleList = new oxModuleList();

        $this->assertSame($aModuleFiles, $oModuleList->getModuleFiles());
    }

    public function testGetModuleFilesWhenFileWasNotSet()
    {
        $this->getConfig()->setConfigParam('aModuleFiles', array());

        $oModuleList = new oxModuleList();

        $this->assertSame(array(), $oModuleList->getModuleFiles());
    }

    public function testGetModuleExtensionsWithMultipleExtensions()
    {
        $oModuleList = new oxModuleList();
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
        $oModuleList = new oxModuleList();

        $this->getConfig()->setConfigParam('aModules', array());

        $this->assertSame(array(), $oModuleList->getModuleExtensions('mod'));
    }
}
