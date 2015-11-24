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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

abstract class MultilanguageTestCase extends OxidTestCase
{
    protected $originalLanguageArray = null;
    protected $originalBaseLanguageId = null;
    protected $languageMain = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->originalLanguageArray = $this->getLanguageMain()->_getLanguages();
        $this->originalBaseLanguageId = oxRegistry::getLang()->getBaseLanguage();
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $this->storeLanguageConfiguration($this->originalLanguageArray);
        $this->updateViews();

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
    protected function prepare($count = 9)
    {
        for ($i=0;$i<$count;$i++) {
            $languageName = chr(97+$i) . chr(97+$i);
            $languageId = $this->insertLanguage($languageName);
        }
        //we need a fresh instance of language object in registry,
        //otherwise stale data is used for language abbreviations.
        oxRegistry::set('oxLang', null);

        $this->updateViews();

        return $languageId;
    }

    /**
     * Test helper to insert a new language with given id.
     *
     * @param $iLanguageId
     *
     * @return integer
     */
    protected function insertLanguage($languageId)
    {
        $languages = $this->getLanguageMain()->_getLanguages();
        $baseId = $this->getLanguageMain()->_getAvailableLangBaseId();
        $sort = $baseId*100;

        $languages['params'][$languageId] = array('baseId' => $baseId,
                                                  'active' => 1,
                                                  'sort'   => $sort);

        $languages['lang'][$languageId] = $languageId;
        $languages['urls'][$baseId]     = '';
        $languages['sslUrls'][$baseId]  = '';
        $this->getLanguageMain()->setLanguageData($languages);

        $this->storeLanguageConfiguration($languages);

        if (!$this->getLanguageMain()->_checkMultilangFieldsExistsInDb($languageId)) {
            $this->getLanguageMain()->_addNewMultilangFieldsToDb();
        }

        return $baseId;
    }

    /**
     * Test helper for saving language configuration.
     *
     * @param $languages
     */
    protected function storeLanguageConfiguration($languages)
    {
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $languages['params']);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $languages['lang']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $languages['urls']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $languages['sslUrls']);
    }

    /**
     * Test helder to trigger view update.
     */
    protected function updateViews()
    {
        $oMeta = oxNew('oxDbMetaDataHandler');
        $oMeta->updateViews();
    }

    /**
     * Getter for Language_Main_Helper proxy class.
     *
     * @return object
     */
    protected function getLanguageMain()
    {
        if (is_null($this->languageMain)) {
            $this->languageMain = $this->getProxyClass('Language_Main_Helper');
            $this->languageMain->render();
        }
        return $this->languageMain;
    }

    /**
     * Create additional multilanguage table.
     *
     * @param string $name
     */
    protected function createTable($name = 'addtest')
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . $name . "` (" .
               "`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Item id'," .
               "`TITLE` varchar(128) NOT NULL DEFAULT '' COMMENT 'Title (multilanguage)'," .
               "`TITLE_1` varchar(128) NOT NULL DEFAULT ''," .
               "`TITLE_2` varchar(128) NOT NULL DEFAULT ''," .
               "`TITLE_3` varchar(128) NOT NULL DEFAULT ''," .
               "`TITLE_4` varchar(128) NOT NULL DEFAULT ''," .
               "`TITLE_5` varchar(128) NOT NULL DEFAULT ''," .
               "`TITLE_6` varchar(128) NOT NULL DEFAULT ''," .
               "`TITLE_7` varchar(128) NOT NULL DEFAULT ''," .
               "PRIMARY KEY (`OXID`)" .
               ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='for testing'";

        oxDb::getDb()->query($sql);
        oxDb::getInstance()->getTableDescription($name); //throws exception if table does not exist
        $this->additionalTables[] = $name;
    }

    /**
     * Remove additional multilanguage tables and related.
     *
     * @return null
     */
    protected function removeAdditionalTables($name)
    {
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME LIKE '" . $name . "%'";
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getArray($sql);
        foreach ($result as $sub) {
            oxDb::getDb()->query("DROP TABLE IF EXISTS `" . $sub['TABLE_NAME'] . "`");
        }
    }
}

class Language_Main_Helper extends Language_Main
{
    public function getLanguageData()
    {
        return $this->_aLangData;
    }

    public function setLanguageData($languageData)
    {
        $this->_aLangData = $languageData;
    }
}
