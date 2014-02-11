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

class Unit_Core_oxModuleTest extends OxidTestCase
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
     * oxModule::load() test case
     *
     * @return null
     */
    public function testLoad()
    {
            $aModule = array(
                'id'           => 'invoicepdf',
                'title'        => 'Invoice PDF',
                'description'  => 'Module to export invoice PDF files.',
                'thumbnail'    => 'picture.png',
                'version'      => '1.0',
                'author'       => 'OXID eSales AG',
                'extend'       => array(
                    'oxorder' => 'oe/invoicepdf/myorder'
                ),
                'active' => true
            );

            $oModule = $this->getProxyClass( 'oxmodule' );
            $this->assertTrue( $oModule->load( 'oe/invoicepdf' ) );
            $this->assertEquals( $aModule, $oModule->getNonPublicVar( "_aModule" ) );
    }

    /**
     * oxModule::load() test case, no extend
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
            $this->assertTrue( $oModule->isActive() );
            $this->assertFalse( $oModule->isExtended() );
    }

    /**
     * oxModule::load() test case
     *
     * @return null
     */
    public function testLoadWhenModuleDoesNotExists()
    {
        $oModule = new oxModule;
        $this->assertFalse( $oModule->load( 'non_existing_module' ) );
    }

    /**
     * oxModule::loadByDir()
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
     * oxModule::getInfo() test case
     *
     * @return null
     */
    public function testGetInfo()
    {
        $aModule = array(
            'id'    => 'testModuleId',
            'title' => 'testModuleTitle'
        );

        $oModule = new oxModule();
        $oModule->setModuleData( $aModule );

        $this->assertEquals( "testModuleId", $oModule->getInfo( "id" ) );
        $this->assertEquals( "testModuleTitle", $oModule->getInfo( "title" ) );
    }

    /**
     * oxModule::getInfo() test case - selecting multi language value
     *
     * @return null
     */
    public function testGetInfo_usingLanguage()
    {
        $aModule = array(
            'title' => 'testModuleTitle',
            'description' => array( "en" => "test EN value", "de" => "test DE value" )
        );

        $oModule = new oxModule();
        $oModule->setModuleData( $aModule );

        $this->assertEquals( 'testModuleTitle', $oModule->getInfo( "title" ) );
        $this->assertEquals( 'testModuleTitle', $oModule->getInfo( "title", 1 ) );

        $this->assertEquals( "test DE value", $oModule->getInfo( "description", 0 ) );
        $this->assertEquals( "test EN value", $oModule->getInfo( "description", 1 ) );
        $this->assertEquals( "test EN value", $oModule->getInfo( "description", 2 ) );
    }

    /**
     * oxModule::isActive() test case, empty
     *
     * @return null
     */
    public function testIsActiveEmpty()
    {
        $aModules = array();
        $this->getConfig()->setConfigParam( "aModules", $aModules );

        $aExtend = array('extend' => array());
        $oModule = new oxModule();
        $oModule->setModuleData( $aExtend );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, active
     *
     * @return null
     */
    public function testIsActiveActive()
    {
        $aModules = array('oxtest' => 'test/mytest');
        $this->getConfig()->setConfigParam( "aModules", $aModules );

        $aExtend  = array('id' => 'test', 'extend' => array('oxtest' => 'test/mytest'));
        $oModule = new oxModule();
        $oModule->setModuleData( $aExtend );

        $this->assertTrue($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, active in chain
     *
     * @return null
     */
    public function testIsActiveActiveChain()
    {
        $aModules = array('oxtest' => 'test/mytest&test2/mytest2');
        $this->getConfig()->setConfigParam( "aModules", $aModules );

        $aExtend  = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule = new oxModule();
        $oModule->setModuleData( $aExtend );

        $this->assertTrue($oModule->isActive());
    }
    /**
     * oxModule::isActive() test case, inactive
     *
     * @return null
     */
    public function testIsActiveInactive()
    {
        $aModule  = array('extend' => array('oxtest' => 'test/mytest'));
        $oModule = new oxModule();
        $oModule->setModuleData( $aModule );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, inactive in chain
     *
     * @return null
     */
    public function testIsActiveInactiveChain()
    {
        $aModules = array('oxtest' => 'test1/mytest1&test2/mytest2');
        $this->getConfig()->setConfigParam( "aModules", $aModules );

        $aExtend  = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule = new oxModule();
        $oModule->setModuleData( $aExtend );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, deactivated
     *
     * @return null
     */
    public function testIsActiveDeactivated()
    {
        $aDisabledModules = array('test');
        $this->getConfig()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $aModule  = array('id' => 'test');
        $oModule = new oxModule();
        $oModule->setModuleData( $aModule );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, not deactivated in chain
     *
     * @return null
     */
    public function testIsActiveDeactivatedChain()
    {
        $aDisabledModules = array('mytest1', 'test', 'test2');
        $this->getConfig()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $aModule  = array('id' => 'test');
        $oModule = new oxModule();
        $oModule->setModuleData( $aModule );

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, active
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

    public function providerIsActive_shopClassExtendedByMoreThanOneClass()
    {
        return array(
            // Module active
            array(
                array(
                    'oxtest1' =>  array(
                        'module1/module1mytest0',
                        'test1/__testmytest1',
                        'test1/__testmytest2'
                    )
                ),
                array(
                'id' => '__test',
                'extend' => array(
                    'oxtest1' => array(
                        'test1/__testmytest1', 'test1/__testmytest2'
                    )
                )
            ),
                true
            ),
            // Module inactive, because one of extensions missing in activated extensions array
            array(
                array(
                    'oxtest1' =>  array(
                        'module1/module1mytest0',
                        'test1/__testmytest1',
                        'test1/__testmytest2'
                    )
                ),
                array(
                    'id' => '__test',
                    'extend' => array(
                        'oxtest1' => array(
                            'test1/__testmytest1',
                            'test1/__testmytest2',
                            'test1/__testmytest3'
                        )
                    )
                ),
                false
            ),
            // Module inactive, because there is no extension in activated extensions array
            array(
                array(
                    'oxtest1' =>  array(
                        'module1/module1mytest0',
                    )
                ),
                array(
                'id' => '__test',
                'extend' => array(
                    'oxtest1' => array(
                        'test1/__testmytest1', 'test1/__testmytest2'
                    )
                )
            ),
                false
            ),
        );
    }

    /**
     * Test for bug #4424
     * Checks if possible to extend one shop class with more than one module classes.
     *
     * @dataProvider providerIsActive_shopClassExtendedByMoreThanOneClass
     */
    public function testIsActive_shopClassExtendedByMoreThanOneClass( $aAlreadyActivatedModule, $aModuleToActivate, $blResult )
    {
        $oModuleHandler = $this->getMock( 'oxModule', array( 'getAllModules' ) );
        $oModuleHandler->expects( $this->once() )->method( 'getAllModules')->will( $this->returnValue( $aAlreadyActivatedModule ) );
        $oModuleHandler->setModuleData( $aModuleToActivate );

        $this->assertSame( $blResult, $oModuleHandler->isActive(), 'Module extends shop class, so methods should return true.' );
    }

    /**
     * oxModule::isExtended() test case,
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
     * oxModule::isExtended() test case,
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
     * oxModule::isExtended() test case,no metadata
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

    public function testHasExtendClass_hasExtendedClass_true()
    {
        $oModuleHandler = $this->getProxyClass( 'oxmodule') ;
        $aModule  = array( 'id' => '__test', 'extend' => array( 'oxtest1' => 'test1/mytest1' ) );

        $oModuleHandler->setNonPublicVar( "_aModule", $aModule );
        $oModuleHandler->setNonPublicVar( "_blMetadata", false );

        $this->assertTrue( $oModuleHandler->hasExtendClass(), 'Module has extended class, so methods should return true.' );
    }

    public function testHasExtendClass_hasNoExtendClassArray_false()
    {
        $oModuleHandler = $this->getProxyClass( 'oxmodule') ;
        $aModule  = array( 'id' => '__test' );

        $oModuleHandler->setNonPublicVar( "_aModule", $aModule );
        $oModuleHandler->setNonPublicVar( "_blMetadata", false );

        $this->assertFalse( $oModuleHandler->hasExtendClass(), 'Module has no extended class, so methods should return false.' );
    }

    public function testHasExtendClass_hasEmptyExtendedClassArray_false()
    {
        $oModuleHandler = $this->getProxyClass( 'oxmodule') ;
        $aModule  = array( 'id' => '__test', 'extend' => array() );

        $oModuleHandler->setNonPublicVar( "_aModule", $aModule );
        $oModuleHandler->setNonPublicVar( "_blMetadata", false );

        $this->assertFalse( $oModuleHandler->hasExtendClass(), 'Module has no extended class, so methods should return false.' );
    }

    /**
     * oxModule::mergeModuleArrays() test case, empty
     */
    public function testMergeModuleArraysEmpty()
    {
        $oModule =new oxModule();

        $aAllModules = array();
        $aAddModules = array();
        $this->assertEquals($aAllModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxModule::mergeModuleArrays() test case, add single
     */
    public function testMergeModuleArraysAddSingle()
    {
        $oModule = new oxModule();
        $aAllModules = array();
        $aAddModules = array('oxtest' => 'test/mytest');
        $aMrgModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aMrgModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxModule::mergeModuleArrays() test case, add
     */
    public function testMergeModuleArraysAdd()
    {
        $oModule = new oxModule();
        $aAllModules = array();
        $aAddModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aAddModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxModule::mergeModuleArrays() test case, existing
     */
    public function testMergeModuleArraysExisting()
    {
        $oModule = new oxModule();
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aAddModules = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aAllModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxModule::mergeModuleArrays() test case, append
     */
    public function testMergeModuleArraysAppend()
    {
        $oModule = new oxModule();
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aAddModules = array('oxtest' => array('test1/mytest1'));
        $aMrgModules = array('oxtest' => array('test/mytest','test1/mytest1'));

        $this->assertEquals($aMrgModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxModule::mergeModuleArrays() test case, add and append
     */
    public function testMergeModuleArraysAddAndAppend()
    {
        $oModule = new oxModule();
        $aAllModules = array('oxtest' => array('test/mytest'));
        $aAddModules = array('oxtest' => array('test1/mytest1'), 'oxtest2' => array('test2/mytest2'));
        $aMrgModules = array('oxtest' => array('test/mytest','test1/mytest1'), 'oxtest2' => array('test2/mytest2'));

        $this->assertEquals($aMrgModules, $oModule->mergeModuleArrays($aAllModules, $aAddModules));
    }

    /**
     * oxModule::filterModuleArrays() test case, empty
     */
    public function testFilterModuleArrayEmpty()
    {
        $oModule = new oxModule();
        $aModules = array('oxtest' => array('test/mytest','test1/mytest1'));
        $aExtend  = array();
        $this->assertEquals($aExtend, $oModule->filterModuleArray($aModules, 'notRegisteredExtension'));
    }

    /**
     * oxModule::filterModuleArrays() test case, single
     */
    public function testFilterModuleArraySingle()
    {
        $oModule = new oxModule();

        $aModules = array('oxtest' => array('test/mytest','test1/mytest1'));
        $aExtend  = array('oxtest' => array('test/mytest'));

        $this->assertEquals($aExtend, $oModule->filterModuleArray($aModules, 'test'));
    }

    /**
     * oxModule::getDisabledModules() test case
     */
    public function testGetDisabledModules()
    {
        $aDisabledModules = array(
            'testExt1',
            'testExt2'
        );

        $this->getConfig()->setConfigParam( "aDisabledModules", $aDisabledModules );

        $oModule = new oxModule();

        $this->assertEquals( $aDisabledModules, $oModule->getDisabledModules() );
    }

    /**
     * oxModule::getModulePaths() test case
     */
    public function testGetModulePaths()
    {
        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );

        $this->getConfig()->setConfigParam( "aModulePaths", $aModulePaths );

        $oModule = new oxModule();

        $this->assertEquals( $aModulePaths, $oModule->getModulePaths() );
    }

    /**
     * oxModule::testGetModuleFullPaths() test case
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
     * oxModule::testGetModuleFullPaths() test case
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
     * oxModule::getId() test case
     */
    public function testGetId()
    {
        $aModule = array(
            'id'  => 'testModuleId'
        );

        $oModule = new oxModule;
        $oModule->setModuleData($aModule);

        $this->assertEquals( 'testModuleId', $oModule->getId() );
    }

    public function testGetExtensions_hasExtensions_array()
    {
        $aModule = array(
            'id'  => 'testModuleId',
            'extend' => array( 'class' => 'vendor/module/path/class' )
        );

        $oModule = new oxModule;
        $oModule->setModuleData( $aModule );

        $this->assertEquals( array( 'class' => 'vendor/module/path/class' ) , $oModule->getExtensions() );
    }

    public function testGetExtensions_hasNoExtensions_emptyArray()
    {
        $aModule = array(
            'id'  => 'testModuleId'
        );

        $oModule = new oxModule;
        $oModule->setModuleData( $aModule );

        $this->assertEquals( array() , $oModule->getExtensions() );
    }

    /**
     * oxModule::hasMetadata() test case
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
     * oxModule::isRegistered() test case
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
     * oxModule::getTitle() test case
     *
     * @return null
     */
    public function testGetTitle()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();
        $oModule = $this->getMock( 'oxModule', array('getInfo') );
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("title"), $this->equalTo($iLang) )->will( $this->returnValue("testTitle") );

        $this->assertEquals( "testTitle", $oModule->getTitle() );
    }

    /**
     * oxModule::getDescription() test case
     *
     * @return null
     */
    public function testGetDescription()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();
        $oModule = $this->getMock( 'oxModule', array('getInfo') );
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("description"), $this->equalTo($iLang) )->will( $this->returnValue("testDesc") );

        $this->assertEquals( "testDesc", $oModule->getDescription() );
    }

    /**
     * oxModule::_changeBlockStatus() test case
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
     * oxModule::_addTemplateBlocks() test case
     *
     * @return null
     */
    public function testAddTemplateBlocks()
    {
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
     * oxModule::_hasInstalledTemplateBlocks() test case
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
     * oxModule::_addModuleFiles() test case
     *
     * @return null
     */
    public function testAddModuleFiles()
    {
        $oConfig   = new oxConfig();
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
     * oxModule::_addModuleVersion() test case
     *
     * @return null
     */
    public function testAddModuleVersion()
    {
        $oConfig   = new oxConfig();
        $sModuleId = 'testmodule';
        $sModuleVersion = "1.1";

        $oModule = new oxmodule();
        $oModule->_addModuleVersion( $sModuleVersion, $sModuleId);

        $aConfigModuleVersions = $oConfig->getConfigParam('aModuleVersions');

        $this->assertArrayHasKey($sModuleId, $aConfigModuleVersions);
        $this->assertEquals( $sModuleVersion, $aConfigModuleVersions[$sModuleId] );
    }

    /**
     * oxModule::_addTemplateFiles() test case
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
     * oxModule::_addModuleSettings() test case
     *
     * related to @ticket 4255
     *
     * @return null
     */
    public function testAddModuleSettings()
    {
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
        $this->getConfig()->setConfigParam( "aDisabledModules", $aDisabledModules );
        $this->getConfig()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "oe/invoicepdf2/myorder";

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->getIdByPath( $sModule );
        $this->assertEquals( 'invoicepdf2', $oModule->getIdByPath( $sModule ) );
    }

    public function testGetIdByPathUnknownPath()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2");
        $this->getConfig()->setConfigParam( "aDisabledModules", $aDisabledModules );
        $this->getConfig()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "invoicepdf/myorder";

        $oModule = new oxModule();
        $oModule->getIdByPath( $sModule );
        $this->assertEquals( 'invoicepdf', $oModule->getIdByPath( $sModule ) );
    }

    public function testGetIdByPathUnknownPathNotDir()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2");
        $this->getConfig()->setConfigParam( "aDisabledModules", $aDisabledModules );
        $this->getConfig()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "myorder";

        $oModule = new oxModule();
        $oModule->getIdByPath( $sModule );
        $this->assertEquals( 'myorder', $oModule->getIdByPath( $sModule ) );
    }

}
