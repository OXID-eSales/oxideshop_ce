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

class testOxUtils extends oxUtils
{

    public function setNonPublicVar($name, $value)
    {
        $this->$name = $value;
    }

    public function getNonPublicVar($name, $value)
    {
        $this->$name = $value;
    }

    public function __call($sMethod, $aArgs)
    {
        if (substr($sMethod, 0, 4) == "UNIT") {
            $sMethod = str_replace("UNIT", "_", $sMethod);
        }
        if (method_exists($this, $sMethod)) {
            return call_user_func_array(array(& $this, $sMethod), $aArgs);
        }

        throw new oxSystemComponentException("Function '$sMethod' does not exist or is not accessible! (" . __CLASS__ . ")" . PHP_EOL);
    }
}

class Unit_Core_oxutilsTest extends OxidTestCase
{

    protected $_sTestLogFileName = null;

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRegistry::getUtils()->commitFileCache();

        clearstatcache();
        //removing test files from tmp dir
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sCompileDir') . "*testFileCache*.txt";
        $aPathes = glob($sFilePath);
        if (is_array($aPathes)) {
            foreach ($aPathes as $sFilename) {
                @unlink($sFilename);
            }
        }

        if ($this->_sTestLogFileName !== null) {
            unlink($this->_sTestLogFileName);
            $this->_sTestLogFileName = null;
        }
        if (file_exists('tmp_testCacheName')) {
            unlink('tmp_testCacheName');
        }

        $oUtils = oxRegistry::getUtils();
        $sFileName = $oUtils->getCacheFilePath("testVal", false, 'php');
        if (file_exists($sFileName)) {
            unlink($sFileName);
        }

        $sFileName = $oUtils->getCacheFilePath('testCache1');
        if (file_exists($sFileName)) {
            unlink($sFileName);
        }

