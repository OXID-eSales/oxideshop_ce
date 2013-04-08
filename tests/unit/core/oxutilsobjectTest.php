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
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class modOxUtilsObject_oxUtilsObject extends oxUtilsObject
{

    public function setClassNameCache($aValue)
    {
        parent::$_aInstanceCache = $aValue;
    }

    public function getClassNameCache()
    {
        return parent::$_aInstanceCache;
    }

}

/**
 * Test class for Unit_Core_oxutilsobjectTest::testGetObject() test case
 */
class _oxutils_test
{
    /**
     * Does nothing
     *
     * @param bool $a [optional]
     * @param bool $b [optional]
     * @param bool $c [optional]
     * @param bool $d [optional]
     * @param bool $e [optional]
     *
     * @return null
     */
    public function __construct( $a = false, $b = false, $c = false, $d = false, $e = false )
    {
    }
}

class oxModuleUtilsObject extends oxUtilsObject
{
    public function getActiveModuleChain($aClassChain)
    {
        return parent::_getActiveModuleChain($aClassChain);
    }

}

class Unit_Core_oxutilsobjectTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function tearDown()
    {
        oxRemClassModule('modOxUtilsObject_oxUtilsObject');


        $oArticle = new oxarticle();
        $oArticle->delete( 'testArticle' );

        parent::tearDown();
    }

    /**
     * Testing oxUtilsObject::_getObject();
     *
     * @return null
     */
    public function testGetObject()
    {
        $this->assertTrue( oxNew( '_oxutils_test' ) instanceof _oxutils_test );
        $this->assertTrue( oxNew( '_oxutils_test', 1 ) instanceof _oxutils_test );
        $this->assertTrue( oxNew( '_oxutils_test', 1, 2 ) instanceof _oxutils_test );
        $this->assertTrue( oxNew( '_oxutils_test', 1, 2, 3 ) instanceof _oxutils_test );
        $this->assertTrue( oxNew( '_oxutils_test', 1, 2, 3, 4 ) instanceof _oxutils_test );
    }

    public function testOxNew()
    {
        $myConfig = oxConfig::getInstance();
        // 20070808-AS - check known classnames
        $oArticle = oxNew( 'oxarticle' );
        $oArticle = oxNew( 'oxarticle', array( 'aaa' => 'bbb' ) );
        $oArticle = oxNew( 'oxarticle', array( 'aaa' => 'bbb' ) );

        $this->assertTrue( $oArticle instanceof oxarticle );
        $this->assertTrue( isset( $oArticle->aaa ) );
        $this->assertEquals( 'bbb', $oArticle->aaa );
        $sShopDir = "misc/";

        modConfig::getInstance()->setConfigParam("sShopDir", $sShopDir);
        include_once $sShopDir."/modules/oxNewDummyModule.php";

        $aModules = array(strtolower('oxNewDummyModule') => 'oxNewDummyUserModule&oxNewDummyUserModule2');
        modConfig::getInstance()->setConfigParam("aModules", $aModules);
        $oNewDummyModule = oxNew("oxNewDummyModule" );
        $this->assertTrue($oNewDummyModule instanceof oxNewDummyModule);
        $oNewDummyUserModule = oxNew("oxNewDummyUserModule");
        $this->assertTrue($oNewDummyModule instanceof $oNewDummyUserModule);
        //$oNewDummyUserModule2 = modUtils_oxNew("oxNewDummyUserModule2");
        $oNewDummyUserModule2 = oxNew("oxNewDummyUserModule2");
        $this->assertTrue($oNewDummyModule instanceof $oNewDummyUserModule2);

        //if extended class do not exists, shop should work #3371 
        $aModules = array(strtolower('oxNewDummyModule') => 'oxNewDummyUserModule&notExisting');
        modConfig::getInstance()->setConfigParam("aModules", $aModules);

        $oNewDummyModule = oxNew( "oxNewDummyModule" );
        $this->assertTrue($oNewDummyModule instanceof oxNewDummyModule);

        try {
            // This code is expected to raise an exception ...
            $oNewExc = oxNew("non_existing_class");
            $this->fail('An expected oxSystemComponentException has not been raised.');
        } catch (oxSystemComponentException $oEx) {
            // Expected result if oxNew cannot create an object.
            $this->assertEquals($oEx->getMessage(), 'EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND');
        }
    }

    /**
     * oxnew test
     */
    public function testOxNewArticle()
    {
        $oUtilsObj = new oxutilsobject();

        $oArticle    = $oUtilsObj->oxNewArticle( '2177', array( 'aaa' => 'bbb' ) );
        $oNewArticle = $oUtilsObj->oxNewArticle( '2177' );

        $this->assertEquals( $oArticle, $oNewArticle );
    }
    public function testOxNewArticleAndLoad()
    {
        $oUtilsObj = new oxutilsobject();

        $oArticle = $oUtilsObj->oxnew( 'oxarticle' );
        $oArticle->load( '2177' );

        $oNewArticle = $oUtilsObj->oxNewArticle( '2177' );

        $this->assertEquals( $oArticle->getId(), $oNewArticle->getId() );
    }

    public function testGenerateUid()
    {
      //no real test possible, but at least generated ids should be different
      $id1 = oxUtilsObject::getInstance()->generateUid();
      $id2 = oxUtilsObject::getInstance()->generateUid();
      $this->assertNotEquals($id1, $id2);
    }




    public function testResetInstanceCacheSingle()
    {
        $oTestInstance = new modOxUtilsObject_oxUtilsObject();
        $aInstanceCache = array("oxArticle" => new oxArticle(), "oxattribute" => new oxAttribute());
        $oTestInstance->setClassNameCache($aInstanceCache);

        $oTestInstance->resetInstanceCache("oxArticle");

        $aGotInstanceCache = $oTestInstance->getClassNameCache();

        $this->assertEquals(1, count($aGotInstanceCache));
        $this->assertTrue($aGotInstanceCache["oxattribute"] instanceof oxAttribute );
    }

    public function testResetInstanceCacheAll()
    {
        $oTestInstance = new modOxUtilsObject_oxUtilsObject();
        $aInstanceCache = array("oxArticle" => new oxArticle(), "oxattribute" => new oxAttribute());
        $oTestInstance->setClassNameCache($aInstanceCache);

        $oTestInstance->resetInstanceCache();

        $aGotInstanceCache = $oTestInstance->getClassNameCache();

        $this->assertEquals(0, count($aGotInstanceCache));
    }

    public function testGetActiveModuleChain()
    {
        $aModuleChain = array("oe/invoicepdf2/myorder");
        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam' ) );
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('aDisabledModules'))->will($this->returnValue(array("invoicepdf")));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('aModulePaths'))->will($this->returnValue(array("invoicepdf2" => "oe/invoicepdf2", "invoicepdf" => "oe/invoicepdf")));

        $this->getProxyClass( "oxUtilsObject" );
        $oUtilsObject = $this->getMock( 'oxUtilsObjectPROXY', array( 'getConfig' ) );
        $oUtilsObject->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( $aModuleChain, $oUtilsObject->UNITgetActiveModuleChain( $aModuleChain ) );
    }

    public function testGetActiveModuleChainIfDisabled()
    {
        $aModuleChain = array("oe/invoicepdf/myorder");
        $aModuleChainResult = array();
        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam' ) );
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('aDisabledModules'))->will($this->returnValue(array("invoicepdf")));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('aModulePaths'))->will($this->returnValue(array("invoicepdf" => "oe/invoicepdf")));

        $this->getProxyClass( "oxUtilsObject" );
        $oUtilsObject = $this->getMock( 'oxUtilsObjectPROXY', array( 'getConfig' ) );
        $oUtilsObject->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( $aModuleChainResult, $oUtilsObject->UNITgetActiveModuleChain( $aModuleChain ) );
    }

    public function testGetActiveModuleChainIfDisabledWithoutPath()
    {
        $aModuleChain = array("invoicepdf/myorder");
        $aModuleChainResult = array();
        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam' ) );
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('aDisabledModules'))->will($this->returnValue(array("invoicepdf")));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('aModulePaths'))->will($this->returnValue(array("invoicepdf2" => "oe/invoicepdf2")));

        $this->getProxyClass( "oxUtilsObject" );
        $oUtilsObject = $this->getMock( 'oxUtilsObjectPROXY', array( 'getConfig' ) );
        $oUtilsObject->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( $aModuleChainResult, $oUtilsObject->UNITgetActiveModuleChain( $aModuleChain ) );
    }

    public function testDisableModule()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2", "invoicepdf" => "oe/invoicepdf");
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "oe/invoicepdf2/myorder";
        $aDisabledModulesResult = array('test1', 'invoicepdf2');

        $oUtilsObject = $this->getProxyClass( "oxUtilsObject" );
        $oUtilsObject->UNITdisableModule( $sModule );
        modConfig::getInstance()->getConfigParam( "aDisabledModules" );
        $this->assertEquals( $aDisabledModulesResult, modConfig::getInstance()->getConfigParam( "aDisabledModules" ) );
    }

    public function testDisableModuleUnknownPath()
    {
        $aDisabledModules = array('test1');
        $aModulePaths     = array("invoicepdf2" => "oe/invoicepdf2");
        modConfig::getInstance()->setConfigParam( "aDisabledModules", $aDisabledModules );
        modConfig::getInstance()->setConfigParam( "aModulePaths", $aModulePaths );
        $sModule = "invoicepdf/myorder";
        $aDisabledModulesResult = array('test1', 'invoicepdf');

        $oUtilsObject = $this->getProxyClass( "oxUtilsObject" );
        $oUtilsObject->UNITdisableModule( $sModule );
        modConfig::getInstance()->getConfigParam( "aDisabledModules" );
        $this->assertEquals( $aDisabledModulesResult, modConfig::getInstance()->getConfigParam( "aDisabledModules" ) );
    }

}
