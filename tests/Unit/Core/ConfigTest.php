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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxubase;

use \oxConfig;
use \stdClass;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

use OxidEsales\EshopCommunity\Core\Module\ModuleTemplatePathCalculator;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseConnectionException;
use OxidEsales\EshopCommunity\Core\Registry;

class modForTestGetBaseTplDirExpectsDefault extends oxConfig
{
    public function init()
    {
        if ($this->_blInit) {
            return;
        }
        $this->_blInit = true;
        $this->_loadVarsFromFile();
        $this->_setDefaults();
    }

    public function getShopId()
    {
        return 'xxx';
    }
}


class modForTestInitLoadingPriority extends oxConfig
{

    public $iDebug;

    protected function _loadVarsFromDb($sShopID, $aOnlyVars = null, $sModule = '')
    {
        $this->_setConfVarFromDb("iDebug", "str", 33);

        return true;
    }
}

class ConfigTest extends \OxidTestCase
{

    protected $_iCurr = null;
    protected $_aShops = array();
    private $shopUrl = 'http://www.example.com/';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->getConfig()->sTheme = false;

        // copying
        $this->_iCurr = $this->getSession()->getVariable('currency');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        // cleaning up
        $sQ = 'delete from oxconfig where oxvarname = "xxx" ';
        oxDb::getDb()->execute($sQ);

        foreach ($this->_aShops as $oShop) {
            $oShop->delete();
        }
        $this->_aShops = array();

        $sDir = $this->getConfig()->getConfigParam('sShopDir') . "/out/2";
        if (is_dir(realpath($sDir))) {
            oxRegistry::get("oxUtilsFile")->deleteDir($sDir);
        }
        $sDir = $this->getConfig()->getConfigParam('sShopDir') . "/out/en/tpl";
        if (is_dir(realpath($sDir))) {
            oxRegistry::get("oxUtilsFile")->deleteDir($sDir);
        }

