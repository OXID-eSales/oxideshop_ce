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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

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
        $aModulesArray  = array();
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
        $aModulesArray  = array('oxtest' => array('test/mytest'));
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
        $aModulesArray  = array('oxtest' => array('test/mytest','test1/mytest1'));
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
        $aAllModules = array('oxtest' => array('test/mytest','test1/mytest1'));
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
        $aAllModules = array('oxtest' => array('test/mytest','test1/mytest1'), 'oxtest2' => array('test2/mytest2'));
        $aRemModules = array('oxtest' => array('test/mytest'), 'oxtest2' => array('test2/mytest2'));
        $aMrgModules = array('oxtest' => array('test1/mytest1'));
        $this->assertEquals($aMrgModules, $oModuleList->diffModuleArrays($aAllModules, $aRemModules));
    }

    /**
     * oxModuleList::getAllModules() test case
     *
     * @return null
     */
    public function testGetAllModules()
    {
        $aModules = array(
            'oxorder'  => 'testExt1/module1&testExt2/module1',
            'oxnews'   => 'testExt2/module2'
        );

        $aResult = array(
            'oxorder'  => array( 'testExt1/module1', 'testExt2/module1' ),
            'oxnews'   => array( 'testExt2/module2' )
        );

        $oModuleList = $this->getProxyClass('oxmodulelist');
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $this->assertEquals( $aResult, $oModuleList->getAllModules() );
    }

    /**
     * oxModuleList::extractModulePaths() test case
     *
     * @return null
     */
    public function testExtractModulePaths()
    {
        $aModules = array(
            'oxorder'  => 'testExt1/module1&testExt2/module1',
            'oxnews'   => 'testExt2/module2'
        );

        $aResult = array(
            'testExt1' => 'testExt1',
            'testExt2' => 'testExt2'
        );

        $oModuleList = $this->getProxyClass('oxmodulelist');
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $this->assertEquals( $aResult, $oModuleList->extractModulePaths() );
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

        $oModuleList = $this->getMock( 'oxmodulelist', array('extractModulePaths', 'getModulePaths', 'getDisabledModules') );
        $oModuleList->expects($this->once())->method('extractModulePaths')->will( $this->returnValue($aModulePaths) );
        $oModuleList->expects($this->once())->method('getModulePaths')->will( $this->returnValue(false) );
        $oModuleList->expects($this->once())->method('getDisabledModules')->will( $this->returnValue($aDisabledModules) );

        $this->assertEquals( $aResult, $oModuleList->getActiveModuleInfo() );
    }

    /**
     * oxmodulelist::getDisabledModuleInfo() test case
     *
     * @return null
     */
    public function testGetDeisabledModuleInfoNoDisabled()
    {
        $oModuleList = $this->getMock( 'oxmodulelist', array('getDisabledModules') );;
        $oModuleList->expects($this->once())->method('getDisabledModules')->will( $this->returnValue(array()) );

        $this->assertEquals( array(), $oModuleList->getDisabledModuleInfo() );
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

        $oModuleList = $this->getMock( 'oxmodulelist', array('extractModulePaths', 'getModulePaths', 'getDisabledModules') );
        $oModuleList->expects($this->once())->method('extractModulePaths')->will( $this->returnValue($aModulePaths) );
        $oModuleList->expects($this->once())->method('getModulePaths')->will( $this->returnValue(false) );
        $oModuleList->expects($this->once())->method('getDisabledModules')->will( $this->returnValue($aDisabledModules) );

        $this->assertEquals( $aResult, $oModuleList->getDisabledModuleInfo() );
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

        $oModuleList = $this->getMock( 'oxmodulelist', array( 'getModulePaths', 'getDisabledModules') );
        $oModuleList->expects($this->once())->method('getModulePaths')->will( $this->returnValue($aModulePaths) );
        $oModuleList->expects($this->once())->method('getDisabledModules')->will( $this->returnValue($aDisabledModules) );

        $this->assertEquals( $aResult, $oModuleList->getDisabledModuleInfo() );
    }

     /**
     * oxmodulelist::getLegacyModules() test case
     *
     * @return null
     */
    public function testGetLegacyModules()
    {
        $aLegacyModules["testModule"] = array(
            'title'        => 'Test Module',
            'extend'       => array ('oxnews' => 'testModule/testModuleClass')
        );

        modConfig::getInstance()->setConfigParam( "aLegacyModules", $aLegacyModules );

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals( $aLegacyModules, $oModuleList->getLegacyModules() );
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

        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals( $aDisabledModules, $oModuleList->getDisabledModules() );
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

        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals( $aModulePaths, $oModuleList->getModulePaths() );
    }

     /**
     * oxmodulelist::getDisabledModuleClasses() test case
     *
     * @return null
     */
    public function testGetDisabledModuleClasses()
    {
        $aModules = array(
            'oxorder'  => 'testExt1/testExt11/module1&testExt2/module1',
            'oxnews'   => 'testExt2/module2'
        );
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );

        $aDisabledModuleClasses = array(
            'testExt1/testExt11/module1',
            'testExt2/module1',
            'testExt2/module2'
        );
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals( $aDisabledModuleClasses, $oModuleList->getDisabledModuleClasses() );
    }

     /**
     * oxmodulelist::getDisabledModuleClasses() test case
     *
     * @return null
     */
    public function testGetDisabledModuleClassesIfNoPath()
    {
        $aModules = array(
            'oxorder'  => 'testExt1/testExt11/module1&testExt2/module1',
            'oxnews'   => 'testExt2/module2'
        );
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
        );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );

        $aDisabledModuleClasses = array(
            'testExt1/testExt11/module1',
            'testExt2/module1',
            'testExt2/module2'
        );
        $oModuleList = $this->getProxyClass('oxmodulelist');

        $this->assertEquals( $aDisabledModuleClasses, $oModuleList->getDisabledModuleClasses() );
    }

     /**
     * oxmodulelist::_removeFromModulesArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesArray()
    {
        $aModules = array(
            'oxorder'  => 'testExt1/module1',
            'oxnews'   => 'testExt2/module2'
        );

        $aDeletedExt = array(
            'oxnews'   => 'testExt2/module2'
        );

        $aResult = array(
            'oxorder'  => 'testExt1/module1'
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aModules' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getAllModules') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getAllModules')->will( $this->returnValue($aModules) );


        $oModuleList->_removeFromModulesArray( $aDeletedExt );
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

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'arr' ), $this->equalTo( 'aDisabledModules' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getDisabledModules') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getDisabledModules')->will( $this->returnValue($aModules) );


        $oModuleList->_removeFromDisabledModulesArray( $aDeletedExt );
    }

    /**
     * oxmodulelist::_removeFromLegacyModulesArray() test case
     *
     * @return null
     */
    public function testRemoveFromLegacyModulesArray()
    {
        $aLegacyExt = array(
            'myext1'  => array( "title" => "test title 1"),
            'myext2'  => array( "title" => "test title 2")
        );

        $aDeletedExtIds = array( "myext1" );

        $aResult = array(
            'myext2'  => array( "title" => "test title 2")
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aLegacyModules' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getLegacyModules') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getLegacyModules')->will( $this->returnValue($aLegacyExt) );


        $oModuleList->_removeFromLegacyModulesArray( $aDeletedExtIds );
    }

    /**
     * oxmodulelist::_removeFromModulesPathsArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesPathsArray()
    {
        $aModulePaths = array(
            'myext1'  => array( "title" => "test title 1"),
            'myext2'  => array( "title" => "test title 2")
        );

        $aDeletedExtIds = array( "myext1" );

        $aResult = array(
            'myext2'  => array( "title" => "test title 2")
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aModulePaths' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getModulePaths') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getModulePaths')->will( $this->returnValue($aModulePaths) );


        $oModuleList->_removeFromModulesPathsArray( $aDeletedExtIds );
    }

    /**
     * oxmodulelist::_removeFromModulesVersionsArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesVersionsArray()
    {
        $aModuleVersions = array(
            'myext1'  => '1.0',
            'myext2'  => '2.4'
        );

        $aDeletedExtIds = array( "myext1" );

        $aResult = array(
            'myext2'  => '2.4'
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aModuleVersions' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getModuleVersions') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getModuleVersions')->will( $this->returnValue($aModuleVersions) );


        $oModuleList->_removeFromModulesVersionsArray( $aDeletedExtIds );
    }

    /**
     * oxmodulelist::_removeFromModulesEventsArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesEventsArray()
    {
        $aModuleEvents = array(
            'myext1'  => array('onActivate' => 'date'),
            'myext2'  => array('onActivate' => 'date'),
        );

        $aDeletedExtIds = array( "myext1" );

        $aResult = array(
            'myext2'  => array('onActivate' => 'date'),
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aModuleEvents' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getModuleEvents') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getModuleEvents')->will( $this->returnValue($aModuleEvents) );


        $oModuleList->_removeFromModulesEventsArray( $aDeletedExtIds );
    }

    /**
     * oxmodulelist::_removeFromModulesFilesArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesFilesArray()
    {
        $aModuleFiles = array(
            'myext1'  => array( "title" => "test title 1"),
            'myext2'  => array( "title" => "test title 2")
        );

        $aDeletedExtIds = array( "myext1" );

        $aResult = array(
            'myext2'  => array( "title" => "test title 2")
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aModuleFiles' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getModuleFiles') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getModuleFiles')->will( $this->returnValue($aModuleFiles) );


        $oModuleList->_removeFromModulesFilesArray( $aDeletedExtIds );
    }

    /**
     * oxmodulelist::_removeFromModulesTemplatesArray() test case
     *
     * @return null
     */
    public function testRemoveFromModulesTemplatesArray()
    {
        $aModuleTemplates = array(
            'myext1'  => array( "title" => "test title 1"),
            'myext2'  => array( "title" => "test title 2")
        );

        $aDeletedExtIds = array( "myext1" );

        $aResult = array(
            'myext2'  => array( "title" => "test title 2")
        );

        $oConfig = $this->getMock( "oxConfig", array( "saveShopConfVar" ) );
        $oConfig->expects($this->once())->method('saveShopConfVar')->with( $this->equalTo( 'aarr' ), $this->equalTo( 'aModuleTemplates' ), $this->equalTo( $aResult ) );

        $oModuleList = $this->getMock( 'oxmodulelist', array('getConfig', 'getModuleTemplates') );
        $oModuleList->expects($this->once())->method('getConfig')->will( $this->returnValue($oConfig) );
        $oModuleList->expects($this->once())->method('getModuleTemplates')->will( $this->returnValue($aModuleTemplates) );


        $oModuleList->_removeFromModulesTemplatesArray( $aDeletedExtIds );
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

        $oDb->execute( $sQ1 );
        $oDb->execute( $sQ2 );
        $oDb->execute( $sQ3 );

        $aDeletedExtIds = array( "myext1" );

        $oModuleList = $this->getProxyClass('oxmodulelist');

        $oModuleList->_removeFromDatabase( $aDeletedExtIds );
    }

     /**
     * oxmodulelist::cleanup() test case
     *
     * @return null
     */
    public function testCleanup()
    {
        $oModuleList = $this->getMock( 'oxmodulelist', array('_removeFromModulesArray', '_removeFromDisabledModulesArray', '_removeFromLegacyModulesArray', '_removeFromModulesPathsArray', '_removeFromModulesTemplatesArray', '_removeFromModulesVersionsArray', '_removeFromModulesEventsArray', '_removeFromModulesFilesArray', '_removeFromDatabase') );
        $oModuleList->expects($this->once())->method('_removeFromModulesArray');
        $oModuleList->expects($this->once())->method('_removeFromDisabledModulesArray');
        $oModuleList->expects($this->once())->method('_removeFromLegacyModulesArray');
        $oModuleList->expects($this->once())->method('_removeFromModulesPathsArray');
        $oModuleList->expects($this->once())->method('_removeFromModulesVersionsArray');
        $oModuleList->expects($this->once())->method('_removeFromModulesEventsArray');
        $oModuleList->expects($this->once())->method('_removeFromModulesFilesArray');
        $oModuleList->expects($this->once())->method('_removeFromModulesTemplatesArray');
        $oModuleList->expects($this->once())->method('_removeFromDatabase');

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

        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $oModuleList = $this->getProxyClass( 'oxModuleList' );
        $oModuleList->setNonPublicVar( "_aModule", $aModules );

        $this->assertTrue( $oModuleList->_extendsClasses("mod3/dir3") );
        $this->assertTrue( $oModuleList->_extendsClasses("mod") );
        $this->assertTrue( $oModuleList->_extendsClasses("myext") );
        $this->assertFalse( $oModuleList->_extendsClasses("mo") );
        $this->assertFalse( $oModuleList->_extendsClasses("mod4") );
        $this->assertFalse( $oModuleList->_extendsClasses("mod3/dir") );
        $this->assertFalse( $oModuleList->_extendsClasses("od3/dir") );
        $this->assertFalse( $oModuleList->_extendsClasses("dir3/testModule3") );
    }

    /**
     * oxmodulelist::_saveModulePath() test case
     *
     * @return null
     */
    public function testSaveModulePath()
    {
        $aModulePaths    = array( "testId1"=>"testpPath1", "testId2"=>"testPath2" );
        $aModulePathsRes = array_merge( $aModulePaths, array("testId3"=>"testPath3") );

        $oConfig = $this->getMock( 'oxConfig', array('saveShopConfVar') );
        $oConfig->expects( $this->once() )->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aModulePaths"), $this->equalTo($aModulePathsRes) );

        $oModuleList = $this->getMock( 'oxModuleList', array('getModulePaths', 'getConfig') );
        $oModuleList->expects( $this->once() )->method( 'getModulePaths' )->will( $this->returnValue($aModulePaths) );
        $oModuleList->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue($oConfig) );

        $oModuleList->_saveModulePath( "testId3", "testPath3" );
    }

    /**
     * oxmodulelist::getModulesFromDir() test case
     *
     * @return null
     */
    public function testGetModulesFromDir()
    {
            $sModulesDir = oxConfig::getInstance()->getModulesDir();

            $oModuleList = new oxModuleList;
            $aModules = $oModuleList->getModulesFromDir( $sModulesDir );

            $oModule = $aModules["invoicepdf"];
            $this->assertEquals( "invoicepdf", $oModule->getId() );
    }

    /**
     * oxmodulelist::_isVendorDir() test case
     *
     * @return null
     */
    public function testIsVendorDir()
    {
            $sModulesDir = oxConfig::getInstance()->getModulesDir();

            $oModuleList = new oxModuleList;

            $this->assertFalse( $oModuleList->_isVendorDir( $sModulesDir."/invoicepdf" ) );
    }

    /**
     * oxmodulelist::getDeletedExtensions() test case
     *
     * @return null
     */
    public function testGetDeletedExtensions()
    {
        $aModules = array(
            'oxarticle' => 'mod/testModule&mod2/testModule2/',
        );
            $aModules = array(
                'oxarticle' => 'mod/testModule&mod2/testModule2/',
                'oxorder' => 'oe/invoicepdf/myorder'
            );
        $aDeletedExt = array(
            'oxarticle' => array ('mod/testModule',
                                  'mod2/testModule2/',)
        );

        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $oModuleList = $this->getProxyClass( 'oxModuleList' );
        $aDeletedExtRes = $oModuleList->getDeletedExtensions();
        $this->assertEquals( $aDeletedExt, $aDeletedExtRes );
    }

    /**
     * oxmodulelist::getDeletedExtensionIds() test case
     *
     * @return null
     */
    public function testGetDeletedExtensionIds()
    {
        $aModulePaths = array(
            'mod3' => 'mod',
        );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );

        $aDeletedExt = array(
            'oxarticle' => array ('mod/testModule',
                                  'mod2/testModule2/',
                                  'testModule3')
        );
        $aDeletedIds = array ('mod3',
                              'mod2',
                              'testModule3');

        $oModuleList = $this->getProxyClass( 'oxModuleList' );
        $aDeletedExtIds = $oModuleList->getDeletedExtensionIds($aDeletedExt);
        $this->assertEquals( $aDeletedIds, $aDeletedExtIds );
    }

}
