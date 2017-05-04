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

class modForTestGetBaseTplDirExpectsDefault extends oxConfig
{

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

// P
/*
class modForTestGetTemplateDirExpectsDefault extends oxConfig
{
    public function getShopLanguage()
    {
        return 'xxx';
    }
}
*/

class modForTestGetBaseTemplateDirNonAdminNonSsl extends oxConfig
{

    public function isSsl()
    {
        return false;
    }
}

class modForTestGetBaseTemplateDirAdminSsl extends oxConfig
{

    public function getConfigParam($sP)
    {
        $orig = parent::getConfigParam($sP);
        if ($sP == 'sSSLShopURL') {
            return $orig ? $orig : 'https://leuleuleu/';
        }

        return $orig;
    }

    public function isSsl()
    {
        return true;
    }

    public function getSslShopUrl($iLang = null)
    {
        return $this->getConfigParam('sSSLShopURL');
    }
}

class modForTestGetImageDirNativeImagesIsSsl extends modForTestGetBaseTemplateDirAdminSsl
{

    public function isSsl()
    {
        return true;
    }
}

class modForGetShopHomeUrl extends oxConfig
{

    public function getShopUrl($iLang = null, $blAdmin = null)
    {
        return 'http://www.example.com/';
    }

    public function getSslShopUrl($iLang = null)
    {
        return 'https://www.example.com/';
    }
}

class modFortestGetShopTakingFromRequestNoMall extends oxConfig
{

    public function isMall()
    {
        return false;
    }
}

class Unit_Core_oxconfigTest extends OxidTestCase
{

    protected $_iCurr = null;
    protected $_aShops = array();

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

        return;

        for ($i = 2; $i < 7; $i++) {
            $this->_aShops[$i] = oxNew('oxbase');
            $this->_aShops[$i]->init('oxshops');
            $this->_aShops[$i]->setId($i);
            $this->_aShops[$i]->oxshop__oxactive = new oxField(1, oxField::T_RAW);
            $this->_aShops[$i]->oxshop__oxactive_1 = new oxField(1, oxField::T_RAW);
            $this->_aShops[$i]->oxshop__oxactive_2 = new oxField(1, oxField::T_RAW);
            $this->_aShops[$i]->oxshop__oxactive_3 = new oxField(1, oxField::T_RAW);
            $this->_aShops[$i]->save();
        }
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

        $sCustConfigPath = getShopBasePath() . "/cust_config.inc.php";
        if (file_exists($sCustConfigPath)) {
            unlink($sCustConfigPath);
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
     * By default it should return false
     *
     * @return
     */
    public function testIsUtf()
    {
        $oConfig = new oxConfig();
        $this->assertFalse($oConfig->isUtf());
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

        return $sShop . 'application/views/' . $sTheme;
    }

    /**
     * Testing config init - no connection to DB
     */
    public function testInit_noConnection()
    {
        $oConfig = $this->getMock("oxconfig", array("_loadVarsFromDb"));
        $oEx = oxNew("oxConnectionException");
        $oConfig->expects($this->once())->method('_loadVarsFromDb')->will($this->throwException($oEx));

        $this->assertFalse($oConfig->init());
    }

    /**
     * Testing config parameters getter
     */
    public function testGetConfigParamCheckingDbParam()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertFalse($oConfig->getConfigParam('blEnterNetPrice'));
    }

    public function testGetConfigParamCheckingNotExistingParam()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertNull($oConfig->getConfigParam('xxx'));
    }

