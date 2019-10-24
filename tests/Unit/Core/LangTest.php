<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Theme;
use oxLang;
use \stdClass;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class LangTest extends \OxidTestCase
{

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        // cleanup
        oxRegistry::getUtils()->oxResetFileCache();

        $theme = oxNew(Theme::class);
        $theme->load('azure');
        $theme->activate();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // cleanup
        oxRegistry::getUtils()->oxResetFileCache();

        $sFileName = getShopBasePath() . "/out/azure/de/my_lang.php";
        if (file_exists($sFileName)) {
            unlink($sFileName);
        }

        parent::tearDown();
    }

    /**
     * Tests oxLang::processUrl()
     *
     * @return null
     */
    public function testProcessUrl()
    {
        $myConfig = $this->getConfig();

        $iDefL = $myConfig->getConfigParam('sDefaultLang');
        $oLang = oxNew('oxLang');
        $this->assertEquals("url", $oLang->processUrl("url", $iDefL));
        $this->assertEquals("url?lang=9&amp;", $oLang->processUrl("url", 9));
        $this->assertEquals("url?lang=9&amp;", $oLang->processUrl("url?", 9));
        $this->assertEquals("url?lang=$iDefL&amp;", $oLang->processUrl("url?lang=15&amp;", $iDefL));
        $this->assertEquals("url?lang=9", $oLang->processUrl("url?lang=3", 9));

        $this->assertEquals("url?x&amp;lang=9&amp;", $oLang->processUrl("url?x&amp;", 9));
        $this->assertEquals("url?x&amp;", $oLang->processUrl("url?x&amp;", $iDefL));
        $this->assertEquals("url?x&amp;lang=9", $oLang->processUrl("url?x&amp;lang=3", 9));
        $this->assertEquals("url?x&amp;lang=$iDefL&amp;", $oLang->processUrl("url?x&amp;lang=5&amp;", $iDefL));
    }

    /**
     * Tests oxLang::getName()
     *
     * @return null
     */
    public function testGetName()
    {
        $oLang = oxNew('oxLang');
        $this->assertEquals("lang", $oLang->getName());
    }

    /**
     * Tests oxLang::getFormLang()
     *
     * @return null
     */
    public function testGetFormLang()
    {
        $sFormLang = "<input type=\"hidden\" name=\"lang\" value=\"9\" />";

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("getBaseLanguage"));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(9));
        $this->assertEquals($sFormLang, $oLang->getFormLang());
    }

    /**
     * Tests oxLang::getUrlLang()
     *
     * @return null
     */
    public function testgetUrlLang()
    {
        $sUrlLang = "lang=9";

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("getBaseLanguage"));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(9));
        $this->assertEquals($sUrlLang, $oLang->getUrlLang());
    }

    public function testGetLangFilesContainsAllLanguages()
    {
        $sPath = $this->getConfig()->getAppDir();

        $aPathArray = array(
            $sPath . "translations/de/lang.php",
            $sPath . "translations/de/translit_lang.php",
            $sPath . "views/azure/de/lang.php",
            $sPath . "views/azure/de/cust_lang.php"
        );

        $oLang = oxNew('oxLang');
        foreach ($aPathArray as $languageFilePath) {
            $this->assertContains($languageFilePath, $oLang->UNITgetLangFilesPathArray(0));
        }
    }

    public function testGetLangFilesPathContainsCustomLanguage()
    {
        $sPath = $this->getConfig()->getAppDir();

        $customLangPath = $sPath . "views/azure/de/cust_lang.php";

        $oLang = oxNew('oxLang');
        $this->assertContains($customLangPath, $oLang->UNITgetLangFilesPathArray(0));
    }

    public function testGetLangFilesPathContainsOnlyAvailableLanguages()
    {
        $languageId = 0;
        $language = oxNew(\OxidEsales\Eshop\Core\Language::class);
        $currentLanguageAbbreviation = $language->getLanguageAbbr($languageId);

        $languagePaths = $language->UNITgetLangFilesPathArray($languageId);

        foreach ($languagePaths as $languagePath) {
            $this->assertContains(
                '/' . $currentLanguageAbbreviation . '/',
                $languagePath,
                "The path" . $languagePath . "contains a different language than " .  $currentLanguageAbbreviation . "."
            );
        }
    }

    public function testGetLangFilesPathForModules()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }
        $sFilePath = $this->getConfig()->getConfigParam('sShopDir') . 'modules/oxlangTestModule/translations/de/';

        if (!is_dir($sFilePath)) {
            mkdir($sFilePath, 0755, true);
        }

        file_put_contents($sFilePath . "/test_lang.php", 'langfile');

        $sPath = $this->getConfig()->getAppDir();
        $sShopPath = $this->getConfig()->getConfigParam('sShopDir');
        $aPathArray = array(
            $sPath . "translations/de/lang.php",
            $sPath . "translations/de/translit_lang.php",
            $sPath . "views/azure/de/lang.php",
            $sShopPath . "modules/oxlangTestModule/translations/de/test_lang.php",
            $sPath . "views/azure/de/cust_lang.php"
        );

        $aInfo = array('oxlangTestModule' => 'oxlangTestModule');

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("_getActiveModuleInfo"));
        $oLang->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));

        $this->assertEquals($aPathArray, $oLang->UNITgetLangFilesPathArray(0));

        unlink($sShopPath . "modules/oxlangTestModule/translations/de/test_lang.php");
        rmdir($sShopPath . "modules/oxlangTestModule/translations/de/");
        rmdir($sShopPath . "modules/oxlangTestModule/translations/");
        rmdir($sShopPath . "modules/oxlangTestModule/");
    }

    public function testGetLangFilesPathForModulesWithApplicationFolder()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }
        $sFilePath = $this->getConfig()->getConfigParam('sShopDir') . 'modules/oxlangTestModule/Application/translations/de/';

        if (!is_dir($sFilePath)) {
            mkdir($sFilePath, 0755, true);
        }

        file_put_contents($sFilePath . "/test_lang.php", 'langfile');

        $sPath = $this->getConfig()->getAppDir();
        $sShopPath = $this->getConfig()->getConfigParam('sShopDir');
        $aPathArray = array(
            $sPath . "translations/de/lang.php",
            $sPath . "translations/de/translit_lang.php",
            $sPath . "views/azure/de/lang.php",
            $sShopPath . "modules/oxlangTestModule/Application/translations/de/test_lang.php",
            $sPath . "views/azure/de/cust_lang.php"
        );

        $aInfo = array('oxlangTestModule' => 'oxlangTestModule');

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("_getActiveModuleInfo"));
        $oLang->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));

        $this->assertEquals($aPathArray, $oLang->UNITgetLangFilesPathArray(0));

        unlink($sShopPath . "modules/oxlangTestModule/Application/translations/de/test_lang.php");
        rmdir($sShopPath . "modules/oxlangTestModule/Application/translations/de/");
        rmdir($sShopPath . "modules/oxlangTestModule/Application/translations/");
        rmdir($sShopPath . "modules/oxlangTestModule/Application/");
    }


    public function testGetLangFilesPathForAdmin()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }
        $sFilePath = $this->getConfig()->getConfigParam('sShopDir') . 'modules/oxlangTestModule/views/admin/de/';

        if (!is_dir($sFilePath)) {
            mkdir($sFilePath, 0755, true);
        }

        file_put_contents($sFilePath . "/test1_lang.php", 'langfile');
        file_put_contents($sFilePath . "/module_options.php", 'langfile');

        $sPath = $this->getConfig()->getAppDir();
        $sShopPath = $this->getConfig()->getConfigParam('sShopDir');
        $aPathArray = array(
            $sPath . "views/admin/de/lang.php",
            $sPath . "translations/de/translit_lang.php",
            $sPath . "views/admin/de/help_lang.php",
            $sPath . "views/azure/de/theme_options.php",
            $sShopPath . "modules/oxlangTestModule/views/admin/de/test1_lang.php",
            $sShopPath . "modules/oxlangTestModule/views/admin/de/module_options.php",
            $sPath . "views/admin/de/cust_lang.php"
        );
        $aInfo = array('oxlangTestModule' => 'oxlangTestModule');

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("_getActiveModuleInfo"));
        $oLang->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));

        $aResult = $oLang->UNITgetAdminLangFilesPathArray(0);

        foreach ($aPathArray as $sPath) {
            $this->assertTrue(array_search($sPath, $aResult) !== false, "Language file '$sPath' was not found as registered");
        }

        unlink($sShopPath . "modules/oxlangTestModule/views/admin/de/test1_lang.php");
        unlink($sShopPath . "modules/oxlangTestModule/views/admin/de/module_options.php");
        rmdir($sShopPath . "modules/oxlangTestModule/views/admin/de/");
        rmdir($sShopPath . "modules/oxlangTestModule/views/admin/");
        rmdir($sShopPath . "modules/oxlangTestModule/views/");
        rmdir($sShopPath . "modules/oxlangTestModule/");
    }

    public function testGetLangFileCacheName()
    {
        $myConfig = $this->getConfig();
        $sCacheName = "langcache_1_1_" . $myConfig->getShopId() . "_" . $myConfig->getConfigParam('sTheme') . '_default';

        $oLang = oxNew('oxLang');
        $this->assertEquals($sCacheName, $oLang->UNITgetLangFileCacheName(true, 1));

        $sCacheName = "langcache_1_1_" . $myConfig->getShopId() . "_" . $myConfig->getConfigParam('sTheme') . '_9fe20164bd4aeab975137aae7f30a1ce';
        $this->assertEquals($sCacheName, $oLang->UNITgetLangFileCacheName(true, 1, array('asdasd', 'dasasd')));
    }

    public function testGetLanguageFileData()
    {
        oxTestModules::addFunction("oxUtils", "getLangCache", "{}");
        oxTestModules::addFunction("oxUtils", "setLangCache", "{}");

        $sFilePrefix = md5(uniqid(rand(), true));

        //writing a test lang file
        $sFilePath = $this->getConfig()->getConfigParam('sCompileDir');
        file_put_contents($sFilePath . "/baselang$sFilePrefix.txt", '<?php $aSeoReplaceChars = array("t1" => "r1", "t2" => "r2", "t3" => "r3"); $aLang = array( "charset" => "UTF-8", "TESTKEY" => "baseVal");');
        file_put_contents($sFilePath . "/testlang$sFilePrefix.txt", '<?php $aSeoReplaceChars = array("t1" => "overide1", "t4"=>"add"); $aLang = array( "charset" => "ISO-8859-15", "TESTKEY" => "testVal");');

        $aLangFilesPath = array($sFilePath . "/baselang$sFilePrefix.txt", $sFilePath . "/testlang$sFilePrefix.txt");

        $aResult = array(
            "charset" => "UTF-8",
            "TESTKEY" => "testVal",
            '_aSeoReplaceChars' => array(
                "t1" => "overide1",
                "t2" => "r2",
                "t3" => "r3",
                "t4" => "add"
            )
        );

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("_getLangFilesPathArray"));
        $oLang->expects($this->any())->method('_getLangFilesPathArray')->will($this->returnValue($aLangFilesPath));
        $oLangFilesData = $oLang->UNITgetLanguageFileData(false, 0);

        $this->assertEquals($aResult, $oLangFilesData);
    }

    public function testSetCharsetToUtf8IfMissing()
    {
        $sFilePrefix = md5(uniqid(rand(), true));

        //writing a test lang file
        $sFilePath = $this->getConfig()->getConfigParam('sCompileDir');
        file_put_contents($sFilePath . "/baselang$sFilePrefix.txt", '<?php $aLang = array( "TESTKEY" => "value");');

        $aLangFilesPath = array($sFilePath . "/baselang$sFilePrefix.txt");

        $aResult = array(
            "charset" => "UTF-8",
            "TESTKEY" => "value",
            "_aSeoReplaceChars" => [],
        );

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("_getLangFilesPathArray"));
        $oLang->expects($this->any())->method('_getLangFilesPathArray')->will($this->returnValue($aLangFilesPath));
        $oLangFilesData = $oLang->UNITgetLanguageFileData(false, 0);

        $this->assertEquals($aResult, $oLangFilesData);
    }

    public function testGetLanguageFileDataInUtfMode()
    {
        oxTestModules::addFunction("oxUtils", "getLangCache", "{}");
        oxTestModules::addFunction("oxUtils", "setLangCache", "{}");

        $sFilePrefix = uniqid('', true);

        //writing a test lang file
        $sFilePath = $this->getConfig()->getConfigParam('sCompileDir');
        file_put_contents($sFilePath . "/baselang$sFilePrefix.txt", '<?php $aSeoReplaceChars = array("t1" => "r1", "t2" => "r2", "t3" => "r3"); $aLang = array( "charset" => "ISO-8859-15", "TESTKEY" => "baseVal");');
        file_put_contents($sFilePath . "/testlang$sFilePrefix.txt", '<?php $aSeoReplaceChars = array("t1" => "overide1"); $aLang = array( "charset" => "ISO-8859-15", "TESTKEY" => "testVal");');

        $aLangFilesPath = array($sFilePath . "/baselang$sFilePrefix.txt", $sFilePath . "/testlang$sFilePrefix.txt");

        $aResult = array(
            "charset" => "UTF-8",
            '_aSeoReplaceChars' => array(
                't1' => 'overide1',
                't2' => 'r2',
                't3' => 'r3'
            ),
            'TESTKEY' => 'testVal'
        );

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("_getLangFilesPathArray"));
        $oLang->expects($this->any())->method('_getLangFilesPathArray')->will($this->returnValue($aLangFilesPath));
        $oLangFilesData = $oLang->UNITgetLanguageFileData(false, 0);

        $this->assertEquals($aResult, $oLangFilesData);
    }

    public function testGetLanguageFileDataUtf()
    {
        oxTestModules::addFunction("oxUtils", "getLangCache", "{}");
        oxTestModules::addFunction("oxUtils", "setLangCache", "{}");

        $sFilePrefix = uniqid('', true);

        //writing a test lang file
        $sFilePath = $this->getConfig()->getConfigParam('sCompileDir');
        file_put_contents($sFilePath . "/baselang$sFilePrefix.txt", '<?php $aSeoReplaceChars = array("t1" => "overide1"); $aLang = array( "charset" => "iso-8859-15", "TESTKEY" => "baseVal");');
        file_put_contents($sFilePath . "/testlang$sFilePrefix.txt", '<?php $aLang = array( "charset" => "iso-8859-15", "TESTKEY" => "testVal");');

        $aLangFilesPath = array($sFilePath . "/baselang$sFilePrefix.txt", $sFilePath . "/testlang$sFilePrefix.txt");

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('_getLangFileCacheName', "_getLangFilesPathArray"));
        $oLang->expects($this->any())->method('_getLangFileCacheName')->will($this->returnValue(false));
        $oLang->expects($this->any())->method('_getLangFilesPathArray')->will($this->returnValue($aLangFilesPath));

        $oLangFilesData = $oLang->UNITgetLanguageFileData(false, 0);

        $aResult = array("charset" => "UTF-8", "TESTKEY" => "testVal", '_aSeoReplaceChars' => array("t1" => "overide1"));
        $this->assertEquals($aResult, $oLangFilesData);
    }

    public function testGetLanguageFileNoDuplicatedSeoReplaceChars()
    {
        oxTestModules::addFunction("oxUtils", "getLangCache", "{}");
        oxTestModules::addFunction("oxUtils", "setLangCache", "{}");

        $sFilePrefix = uniqid('', true);

        //writing a test lang file
        $sFilePath = $this->getConfig()->getConfigParam('sCompileDir');


        file_put_contents(
            $sFilePath . "/baselang$sFilePrefix.txt",
            '<?php
            $aSeoReplaceChars = array(
                "ä" => "ae",
                "ö" => "oe",
                "ß" => "ss",
                "x" => "z",
            );
            $aLang = array(
                "charset" => "ISO-8859-15",
                "TESTKEY" => "bäseVäl"
            );'
        );

        file_put_contents(
            $sFilePath . "/testlang$sFilePrefix.txt",
            '<?php
            $aLang = array(
                "charset" => "ISO-8859-15",
                "TESTKEY" => "testVäl"
            );'
        );

        $aLangFilesPath = array($sFilePath . "/baselang$sFilePrefix.txt", $sFilePath . "/testlang$sFilePrefix.txt");

        $aResult = array(
            "charset" => "UTF-8",
            '_aSeoReplaceChars' => array(
                "ä" => "ae",
                "ö" => "oe",
                "ß" => "ss",
                "x" => "z",
            ),
            "TESTKEY" => "testVäl"
        );


        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('_getLangFileCacheName', "_getLangFilesPathArray"));
        $oLang->expects($this->any())->method('_getLangFileCacheName')->will($this->returnValue(false));
        $oLang->expects($this->any())->method('_getLangFilesPathArray')->will($this->returnValue($aLangFilesPath));
        $oLangFilesData = $oLang->UNITgetLanguageFileData(false, 0);

        $this->assertEquals($aResult, $oLangFilesData);
    }

    public function testRecodeLangArray()
    {
        $aLang['ACCOUNT_MAIN_BACKTOSHOP'] = "Zurück zum Shop";
        $aRecoded['ACCOUNT_MAIN_BACKTOSHOP'] = iconv('ISO-8859-15', 'UTF-8', $aLang['ACCOUNT_MAIN_BACKTOSHOP']);

        $oLang = oxNew('oxLang');
        $aResult = $oLang->UNITrecodeLangArray($aLang, 'ISO-8859-15');
        $this->assertNotEquals($aLang, $aResult);
        $this->assertEquals($aRecoded, $aResult);
    }

    public function testTranslateStringWithGeneratedLangFile()
    {
        $oLang = oxNew('oxLang');
        $sVersionPrefix = oxNew('oxUtils')->getEditionCacheFilePrefix();

        $sVal = "Zurück zum Shop";
        $myConfig = $this->getConfig();
        $sCacheName = "langcache_1_1_" . $myConfig->getShopId() . "_" . $myConfig->getConfigParam('sTheme') . '_default';

        //writing a test file
        $sFileName = $this->getConfig()->getConfigParam('sCompileDir') . "/ox{$sVersionPrefix}c_{$sCacheName}.txt";
        $sFileContents = '<?php $aLangCache = array( "ACCOUNT_MAIN_BACKTOSHOP" => "' . $sVal . '");';
        file_put_contents($sFileName, $sFileContents);

        $this->assertEquals($sVal, $oLang->translateString("ACCOUNT_MAIN_BACKTOSHOP", 1, 1));
    }

    /**
     * Testing vat formatting functionality
     */
    public function testFormatVat()
    {
        $oCur = new stdClass();
        $oCur->decimal = 2;
        $oCur->dec = '.';
        $oCur->thousand = '';

        $oLang = oxRegistry::getLang();
        $this->assertEquals('18', $oLang->formatVat(18.00));
        $this->assertEquals('21,5', $oLang->formatVat(21.50));
        $this->assertEquals('1,5', $oLang->formatVat(1.50));
        $this->assertEquals('21.5', $oLang->formatVat(21.50, $oCur));
    }

    /**
     * Testing string translation function
     */
    // in admin mode
    public function testTranslateStringIsAdmin()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $this->assertEquals('Aktiv', $oLang->translateString("GENERAL_ACTIVE", 0));
        $this->assertEquals('Active', $oLang->translateString("GENERAL_ACTIVE", 1));

        $this->assertEquals('Dieser Benutzer existiert bereits!', $oLang->translateString("EXCEPTION_USER_USEREXISTS", 0));
        $this->assertEquals('This user allready exists!', $oLang->translateString("EXCEPTION_USER_USEREXISTS", 1));

        $this->assertEquals('blafoowashere123', $oLang->translateString("blafoowashere123"));
        $this->assertEquals('', $oLang->translateString(""));
        $this->assertEquals('\/ß[]~ä#-', $oLang->translateString("\/ß[]~ä#-"));
    }

    // in non amdin mode
    public function testTranslateStringIsNotAdmin()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertEquals('blafoowashere123', $oLang->translateString("blafoowashere123"));
        $this->assertEquals('', $oLang->translateString(""));
        $this->assertEquals('\/ß[]~ä#-', $oLang->translateString("\/ß[]~ä#-"));
    }

    public function testFormatsCurrencyUsingDefaultValues()
    {
        /** @var oxLang $oLang */
        $oLang = oxNew('oxLang');
        $sFormatted = $oLang->formatCurrency(10322.324);

        $this->assertEquals($sFormatted, '10.322,32');
    }

    public function testFormatsCurrencyByPassingCurrencyObject()
    {
        /** @var oxLang $oLang */
        $oLang = oxNew('oxLang');
        $oActCur = $this->getConfig()->getActShopCurrencyObject();
        $sFormatted = $oLang->formatCurrency(10322.324, $oActCur);

        $this->assertEquals($sFormatted, '10.322,32');
    }

    public function testFormatsCurrencyUsingSimulatedCurrencyObject()
    {
        /** @var oxLang $oLang */
        $oLang = oxNew('oxLang');
        $oActCur = new stdClass();
        $oActCur->decimal = 3;
        $oActCur->dec = '~';
        $oActCur->thousand = '#';
        $sFormatted = $oLang->formatCurrency(10322.326, $oActCur);

        $this->assertEquals($sFormatted, "10#322~326");
    }

    /**
     * Testing language tag getter
     */
    // emulated lang 1
    public function testGetLanguageTagEmulatedLang()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getBaseLanguage'));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(1));

        $this->assertEquals('_1', $oLang->getLanguageTag());
    }

    // default lang 0
    public function testGetLanguageTagDefaultLang()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getBaseLanguage'));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(0));

        $this->assertEquals('', $oLang->getLanguageTag());
    }

    // passing language ids
    public function testGetLanguageTagPassedLang()
    {
        $oLang = oxNew('oxLang');
        $this->assertEquals('', $oLang->getLanguageTag(0));
        $this->assertEquals('_1', $oLang->getLanguageTag(1));
    }

    public function testResetBaseLanguage()
    {
        $this->setRequestParameter('lang', '1');
        $oLang = oxNew('oxLang');

        $this->assertEquals(1, $oLang->getBaseLanguage());
        $this->setRequestParameter('lang', '0');
        $this->assertEquals(1, $oLang->getBaseLanguage());
        $oLang->resetBaseLanguage();
        $this->assertEquals(0, $oLang->getBaseLanguage());
        $this->setRequestParameter('lang', '1');
        $this->assertEquals(0, $oLang->getBaseLanguage());
        $oLang->resetBaseLanguage();
        $this->assertEquals(1, $oLang->getBaseLanguage());
    }

    /**
     * Testing getBaseLanguage() - testing if all request given parameters are used
     */
    public function testGetBaseLanguageTestingRequest()
    {
        $this->setRequestParameter('changelang', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $this->assertEquals(1, $oLang->getBaseLanguage());

        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', 1);
        $oLang = oxNew('oxLang');

        $this->assertEquals(1, $oLang->getBaseLanguage());

        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', 1);
        $oLang = oxNew('oxLang');

        $this->assertEquals(1, $oLang->getBaseLanguage());

        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', null);
        $oLang = oxNew('oxLang');

        $this->getConfig()->setConfigParam('sDefaultLang', 1);

        $this->assertEquals(1, $oLang->getBaseLanguage());
    }

    /**
     * Testing getBaseLanguage() - testing if bad language id is fixed
     */
    public function testGetBaseLanguagePassingNotExistingShouldBeFixed()
    {
        $this->setRequestParameter('changelang', 'xxx');
        $oLang = oxNew('oxLang');

        $this->assertEquals(0, $oLang->getBaseLanguage());
    }

    /**
     * Testing getBaseLanguage() - testing if getting base lang id ignores 'tpllanguage' param
     */
    public function testGetBaseLanguageIgnoresSettedTemplateLanguageParam()
    {
        $this->setRequestParameter('changelang', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', 1);

        $oLang = oxNew('oxLang');

        $this->assertEquals(0, $oLang->getBaseLanguage());
    }

    /**
     * Testing getBaseLanguage() - caches already setted language id
     */
    public function testGetBaseLanguageCaching()
    {
        $this->setRequestParameter('language', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertEquals(1, $oLang->getBaseLanguage());

        $this->setRequestParameter('language', 0);
        $this->assertEquals(1, $oLang->getTplLanguage());
    }

    /**
     * Testing getBaseLanguage() - getting language id using browser detect
     */
    public function testGetBaseLanguage_detectingByBrowser()
    {
        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', null);
        $this->setRequestParameter('aLanguageURLs', null);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('detectLanguageByBrowser', 'validateLanguage'));
        $oLang->expects($this->any())->method('validateLanguage')->with($this->equalTo(1))->will($this->returnValue(1));
        $oLang->expects($this->once())->method('detectLanguageByBrowser')->will($this->returnValue(1));

        $this->assertEquals(1, $oLang->getBaseLanguage());
    }

    /**
     * Testing getBaseLanguage() - getting language id using browser detect when
     * search engine detected
     */
    public function testGetBaseLanguage_detectingByBrowser_searchEngineDetected()
    {
        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', null);
        $this->setRequestParameter('aLanguageURLs', null);

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('isSearchEngine'));
        $oUtils->expects($this->any())->method('isSearchEngine')->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('detectLanguageByBrowser', 'validateLanguage'));
        $oLang->expects($this->any())->method('validateLanguage')->with($this->equalTo(0))->will($this->returnValue(0));
        $oLang->expects($this->never())->method('detectLanguageByBrowser');

        $this->assertEquals(0, $oLang->getBaseLanguage());
    }

    /**
     * Testing getBaseLanguage() - getting language id using browser detect when
     * search engine detected
     */
    public function testGetBaseLanguage_detectingByBrowser_adminMode()
    {
        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', null);
        $this->setRequestParameter('aLanguageURLs', null);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('detectLanguageByBrowser', 'isAdmin', 'validateLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('validateLanguage')->with($this->equalTo(0))->will($this->returnValue(0));
        $oLang->expects($this->never())->method('detectLanguageByBrowser');

        $this->assertEquals(0, $oLang->getBaseLanguage());
    }

    /**
     * Testing getTplLanguage() - in non admin mode should return base language id
     */
    public function testGetTplLanguageInNonAdminMode()
    {
        $this->setRequestParameter('tpllanguage', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(0));

        $this->assertEquals(0, $oLang->getTplLanguage());
    }

    /**
     * Testing getTplLanguage() - testind in admin mode
     */
    public function testGetTplLanguageInAdminMode()
    {
        //$this->setRequestParameter( 'tpllanguage', 1 );
        $this->getSession()->setVariable('tpllanguage', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(0));

        $this->assertEquals(1, $oLang->getTplLanguage());
    }

    /**
     * Testing getTplLanguage() - testing in admin mode, when no tpllanguage param exists
     */
    public function testGetTplLanguageInAdmin()
    {
        $this->getSession()->setVariable('tpllanguage', 999);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'setTplLanguage', 'getBaseLanguage'));
        $oLang->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->once())->method('setTplLanguage')->with($this->equalTo(999))->will($this->returnValue(777));
        $oLang->expects($this->never())->method('getBaseLanguage');

        $this->assertEquals(777, $oLang->getTplLanguage());
    }

    /**
     * Testing getTplLanguage() - testing in admin mode, when no tpllanguage param exists
     */
    public function testGetTplLanguageNonAdmin()
    {
        $this->getSession()->setVariable('tpllanguage', 999);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'setTplLanguage', 'getBaseLanguage'));
        $oLang->expects($this->once())->method('isAdmin')->will($this->returnValue(false));
        $oLang->expects($this->never())->method('setTplLanguage');
        $oLang->expects($this->once())->method('getBaseLanguage')->will($this->returnValue(555));

        $this->assertEquals(555, $oLang->getTplLanguage());
    }

    /**
     * Testing getTplLanguage() - caches already setted language id
     */
    public function testGetTplLanguageCaching()
    {
        //$this->setRequestParameter( 'tpllanguage', 1 );
        $this->getSession()->setVariable('tpllanguage', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(0));

        $this->assertEquals(1, $oLang->getTplLanguage());

        $this->setRequestParameter('tpllanguage', 0);
        $this->assertEquals(1, $oLang->getTplLanguage());
    }

    /**
     * Testing getTplLanguage() - testing if bad language id is fixed
     */
    public function testGetTplLanguagePassingNotExistingShouldBeFixed()
    {
        $this->setRequestParameter('tpllanguage', 'xxx');

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $this->assertEquals(0, $oLang->getTplLanguage());
    }

    /**
     * Testing getEditLanguage() - in admin mode
     */
    public function testGetEditLanguageInAdminMode()
    {
        $this->setRequestParameter('editlanguage', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(0));

        $this->assertEquals(1, $oLang->getEditLanguage());
    }

    /**
     * Testing getEditLanguage() - in non admin mode
     */
    public function testGetEditLanguageinNonAdminMode()
    {
        $this->setRequestParameter('editlanguage', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(0));

        $this->assertEquals(0, $oLang->getEditLanguage());
    }

    /**
     * Testing getEditLanguage() - when no editlanguage param exists
     */
    public function testGetEditLanguageWithoutEditLangParam()
    {
        $this->setRequestParameter('editlanguage', null);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(1));

        $this->assertEquals(1, $oLang->getEditLanguage());
    }

    /**
     * Testing getEditLanguage() - new language param overides editlangparam
     * whene saveing to difference lang
     */
    public function testGetEditLanguageNewLangParamOveridesEditLangParam()
    {
        $this->setRequestParameter('editlanguage', 0);
        $this->setRequestParameter('new_lang', 1);

        $oView = oxNew('oxView');
        $oView->setFncName('saveinnlang');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getActiveView'));
        $oConfig->expects($this->any())->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->init();

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage', 'getConfig'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(2));
        $oLang->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(1, $oLang->getEditLanguage());
    }

    /**
     * Testing getEditLanguage() - caches already setted language id
     */
    public function testGetEditLanguageCaching()
    {
        $this->setRequestParameter('editlanguage', 1);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(2));

        $this->assertEquals(1, $oLang->getEditLanguage());

        $this->setRequestParameter('editlanguage', 0);
        $this->assertEquals(1, $oLang->getEditLanguage());
    }

    /**
     * Testing getEditLanguage() - testing if bad language id is fixed
     */
    public function testGetEditLanguagePassingNotExistingShouldBeFixed()
    {
        $this->setRequestParameter('editlanguage', 'xxx');

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        $this->assertEquals(0, $oLang->getEditLanguage());
    }

    /**
     * Testing getBaseLanguage() - testing if url configuration sets language
     */
    public function testGetBaseLanguageLanguageURLs()
    {
        $this->setRequestParameter('changelang', 1);
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('isCurrentUrl'));
        $oConfig->expects($this->any())->method('isCurrentUrl')->will($this->returnValue(true));
        $oConfig->init();

        $oConfig->setConfigParam('aLanguageURLs', array(1 => 'xxx'));

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin'));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $oLang->setConfig($oConfig);


        $this->assertEquals(1, $oLang->getBaseLanguage());
    }

    /**
     * Testing language array getter
     */
    public function testGetLanguageArray()
    {
        // preparing fixture
        $oDe = new stdClass();
        $oDe->id = 0;
        $oDe->abbr = 'de';
        $oDe->oxid = 'de';
        $oDe->name = 'Deutsch';
        $oDe->active = '1';
        $oDe->sort = '1';
        $oDe->selected = 1;

        $oEng = clone $oDe;
        $oEng->id = 1;
        $oEng->abbr = 'en';
        $oEng->oxid = 'en';
        $oEng->name = 'English';
        $oEng->active = '1';
        $oEng->sort = '2';
        $oEng->selected = 0;

        $aLangArray = array($oDe, $oEng);

        $oLang = oxNew('oxLang');
        $this->assertEquals($aLangArray, $oLang->getLanguageArray(0, true, true));
    }

    /**
     * #1290: impossible to switch languages in admin, if third language is created as default and only one active
     */
    public function testGetLanguageArrayWithNewLang()
    {
        $aLanguages = array('de' => 'Deutsch', 'ru' => 'Russian');
        $this->getConfig()->setConfigParam('aLanguages', $aLanguages);
        $aLangParams['de']['baseId'] = 0;
        $aLangParams['de']['abbr'] = 'de';
        $aLangParams['de']['sort'] = '1';
        $aLangParams['de']['active'] = '1';
        $aLangParams['ru']['baseId'] = 2;
        $aLangParams['ru']['abbr'] = 'ru';
        $aLangParams['ru']['sort'] = '2';
        $aLangParams['ru']['active'] = '1';

        $this->getConfig()->setConfigParam('aLanguageParams', $aLangParams);

        // preparing fixture
        $oDe = new stdClass();
        $oDe->id = 0;
        $oDe->abbr = 'de';
        $oDe->oxid = 'de';
        $oDe->name = 'Deutsch';
        $oDe->active = '1';
        $oDe->sort = '1';
        $oDe->selected = 0;

        $oRus = clone $oDe;
        $oRus->id = 2;
        $oRus->abbr = 'ru';
        $oRus->oxid = 'ru';
        $oRus->name = 'Russian';
        $oRus->active = '1';
        $oRus->sort = '2';
        $oRus->selected = 1;

        $aLangArray = array(0 => $oDe, 2 => $oRus);

        $oLang = oxNew('oxLang');

        $this->assertEquals($aLangArray, $oLang->getLanguageArray(2));
    }

    /**
     * Testing language names getter when one language is inactive
     * (M:1027)
     */
    public function testGetLanguageArray_withIncacitiveLang()
    {
        // preparing fixture
        $oEng = new stdClass();
        $oEng->id = 1;
        $oEng->abbr = 'en';
        $oEng->oxid = 'en';
        $oEng->name = 'English';
        $oEng->active = '1';
        $oEng->sort = '2';
        $oEng->selected = 1;

        $aLangArray = array(1 => $oEng);

        $oConfig = $this->getConfig();
        $aLangParams = $oConfig->getConfigParam('aLanguageParams');
        $aLangParams["de"]["active"] = false;
        $aLangParams = $oConfig->setConfigParam('aLanguageParams', $aLangParams);

        $oLang = oxNew('oxLang');
        $aLanguages = $oLang->getLanguageArray(1, true);
        $this->assertEquals($aLangArray, $aLanguages);

        $this->assertEquals(1, $aLanguages[1]->selected);
    }

    /**
     * Testing language name getter when language parameters array does not exist
     */
    public function testGetLanguageAbbrWhenLangParamsArrayDoesNotExists()
    {
        $this->getConfig()->setConfigParam('aLanguageParams', null);

        $oLang = $this->getProxyClass("oxLang");
        $oLang->setNonPublicVar('_iBaseLanguageId', 0);

        $this->assertEquals('de', $oLang->getLanguageAbbr(0));
        $this->assertEquals('en', $oLang->getLanguageAbbr(1));
        $this->assertEquals(3, $oLang->getLanguageAbbr(3));
        $this->assertEquals('de', $oLang->getLanguageAbbr(null));
    }

    /**
     * Testing language name getter when language parameters array exist
     */
    public function testGetLanguageAbbrWhenLangParamsArrayExists()
    {
        $aLangParams['de']['baseId'] = 0;
        $aLangParams['de']['abbr'] = 'de';
        $aLangParams['ru']['baseId'] = 1;
        $aLangParams['ru']['abbr'] = 'ru';
        $aLangParams['en']['baseId'] = 3;
        $aLangParams['en']['abbr'] = 'ru';

        $this->getConfig()->setConfigParam('aLanguageParams', $aLangParams);

        $oLang = $this->getProxyClass("oxLang");
        $oLang->setNonPublicVar('_iBaseLanguageId', 0);

        $this->assertEquals('de', $oLang->getLanguageAbbr(0));
        $this->assertEquals('ru', $oLang->getLanguageAbbr(1));
        $this->assertEquals(2, $oLang->getLanguageAbbr(2));
        $this->assertEquals('en', $oLang->getLanguageAbbr(3));
        $this->assertEquals('de', $oLang->getLanguageAbbr(null));
    }

    /**
     * Testing language name getter
     */
    public function testGetLanguageAbbr()
    {
        $oLang = $this->getProxyClass("oxLang");
        $oLang->setNonPublicVar('_iBaseLanguageId', 0);

        $this->assertEquals('de', $oLang->getLanguageAbbr(0));
        $this->assertEquals('en', $oLang->getLanguageAbbr(1));
        $this->assertEquals(3, $oLang->getLanguageAbbr(3));
        $this->assertEquals('de', $oLang->getLanguageAbbr(null));
    }

    /**
     * Testing language name getter
     */
    public function testGetLanguageAbbrAdmin()
    {
        $oLang1 = new stdClass();
        $oLang1->abbr = 'test1';
        $oLang1->id = 0;
        $oLang2 = new stdClass();
        $oLang2->abbr = 'test2';
        $oLang2->id = 1;

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("isAdmin", "getAdminTplLanguageArray"));
        $oLang->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->any())->method('getAdminTplLanguageArray')->will($this->returnValue(array($oLang1, $oLang2)));

        $this->assertEquals('de', $oLang->getLanguageAbbr(0));
        $this->assertEquals('en', $oLang->getLanguageAbbr(1));
        $this->assertEquals(2, $oLang->getLanguageAbbr(2));
    }

    /**
     * Testing language array getter - if returns already setted base language id
     */
    public function testGetBaseLanguageReturnsAlreadySettedValue()
    {
        $oLang = $this->getProxyClass("oxLang");
        $oLang->setNonPublicVar('_iBaseLanguageId', 2);

        $this->assertEquals(2, $oLang->getBaseLanguage());
    }

    /**
     * Testing base language setter
     */
    public function testSetBaseLanguage()
    {
        $oLang = oxNew('oxLang');
        $oLang->setBaseLanguage(2);

        $this->assertEquals(2, $oLang->getBaseLanguage());
        $this->assertEquals(2, $this->getSession()->getVariable('language'));
    }

    public function testSetBaseLanguageWithoutParams()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getBaseLanguage'));
        $oLang->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(1));
        $oLang->setBaseLanguage();

        $this->assertEquals(1, $this->getSession()->getVariable('language'));
    }

    /**
     * Testing template language setter
     */
    public function testSetTplLanguageIsAdmin()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage', 'getAdminTplLanguageArray'));
        $oLang->expects($this->once())->method('getBaseLanguage')->will($this->returnValue(777));
        $oLang->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oLang->expects($this->once())->method('getAdminTplLanguageArray')->will($this->returnValue(array(2 => 1)));
        $this->assertEquals(2, $oLang->setTplLanguage());
        $this->assertEquals(2, $this->getSession()->getVariable('tpllanguage'));
    }

    /**
     * Testing template language setter
     */
    public function testSetTplLanguageNonAdmin()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getBaseLanguage', 'getAdminTplLanguageArray'));
        $oLang->expects($this->once())->method('getBaseLanguage')->will($this->returnValue(777));
        $oLang->expects($this->once())->method('isAdmin')->will($this->returnValue(false));
        $oLang->expects($this->never())->method('getAdminTplLanguageArray');
        $this->assertEquals(777, $oLang->setTplLanguage());
        $this->assertEquals(777, $this->getSession()->getVariable('tpllanguage'));
    }

    /**
     * Testing validating language id
     */
    public function testValidateLanguage()
    {
        $oLang = oxNew('oxLang');

        $this->assertEquals(1, $oLang->validateLanguage(1));
        $this->assertEquals(0, $oLang->validateLanguage(3));
        $this->assertEquals(0, $oLang->validateLanguage('xxx'));
    }

    public function testGetLanguageNames()
    {
        $this->assertEquals(array(0 => 'Deutsch', 1 => 'English'), oxRegistry::getLang()->getLanguageNames());
    }

    //#1290: impossible to switch languages in admin, if third language is created as default and only one active
    public function testGetLanguageNamesWithNewLang()
    {
        $aLanguages = array('de' => 'Deutsch', 'ru' => 'Russian');
        $this->getConfig()->setConfigParam('aLanguages', $aLanguages);
        $oLangIds = array(0 => 'de', 2 => 'ru');
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getLanguageIds'));
        $oLang->expects($this->any())->method('getLanguageIds')->will($this->returnValue($oLangIds));

        $this->assertEquals(array(0 => 'Deutsch', 2 => 'Russian'), $oLang->getLanguageNames());
    }


    public function testGetLangTranslationArray()
    {
        $oSubj = $this->getProxyClass("oxLang");
        $aTrArray = $oSubj->UNITgetLangTranslationArray();
        $this->assertTrue(isset($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"]));
        $this->assertEquals($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"], "[?] Sie haben Fragen zu diesem Artikel?");
    }

    public function testGetLangTranslationArrayLang1()
    {
        $oSubj = $this->getProxyClass("oxLang");
        $aTrArray = $oSubj->UNITgetLangTranslationArray(1);
        $this->assertTrue(isset($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"]));
        $this->assertEquals($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"], "[?] Have questions about this product?");
    }

    public function testGetLangTranslationArrayIsSetInCache()
    {
        $oSubj = $this->getProxyClass("oxLang");
        $oSubj->setNonPublicVar('_aLangCache', array('langcache_0_1_' . $this->getConfig()->getShopId() . '_basic_default' => array('1' => array("ACCOUNT_LOGIN" => "Login"))));
        $aTrArray = $oSubj->UNITgetLangTranslationArray(1);
        $this->assertTrue(isset($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"]));
        $this->assertEquals($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"], "[?] Have questions about this product?");
    }

    public function testGetLangTranslationArrayIfBaseLAngNotSet()
    {
        $oSubj = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getBaseLanguage'));
        $oSubj->expects($this->any())->method('getBaseLanguage')->will($this->returnValue(null));
        $aTrArray = $oSubj->UNITgetLangTranslationArray();
        $this->assertTrue(isset($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"]));
        $this->assertEquals($aTrArray["QUESTIONS_ABOUT_THIS_PRODUCT_2"], "[?] Sie haben Fragen zu diesem Artikel?");
    }

    public function testGetLangTranslationArrayModuleFile()
    {
        oxRegistry::getUtils()->oxResetFileCache();

        //writing a test file
        $sFileContents = '<?php $aLang = array( "charset" => "UTF-8", "TESTKEY" => "testVal");';
        $sFileName = getShopBasePath() . "/Application/views/azure/de/my_lang.php";
        $sShopId = $this->getConfig()->getShopId();
        $sCacheKey = "languagefiles__0_$sShopId";
        oxRegistry::getUtils()->toFileCache($sCacheKey, null);

        file_put_contents($sFileName, $sFileContents);
        $oSubj = $this->getProxyClass("oxLang");
        $aTrArray = $oSubj->UNITgetLangTranslationArray();

        $this->assertTrue(isset($aTrArray["TESTKEY"]));

        //cleaning up
        $this->assertTrue(file_exists($sFileName));
        unlink($sFileName);
        $this->assertFalse(file_exists($sFileName));

        $this->assertEquals("testVal", $aTrArray["TESTKEY"]);

        oxRegistry::getUtils()->toFileCache($sCacheKey, null);
    }

    /**
     * Testing getLanguageArray() - if returned array keys are languages base ID's,
     * not loop counter values
     */
    public function testGetLanguageArrayHasGoodKeysValues()
    {
        $aLanguages = array('de' => 'Deutch', 'en' => 'English', 'ru' => 'Russian');
        $this->getConfig()->setConfigParam('aLanguages', $aLanguages);

        $aLangParams['de']['baseId'] = 0;
        $aLangParams['de']['abbr'] = 'de';
        $aLangParams['ru']['baseId'] = 1;
        $aLangParams['ru']['abbr'] = 'ru';
        $aLangParams['en']['baseId'] = 3;
        $aLangParams['en']['abbr'] = 'ru';

        $this->getConfig()->setConfigParam('aLanguageParams', $aLangParams);

        $oLang = oxNew('oxLang');
        $aKeys = array(0, 3, 1);

        $this->assertEquals($aKeys, array_keys($oLang->getLanguageArray()));
    }

    /**
     * Testing getLanguageIds() - if returns correct languages abbervations array
     * when language params array exists
     */
    public function testGetLanguageIds()
    {
        $aLanguages = array('de' => 'Deutch', 'en' => 'English', 'ru' => 'Russian');
        $this->getConfig()->setConfigParam('aLanguages', $aLanguages);

        $aLangParams['de']['baseId'] = 0;
        $aLangParams['de']['abbr'] = 'de';
        $aLangParams['ru']['baseId'] = 1;
        $aLangParams['ru']['abbr'] = 'ru';
        $aLangParams['en']['baseId'] = 3;
        $aLangParams['en']['abbr'] = 'ru';

        $this->getConfig()->setConfigParam('aLanguageParams', $aLangParams);

        $oLang = new oxLang();
        $aLangIds = array(0 => 'de', 1 => 'ru', 3 => 'en');

        $this->assertEquals($aLangIds, $oLang->getLanguageIds());
    }

    /**
     * Testing getLanguageIds() - if returns correct languages abbervations array
     * when language params array does not exists
     */
    public function testGetLanguageIdsWhenLangParamsNotExists()
    {
        $aLangIds = array(0 => 'de', 1 => 'en');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getConfigParam"));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('aLanguageParams'))->will($this->returnValue(null));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('aLanguages'))->will($this->returnValue($aLangIds));

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("getConfig"));
        $oLang->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(array(0, 1), $oLang->getLanguageIds());
    }

    /**
     * Testing detecting language by browser
     */
    public function testDetectLanguageByBrowser()
    {
        // preparing fixture
        $oDe = new stdClass();
        $oDe->id = 0;
        $oDe->abbr = 'de';
        $oDe->active = '1';

        $oEng = clone $oDe;
        $oEng->id = 1;
        $oEng->abbr = 'en';
        $oEng->active = '1';

        $aLangArray = array($oDe, $oEng);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getLanguageArray'));
        $oLang->expects($this->any())->method('getLanguageArray')->with($this->equalTo(null), $this->equalTo(true))->will($this->returnValue($aLangArray));

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-EN';
        $this->assertEquals(1, $oLang->detectLanguageByBrowser());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
        $this->assertEquals(1, $oLang->detectLanguageByBrowser());
    }

    /**
     * Testing detecting language by browser - no such language in shop
     */
    public function testDetectLanguageByBrowser_langNotInShop()
    {
        // preparing fixture
        $oDe = new stdClass();
        $oDe->id = 0;
        $oDe->abbr = 'de';

        $oEng = clone $oDe;
        $oEng->id = 1;
        $oEng->abbr = 'en';

        $aLangArray = array($oDe, $oEng);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getLanguageArray'));
        $oLang->expects($this->once())->method('getLanguageArray')->with($this->equalTo(null), $this->equalTo(true))->will($this->returnValue($aLangArray));

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'es';
        $this->assertNull($oLang->detectLanguageByBrowser());
    }

    /**
     * Testing detecting language by browser - cant detect browser lang
     */
    public function testDetectLanguageByBrowser_cantDetectLanguage()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getLanguageArray'));
        $oLang->expects($this->never())->method('getLanguageArray');

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
        $this->assertNull($oLang->detectLanguageByBrowser());
    }

    /**
     * Testing detecting language - getting language from cookie
     */
    public function testBaseLanguage_getsFromCookie()
    {
        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', null);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', null);
        $this->setRequestParameter('aLanguageURLs', null);

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, array('getOxCookie'));
        $oUtilsServer->expects($this->exactly(2))->method('getOxCookie')->with($this->equalTo('language'))->will($this->returnValue(1));

        oxTestModules::addModuleObject('oxUtilsServer', $oUtilsServer);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('detectLanguageByBrowser', 'validateLanguage'));
        $oLang->expects($this->any())->method('validateLanguage')->with($this->equalTo(1))->will($this->returnValue(1));
        $oLang->expects($this->never('detectLanguageByBrowser'))->method('detectLanguageByBrowser');


        $this->assertEquals(1, $oLang->getBaseLanguage());
    }

    /**
     * Testing detecting language - setting cookie with language ID
     */
    public function testBaseLanguage_setsToCookie()
    {
        $this->setRequestParameter('changelang', null);
        $this->setRequestParameter('lang', 1);
        $this->setRequestParameter('tpllanguage', null);
        $this->setRequestParameter('language', null);
        $this->setRequestParameter('aLanguageURLs', null);

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, array('setOxCookie'));
        $oUtilsServer->expects($this->once())->method('setOxCookie')->with($this->equalTo('language'), $this->equalTo(1));

        oxTestModules::addModuleObject('oxUtilsServer', $oUtilsServer);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('validateLanguage'));
        $oLang->expects($this->any())->method('validateLanguage')->with($this->equalTo(1))->will($this->returnValue(1));

        $this->assertEquals(1, $oLang->getBaseLanguage());
    }

    /**
     * Testing oxLang::getObjectTplLanguage()
     *
     * @return null
     */
    public function testGetObjectTplLanguage()
    {
        $oStdLang = new stdClass();
        $oStdLang->active = 0;

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('isAdmin', 'getTplLanguage'));
        $oLang->expects($this->once())->method('isAdmin')->will($this->returnValue(false));
        $oLang->expects($this->once())->method('getTplLanguage')->will($this->returnValue(444));

        $this->assertEquals(444, $oLang->getObjectTplLanguage());
    }

    /**
     * Testing oxLang::getAdminTplLanguageArray()
     *
     * @return null
     */
    public function testGetAdminTplLanguageArray()
    {
        $oLt = new stdClass();
        $oLt->name = 'Lithuanian';
        $oLt->abbr = 'lt';
        $oLt->sort = 0;
        $oLt->id = 0;
        $oLt->selected = 0;
        $oLt->active = 0;

        $oLv = new stdClass();
        $oLv->name = 'Latvian';
        $oLv->abbr = 'lv';
        $oLv->sort = 1;
        $oLv->id = 1;
        $oLv->selected = 0;
        $oLv->active = 0;

        $oDe = new stdClass();
        $oDe->name = 'Deutsch';
        $oDe->abbr = 'de';
        $oDe->sort = 2;
        $oDe->id = 2;
        $oDe->selected = 0;
        $oDe->active = 0;

        $oEn = new stdClass();
        $oEn->name = 'English';
        $oEn->abbr = 'en';
        $oEn->sort = 3;
        $oEn->id = 3;
        $oEn->selected = 0;
        $oEn->active = 0;

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getLanguageIds', 'getLanguageArray'));
        $oLang->expects($this->once())->method('getLanguageArray')->will($this->returnValue(array($oLt, $oDe, $oEn)));

        $this->assertEquals(array(1 => $oDe, 2 => $oEn), $oLang->getAdminTplLanguageArray());
    }


    public function testGetLanguageMap()
    {
        oxTestModules::addFunction("oxUtils", "getLangCache", "{}");
        oxTestModules::addFunction("oxUtils", "setLangCache", "{}");

        $oLang = oxNew('oxLang');
        $aMapData = $oLang->UNITgetLanguageMap(1);

        $this->assertTrue(count($aMapData) > 0);
    }

    public function testGetMultiLangTables()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oLang = oxNew('oxLang');
        $aTable = $oLang->getMultiLangTables();
        $this->assertTrue(count($aTable) == 22);

        $this->getConfig()->setConfigParam('aMultiLangTables', array('table1', 'table2'));

        $aTable = $oLang->getMultiLangTables();
        $this->assertTrue(count($aTable) == 24);
    }

    /**
     * Test case for oxLang::_collectSimilar()
     *
     * @return null
     */
    public function testCollectSimilar()
    {
        $aData = array("A_1_1" => "1_1",
                       "A_1_2" => "1_2",
                       "A_1_3" => "1_3",
                       "A_1_4" => "1_4",

                       "B_1_1" => "1_1",
                       "B_1_2" => "1_2",
                       "B_1_3" => "1_3",
                       "B_1_4" => "1_4",
        );

        $aResData1 = array("A_1_1" => "1_1",
                           "A_1_2" => "1_2",
                           "A_1_3" => "1_3",
                           "A_1_4" => "1_4"
        );

        $aResData2 = array("B_1_1" => "1_1",
                           "B_1_2" => "1_2",
                           "B_1_3" => "1_3",
                           "B_1_4" => "1_4"
        );

        $oLang = oxNew('oxLang');

        // initial check..
        $this->assertEquals($aResData1, $oLang->UNITcollectSimilar($aData, "A_1_"));
        $this->assertEquals($aResData2, $oLang->UNITcollectSimilar($aData, "B_1_"));

        // checking if appends given array
        $aCollection = $oLang->UNITcollectSimilar($aData, "A_1_");
        $this->assertEquals($aResData1 + $aResData2, $oLang->UNITcollectSimilar($aData, "B_1_", $aCollection));
    }

    /**
     * Test case for oxLang::getSimilarByKey()
     *
     * @return null
     */
    public function testGetSimilarByKey()
    {
        $oLang = oxNew('oxLang');

        // non admin
        $aRes = $oLang->getSimilarByKey("DETAILS_VPE_MESSAGE", 0, false);
        $this->assertEquals(1, count($aRes));
        $this->assertTrue(isset($aRes["DETAILS_VPE_MESSAGE"]));

        $aRes = $oLang->getSimilarByKey("DETAILS_VPE_MESSAGE", 1, false);
        $this->assertEquals(1, count($aRes));
        $this->assertTrue(isset($aRes["DETAILS_VPE_MESSAGE"]));

        // non admin from map
//      map is being removed, deprecated
//        $aRes = $oLang->getSimilarByKey( "ADD_RECOMM_", 0, false );
//        $this->assertEquals( 2, count( $aRes ) );
//        $this->assertTrue( isset( $aRes["ADD_RECOMM_ADDRECOMMLINK1"] ) );
//
//        $aRes = $oLang->getSimilarByKey( "ADD_RECOMM_", 1, false );
//        $this->assertEquals( 2, count( $aRes ) );
//        $this->assertTrue( isset( $aRes["ADD_RECOMM_ADDRECOMMLINK1"] ) );

        // admin
        $aRes = $oLang->getSimilarByKey("GENERAL_FIELDS_", 0, true);
        $this->assertEquals(3, count($aRes));
        $this->assertTrue(isset($aRes["GENERAL_FIELDS_ADD"]));
        $this->assertTrue(isset($aRes["GENERAL_FIELDS_DELETE"]));

        $aRes = $oLang->getSimilarByKey("GENERAL_FIELDS_", 1, true);
        $this->assertEquals(3, count($aRes));
        $this->assertTrue(isset($aRes["GENERAL_FIELDS_ADD"]));
        $this->assertTrue(isset($aRes["GENERAL_FIELDS_DELETE"]));
    }

    public function testGetSeoReplaceChars()
    {
        $aLangChars = array('t1' => 'new1', 't3' => 'r3');
        $aExpResult = array(
            't1' => 'new1',
            't3' => 'r3',
        );

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("translateString"));
        $oLang->expects($this->once())->method('translateString')->will($this->returnValue($aLangChars));
        $aReplaceData = $oLang->getSeoReplaceChars(1);

        $this->assertEquals($aExpResult, $aReplaceData);
    }

    public function testGetActiveModuleInfo()
    {
        oxTestModules::addFunction('oxModulelist', 'getActiveModuleInfo', '{ return true; }');
        $oUV = $this->getProxyClass('oxlang');

        $this->assertTrue($oUV->UNITgetActiveModuleInfo());
    }

    /**
     * Data provider for testGetInvalidViews
     *
     * @return array
     */
    public function providerGetAllShopLanguageIds()
    {
        return array(
            array('aLanguageParams', 'aLanguages', array('lt' => 'Lithuanian', 'de' => 'Deutsch')),
            array('aLanguages', 'aLanguageParams',
                  array('de' => array('baseId' => 0,
                                      'active' => "1",
                                      'sort'   => "1",
                  ),
                        'lt' => array('baseId' => 0,
                                      'active' => "1",
                                      'sort'   => "2",
                        ),
                  )),
        );
    }

    /**
     * Tests getting list of invalid views
     *
     * @param string $sLanguageParamNameDisabled - language config parameter that will be disabled
     * @param string $sLanguageParamName         - language config parameter that will be used
     * @param array  $aLanguageParamValue        - language config parameter value
     *
     * @dataProvider providerGetAllShopLanguageIds
     */
    public function testGetAllShopLanguageIds($sLanguageParamNameDisabled, $sLanguageParamName, $aLanguageParamValue)
    {
        $oDb = oxDb::getDb();

        $this->_setBaseShopLanguageParameters();

        // disable language config parameter because we are testing each language parameter separately
        $oDb->execute("delete from `oxconfig` WHERE `oxvarname` = '{$sLanguageParamNameDisabled}' ");

        $aAssertLanguageIds = array(0 => 'de', 1 => 'ru', 3 => 'en');

        /** @var oxLang $oLang */
        $oLang = oxNew('oxLang');
        $aAllShopLanguageIds = $oLang->getAllShopLanguageIds();

        $aMissingLanguages = array_diff($aAssertLanguageIds, $aAllShopLanguageIds);

        $this->assertEquals(0, count($aMissingLanguages), "All shop language array is not as expected");
    }

    /**
     *
     */
    private function _setBaseShopLanguageParameters()
    {
        $aLanguages = array(
            'de' => 'Deutch',
            'en' => 'English',
            'ru' => 'Russian'
        );
        $aLanguageParams = array(
            'de' => array('baseId' => 0, 'abbr' => 'de'),
            'ru' => array('baseId' => 1, 'abbr' => 'ru'),
            'en' => array('baseId' => 3, 'abbr' => 'en'),
        );

        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $aLanguages);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $aLanguageParams);
        $this->getConfig()->setConfigParam('aLanguages', $aLanguages);
        $this->getConfig()->setConfigParam('aLanguageParams', $aLanguageParams);
    }

    public function testIsTranslatedDefaultValue()
    {
        $oLang = oxNew('oxLang');
        $this->assertTrue($oLang->isTranslated());
    }

    public function testIsTranslatedSetTrue()
    {
        $oLang = oxNew('oxLang');
        $oLang->setIsTranslated();
        $this->assertTrue($oLang->isTranslated());
    }

    public function testIsTranslatedSetFalse()
    {
        $oLang = oxNew('oxLang');
        $oLang->setIsTranslated(false);
        $this->assertFalse($oLang->isTranslated());
    }

    public function testIsTranslatedInTranslationActionTranslationNotFound()
    {
        $oLang = oxNew('oxLang');
        $oLang->translateString('NOT_EXISTING_KEY');
        $this->assertFalse($oLang->isTranslated());
    }

    public function testIsTranslatedInTranslationActionTranslationFound()
    {
        $oLang = oxNew('oxLang');
        $oLang->translateString('HOME');
        $this->assertTrue($oLang->isTranslated());
    }

    /**
     * Test if BUG #5775 is fixed
     * "oxLang::processUrl" did not append the language parameter to the URL
     * if it was the same as the shops default language
     */
    public function testProcessUrlAppendsLanguageParameterOnDefaultLanguageAndDifferentBrowserLanguage()
    {
        $aLanguages = array(
            'de' => 'Deutsch',
            'en' => 'English',
        );

        $aLanguageParams = array(
            'de' => array('baseId' => 0, 'abbr' => 'de', 'active' => true),
            'en' => array('baseId' => 1, 'abbr' => 'en', 'active' => true),
        );

        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $aLanguages);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $aLanguageParams);
        $this->getConfig()->setConfigParam('aLanguages', $aLanguages);
        $this->getConfig()->setConfigParam('aLanguageParams', $aLanguageParams);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('_getBrowserLanguage'));
        $oLang->expects($this->any())->method('_getBrowserLanguage')->will(
            $this->returnValue("en")
        );

        // Set default language to german
        $this->getConfig()->setConfigParam('sDefaultLang', 0);

        // Fake language selection in frontend to shop default language
        $oLang->setBaseLanguage(0);

        $shopURL = $this->getConfig()->getShopHomeUrl();
        $processURL = $shopURL . "cl=account&amp;";
        $expectingURL = $shopURL . "cl=account&amp;lang=0&amp;";

        $processedURL = $oLang->processUrl($processURL);

        $this->assertEquals(
            $expectingURL,
            $processedURL,
            "Processed URL does not contain default Shop Language as parameter."
        );
    }
}