        $this->cleanUpTable('oxconfig');
        parent::tearDown();
    }

    public function testGetLogsDir()
    {
        $this->assertEquals($this->getConfig()->getConfigParam('sShopDir') . 'log/', $this->getConfig()->getLogsDir());
    }

    /*
     * Testing special ssl handling for profihost customers
     */
    public function testIsSsl_specialHandling()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return null; } else { return array( "HTTP_X_FORWARDED_SERVER" => "sslsites.de" ); } }');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'), array(), '', false);
        $oConfig->expects($this->never())->method('getConfigParam');
        $this->assertTrue($oConfig->isSsl());
    }

    /*
     * Testing method when shop is not in ssl mode
     */
    public function testIsSsl_notSslMode()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return null; } else { return array(); } }');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->never())->method('getConfigParam');

        $this->assertFalse($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode but no ssl shop links exist
     */
    public function testIsSsl_SslMode_NoSslShopUrl()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('sSSLShopURL'))->will($this->returnValue(''));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('sMallSSLShopURL'))->will($this->returnValue(''));

        $this->assertFalse($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode and ssl shop link exists
     */
    public function testIsSsl_SslMode_WithSslShopUrl()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('sSSLShopURL'))->will($this->returnValue('https://eshop/'));

        $this->assertTrue($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode and only subshop ssl link exists
     * (M:1271)
     */
    public function testIsSsl_SslMode_WithSslShopUrl_forSubshop()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('sSSLShopURL'))->will($this->returnValue(''));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('sMallSSLShopURL'))->will($this->returnValue('https://subshop/'));

        $this->assertTrue($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode with different params returnede
     * by HTTPS parameter
     * (M:1271)
     */
    public function testIsSsl_SslMode_WithDifferentParams()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('sSSLShopURL'))->will($this->returnValue('https://eshop'));
        $this->assertTrue($oConfig->isSsl());

        oxTestModules::cleanUp();
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( $aA[0] == "HTTPS" ) { return "on"; } else { return array(); } }');
        $this->assertTrue($oConfig->isSsl());
    }

    /**
     * test that is httpsOnly method on config returns true if connection is using https and the shop is configured
     * with https for both urls
     */
    public function testIsHttpsOnlySameUrlWithSSl() {
        $res = $this->isHttpsOnlySameUrl(true);
        $this->assertTrue( $res);
    }

    /**
     * test that is httpsOnly method on config returns false if connection is not using https and the shop is configured
     * with http for both urls
     */
    public function testIsHttpsOnlySameUrlNotSsl() {
        $res = $this->isHttpsOnlySameUrl(false);
        $this->assertFalse($res);
    }

    /**
     * simulates https or http connection depending on the $withSSl parameter and
     * runs isHttpsOnly method on config object
     * the result is returned
     * @return bool
     */
    private function isHttpsOnlySameUrl($withSsl)
    {
        $config = $this->getMock('oxconfig', array('isSsl', 'getSslShopUrl', 'getShopUrl'));
        $config->expects($this->any())->method('isSsl')->will($this->returnValue($withSsl));
        foreach (['getSslShopUrl', 'getShopUrl'] as $method) {
            $config->expects($this->any())->method($method)->will($this->returnValue('http'. ($withSsl?'s':'') . '://oxid-esales.com'));
        }
        $res = $config->isHttpsOnly();
        return $res;
    }

    public function testIsUtfWhenInUtfMode()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->any())->method('getConfigParam')->with($this->equalTo('iUtfMode'))->will($this->returnValue(0));
        $this->assertFalse($oConfig->isUtf());
    }

    public function testIsUtfWhenInISOMode()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->any())->method('getConfigParam')->with($this->equalTo('iUtfMode'))->will($this->returnValue(1));
        $this->assertTrue($oConfig->isUtf());
    }

    private function _getOutPath($oConfig, $sTheme = null, $blAbsolute = true)
    {
        $sShop = $blAbsolute ? $oConfig->getConfigParam('sShopDir') : "";

        if (is_null($sTheme)) {
            $sTheme = $oConfig->getConfigParam('sTheme');
        }

        if ($sTheme) {
            $sTheme .= "/";
        }

        return $sShop . 'out/' . $sTheme;
    }

    private function _getViewsPath($oConfig, $sTheme = null, $blAbsolute = true)
    {
        $sShop = $blAbsolute ? $oConfig->getConfigParam('sShopDir') : "";

        if (is_null($sTheme)) {
            $sTheme = $oConfig->getConfigParam('sTheme');
        }

        if ($sTheme) {
            $sTheme .= "/";
        }

        return $sShop . 'Application/views/' . $sTheme;
    }

    /**
     * Testing config init - no connection to DB
     */
    public function testInit_noConnection()
    {
        $this->setTime(time());

        /** @var DatabaseConnectionException $oEx */
        $previousException = new \Exception();
        $oEx = new DatabaseConnectionException('', 0, $previousException);

        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $utilsMock */
        $utilsMock = $this->getMock('oxUtils', array('showMessageAndExit'));
        $utilsMock->expects($this->once())->method('showMessageAndExit')->with($this->equalTo($oEx->getString()));
        oxRegistry::set('oxUtils', $utilsMock);

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock("oxConfig", array("_loadVarsFromDb"));
        $oConfig->expects($this->once())->method('_loadVarsFromDb')->will($this->throwException($oEx));
        $oConfig->setConfigParam('iDebug', -1);

        $oConfig->init();
    }

    /**
     * Testing config parameters getter
     */
    public function testGetConfigParamCheckingDbParam()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertFalse($oConfig->getConfigParam('blEnterNetPrice'));
    }

    public function testGetConfigParamCheckingNotExistingParam()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertNull($oConfig->getConfigParam('xxx'));
    }

    public function testGetConfigParamDefaultValueWhenConfigValueNotFound()
    {
        $oConfig = oxNew('oxConfig');

        $this->assertSame('defaultValue', $oConfig->getConfigParam('nonExisting', 'defaultValue'));
    }

    /**
     * Testing config parameters setter
     */
    public function testSetConfigParamOverridingLocalParam()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('dbType', 'yyy');
        $this->assertEquals('yyy', $oConfig->getConfigParam('dbType'));
    }

    public function testSetConfigParamOverridingCachedParam()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('xxx', 'yyy');
        $this->assertEquals('yyy', $oConfig->getConfigParam('xxx'));
    }

    /**
     * Testing config cache setter
     */
    public function testSetGlobalParameter()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setGlobalParameter('xxx', 'yyy');
        $this->assertEquals('yyy', $oConfig->getGlobalParameter('xxx'));
    }

    /**
     * Testing config cache getter
     */
    public function testGetGlobalParameterNoParameter()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertNull($oConfig->getGlobalParameter('xxx'));
    }


    /**
     * Testing active view getter
     */
    public function testGetActiveView_NoViewSetYet()
    {
        $oConfig = oxNew('oxConfig');

        $this->assertTrue($oConfig->getActiveView() instanceof oxubase);
    }

    public function testSetGetActiveView()
    {
        $oConfig = oxNew('oxConfig');

        $oView = new stdClass();
        $oConfig->setActiveView($oView);

        $this->assertEquals($oView, $oConfig->getActiveView());
    }

    public function testGetTopActiveView()
    {
        $oConfig = oxNew('oxConfig');

        $oView1 = new stdClass();
        $oView1->sTestItem = "testValue1";

        $oView2 = new stdClass();
        $oView2->sTestItem = "testValue2";

        $oConfig->setActiveView($oView1);
        $oConfig->setActiveView($oView2);

        $this->assertEquals($oView2, $oConfig->getActiveView());
        $this->assertEquals($oView1, $oConfig->getTopActiveView());
    }

    public function testDropLastActiveView()
    {
        $oConfig = oxNew('oxConfig');

        $oView1 = new stdClass();
        $oView1->sTestItem = "testValue1";

        $oView2 = new stdClass();
        $oView2->sTestItem = "testValue2";

        $oConfig->setActiveView($oView1);
        $oConfig->setActiveView($oView2);

        $this->assertEquals($oView2, $oConfig->getActiveView());

        $oConfig->dropLastActiveView();
        $this->assertEquals($oView1, $oConfig->getActiveView());
    }

    public function testHasActiveViewsChain()
    {
        $oConfig = oxNew('oxConfig');

        $oView1 = new stdClass();
        $oView1->sTestItem = "testValue1";

        $oView2 = new stdClass();
        $oView2->sTestItem = "testValue2";

        $oConfig->setActiveView($oView1);
        $this->assertFalse($oConfig->hasActiveViewsChain());

        $oConfig->setActiveView($oView2);
        $this->assertTrue($oConfig->hasActiveViewsChain());
    }

    public function testHasActiveViewsChain_noViews()
    {
        $oConfig = oxNew('oxConfig');

        $this->assertFalse($oConfig->hasActiveViewsChain());
    }

    public function testGetActiveViewsList()
    {
        $oConfig = oxNew('oxConfig');

        $oView1 = new stdClass();
        $oView1->sTestItem = "testValue1";

        $oView2 = new stdClass();
        $oView2->sTestItem = "testValue2";

        $oConfig->setActiveView($oView1);
        $oConfig->setActiveView($oView2);

        $this->assertEquals(array($oView1, $oView2), $oConfig->getActiveViewsList());
    }

    public function testGetActiveViewsNames()
    {
        $oConfig = oxNew('oxConfig');

        $oView1 = $this->getMock("oxView", array("getClassName"));
        $oView1->expects($this->once())->method('getClassName')->will($this->returnValue("testViewName1"));

        $oView2 = $this->getMock("oxView", array("getClassName"));
        $oView2->expects($this->once())->method('getClassName')->will($this->returnValue("testViewName2"));

        $oConfig->setActiveView($oView1);
        $oConfig->setActiveView($oView2);

        $this->assertEquals(array("testViewName1", "testViewName2"), $oConfig->getActiveViewsNames());
    }

    /**
     * Testing base shop id getter
     */
    public function testGetBaseShopId()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $this->assertEquals(ShopIdCalculator::BASE_SHOP_ID, $oConfig->getBaseShopId());
    }

    /**
     * Testing mall mode getter
     */
    public function testIsMall()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertFalse($oConfig->isMall());
    }

    /**
     * Testing productive mode check
     */
    public function testIsProductiveModeForEnterpise()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sQ = 'select oxproductive from oxshops where oxid = "' . $oConfig->getShopID() . '"';
        $blProductive = ( bool ) oxDb::getDb()->getOne($sQ);

        $this->assertEquals($blProductive, $oConfig->isProductiveMode());
    }

    /**
     * Testing config info loader method
     * (no need to test all ...)
     */
    // testing random boolean parameter
    public function testLoadVarsFromDbRandomBool()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $sQ = 'select oxvarname from oxconfig where oxvartype="bool" and oxshopid="' . $sShopId . '" and oxmodule="" order by rand()';
        $sVar = oxDb::getDb()->getOne($sQ);

        $sQ = 'select DECODE( oxvarvalue, "' . $oConfig->getConfigParam('sConfigKey') . '") from oxconfig where oxshopid="' . $sShopId . '" and oxvarname="' . $sVar . '" and oxmodule=""';
        $sVal = oxDb::getDb()->getOne($sQ);

        $oConfig->UNITloadVarsFromDB($sShopId, array($sVar));

        $this->assertEquals(($sVal == 'true' || $sVal == '1'), $oConfig->getConfigParam($sVar));
    }

    // testing random array parameter
    public function testLoadVarsFromDbRandomArray()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $sQ = 'select oxvarname from oxconfig where oxvartype="arr" and oxshopid="' . $sShopId . '"  and oxmodule="" order by rand()';
        $sVar = oxDb::getDb()->getOne($sQ);

        $sQ = 'select DECODE( oxvarvalue, "' . $oConfig->getConfigParam('sConfigKey') . '") from oxconfig where oxshopid="' . $sShopId . '" and oxvarname="' . $sVar . '" and oxmodule=""';
        $sVal = oxDb::getDb()->getOne($sQ);

        $oConfig->UNITloadVarsFromDB($sShopId, array($sVar));

        $this->assertEquals(unserialize($sVal), $oConfig->getConfigParam($sVar));
    }

    // testing random no bool, array/assoc array parameter
    public function testLoadVarsFromDbAnyOther()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $sQ = 'select oxvarname from oxconfig where (oxmodule="" or oxmodule="theme:azure") and oxvartype not in ( "bool", "arr", "aarr" )  and oxshopid="' . $sShopId . '"  and oxmodule="" order by rand()';
        $sVar = oxDb::getDb()->getOne($sQ);

        $sQ = 'select DECODE( oxvarvalue, "' . $oConfig->getConfigParam('sConfigKey') . '") from oxconfig where oxshopid="' . $sShopId . '" and oxvarname="' . $sVar . '" and oxmodule=""';
        $sVal = oxDb::getDb()->getOne($sQ);

        $oConfig->UNITloadVarsFromDB($sShopId, array($sVar));

        $this->assertEquals($sVal, $oConfig->getConfigParam($sVar));
    }

    // not existing variable
    public function testLoadVarsFromDbNotExisting()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $oConfig->UNITloadVarsFromDB($sShopId, array(time()));

        $this->assertNull($oConfig->getConfigParam('nonExistingParameter'));
    }

    public function testSetConfVarFromDb()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array("setConfigParam"));
        $oConfig->expects($this->at(0))->method('setConfigParam')
            ->with(
                $this->equalTo("test1"),
                $this->equalTo("t1")
            );
        $oConfig->expects($this->at(1))->method('setConfigParam')
            ->with(
                $this->equalTo("test2"),
                $this->equalTo(array('x'))
            );
        $oConfig->expects($this->at(2))->method('setConfigParam')
            ->with(
                $this->equalTo("test3"),
                $this->equalTo(array('x' => 'y'))
            );
        $oConfig->expects($this->at(3))->method('setConfigParam')
            ->with(
                $this->equalTo("test4"),
                $this->equalTo(true)
            );
        $oConfig->expects($this->at(4))->method('setConfigParam')
            ->with(
                $this->equalTo("test5"),
                $this->equalTo(false)
            );

        $oConfig->_setConfVarFromDb('test1', 'blabla', 't1');
        $oConfig->_setConfVarFromDb('test2', 'arr', serialize(array('x')));
        $oConfig->_setConfVarFromDb('test3', 'aarr', serialize(array('x' => 'y')));
        $oConfig->_setConfVarFromDb('test4', 'bool', 'true');
        $oConfig->_setConfVarFromDb('test5', 'bool', '0');
    }

    /**
     * testing close page
     */
    public function testPageClose()
    {
        $oStart = $this->getMock('oxStart', array('pageClose'));
        $oStart->expects($this->once())->method('pageClose');

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam("_oStart", $oStart);

        $oConfig->pageClose();
    }

    /**
     * testing shops configuration param getter
     */
    public function testgetShopConfVar()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getShopId();
        $sConfKey = $oConfig->getConfigParam('sConfigKey');
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $aVars = array("theme:basic#iNewBasketItemMessage",
                       "theme:azure#iNewBasketItemMessage",

                       "theme:basic#iTopNaviCatCount",
                       "theme:azure#iTopNaviCatCount",

                       "#sCatThumbnailsize",
                       "theme:basic#sCatThumbnailsize",
                       "theme:azure#sCatThumbnailsize",

                       "#sThumbnailsize",
                       "theme:basic#sThumbnailsize",
                       "theme:azure#sThumbnailsize",

                       "#sZoomImageSize",
                       "theme:basic#sZoomImageSize",
                       "theme:azure#sZoomImageSize");
        foreach ($aVars as $sData) {

            $aData = explode("#", $sData);
            $sModule = $aData[0] ? $aData[0] : oxConfig::OXMODULE_THEME_PREFIX . $oConfig->getConfigParam('sTheme');
            $sVar = $aData[1];

            $sQ = "select DECODE( oxvarvalue, '{$sConfKey}') from oxconfig where oxshopid='{$sShopId}' and oxmodule = '{$sModule}' and  oxvarname='{$sVar}'";
            $this->assertEquals($oDb->getOne($sQ), $oConfig->getShopConfVar($sVar, $sShopId, $sModule), "\nshop:{$sShopId}; {$sModule}; var:{$sVar}\n");
        }
    }

    public function testgetShopConfVarCheckingDbParamWhenMoreThan1InDB()
    {
        $this->cleanUpTable('oxconfig');
        $oConfig = oxNew('oxConfig');
        $sShopId = $oConfig->getBaseShopId();

        $sQ1 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values
                                    ('_test1', '$sShopId', 'testVar1', 'int', 0x071d6980dc7afb6707bb)";
        $sQ2 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values
                                    ('_test2', '$sShopId', 'testVar1', 'int', 0x071d6980dc7afb6707bb)";

        oxDb::getDb()->execute($sQ1);
        oxDb::getDb()->execute($sQ2);

        $oConfig = oxNew('oxConfig');
        $this->assertFalse($oConfig->getShopConfVar('testVar1') == null);

    }


    /**
     * Testing if shop var saver writes correct info into db
     */
    public function testsaveShopConfVar()
    {
        $sName = 'xxx';
        $sVal = '123';

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getShopId();
        $oConfig->saveShopConfVar('int', $sName, $sVal, $sShopId);
        $this->assertEquals($sVal, $oConfig->getShopConfVar($sName, $sShopId));
        $this->assertEquals($sVal, $oConfig->getConfigParam($sName));
    }

    /**
     * Testing if shop var saver writes correct info into db
     */
    public function testsaveShopConfVarSerialized()
    {
        $sVar = 'array';
        $aVal = array('a', 'b', 'c');

        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('arr', $sVar, $aVal);
            $this->assertEquals($aVal, $oConfig->getShopConfVar($sVar));
            $this->assertEquals($aVal, $oConfig->getConfigParam($sVar));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='array'");
        if ($oE) {
            throw $oE;
        }
    }

    public function testSaveModuleConfVar()
    {
        oxDb::getDb()->execute('delete from oxconfig where oxvarname="oxtesting"');
        $this->assertFalse(oxDb::getDb()->getOne('select oxvarvalue from oxconfig where oxvarname="oxtesting"'));

        $config = $this->getConfig();

        $config->saveShopConfVar('string', 'oxtesting', 'test', null, '');
        $config->saveShopConfVar('string', 'oxtesting', 'test', null, 'theme:basic');

        $this->assertEquals('test', $config->getConfigParam('oxtesting'));

        oxDb::getDb()->execute('delete from oxconfig where oxmodule="theme:basic" and oxvarname="oxtesting"');
        $this->getConfig()->saveShopConfVar('string', 'oxtesting', 'test', null, 'theme:basic');

        $this->assertEquals('test', $this->getConfig()->getConfigParam('oxtesting'));

        oxDb::getDb()->execute('delete from oxconfig where oxvarname="oxtesting"');
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolTrue1()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('bool', "testVar", 1);
            $this->assertTrue($oConfig->getShopConfVar("testVar"));
            $this->assertTrue($oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolTrue2()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('bool', "testVar", true);
            $this->assertTrue($oConfig->getShopConfVar("testVar"));
            $this->assertTrue($oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolTrue3()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('bool', "testVar", "true");
            $this->assertTrue($oConfig->getShopConfVar("testVar"));
            $this->assertTrue($oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolFalse1()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('bool', "testVar", false);
            $this->assertFalse($oConfig->getShopConfVar("testVar"));
            $this->assertFalse($oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolFalse2()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('bool', "testVar", 0);
            $this->assertFalse($oConfig->getShopConfVar("testVar"));
            $this->assertFalse($oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolFalse3()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('bool', "testVar", "false");
            $this->assertFalse($oConfig->getShopConfVar("testVar"));
            $this->assertFalse($oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes num value with valid string to config correctly
     */
    public function testsaveShopConfVarNumValidString()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('num', "testVar", "10.000,5989");
            $this->assertEquals(10000.5989, $oConfig->getShopConfVar("testVar"));
            $this->assertEquals(10000.5989, $oConfig->getConfigParam("testVar"));
            $oConfig->saveShopConfVar('num', "testVar", "20,000.5989");
            $this->assertEquals(20000.5989, $oConfig->getShopConfVar("testVar"));
            $this->assertEquals(20000.5989, $oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes num value with invalid string to config correctly
     */
    public function testsaveShopConfVarNumInvalidString()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('num', "testVar", "abc");
            $this->assertEquals(0, $oConfig->getShopConfVar("testVar"));
            $this->assertEquals(0, $oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing if shop var saver writes num value with float to config correctly
     */
    public function testsaveShopConfVarNumFloat()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $oE = null;
        try {
            $oConfig->saveShopConfVar('num', "testVar", 50.009);
            $this->assertEquals(50.009, $oConfig->getShopConfVar("testVar"));
            $this->assertEquals(50.009, $oConfig->getConfigParam("testVar"));
        } catch (Exception $oE) {
            // rethrow later
        }
        oxDb::getDb()->execute("delete from oxconfig where oxvarname='testVar'");
        if ($oE) {
            throw $oE;
        }
    }

    /**
     * Testing currency array getter
     */
    public function testGetCurrencyArray()
    {
        // preparing fixture
        $oEur = new stdClass();
        $oEur->id = 0;
        $oEur->name = 'EUR';
        $oEur->rate = '1.00';
        $oEur->dec = ',';
        $oEur->thousand = '.';
        $oEur->sign = '€';
        $oEur->decimal = '2';
        $oEur->selected = 1;

        $oGbp = clone $oEur;
        $oGbp->id = 1;
        $oGbp->name = 'GBP';
        $oGbp->rate = '0.8565';
        $oGbp->dec = '.';
        $oGbp->thousand = '';
        $oGbp->sign = '£';
        $oGbp->decimal = '2';
        $oGbp->selected = 0;

        $oChf = clone $oEur;
        $oChf->id = 2;
        $oChf->name = 'CHF';
        $oChf->rate = '1.4326';
        $oChf->dec = ',';
        $oChf->thousand = '.';
        $oChf->sign = '<small>CHF</small>';
        $oChf->decimal = '2';
        $oChf->selected = 0;

        $oUsd = clone $oEur;
        $oUsd->id = 3;
        $oUsd->name = 'USD';
        $oUsd->rate = '1.2994';
        $oUsd->dec = '.';
        $oUsd->thousand = '';
        $oUsd->sign = '$';
        $oUsd->decimal = '2';
        $oUsd->selected = 0;
        $aCurrArray = array($oEur, $oGbp, $oChf, $oUsd);

        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $this->assertEquals($aCurrArray, $oConfig->getCurrencyArray(0));
    }

    /**
     * Testing currency getter
     */
    public function testGetCurrencyObjectNotExisting()
    {
        $oConfig = oxNew('oxConfig');
        $this->assertNull($oConfig->getCurrencyObject('xxx'));
    }

    public function testGetCurrencyObjectExisting()
    {
        // preparing fixture
        $oEur = new stdClass();
        $oEur->id = 0;
        $oEur->name = 'EUR';
        $oEur->rate = '1.00';
        $oEur->dec = ',';
        $oEur->thousand = '.';
        $oEur->sign = '€';
        $oEur->decimal = '2';
        $oEur->selected = 0;

        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $this->assertEquals($oEur, $oConfig->getCurrencyObject($oEur->name));
    }

    /**
     * Testing active shop getter if it returns same object + if serial is set while loading shop
     */
    public function testGetActiveShop()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        // comparing serials
        $oShop = $oConfig->getActiveShop();

        // additionally checking caching
        $oShop->xxx = 'yyy';
        $this->assertEquals('yyy', $oConfig->getActiveShop()->xxx);

        // checking if different language forces reload
        $iCurrLang = oxRegistry::getLang()->getBaseLanguage();
        oxRegistry::getLang()->resetBaseLanguage();
        $this->setRequestParameter('lang', $iCurrLang + 1);

        $oShop = $oConfig->getActiveShop();
        $this->assertFalse(isset($oShop->xxx));
    }

    /**
     * Testing Mandate Counter (default installation count is 0)
     */
    public function testGetMandateCountOneSubShop()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $this->assertEquals(1, $oConfig->getMandateCount());
    }

    public function testThemeNameExpectsDefault()
    {
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $this->assertEquals('azure', $oConfig->getConfigParam('sTheme'));
    }

    public function testGetResourceUrlExpectsDefault()
    {
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, 'admin', false) . "src/";
        $this->assertEquals($sDir, $oConfig->getResourceUrl('', true));
    }

    public function testGetResourceUrlNonAdminExpectsDefault()
    {
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, null, false) . "src/";
        $this->assertEquals($sDir, $oConfig->getResourceUrl());
    }

    /**
     * Testing current (language check included) template directory getter
     */
    public function testGetTemplateDirNonAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $this->_getViewsPath($oConfig);
        $sDir .= 'tpl/';

        $this->assertEquals($sDir, $oConfig->getTemplateDir());
    }

    public function testGetTemplateDirExpectsDefault()
    {
        oxRegistry::getLang()->setBaseLanguage(999);
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $this->_getViewsPath($oConfig, 'admin') . 'tpl/';
        $this->assertEquals($sDir, $oConfig->getTemplateDir(true));
    }

    /**
     * Testing templates URL getter
     */
    public function testGetTemplateUrlNonAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getViewsPath($oConfig, null, false);
        $sDir .= 'tpl/';

        $this->assertEquals($sDir, $oConfig->getTemplateUrl());
    }

    public function testGetTemplateUrlExpectsDefault()
    {
        $oConfig = oxNew('oxConfig');
        oxRegistry::getLang()->setBaseLanguage(999);
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getViewsPath($oConfig, 'admin', false) . 'tpl/';
        $this->assertEquals($sDir, $oConfig->getTemplateUrl(null, true));
    }


    /**
     * Testing base template directory getter
     */
    public function testGetResourceUrlNonAdminNonSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('isSsl'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, null, false) . 'src/';
        $this->assertEquals($sDir, $oConfig->getResourceUrl());
    }

    public function testGetResourceUrlAdminSsl()
    {
        $oConfig = $this->getConfigWithSslMocked();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sSSLShopURL') . 'out/admin/src/';
        $this->assertEquals($sDir, $oConfig->getResourceUrl(null, true));
    }

    /**
     * Testing template file location getter
     */
    public function testGetTemplatePathNonAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $this->_getViewsPath($oConfig) . 'tpl/page/shop/start.tpl';

        $this->assertEquals($sDir, $oConfig->getTemplatePath('page/shop/start.tpl', false));
    }

    public function testGetTemplatePathAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $this->_getViewsPath($oConfig, 'admin') . 'tpl/start.tpl';
        $this->assertEquals($sDir, $oConfig->getTemplatePath('start.tpl', true));
    }

    /**
     * Test if correct template path is returned if template found in module template configurations
     */
    public function testGetModuleTemplatePath()
    {
        $expected = 'moduleTemplatePath';

        // tell module template calculator to give good result
        $moduleTemplateCalculatorStub = $this->getMock(ModuleTemplatePathCalculator::class, ['calculateModuleTemplatePath']);
        $moduleTemplateCalculatorStub->method('calculateModuleTemplatePath')->willReturn($expected);

        // imitate config with empty getDir response
        $configStub = $this->getMock(oxConfig::class, ['getDir', 'getModuleTemplatePathCalculator']);
        $configStub->method('getModuleTemplatePathCalculator')->willReturn($moduleTemplateCalculatorStub);

        // test if returns correct template
        $actual = $configStub->getTemplatePath('someTemplateName.tpl', true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test if correct template path is returned if template is Not found in module template configurations
     */
    public function testGetModuleTemplatePathCalculatorException()
    {
        $expected = '';

        // tell module template calculator to give good result
        $moduleTemplateCalculatorStub = $this->getMock(ModuleTemplatePathCalculator::class, ['calculateModuleTemplatePath']);
        $moduleTemplateCalculatorStub->method('calculateModuleTemplatePath')->willThrowException(oxNew('oxException', 'Some calculator exception'));

        // imitate config with empty getDir response
        $configStub = $this->getMock(oxConfig::class, ['getDir', 'getModuleTemplatePathCalculator']);
        $configStub->method('getModuleTemplatePathCalculator')->willReturn($moduleTemplateCalculatorStub);

        // test if returns correct template
        $actual = $configStub->getTemplatePath('someTemplateName.tpl', true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Testing getAbsDynImageDir getter
     */
    public function testGetTranslationsDir()
    {
        $oConfig = oxNew('oxConfig');
        $sDir = $this->getConfigParam('sShopDir') . 'Application/translations/en/lang.php';
        $this->assertEquals($sDir, $oConfig->getTranslationsDir('lang.php', 'en'));
        $this->assertFalse($oConfig->getTranslationsDir('lang.php', 'na'));
    }

    /**
     * Testing getAbsDynImageDir getter
     */
    public function testGetAbsDynImageDirForCustomShop()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/pictures/';

        $this->assertEquals($sDir, $oConfig->getPictureDir(false));
    }

    public function testGetAbsDynImageDirForSecondLang()
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('blUseDifferentDynDirs', true);

        $sDir = $oConfig->getConfigParam('sShopDir') . "out/pictures/";

        $this->assertEquals($sDir, $oConfig->getPictureDir(false));
    }


    /**
     * Testing getImageDir getter
     */
    public function testGetImageDir()
    {
        $oConfig = oxNew('oxConfig');

        $oConfig->init();

        $sDir = $this->_getOutPath($oConfig) . 'img/';
        $this->assertEquals($sDir, $oConfig->getImageDir());
    }

    /**
     * Testing getImageDir getter
     */
    public function testGetImageDirMultiLangDirsExist()
    {
        $oConfig = oxNew('oxConfig');

        oxRegistry::getLang()->setTplLanguage(4);

        $oConfig->init();
        $sLangDir = $this->_getOutPath($oConfig) . 'img/';
        $sNoLangDir = $this->_getOutPath($oConfig) . 'img/';

        try {
            $this->assertEquals($sLangDir, $oConfig->getImageDir());
            $this->assertEquals($sNoLangDir, $oConfig->getImageDir());

        } catch (Exception $e) {
        }

        oxRegistry::getLang()->setTplLanguage();
        /*
        if (is_dir(realpath($sLangDir))) {
            rmdir($sLangDir);
        }*/
        $sD = $this->_getOutPath($oConfig) . '/4';
        if (is_dir(realpath($sD))) {
            rmdir($sD);
        }

        if ($e) {
            throw $e;
        }
    }


    /**
     * Testing getAbsAdminImageDir getter
     */
    public function testGetAbsAdminGetImageDirDefault()
    {
        oxRegistry::getLang()->setBaseLanguage(999);
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/admin/img/';
        $this->assertEquals($sDir, $oConfig->getImageDir(1));
    }

    public function testGetAbsAdminGetImageDirForActLang()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/admin/img/';
        $this->assertEquals($sDir, $oConfig->getImageDir(1));
    }


    /**
     * Testing getCoreUtilsUrl getter
     */
    public function testGetCoreUtilsUrl()
    {
        $oConfig = $this->getMock('oxConfig', array('getCurrentShopUrl'));
        $oConfig->expects($this->any())->method('getCurrentShopUrl')->will($this->returnValue('xxx/'));
        $this->assertEquals('xxx/Core/utils/', $oConfig->getCoreUtilsUrl());
    }

    public function testGetCoreUtilsUrlMall()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', 'http://www.example3.com/');
        $this->assertEquals('http://www.example3.com/Core/utils/', $oConfig->getCoreUtilsUrl());
    }


    /**
     * Testing getCurrentShopURL getter
     */
    public function testGetCurrentShopUrlNoSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('isSsl'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();
        $this->assertEquals($oConfig->getShopUrl(), $oConfig->getCurrentShopUrl());
    }

    public function testGetCurrentShopUrlIsSsl()
    {
        $oConfig = $this->getConfigWithSslMocked();
        $oConfig->init();
        $this->assertEquals($oConfig->getSslShopUrl(), $oConfig->getCurrentShopUrl());
    }

    /**
     * Testing active currency id getter
     */
    public function testGetShopCurrency()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        // simple check if nothing was changed ...
        $this->assertEquals((int) $oConfig->getRequestParameter('currency'), $oConfig->getShopCurrency());
    }


    /**
     * Testing active shop currenty setter
     */
    public function testSetActShopCurrencySettingExisting()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals(0, $oConfig->getShopCurrency());
        $oConfig->setActShopCurrency(1);
        $this->assertEquals(1, $this->getSession()->getVariable('currency'));
    }

    public function testSetActShopCurrencySettingNotExisting()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals(0, $oConfig->getShopCurrency());
        $oConfig->setActShopCurrency('xxx');
        $this->assertEquals(0, $oConfig->getShopCurrency());
    }

    /**
     * Testing getImageDir getter
     */
    public function testGetImageDirNativeImagesIsSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('isAdmin', 'isSsl'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('blNativeImages', true);

        $sUrl = $oConfig->getConfigParam('sSSLShopURL') ? $oConfig->getConfigParam('sSSLShopURL') : $oConfig->getConfigParam('sShopURL');
        $sUrl .= $this->_getOutPath($oConfig, null, false) . 'img/';
        $this->assertEquals($sUrl, $oConfig->getImageUrl());
    }

    public function testGetImageDirDefaultLanguage()
    {
        oxRegistry::getLang()->setBaseLanguage(999);
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();

        $sUrl = $oConfig->getConfigParam('sShopURL');
        $sUrl .= $this->_getOutPath($oConfig, null, false) . 'img/';

        $this->assertEquals($sUrl, $oConfig->getImageUrl());
    }

    /**
     * Testing getImagePath getter
     */
    public function testGetImagePath()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sUrl = $oConfig->getOutDir();
        $this->assertEquals($sUrl . "admin/img/start.gif", $oConfig->getImagePath("start.gif", true));
    }

    /**
     * Testing getNoSslImageDir getter
     */
    public function testGetNoSslgetImageUrlAdminModeSecondLanguage()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . 'out/admin/img/';
        $this->assertEquals($sDir, $oConfig->getImageUrl(true));
    }

    public function testGetNoSslgetImageUrlDefaults()
    {
        $this->getConfig()->setConfigParam('aLanguages', array(0 => 'DE', 1 => 'EN', 2 => 'LT'));
        oxRegistry::getLang()->setBaseLanguage(2);

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, null, false) . 'img/';

        $this->assertEquals($sDir, $oConfig->getImageUrl());
    }

    /**
     * Testing getShopHomeUrl getter
     */
    public function testGetShopHomeUrl()
    {
        $sUrl = $this->shopUrl . 'index.php?';

        $oConfig = oxNew('oxConfig');
        $oConfig->setConfigParam('sShopURL', $this->shopUrl);
        $oConfig->init();

        $this->setToRegistryOxUtilsUrlMock('index.php');

        $this->assertEquals($sUrl, $oConfig->getShopHomeUrl());
    }

    /**
     * Testing getShopSecureHomeUrl getter
     */
    public function testGetShopSecureHomeUrl()
    {
        $sUrl = $this->shopUrl . 'index.php?';

        $oConfig = oxNew('oxConfig');
        $oConfig->setConfigParam('sShopURL', $this->shopUrl);
        $oConfig->init();

        $this->setToRegistryOxUtilsUrlMock('index.php');

        $this->assertEquals($sUrl, $oConfig->getShopSecureHomeUrl());
    }


    /**
     * Testing getSslShopUrl getter
     */
    public function testGetSslShopUrlLanguageUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $aLanguageSslUrls = array(5 => 'https://www.example.com/');
        $oConfig->setConfigParam('aLanguageSSLURLs', $aLanguageSslUrls);
        $this->assertEquals($aLanguageSslUrls[5], $oConfig->getSslShopUrl(5));
    }

    public function testGetSslShopUrlByLanguageArrayAddsEndingSlash()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', array(5 => 'http://www.example.com'));
        $this->assertEquals('http://www.example.com/', $oConfig->getSslShopUrl(5));
    }

    public function testGetSslShopUrlMallSslUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', 'https://www.example2.com/');
        $this->assertEquals('https://www.example2.com/', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlMallSslUrlAddsEndingSlash()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', 'https://www.example2.com');
        $this->assertEquals('https://www.example2.com/', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlMallUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', 'https://www.example3.com/');
        $this->assertEquals('https://www.example3.com/', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlSslUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', null);
        $oConfig->setConfigParam('sSSLShopURL', 'https://www.example4.com');
        $this->assertEquals('https://www.example4.com', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlDefaultUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', null);
        $oConfig->setConfigParam('sSSLShopURL', null);
        $this->assertEquals($oConfig->getConfigParam('sShopURL'), $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlConfigUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('sSSLShopURL', 'https://www.example4.com');
        $this->assertEquals('https://www.example4.com', $oConfig->getSslShopUrl());
    }

    /**
     * Testing getActShopCurrencyObject getter
     */
    public function testGetActShopCurrencyObjectCurrent()
    {
        $oGbp = new stdClass();
        $oGbp->id = 1;
        $oGbp->name = 'GBP';
        $oGbp->rate = '0.8565';
        $oGbp->dec = '.';
        $oGbp->thousand = '';
        $oGbp->sign = '£';
        $oGbp->decimal = '2';
        $oGbp->selected = 0;

        $this->setRequestParameter('cur', 1);
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals($oGbp, $oConfig->getActShopCurrencyObject());

    }

    public function testGetActShopCurrencyObjectDefauls()
    {
        // preparing fixture
        $oEur = new stdClass();
        $oEur->id = 0;
        $oEur->name = 'EUR';
        $oEur->rate = '1.00';
        $oEur->dec = ',';
        $oEur->thousand = '.';
        $oEur->sign = '€';
        $oEur->decimal = '2';
        $oEur->selected = 0;

        $this->setRequestParameter('cur', 999);
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals($oEur, $oConfig->getActShopCurrencyObject());
    }


    /**
     * Testing getShopCurrentUrl getter
     */
    public function testGetShopCurrentUrlIsSsl()
    {
        $oConfig = $this->getConfigWithSslMocked();
        $oConfig->init();
        $oConfig->setConfigParam('sSSLShopURL', 'https://www.example.com/');
        $this->assertEquals(0, strpos($oConfig->getShopCurrentUrl(), 'https://www.example.com/index.php?'));
    }

    public function testGetShopCurrentUrlNoSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('isSsl'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('sShopURL', 'http://www.example.com/');
        $this->assertEquals(0, strpos($oConfig->getShopCurrentUrl(), 'http://www.example.com/index.php?'));
    }


    /**
     * Testing getShopUrl getter
     */
    public function testGetShopUrlIsAdmin()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopURL'), $oConfig->getShopUrl());
    }

    public function testGetShopUrlByLanguageArray()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageURLs', array(5 => 'http://www.example.com/'));
        $this->assertEquals('http://www.example.com/', $oConfig->getShopUrl(5));
    }

    public function testGetShopUrlByLanguageArrayAddsEndingSlash()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageURLs', array(5 => 'http://www.example.com'));
        $this->assertEquals('http://www.example.com/', $oConfig->getShopUrl(5));
    }

    public function testGetShopUrlByMallUrl()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();

        $oConfig->setConfigParam('aLanguageURLs', null);
        $oConfig->setConfigParam('sMallShopURL', 'http://www.example2.com/');
        $this->assertEquals('http://www.example2.com/', $oConfig->getShopUrl());
    }

    public function testGetShopUrlByMallUrlAddsEndingSlash()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();

        $oConfig->setConfigParam('aLanguageURLs', null);
        $oConfig->setConfigParam('sMallShopURL', 'http://www.example2.com');
        $this->assertEquals('http://www.example2.com/', $oConfig->getShopUrl());
    }

    public function testGetShopUrlDefaultUrl()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageURLs', null);
        $oConfig->setConfigParam('sMallShopURL', null);
        $this->assertEquals($oConfig->getConfigParam('sShopURL'), $oConfig->getShopUrl());
    }

    /**
     * Testing getShopLanguage getter
     */
    // testing if all request given parameters are used
    /* P
    public function testGetShopLanguageTestingRequest()
    {
        $this->setRequestParameter( 'changelang', 1 );
        $oConfig = $this->getMock( 'oxConfig', array( 'isAdmin' ) );
        $oConfig->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oConfig->init();
        //$oConfig->setNonPublicVar( '_iLanguageId', null );
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        $this->setRequestParameter( 'changelang', null );
        $this->setRequestParameter( 'lang', 1 );
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        $this->setRequestParameter( 'changelang', null );
        $this->setRequestParameter( 'lang',       null );
        $this->setRequestParameter( 'tpllanguage', 1 );
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        $this->setRequestParameter( 'changelang',  null );
        $this->setRequestParameter( 'lang',        null );
        $this->setRequestParameter( 'tpllanguage', null );
        $this->setRequestParameter( 'language', 1 );
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        $this->setRequestParameter( 'changelang',  null );
        $this->setRequestParameter( 'lang',        null );
        $this->setRequestParameter( 'tpllanguage', null );
        $this->setRequestParameter( 'language',    null );
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam( 'sDefaultLang', 1 );
        $this->assertEquals( 1, $oConfig->getShopLanguage() );
    }
    // testing if bad language id is fixed
    public function testGetShopLanguagePassingNotExistingShouldBeFixed()
    {
        $this->setRequestParameter( 'changelang', 'xxx' );
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals( 0, $oConfig->getShopLanguage() );
    }
    // testing if url configuration sets language
    public function testGetShopLanguageLanguageURLs()
    {
        $oConfig = $this->getMock( 'modFortestGetShopLanguageLanguageURLs', array( 'isAdmin' ) );
        $oConfig->expects( $this->any() )->method( 'isAdmin' )->will( $this->returnValue( false ) );
        $oConfig->init();
        $oConfig->setConfigParam( 'aLanguageURLs', array( 1 => 'xxx' ) );

        $this->assertEquals( 1, $oConfig->getShopLanguage() );
    }
    */

    /**
     * Merger them both - GetShopId and GetActiveShopId
     * In community and professional editions  shop id is always oxbaseshop
     */
    public function testGetShopIdForPeAlwaysOxbaseshop()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals(ShopIdCalculator::BASE_SHOP_ID, $oConfig->getShopId());
    }

    public function testGetUploadedFile()
    {
        $aBack = $_FILES;
        $_FILES['upload'] = 'testValue';

        $this->assertEquals('testValue', $this->getConfig()->getUploadedFile('upload'));

        $_FILES = $aBack;
    }

    public function testGetRequestParameterEscaped()
    {
        $config = oxNew('oxConfig');
        $_POST['testParameterKey'] = '&testValue';

        $this->assertEquals('&amp;testValue', $config->getRequestParameter('testParameterKey'));
    }

    public function testGetRequestParameterRaw()
    {
        $config = oxNew('oxConfig');
        $_POST['testParameterKey'] = '&testValue';

        $this->assertEquals('&testValue', $config->getRequestParameter('testParameterKey', true));
    }

    public function testGetEdition()
    {
        $sShopId = $this->getConfig()->getShopId();
        $sEdition = oxDb::getDb()->getOne("select oxedition from oxshops where oxid = '$sShopId'");
        $this->assertEquals($sEdition, $this->getConfig()->getEdition());
    }

    public function testGetRevision_FileExists()
    {
        $oConfig = oxNew('oxConfig');
        $sFileName = 'pkg.rev';
        $iRevisionNum = 12345;
        $sFilePath = $this->createFile($sFileName, $iRevisionNum);
        $oConfig->setConfigParam('sShopDir', dirname($sFilePath));
        $this->assertEquals($iRevisionNum, $oConfig->getRevision());
        unlink($sFilePath);
    }

    public function testGetRevision_NoFile()
    {
        $oConfig = oxNew('oxConfig');
        $sDir = $this->getConfig()->getConfigParam('sShopDir') . '/out/downloads/';
        $oConfig->setConfigParam('sShopDir', $sDir);
        $this->assertFalse($oConfig->getRevision());
    }

    public function testGetPackageInfo_FileExists()
    {
        $oConfig = oxNew('oxConfig');
        $sFileName = 'pkg.info';
        $sFileContent = 'Inserting test string';
        $sFilePath = $this->createFile($sFileName, $sFileContent);
        $oConfig->setConfigParam('sShopDir', dirname($sFilePath));
        $this->assertEquals($sFileContent, $oConfig->getPackageInfo());
        unlink($sFilePath);
    }

    public function testGetPackageInfo_NoFile()
    {
        $oConfig = oxNew('oxConfig');
        $sDir = $this->getConfig()->getConfigParam('sShopDir') . '/out/downloads/';
        $oConfig->setConfigParam('sShopDir', $sDir);
        $this->assertFalse($oConfig->getPackageInfo());
    }

    public function testGetEditionNotEmpty()
    {
        $this->assertNotEquals('', $this->getConfig()->getEdition());
    }

    public function testGetFullEdition()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community editions only.');
        }

        $sFEdition = $this->getConfig()->getFullEdition();
        $this->assertEquals("Community Edition", $sFEdition);

        $oConfig = $this->getMock('oxConfig', array('getEdition'));
        $oConfig->expects($this->any())->method('getEdition')->will($this->returnValue("Test Edition"));
        $this->assertEquals("Test Edition", $oConfig->getFullEdition());
    }

    public function testGetVersion()
    {
        $sShopId = $this->getConfig()->getShopId();
        $sVer = oxDb::getDb()->getOne("select oxversion from oxshops where oxid = '$sShopId'");
        $this->assertEquals($sVer, $this->getConfig()->getVersion());
    }

    public function testGetVersionNotEmpty()
    {
        $this->assertNotEquals('', $this->getConfig()->getVersion());
    }

    public function testCorrectVersion()
    {
        $this->assertTrue(version_compare($this->getConfig()->getVersion(), '4.9') >= 0);
    }

    public function testGetDir_level5()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/1/de/test1/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test1', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . '1/de/test1/text.txt', $sDir);
    }

    public function testGetDir_delvel4()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/1/test2/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test2', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . '1/test2/text.txt', $sDir);
    }

    public function testGetDir_level3()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/de/test2a/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test2a', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'de/test2a/text.txt', $sDir);
    }

    public function testGetDir_delvel2()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/test3/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test3', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'test3/text.txt', $sDir);
    }

    public function testGetDir_delvel1()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test4', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'text.txt', $sDir);
    }

    public function testGetDir_delvel0()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/de/test5/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . "out/";

        $sDir = $oConfig->getDir('text.txt', 'test5', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'de/test5/text.txt', $sDir);
    }

    public function testGetDirIfEditionTemplateFound()
    {
        $expectedResult = 'someEditionTemplateResponse';

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('getEditionTemplate'));
        $config->expects($this->any())->method('getEditionTemplate')->will($this->returnValue($expectedResult));
        $config->init();

        $realResult = $config->getDir('xxx', 'xxx', false);
        $this->assertEquals($expectedResult, $realResult);
    }

    public function testGetOutDir()
    {
        $oConfig = oxNew('oxConfig');
        $this->assertEquals('out/', $oConfig->getOutDir(false));
        $oConfig->setConfigParam('sShopDir', 'test/');
        $this->assertEquals('test/out/', $oConfig->getOutDir());
    }

    public function testGetOutUrl()
    {
        $oConfig = $this->getMock('oxConfig', array('isAdmin', 'getShopUrl'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->expects($this->any())->method('getShopUrl')->will($this->returnValue('testUrl/'));
        $oConfig->init();
        $this->assertEquals('testUrl/out/', $oConfig->getOutUrl(false, null, true));
    }

    public function testGetOutUrlSsl()
    {
        $oConfig = $this->getMock('oxConfig', array('isSsl', 'getSslShopUrl'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->any())->method('getSslShopUrl')->will($this->returnValue('sslUrl/'));
        $oConfig->init();
        $this->assertEquals('sslUrl/out/', $oConfig->getOutUrl(null, false, true));
    }

    public function testGetOutUrlIsAdminFromParam()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->setConfigParam('sShopURL', 'testUrl/');
        $this->assertEquals('testUrl/out/', $oConfig->getOutUrl(false, true, true));
    }

    public function testGetOutUrlIsSslFromParam()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->setConfigParam('sSSLShopURL', 'testSslUrl/');
        $this->assertEquals('testSslUrl/out/', $oConfig->getOutUrl(true, false, false));
    }

    /**
     * Testing getPicturePath getter
     */
    public function testGetPicturePath()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/pictures/';

        $this->assertEquals($sDir, $oConfig->getPicturePath(null, false));
    }

    /**
     * Testing getPictureUrl getter
     */
    public function testGetPictureUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sMainURL = $oConfig->getConfigParam('sShopURL');
        $sMallURL = 'http://www.example.com/';

        $sDir = 'out/pictures/';

        $oConfig->setConfigParam('sMallShopURL', $sMallURL);

        $oConfig->setConfigParam('blNativeImages', false);
        $this->assertEquals($sMainURL . $sDir, $oConfig->getPictureUrl(null, false));

        $oConfig->setConfigParam('blNativeImages', true);
        $this->assertEquals($sMallURL . $sDir, $oConfig->getPictureUrl(null, false));
    }

    public function testGetPictureUrlForAltImageDirA()
    {
        $sDir = 'http://www.example.com/test.gif';

        $oPH = $this->getMock('oxPictureHandler', array('getAltImageUrl'));
        $oPH->expects($this->once())->method('getAltImageUrl')->will($this->returnValue($sDir));
        oxTestModules::addModuleObject('oxPictureHandler', $oPH);

        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $this->assertEquals($sDir, $oConfig->getPictureUrl("/test.gif", false));
    }

    public function testGetPictureUrlFormerTplSupport()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('blFormerTplSupport', true);
        $this->assertContains('nopic.jpg', $oConfig->getPictureUrl("test.gif", false));
    }

    public function testGetPictureUrlNeverEmptyString()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('blFormerTplSupport', true);
        $this->assertNotEquals('', $oConfig->getPictureUrl("test.gif", false));
        $this->assertContains('master/nopic.jpg', $oConfig->getPictureUrl("test.gif", false));
    }

    public function testgetPictureUrlForBugEntry0001557()
    {
        $myConfig = $this->getConfig();

        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam("sAltImageDir", false);
        $oConfig->setConfigParam("blFormerTplSupport", false);

        $sNoPicUrl = $myConfig->getConfigParam("sShopURL") . 'out/pictures/master/nopic.jpg';

        $this->assertEquals($sNoPicUrl, $oConfig->getPictureUrl("unknown.file", true));
    }

    public function testGetTemplateBase()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals("Application/views/admin/", $oConfig->getTemplateBase(true));
    }

    public function testGetResourcePath()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopDir') . "out/admin/src/main.css", $oConfig->getResourcePath("main.css", true));
    }

    public function testGetResourceDir()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopDir') . "out/admin/src/", $oConfig->getResourceDir(true));
    }

    public function testGetResourceUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sMainURL = $oConfig->getConfigParam('sShopURL');
        $sMallURL = 'http://www.example.com/';

        $sDir = 'out/azure/src/';

        $oConfig->setConfigParam('sMallShopURL', $sMallURL);

        $oConfig->setConfigParam('blNativeImages', false);
        $this->assertEquals($sMainURL . $sDir, $oConfig->getResourceUrl(null, false));

        $oConfig->setConfigParam('blNativeImages', true);
        $this->assertEquals($sMallURL . $sDir, $oConfig->getResourceUrl(null, false));
    }

    public function testIsDemoShop()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('blDemoShop', true);
        $this->assertTrue($oConfig->isDemoShop());
    }

    public function testUtfModeIsSet()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with('iUtfMode')->will($this->returnValue(1));
        $this->assertTrue($oConfig->isUtf(), 'Should be utf mode.');
    }

    public function testUtfModeIsNotSet()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with('iUtfMode')->will($this->returnValue(0));
        $this->assertFalse($oConfig->isUtf(), 'Should not be utf mode.');
    }

    public function testIsThemeOption()
    {
        $oConfig = $this->getProxyClass("oxConfig");
        $oConfig->setNonPublicVar("_aThemeConfigParams", array('param1' => 'theme1', 'param2' => 'theme2'));

        $this->assertTrue($oConfig->isThemeOption('param1'));
        $this->assertFalse($oConfig->isThemeOption('aaa'));
    }

    /**
     * Testing value decoder
     *
     * @return null
     */
    public function testDecodeValue()
    {
        $sString = "test";
        $oObject = new stdClass();
        $blBool = "true";
        $aArray = array(1, 2, 3);
        $aAssocArray = array("a" => 1, "b" => 2, "c" => 3);

        $oConfig = oxNew('oxConfig');
        $this->assertEquals($sString, $oConfig->decodeValue("string", $sString));
        $this->assertEquals($oObject, $oConfig->decodeValue("object", $oObject));
        $this->assertEquals(true, $oConfig->decodeValue("bool", $blBool));
        $this->assertEquals($aArray, $oConfig->decodeValue("arr", serialize($aArray)));
        $this->assertEquals($aAssocArray, $oConfig->decodeValue("aarr", serialize($aAssocArray)));
    }

    /**
     * Test case for oxConfig::getDecodeValueQuery()
     *
     * @return null
     */
    public function testGetDecodeValueQuery()
    {
        $oConfig = oxNew('oxConfig');
        $sQ = " DECODE( oxvarvalue, '" . $oConfig->getConfigParam('sConfigKey') . "') ";
        $this->assertEquals($sQ, $oConfig->getDecodeValueQuery());
    }

    public function testGetShopMainUrl()
    {
        $oConfig = oxNew('oxConfig');

        $sSSLUrl = 'https://shop';
        $sUrl = 'http://shop';

        $oConfig->setConfigParam('sSSLShopURL', $sSSLUrl);
        $oConfig->setConfigParam('sShopURL', $sUrl);


        $oConfig->setIsSsl();
        $this->assertEquals($sUrl, $oConfig->getShopMainUrl());

        $oConfig->setIsSsl(true);
        $this->assertEquals($sSSLUrl, $oConfig->getShopMainUrl());

    }

    /**
     * Checks if config variables loaded from congfig.inc.php file
     * takes higher priority compared to the ones loaded from db.
     * This is a test case for bug #3427
     */
    public function testConfigFilePriority()
    {
        $oConfig = new modForTestInitLoadingPriority();
        $oConfig->init();
        $this->assertNotEquals(33, $oConfig->iDebug);
        $this->assertNotEquals(33, $oConfig->getConfigParam("iDebug"));
    }

    /**
     * oxmodule::getAllModules() test case
     *
     * @return null
     */
    public function testGetAllModules()
    {
        $aModules = array(
            'oxorder' => 'testExt1/module1&testExt2/module1',
            'oxnews'  => 'testExt2/module2'
        );

        $aResult = array(
            'oxorder' => array('testExt1/module1', 'testExt2/module1'),
            'oxnews'  => array('testExt2/module2')
        );

        $oConfig = $this->getMock('oxconfig', array("getConfigParam"));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo("aModules"))->will($this->returnValue($aModules));

        $this->assertEquals($aResult, $oConfig->getAllModules());
    }

    /**
     * oxmodule::parseModuleChains() test case, empty
     *
     * @return null
     */
    public function testParseModuleChainsEmpty()
    {
        $oConfig = $this->getProxyClass('oxconfig');

        $aModules = array();
        $aModulesArray = array();
        $this->assertEquals($aModulesArray, $oConfig->parseModuleChains($aModules));
    }

    /**
     * oxmodule::parseModuleChains() test case, single
     *
     * @return null
     */
    public function testParseModuleChainsSigle()
    {
        $oConfig = $this->getProxyClass('oxconfig');
        $aModules = array('oxtest' => 'test/mytest');
        $aModulesArray = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aModulesArray, $oConfig->parseModuleChains($aModules));
    }

    /**
     * oxmodule::parseModuleChains() test case
     *
     * @return null
     */
    public function testParseModuleChains()
    {
        $oConfig = $this->getProxyClass('oxConfig');
        $aModules = array('oxtest' => 'test/mytest&test1/mytest1');
        $aModulesArray = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $this->assertEquals($aModulesArray, $oConfig->parseModuleChains($aModules));
    }

    /**
     * Tests that custom config is being set and variables from it are reachable
     *
     */
    public function testLoadCustomConfig()
    {
        $this->createFile('config.inc.php', '<?php $this->testVar = "testValue";');
        $file = $this->createFile('cust_config.inc.php', '<?php $this->customVar = "customValue";');
        $this->setConfigParam('sShopDir', dirname($file));

        /** @var oxConfig $config */
        $config = $this->getMock('oxConfig', array('init'));
        $config->_loadVarsFromFile();

        $this->assertSame("customValue", $config->getConfigParam("customVar"));
    }

    /**
     * Testing config init - loading config vars returns no result
     */
    public function testInit_noValuesFromConfig()
    {
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $utilsMock */
        $utilsMock = $this->getMock('oxUtils', array('showMessageAndExit'));
        $utilsMock->expects($this->once())->method('showMessageAndExit');
        oxRegistry::set('oxUtils', $utilsMock);

        /** @var oxconfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock("oxConfig", array("_loadVarsFromDb"));
        $oConfig->expects($this->once())->method('_loadVarsFromDb')->will($this->returnValue(false));
        $oConfig->setConfigParam('iDebug', -1);

        $oConfig->init();
    }

    /**
     * Testing config parameters getter
     */
    public function testInit_noShopId()
    {
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $utilsMock */
        $utilsMock = $this->getMock('oxUtils', array('showMessageAndExit'));
        $utilsMock->expects($this->once())->method('showMessageAndExit');
        oxRegistry::set('oxUtils', $utilsMock);

        /** @var oxconfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock("oxConfig", array("getShopId"));
        $oConfig->expects($this->once())->method('getShopId')->will($this->returnValue(false));
        $oConfig->setConfigParam('iDebug', -1);

        $oConfig->init();
    }

    /**
     * @dataProvider getSystemConfigurationParameters
     */
    public function testSaveSystemConfigurationParameterInMainShop($sType, $sName, $sValue)
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->saveSystemConfigParameter($sType, $sName, $sValue);

        if ($sType == 'num') {
            $this->assertEquals((float) $sValue, $oConfig->getSystemConfigParameter($sName));
        } else {
            $this->assertEquals($sValue, $oConfig->getSystemConfigParameter($sName));
        }
    }

    /**
     * @dataProvider getSystemConfigurationParameters
     */
    public function testSaveSystemConfigurationParameterInSubShop($sType, $sName, $sValue)
    {
        $this->getConfig()->setShopId(2);

        $oConfig = oxNew('oxConfig');
        $oConfig->saveSystemConfigParameter($sType, $sName, $sValue);

        if ($sType == 'num') {
            $this->assertEquals((float) $sValue, $oConfig->getSystemConfigParameter($sName));
        } else {
            $this->assertEquals($sValue, $oConfig->getSystemConfigParameter($sName));
        }
    }

    /**
     * Mock oxUtilsServer to see that oxConfig::isCurrentUrl return same result.
     */
    public function testIsCurrentUrlIsWrapperForOxUtilsServer()
    {
        $sURLToCheck = 'some url which does not matter as we check against mock';

        /** @var oxUtilsServer|PHPUnit_Framework_MockObject_MockObject $oUtilsServer */
        $oUtilsServer = $this->getMock('oxUtilsServer');
        $oUtilsServer->expects($this->any())->method('isCurrentUrl')->will($this->returnValue(true));
        oxRegistry::set('oxUtilsServer', $oUtilsServer);

        $this->assertTrue($this->getConfig()->isCurrentUrl($sURLToCheck));

        /** @var oxUtilsServer|PHPUnit_Framework_MockObject_MockObject $oUtilsServer */
        $oUtilsServer = $this->getMock('oxUtilsServer');
        $oUtilsServer->expects($this->any())->method('isCurrentUrl')->will($this->returnValue(false));
        oxRegistry::set('oxUtilsServer', $oUtilsServer);

        $this->assertFalse($this->getConfig()->isCurrentUrl($sURLToCheck));
    }

    public function testReinitialize()
    {
        $config = oxNew('oxConfig');
        $config->init();

        oxNew('oxConfig')->saveShopConfVar('string', 'testReinitialize', 'testReinitialize');

        $config->reinitialize();

        $this->assertEquals('testReinitialize', $config->getConfigParam('testReinitialize'));
    }

    /**
     * @return oxConfig|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfigWithSslMocked()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('isSsl', 'getSslShopUrl'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->any())->method('getSslShopUrl')->will($this->returnValue($this->getConfigParam('sSSLShopURL')));
        $oConfig->setConfigParam('sSSLShopURL', 'https://testUrl/');

        return $oConfig;
    }

    /**
     * Data provider for testSaveSystemConfigurationParameter
     */
    public function getSystemConfigurationParameters()
    {
        return array(
            array('arr', 'aPraram', array(1, 3)),
            array('aarr', 'aAParam', array('a' => 1)),
            array('bool', 'blParam', true),
            array('num', 'numNum', 2),
            array('int', 'iNum1', 0),
            array('int', 'iNum2', 4),
        );
    }

    /**
     * @param string $entryPoint
     */
    private function setToRegistryOxUtilsUrlMock($entryPoint)
    {
        $utilsUrl = $this->getMock('oxUtilsUrl');
        $utilsUrl->expects($this->atLeastOnce())
            ->method('processUrl')
            ->with($this->identicalTo($this->shopUrl . $entryPoint, false))
            ->will($this->returnValue($this->shopUrl . $entryPoint . '?'));
        oxRegistry::set('oxUtilsUrl', $utilsUrl);
    }


}