    /**
     * Testing config parameters setter
     */
    public function testSetConfigParamOverridingLocalParam()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('dbType', 'yyy');
        $this->assertEquals('yyy', $oConfig->getConfigParam('dbType'));
    }

    public function testSetConfigParamOverridingCachedParam()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('xxx', 'yyy');
        $this->assertEquals('yyy', $oConfig->getConfigParam('xxx'));
    }

    /**
     * Testing config cache setter
     */
    public function testSetGlobalParameter()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setGlobalParameter('xxx', 'yyy');
        $this->assertEquals('yyy', $oConfig->getGlobalParameter('xxx'));
    }

    /**
     * Testing config cache getter
     */
    public function testGetGlobalParameterNoParameter()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertNull($oConfig->getGlobalParameter('xxx'));
    }


    /**
     * Testing active view getter
     */
    public function testGetActiveView_NoViewSetYet()
    {
        $oConfig = new oxConfig();

        $this->assertTrue($oConfig->getActiveView() instanceof oxubase);
    }

    public function testSetGetActiveView()
    {
        $oConfig = new oxConfig();

        $oView = new stdClass();
        $oConfig->setActiveView($oView);

        $this->assertEquals($oView, $oConfig->getActiveView());
    }

    public function testGetTopActiveView()
    {
        $oConfig = new oxConfig();

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
        $oConfig = new oxConfig();

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
        $oConfig = new oxConfig();

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
        $oConfig = new oxConfig();

        $this->assertFalse($oConfig->hasActiveViewsChain());
    }

    public function testGetActiveViewsList()
    {
        $oConfig = new oxConfig();

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
        $oConfig = new oxConfig();

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
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals('oxbaseshop', $oConfig->getBaseShopId());
    }

    /**
     * Testing mall mode getter
     */
    public function testIsMall()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertFalse($oConfig->isMall());
    }


    /**
     * Testing productive mode check
     */
    public function testIsProductiveModeForEnterpise()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
        $oConfig->init();
        $sShopId = $oConfig->getBaseShopId();

        $oConfig->UNITloadVarsFromDB($sShopId, array(time()));

        $this->assertNull($oConfig->getConfigParam($sVar));
    }

    public function testSetConfVarFromDb()
    {
        $oConfig = $this->getMock(
            oxTestModules::publicize('oxconfig', '_setConfVarFromDb'),
            array("setConfigParam")
        );
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

        $oConfig->p_setConfVarFromDb('test1', 'blabla', 't1');
        $oConfig->p_setConfVarFromDb('test2', 'arr', serialize(array('x')));
        $oConfig->p_setConfVarFromDb('test3', 'aarr', serialize(array('x' => 'y')));
        $oConfig->p_setConfVarFromDb('test4', 'bool', 'true');
        $oConfig->p_setConfVarFromDb('test5', 'bool', '0');
    }

    /**
     * testing close page
     */
    public function testPageClose()
    {
        $oStart = $this->getMock('oxStart', array('pageClose'));
        $oStart->expects($this->once())->method('pageClose');

        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam("_oStart", $oStart);

        $oConfig->pageClose();
    }

    /**
     * testing shops configuration param getter
     */
    public function testgetShopConfVar()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
        $sShopId = $oConfig->getBaseShopId();

        $sQ1 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values
                                    ('_test1', '$sShopId', 'testVar1', 'int', 0x071d6980dc7afb6707bb)";
        $sQ2 = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values
                                    ('_test2', '$sShopId', 'testVar1', 'int', 0x071d6980dc7afb6707bb)";

        oxDb::getDb()->execute($sQ1);
        oxDb::getDb()->execute($sQ2);

        $oConfig = new oxConfig();
        $this->assertFalse($oConfig->getShopConfVar('testVar1') == null);

    }


    /**
     * Testing if shop var saver writes correct info into db
     */
    public function testsaveShopConfVar()
    {
        $sName = 'xxx';
        $sVal = '123';

        $oConfig = new oxConfig();
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

        $oConfig = new oxConfig();
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

        $this->getConfig()->saveShopConfVar('string', 'oxtesting', 'test', null, '');
        $this->getConfig()->saveShopConfVar('string', 'oxtesting', 'test', null, 'theme:basic');

        $this->getConfig()->cleanup();
        $this->assertEquals('test', $this->getConfig()->getConfigParam('oxtesting'));

        oxDb::getDb()->execute('delete from oxconfig where oxmodule="theme:basic" and oxvarname="oxtesting"');
        $this->getConfig()->saveShopConfVar('string', 'oxtesting', 'test', null, 'theme:basic');

        $this->getConfig()->cleanup();
        $this->assertEquals('test', $this->getConfig()->getConfigParam('oxtesting'));

        oxDb::getDb()->execute('delete from oxconfig where oxvarname="oxtesting"');
    }

    /**
     * Testing if shop var saver writes bool value to config correctly
     */
    public function testsaveShopConfVarBoolTrue1()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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
     * Testing serial number setter
     */
    public function testSetSerial()
    {
    }

    /**
     * Testing language array getter
     */
    /* P
    public function testGetLanguageArray()
    {
        // preparing fixture
        $oDe = new stdclass;
        $oDe->id = 0;
        $oDe->name = 'Deutsch';
        $oDe->selected = 1;

        $oEng = clone $oDe;
        $oEng->id = 1;
        $oEng->name = 'English';
        $oEng->selected = 0;
        $aLangArray = array( $oDe, $oEng );

        $oConfig = new oxConfig();
        $oConfig->init();

        $this->assertEquals( $aLangArray, $oConfig->getLanguageArray( 0 ) );
    }
    */

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
        $oEur->sign = '¤';
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

        $oConfig = new oxConfig();
        $oConfig->init();

        $this->assertEquals($aCurrArray, $oConfig->getCurrencyArray(0));
    }

    /**
     * Testing currency getter
     */
    public function testGetCurrencyObjectNotExisting()
    {
        $oConfig = new oxConfig();
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
        $oEur->sign = '¤';
        $oEur->decimal = '2';
        $oEur->selected = 0;

        $oConfig = new oxConfig();
        $oConfig->init();

        $this->assertEquals($oEur, $oConfig->getCurrencyObject($oEur->name));
    }


    /**
     * Testing if serial getter
     */
    // if it really returns same object
    public function testGetSerialIsSameObject()
    {
    }



    /**
     * Testing active shop getter if it returns same object + if serial is set while loading shop
     */
    public function testGetActiveShop()
    {
        $oConfig = new oxConfig();
        $oConfig->init();


        // comparing serials
        $oShop = $oConfig->getActiveShop();


        // additionally checking caching
        $oShop->xxx = 'yyy';
        $this->assertEquals('yyy', $oConfig->getActiveShop()->xxx);

        // checking if different language forces reload
        $iCurrLang = oxRegistry::getLang()->getBaseLanguage();
        oxRegistry::getLang()->resetBaseLanguage();
        modConfig::setRequestParameter('lang', $iCurrLang + 1);

        $oShop = $oConfig->getActiveShop();
        $this->assertFalse(isset($oShop->xxx));
    }



    /**
     * Testing Mandate Counter (default installation count is 0)
     */
    public function testGetMandateCountOneSubShop()
    {

        $oConfig = new oxConfig();
        $oConfig->init();

        $this->assertEquals(1, $oConfig->getMandateCount());
    }


    public function testThemeNameExpectsDefault()
    {
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $oConfig->init();
        $this->assertEquals('azure', $oConfig->getConfigParam('sTheme'));
    }

    public function testGetResourceUrlExpectsDefault()
    {
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, 'admin', false) . "src/";
        $this->assertEquals($sDir, $oConfig->getResourceUrl('', true));
    }

    public function testGetResourceUrlNonAdminExpectsDefault()
    {
        $oConfig = new modForTestGetBaseTplDirExpectsDefault();
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, null, false) . "src/";
        $this->assertEquals($sDir, $oConfig->getResourceUrl());
    }

    /**
     * Testing current (language check included) template directory getter
     */
    public function testGetTemplateDirNonAdmin()
    {
        $oConfig = new oxConfig();
        $oConfig->init();

        $sDir = $this->_getViewsPath($oConfig);
        if ($oConfig->getConfigParam('sTheme') != 'azure') {
            $sDir .= 'de/tpl/';
        } else {
            $sDir .= 'tpl/';
        }

        $this->assertEquals($sDir, $oConfig->getTemplateDir());
    }

    public function testGetTemplateDirExpectsDefault()
    {
        oxRegistry::getLang()->setBaseLanguage(999);
        $oConfig = new oxConfig();
        $oConfig->init();
        $sDir = $this->_getViewsPath($oConfig, 'admin') . 'tpl/';
        $this->assertEquals($sDir, $oConfig->getTemplateDir(true));
    }

    /**
     * Testing templates URL getter
     */
    public function testGetTemplateUrlNonAdmin()
    {
        $oConfig = new oxConfig();
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getViewsPath($oConfig, null, false);
        $sDir .= ($oConfig->getConfigParam('sTheme') != 'azure') ? 'de/tpl/' : 'tpl/';

        $this->assertEquals($sDir, $oConfig->getTemplateUrl());
    }

    public function testGetTemplateUrlExpectsDefault()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new modForTestGetBaseTemplateDirNonAdminNonSsl();
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, null, false) . 'src/';
        $this->assertEquals($sDir, $oConfig->getResourceUrl());
    }

    public function testGetResourceUrlAdminSsl()
    {
        $oConfig = new modForTestGetBaseTemplateDirAdminSsl();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sSSLShopURL') . 'out/admin/src/';
        $this->assertEquals($sDir, $oConfig->getResourceUrl(null, true));
    }

    /**
     * Testing template file location getter
     */
    public function testGetTemplatePathNonAdmin()
    {
        $oConfig = new oxConfig();
        $oConfig->init();

        $sDir = $this->_getViewsPath($oConfig);
        $sDir .= ($oConfig->getConfigParam('sTheme') != 'azure') ? 'de/tpl/' : 'tpl/';
        $sDir .= 'page/shop/start.tpl';

        $this->assertEquals($sDir, $oConfig->getTemplatePath('page/shop/start.tpl', false));
    }

    public function testGetTemplatePathAdmin()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $sDir = $this->_getViewsPath($oConfig, 'admin') . 'tpl/start.tpl';
        $this->assertEquals($sDir, $oConfig->getTemplatePath('start.tpl', true));
    }

    /**
     * Testing getAbsDynImageDir getter
     */
    public function testGetTranslationsDir()
    {
        $oConfig = new oxConfig();
        $sDir = $this->getConfigParam('sShopDir') . 'application/translations/en/lang.php';
        $this->assertEquals($sDir, $oConfig->getTranslationsDir('lang.php', 'en'));
        $this->assertFalse($oConfig->getTranslationsDir('lang.php', 'na'));
    }

    /**
     * Testing getAbsDynImageDir getter
     */
    public function testGetAbsDynImageDirForCustomShop()
    {
        $oConfig = new oxConfig();
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/pictures/';


        $this->assertEquals($sDir, $oConfig->getPictureDir(false));
    }

    public function testGetAbsDynImageDirForSecondLang()
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('blUseDifferentDynDirs', true);

        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/pictures/';


        $this->assertEquals($sDir, $oConfig->getPictureDir(false));
    }


    /**
     * Testing getImageDir getter
     */
    public function testGetImageDir()
    {
        $oConfig = new oxConfig();

        $oConfig->init();

        $sDir = $this->_getOutPath($oConfig) . 'img/';
        $this->assertEquals($sDir, $oConfig->getImageDir());
    }

    /**
     * Testing getImageDir getter
     */
    public function testGetImageDirMultiLangDirsExist()
    {
        $oConfig = new oxConfig();

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
        $oConfig = new oxConfig();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/admin/img/';
        $this->assertEquals($sDir, $oConfig->getImageDir(1));
    }

    public function testGetAbsAdminGetImageDirForActLang()
    {
        $oConfig = new oxConfig();
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
        $this->assertEquals('xxx/core/utils/', $oConfig->getCoreUtilsUrl());
    }

    public function testGetCoreUtilsUrlMall()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', 'http://www.example3.com/');
        $this->assertEquals('http://www.example3.com/core/utils/', $oConfig->getCoreUtilsUrl());
    }


    /**
     * Testing getCurrentShopURL getter
     */
    public function testGetCurrentShopUrlNoSsl()
    {
        $oConfig = new modForTestGetBaseTemplateDirNonAdminNonSsl();
        $oConfig->init();
        $this->assertEquals($oConfig->getShopUrl(), $oConfig->getCurrentShopUrl());
    }

    public function testGetCurrentShopUrlIsSsl()
    {
        $oConfig = new modForTestGetBaseTemplateDirAdminSsl();
        $oConfig->init();
        $this->assertEquals($oConfig->getSslShopUrl(), $oConfig->getCurrentShopUrl());
    }


    /**
     * Testing active currency id getter
     */
    public function testGetShopCurrency()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        // simple check if nothing was changed ...
        $this->assertEquals((int) $oConfig->getRequestParameter('currency'), $oConfig->getShopCurrency());
    }


    /**
     * Testing active shop currenty setter
     */
    public function testSetActShopCurrencySettingExisting()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals(0, $oConfig->getShopCurrency());
        $oConfig->setActShopCurrency(1);
        $this->assertEquals(1, $this->getSession()->getVariable('currency'));
    }

    public function testSetActShopCurrencySettingNotExisting()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals(0, $oConfig->getShopCurrency());
        $oConfig->setActShopCurrency('xxx');
        $this->assertEquals(0, $oConfig->getShopCurrency());
    }


    /**
     * Testing URL checker
     */
    // by passing empty URL it returns false
    public function testIsCurrentUrlNoUrl()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertTrue($oConfig->isCurrentUrl(''));
    }

    public function testIsCurrentUrlRandomUrl()
    {
        $sUrl = 'http://www.example.com/example/example.php';
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertFalse($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlPassingCurrent()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $sUrl = $oConfig->getConfigParam('sShopURL') . '/example.php';
        $this->assertFalse($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlNoProtocol()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $sUrl = 'www.example.com';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlBadProtocol()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $sUrl = 'ftp://www.example.com';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlBugFixTest()
    {
        $sUrl = 'http://www.example.com.ru';
        $oConfig = new oxConfig();
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'http://www.example.com';
        $_SERVER['SCRIPT_NAME'] = '';
        $this->assertfalse($oConfig->isCurrentUrl($sUrl));

        $sUrl = 'http://www.example.com';
        $oConfig = new oxConfig();
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'http://www.example.com.ru';
        $_SERVER['SCRIPT_NAME'] = '';
        $this->assertfalse($oConfig->isCurrentUrl($sUrl));

        //#4010: force_sid added in https to every link
        $sUrl = 'https://www.example.com.ru';
        $oConfig = new oxConfig();
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'www.example.com.ru';
        $_SERVER['SCRIPT_NAME'] = '';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    /**
     * Bug fix 0005685: Varnish issues on balanced system
     * Force sid is added on each link if proxy is in between client and Shop server.
     */
    public function testIsCurrentUrlWithLoadBalancer()
    {
        $sUrl = 'https://www.example.com.ru';
        $oConfig = new oxConfig();
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'www.loadbalancer.de';
        $_SERVER['SCRIPT_NAME'] = '';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.example.com.ru';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    /**
     * Test different script names, which should be recognized as belonging to the current URL
     *
     * @dataProvider dataProviderTestIsCurrentUrlWithPathInScriptName
     *
     * @param $scriptName
     */
    public function testIsCurrentUrlWithPathInScriptName($scriptName)
    {
        $sUrl = 'http://www.example.com';
        $oConfig = new oxConfig();
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'http://www.example.com';
        $_SERVER['SCRIPT_NAME'] = $scriptName;

        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    public function dataProviderTestIsCurrentUrlWithPathInScriptName()
    {
        return array(
            array(
                'scriptName' => '/core/utils/verificationimg.php?e_mac=ox_MEQNDB4fVQEF'
            ),
            array(
                'scriptName' => '/modules/oxps/somemodule/file.php'
            ),
            /**
             * TODO Make this script name pass too
             * array('/some/random/path/to/file.php'),
             */
        );
    }

    /**
     * Testing getImageDir getter
     */
    public function testGetImageDirNativeImagesIsSsl()
    {
        $oConfig = $this->getMock('modForTestGetImageDirNativeImagesIsSsl', array('isAdmin'));
        $oConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
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
        $oConfig = new oxConfig();
        $oConfig->init();

        $sUrl = $oConfig->getOutDir();
        $this->assertEquals($sUrl . "admin/img/start.gif", $oConfig->getImagePath("start.gif", true));
    }

    /**
     * Testing getNoSslImageDir getter
     */
    public function testGetNoSslgetImageUrlAdminModeSecondLanguage()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . 'out/admin/img/';
        $this->assertEquals($sDir, $oConfig->getImageUrl(true));
    }

    public function testGetNoSslgetImageUrlDefaults()
    {
        $this->getConfig()->setConfigParam('aLanguages', array(0 => 'DE', 1 => 'EN', 2 => 'LT'));
        oxRegistry::getLang()->setBaseLanguage(2);

        $oConfig = new oxConfig();
        $oConfig->init();
        $sDir = $oConfig->getConfigParam('sShopURL') . $this->_getOutPath($oConfig, null, false) . 'img/';

        $this->assertEquals($sDir, $oConfig->getImageUrl());
    }


    /**
     * Testing getShopHomeUrl getter
     */
    public function testGetShopHomeUrl()
    {
        $oConfig = new modForGetShopHomeUrl();
        $oConfig->init();
        $sUrl = oxRegistry::get('oxUtilsUrl')->processUrl('http://www.example.com/index.php', false);
        $this->assertEquals($sUrl, $oConfig->getShopHomeUrl());
    }

    /**
     * Testing getShopHomeUrl getter
     */
    public function testGetWidgetUrl()
    {
        $oConfig = new modForGetShopHomeUrl();
        $oConfig->init();
        $sUrl = oxRegistry::get('oxUtilsUrl')->processUrl('http://www.example.com/widget.php', false);
        $this->assertEquals($sUrl, $oConfig->getWidgetUrl());
    }

    /**
     * Testing getShopSecureHomeUrl getter
     */
    public function testGetShopSecureHomeUrl()
    {
        $oConfig = new modForGetShopHomeUrl();
        $oConfig->init();
        $sUrl = oxRegistry::get('oxUtilsUrl')->processUrl('https://www.example.com/index.php', false);
        $this->assertEquals($sUrl, $oConfig->getShopSecureHomeUrl());
    }


    /**
     * Testing getSslShopUrl getter
     */
    public function testGetSslShopUrlLanguageUrl()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', 'https://www.example2.com/');
        $this->assertEquals('https://www.example2.com/', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlMallSslUrlAddsEndingSlash()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', 'https://www.example2.com');
        $this->assertEquals('https://www.example2.com/', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlMallUrl()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', 'https://www.example3.com/');
        $this->assertEquals('https://www.example3.com/', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlSslUrl()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', null);
        $oConfig->setConfigParam('sSSLShopURL', 'https://www.example4.com');
        $this->assertEquals('https://www.example4.com', $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlDefaultUrl()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('aLanguageSSLURLs', null);
        $oConfig->setConfigParam('sMallSSLShopURL', null);
        $oConfig->setConfigParam('sMallShopURL', null);
        $oConfig->setConfigParam('sSSLShopURL', null);
        $this->assertEquals($oConfig->getConfigParam('sShopURL'), $oConfig->getSslShopUrl());
    }

    public function testGetSslShopUrlConfigUrl()
    {
        $oConfig = new oxConfig();
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

        modConfig::setRequestParameter('cur', 1);
        $oConfig = new oxConfig();
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
        $oEur->sign = '¤';
        $oEur->decimal = '2';
        $oEur->selected = 0;

        modConfig::setRequestParameter('cur', 999);
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals($oEur, $oConfig->getActShopCurrencyObject());
    }


    /**
     * Testing getShopCurrentUrl getter
     */
    public function testGetShopCurrentUrlIsSsl()
    {
        $oConfig = new modForTestGetBaseTemplateDirAdminSsl();
        $oConfig->init();
        $oConfig->setConfigParam('sSSLShopURL', 'https://www.example.com/');
        $this->assertEquals(0, strpos($oConfig->getShopCurrentUrl(), 'https://www.example.com/index.php?'));
    }

    public function testGetShopCurrentUrlNoSsl()
    {
        $oConfig = new modForTestGetBaseTemplateDirNonAdminNonSsl();
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
        modConfig::setRequestParameter( 'changelang', 1 );
        $oConfig = $this->getMock( 'oxConfig', array( 'isAdmin' ) );
        $oConfig->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oConfig->init();
        //$oConfig->setNonPublicVar( '_iLanguageId', null );
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        modConfig::setRequestParameter( 'changelang', null );
        modConfig::setRequestParameter( 'lang', 1 );
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        modConfig::setRequestParameter( 'changelang', null );
        modConfig::setRequestParameter( 'lang',       null );
        modConfig::setRequestParameter( 'tpllanguage', 1 );
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        modConfig::setRequestParameter( 'changelang',  null );
        modConfig::setRequestParameter( 'lang',        null );
        modConfig::setRequestParameter( 'tpllanguage', null );
        modConfig::setRequestParameter( 'language', 1 );
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals( 1, $oConfig->getShopLanguage() );

        modConfig::setRequestParameter( 'changelang',  null );
        modConfig::setRequestParameter( 'lang',        null );
        modConfig::setRequestParameter( 'tpllanguage', null );
        modConfig::setRequestParameter( 'language',    null );
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam( 'sDefaultLang', 1 );
        $this->assertEquals( 1, $oConfig->getShopLanguage() );
    }
    // testing if bad language id is fixed
    public function testGetShopLanguagePassingNotExistingShouldBeFixed()
    {
        modConfig::setRequestParameter( 'changelang', 'xxx' );
        $oConfig = new oxConfig();
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
     */
    // PE: always oxbaseshop
    public function testGetShopIdForPeAlwaysOxbaseshop()
    {

        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals('oxbaseshop', $oConfig->getShopId());
    }







    public function getShopFromLangUrls_isCurrentUrl15($url)
    {
        if (!in_array($url, array('asd', 'dsa', 'asda', 'dsad'))) {
            $this->fail("unknown url given");
        }

        return $url == 'dsad';
    }

    public function getShopFromLangUrls_isCurrentUrl14($url)
    {
        if (!in_array($url, array('asd', 'dsa', 'asda', 'dsad'))) {
            $this->fail("unknown url given");
        }

        return $url == 'asd';
    }



    public function testGetUploadedFile()
    {
        $aBack = $_FILES;
        $_FILES['upload'] = 'testValue';

        $this->assertEquals('testValue', $this->getConfig()->getUploadedFile('upload'));

        $_FILES = $aBack;
    }

    public function testGetRequestParameter()
    {
        $oConfig = $this->getConfig();
        $oConfig->setRequestParameter('testval', '_testval');

        $this->assertEquals('_testval', $oConfig->getRequestParameter('testval'));
    }

    public function testGetEdition()
    {
        $sShopId = $this->getConfig()->getShopId();
        $sEdition = oxDb::getDb()->getOne("select oxedition from oxshops where oxid = '$sShopId'");
        $this->assertEquals($sEdition, $this->getConfig()->getEdition());
    }

    public function testGetRevision_FileExists()
    {
        $oConfig = new oxConfig();
        $sFileName = 'pkg.rev';
        $iRevisionNum = 12345;
        $sFilePath = $this->createFile($sFileName, $iRevisionNum);
        $oConfig->setConfigParam('sShopDir', dirname($sFilePath));
        $this->assertEquals($iRevisionNum, $oConfig->getRevision());
        unlink($sFilePath);
    }

    public function testGetRevision_NoFile()
    {
        $oConfig = new oxConfig();
        $sDir = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/downloads/';
        $oConfig->setConfigParam('sShopDir', $sDir);
        $this->assertFalse($oConfig->getRevision());
    }

    public function testGetPackageInfo_FileExists()
    {
        $oConfig = new oxConfig();
        $sFileName = 'pkg.info';
        $sFileContent = 'Inserting test string';
        $sFilePath = $this->createFile($sFileName, $sFileContent);
        $oConfig->setConfigParam('sShopDir', dirname($sFilePath));
        $this->assertEquals($sFileContent, $oConfig->getPackageInfo());
        unlink($sFilePath);
    }

    public function testGetPackageInfo_NoFile()
    {
        $oConfig = new oxConfig();
        $sDir = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/downloads/';
        $oConfig->setConfigParam('sShopDir', $sDir);
        $this->assertFalse($oConfig->getPackageInfo());
    }

    public function testGetEditionNotEmpty()
    {
        $this->assertNotEquals('', $this->getConfig()->getEdition());
    }

    public function testGetFullEdition()
    {
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
        //at least version 4.0.0.0 (should assert corerctly for higher numbers as well)
        $this->assertTrue(version_compare($this->getConfig()->getVersion(), '4.0.0.0') >= 0);
    }


    public function testGetDir_level5()
    {
        $sTestDir = getTestsBasePath() . '/unit/';

        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test1', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . '1/de/test1/text.txt', $sDir);
    }

    public function testGetDir_delvel4()
    {
        $sTestDir = getTestsBasePath() . '/unit/';

        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test2', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . '1/test2/text.txt', $sDir);
    }

    public function testGetDir_level3()
    {
        $sTestDir = getTestsBasePath() . '/unit/';

        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test2a', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'de/test2a/text.txt', $sDir);
    }

    public function testGetDir_delvel2()
    {
        $sTestDir = getTestsBasePath() . '/unit/';

        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test3', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'test3/text.txt', $sDir);
    }

    public function testGetDir_delvel1()
    {
        $sTestDir = getTestsBasePath() . '/unit/';

        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . $this->_getOutPath($oConfig, 'test4', false);

        $sDir = $oConfig->getDir('text.txt', 'test4', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'text.txt', $sDir);
    }

    public function testGetDir_delvel0()
    {
        $sTestDir = getTestsBasePath() . '/unit/';

        $oConfig = $this->getMock('oxConfig', array('getOutDir'));
        $oConfig->expects($this->any())->method('getOutDir')->will($this->returnValue($sTestDir . 'out/'));
        $oConfig->init();

        $sOutDir = $sTestDir . "out/";

        $sDir = $oConfig->getDir('text.txt', 'test5', false, 0, 1, 'test4');
        $this->assertEquals($sOutDir . 'de/test5/text.txt', $sDir);
    }




    public function testGetOutDir()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
        $oConfig->setConfigParam('sShopURL', 'testUrl/');
        $this->assertEquals('testUrl/out/', $oConfig->getOutUrl(false, true, true));
    }

    public function testGetOutUrlIsSslFromParam()
    {
        $oConfig = new oxConfig();
        $oConfig->setConfigParam('sSSLShopURL', 'testSslUrl/');
        $this->assertEquals('testSslUrl/out/', $oConfig->getOutUrl(true, false, false));
    }

    /**
     * Testing getPicturePath getter
     */
    public function testGetPicturePath()
    {
        $oConfig = new oxConfig();
        $oConfig->init();

        $sDir = $oConfig->getConfigParam('sShopDir') . 'out/pictures/';


        $this->assertEquals($sDir, $oConfig->getPicturePath(null, false));
    }

    /**
     * Testing getPictureUrl getter
     */
    public function testGetPictureUrl()
    {
        $oConfig = new oxConfig();
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

        $oConfig = new oxConfig();
        $oConfig->init();

        $this->assertEquals($sDir, $oConfig->getPictureUrl("/test.gif", false));
    }

    public function testGetPictureUrlFormerTplSupport()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('blFormerTplSupport', true);
        //$this->assertEquals( '', $oConfig->getPictureUrl( "test.gif", false) );
        $this->assertContains('nopic.jpg', $oConfig->getPictureUrl("test.gif", false));
    }

    public function testGetPictureUrlNeverEmptyString()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam('blFormerTplSupport', true);
        $this->assertNotEquals('', $oConfig->getPictureUrl("test.gif", false));
        $this->assertContains('master/nopic.jpg', $oConfig->getPictureUrl("test.gif", false));
    }

    public function testgetPictureUrlForBugEntry0001557()
    {
        $myConfig = $this->getConfig();

        $oConfig = new oxConfig();
        $oConfig->init();
        $oConfig->setConfigParam("sAltImageDir", false);
        $oConfig->setConfigParam("blFormerTplSupport", false);

        $sNoPicUrl = $myConfig->getConfigParam("sShopURL") . "out/pictures/master/nopic.jpg";


        $this->assertEquals($sNoPicUrl, $oConfig->getPictureUrl("unknown.file", true));
    }

    public function testGetTemplateBase()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals("application/views/admin/", $oConfig->getTemplateBase(true));
    }

    public function testGetResourcePath()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopDir') . "out/admin/src/main.css", $oConfig->getResourcePath("main.css", true));
    }

    public function testGetResourceDir()
    {
        $oConfig = new oxConfig();
        $oConfig->init();
        $this->assertEquals($oConfig->getConfigParam('sShopDir') . "out/admin/src/", $oConfig->getResourceDir(true));
    }

    public function testGetResourceUrl()
    {
        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
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

        $oConfig = new oxConfig();
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
        $oConfig = new oxConfig();
        $sQ = " DECODE( oxvarvalue, '" . $oConfig->getConfigParam('sConfigKey') . "') ";
        $this->assertEquals($sQ, $oConfig->getDecodeValueQuery());
    }

    public function testGetShopMainUrl()
    {
        $oConfig = new oxConfig();

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
     * Checks if shop license has demo mode
     */
    public function testHasDemoKey()
    {
        return;
        // all modules off
        $oSerial = $this->getMock('oxSerial', array("isFlagEnabled"));
        $oSerial->expects($this->once())->method('isFlagEnabled')->will($this->returnValue(true));

        $oConfig = $this->getMock('oxconfig', array("getSerial"));
        $oConfig->expects($this->once())->method('getSerial')->will($this->returnValue($oSerial));

        $this->assertTrue($oConfig->hasDemoKey());
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
        $oConfig = $this->getProxyClass('oxconfig');
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
        $sDir = getShopBasePath();
        $sCustConfig = $sDir . "/cust_config.inc.php";

        $handle = fopen($sCustConfig, "w");
        chmod($sCustConfig, 0777);

        $data = '<?php $this->custVar = test;';
        fwrite($handle, $data);

        $oConfig = $this->getProxyClass('oxconfig');
        $oConfig->_loadVarsFromFile();
        $sVar = $oConfig->getConfigParam("custVar");

        $this->assertSame("test", $sVar);
    }

    /**
     * Testing input processor. Checking 3 cases - passing object, array, string.
     *
     */
    public function testCheckParamSpecialChars()
    {
        $oVar = new stdClass();
        $oVar->xxx = 'yyy';
        $aVar = array('&\\o<x>i"\'d' . chr(0));
        $sVar = '&\\o<x>i"\'d' . chr(0);
        $oConfig = oxRegistry::getConfig();
        // object must came back the same
        $this->assertEquals($oVar, $oConfig->checkParamSpecialChars($oVar));

        // array items comes fixed
        $this->assertEquals(array("&amp;&#092;o&lt;x&gt;i&quot;&#039;d"), $oConfig->checkParamSpecialChars($aVar));

        // string comes fixed
        $this->assertEquals('&amp;&#092;o&lt;x&gt;i&quot;&#039;d', $oConfig->checkParamSpecialChars($sVar));
    }

    /**
     * Testing input processor. Checking array, if few values must not be checked.
     *
     */
    public function testCheckParamSpecialCharsForArray()
    {
        $aValues = array('first' => 'first char &', 'second' => 'second char &', 'third' => 'third char &');
        $aRaw = array('first', 'third');
        // object must came back the same
        $aRet = oxRegistry::getConfig()->checkParamSpecialChars($aValues, $aRaw);
        $this->assertEquals($aValues['first'], $aRet['first']);
        $this->assertEquals('second char &amp;', $aRet['second']);
        $this->assertEquals($aValues['third'], $aRet['third']);
    }

    /**
     * Test if checkParamSpecialChars also can fix arrays
     *
     */
    public function testCheckParamSpecialCharsAlsoFixesArrayKeys()
    {
        $test = array(
            array(
                'data'   => array('asd&' => 'a%&'),
                'result' => array('asd&amp;' => 'a%&amp;'),
            ),
            array(
                'data'   => 'asd&',
                'result' => 'asd&amp;',
            )
        );
        $oConfig = $this->getConfig();
        foreach ($test as $check) {
            $this->assertEquals($check['result'], oxRegistry::getConfig()->checkParamSpecialChars($check['data']));
        }
    }

    /**
     * @return array
     */
    public function providerCheckParamSpecialChars_newLineExist_newLineChanged()
    {
        return array(
            array("\r", '&#13;'),
            array("\n", '&#10;'),
            array("\r\n", '&#13;&#10;'),
            array("\n\r", '&#10;&#13;'),
        );
    }

    /**
     * @dataProvider providerCheckParamSpecialChars_newLineExist_newLineChanged
     */
    public function testCheckParamSpecialChars_newLineExist_newLineChanged($sNewLineCharacter, $sEscapedNewLineCharacter)
    {
        $oVar = new stdClass();
        $oVar->xxx = "text" . $sNewLineCharacter;
        $aVar = array("text" . $sNewLineCharacter);
        $sVar = "text" . $sNewLineCharacter;

        $oConfig = oxRegistry::getConfig();
        // object must came back the same
        $this->assertEquals($oVar, $oConfig->checkParamSpecialChars($oVar));

        // array items comes fixed
        $this->assertEquals(array("text" . $sEscapedNewLineCharacter), $oConfig->checkParamSpecialChars($aVar));

        // string comes fixed
        $this->assertEquals("text" . $sEscapedNewLineCharacter, $oConfig->checkParamSpecialChars($sVar));
    }


    /**
     * Testing config init - loading config vars returns no result
     */
    public function testInit_noValuesFromConfig()
    {
        $oConfig = $this->getMock("oxconfig", array("_loadVarsFromDb"));
        $oConfig->expects($this->once())->method('_loadVarsFromDb')->will($this->returnValue(false));

        $this->assertFalse($oConfig->init());
    }

    /**
     * Testing config parameters getter
     */
    public function testInit_noShopId()
    {
        $oConfig = $this->getMock("oxconfig", array("getShopId"));
        $oConfig->expects($this->once())->method('getShopId')->will($this->returnValue(false));

        $this->assertFalse($oConfig->init());
    }

    /**
     * @dataProvider getSystemConfigurationParameters
     */
    public function testSaveSystemConfigurationParameterInMainShop($sType, $sName, $sValue)
    {
        $oConfig = new oxConfig();
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

        $oConfig = new oxConfig();
        $oConfig->saveSystemConfigParameter($sType, $sName, $sValue);

        if ($sType == 'num') {
            $this->assertEquals((float) $sValue, $oConfig->getSystemConfigParameter($sName));
        } else {
            $this->assertEquals($sValue, $oConfig->getSystemConfigParameter($sName));
        }

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
}
