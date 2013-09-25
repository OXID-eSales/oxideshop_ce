<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: oxcaptchaTest.php 26841 2010-03-25 13:58:15Z arvydas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxmoduleTest extends OxidTestCase
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
     * oxmodule::load() test case
     *
     * @return null
     */
    public function testLoad()
    {
            $aModule = array(
                'id'           => 'invoicepdf',
                'title'        => 'Invoice PDF',
                'description'  => 'Module for making invoice PDF files.',
                'thumbnail'    => 'picture.png',
                'version'      => '1.0',
                'author'       => 'OXID eSales AG',
                'active'       => true,
                'extend'       => array ('oxorder' => 'invoicepdf/myorder')
            );

            $oModule = $this->getProxyClass( 'oxmodule' );
            $this->assertTrue( $oModule->load( 'invoicepdf' ) );

            $this->assertEquals( $oModule->getNonPublicVar( "_aModule" ), $aModule );
    }

    /**
     * oxmodule::load() test case, no extend
     *
     * @return null
     */
    public function testLoadNoExtend()
    {
            $aModule = array(
                'id'           => 'invoicepdf',
                'title'        => 'Invoice PDF',
                'description'  => 'Module for making invoice PDF files.',
                'thumbnail'    => 'picture.png',
                'version'      => '1.0',
                'author'       => 'OXID eSales AG',
                'active'       => true,
                'extend'       => array ()
            );

            $oModule = $this->getProxyClass( 'oxmodule' );
            $oModule->setNonPublicVar( "_aModule", $aModule );
            $this->assertFalse( $oModule->isActive() );
            $this->assertFalse( $oModule->isExtended() );
    }

    /**
     * oxmodule::load() test case
     *
     * @return null
     */
    public function testLoadWhenModuleDoesNotExists()
    {
        $oModule = new oxModule;
        $this->assertFalse( $oModule->load( 'non_existing_module' ) );
    }

    /**
     * oxmodule::load() test legacy modules loading from "aLegacyModules" config option
     *
     * @return null
     */
    public function testLoad_getInfoFromLegacyArray()
    {
            $aModule = array(
                'id'           => 'functions.php',
                'title'        => 'Test Module',
                'extend'       => array ('oxnews' => 'testModule/testModuleClass'),
                'active'       => false
            );

            $aLegacyModules["functions.php"] = array(
                'id'           => 'functions.php',
                'title'        => 'Test Module',
                'extend'       => array ('oxnews' => 'testModule/testModuleClass')
            );

            modConfig::getInstance()->setConfigParam("aLegacyModules", $aLegacyModules);

            $oModule = $this->getProxyClass( 'oxmodule' );
            $this->assertTrue( $oModule->load( 'functions.php' ) );
            $this->assertTrue( $oModule->isLegacy() );
            $this->assertTrue( $oModule->isRegistered() );

            $this->assertEquals( $oModule->getNonPublicVar( "_aModule" ), $aModule );
    }

    /**
     * oxmodule::load() test legacy modules load - module info is not in config
     *
     * @return null
     */
    public function testLoad_noInfoInLegacyArray()
    {
            $aModule = array(
                'id'           => 'functions.php',
                'title'        => 'functions.php',
                'extend'       => array(),
                'active'       => false
            );

            $oModule = $this->getProxyClass( 'oxmodule' );
            $this->assertTrue( $oModule->load( 'functions.php' ) );
            $this->assertTrue( $oModule->isLegacy() );
            $this->assertFalse( $oModule->isRegistered() );

            $this->assertEquals( $oModule->getNonPublicVar( "_aModule" ), $aModule );
    }

    /**
     * oxmodule::load() testing loading module from standalone file (not a directory)
     *
     * @return null
     */
    public function testLoad_standaloneFile()
    {
            $aModule = array(
                'id'           => 'functions.php',
                'title'        => 'functions.php',
                'extend'       => array(),
                'active'       => false
            );

            $oModule = $this->getProxyClass( 'oxmodule' );
            $this->assertTrue( $oModule->load( 'functions.php' ) );
            $this->assertTrue( $oModule->isFile() );
            $this->assertTrue( $oModule->isLegacy() );
            $this->assertFalse( $oModule->isRegistered() );

            $this->assertEquals( $oModule->getNonPublicVar( "_aModule" ), $aModule );
    }

    /**
     * oxmodule::loadByDir()
     *
     * @return null
     */
    public function testLoadByDir()
    {
        $aModulesPaths = array( "testModuleId" => "test/path" );
        $oModule = $this->getMock( "oxModule", array( "load", "getModulePaths" ));
        $oModule->expects( $this->at(0) )->method( 'getModulePaths' )->will( $this->returnValue( $aModulesPaths ) );
        $oModule->expects( $this->at(1) )->method( 'load' )->with( $this->equalTo( "noSuchTest/path" ) )->will( $this->returnValue( false ) );
        $oModule->expects( $this->at(2) )->method( 'getModulePaths' )->will( $this->returnValue( $aModulesPaths ) );
        $oModule->expects( $this->at(3) )->method( 'load' )->with( $this->equalTo( "testModuleId" ) )->will( $this->returnValue( true ) );

        $this->assertFalse( $oModule->loadByDir( "noSuchTest/path" ) );
        $this->assertTrue( $oModule->loadByDir( "test/path" ) );
    }

    /**
     * oxmodule::getInfo() test case
     *
     * @return null
     */
    public function testGetInfo()
    {
        $aModule = array(
            'id'    => 'testModuleId',
            'title' => 'testModuleTitle'
        );

        $oModule = $this->getProxyClass( 'oxmodule' );
        $oModule->setNonPublicVar( "_aModule", $aModule );

        $this->assertEquals( "testModuleId", $oModule->getInfo( "id" ) );
        $this->assertEquals( "testModuleTitle", $oModule->getInfo( "title" ) );
    }

    /**
     * oxmodule::getInfo() test case - selecting multilanguage value
     *
     * @return null
     */
    public function testGetInfo_usingLanguage()
    {
        $aModule = array(
            'title' => 'testModuleTitle',
            'description' => array( "en" => "test EN value", "de" => "test DE value" )
        );

        $oModule = $this->getProxyClass( 'oxmodule' );
        $oModule->setNonPublicVar( "_aModule", $aModule );

        $this->assertEquals( 'testModuleTitle', $oModule->getInfo( "title" ) );
        $this->assertEquals( 'testModuleTitle', $oModule->getInfo( "title", 1 ) );

        $this->assertEquals( "test DE value", $oModule->getInfo( "description", 0 ) );
        $this->assertEquals( "test EN value", $oModule->getInfo( "description", 1 ) );
        $this->assertEquals( "test EN value", $oModule->getInfo( "description", 2 ) );
    }

    /**
     * oxmodule::isActive() test case, empty
     *
     * @return null
     */
    public function testIsActiveEmpty()
    {
        $aModules = array();
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend = array('extend' => array());
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxmodule::isActive() test case, active
     *
     * @return null
     */
    public function testIsActiveActive()
    {
        $aModules = array('oxtest' => 'test/mytest');
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('id' => 'test', 'extend' => array('oxtest' => 'test/mytest'));
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        $this->assertTrue($oModule->isActive());
    }

    /**
     * oxmodule::isActive() test case, active in chain
     *
     * @return null
     */
    public function testIsActiveActiveChain()
    {
        $aModules = array('oxtest' => 'test/mytest&test2/mytest2');
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        $this->assertTrue($oModule->isActive());
    }
    /**
     * oxmodule::isActive() test case, inactive
     *
     * @return null
     */
    public function testIsActiveInactive()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aModule  = array('extend' => array('oxtest' => 'test/mytest'));
        $oModule->setNonPublicVar( "_aModule", $aModule );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxmodule::isActive() test case, inactive in chain
     *
     * @return null
     */
    public function testIsActiveInactiveChain()
    {
        $aModules = array('oxtest' => 'test1/mytest1&test2/mytest2');
        modConfig::getInstance()->setConfigParam( "aModules", $aModules );

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxmodule::isActive() test case, deactivated
     *
     * @return null
     */
    public function testIsActiveDeactivated()
    {
        $aDisabledModules = array('test');
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $oModule = $this->getProxyClass('oxmodule');
        $aModule  = array('id' => 'test');
        $oModule->setNonPublicVar( "_aModule", $aModule );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxmodule::isActive() test case, not deactivated in chain
     *
     * @return null
     */
    public function testIsActiveDeactivatedChain()
    {
        $aDisabledModules = array('mytest1', 'test', 'test2');
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $oModule = $this->getProxyClass('oxmodule');
        $aModule  = array('id' => 'test');
        $oModule->setNonPublicVar( "_aModule", $aModule );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxmodule::isActive() test case, active
     *
     * @return null
     */
    public function testIsActiveWithNonExistingModuleLoaded()
    {
        $oModule = $this->getMock("oxmodule", array("getDisabledModules"));
        $oModule->expects( $this->any() )->method( 'getDisabledModules' )->will($this->returnValue( array() ));
        $oModule->load('non_existing_module');

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxmodule::isExtended() test case,
     *
     * @return null
     */
    public function testIsExtendedNot()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array());
        $oModule->setNonPublicVar( "_aModule", $aExtend );
        $oModule->setNonPublicVar( "_blMetadata", true );

        $this->assertFalse($oModule->isExtended());
    }

    /**
     * oxmodule::isExtended() test case,
     *
     * @return null
     */
    public function testIsExtendedYes()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array('oxtest1' => 'test1/mytest1',));
        $oModule->setNonPublicVar( "_aModule", $aExtend );
        $oModule->setNonPublicVar( "_blMetadata", true );

        $this->assertTrue($oModule->isExtended());
    }

    /**
     * oxmodule::isExtended() test case,no metadata
     *
     * @return null
     */
    public function testIsExtendedNotNoMeadata()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array());
        $oModule->setNonPublicVar( "_aModule", $aExtend );
        $oModule->setNonPublicVar( "_blMetadata", false );

        $this->assertFalse($oModule->isExtended());
    }

    /**
     * oxmodule::isExtended() test case, no metadata
     *
     * @return null
     */
    public function testIsExtendedYeNoMeadatas()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array('oxtest1' => 'test1/mytest1',));
        $oModule->setNonPublicVar( "_aModule", $aExtend );
        $oModule->setNonPublicVar( "_blMetadata", false );

        $this->assertFalse($oModule->isExtended());
    }

    /**
     * oxmodule::activate() test case, empty array
     *
     * @return null
     */
    public function testActivate()
    {
        $aModulesBefore = array();
        $aModulesAfter  = array('oxtest' => 'testdir/mytest');

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array('oxtest' => 'testdir/mytest'), 'id' => 'test', 'dir' => 'testdir');
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        //odConfig::getInstance()->setConfigParam( "aModules", $aModulesBefore );
        oxRegistry::getConfig()->setConfigParam( "aModules", $aModulesBefore );

        $this->assertEquals($aModulesBefore, modConfig::getInstance()->getConfigParam("aModules") );

        $this->assertTrue($oModule->activate());
        $this->assertEquals($aModulesAfter, modConfig::getInstance()->getConfigParam("aModules") );
    }

    /**
     * oxmodule::activate() test case, already activated
     *
     * @return null
     */
    public function testActivateActive()
    {
        $aModulesBefore = array('oxtest' => 'test/mytest');
        $aModulesAfter  = array('oxtest' => 'test/mytest');
        $aDisabledModulesBefore = array('test');
        $aDisabledModulesAfter  = array();

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        //modConfig::getInstance()->setConfigParam( "aModules", $aModulesBefore );
        //modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModulesBefore );
        oxRegistry::getConfig()->setConfigParam( "aModules", $aModulesBefore);
        oxRegistry::getConfig()->setConfigParam( "aDisabledModules", $aDisabledModulesBefore );

        $this->assertEquals($aModulesBefore, modConfig::getInstance()->getConfigParam("aModules") );
        $this->assertEquals($aDisabledModulesBefore, modConfig::getInstance()->getConfigParam("aDisabledModules") );

        $this->assertTrue($oModule->activate());
        $this->assertEquals($aModulesAfter, modConfig::getInstance()->getConfigParam("aModules") );
        $this->assertEquals($aDisabledModulesAfter, modConfig::getInstance()->getConfigParam("aDisabledModules") );
    }

    /**
     * oxmodule::activate() test case, append to chain
     *
     * @return null
     */
    public function testActivateChain()
    {
        $aModulesBefore = array('oxtest' => 'test/mytest');
        $aModulesAfter  = array('oxtest' => 'test/mytest&test1/mytest1');

        $oModule = $this->getProxyClass('oxmodule');
        $aExtend  = array('extend' => array('oxtest' => 'test1/mytest1'));
        $oModule->setNonPublicVar( "_aModule", $aExtend );

        //modConfig::getInstance()->setConfigParam( "aModules", $aModulesBefore );
        oxRegistry::getConfig()->setConfigParam("aModules", $aModulesBefore);
        $this->assertEquals($aModulesBefore, modConfig::getInstance()->getConfigParam("aModules") );

        $this->assertTrue($oModule->activate());
        $this->assertEquals($aModulesAfter, modConfig::getInstance()->getConfigParam("aModules") );
    }

    /**
     * oxmodule::deactivate() test case, empty array
     *
     * @return null
     */
    public function testDeactivate()
    {
        $oConfig = $this->getMock( 'oxConfig', array('saveShopConfVar', 'setConfigParam') );
        $oConfig->expects( $this->once() )->method('saveShopConfVar')->with($this->equalTo("arr"), $this->equalTo("aDisabledModules"), $this->equalTo(array("testId1", "testId2")) );
        $oConfig->expects( $this->once() )->method('setConfigParam')->with($this->equalTo("aDisabledModules"), $this->equalTo(array("testId1", "testId2")) );

        $oModule = $this->getMock( 'oxModule', array('getId', 'getModuleEvents', 'getDisabledModules', 'getConfig'), array(), "", false );
        $oModule->expects( $this->any() )->method('getId')->will( $this->returnValue( "testId2" ) );
        $oModule->expects( $this->once() )->method('getModuleEvents')->will( $this->returnValue( array() ) );
        $oModule->expects( $this->once() )->method('getDisabledModules')->will( $this->returnValue( array("testId1") ) );
        $oModule->expects( $this->any() )->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertTrue( $oModule->deactivate() );
    }

    /**
     * oxmodule::deactivate() test case, when disabling two identical modules
     *
     * @return null
     */
    public function testDeactivateDuplicate()
    {
        $oConfig = $this->getMock( 'oxConfig', array('saveShopConfVar', 'setConfigParam') );
        $oConfig->expects( $this->once() )->method('saveShopConfVar')->with($this->equalTo("arr"), $this->equalTo("aDisabledModules"), $this->equalTo(array("testId1")) );
        $oConfig->expects( $this->once() )->method('setConfigParam')->with($this->equalTo("aDisabledModules"), $this->equalTo(array("testId1")) );

        $oModule = $this->getMock( 'oxModule', array('getId', 'getModuleEvents','getDisabledModules', 'getConfig'), array(), "", false );
        $oModule->expects( $this->any() )->method('getId')->will( $this->returnValue( "testId1" ) );
        $oModule->expects( $this->once() )->method('getModuleEvents')->will( $this->returnValue( array() ) );
        $oModule->expects( $this->once() )->method('getDisabledModules')->will( $this->returnValue( array("testId1") ) );
        $oModule->expects( $this->any() )->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertTrue( $oModule->deactivate() );
    }

    /**
     * oxmodule::buildModuleChains() test case, empty
     *
     * @return null
     */
    public function testBuildModuleChainsEmpty()
    {
        $oModule = $this->getProxyClass('oxmodule');

        $aModules = array();
        $aModulesArray  = array();
        $this->assertEquals($aModules, $oModule->buildModuleChains($aModulesArray));
    }

    /**
     * oxmodule::buildModuleChains() test case, single
     *
     * @return null
     */
    public function testBuildModuleChainsSingle()
    {
        $oModule = $this->getProxyClass('oxmodule');

        $aModules = array('oxtest' => 'test/mytest');
        $aModulesArray  = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aModules, $oModule->buildModuleChains($aModulesArray));
    }

    /**
     * oxmodule::buildModuleChains() test case
     *
     * @return null
     */
    public function testBuildModuleChains()
    {
        $oModule = $this->getProxyClass('oxmodule');

        $aModules = array('oxtest' => 'test/mytest&test1/mytest1');
        $aModulesArray  = array('oxtest' => array('test/mytest','test1/mytest1'));
        $this->assertEquals($aModules, $oModule->buildModuleChains($aModulesArray));
    }

    /**
     * oxmodule::mergeModuleArrays() test case, empty
     *
     * @return null
     */
    public function testMergeModuleArraysEmpty()
    {
        $oModule = $this->getProxyClass('oxmodule');

        $aAllModules = array();
        $aAddModules = array();
        $this->assertEquals($aAllModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxmodule::mergeModuleArrays() test case, add single
     *
     * @return null
     */
    public function testMergeModuleArraysAddSingle()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aAllModules = array();
        $aAddModules = array('oxtest' => 'test/mytest');
        $aMrgModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aMrgModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxmodule::mergeModuleArrays() test case, add
     *
     * @return null
     */
    public function testMergeModuleArraysAdd()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aAllModules = array();
        $aAddModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aAddModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxmodule::mergeModuleArrays() test case, existing
     *
     * @return null
     */
    public function testMergeModuleArraysExisting()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aAddModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aAllModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxmodule::mergeModuleArrays() test case, appenf
     *
     * @return null
     */
    public function testMergeModuleArraysAppend()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aAddModules = array('oxtest' => array('test1/mytest1'));
        $aMrgModules = array('oxtest' => array('test/mytest','test1/mytest1'));
        $this->assertEquals($aMrgModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxmodule::mergeModuleArrays() test case, add and append
     *
     * @return null
     */
    public function testMergeModuleArraysAddAndAppend()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aAddModules = array('oxtest' => array('test1/mytest1'), 'oxtest2' => array('test2/mytest2'));
        $aMrgModules = array('oxtest' => array('test/mytest','test1/mytest1'), 'oxtest2' => array('test2/mytest2'));
        $this->assertEquals($aMrgModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxmodule::filterModuleArrays() test case, empty
     *
     * @return null
     */
    public function testFilterModuleArrayEmpty()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aModules = array('oxtest' => array('test/mytest','test1/mytest1'));
        $aExtend  = array();
        $this->assertEquals($aExtend, $oModule->filterModuleArray($aModules, 'notRegisteredExtension'));
    }

    /**
     * oxmodule::filterModuleArrays() test case, single
     *
     * @return null
     */
    public function testFilterModuleArraySingle()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $aModules = array('oxtest' => array('test/mytest','test1/mytest1'));
        $aExtend  = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aExtend, $oModule->filterModuleArray($aModules, 'test'));
    }

     /**
     * oxmodule::getLegacyModules() test case
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

        $oModule = $this->getProxyClass('oxmodule');

        $this->assertEquals( $aLegacyModules, $oModule->getLegacyModules() );
    }

     /**
     * oxmodule::getDisabledModules() test case
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

        $oModule = $this->getProxyClass('oxmodule');

        $this->assertEquals( $aDisabledModules, $oModule->getDisabledModules() );
    }

     /**
     * oxmodule::getDisabledModules() test case
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

        $oModule = $this->getProxyClass('oxmodule');

        $this->assertEquals( $aModulePaths, $oModule->getModulePaths() );
    }

     /**
     * oxmodule::testGetModuleFullPaths() test case
     *
     * @return null
     */
    public function testGetModuleFullPath()
    {
        $sModId = "testModule";

        $oConfig = $this->getMock('oxconfig', array('getModulesDir'));
        $oConfig->expects( $this->any() )
                ->method( 'getModulesDir' )
                ->will($this->returnValue( "/var/path/to/modules/" ));

        $oModule = $this->getMock('oxmodule', array('getModulePath', 'getConfig'));
        $oModule->expects( $this->any() )
                ->method( 'getModulePath' )
                ->with( $this->equalTo($sModId) )
                ->will( $this->returnValue( "oe/module/" ) );

        $oModule->expects( $this->any() )
                ->method( 'getConfig' )
                ->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( "/var/path/to/modules/oe/module/", $oModule->getModuleFullPath( $sModId ) );
    }

    /**
     * oxmodule::testGetModuleFullPaths() test case
     *
     * @return null
     */
    public function testGetModuleFullPathWhenNoModulePathExists()
    {
        $sModId = "testModule";

        $oConfig = $this->getMock('oxconfig', array('getModulesDir'));
        $oConfig->expects( $this->any() )
            ->method( 'getModulesDir' )
            ->will($this->returnValue( "/var/path/to/modules/" ));

        $oModule = $this->getMock('oxmodule', array('getModulePath', 'getConfig'));
        $oModule->expects( $this->any() )
            ->method( 'getModulePath' )
            ->with( $this->equalTo($sModId) )
            ->will( $this->returnValue( null ) );

        $oModule->expects( $this->any() )
            ->method( 'getConfig' )
            ->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( false, $oModule->getModuleFullPath( $sModId ) );
    }

     /**
     * oxmodule::getId() test case
     *
     * @return null
     */
    public function testGetId()
    {
        $aModule = array(
            'id'  => 'testModuleId'
        );

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar( "_aModule", $aModule );

        $this->assertEquals( 'testModuleId', $oModule->getId() );
    }

     /**
     * oxmodule::hasMetadata() test case
     *
     * @return null
     */
    public function testHasMetadata()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar( "_blMetadata", false );
        $this->assertFalse( $oModule->hasMetadata() );

        $oModule->setNonPublicVar( "_blMetadata", true );
        $this->assertTrue( $oModule->hasMetadata() );
    }

     /**
     * oxmodule::isFile() test case
     *
     * @return null
     */
    public function testIsFile()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar( "_blFile", false );
        $this->assertFalse( $oModule->isFile() );

        $oModule->setNonPublicVar( "_blFile", true );
        $this->assertTrue( $oModule->isFile() );
    }

     /**
     * oxmodule::isLegacy() test case
     *
     * @return null
     */
    public function testIsLegacy()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar( "_blLegacy", false );
        $this->assertFalse( $oModule->isLegacy() );

        $oModule->setNonPublicVar( "_blLegacy", true );
        $this->assertTrue( $oModule->isLegacy() );
    }


     /**
     * oxmodule::isRegistered() test case
     *
     * @return null
     */
    public function testIsRegistered()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar( "_blRegistered", false );
        $this->assertFalse( $oModule->isRegistered() );

        $oModule->setNonPublicVar( "_blRegistered", true );
        $this->assertTrue( $oModule->isRegistered() );
    }


     /**
     * oxmodule::getTitle() test case
     *
     * @return null
     */
    public function testGetTitle()
    {
        $iLang = oxLang::getInstance()->getTplLanguage();
        $oModule = $this->getMock( 'oxModule', array('getInfo') );
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("title"), $this->equalTo($iLang) )->will( $this->returnValue("testTitle") );

        $this->assertEquals( "testTitle", $oModule->getTitle() );
    }

     /**
     * oxmodule::getDescription() test case
     *
     * @return null
     */
    public function testGetDescription()
    {
        $iLang = oxLang::getInstance()->getTplLanguage();
        $oModule = $this->getMock( 'oxModule', array('getInfo') );
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("description"), $this->equalTo($iLang) )->will( $this->returnValue("testDesc") );

        $this->assertEquals( "testDesc", $oModule->getDescription() );
    }

    /**
     * oxmodule::_changeBlockStatus() test case
     *
     * @return null
     */
    public function testChangeBlockStatus()
    {
        $oDb = oxDb::getDb();
        $oConfig = new oxConfig();
        $sShopId = $oConfig->getShopId();

        $sQ = "insert into oxtplblocks (oxid, oxactive, oxshopid, oxblockname, oxmodule) values
                                     ('_test1', '0', '$sShopId', 'testBlockName1', 'testext')";

        $oDb->execute( $sQ );

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->UNITchangeBlockStatus( 'testext', '1' );
        $iActive = $oDb->getOne( "select oxactive from oxtplblocks where oxmodule='testext'" );

        $this->assertEquals( '1', $iActive );
    }

    /**
     * oxmodule::_addTemplateBlocks() test case
     *
     * @return null
     */
    public function testAddTemplateBlocks()
    {
        $oDb     = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oConfig = new oxConfig();
        $sShopId = $oConfig->getShopId();

        $oUtilsObject = $this->getMock( 'oxUtilsObject', array('generateUId') );
        $oUtilsObject->expects( $this->at(0) )->method( 'generateUId' )->will( $this->returnValue('_testId1') );
        $oUtilsObject->expects( $this->at(1) )->method( 'generateUId' )->will( $this->returnValue('_testId2') );

        oxTestModules::addModuleObject('oxUtilsObject', $oUtilsObject);

        $aModuleBlocks = array(
            //shop template path, block name, block filename, block possition
            array("template"=>"page/checkout/basket.tpl", "block"=>"basket_btn_next_top", "file"=>"oepaypalexpresscheckout.tpl", "position"=>"1"),
            array("template"=>"page/checkout/order.tpl", "block"=>"basket_btn_next_bottom", "file"=>"oepaypalorder.tpl", "position"=>"2"),
        );

        $oModule = $this->getMock( 'oxModule', array('getId') );
        $oModule->expects( $this->once() )->method( 'getId' )->will( $this->returnValue("testModuleId") );

        $oModule->_addTemplateBlocks( $aModuleBlocks );

        // checking result
        $aRes[] = array( "OXID"=>"_testId1", "OXACTIVE"=>"1", "OXSHOPID"=>$sShopId, "OXTEMPLATE"=>"page/checkout/basket.tpl", "OXBLOCKNAME"=>"basket_btn_next_top", "OXPOS"=>"1", "OXFILE"=>"oepaypalexpresscheckout.tpl", "OXMODULE"=>"testModuleId" ) ;
        $aRes[] = array( "OXID"=>"_testId2", "OXACTIVE"=>"1", "OXSHOPID"=>$sShopId, "OXTEMPLATE"=>"page/checkout/order.tpl", "OXBLOCKNAME"=>"basket_btn_next_bottom", "OXPOS"=>"2", "OXFILE"=>"oepaypalorder.tpl", "OXMODULE"=>"testModuleId" ) ;

        $aBlocks = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getAll( "SELECT OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE FROM oxtplblocks WHERE oxid IN ('_testId1','_testId2') ORDER BY oxid" );

        $this->assertEquals( $aRes, $aBlocks );
    }

    /**
     * oxmodule::_hasInstalledTemplateBlocks() test case
     *
     * @return null
     */
    public function testHasInstalledTemplateBlocks()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $oDb     = oxDb::getDb();

        $sSql = "INSERT INTO `oxtplblocks` (`OXID`, `OXACTIVE`, `OXSHOPID`, `OXTEMPLATE`, `OXBLOCKNAME`, `OXPOS`, `OXFILE`, `OXMODULE`) ".
                "VALUES ('_testId', 1, '$sShopId', 'testTemplate.tpl', 'testBlockName', '1', 'testFile.tpl', 'testModuleId1')";

        $oDb->execute( $sSql );

        $oModule = $this->getProxyClass('oxmodule');

        $this->assertTrue( $oModule->_hasInstalledTemplateBlocks("testModuleId1") );
        $this->assertFalse( $oModule->_hasInstalledTemplateBlocks("testModuleId2") );
    }

    /**
     * oxmodule::_addModuleFiles() test case
     *
     * @return null
     */
    public function testAddModuleFiles()
    {
        $oConfig   = new oxConfig();
        $sShopId   = $oConfig->getShopId();
        $sModuleId = 'testmodule';
        $aModuleFiles = array(
            "testfilea"=>"testmodule/core/testfilea.php",
            "testfileb"=>"testmodule/core/testfileb.php"
        );

        $oModule = new oxmodule();
        $oModule->_addModuleFiles( $aModuleFiles, $sModuleId);

        $aConfigModuleFiles = $oConfig->getConfigParam('aModuleFiles');

        $this->assertArrayHasKey($sModuleId, $aConfigModuleFiles);
        $this->assertEquals( $aModuleFiles, $aConfigModuleFiles[$sModuleId] );
    }

    /**
     * oxmodule::_addModuleVersion() test case
     *
     * @return null
     */
    public function testAddModuleVersion()
    {
        $oConfig   = new oxConfig();
        $sShopId   = $oConfig->getShopId();
        $sModuleId = 'testmodule';
        $sModuleVersion = "1.1";

        $oModule = new oxmodule();
        $oModule->_addModuleVersion( $sModuleVersion, $sModuleId);

        $aConfigModuleVersions = $oConfig->getConfigParam('aModuleVersions');

        $this->assertArrayHasKey($sModuleId, $aConfigModuleVersions);
        $this->assertEquals( $sModuleVersion, $aConfigModuleVersions[$sModuleId] );
    }

    /**
     * oxmodule::_addTemplateFiles() test case
     *
     * @return null
     */
    public function testAddTemplatesFiles()
    {
        $oConfig   = new oxConfig();
        $sModuleId = 'testmodule';
        $aModuleTemplates = array(
            "testa.tpl"=>"testmodule/out/testa.tpl",
            "testb.tpl"=>"testmodule/out/testb.tpl"
        );

        $oModule = new oxmodule();
        $oModule->_addTemplateFiles( $aModuleTemplates, $sModuleId);

        $aConfigModuleTemplates = $oConfig->getConfigParam('aModuleTemplates');

        $this->assertArrayHasKey($sModuleId, $aConfigModuleTemplates);
        $this->assertEquals( $aModuleTemplates, $aConfigModuleTemplates[$sModuleId] );
    }

    /**
     * oxmodule::_addModuleSettings() test case
	 * 
	 * related to @ticket 4255
     *
     * @return null
     */
    public function testAddModuleSettings()
    {
        $oDb     = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oConfig = new oxConfig();
        $sShopId = $oConfig->getShopId();

        $oUtilsObject = $this->getMock( 'oxUtilsObject', array('generateUId') );
        $oUtilsObject->expects( $this->at(0) )->method( 'generateUId' )->will( $this->returnValue('_testId1') );
        $oUtilsObject->expects( $this->at(1) )->method( 'generateUId' )->will( $this->returnValue('_testId1') );
        $oUtilsObject->expects( $this->at(2) )->method( 'generateUId' )->will( $this->returnValue('_testId2') );
        $oUtilsObject->expects( $this->at(3) )->method( 'generateUId' )->will( $this->returnValue('_testId2') );
        oxTestModules::addModuleObject('oxUtilsObject', $oUtilsObject);

        $sModuleId       = 'testmodule';
        // test different constraints for #4255
        $aModuleSettings = array(
            array( 'group' => 'test1',  'name' => 'test_var_1', 'type' => 'str', 'value' => 'A', 'position' => '1', 'constrains' => 'compatibility1' ),
            array( 'group' => 'test2',  'name' => 'test_var_2', 'type' => 'str', 'value' => 'B', 'position' => '2', 'constraints' => 'compatibility2' ),
        );

        $oModule = $this->getMock( 'oxModule', array('getId') );
        $oModule->expects( $this->once() )->method( 'getId' )->will( $this->returnValue($sModuleId) );

        $oModule->_addModuleSettings( $aModuleSettings );

        // checking result
        $aRes[] = array( "OXID" => "_testId1", "OXCFGMODULE" => "module:".$sModuleId, "OXCFGVARNAME" => "test_var_1", "OXGROUPING" => "test1", "OXPOS" => "1", "OXVARCONSTRAINT" => "compatibility1" ) ;
        $aRes[] = array( "OXID" => "_testId2", "OXCFGMODULE" => "module:".$sModuleId, "OXCFGVARNAME" => "test_var_2", "OXGROUPING" => "test2", "OXPOS" => "2", "OXVARCONSTRAINT" => "compatibility2" ) ;

        $aSettings = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll( "SELECT OXID,OXCFGMODULE,OXCFGVARNAME,OXGROUPING,OXPOS,OXVARCONSTRAINT FROM oxconfigdisplay WHERE oxcfgvarname IN ('test_var_1','test_var_2') ORDER BY oxid" );

        $this->assertEquals( $aRes, $aSettings );
    }

    public function testGetIdByPath()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2", "invoicepdf" => "oe/invoicepdf");
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "oe/invoicepdf2/myorder";

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->getIdByPath( $sModule );
        $this->assertEquals( 'invoicepdf2', $oModule->getIdByPath( $sModule ) );
    }

    public function testGetIdByPathUnknownPath()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2");
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "invoicepdf/myorder";

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->getIdByPath( $sModule );
        $this->assertEquals( 'invoicepdf', $oModule->getIdByPath( $sModule ) );
    }

    public function testGetIdByPathUnknownPathNotDir()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2");
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "myorder";

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->getIdByPath( $sModule );
        $this->assertEquals( 'myorder', $oModule->getIdByPath( $sModule ) );
    }

    public function testSaveLegacyModule()
    {
        // prepearing test data
        $aExtendedClasses = array("oxarticle => dir1/module1");
        $moduleId         = "dir1_module1";
        $moduleName       = "module1";
        modConfig::getInstance()->setConfigParam( "aLegacyModules", null );

        // result data
        $aLegacyModules = array( "dir1_module1" => array( "id" => "dir1_module1",
                                                          "title" => "module1",
                                                          "extend" => array("oxarticle" => "dir1/module1")) );

        $oConfig = $this->getMock( 'oxConfig', array('saveShopConfVar') );
        $oConfig->expects( $this->at(0) )->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aLegacyModules"), $this->equalTo($aLegacyModules) );

        $oModule = $this->getMock( 'oxmodule', array('getConfig'), array(), "", false );
        $oModule->expects( $this->any() )->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $sId = $oModule->saveLegacyModule($moduleId, $moduleName, $aExtendedClasses);
        $this->assertEquals( 'dir1_module1', $sId );
    }

    public function testUpdateModuleIds()
    {
        // prepearing test data
        $aTestModulePaths     = array( "dir1/module1" => "dir1/module1", "dir2/module2" => "dir2/module2" );
        $aTestDisabledModules = array( "dir2/module2", "dir4/module4" );

        modConfig::getInstance()->setConfigParam( "aModulePaths", $aTestModulePaths );
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aTestDisabledModules );

        // result data
        $aModulePaths     = array( "dir1/module1" => "dir1/module1", "dir2Module" => "dir2/module2" );
        $aDisabledModules = array( "dir2Module", "dir4/module4" );

        $oConfig = $this->getMock( 'oxConfig', array('saveShopConfVar') );
        $oConfig->expects( $this->at(0) )->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aModulePaths"), $this->equalTo($aModulePaths) );
        $oConfig->expects( $this->at(1) )->method('saveShopConfVar')->with($this->equalTo("arr"), $this->equalTo("aDisabledModules"), $this->equalTo($aDisabledModules) );

        $oModule = $this->getMock( 'oxmodule', array('getConfig'), array(), "", false );
        $oModule->expects( $this->any() )->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $oModule->updateModuleIds( "dir2/module2", "dir2Module" );
    }
}
