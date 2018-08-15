<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use Language_Main;
use \oxRegistry;
use \oxTestModules;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests for Language_Main class
 */
class LanguageMainTest extends \OxidTestCase
{

    /**
     * Language_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Language_Main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNull($aViewData["allowSharedEdit"]);
        $this->assertNull($aViewData["malladmin"]);

        $this->assertNull($aViewData["updatelist"]);

        $this->assertEquals('language_main.tpl', $sTplName);
    }

    /**
     * Language_Main::Save() test case, testing upadating existing language
     *
     * @return null
     */
    public function testSave_update()
    {
        $aNewParams['abbr'] = 'en';
        $aNewParams['active'] = 1;
        $aNewParams['default'] = false;
        $aNewParams['sort'] = 10;
        $aNewParams['desc'] = 'testEnglish';
        $aNewParams['baseurl'] = 'testBaseUrl';
        $aNewParams['basesslurl'] = 'testBaseSslUrl';

        $aDefaultLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aDefaultLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 2);
        $aDefaultLangData['lang'] = array("de" => "Deutsch", "en" => "English");
        $aDefaultLangData['urls'] = array(0 => "", 1 => "testBaseUrl");
        $aDefaultLangData['sslUrls'] = array(0 => "", 1 => "testBaseSslUrl");

