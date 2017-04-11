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
    public function __construct($a = false, $b = false, $c = false, $d = false, $e = false)
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

        $oConfigFile = new OxConfigFile(OX_BASE_PATH . "config.inc.php");
        OxRegistry::set("OxConfigFile", $oConfigFile);


        $oArticle = new oxarticle();
        $oArticle->delete('testArticle');

        $sFilePath = $this->getTestFilePath();
        if (!empty($sFilePath) && file_exists($sFilePath)) {
            unlink($sFilePath);
        }

        parent::tearDown();
    }

    /**
     * Testing oxUtilsObject::_getObject();
     *
     * @return null
     */
    public function testGetObject()
    {
        $this->assertTrue(oxNew('_oxutils_test') instanceof _oxutils_test);
        $this->assertTrue(oxNew('_oxutils_test', 1) instanceof _oxutils_test);
        $this->assertTrue(oxNew('_oxutils_test', 1, 2) instanceof _oxutils_test);
        $this->assertTrue(oxNew('_oxutils_test', 1, 2, 3) instanceof _oxutils_test);
        $this->assertTrue(oxNew('_oxutils_test', 1, 2, 3, 4) instanceof _oxutils_test);
    }

    public function testOxNew()
    {
        // 20070808-AS - check known classnames
        $oArticle = oxNew('oxarticle');
        $oArticle = oxNew('oxarticle', array('aaa' => 'bbb'));
        $oArticle = oxNew('oxarticle', array('aaa' => 'bbb'));

        $this->assertTrue($oArticle instanceof oxarticle);
        $this->assertTrue(isset($oArticle->aaa));
        $this->assertEquals('bbb', $oArticle->aaa);
        $sShopDir = getTestsBasePath() . "misc/";

        modConfig::getInstance()->setConfigParam("sShopDir", $sShopDir);
        include_once $sShopDir . "/modules/oxNewDummyModule.php";

        $aModules = array(strtolower('oxNewDummyModule') => 'oxNewDummyUserModule&oxNewDummyUserModule2');
        modConfig::getInstance()->setConfigParam("aModules", $aModules);
        oxUtilsObject::resetModuleVars();

        $oNewDummyModule = oxNew("oxNewDummyModule");
        $this->assertTrue($oNewDummyModule instanceof oxNewDummyModule);

        //the following code should work uncommented after #4301 is fixed
        /*
        $oNewDummyUserModule = oxNew("oxNewDummyUserModule");
        $this->assertTrue($oNewDummyModule instanceof $oNewDummyUserModule);
        //$oNewDummyUserModule2 = modUtils_oxNew("oxNewDummyUserModule2");
        $oNewDummyUserModule2 = oxNew("oxNewDummyUserModule2");
        $this->assertTrue($oNewDummyModule instanceof $oNewDummyUserModule2);*/

        //if extended class do not exists, shop should work #3371
        $aModules = array(strtolower('oxNewDummyModule') => 'oxNewDummyUserModule&notExisting');
        modConfig::getInstance()->setConfigParam("aModules", $aModules);
        oxUtilsObject::resetModuleVars();

        $oNewDummyModule = oxNew("oxNewDummyModule");
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
        $this->assertTrue($aGotInstanceCache["oxattribute"] instanceof oxAttribute);
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

        $this->getProxyClass("oxUtilsObject");
        $oUtilsObject = $this->getMock('oxUtilsObjectPROXY', array('getModuleVar'));
        $oUtilsObject->expects($this->at(0))->method('getModuleVar')->with($this->equalTo('aDisabledModules'))->will($this->returnValue(array("invoicepdf")));
        $oUtilsObject->expects($this->at(1))->method('getModuleVar')->with($this->equalTo('aModulePaths'))->will($this->returnValue(array("invoicepdf2" => "oe/invoicepdf2", "invoicepdf" => "oe/invoicepdf")));

        $this->assertEquals($aModuleChain, $oUtilsObject->UNITgetActiveModuleChain($aModuleChain));
    }

    public function testGetActiveModuleChainIfDisabled()
    {
        $aModuleChain = array("oe/invoicepdf/myorder");
        $aModuleChainResult = array();

        $this->getProxyClass("oxUtilsObject");
        $oUtilsObject = $this->getMock('oxUtilsObjectPROXY', array('getModuleVar'));
        $oUtilsObject->expects($this->at(0))->method('getModuleVar')->with($this->equalTo('aDisabledModules'))->will($this->returnValue(array("invoicepdf")));
        $oUtilsObject->expects($this->at(1))->method('getModuleVar')->with($this->equalTo('aModulePaths'))->will($this->returnValue(array("invoicepdf" => "oe/invoicepdf")));

        $this->assertEquals($aModuleChainResult, $oUtilsObject->UNITgetActiveModuleChain($aModuleChain));
    }

    public function testGetActiveModuleChainIfDisabledWithoutPath()
    {
        $aModuleChain = array("invoicepdf/myorder");
        $aModuleChainResult = array();

        $this->getProxyClass("oxUtilsObject");
        $oUtilsObject = $this->getMock('oxUtilsObjectPROXY', array('getModuleVar'));
        $oUtilsObject->expects($this->at(0))->method('getModuleVar')->with($this->equalTo('aDisabledModules'))->will($this->returnValue(array("invoicepdf")));
        $oUtilsObject->expects($this->at(1))->method('getModuleVar')->with($this->equalTo('aModulePaths'))->will($this->returnValue(array("invoicepdf2" => "oe/invoicepdf2")));

        $this->assertEquals($aModuleChainResult, $oUtilsObject->UNITgetActiveModuleChain($aModuleChain));
    }

    public function testDisableModule()
    {
        $sModuleId = 'testId';

        $oModule = new oxModule();
        $oModule->load($sModuleId);

        $oModuleInstaller = $this->getMock('oxModuleInstaller', array('deactivate'));
        $oModuleInstaller->expects($this->once())->method('deactivate')->with($oModule);

        oxTestModules::addModuleObject('oxModuleInstaller', $oModuleInstaller);

        $oUtilsObject = $this->getProxyClass("oxUtilsObject");
        $oUtilsObject->UNITdisableModule($sModuleId);
    }

    public function testSetGetCache()
    {
        $sTest = "test val";

        $oSubj = $this->getProxyClass("oxUtilsObject");

        $oSubj->UNITsetToCache("testKey", $sTest);
        $this->assertEquals($sTest, $oSubj->UNITgetFromCache("testKey"));
    }

    public function testGetModuleVarFromDB()
    {
        $oSubj = $this->getProxyClass("oxUtilsObject");
        $this->assertEquals(Array("a7c40f631fc920687.20179984"), $oSubj->UNITgetModuleVarFromDB("aHomeCountry"));
    }

    public function testGetCacheFileName()
    {
        $oSubj = $this->getProxyClass("oxUtilsObject");
        $sBaseShop = oxRegistry::getConfig()->getBaseShopId();

        $sExpt = "config." . $sBaseShop . ".testval.txt";
        $this->assertEquals($sExpt, basename($oSubj->UNITgetCacheFileName("testVal")));
    }

    public function testGetCacheDir()
    {
        $oSubj = $this->getProxyClass("oxUtilsObject");
        $this->assertContains("tmp", $oSubj->UNITgetCacheDir());
    }

    public function testGetClassName_classExist_moduleClassReturn()
    {
        $sClassName = 'oxorder';
        $sClassNameWhichExtends = $sClassNameExpect = 'oemodulenameoxorder';
        $oUtilsObject = $this->_prepareFakeModule($sClassName, $sClassNameWhichExtends);

        $this->assertSame($sClassNameExpect, $oUtilsObject->getClassName($sClassName));
    }

    public function testGetClassName_classNotExist_originalClassReturn()
    {
        $sClassName = $sClassNameExpect = 'oxorder';
        $sClassNameWhichExtends = 'oemodulenameoxorder_different2';
        $oUtilsObject = $this->_prepareFakeModule($sClassName, $sClassNameWhichExtends);

        $this->assertSame($sClassNameExpect, $oUtilsObject->getClassName($sClassName));
    }

    public function testGetClassName_classNotExistDoDisableModuleOnError_originalClassReturn()
    {
        $this->_setConfigFileParam('blDoNotDisableModuleOnError', false);

        $sClassName = $sClassNameExpect = 'oxorder';
        $sClassNameWhichExtends = 'oemodulenameoxorder_different3';
        $oUtilsObject = $this->_prepareFakeModule($sClassName, $sClassNameWhichExtends);

        $this->assertSame($sClassNameExpect, $oUtilsObject->getClassName($sClassName));
    }

    public function testGetClassName_classNotExistDoNotDisableModuleOnError_errorThrow()
    {
        $this->_setConfigFileParam('blDoNotDisableModuleOnError', true);

        $sClassName = 'oxorder';
        $sClassNameWhichExtends = 'oemodulenameoxorder_different4';
        $oUtilsObject = $this->_prepareFakeModule($sClassName, $sClassNameWhichExtends);

        try {
            $oUtilsObject->getClassName($sClassName);
        } catch (Exception $e) {
            $this->assertEquals('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND', $e->getMessage(), 'Exception message wrong.');

            return;
        }

        $this->fail('Should throw exception as class does not exist and config parameter set to not disable module on error');
    }

    private function _setConfigFileParam($sParamName, $sParamValue)
    {
        $oConfigFile = new OxConfigFile(OX_BASE_PATH . "config.inc.php");
        $oConfigFile->$sParamName = $sParamValue;
        OxRegistry::set("OxConfigFile", $oConfigFile);
    }

    private function _prepareFakeModule($sClassToExtend, $sClassNameWhichExtends)
    {
        $aModulesArray = array(
            $sClassToExtend => $sClassNameWhichExtends,
        );

        $oUtilsObject = new oxUtilsObject();
        $oUtilsObject->setModuleVar('aModules', $aModulesArray);

        $sFilePath = $this->getTestFilePath();
        file_put_contents($sFilePath, '<?php class oemodulenameoxorder_different extends oemodulenameoxorder_parent {}');

        return $oUtilsObject;
    }

    /**
     * Get path to test file.
     *
     * @return string
     */
    private function getTestFilePath()
    {
        return $this->getConfig()->getConfigParam('sShopDir') . 'modules/oemodulenameoxorder.php';
    }
}