        parent::tearDown();
    }

    /**
     *
     * @return unknown_type
     */
    public function testExtractDomain()
    {
        $oUtils = new oxUtils();
        $this->assertEquals("oxid-esales.com", $oUtils->extractDomain("www.oxid-esales.com"));
        $this->assertEquals("oxid-esales.com", $oUtils->extractDomain("oxid-esales.com"));
        $this->assertEquals("127.0.0.1", $oUtils->extractDomain("127.0.0.1"));
        $this->assertEquals("oxid-esales.com", $oUtils->extractDomain("ssl.oxid-esales.com"));
        $this->assertEquals("oxid-esales", $oUtils->extractDomain("oxid-esales"));
    }

    public function testShowMessageAndExit()
    {
        $oSession = $this->getMock("oxSession", array("freeze"));
        $oSession->expects($this->once())->method('freeze');

        $oUtils = $this->getMock("oxUtils", array("getSession", "commitFileCache"));
        $oUtils->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oUtils->expects($this->once())->method('commitFileCache');

        $oUtils->showMessageAndExit("");
    }

    public function testWriteToLog()
    {
        $sLogMessage = $sLogFileName = md5(uniqid(rand(), true));

        $oUtils = new oxUtils();
        $oUtils->writeToLog($sLogMessage, $sLogFileName);

        $this->_sTestLogFileName = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'log/' . $sLogFileName;

        clearstatcache();
        $this->assertTrue(file_exists($this->_sTestLogFileName));
        $this->assertEquals($sLogMessage, file_get_contents($this->_sTestLogFileName));
    }

    public function testSetLangCache()
    {
        $aLangCache = array("ggg" => "bbb");
        $sCacheName = 'tmp_testCacheName';
        $sCache = "<?php\n\$aLangCache = " . var_export($aLangCache, true) . ";";

        $oUtils = $this->getMock('oxutils', array('getCacheFilePath'));
        $oUtils->expects($this->once())->method('getCacheFilePath')->with($this->equalTo($sCacheName))->will($this->returnValue("tmp_testCacheName"));
        $oUtils->setLangCache($sCacheName, $aLangCache);
    }


    public function testgetLangCache()
    {
        $sCacheName = time();
        $aLangCache = array("ggg" => "bbb");

        $oUtils = new oxutils();
        $oUtils->setLangCache($sCacheName, $aLangCache);

        $this->assertEquals($aLangCache, $oUtils->getLangCache($sCacheName));
    }

    /**
     * Seo mode checker
     */
    public function testSeoIsActive()
    {
        // as now SEO is on by default
        $oUtils = new oxutils();

        $oConfig = $oUtils->getConfig();
        $oConfig->setConfigParam('aSeoModes', array('testshop' => array(2 => false, 3 => true)));

        $this->assertTrue($oUtils->seoIsActive());

        // cache test
        $this->assertTrue($oUtils->seoIsActive(false, 'testshop', 2));
        $this->assertFalse($oUtils->seoIsActive(true, 'testshop', 2));

        // config test
        $this->assertTrue($oUtils->seoIsActive(true, 'testshop', 3));
    }

    public function testGetArrFldName()
    {
        $sTestString = ".S.o.me.. . Na.me.";
        $sShouldBeResult = "__S__o__me____ __ Na__me__";

        $this->assertEquals($sShouldBeResult, oxRegistry::getUtils()->getArrFldName($sTestString));
    }

    public function optionsAndValuesProvider()
    {
        return array(
            array(true, true, 1),
            array(true, false, 1.2),
            array(false, true, 1.2),
            array(false, false, 1),
        );
    }

    /**
     * Tests how selection lists are outputted with show as net price option, and enter as net price option.
     * Tests all 4 combination of both options.
     *
     * @dataProvider optionsAndValuesProvider
     */
    public function testValueCalculationBasedOnOptions($blEnterNetPrice, $blShowNetPrice, $iVatModifier)
    {
        $myConfig = oxRegistry::getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectLists', true);
        modConfig::getInstance()->setConfigParam('bl_perfUseSelectlistPrice', true);

        modConfig::getInstance()->setConfigParam('blEnterNetPrice', $blEnterNetPrice);
        modConfig::getInstance()->setConfigParam('blShowNetPrice', $blShowNetPrice);

        $sTestString = "one!P!99.5%__oneValue@@two!P!12,41__twoValue@@three!P!-5,99__threeValue@@Lagerort__Lager 1@@";
        $aResult = oxRegistry::getUtils()->assignValuesFromText($sTestString, 20);

        $aShouldBe = array();
        $oObject = new stdClass();
        $oObject->price = '99.5';
        $oObject->priceUnit = '%';
        $oObject->fprice = '99.5%';
        $oObject->name = 'one +99.5%';
        $oObject->value = 'oneValue';
        $aShouldBe[] = $oObject;

        $dPrice = str_replace('.', ',', $this->_alterPrice(12.41, $iVatModifier, $blShowNetPrice, $blEnterNetPrice));

        $oObject = new stdClass();
        $oObject->price = '12.41';
        $oObject->fprice = '12,41';
        $oObject->priceUnit = 'abs';
        $oObject->name = "two +$dPrice " . $oCurrency->sign;
        $oObject->value = 'twoValue';
        $aShouldBe[] = $oObject;

        $dPrice = str_replace('.', ',', $this->_alterPrice(5.99, $iVatModifier, $blShowNetPrice, $blEnterNetPrice));

        $oObject = new stdClass();
        $oObject->price = '-5.99';
        $oObject->fprice = '-5,99';
        $oObject->priceUnit = 'abs';
        $oObject->name = "three -$dPrice " . $oCurrency->sign;
        $oObject->value = 'threeValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->name = 'Lagerort';
        $oObject->value = 'Lager 1';
        $aShouldBe[] = $oObject;

        $this->assertEquals($aShouldBe, $aResult);
    }

    /**
     * Helper function to alter prices for checking correct vat prices
     *
     *
     */
    protected function _alterPrice($dPrice, $iVatModifier, $blShowNetPrice, $blEnterNetPrice)
    {
        if ($blEnterNetPrice && !$blShowNetPrice) {
            $dPrice *= $iVatModifier;
        } else {
            $dPrice /= $iVatModifier;
        }

        return round($dPrice, 2);
    }

    /**
     * Check of full version processor
     */
    public function testAssignValuesFromTextFull()
    {
        $myConfig = oxRegistry::getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectLists', true);
        modConfig::getInstance()->setConfigParam('bl_perfUseSelectlistPrice', true);

        $sTestString = "one!P!99.5%__oneValue@@two!P!12,41__twoValue@@three!P!-5,99__threeValue@@Lagerort__Lager 1@@";
        $aResult = oxRegistry::getUtils()->assignValuesFromText($sTestString);

        $aShouldBe = array();
        $oObject = new stdClass();
        $oObject->price = '99.5';
        $oObject->priceUnit = '%';
        $oObject->fprice = '99.5%';
        $oObject->name = 'one +99.5%';
        $oObject->value = 'oneValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->price = '12.41';
        $oObject->fprice = '12,41';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'two +12,41 ' . $oCurrency->sign;
        $oObject->value = 'twoValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->price = '-5.99';
        $oObject->fprice = '-5,99';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'three -5,99 ' . $oCurrency->sign;
        $oObject->value = 'threeValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->name = 'Lagerort';
        $oObject->value = 'Lager 1';
        $aShouldBe[] = $oObject;

        $this->assertEquals($aShouldBe, $aResult);
    }

    /**
     * Check of full version processor
     */
    public function testAssignValuesFromTextFullIfPriceIsZero()
    {
        $myConfig = oxRegistry::getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectLists', true);
        modConfig::getInstance()->setConfigParam('bl_perfUseSelectlistPrice', true);

        $sTestString = "one__oneValue@@two!P!0.00__twoValue@@";
        $aResult = oxRegistry::getUtils()->assignValuesFromText($sTestString);

        $aShouldBe = array();
        $oObject = new stdClass();
        $oObject->name = 'one';
        $oObject->value = 'oneValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->price = '0.00';
        $oObject->fprice = '0,00';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'two';
        $oObject->value = 'twoValue';
        $aShouldBe[] = $oObject;

        $this->assertEquals($aShouldBe, $aResult);
    }

    /**
     * Check of full version processor (If NetPrice Is Entered)
     *  FS#2616
     */
    public function testAssignValuesFromTextFullWithVat()
    {
        $myConfig = oxRegistry::getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectLists', true);
        modConfig::getInstance()->setConfigParam('bl_perfUseSelectlistPrice', true);
        modConfig::getInstance()->setConfigParam('blEnterNetPrice', true);

        $sTestString = "one!P!99.5%__oneValue@@two!P!12,41__twoValue@@";
        $aResult = oxRegistry::getUtils()->assignValuesFromText($sTestString, 19);

        $aShouldBe = array();
        $oObject = new stdClass();
        $oObject->price = '99.5';
        $oObject->priceUnit = '%';
        $oObject->fprice = '99.5%';
        $oObject->name = 'one +99.5%';
        $oObject->value = 'oneValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->price = '12.41';
        $oObject->fprice = '12,41';
        $oObject->priceUnit = 'abs';
        $oObject->name = 'two +14,77 ' . $oCurrency->sign;
        $oObject->value = 'twoValue';
        $aShouldBe[] = $oObject;

        $this->assertEquals($aShouldBe, $aResult);
    }

    /**
     * Check of simplified version processor
     */
    public function testAssignValuesFromTextLite()
    {
        $myConfig = oxRegistry::getConfig();
        $oCurrency = $myConfig->getActShopCurrencyObject();

        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectLists', false);
        modConfig::getInstance()->setConfigParam('bl_perfUseSelectlistPrice', false);

        $sTestString = "one!P!99.5%__oneValue@@two!P!12,41__twoValue@@three!P!-5,99__threeValue@@Lagerort__Lager 1@@";
        $aResult = oxRegistry::getUtils()->assignValuesFromText($sTestString);

        $aShouldBe = array();
        $oObject = new stdClass();
        $oObject->name = 'one';
        $oObject->value = 'oneValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->name = 'two';
        $oObject->value = 'twoValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->name = 'three';
        $oObject->value = 'threeValue';
        $aShouldBe[] = $oObject;

        $oObject = new stdClass();
        $oObject->name = 'Lagerort';
        $oObject->value = 'Lager 1';
        $aShouldBe[] = $oObject;

        $this->assertEquals($aShouldBe, $aResult);
    }

    public function testAssignValuesToText()
    {

        $aTestArray = array('one' => 11, 'two' => 22, 'three' => 33, 'fourfour' => 44.44);
        $sResult = oxRegistry::getUtils()->assignValuesToText($aTestArray);
        $sShouldBeResult = "one__11@@two__22@@three__33@@fourfour__44.44@@";
        $sShouldNotBeResult = "on__11@@two__22@@three__33@@fourfour__44.44@@";
        $this->assertEquals($sShouldBeResult, $sResult);
        $this->assertNotEquals($sShouldNotBeResult, $sResult);
    }

    public function testCurrency2Float()
    {
        $oActCur = oxRegistry::getConfig()->getActShopCurrencyObject();
        $fFloat = oxRegistry::getUtils()->currency2Float("10.322,32", $oActCur);
        $this->assertEquals($fFloat, 10322.32);
        $fFloat = oxRegistry::getUtils()->currency2Float("10,322.32", $oActCur);
        $this->assertEquals($fFloat, (float) "10.322.32");
        $fFloat = oxRegistry::getUtils()->currency2Float("10 322,32", $oActCur);
        $this->assertEquals($fFloat, (float) "10322.32");
        $fFloat = oxRegistry::getUtils()->currency2Float("10 322.32", $oActCur);
        $this->assertEquals($fFloat, (float) "10322.32");
    }

    /**
     * Testing if shop var saver writes num value with valid string to config correctly
     */
    public function testString2Float()
    {
        $oUtils = oxRegistry::getUtils();
        $fFloat = $oUtils->string2Float("10.322,32");
        $this->assertEquals($fFloat, 10322.32);
        $fFloat = $oUtils->string2Float("10,322.32");
        $this->assertEquals($fFloat, 10322.32);
        $fFloat = $oUtils->string2Float("10322,32");
        $this->assertEquals($fFloat, 10322.32);
        $fFloat = $oUtils->string2Float("10322.32");
        $this->assertEquals($fFloat, 10322.32);
        $fFloat = $oUtils->string2Float("10.32225");
        $this->assertEquals($fFloat, 10.32225);
        $fFloat = $oUtils->string2Float("10 000.32225");
        $this->assertEquals($fFloat, 10000.32225);
        $fFloat = $oUtils->string2Float("10 000.00");
        $this->assertEquals($fFloat, 10000);
    }

    /**
     * SE check, non admin mode, will cache result
     */
    public function testIsSearchEngineNonAdminNonSE()
    {
        // cleaning ..
        $myConfig = oxRegistry::getConfig();

        modConfig::getInstance()->setConfigParam('iDebug', 1);
        modConfig::getInstance()->setConfigParam('aRobots', array());

        $oUtils = $this->getMock('oxUtils', array('isAdmin'));
        $oUtils->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertFalse($oUtils->isSearchEngine('xxx'));
        $this->assertFalse($oUtils->isSearchEngine('googlebot'));
    }

    public function testIsSearchEngineNonAdminSE()
    {
        // cleaning ..
        $myConfig = oxRegistry::getConfig();

        modConfig::getInstance()->setConfigParam('iDebug', 0);
        modConfig::getInstance()->setConfigParam('aRobots', array('googlebot', 'xxx'));

        $oUtils = $this->getMock('oxUtils', array('isAdmin'));
        $oUtils->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertTrue($oUtils->isSearchEngine('googlebot'));
        $this->assertTrue($oUtils->isSearchEngine('xxx'));
    }

    public function testIsSearchEngineAdminAndDebugOn()
    {
        // cleaning ..
        $myConfig = oxRegistry::getConfig();

        modConfig::getInstance()->setConfigParam('iDebug', 1);
        modConfig::getInstance()->setConfigParam('aRobots', array('googlebot', 'xxx'));

        $oUtils = $this->getMock('oxUtils', array('isAdmin'));
        $oUtils->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $this->assertFalse($oUtils->isSearchEngine('xxx'));
        $this->assertFalse($oUtils->isSearchEngine('googlebot'));
    }

    public function testIsSearchEngineAdminAndDebugOff()
    {
        // cleaning ..
        $myConfig = oxRegistry::getConfig();

        modConfig::getInstance()->setConfigParam('iDebug', 1);
        modConfig::getInstance()->setConfigParam('aRobots', array('googlebot', 'xxx'));

        $oUtils = $this->getMock('oxUtils', array('isAdmin'));
        $oUtils->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $this->assertFalse($oUtils->isSearchEngine('googlebot'));
        $this->assertFalse($oUtils->isSearchEngine('xxx'));
    }

    public function testIsValidEmail()
    {
        $this->assertTrue(oxRegistry::getUtils()->isValidEmail('mathias.krieck@oxid-esales.com'));
        $this->assertTrue(oxRegistry::getUtils()->isValidEmail('mytest@com.org'));
        $this->assertFalse(oxRegistry::getUtils()->isValidEmail('�mathias.krieck@oxid-esales.com'));
        $this->assertFalse(oxRegistry::getUtils()->isValidEmail('my/test@com.org'));
        $this->assertFalse(oxRegistry::getUtils()->isValidEmail('@com.org'));
        $this->assertFalse(oxRegistry::getUtils()->isValidEmail('mytestcom.org'));
        $this->assertFalse(oxRegistry::getUtils()->isValidEmail('mytest@com'));
    }

    public function testLoadAdminProfile()
    {
        $aProfiles = oxRegistry::getUtils()->loadAdminProfile(array('640x480', '14'));
        $this->assertContains('640x480', $aProfiles[0]);

        $aProfiles = oxRegistry::getUtils()->loadAdminProfile(v);
        $this->assertNull($aProfiles);

        $aProfiles = oxRegistry::getUtils()->loadAdminProfile("teststring");
        $this->assertNull($aProfiles);
    }

    public function testFRound()
    {
        $myConfig = oxRegistry::getConfig();

        $this->assertEquals('9.84', oxRegistry::getUtils()->fRound('9.844'));
        $this->assertEquals('9.85', oxRegistry::getUtils()->fRound('9.845'));
        $this->assertEquals('9.85', oxRegistry::getUtils()->fRound('9.849'));
        $this->assertEquals('0', oxRegistry::getUtils()->fRound('blafoo'));
        $this->assertEquals('9', oxRegistry::getUtils()->fRound('9,849'));

        //negative
        $this->assertEquals('-9.84', oxRegistry::getUtils()->fRound('-9.844'));
        $this->assertEquals('-9.85', oxRegistry::getUtils()->fRound('-9.845'));
        $this->assertEquals('-9.85', oxRegistry::getUtils()->fRound('-9.849'));
        $this->assertEquals('-9', oxRegistry::getUtils()->fRound('-9,849'));


        $aCur = $myConfig->getCurrencyArray();
        $oCur = $aCur[1];
        $this->assertEquals('9.84', oxRegistry::getUtils()->fRound('9.844', $oCur));
        $this->assertEquals('9.85', oxRegistry::getUtils()->fRound('9.845', $oCur));
        $this->assertEquals('9.85', oxRegistry::getUtils()->fRound('9.849', $oCur));
        $this->assertEquals('0', oxRegistry::getUtils()->fRound('blafoo', $oCur));
        $this->assertEquals('9', oxRegistry::getUtils()->fRound('9,849', $oCur));

        $this->assertEquals('-9.84', oxRegistry::getUtils()->fRound('-9.844', $oCur));
        $this->assertEquals('-9.85', oxRegistry::getUtils()->fRound('-9.845', $oCur));
        $this->assertEquals('-9.85', oxRegistry::getUtils()->fRound('-9.849', $oCur));
        $this->assertEquals('-9', oxRegistry::getUtils()->fRound('-9,849', $oCur));

        $this->assertEquals('1522.61', oxRegistry::getUtils()->fRound('1522.605', $oCur));

    }

    public function testToFromStaticCache()
    {
        $oUtils = new oxutils();

        $sName = "SomeName";
        $mContent = "SomeContent";
        $sKey = "SomeKey";

        $oUtils->toStaticCache($sName, $mContent);
        $this->assertEquals($mContent, $oUtils->fromStaticCache($sName));

        $sName = "SomeOtherName";
        $mContent = "SomeOtherContent";
        $sKey = "SomeOtherKey";

        $oUtils->toStaticCache($sName, $mContent, $sKey);
        $aOut = $oUtils->fromStaticCache($sName);
        $this->assertEquals($mContent, $aOut[$sKey]);

        // testing non existing
        $this->assertNull($oUtils->fromStaticCache(time()));
    }

    public function testCleanStaticCacheSpecific()
    {
        $oUtils = new oxutils();

        $sName1 = "SomeName";
        $mContent1 = "SomeContent";
        $sKey1 = "SomeKey";

        $sName2 = "SomeName2";
        $mContent2 = "SomeContent2";
        $sKey2 = "SomeKey2";

        $oUtils->toStaticCache($sName1, $mContent1);
        $oUtils->toStaticCache($sName2, $mContent2);
        $oUtils->cleanStaticCache($sName2);

        $this->assertEquals($mContent1, $oUtils->fromStaticCache($sName1));
        $this->assertEquals(null, $oUtils->fromStaticCache($mContent1));

    }

    public function testCleanStaticCacheFullClean()
    {

        $oUtils = new oxutils();

        $sName1 = "SomeName";
        $mContent1 = "SomeContent";
        $sKey1 = "SomeKey";

        $sName2 = "SomeName2";
        $mContent2 = "SomeContent2";
        $sKey2 = "SomeKey2";

        $oUtils->toStaticCache($sName1, $mContent1);
        $oUtils->toStaticCache($sName2, $mContent2);
        $oUtils->cleanStaticCache();

        $this->assertEquals(null, $oUtils->fromStaticCache($sName1));
        $this->assertEquals(null, $oUtils->fromStaticCache($sName2));
    }

    public function testToFileCacheFileCache()
    {
        $sName = "testFileCache";
        $sInput = "test_test_test";

        $oUtils = new oxutils();
        $oUtils->toFileCache($sName, $sInput);
        $this->assertEquals($sInput, $oUtils->fromFileCache($sName));
    }

    public function testToFileCacheFileCacheDoubleWrite1()
    {
        $sName1 = "testFileCache";
        $sName2 = "testFileCache2";
        $sInput1 = "test_test_test";
        $sInput2 = "test_test";

        $oUtils = new oxutils();
        $oUtils->toFileCache($sName1, $sInput1);
        $oUtils->toFileCache($sName2, $sInput2);
        $this->assertEquals($sInput1, $oUtils->fromFileCache($sName1));
        $this->assertEquals($sInput2, $oUtils->fromFileCache($sName2));
    }

    public function testToFileCacheFileCacheDoubleWrite2()
    {
        $sName1 = "testFileCache";
        $sName2 = "testFileCache2";
        $sInput1 = "test_test_test";
        $sInput2 = "test_test";

        $oUtils = new oxutils();
        $oUtils->toFileCache($sName1, $sInput1);
        $this->assertEquals($sInput1, $oUtils->fromFileCache($sName1));
        $oUtils->toFileCache($sName2, $sInput2);
        $this->assertEquals($sInput2, $oUtils->fromFileCache($sName2));
    }

    public function testToFileCacheFileCacheDoubleWrite3()
    {
        $sName1 = "testFileCache1";
        $sName2 = "testFileCache2";
        $sInput1 = "test_test_test";
        $sInput2 = "test_test";

        $oUtils = $this->getProxyClass('oxutils');
        $oUtils->toFileCache($sName1, $sInput1);
        $oUtils->toFileCache($sName2, $sInput2);
        $oUtils->commitFileCache();
        $this->assertEquals($sInput1, $oUtils->fromFileCache($sName1));
        $this->assertEquals($sInput2, $oUtils->fromFileCache($sName2));
    }


    public function testOxResetFileCache()
    {
        $myConfig = oxRegistry::getConfig();
        $sName = "testFileCache";
        $sInput = "test_test_test";

        //getting cached files prefix
        $myUtilsTest = $this->getProxyClass("oxUtils");
        $sFilePath = $myUtilsTest->getCacheFilePath("test");
        $sCacheFilePrefix = preg_replace("/.*\/(ox[^_]*)_.*/", "$1", $sFilePath);

        $oUtils = oxRegistry::getUtils();
        for ($iMax = 0; $iMax < 10; $iMax++) {
            $oUtils->toFileCache($sName . "_" . $iMax, $sInput . "_" . $iMax);
        }
        $oUtils->commitFileCache();

        //checking if test files were written to temp dir
        $sFilePath = $myConfig->getConfigParam('sCompileDir') . "/{$sCacheFilePrefix}_testFileCache*.txt";
        $aPathes = glob($sFilePath);
        $this->assertEquals(10, count($aPathes), "Error writing test files to cache dir");

        //actual test
        $this->assertNull($oUtils->oxResetFileCache());

        $sFilePath = $myConfig->getConfigParam('sCompileDir') . "/{$sCacheFilePrefix}_testFileCache*.txt";
        $aPathes = glob($sFilePath);
        $this->assertTrue($aPathes == null);
    }

    public function testOxResetFileCacheSkipsTablesFieldNames()
    {
        $myConfig = oxRegistry::getConfig();
        $sName = "testFileCache";
        $sInput = "test_test_test";

        //getting cached files prefix
        $myUtilsTest = $this->getProxyClass("oxUtils");
        $sFilePath = $myUtilsTest->getCacheFilePath("test");
        $sCacheFilePrefix = preg_replace("/.*\/(ox[^_]*)_.*/", "$1", $sFilePath);

        //this file must be skipped
        $oUtils = oxRegistry::getUtils();
        $oUtils->toFileCache("fieldnames_testTest", "testCacheValue");
        $oUtils->commitFileCache();

        //checking if test file were written to temp dir
        $sFilePath = $myConfig->getConfigParam('sCompileDir') . "/{$sCacheFilePrefix}_fieldnames_testTest.txt";
        clearstatcache();
        $this->assertTrue(file_exists($sFilePath), "Error writing test files to cache dir");

        for ($iMax = 0; $iMax < 10; $iMax++) {
            $oUtils->toFileCache($sName . "_" . $iMax, $sInput . "_" . $iMax);
        }
        $oUtils->commitFileCache();

        //checking if test files were written to temp dir
        $sFilePath = $myConfig->getConfigParam('sCompileDir') . "/{$sCacheFilePrefix}_testFileCache*.txt";
        $aPathes = glob($sFilePath);
        $this->assertEquals(10, count($aPathes), "Error writing test files to cache dir: " . count($aPathes));

        //actual test
        $this->assertNull($oUtils->oxResetFileCache());

        $sFilePath = $myConfig->getConfigParam('sCompileDir') . "/{$sCacheFilePrefix}_fieldnames_testTest.txt";
        $aPathes = glob($sFilePath);

        @unlink($aPathes[0]); //deleting test cache file
        $this->assertEquals(1, count($aPathes));
    }

    public function testResetTemplateCache()
    {
        $myConfig = oxRegistry::getConfig();

        $oUtils = oxRegistry::getUtils();
        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty(true);
        $sTmpDir = $myConfig->getConfigParam('sCompileDir') . "/smarty/";

        $aTemplates = array('message/success.tpl', 'message/notice.tpl', 'message/errors.tpl',);
        foreach ($aTemplates as $sTpl) {
            $oSmarty->fetch($sTpl);
        }

        $sRemoveTemplate = basename(reset($aTemplates));
        $sLeaveTemplate = basename(array_pop($aTemplates));

        //checking if test files were written to temp dir
        $this->assertEquals(1, count(glob("{$sTmpDir}/*{$sRemoveTemplate}.php")), "File written " . $sRemoveTemplate);
        $this->assertEquals(1, count(glob("{$sTmpDir}/*{$sLeaveTemplate}.php")), "File written " . $sLeaveTemplate);

        //Remove templates
        $this->assertNull($oUtils->resetTemplateCache($aTemplates));

        $this->assertEquals(0, count(glob("{$sTmpDir}/*{$sRemoveTemplate}.php")), "File removed " . $sRemoveTemplate);
        $this->assertEquals(1, count(glob("{$sTmpDir}/*{$sLeaveTemplate}.php")), "File left " . $sLeaveTemplate);
    }

    public function testResetLanguageCache()
    {
        $myConfig = oxRegistry::getConfig();

        $oUtils = oxRegistry::getUtils();
        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty(true);
        $sTmpDir = $myConfig->getConfigParam('sCompileDir');

        $aFiles = array('langcache_1_a', 'langcache_1_b', 'langcache_1_c');
        foreach ($aFiles as $sFile) {
            $oUtils->setLangCache($sFile, array($sFile));
        }

        foreach ($aFiles as $sFile) {
            $this->assertEquals(array($sFile), $oUtils->getLangCache($sFile));
        }

        $this->assertNull($oUtils->resetLanguageCache());

        foreach ($aFiles as $sFile) {
            $this->assertNull($oUtils->getLangCache($sFile));
        }

    }

    public function testGetRemoteCachePath()
    {
        $sTestsPath = getTestsBasePath();
        touch($sTestsPath.'misc/actions_main.inc.php', time(), time());
        $this->assertEquals($sTestsPath.'misc/actions_main.inc.php', oxRegistry::getUtils()->GetRemoteCachePath('http://www.blafoo.null', $sTestsPath.'misc/actions_main.inc.php'));
        //ensure that file is older than 24h
        touch($sTestsPath.'misc/actions_main.inc.php', time() - 90000, time() - 90000);
        $this->assertEquals($sTestsPath.'misc/actions_main.inc.php', oxRegistry::getUtils()->GetRemoteCachePath(oxRegistry::getConfig()->getShopURL(), $sTestsPath.'misc/actions_main.inc.php'));
        touch($sTestsPath.'misc/actions_main.inc.php', time() - 90000, time() - 90000);
        $this->assertEquals($sTestsPath.'misc/actions_main.inc.php', oxRegistry::getUtils()->GetRemoteCachePath('http://www.blafoo.null', $sTestsPath.'misc/actions_main.inc.php'));
        $this->assertEquals(false, oxRegistry::getUtils()->GetRemoteCachePath('http://www.blafoo.null', 'misc/blafoo.test'));
    }

    public function testCheckAccessRights()
    {

        $mySession = oxRegistry::getSession();
        $backUpAuth = $mySession->getVariable("auth");

        $mySession->setVariable("auth", "oxdefaultadmin");
        $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());

        //  self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', 'return 1;'));

        $mySession->setVariable("auth", "oxdefaultadmin");
        $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());
        $mySession->setVariable("auth", "blafooUser");


        //self::$test_sql_used = null;
        modDB::getInstance()->addClassFunction('getOne', create_function('$sql', 'return 0;'));

        $this->assertEquals(false, oxRegistry::getUtils()->checkAccessRights());

        $mySession->setVariable("auth", $backUpAuth);
        modDB::getInstance()->cleanup();
    }

    public function testCheckAccessRightsChecksSubshopAdminShop()
    {

        $mySession = oxRegistry::getSession();
        $backUpAuth = $mySession->getVariable("auth");

        $e = null;
        try {
            modDB::getInstance()->addClassFunction('getOne', create_function('$sql', 'return 1;'));
            $mySession->setVariable("auth", "blafooUser");
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('fnc', 'chshp');
            $this->assertEquals(false, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('fnc', null);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());

            modConfig::setRequestParameter('actshop', 1);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('actshop', 2);
            $this->assertEquals(false, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('actshop', null);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());

            modConfig::setRequestParameter('shp', 1);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('shp', 2);
            $this->assertEquals(false, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('shp', null);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());

            modConfig::setRequestParameter('currentadminshop', 1);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('currentadminshop', 2);
            $this->assertEquals(false, oxRegistry::getUtils()->checkAccessRights());
            modConfig::setRequestParameter('currentadminshop', null);
            $this->assertEquals(true, oxRegistry::getUtils()->checkAccessRights());
        } catch (Exception  $e) {

        }


        $mySession->setVariable("auth", $backUpAuth);
        modDB::getInstance()->cleanup();


        if ($e) {
            throw $e;
        }
    }

    public function testIsValidAlpha()
    {

        $this->assertEquals(true, oxRegistry::getUtils()->isValidAlpha('oxid'));
        $this->assertEquals(true, oxRegistry::getUtils()->isValidAlpha('oxid1'));
        $this->assertEquals(false, oxRegistry::getUtils()->isValidAlpha('oxid.'));
        $this->assertEquals(false, oxRegistry::getUtils()->isValidAlpha('oxid{'));
        $this->assertEquals(true, oxRegistry::getUtils()->isValidAlpha('oxi_d'));
        $this->assertEquals(false, oxRegistry::getUtils()->isValidAlpha('ox\\id'));
    }

    public function testAddUrlParameters()
    {
        $oUtils = new oxUtils();

        $sURL = 'http://www.url.com';
        $aParams = array('string' => 'someString', 'bool1' => false, 'bool2' => true, 'int' => 1234, 'float' => 123.45, 'negfloat' => -123.45);

        $sReturnURL = "http://www.url.com?string=someString&bool1=&bool2=1&int=1234&float=123.45&negfloat=-123.45";
        $this->assertEquals($sReturnURL, $oUtils->UNITaddUrlParameters($sURL, $aParams));

        $sURL = 'http://www.url.com/index.php?cl=aaa';
        $sReturnURL = "http://www.url.com/index.php?cl=aaa&string=someString&bool1=&bool2=1&int=1234&float=123.45&negfloat=-123.45";
        $this->assertEquals($sReturnURL, $oUtils->UNITaddUrlParameters($sURL, $aParams));

    }

    public function testOxMimeContentType()
    {
        $oUtils = new oxUtils();
        $sFile = 'asdnasd/asdasd.asd.ad.ad.asd.gif';
        $this->assertEquals('image/gif', $oUtils->oxMimeContentType($sFile));

        $sFile = 'asdnasd/asdasd.asd.ad.ad.asd.jpeg';
        $this->assertEquals('image/jpeg', $oUtils->oxMimeContentType($sFile));

        $sFile = 'asdnasd/asdasd.asd.ad.ad.asd.jpg';
        $this->assertEquals('image/jpeg', $oUtils->oxMimeContentType($sFile));

        $sFile = 'asdnasd/asdasd.asd.ad.ad.asd.png';
        $this->assertEquals('image/png', $oUtils->oxMimeContentType($sFile));

        $sFile = 'asdnasd/asdasd.asd.ad.ad.asdjpeg';
        $this->assertEquals(false, $oUtils->oxMimeContentType($sFile));
        $this->assertEquals(false, $oUtils->oxMimeContentType(''));
    }

    public function testStrManStrRem()
    {
        $sTests = "myblaaFooString!";
        $sKey = "oxid987654321";
        $oUtils = new oxUtils();

        $sCode = $oUtils->strMan($sTests, $sKey);
        $this->assertNotEquals($sTests, $sCode);

        $sCode = $oUtils->strRem($sCode, $sKey);
        $this->assertEquals($sCode, $sTests);

        $sCode = $oUtils->strMan($sTests);
        $this->assertNotEquals($sTests, $sCode);

        $sCode = $oUtils->strRem($sCode);
        $this->assertEquals($sTests, $sCode);
    }

    public function testStrRot13()
    {
        $sTests = "myblaaFooString!";
        $sCode = oxRegistry::getUtils()->strRot13($sTests);
        $this->assertEquals($sCode, "zloynnSbbFgevat!");
    }

    public function testRedirectOffline_WithDefaultHeader()
    {
        $oConfig = $this->getMock('oxConfig', array('getShopUrl'));
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue('http://shopUrl/'));

        $oUtils = $this->getMock('oxutils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo('http://shopUrl/offline.html'), $this->equalTo(false), $this->equalTo(302));
        $oUtils->setConfig($oConfig);

        $oUtils->redirectOffline();
    }

    public function testRedirectOffline_WithDifferentHeader()
    {
        $oConfig = $this->getMock('oxConfig', array('getShopUrl'));
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue('http://shopUrl/'));

        $oUtils = $this->getMock('oxutils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo('http://shopUrl/offline.html'), $this->equalTo(false), $this->equalTo(500));
        $oUtils->setConfig($oConfig);

        $oUtils->redirectOffline(500);
    }

    public function testRedirect()
    {
        $oSession = $this->getMock('oxsession', array('freeze'));
        $oSession->expects($this->once())->method('freeze');

        $oUtils = $this->getMock('oxutils', array('_simpleRedirect', 'getSession'));
        $oUtils->expects($this->once())->method('_simpleRedirect')->with($this->equalTo('url?redirected=1'));
        $oUtils->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oUtils->redirect('url');
    }

    public function providerRedirectCodes()
    {
        return array(
            array(301, 'HTTP/1.1 301 Moved Permanently'),
            array(302, 'HTTP/1.1 302 Found'),
            array(500, 'HTTP/1.1 500 Internal Server Error'),
            array(423958, 'HTTP/1.1 302 Found'),
        );
    }

    /**
     * @param int    $iCode   header code
     * @param string $sHeader formed expected header string
     *
     * @dataProvider providerRedirectCodes
     */
    public function testRedirectCodes($iCode, $sHeader)
    {
        $oSession = $this->getMock('oxsession', array('freeze'));
        $oSession->expects($this->any())->method('freeze');

        // test also any other to redirect only temporary
        $oUtils = $this->getMock('oxutils', array('_simpleRedirect', 'getSession'));
        $oUtils->expects($this->once())->method('_simpleRedirect')->with($this->equalTo('url'), $this->equalTo($sHeader));
        $oUtils->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oUtils->redirect('url', false, $iCode);
    }

    public function testReRedirect()
    {
        modConfig::setRequestParameter('redirected', '1');

        $oUtils = $this->getMock('oxutils', array('_simpleRedirect', '_addUrlParameters', 'getSession'));
        $oUtils->expects($this->never())->method('_simpleRedirect');
        $oUtils->expects($this->never())->method('_addUrlParameters');
        $oUtils->expects($this->never())->method('getSession');
        $oUtils->redirect('url');

    }

    public function testRedirectWithEncodedEntities()
    {
        $oUtils = $this->getMock('oxutils', array('_simpleRedirect'));
        $oUtils->expects($this->once())->method('_simpleRedirect')->with($this->equalTo('url?param1=1&param2=2&param3=3&redirected=1'));
        $oUtils->redirect('url?param1=1&param2=2&amp;param3=3');
    }

    public function testFromFileCacheEmpty()
    {
        $oUtils = new oxutils();
        $sCacheHit = $oUtils->fromFileCache("notexistantkey");
        $this->assertFalse($sCacheHit === false);
        $this->assertNull($sCacheHit);
    }

    public function testCheckUrlEndingSlash()
    {
        $oUtils = new oxutils();
        $this->assertEquals("http://www.site.de/", $oUtils->checkUrlEndingSlash("http://www.site.de/"));
        $this->assertEquals("http://www.site.de/", $oUtils->checkUrlEndingSlash("http://www.site.de"));
    }

    public function testCacheRaceConditions0Size()
    {
        $oUtils = new oxutils();
        $sFileName = $oUtils->getCacheFilePath('testCache1');
        @unlink($sFileName);
        $oUtils->toFileCache('testCache1', 'teststs');
        $oUtils->commitFileCache();
        $this->assertEquals(serialize(array('content' => 'teststs')), file_get_contents($sFileName));
        unlink($sFileName);
    }

    public function testCacheRaceConditionsNon0Size()
    {
        $oUtils = new oxutils();
        $sFileName = $oUtils->getCacheFilePath('testCache2');
        @unlink($sFileName);
        $oUtils->toFileCache('testCache2', 'teststs');
        $oUtils->commitFileCache();
        $sFileContents = file_get_contents($sFileName);
        $this->assertEquals(serialize(array('content' => 'teststs')), $sFileContents);
        unlink($sFileName);
    }

    public function testCacheRaceConditionsIgnoredBySisterProcess()
    {
        $oUtils1 = new oxutils();
        $oUtils2 = new oxutils();
        $sFileName = $oUtils1->getCacheFilePath('testCache3');
        @unlink($sFileName);
        $oUtils1->toFileCache('testCache3', 'instance1111');
        $oUtils2->toFileCache('testCache3', 'instance2222');
        $oUtils1->commitFileCache();
        $oUtils2->commitFileCache();
        $sFileContents = file_get_contents($sFileName);
        $this->assertEquals(serialize(array('content' => 'instance1111')), $sFileContents);
        unlink($sFileName);
    }

    public function testCachingLockRelease()
    {
        clearstatcache();
        $oUtils1 = new oxutils();
        $sFileName = $oUtils1->getCacheFilePath('testCache3');
        @unlink($sFileName);
        $this->assertFalse(file_exists($sFileName));

        $oUtils1->toFileCache('testCache3', 'instance1111');
        clearstatcache();
        $this->assertTrue(file_exists($sFileName));
        $this->assertEquals(0, filesize($sFileName));

        $oUtils1->commitFileCache();
        clearstatcache();
        $this->assertEquals(serialize(array('content' => 'instance1111')), file_get_contents($sFileName));
        $this->assertNotEquals(0, filesize($sFileName));

        $oUtils2 = new oxutils();
        $oUtils2->toFileCache('testCache3', 'instance2222');
        clearstatcache();
        $this->assertTrue(file_exists($sFileName));
        $this->assertEquals(0, filesize($sFileName));

        $oUtils2->commitFileCache();
        clearstatcache();
        $this->assertEquals(serialize(array('content' => 'instance2222')), file_get_contents($sFileName));
        $this->assertNotEquals(0, filesize($sFileName));

        unlink($sFileName);
    }

    /**
     *
     */
    public function testCanPreview()
    {
        modConfig::setRequestParameter("preview", null);
        $oUtils = new oxUtils();
        $this->assertNull($oUtils->canPreview());

        modConfig::setRequestParameter("preview", "132");
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{ return "123"; }');
        $this->assertFalse($oUtils->canPreview());

        $oUser = new oxUser();
        $oUser->load("oxdefaultadmin");

        $oUtils = $this->getMock("oxUtils", array("getUser"));
        $oUtils->expects($this->any())->method("getUser")->will($this->returnValue($oUser));

        modConfig::setRequestParameter("preview", $oUtils->getPreviewId());
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{ return "123"; }');

        $this->assertTrue($oUtils->canPreview());
    }

    /**
     * oxUtils::getPreviewId() test case
     *
     * @return null
     */
    public function testGetPreviewId()
    {

        $sAdminSid = oxRegistry::get("oxUtilsServer")->getOxCookie('admin_sid');
        $sCompare = md5($sAdminSid . "testID" . "testPass" . "tesrRights");

        $oUser = $this->getMock("oxUser", array("getId"));
        $oUser->expects($this->once())->method("getId")->will($this->returnValue("testID"));
        $oUser->oxuser__oxpassword = new oxField("testPass");
        $oUser->oxuser__oxrights = new oxField("tesrRights");

        $oUtils = $this->getMock("oxUtils", array("getUser"));
        $oUtils->expects($this->once())->method("getUser")->will($this->returnValue($oUser));

        $this->assertEquals($sCompare, $oUtils->getPreviewId());
    }

    public function testHandlePageNotFoundError()
    {
        $this->markTestIncomplete("Incorrect test for page not found headers. Normal headers mixed up with page not found");
        oxTestModules::addFunction('oxutils', 'showMessageAndExit', '{$this->showMessageAndExitCall[] = $aA; }');
        oxTestModules::addFunction('oxutils', 'setHeader', '{$this->setHeaderCall[] = $aA;}');
        oxTestModules::addFunction('oxUtilsView', 'getTemplateOutput', '{$this->getTemplateOutputCall[] = $aA; return "msg_".count($this->getTemplateOutputCall);}');

        oxRegistry::getUtils()->handlePageNotFoundError();
        $this->assertGreaterThanOrEqual(1, count(oxRegistry::getUtils()->setHeaderCall));
        $this->assertEquals(1, count(oxRegistry::get("oxUtilsView")->getTemplateOutputCall));
        $this->assertEquals(1, count(oxRegistry::getUtils()->showMessageAndExitCall));
        $this->assertEquals("msg_1", oxRegistry::getUtils()->showMessageAndExitCall[0][0]);
        $this->assertEquals("HTTP/1.0 404 Not Found", oxRegistry::getUtils()->setHeaderCall[0][0]);

        oxRegistry::getUtils()->handlePageNotFoundError("url aa");
        $this->assertGreaterThanOrEqual(2, count(oxRegistry::getUtils()->setHeaderCall));
        $this->assertEquals(2, count(oxRegistry::get("oxUtilsView")->getTemplateOutputCall));
        $this->assertEquals(2, count(oxRegistry::getUtils()->showMessageAndExitCall));
        $this->assertEquals("msg_2", oxRegistry::getUtils()->showMessageAndExitCall[1][0]);
        $this->assertEquals("HTTP/1.0 404 Not Found", oxRegistry::getUtils()->setHeaderCall[1][0]);

        oxTestModules::addFunction('oxUBase', 'render', '{throw new Exception();}');

        oxRegistry::getUtils()->handlePageNotFoundError("url aa");
        $this->assertEquals(3, count(oxRegistry::getUtils()->setHeaderCall));
        $this->assertEquals(2, count(oxRegistry::get("oxUtilsView")->getTemplateOutputCall));
        $this->assertEquals(3, count(oxRegistry::getUtils()->showMessageAndExitCall));
        $this->assertEquals("Page not found.", oxRegistry::getUtils()->showMessageAndExitCall[2][0]);
    }

    public function testToPhpFileCache()
    {
        $sTestArray = array("testVal1", "key1" => "testVal2");

        $oUtils = oxRegistry::getUtils();
        $oUtils->toPhpFileCache("testVal", $sTestArray);
        $oUtils->commitFileCache();

        $sFileName = oxRegistry::getUtils()->getCacheFilePath("testVal", false, 'php');

        include($sFileName);

        $this->assertEquals($_aCacheContents['content'], $sTestArray);
        unlink($sFileName);
    }

    /**
     * Test for bug #1737
     *
     */
    public function testToPhpFileCacheException()
    {
        $oSubj = $this->getMock("oxUtils", array("getCacheFilePath"));
        $oSubj->expects($this->any())->method("getCacheFilePath")->will($this->returnValue(false));

        oxTestModules::addModuleObject("oxUtils", $oSubj);

        $sTestArray = array("testVal1", "key1" => "testVal2");
        oxRegistry::getUtils()->toPhpFileCache("testVal2", $sTestArray);
        $aCacheContents = oxRegistry::getUtils()->fromPhpFileCache("testVal2");

        $this->assertNull($aCacheContents);


    }

    public function testFromPhpFileCache()
    {
        $sTestArray = array("testVal1", "key1" => "testVal2");

        $oUtils = oxRegistry::getUtils();
        $oUtils->toPhpFileCache("testVal", $sTestArray);
        $oUtils->commitFileCache();

        $this->assertEquals($oUtils->fromPhpFileCache("testVal"), $sTestArray);
    }

    /**
     * oxUtils::getCacheMeta() & oxUtils::setCacheMeta() test case
     *
     * @return null
     */
    public function testGetCacheMetaSetCacheMeta()
    {
        $oUtils = new oxUtils();
        $oUtils->setCacheMeta("xxx", "yyy");

        $this->assertFalse($oUtils->getCacheMeta("yyy"));
        $this->assertEquals("yyy", $oUtils->getCacheMeta("xxx"));
    }

    /**
     * oxUtils::_readFile() test case
     *
     * @return null
     */
    public function testReadFile()
    {
        $sFilePath = oxRegistry::getUtils()->getCacheFilePath("testVal", false, 'php');
        if (($hFile = @fopen($sFilePath, "w")) !== false) {
            fwrite($hFile, serialize("test"));
            fclose($hFile);

            $oUtils = new oxUtils();
            $this->assertEquals("test", $oUtils->UNITreadFile($sFilePath));

            return;
        }

        $this->markTestSkipped("Unable to create file {$sFilePath}");
    }

    /**
     * oxUtils::_includeFile() test case
     *
     * @return null
     */
    public function testIncludeFile()
    {
        $sFilePath = oxRegistry::getUtils()->getCacheFilePath("testVal", false, 'php');
        if (($hFile = @fopen($sFilePath, "w")) !== false) {
            fwrite($hFile, '<?php $_aCacheContents = "test123";');
            fclose($hFile);

            $oUtils = new oxUtils();
            $this->assertEquals("test123", $oUtils->UNITincludeFile($sFilePath));

            return;
        }

        $this->markTestSkipped("Unable to create file {$sFilePath}");
    }

    /**
     * oxUtils::_processCache() test case
     *
     * @return null
     */
    public function testProcessCache()
    {
        $oUtils = $this->getMock("oxutils", array("getCacheMeta"));
        $oUtils->expects($this->at(0))->method('getCacheMeta')->will($this->returnValue(false));
        $oUtils->expects($this->at(1))->method('getCacheMeta')->will($this->returnValue(array("serialize" => false)));

        $this->assertEquals(serialize(123), $oUtils->UNITprocessCache(123, 123));
        $this->assertNotEquals(serialize(123), $oUtils->UNITprocessCache(123, 123));
    }

    /**
     * Tests if cache works when TTL is not exceeded
     */
    public function testGetTtlCachingInTime()
    {
        $this->setTime(10);

        $oUtils = new oxUtils();
        $oUtils->toFileCache('anykey', 'test', 10);
        $oUtils->commitFileCache();

        $oUtils2 = new oxUtils();

        $this->setTime(15);
        $this->assertEquals('test', $oUtils2->fromFileCache('anykey'));
    }

    /**
     * Tests if cache works when TTL is exceeded
     */
    public function testGetTtlCachingTooLate()
    {
        $this->setTime(10);
        $oUtils = new oxUtils();
        $oUtils->toFileCache('otherkey', 'test', 10);
        $oUtils->commitFileCache();

        $oUtils2 = new oxUtils();

        $this->setTime(145);
        $this->assertEquals(null, $oUtils2->fromFileCache('otherkey'));
    }

    /**
     * Bug fix 0005811: Selectlist prices are displayed wrong under certain circumstances
     */
    public function testPreparePriceForUserWithChangedBehaviourWhenBruttoMode()
    {
        $this->setConfigParam('blShowNetPrice', false);

        // Mocking not necessary method for testing method to be called. Leaving mock empty would stub all class methods.
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $oUtils */
        $oUtils = $this->getMock('oxUtils', array('_getArticleUser'));
        $this->assertSame(10, $oUtils->_preparePrice(10, 10));
    }

    /**
     * Bug fix 0005811: Selectlist prices are displayed wrong under certain circumstances
     */
    public function testPreparePriceForUserWithChangedBehaviourWhenNettoMode()
    {
        $this->setConfigParam('blShowNetPrice', true);
        // Mocking not necessary method for testing method to be called. Leaving mock empty would stub all class methods.
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $oUtils */
        $oUtils = $this->getMock('oxUtils', array('_getArticleUser'));
        $this->assertSame(9.09, $oUtils->_preparePrice(10, 10));
    }

    /**
     * Bug fix 0005811: Selectlist prices are displayed wrong under certain circumstances
     */
    public function testPreparePriceForUserWithChangedBehaviourWhenNettoModeButUserBruttoMode()
    {
        $this->setConfigParam('blShowNetPrice', true);

        $oUser = $this->getMock('oxUser', array('isPriceViewModeNetto'));
        $oUser->expects($this->any())->method('isPriceViewModeNetto')->will($this->returnValue(false));

        // Mocking not necessary method for testing method to be called. Leaving mock empty would stub all class methods.
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $oUtils */
        $oUtils = $this->getMock('oxUtils', array('_getArticleUser'));
        $oUtils->expects($this->atLeastOnce())->method('_getArticleUser')->will($this->returnValue($oUser));
        $this->assertSame(10, $oUtils->_preparePrice(10, 10));
    }

    /**
     * Bug fix 0005811: Selectlist prices are displayed wrong under certain circumstances
     */
    public function testPreparePriceForUserWithChangedBehaviourWhenBruttoModeButUserNettoMode()
    {
        $this->setConfigParam('blShowNetPrice', false);

        $oUser = $this->getMock('oxUser', array('isPriceViewModeNetto'));
        $oUser->expects($this->any())->method('isPriceViewModeNetto')->will($this->returnValue(true));

        // Mocking not necessary method for testing method to be called. Leaving mock empty would stub all class methods.
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $oUtils */
        $oUtils = $this->getMock('oxUtils', array('_getArticleUser'));
        $oUtils->expects($this->atLeastOnce())->method('_getArticleUser')->will($this->returnValue($oUser));
        $this->assertSame(9.09, $oUtils->_preparePrice(10, 10));
    }
}
