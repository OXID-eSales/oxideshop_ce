<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxConfig;
use oxDb;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\Theme;
use OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Facts\Facts;
use oxRegistry;
use oxTestModules;
use stdClass;

class modForTestGetBaseTplDirExpectsDefault extends oxConfig
{
    public function init()
    {
        if ($this->_blInit) {
            return;
        }
        $this->_blInit = true;
        $this->loadVarsFromFile();
        $this->setDefaults();
    }

    public function getShopId()
    {
        return 'xxx';
    }
}


class modForTestInitLoadingPriority extends oxConfig
{
    public $iDebug;

    protected function loadVarsFromDb($sShopID, $aOnlyVars = null, $sModule = '')
    {
        $this->setConfVarFromDb("iDebug", "str", 33);

        return true;
    }
}

class ConfigTest extends \OxidTestCase
{
    use ContainerTrait;
    protected $_iCurr = null;
    protected $_aShops = [];
    private $shopUrl = 'http://www.example.com/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->getConfig()->sTheme = false;

        // copying
        $this->_iCurr = $this->getSession()->getVariable('currency');

        $theme = oxNew(Theme::class);
        $theme->load(ACTIVE_THEME);
        $theme->activate();
    }

    protected function tearDown(): void
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        // cleaning up
        $sQ = 'delete from oxconfig where oxvarname = "xxx" ';
        oxDb::getDb()->execute($sQ);

        foreach ($this->_aShops as $oShop) {
            $oShop->delete();
        }
        $this->_aShops = [];

        $sDir = $this->getConfig()->getConfigParam('sShopDir') . "/out/2";
        if (is_dir(realpath($sDir))) {
            \OxidEsales\Eshop\Core\Registry::getUtilsFile()->deleteDir($sDir);
        }
        $sDir = $this->getConfig()->getConfigParam('sShopDir') . "/out/en/tpl";
        if (is_dir(realpath($sDir))) {
            \OxidEsales\Eshop\Core\Registry::getUtilsFile()->deleteDir($sDir);
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
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return null; } else { return array( "HTTP_X_FORWARDED_SERVER" => "sslsites.de" ); } }');

        $oConfig = $this->getMock(Config::class, ['getConfigParam'], [], '', false);
        $oConfig->expects($this->never())->method('getConfigParam');
        $this->assertTrue($oConfig->isSsl());
    }

    /*
     * Testing method when shop is not in ssl mode
     */
    public function testIsSsl_notSslMode()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return null; } else { return array(); } }');

        $oConfig = $this->getMock(Config::class, ['getConfigParam']);
        $oConfig->expects($this->never())->method('getConfigParam');

        $this->assertFalse($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode but no ssl shop links exist
     */
    public function testIsSsl_SslMode_NoSslShopUrl()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $config = $this->getMock(Config::class, ['getConfigParam']);
        $config
            ->method('getConfigParam')
            ->withConsecutive(['sSSLShopURL'], ['sMallSSLShopURL'])
            ->willReturnOnConsecutiveCalls('', '');

        $this->assertFalse($config->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode and ssl shop link exists
     */
    public function testIsSsl_SslMode_WithSslShopUrl()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock(Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('sSSLShopURL'))->will($this->returnValue('https://eshop/'));

        $this->assertTrue($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode and only subshop ssl link exists
     * (M:1271)
     */
    public function testIsSsl_SslMode_WithSslShopUrl_forSubshop()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock(Config::class, ['getConfigParam']);
        $oConfig
            ->method('getConfigParam')
            ->withConsecutive(['sSSLShopURL'], ['sMallSSLShopURL'])
            ->willReturnOnConsecutiveCalls('', 'https://subshop/');

        $this->assertTrue($oConfig->isSsl());
    }

    /*
     * Testing method when shop is in ssl mode with different params returnede
     * by HTTPS parameter
     * (M:1271)
     */
    public function testIsSsl_SslMode_WithDifferentParams()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return 1; } else { return array(); } }');

        $oConfig = $this->getMock(Config::class, ['getConfigParam']);
        $oConfig->method('getConfigParam')->with($this->equalTo('sSSLShopURL'))->will($this->returnValue('https://eshop'));
        $this->assertTrue($oConfig->isSsl());

        oxTestModules::cleanUp();
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", '{ if ( isset($aA[0]) && $aA[0] == "HTTPS" ) { return "on"; } else { return array(); } }');
        $this->assertTrue($oConfig->isSsl());
    }

    /**
     * test that is httpsOnly method on config returns true if connection is using https and the shop is configured
     * with https for both urls
     */
    public function testIsHttpsOnlySameUrlWithSSl()
    {
        $res = $this->isHttpsOnlySameUrl(true);
        $this->assertTrue($res);
    }

    /**
     * test that is httpsOnly method on config returns false if connection is not using https and the shop is configured
     * with http for both urls
     */
    public function testIsHttpsOnlySameUrlNotSsl()
    {
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
        $config = $this->getMock(Config::class, ['isSsl', 'getSslShopUrl', 'getShopUrl']);
        $config->expects($this->any())->method('isSsl')->will($this->returnValue($withSsl));
        foreach (['getSslShopUrl', 'getShopUrl'] as $method) {
            $config->expects($this->any())->method($method)->will($this->returnValue('http' . ($withSsl ? 's' : '') . '://oxid-esales.com'));
        }
        $res = $config->isHttpsOnly();
        return $res;
    }

    private function getOutPath($oConfig, $sTheme = null, $blAbsolute = true)
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

    private function getViewsPath($oConfig, $sTheme = null, $blAbsolute = true)
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
     * When a DatabaseException is thrown, method handleDatabaseException on the ExceptionHandler is called
     *
     * @covers \OxidEsales\Eshop\Core\Config::init()
     */
    public function testInitCallesExceptionHandlerOnDatabaseException()
    {
        $this->setTime(time());

        /**
         * An instance of OxidEsales\Eshop\Core\Exception\DatabaseException::class should be caught and passed to the ExceptionHandler
         */
        $previousException = new \Exception();
        $exception = new \OxidEsales\Eshop\Core\Exception\DatabaseException('', 0, $previousException);

        $exceptionHandlerMock = $this->getMock(ExceptionHandler::class, ['handleDatabaseException']);
        $exceptionHandlerMock->expects($this->once())->method('handleDatabaseException');

        /** @var Config|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMock(Config::class, ['loadVarsFromDb','getExceptionHandler']);
        $config->expects($this->any())->method('loadVarsFromDb')->will($this->throwException($exception));
        $config->expects($this->any())->method('getExceptionHandler')->will($this->returnValue($exceptionHandlerMock));

        $config->init();
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

        $this->assertTrue($oConfig->getActiveView() instanceof \OxidEsales\EshopCommunity\Application\Controller\FrontendController);
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

        $this->assertEquals([$oView1, $oView2], $oConfig->getActiveViewsList());
    }

    /**
     * Test method ActiveViewsIds
     */
    public function testGetActiveViewsIds()
    {
        $config = oxNew('oxConfig');

        $view1 = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getClassKey']);
        $view1->expects($this->once())->method('getClassKey')->will($this->returnValue('testViewId1'));

        $view2 = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getClassKey']);
        $view2->expects($this->once())->method('getClassKey')->will($this->returnValue('testViewId2'));

        $config->setActiveView($view1);
        $config->setActiveView($view2);

        $this->assertEquals(['testViewId1', 'testViewId2'], $config->getActiveViewsIds());
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
        $blProductive = (bool) oxDb::getDb()->getOne($sQ);

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

        $sQ = 'select oxvarvalue from oxconfig where oxshopid="' . $sShopId . '" and oxvarname="' . $sVar . '" and oxmodule=""';
        $sVal = oxDb::getDb()->getOne($sQ);

        $oConfig->loadVarsFromDb($sShopId, [$sVar]);

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

        $sQ = 'select oxvarvalue from oxconfig where oxshopid="' . $sShopId . '" and oxvarname="' . $sVar . '" and oxmodule=""';
        $sVal = oxDb::getDb()->getOne($sQ);

        $oConfig->loadVarsFromDb($sShopId, [$sVar]);

        $this->assertEquals(unserialize($sVal), $oConfig->getConfigParam($sVar));
    }

    // testing random no bool, array/assoc array parameter
    public function testLoadVarsFromDbAnyOther()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $sQ = 'select oxvarname from oxconfig where (oxmodule="" or oxmodule="theme:twig") and oxvartype not in ( "bool", "arr", "aarr" )  and oxshopid="' . $sShopId . '"  and oxmodule="" order by rand()';
        $sVar = oxDb::getDb()->getOne($sQ);

        $sQ = 'select oxvarvalue from oxconfig where oxshopid="' . $sShopId . '" and oxvarname="' . $sVar . '" and oxmodule=""';
        $sVal = oxDb::getDb()->getOne($sQ);

        $oConfig->loadVarsFromDb($sShopId, [$sVar]);

        $this->assertEquals($sVal, $oConfig->getConfigParam($sVar));
    }

    // not existing variable
    public function testLoadVarsFromDbNotExisting()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $oConfig->loadVarsFromDb($sShopId, [time()]);

        $this->assertNull($oConfig->getConfigParam('nonExistingParameter'));
    }

    public function testSetConfVarFromDb()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ["setConfigParam"]);
        $oConfig
            ->method('setConfigParam')
            ->withConsecutive(
                ['test1', 't1'],
                ['test2', ['x']],
                ['test3', ['x' => 'y']],
                ['test4', true],
                ['test5', false],
            );

        $oConfig->setConfVarFromDb('test1', 'blabla', 't1');
        $oConfig->setConfVarFromDb('test2', 'arr', serialize(['x']));
        $oConfig->setConfVarFromDb('test3', 'aarr', serialize(['x' => 'y']));
        $oConfig->setConfVarFromDb('test4', 'bool', 'true');
        $oConfig->setConfVarFromDb('test5', 'bool', '0');
    }

    /**
     * testing close page
     */
    public function testPageClose()
    {
        $oStart = $this->getMock(\OxidEsales\Eshop\Application\Controller\OxidStartController::class, ['pageClose']);
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
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $aVars = ["theme:basic#iNewBasketItemMessage", "theme:twig#iNewBasketItemMessage", "theme:basic#iTopNaviCatCount", "theme:azure#iTopNaviCatCount", "#sCatThumbnailsize", "theme:basic#sCatThumbnailsize", "theme:azure#sCatThumbnailsize", "#sThumbnailsize", "theme:basic#sThumbnailsize", "theme:azure#sThumbnailsize", "#sZoomImageSize", "theme:basic#sZoomImageSize", "theme:azure#sZoomImageSize"];
        foreach ($aVars as $sData) {
            $aData = explode("#", $sData);
            $sModule = $aData[0] ?: oxConfig::OXMODULE_THEME_PREFIX . $oConfig->getConfigParam('sTheme');
            $sVar = $aData[1];

            $sQ = "select oxvarvalue from oxconfig where oxshopid='{$sShopId}' and oxmodule = '{$sModule}' and  oxvarname='{$sVar}'";
            $this->assertEquals($oDb->getOne($sQ), $oConfig->getShopConfVar($sVar, $sShopId, $sModule), "\nshop:{$sShopId}; {$sModule}; var:{$sVar}\n");
        }
    }

    public function testgetShopConfVarCheckingDbParamWhenMoreThan1InDB()
    {
        $this->cleanUpTable('oxconfig');
        $oConfig = oxNew('oxConfig');
        $sShopId = $oConfig->getBaseShopId();

        $sQ1 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values
                                    ('_test1', '$sShopId', 'testVar1', 'int', '1111111111')";
        $sQ2 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values
                                    ('_test2', '$sShopId', 'testVar1', 'int', '1111111111')";

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
        $aVal = ['a', 'b', 'c'];

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
        $aCurrArray = [$oEur, $oGbp, $oChf, $oUsd];

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

    public function testGetResourceUrlExpectsDefault()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->getOutPath($oConfig, $adminTheme, false) . "src/";
        $this->assertEquals($sDir, $oConfig->getResourceUrl('', true));
    }

    /**
     * Testing current (language check included) template directory getter
     */
    public function testGetTemplateDirNonAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $this->getViewsPath($oConfig);
        $sDir .= 'tpl/';

        $this->assertEquals($sDir, $oConfig->getTemplateDir());
    }

    public function testGetTemplateDirExpectsDefault()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $this->getViewsPath($oConfig, $this->get(AdminThemeBridgeInterface::class)->getActiveTheme()) . 'tpl/';
        $this->assertEquals($sDir, $oConfig->getTemplateDir(true));
    }

    /**
     * Testing templates URL getter
     */
    public function testGetTemplateUrlNonAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->getViewsPath($oConfig, null, false);
        $sDir .= 'tpl/';

        $this->assertEquals($sDir, $oConfig->getTemplateUrl());
    }

    public function testGetTemplateUrlExpectsDefault()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->getViewsPath($oConfig, $adminTheme, false) . 'tpl/';
        $this->assertEquals($sDir, $oConfig->getTemplateUrl(null, true));
    }


    /**
     * Testing base template directory getter
     */
    public function testGetResourceUrlNonAdminNonSsl()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['isSsl']);
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->getOutPath($oConfig, null, false) . 'src/';
        $this->assertEquals($sDir, $oConfig->getResourceUrl());
    }

    public function testGetResourceUrlAdminSsl()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = $this->getConfigWithSslMocked();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sSSLShopURL') . 'out/' . $adminTheme . '/src/';
        $this->assertEquals($sDir, $oConfig->getResourceUrl(null, true));
    }

    /**
     * Testing template file location getter
     */
    public function testGetTemplatePathNonAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $templateExtension = $this->getParameter('oxid_esales.templating.engine_template_extension');

        $sDir = $this->getViewsPath($oConfig) . 'tpl/page/shop/start.' . $templateExtension;

        $this->assertEquals($sDir, $oConfig->getTemplatePath('page/shop/start.' . $templateExtension, false));
    }

    public function testGetTemplatePathAdmin()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $templateName = 'start.' . $this->getParameter('oxid_esales.templating.engine_template_extension');
        $sDir =
            $this->getViewsPath($oConfig, $this->get(AdminThemeBridgeInterface::class)->getActiveTheme()) .
            'tpl/' . $templateName
            ;
        $this->assertEquals($sDir, $oConfig->getTemplatePath($templateName, true));
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

        $sDir = $this->getOutPath($oConfig) . 'img/';
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
        $sLangDir = $this->getOutPath($oConfig) . 'img/';
        $sNoLangDir = $this->getOutPath($oConfig) . 'img/';
        $failed = false;

        try {
            $this->assertEquals($sLangDir, $oConfig->getImageDir());
            $this->assertEquals($sNoLangDir, $oConfig->getImageDir());
        } catch (Exception $e) {
            $failed = true;
        }

        oxRegistry::getLang()->setTplLanguage();
        /*
        if (is_dir(realpath($sLangDir))) {
            rmdir($sLangDir);
        }*/
        $sD = $this->getOutPath($oConfig) . '/4';
        if (is_dir(realpath($sD))) {
            rmdir($sD);
        }

        if ($failed) {
            throw $e;
        }
    }


    /**
     * Testing getAbsAdminImageDir getter
     */
    public function testGetAbsAdminGetImageDirDefault()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/' . $adminTheme . '/img/';
        $this->assertEquals($sDir, $oConfig->getImageDir(1));
    }

    public function testGetAbsAdminGetImageDirForActLang()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/' . $adminTheme . '/img/';
        $this->assertEquals($sDir, $oConfig->getImageDir(1));
    }


    /**
     * Testing getCoreUtilsUrl getter
     */
    public function testGetCoreUtilsUrl()
    {
        $oConfig = $this->getMock(Config::class, ['getCurrentShopUrl']);
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
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['isSsl']);
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
        $this->assertEquals((int) \OxidEsales\Eshop\Core\Registry::getRequest()->getRequestEscapedParameter('currency'), $oConfig->getShopCurrency());
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
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['isAdmin', 'isSsl']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('blNativeImages', true);

        $sUrl = $oConfig->getConfigParam('sSSLShopURL') ?: $oConfig->getConfigParam('sShopURL');
        $sUrl .= $this->getOutPath($oConfig, null, false) . 'img/';
        $this->assertEquals($sUrl, $oConfig->getImageUrl());
    }

    public function testGetImageDirDefaultLanguage()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();

        $sUrl = $oConfig->getConfigParam('sShopURL');
        $sUrl .= $this->getOutPath($oConfig, null, false) . 'img/';

        $this->assertEquals($sUrl, $oConfig->getImageUrl());
    }

    /**
     * Testing getImagePath getter
     */
    public function testGetImagePath()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sUrl = $oConfig->getOutDir();
        $this->assertEquals($sUrl . $adminTheme . "/img/start.gif", $oConfig->getImagePath("start.gif", true));
    }

    /**
     * Testing getNoSslImageDir getter
     */
    public function testGetNoSslgetImageUrlAdminModeSecondLanguage()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . 'out/' . $adminTheme . '/img/';
        $this->assertEquals($sDir, $oConfig->getImageUrl(true));
    }

    public function testGetNoSslgetImageUrlDefaults()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->getOutPath($oConfig, null, false) . 'img/';

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
    public function testGetShopSecureHomeUrlReturnsSSLUrlIfSSLUrlIsSet()
    {
        $expectedUrl = $this->shopUrl . 'index.php?';

        $oConfig = oxNew(Config::class);
        $oConfig->setConfigParam('sSSLShopURL', '');
        $oConfig->setConfigParam('sShopURL', $this->shopUrl);
        $oConfig->init();

        \OxidEsales\Eshop\Core\Registry::set(Config::class, $oConfig);

        $this->assertEquals($expectedUrl, $oConfig->getShopSecureHomeUrl());
    }


    /**
     * Testing getShopSecureHomeUrl getter
     */
    public function testGetShopSecureHomeUrlReturnsNonSSLUrlIfSSLUrlIsNotSet()
    {
        $sslUrl = 'https://www.example.com/';
        $expectedUrl = $sslUrl . 'index.php?';

        $oConfig = oxNew(Config::class);
        $oConfig->setConfigParam('sSSLShopURL', $sslUrl);
        $oConfig->setConfigParam('sShopURL', $this->shopUrl);
        $oConfig->init();

        \OxidEsales\Eshop\Core\Registry::set(Config::class, $oConfig);

        $this->assertEquals($expectedUrl, $oConfig->getShopSecureHomeUrl());
    }


    /**
     * Testing getSslShopUrl getter
     */
    public function testGetSslShopUrlLanguageUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $aLanguageSslUrls = [5 => 'https://www.example.com/'];
        $oConfig->setConfigParam('aLanguageSSLURLs', $aLanguageSslUrls);
        $this->assertEquals($aLanguageSslUrls[5], $oConfig->getSslShopUrl(5));
    }

    public function testGetSslShopUrlByLanguageArrayAddsEndingSlash()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', [5 => 'http://www.example.com']);
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
        $this->assertEquals(0, strpos((string) $oConfig->getShopCurrentUrl(), 'https://www.example.com/index.php?'));
    }

    public function testGetShopCurrentUrlNoSsl()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['isSsl']);
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('sShopURL', 'http://www.example.com/');
        $this->assertEquals(0, strpos((string) $oConfig->getShopCurrentUrl(), 'http://www.example.com/index.php?'));
    }


    /**
     * Testing getShopUrl getter
     */
    public function testGetShopUrlIsAdmin()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopURL'), $oConfig->getShopUrl());
    }

    public function testGetShopUrlByLanguageArray()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageURLs', [5 => 'http://www.example.com/']);
        $this->assertEquals('http://www.example.com/', $oConfig->getShopUrl(5));
    }

    public function testGetShopUrlByLanguageArrayAddsEndingSlash()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageURLs', [5 => 'http://www.example.com']);
        $this->assertEquals('http://www.example.com/', $oConfig->getShopUrl(5));
    }

    public function testGetShopUrlByMallUrl()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();

        $oConfig->setConfigParam('aLanguageURLs', null);
        $oConfig->setConfigParam('sMallShopURL', 'http://www.example2.com/');
        $this->assertEquals('http://www.example2.com/', $oConfig->getShopUrl());
    }

    public function testGetShopUrlByMallUrlAddsEndingSlash()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->init();

        $oConfig->setConfigParam('aLanguageURLs', null);
        $oConfig->setConfigParam('sMallShopURL', 'http://www.example2.com');
        $this->assertEquals('http://www.example2.com/', $oConfig->getShopUrl());
    }

    public function testGetShopUrlDefaultUrl()
    {
        $oConfig = $this->getMock(Config::class, ['isAdmin']);
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
        //$oConfig->setNonPublicVar( 'iLanguageId', null );
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
        $_POST['testParameterKey'] = '&testValue';

        $this->assertEquals('&amp;testValue', Registry::getRequest()->getRequestEscapedParameter('testParameterKey'));
    }

    public function testGetRequestParameterRaw()
    {
        $_POST['testParameterKey'] = '&testValue';

        $this->assertEquals('&testValue', Registry::getRequest()->getRequestParameter('testParameterKey'));
    }

    public function testGetEdition()
    {
        $sEdition = (new Facts())->getEdition();
        $this->assertEquals($sEdition, (new Facts())->getEdition());
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
        $this->assertNotEquals('', (new Facts())->getEdition());
    }

    public function testGetFullEdition()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community editions only.');
        }

        $sFEdition = $this->getConfig()->getFullEdition();
        $this->assertEquals("Community Edition", $sFEdition);
    }

    public function testGetVersion()
    {
        $this->assertEquals(oxNew(ShopVersion::class)->getVersion(), ShopVersion::getVersion());
    }

    public function testGetVersionNotEmpty()
    {
        $this->assertNotEquals('', ShopVersion::getVersion());
    }

    public function testCorrectVersion()
    {
        $this->assertTrue(version_compare(ShopVersion::getVersion(), '4.9') >= 0);
    }

    public function testGetDir_level5()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/1/de/test1/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getOutDir']);
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test1', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . '1/de/test1/text.txt', $sDir);
    }

    public function testGetDir_delvel4()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/1/test2/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getOutDir']);
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test2', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . '1/test2/text.txt', $sDir);
    }

    public function testGetDir_level3()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/de/test2a/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getOutDir']);
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test2a', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'de/test2a/text.txt', $sDir);
    }

    public function testGetDir_delvel2()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/test3/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getOutDir']);
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test3', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'test3/text.txt', $sDir);
    }

    public function testGetDir_delvel1()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/test4/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getOutDir']);
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test4', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'text.txt', $sDir);
    }

    public function testGetDir_delvel0()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('out/de/test5/text.txt', '');
        $sTestDir = $vfsStreamWrapper->getRootPath();

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getOutDir']);
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . "out/";

        $sDir = $oConfig->getDir('text.txt', 'test5', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'de/test5/text.txt', $sDir);
    }

    public function testGetDirIfEditionTemplateFound()
    {
        $expectedResult = 'someEditionTemplateResponse';

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMock(Config::class, ['getEditionTemplate']);
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
        $oConfig = $this->getMock(Config::class, ['isAdmin', 'getShopUrl']);
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oConfig->expects($this->any())->method('getShopUrl')->will($this->returnValue('testUrl/'));
        $oConfig->init();
        $this->assertEquals('testUrl/out/', $oConfig->getOutUrl(false, null, true));
    }

    public function testGetOutUrlSsl()
    {
        $oConfig = $this->getMock(Config::class, ['isSsl', 'getSslShopUrl']);
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

        $oPH = $this->getMock(\OxidEsales\Eshop\Core\PictureHandler::class, ['getAltImageUrl']);
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
        $this->assertStringContainsString('nopic.jpg', $oConfig->getPictureUrl("test.gif", false));
    }

    public function testGetPictureUrlNeverEmptyString()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('blFormerTplSupport', true);
        $this->assertNotEquals('', $oConfig->getPictureUrl("test.gif", false));
        $this->assertStringContainsString('master/nopic.jpg', $oConfig->getPictureUrl("test.gif", false));
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
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals("Application/views/" . $adminTheme . "/", $oConfig->getTemplateBase(true));
    }

    public function testGetResourcePath()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopDir') . "out/" . $adminTheme . "/src/main.css", $oConfig->getResourcePath("main.css", true));
    }

    public function testGetResourceDir()
    {
        $adminTheme = $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopDir') . "out/" . $adminTheme . "/src/", $oConfig->getResourceDir(true));
    }

    public function testGetResourceUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $oConfig->setConfigParam('sTheme', ACTIVE_THEME);

        $sMainURL = $oConfig->getConfigParam('sShopURL');
        $sMallURL = 'http://www.example.com/';

        $sDir = 'out/' . ACTIVE_THEME . '/src/';

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

    public function testIsThemeOption()
    {
        $oConfig = $this->getProxyClass("oxConfig");
        $oConfig->setNonPublicVar("_aThemeConfigParams", ['param1' => 'theme1', 'param2' => 'theme2']);

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
        $aArray = [1, 2, 3];
        $aAssocArray = ["a" => 1, "b" => 2, "c" => 3];

        $oConfig = oxNew('oxConfig');
        $this->assertEquals($sString, $oConfig->decodeValue("string", $sString));
        $this->assertEquals($oObject, $oConfig->decodeValue("object", $oObject));
        $this->assertEquals(true, $oConfig->decodeValue("bool", $blBool));
        $this->assertEquals($aArray, $oConfig->decodeValue("arr", serialize($aArray)));
        $this->assertEquals($aAssocArray, $oConfig->decodeValue("aarr", serialize($aAssocArray)));
    }

    public function testDecodeValueWithSerializedObjectWillNotInstantiateIt(): void
    {
        $someObject = oxNew(Article::class);
        $serialized = serialize($someObject);

        $decoded = oxNew(Config::class)->decodeValue('arr', $serialized);

        $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $decoded);
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
     * Tests that custom config is being set and variables from it are reachable
     *
     */
    public function testLoadCustomConfig()
    {
        $this->createFile('config.inc.php', '<?php $this->testVar = "testValue";');
        $file = $this->createFile('cust_config.inc.php', '<?php $this->customVar = "customValue";');
        $this->setConfigParam('sShopDir', dirname($file));

        /** @var oxConfig $config */
        $config = $this->getMock(Config::class, ['init']);
        $config->loadVarsFromFile();

        $this->assertSame("customValue", $config->getConfigParam("customVar"));
    }

    /**
     * Testing config init - loading config vars returns no result
     */
    public function testInit_noValuesFromConfig()
    {
        /** @var oxconfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['loadVarsFromDb', 'handleDbConnectionException']);
        $oConfig->expects($this->once())->method('loadVarsFromDb')->will($this->returnValue(false));
        $oConfig->expects($this->once())->method('handleDbConnectionException');
        $oConfig->setConfigParam('iDebug', -1);

        $oConfig->init();
    }

    /**
     * Testing config parameters getter
     */
    public function testInit_noShopId()
    {
        /** @var oxconfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['getShopId', 'handleDbConnectionException']);
        $oConfig->expects($this->once())->method('getShopId')->will($this->returnValue(false));
        $oConfig->expects($this->once())->method('handleDbConnectionException');
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
     * Mock oxUtilsServer to see that oxConfig::isCurrentUrl return same result.
     */
    public function testIsCurrentUrlIsWrapperForOxUtilsServer()
    {
        $sURLToCheck = 'some url which does not matter as we check against mock';

        /** @var oxUtilsServer|PHPUnit\Framework\MockObject\MockObject $oUtilsServer */
        $oUtilsServer = $this->getMock('oxUtilsServer');
        $oUtilsServer->expects($this->any())->method('isCurrentUrl')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsServer::class, $oUtilsServer);

        $this->assertTrue($this->getConfig()->isCurrentUrl($sURLToCheck));

        /** @var oxUtilsServer|PHPUnit\Framework\MockObject\MockObject $oUtilsServer */
        $oUtilsServer = $this->getMock('oxUtilsServer');
        $oUtilsServer->expects($this->any())->method('isCurrentUrl')->will($this->returnValue(false));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsServer::class, $oUtilsServer);

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
     * Test method getRequestControllerId
     */
    public function testGetRequestControllerId()
    {
        $config = oxNew('oxConfig');
        $_POST['cl'] = 'testControllerId';

        $this->assertEquals('testControllerId', $config->getRequestControllerId());
    }

    /**
     * Test method getRequestControllerId in case it is not set.
     */
    public function testGetRequestControllerIdNotSet()
    {
        $config = oxNew('oxConfig');
        $_POST = [];

        $this->assertNull($config->getRequestControllerId());
    }

    /**
     * Test method getRequestControllerClass()
     */
    public function testGetRequestControllerClass()
    {
        Registry::set(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class, $this->getControllerClassNameResolverMock());

        $config = oxNew('oxConfig');
        $_POST['cl'] = 'DDD';

        $this->assertEquals('Vendor1\OtherTestModule\SomeOtherController', $config->getRequestControllerClass());
    }

    /**
     * Test method getRequestControllerClass()
     */
    public function testGetRequestControllerClassNoMatch()
    {
        Registry::set(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class, $this->getControllerClassNameResolverMock());

        $config = oxNew('oxConfig');
        $_POST['cl'] = 'unknownControlerId';

        $this->assertNull($config->getRequestControllerClass());
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Config::getExceptionHandler()
     */
    public function testGetExceptionHandlerReturnsInstanceOfExceptionHandler()
    {
        $expectedClass = \OxidEsales\Eshop\Core\Exception\ExceptionHandler::class;

        $config = oxNew(Config::class);
        $actualObject = $config->getExceptionHandler();

        $this ->assertInstanceOf($expectedClass, $actualObject);
    }

    /**
     * @return oxConfig|PHPUnit\Framework\MockObject\MockObject
     */
    protected function getConfigWithSslMocked()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(Config::class, ['isSsl', 'getSslShopUrl']);
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
        return [['arr', 'aPraram', [1, 3]], ['aarr', 'aAParam', ['a' => 1]], ['bool', 'blParam', true], ['num', 'numNum', 2], ['int', 'iNum1', 0], ['int', 'iNum2', 4]];
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
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsUrl::class, $utilsUrl);
    }

    /**
     * Test helper
     *
     * @return OxidEsales\EshopCommunity\Core\ShopControllerProvider mock
     */
    private function getShopControllerMapProviderMock()
    {
        $map = ['aAa' => 'OxidEsales\EshopCommunity\Application\SomeController', 'bbb' => 'OxidEsales\EshopCommunity\Application\SomeOtherController', 'CCC' => 'OxidEsales\EshopCommunity\Application\SomeDifferentController'];

        $mock = $this->getMock(\OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider::class, ['getControllerMap'], [], '', false);
        $mock->expects($this->any())->method('getControllerMap')->will($this->returnValue($map));

        return $mock;
    }

    private function getActiveModulesDataProviderBridgeMock(): ActiveModulesDataProviderBridgeInterface
    {
        $map = [
            new Controller('cCc', 'Vendor1\Testmodule\SomeController'),
            new Controller('DDD', 'Vendor1\OtherTestModule\SomeOtherController'),
            new Controller('eee', 'Vendor2\OtherTestModule\SomeDifferentController')
        ];

        $bridge = $this->getMockBuilder(ActiveModulesDataProviderBridgeInterface::class)->getMock();
        $bridge->method('getControllers')->willReturn($map);

        return $bridge;
    }

    /**
     * Test helper
     *
     * @return OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver mock
     */
    private function getControllerClassNameResolverMock()
    {
        $resolver = oxNew(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class, $this->getShopControllerMapProviderMock(), $this->getActiveModulesDataProviderBridgeMock());

        return $resolver;
    }
}
