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

/**
 * Test Language_Main module
 */
class modLanguageMain extends Language_Main
{

    /**
     * Set any field value.
     *
     * @param string $sName  Field name
     * @param string $sValue Field value
     *
     * @return null
     */
    public function setVar($sName, $sValue)
    {
        $this->$sName = $sValue;
    }
}

/**
 * Tests for Language_Main class
 */
class Unit_Admin_LanguageMainTest extends OxidTestCase
{

    /**
     * Language_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Language_Main();
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

        modConfig::setRequestParameter("oxid", "en");
        modConfig::setRequestParameter("editval", $aNewParams);


        $aLangData['params']['de'] = array("baseId" => 0, "active" => 1, "sort" => 1);
        $aLangData['params']['en'] = array("baseId" => 1, "active" => 1, "sort" => 10, "default" => false);
        $aLangData['lang'] = array("de" => "Deutsch", "en" => "testEnglish");
        $aLangData['urls'] = array(0 => "", 1 => "testBaseUrl");
        $aLangData['sslUrls'] = array(0 => "", 1 => "testBaseSslUrl");

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aLanguageParams'), $this->equalTo($aLangData['params']));
        $oConfig->expects($this->at(1))->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aLanguages'), $this->equalTo($aLangData['lang']));
        $oConfig->expects($this->at(2))->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aLanguageURLs'), $this->equalTo($aLangData['urls']));
        $oConfig->expects($this->at(3))->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aLanguageSSLURLs'), $this->equalTo($aLangData['sslUrls']));


        $oMainLang = $this->getMock("Language_Main", array("_validateInput", "getConfig", "_getLanguages"), array(), '', false);
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

        modConfig::setRequestParameter("oxid", -1);
        modConfig::setRequestParameter("editval", $aNewParams);

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->any())->method('saveShopConfVar')->will($this->returnValue(true));


        $oMainLang = $this->getMock("Language_Main", array("_validateInput", "getConfig", "_checkMultilangFieldsExistsInDb", "_addNewMultilangFieldsToDb", "_getLanguages"), array(), '', false);
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
        $sOldId = 'en';
        $sNewId = 'fr';

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

        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')->with($this->equalTo('str'), $this->equalTo('sDefaultLang'), $this->equalTo(1));

        $oView = $this->getMock("modLanguageMain", array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->setVar('_aLangData', $aLangData);

        $oView->UNITsetDefaultLang("en");
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

        $oConfig = $this->getMock("oxConfig", array("getTranslationsDir"));
        $oConfig->expects($this->once())->method("getTranslationsDir")->with($this->equalTo('lang.php'), oxRegistry::getLang()->getLanguageAbbr(1))->will($this->returnValue("dir/to/langfile"));

        $oView = $this->getMock("modLanguageMain", array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->setVar('_aLangData', $aLangData);

        $oView->UNITcheckLangTranslations("en");

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

        $oConfig = $this->getMock("oxConfig", array("getTranslationsDir"));

        $oConfig->expects($this->once())->method("getTranslationsDir")->with($this->equalTo('lang.php'), oxRegistry::getLang()->getLanguageAbbr(1))->will($this->returnValue(""));

        $oView = $this->getMock("modLanguageMain", array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->setVar('_aLangData', $aLangData);

        $oView->UNITcheckLangTranslations("en");

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
        $aLangData['params']['fr'] = array("baseId" => 5, "active" => 1, "sort" => 20, "default" => false);

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
        $oView->setNonPublicVar("_aLangData", $aLangData);

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
        $oView->setNonPublicVar("_aLangData", $aLangData);

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
        modConfig::setRequestParameter("oxid", "-1");
        modConfig::setRequestParameter("editval", array('abbr' => 'en'));

        $oMainLang = $this->getMock("Language_Main", array("_checkLangExists"));
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
        modConfig::setRequestParameter("oxid", "1");
        modConfig::setRequestParameter("editval", array('abbr' => 'en', "desc" => ""));

        $oMainLang = $this->getMock("Language_Main", array("_checkLangExists"));
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
        modConfig::setRequestParameter("oxid", "1");
        modConfig::setRequestParameter("editval", array('abbr' => 'en', "desc" => "English"));

        $oMainLang = $this->getMock("Language_Main", array("_checkLangExists"));
        $oMainLang->expects($this->never())->method('_checkLangExists');

        $this->assertTrue($oMainLang->UNITvalidateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $this->assertNull($aEx);
    }

    /**
     * Testing validation errors - abbreviation contains forbidden characters
     *
     * @return null
     */
    public function testValidateInputInvalidAbbreviation()
    {
        modConfig::setRequestParameter("oxid", "-1");
        modConfig::setRequestParameter("editval", array('abbr' => 'ch-xx'));

        $oMainLang = $this->getMock("Language_Main", array("_checkLangExists"));
        $oMainLang->expects($this->once())->method('_checkLangExists')->with($this->equalTo("ch-xx"))->will($this->returnValue(false));

        $this->assertFalse($oMainLang->_validateInput());

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sErrMsg = oxRegistry::getLang()->translateString("LANGUAGE_ABBREVIATION_INVALID_ERROR");

        $this->assertEquals($sErrMsg, $oEx->getOxMessage());
    }
}