        $this->setRequestParameter("oxid", "en");
        $this->setRequestParameter("editval", $aNewParams);

        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "testEnglish");
        $aLangData['urls'] = array(0 => "", 1 => "testBaseUrl");
        $aLangData['sslUrls'] = array(0 => "", 1 => "testBaseSslUrl");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aLanguageParams'), $this->equalTo($aLangData['params']));
        $oConfig->expects($this->at(1))->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aLanguages'), $this->equalTo($aLangData['lang']));
        $oConfig->expects($this->at(2))->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aLanguageURLs'), $this->equalTo($aLangData['urls']));
        $oConfig->expects($this->at(3))->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aLanguageSSLURLs'), $this->equalTo($aLangData['sslUrls']));
        $oConfig->setConfigParam("blAllowSharedEdit", true);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, array("_validateInput", "getConfig", "_getLanguages"), array(), '', false);
        $oMainLang->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oMainLang->expects($this->once())->method('_getLanguages')->will($this->returnValue($aDefaultLangData));
        $oMainLang->expects($this->once())->method('_validateInput')->will($this->returnValue(true));

        $oMainLang->save();
    }

    /**
     * Language_Main::Save() test case, saveing new language
     *
     * @return null
     */
    public function testSave_addingNewMultilangFieldsToDb()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "English");
        $aLangData['urls'] = array(0 => "", 1 => "testBaseUrl");
        $aLangData['sslUrls'] = array(0 => "", 1 => "testBaseSslUrl");

        $aNewParams['abbr'] = 'fr';
        $aNewParams['active'] = 1;
        $aNewParams['default'] = false;
        $aNewParams['sort'] = 10;
        $aNewParams['desc'] = 'testFr';

        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("editval", $aNewParams);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->any())->method('saveShopConfVar')->will($this->returnValue(true));
        $oConfig->setConfigParam("blAllowSharedEdit", true);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, array("_validateInput", "getConfig", "_checkMultilangFieldsExistsInDb", "_addNewMultilangFieldsToDb", "_getLanguages"), array(), '', false);
        $oMainLang->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oMainLang->expects($this->once())->method('_getLanguages')->will($this->returnValue($aLangData));
        $oMainLang->expects($this->once())->method('_validateInput')->will($this->returnValue(true));
        $oMainLang->expects($this->once())->method('_checkMultilangFieldsExistsInDb')->with($this->equalTo('fr'))->will($this->returnValue(false));
        $oMainLang->expects($this->once())->method('_addNewMultilangFieldsToDb');

        $oMainLang->save();
    }

    /**
     * Language_Main::GetLanguageInfo() test case
     *
     * @return null
     */
    public function testGetLanguageInfo()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "testEnglish");
        $aLangData['urls'] = array(0 => "", 1 => "testBaseUrl");
        $aLangData['sslUrls'] = array(0 => "", 1 => "testBaseSslUrl");

        $aRes['baseId'] = 1;
        $aRes['active'] = 1;
        $aRes['default'] = false;
        $aRes['sort'] = 10;
        $aRes['abbr'] = "en";
        $aRes['desc'] = "testEnglish";
        $aRes['baseurl'] = "testBaseUrl";
        $aRes['basesslurl'] = "testBaseSslUrl";

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertEquals($aRes, $oView->UNITgetLanguageInfo("en"));
    }

    /**
     * Language_Main::GetLanguages() test case
     *
     * @return null
     */
    public function testGetLanguages()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 2);
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "English");
        $aLangData['urls'] = array(0 => "", 1 => "");
        $aLangData['sslUrls'] = array(0 => "", 1 => "");

        $oView = $this->getProxyClass("Language_Main");

        $this->assertEquals($aLangData, $oView->UNITgetLanguages());
    }

    /**
     * Language_Main::UpdateAbbervation() test case
     *
     * @return null
     */
    public function testUpdateAbbervation()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "testEnglish");

        $aRes['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aRes['params']['fr'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aRes['lang'] = array("de" => "Deutsch", "fr" => "testEnglish");

        // defining parameters
        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);
        $oView->UNITupdateAbbervation("en", "fr");

        $this->assertEquals($aRes, $oView->getNonPublicVar("_aLangData"));
    }

    /**
     * Language_Main::SortLangArraysByBaseId() test case
     *
     * @return null
     */
    public function testSortLangArraysByBaseId()
    {
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['lang'] = array("en" => "testEnglish", "de" => "Deutsch");
        $aLangData['urls'] = array(1 => "testBaseUrl", 0 => "");
        $aLangData['sslUrls'] = array(1 => "testBaseSslUrl", 0 => "");

        $aRes['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aRes['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aRes['lang'] = array("de" => "Deutsch", "en" => "testEnglish");
        $aRes['urls'] = array(0 => "", 1 => "testBaseUrl");
        $aRes['sslUrls'] = array(0 => "", 1 => "testBaseSslUrl");

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);
        $oView->UNITsortLangArraysByBaseId("en", "fr");

        $this->assertEquals($aRes, $oView->getNonPublicVar("_aLangData"));
    }

    /**
     * Language_Main::AssignDefaultLangParams() test case
     *
     * @return null
     */
    public function testAssignDefaultLangParams()
    {
        $aLangData = array("de" => "Deutsch", "en" => "testEnglish");

        $aRes['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aRes['en'] = array("baseId" => 1, "active" => 1, "sort" => 2);

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertEquals($aRes, $oView->UNITassignDefaultLangParams($aLangData));
    }

    /**
     * Language_Main::SetDefaultLang() test case
     *
     * @return null
     */
    public function testSetDefaultLang()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')->with($this->equalTo('str'), $this->equalTo('sDefaultLang'), $this->equalTo(1));

        /** @var MockObject|Language_Main $oView */
        $oView = $this->getMock($this->getProxyClassName('Language_Main'), array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->setNonPublicVar('_aLangData', $aLangData);

        $oView->_setDefaultLang("en");
    }

    /**
     * Language_Main::GetAvailableLangBaseId() test case
     *
     * @return null
     */
    public function testGetAvailableLangBaseId()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertEquals(2, $oView->UNITgetAvailableLangBaseId());
    }

    /**
     * Language_Main::CheckLangTranslations() test case
     *
     * @return null
     */
    public function testCheckLangTranslations()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getTranslationsDir"));
        $oConfig->expects($this->once())->method("getTranslationsDir")->with($this->equalTo('lang.php'), oxRegistry::getLang()->getLanguageAbbr(1))->will($this->returnValue("dir/to/langfile"));

        /** @var MockObject|Language_Main $oView */
        $oView = $this->getMock($this->getProxyClassName('Language_Main'), array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->setNonPublicVar('_aLangData', $aLangData);

        $oView->_checkLangTranslations("en");

        //no errors should be added to session
        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Language_Main::CheckLangTranslations() test case
     *
     * @return null
     */
    public function testCheckLangTranslations_withError()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getTranslationsDir"));

        $oConfig->expects($this->once())->method("getTranslationsDir")->with($this->equalTo('lang.php'), oxRegistry::getLang()->getLanguageAbbr(1))->will($this->returnValue(""));

        /** @var MockObject|Language_Main $oView */
        $oView = $this->getMock($this->getProxyClassName('Language_Main'), array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->setNonPublicVar('_aLangData', $aLangData);

        $oView->_checkLangTranslations("en");

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_NOTRANSLATIONS_WARNING");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Language_Main::CheckMultilangFieldsExistsInDb() test case
     *
     * @return null
     */
    public function testCheckMultilangFieldsExistsInDb()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['params']['fr'] = array("baseId" => 9, "active" => 1, "sort" => 20, "default" => false);

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertTrue($oView->UNITcheckMultilangFieldsExistsInDb("de"));
        $this->assertTrue($oView->UNITcheckMultilangFieldsExistsInDb("en"));
        $this->assertFalse($oView->UNITcheckMultilangFieldsExistsInDb("fr"));
    }

    /**
     * Language_Main::AddNewMultilangFieldsToDb() test case
     *
     * @return null
     */
    public function testAddNewMultilangFieldsToDb()
    {
        oxTestModules::addFunction("oxDbMetaDataHandler", "addNewLangToDb", "{return true;}");

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", null);

        $oView->UNITaddNewMultilangFieldsToDb();

        //no errors should be added to session
        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Language_Main::AddNewMultilangFieldsToDb() test case
     *
     * @return null
     */
    public function testAddNewMultilangFieldsToDb_withError()
    {
        oxTestModules::addFunction("oxDbMetaDataHandler", "addNewLangToDb", "{Throw new Exception();}");

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", null);

        $oView->UNITaddNewMultilangFieldsToDb();

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_ERROR_ADDING_MULTILANG_FIELDS");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Language_Main::CheckLangExists() test case
     *
     * @return null
     */
    public function testCheckLangExists()
    {
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "English");

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertTrue($oView->UNITcheckLangExists("de"));
        $this->assertTrue($oView->UNITcheckLangExists("en"));
        $this->assertFalse($oView->UNITcheckLangExists("fr"));
    }

    /**
     * Language_Main::SortLangParamsByBaseIdCallback() test case
     *
     * @return null
     */
    public function testSortLangParamsByBaseIdCallback()
    {
        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertEquals(1, $oView->UNITsortLangParamsByBaseIdCallback($aLangData['params']['en'], $aLangData['params']['de']));
        $this->assertEquals(-1, $oView->UNITsortLangParamsByBaseIdCallback($aLangData['params']['de'], $aLangData['params']['en']));
    }

    /**
     * Testing validation errors - language already exist
     *
     * @return null
     */
    public function testValidateInput_langExists()
    {
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", array('abbr' => 'en'));

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, array("_checkLangExists"));
        $oMainLang->expects($this->once())->method('_checkLangExists')->with($this->equalTo("en"))->will($this->returnValue(true));

        $this->assertFalse($oMainLang->UNITvalidateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_ALREADYEXISTS_ERROR");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Testing validation errors - empty language name
     *
     * @return null
     */
    public function testValidateInput_emptyLangName()
    {
        $this->setRequestParameter("oxid", "1");
        $this->setRequestParameter("editval", array('abbr' => 'en', "desc" => ""));

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, array("_checkLangExists"));
        $oMainLang->expects($this->never())->method('_checkLangExists');

        $this->assertFalse($oMainLang->UNITvalidateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_EMPTYLANGUAGENAME_ERROR");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Testing validation errors - all values valid
     *
     * @return null
     */
    public function testValidateInput_validInput()
    {
        $this->setRequestParameter("oxid", "1");
        $this->setRequestParameter("editval", array('abbr' => 'en', "desc" => "English"));

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, array("_checkLangExists"));
        $oMainLang->expects($this->never())->method('_checkLangExists');

        $this->assertTrue($oMainLang->UNITvalidateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Testing validation errors - abbreviation contains forbidden characters
     */
    public function testValidateInputInvalidAbbreviation()
    {
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", array('abbr' => 'ch-xx'));

        $mainLanguage = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, array("_checkLangExists"));
        $mainLanguage->expects($this->once())->method('_checkLangExists')->with($this->equalTo("ch-xx"))->will($this->returnValue(false));

        $this->assertFalse($mainLanguage->_validateInput());

        $exceptions = oxRegistry::getSession()->getVariable("Errors");
        $exception = unserialize($exceptions["default"][0]);
        $errorMessage = oxRegistry::getLang()->translateString("LANGUAGE_ABBREVIATION_INVALID_ERROR");

        $this->assertEquals($errorMessage, $exception->getOxMessage());
    }
}
