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
class LanguageMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Language_Main::Render() test case
     */
    public function testRender()
    {
        $oView = oxNew('Language_Main');
        $sTplName = $oView->render();

        $this->assertSame('language_main', $sTplName);
    }

    /**
     * Language_Main::Save() test case, testing upadating existing language
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

        $aDefaultLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aDefaultLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 2];
        $aDefaultLangData['lang'] = ["de" => "Deutsch", "en" => "English"];
        $aDefaultLangData['urls'] = [0 => "", 1 => "testBaseUrl"];
        $aDefaultLangData['sslUrls'] = [0 => "", 1 => "testBaseSslUrl"];

        $this->setRequestParameter("oxid", "en");
        $this->setRequestParameter("editval", $aNewParams);

        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aLangData['lang'] = ["de" => "Deutsch", "en" => "testEnglish"];
        $aLangData['urls'] = [0 => "", 1 => "testBaseUrl"];
        $aLangData['sslUrls'] = [0 => "", 1 => "testBaseSslUrl"];

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["saveShopConfVar"]);
        $oConfig
            ->method('saveShopConfVar')
            ->withConsecutive(
                ['aarr', 'aLanguageParams', $aLangData['params']],
                ['aarr', 'aLanguages', $aLangData['lang']],
                ['arr', 'aLanguageURLs', $aLangData['urls']],
                ['arr', 'aLanguageSSLURLs', $aLangData['sslUrls']]
            );

        $oConfig->setConfigParam("blAllowSharedEdit", true);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, ["validateInput", "getConfig", "getLanguages"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oMainLang->expects($this->once())->method('getLanguages')->willReturn($aDefaultLangData);
        $oMainLang->expects($this->once())->method('validateInput')->willReturn(true);

        $oMainLang->save();
    }

    /**
     * Language_Main::Save() test case, saveing new language
     */
    public function testSave_addingNewMultilangFieldsToDb()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aLangData['lang'] = ["de" => "Deutsch", "en" => "English"];
        $aLangData['urls'] = [0 => "", 1 => "testBaseUrl"];
        $aLangData['sslUrls'] = [0 => "", 1 => "testBaseSslUrl"];

        $aNewParams['baseurl'] = 'testUrl';
        $aNewParams['basesslurl'] = 'testUrl';
        $aNewParams['abbr'] = 'fr';
        $aNewParams['active'] = 1;
        $aNewParams['default'] = false;
        $aNewParams['sort'] = 10;
        $aNewParams['desc'] = 'testFr';

        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("editval", $aNewParams);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["saveShopConfVar"]);
        $oConfig->method('saveShopConfVar')->willReturn(true);
        $oConfig->setConfigParam("blAllowSharedEdit", true);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, ["validateInput", "getConfig", "checkMultilangFieldsExistsInDb", "addNewMultilangFieldsToDb", "getLanguages"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oMainLang->expects($this->once())->method('getLanguages')->willReturn($aLangData);
        $oMainLang->expects($this->once())->method('validateInput')->willReturn(true);
        $oMainLang->expects($this->once())->method('checkMultilangFieldsExistsInDb')->with('fr')->willReturn(false);
        $oMainLang->expects($this->once())->method('addNewMultilangFieldsToDb');

        $oMainLang->save();
    }

    /**
     * Language_Main::GetLanguageInfo() test case
     */
    public function testGetLanguageInfo()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aLangData['lang'] = ["de" => "Deutsch", "en" => "testEnglish"];
        $aLangData['urls'] = [0 => "", 1 => "testBaseUrl"];
        $aLangData['sslUrls'] = [0 => "", 1 => "testBaseSslUrl"];

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

        $this->assertEquals($aRes, $oView->getLanguageInfo("en"));
    }

    /**
     * Language_Main::GetLanguages() test case
     */
    public function testGetLanguages()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 2];
        $aLangData['lang'] = ["de" => "Deutsch", "en" => "English"];
        $aLangData['urls'] = [0 => "", 1 => ""];
        $aLangData['sslUrls'] = [0 => "", 1 => ""];

        $oView = $this->getProxyClass("Language_Main");

        $this->assertSame($aLangData, $oView->getLanguages());
    }

    /**
     * Language_Main::UpdateAbbervation() test case
     */
    public function testUpdateAbbervation()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aLangData['lang'] = ["de" => "Deutsch", "en" => "testEnglish"];

        $aRes['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aRes['params']['fr'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aRes['lang'] = ["de" => "Deutsch", "fr" => "testEnglish"];

        // defining parameters
        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);
        $oView->updateAbbervation("en", "fr");

        $this->assertEquals($aRes, $oView->getNonPublicVar("_aLangData"));
    }

    /**
     * Language_Main::SortLangArraysByBaseId() test case
     */
    public function testSortLangArraysByBaseId()
    {
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['lang'] = ["en" => "testEnglish", "de" => "Deutsch"];
        $aLangData['urls'] = [1 => "testBaseUrl", 0 => ""];
        $aLangData['sslUrls'] = [1 => "testBaseSslUrl", 0 => ""];

        $aRes['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aRes['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aRes['lang'] = ["de" => "Deutsch", "en" => "testEnglish"];
        $aRes['urls'] = [0 => "", 1 => "testBaseUrl"];
        $aRes['sslUrls'] = [0 => "", 1 => "testBaseSslUrl"];

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);
        $oView->sortLangArraysByBaseId("en", "fr");

        $this->assertEquals($aRes, $oView->getNonPublicVar("_aLangData"));
    }

    /**
     * Language_Main::AssignDefaultLangParams() test case
     */
    public function testAssignDefaultLangParams()
    {
        $aLangData = ["de" => "Deutsch", "en" => "testEnglish"];

        $aRes['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aRes['en'] = ["baseId" => 1, "active" => 1, "sort" => 2];

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertSame($aRes, $oView->assignDefaultLangParams($aLangData));
    }

    /**
     * Language_Main::SetDefaultLang() test case
     */
    public function testSetDefaultLang()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["saveShopConfVar"]);
        $oConfig->expects($this->atLeastOnce())->method('saveShopConfVar')->with('str', 'sDefaultLang', 1);

        /** @var MockObject|Language_Main $oView */
        $oView = $this->getMock($this->getProxyClassName('Language_Main'), ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->setNonPublicVar('_aLangData', $aLangData);

        $oView->setDefaultLang("en");
    }

    /**
     * Language_Main::GetAvailableLangBaseId() test case
     */
    public function testGetAvailableLangBaseId()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertSame(2, $oView->getAvailableLangBaseId());
    }

    /**
     * Language_Main::CheckLangTranslations() test case
     */
    public function testCheckLangTranslations()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTranslationsDir"]);
        $oConfig->expects($this->once())->method("getTranslationsDir")->with('lang.php', oxRegistry::getLang()->getLanguageAbbr(1))->willReturn("dir/to/langfile");

        /** @var MockObject|Language_Main $oView */
        $oView = $this->getMock($this->getProxyClassName('Language_Main'), ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->setNonPublicVar('_aLangData', $aLangData);

        $oView->checkLangTranslations("en");

        //no errors should be added to session
        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Language_Main::CheckLangTranslations() test case
     */
    public function testCheckLangTranslations_withError()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTranslationsDir"]);

        $oConfig->expects($this->once())->method("getTranslationsDir")->with('lang.php', oxRegistry::getLang()->getLanguageAbbr(1))->willReturn("");

        /** @var MockObject|Language_Main $oView */
        $oView = $this->getMock($this->getProxyClassName('Language_Main'), ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->setNonPublicVar('_aLangData', $aLangData);

        $oView->checkLangTranslations("en");

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_NOTRANSLATIONS_WARNING");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Language_Main::CheckMultilangFieldsExistsInDb() test case
     */
    public function testCheckMultilangFieldsExistsInDb()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];
        $aLangData['params']['fr'] = ["baseId" => 9, "active" => 1, "sort" => 20, "default" => false];

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertTrue($oView->checkMultilangFieldsExistsInDb("de"));
        $this->assertTrue($oView->checkMultilangFieldsExistsInDb("en"));
        $this->assertFalse($oView->checkMultilangFieldsExistsInDb("fr"));
    }

    /**
     * Language_Main::AddNewMultilangFieldsToDb() test case
     */
    public function testAddNewMultilangFieldsToDb()
    {
        oxTestModules::addFunction("oxDbMetaDataHandler", "addNewLangToDb", "{return true;}");

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", null);

        $oView->addNewMultilangFieldsToDb();

        //no errors should be added to session
        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Language_Main::AddNewMultilangFieldsToDb() test case
     */
    public function testAddNewMultilangFieldsToDb_withError()
    {
        oxTestModules::addFunction("oxDbMetaDataHandler", "addNewLangToDb", "{Throw new Exception();}");

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", null);

        $oView->addNewMultilangFieldsToDb();

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_ERROR_ADDING_MULTILANG_FIELDS");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Language_Main::CheckLangExists() test case
     */
    public function testCheckLangExists()
    {
        $aLangData['lang'] = ["de" => "Deutsch", "en" => "English"];

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertTrue($oView->checkLangExists("de"));
        $this->assertTrue($oView->checkLangExists("en"));
        $this->assertFalse($oView->checkLangExists("fr"));
    }

    /**
     * Language_Main::SortLangParamsByBaseIdCallback() test case
     */
    public function testSortLangParamsByBaseIdCallback()
    {
        $aLangData['params']['de'] = ["baseId" => 0, "active" => 1, "sort" => 1];
        $aLangData['params']['en'] = ["baseId" => 1, "active" => 1, "sort" => 10, "default" => false];

        $oView = $this->getProxyClass("Language_Main");
        $oView->setNonPublicVar("_aLangData", $aLangData);

        $this->assertSame(1, $oView->sortLangParamsByBaseIdCallback($aLangData['params']['en'], $aLangData['params']['de']));
        $this->assertSame(-1, $oView->sortLangParamsByBaseIdCallback($aLangData['params']['de'], $aLangData['params']['en']));
    }

    /**
     * Testing validation errors - language already exist
     */
    public function testValidateInput_langExists()
    {
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", ['abbr' => 'en']);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, ["checkLangExists"]);
        $oMainLang->expects($this->once())->method('checkLangExists')->with("en")->willReturn(true);

        $this->assertFalse($oMainLang->validateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_ALREADYEXISTS_ERROR");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Testing validation errors - empty language name
     */
    public function testValidateInput_emptyLangName()
    {
        $this->setRequestParameter("oxid", "1");
        $this->setRequestParameter("editval", ['abbr' => 'en', "desc" => ""]);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, ["checkLangExists"]);
        $oMainLang->expects($this->never())->method('checkLangExists');

        $this->assertFalse($oMainLang->validateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_EMPTYLANGUAGENAME_ERROR");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }

    /**
     * Testing validation errors - all values valid
     */
    public function testValidateInput_validInput()
    {
        $this->setRequestParameter("oxid", "1");
        $this->setRequestParameter("editval", ['abbr' => 'en', "desc" => "English"]);

        $oMainLang = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, ["checkLangExists"]);
        $oMainLang->expects($this->never())->method('checkLangExists');

        $this->assertTrue($oMainLang->validateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Testing validation errors - abbreviation contains forbidden characters
     */
    public function testValidateInputInvalidAbbreviation()
    {
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", ['abbr' => 'ch-xx']);

        $mainLanguage = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class, ["checkLangExists"]);
        $mainLanguage->expects($this->once())->method('checkLangExists')->with("ch-xx")->willReturn(false);

        $this->assertFalse($mainLanguage->validateInput());

        $exceptions = oxRegistry::getSession()->getVariable("Errors");
        $exception = unserialize($exceptions["default"][0]);
        $errorMessage = oxRegistry::getLang()->translateString("LANGUAGE_ABBREVIATION_INVALID_ERROR");

        $this->assertEquals($errorMessage, $exception->getOxMessage());
    }
}
