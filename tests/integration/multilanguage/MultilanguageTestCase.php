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

abstract class MultilanguageTestCase extends OxidTestCase
{
    protected $_aOriginalLanguageArray = null;
    protected $_iOriginalBaseLanguageId = null;
    protected $_iLanguageMain = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_aOriginalLanguageArray = $this->_getLanguageMain()->_getLanguages();
        $this->_iOriginalBaseLanguageId = oxRegistry::getLang()->getBaseLanguage();
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        oxRegistry::getLang()->setBaseLanguage($this->_iOriginalBaseLanguageId);
        $this->_storeLanguageConfiguration($this->_aOriginalLanguageArray);
        $this->_updateViews();

        parent::tearDown();
    }

    /**
     * Test helper for test preparation.
     * Add given count of new languages.
     *
     * @param $count
     *
     * @return int
     */
    protected function _prepare($count = 9)
    {
        for ($i=0;$i<$count;$i++) {
            $sLanguageName = chr(97+$i) . chr(97+$i);
            $iLanguageId = $this->_insertLanguage($sLanguageName);
        }
        //we need a fresh instance of language object in registry,
        //otherwise stale data is used for language abbreviations.
        oxRegistry::set('oxLang', null);

        $this->_updateViews();

        return $iLanguageId;
    }

    /**
     * Test helper to insert a new language with given id.
     *
     * @param $iLanguageId
     *
     * @return integer
     */
    protected function _insertLanguage($iLanguageId)
    {
        $aLanguages = $this->_getLanguageMain()->_getLanguages();
        $iBaseId = $this->_getLanguageMain()->_getAvailableLangBaseId();
        $iSort = $iBaseId*100;

        $aLanguages['params'][$iLanguageId] = array('baseId' => $iBaseId,
                                                    'active' => 1,
                                                    'sort'   => $iSort);

        $aLanguages['lang'][$iLanguageId] = $iLanguageId;
        $aLanguages['urls'][$iBaseId]     = '';
        $aLanguages['sslUrls'][$iBaseId]  = '';
        $this->_getLanguageMain()->setLanguageData($aLanguages);

        $this->_storeLanguageConfiguration($aLanguages);

        if (!$this->_getLanguageMain()->_checkMultilangFieldsExistsInDb($iLanguageId)) {
            $this->_getLanguageMain()->_addNewMultilangFieldsToDb();
        }

        return $iBaseId;
    }

    /**
     * Test helper for saving language configuration.
     *
     * @param $aLanguages
     */
    protected function _storeLanguageConfiguration($aLanguages)
    {
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $aLanguages['params']);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $aLanguages['lang']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $aLanguages['urls']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $aLanguages['sslUrls']);
    }

    /**
     * Test helder to trigger view update.
     */
    protected function _updateViews()
    {
        $oMeta = oxNew('oxDbMetaDataHandler');
        $oMeta->updateViews();
    }

    /**
     * Getter for Language_Main_Helper proxy class.
     *
     * @return object
     */
    protected function _getLanguageMain()
    {
        if (is_null($this->_iLanguageMain)) {
            $this->_iLanguageMain = $this->getProxyClass('Language_Main_Helper');
            $this->_iLanguageMain->render();
        }
        return $this->_iLanguageMain;
    }

}

class Language_Main_Helper extends Language_Main
{
    public function getLanguageData()
    {
        return $this->_aLangData;
    }

    public function setLanguageData($LanguageData)
    {
        $this->_aLangData = $LanguageData;
    }
}
