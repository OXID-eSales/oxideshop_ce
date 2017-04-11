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
 * Tests for Language_List class
 */
class Unit_Admin_LanguageListTest extends OxidTestCase
{

    /**
     * Language_List::DeleteEntry() test case
     *
     * @return null
     */
    public function testDeleteEntry()
    {
        modConfig::getInstance()->setConfigParam("blAllowSharedEdit", true);
        modConfig::setRequestParameter('oxid', 1);

        $iCnt = 0;

        $oConfig = $this->getMock("oxconfig", array("getConfigParam", "saveShopConfVar"));


        $oConfig->expects($this->at($iCnt++))->method('getConfigParam')->with($this->equalTo('aLanguageParams'))->will($this->returnValue(array(1 => array('baseId' => 1))));
        $oConfig->expects($this->at($iCnt++))->method('getConfigParam')->with($this->equalTo('aLanguages'))->will($this->returnValue(array(1 => 1)));
        $oConfig->expects($this->at($iCnt++))->method('getConfigParam')->with($this->equalTo('aLanguageURLs'))->will($this->returnValue(array(1 => 1)));
        $oConfig->expects($this->at($iCnt++))->method('getConfigParam')->with($this->equalTo('aLanguageSSLURLs'))->will($this->returnValue(array(1 => 1)));
        $oConfig->expects($this->at($iCnt++))->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aLanguageParams'), $this->equalTo(array()));
        $oConfig->expects($this->at($iCnt++))->method('saveShopConfVar')->with($this->equalTo('aarr'), $this->equalTo('aLanguages'), $this->equalTo(array()));
        $oConfig->expects($this->at($iCnt++))->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aLanguageURLs'), $this->equalTo(array()));
        $oConfig->expects($this->at($iCnt++))->method('saveShopConfVar')->with($this->equalTo('arr'), $this->equalTo('aLanguageSSLURLs'), $this->equalTo(array()));
        $oConfig->expects($this->at($iCnt++))->method('getConfigParam')->with($this->equalTo('sDefaultLang'))->will($this->returnValue(1));
        $oConfig->expects($this->at($iCnt++))->method('saveShopConfVar')->with($this->equalTo('str'), $this->equalTo('sDefaultLang'), $this->equalTo(0));

        $aTasks = array("getConfig");
        $aTasks[] = "_resetMultiLangDbFields";

        $oView = $this->getMock("Language_List", $aTasks, array(), '', false);
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $oView->expects($this->once())->method('_resetMultiLangDbFields')->with($this->equalTo(1));

        $oView->deleteEntry();
    }

    /**
     * Language_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Language_List();
        $this->assertEquals('language_list.tpl', $oView->render());
    }

    /**
     * Language_List::GetLanguagesList() test case
     *
     * @return null
     */
    public function testGetLanguagesList()
    {
        $oLang1 = new stdClass();
        $oLang1->id = 0;
        $oLang1->oxid = 'de';
        $oLang1->abbr = 'de';
        $oLang1->name = 'Deutsch';
        $oLang1->active = "1";
        $oLang1->sort = "1";
        $oLang1->selected = 1;
        $oLang1->default = true;

        $oLang2 = new stdClass();
        $oLang2->id = 1;
        $oLang2->oxid = 'en';
        $oLang2->abbr = 'en';
        $oLang2->name = 'English';
        $oLang2->active = "1";
        $oLang2->sort = "2";
        $oLang2->selected = 0;
        $oLang2->default = false;

        $oView = new Language_List();
        $this->assertEquals(array($oLang1, $oLang2), $oView->UNITgetLanguagesList());
    }

    /**
     * Language_List::SortLanguagesCallback() test case
     *
     * @return null
     */
    public function testSortLanguagesCallback()
    {
        $oView = $this->getProxyClass("Language_List");

        $oLang1 = new stdClass();
        $oLang1->sort = 'EN';
        $oLang2 = new stdClass();
        $oLang2->sort = 'DE';
        $this->assertEquals(1, $oView->UNITsortLanguagesCallback($oLang1, $oLang2));

        $oLang1 = new stdClass();
        $oLang1->sort = 'DE';
        $oLang2 = new stdClass();
        $oLang2->sort = 'EN';
        $this->assertEquals(-1, $oView->UNITsortLanguagesCallback($oLang1, $oLang2));

        $oLang1 = new stdClass();
        $oLang1->sort = 1;
        $oLang2 = new stdClass();
        $oLang2->sort = 2;
        $oView->setNonPublicVar("_sDefSortOrder", "desc");
        $this->assertEquals(1, $oView->UNITsortLanguagesCallback($oLang1, $oLang2));
    }

    /**
     * Language_List::ResetMultiLangDbFields() test case
     *
     * @return null
     */
    public function testResetMultiLangDbFieldsExceptionThrownWhileResetting()
    {
        oxTestModules::addFunction('oxDbMetaDataHandler', 'resetLanguage', '{ throw new Exception( "resetLanguage" ); }');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ throw new Exception( "addErrorToDisplay" ); }');

        try {
            $oView = new Language_List();
            $oView->UNITresetMultiLangDbFields(3);
        } catch (Exception $oExcp) {
            $this->assertEquals("addErrorToDisplay", $oExcp->getMessage(), "Error in Language_List::UNITresetMultiLangDbFields()");

            return;
        }
        $this->fail("Error in Language_List::UNITresetMultiLangDbFields()");
    }

    /**
     * Language_List::ResetMultiLangDbFields() test case
     *
     * @return null
     */
    public function testResetMultiLangDbFields()
    {
        oxTestModules::addFunction('oxDbMetaDataHandler', 'resetLanguage', '{}');

        $oView = new Language_List();
        $this->assertNull($oView->UNITresetMultiLangDbFields(3));
    }
}
