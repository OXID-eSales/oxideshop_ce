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

class Integration_Multilanguage_ViewTest extends OxidTestCase
{
    /**
     * Make a copy of Stewart+Brown Shirt Kisser Fish for testing
     */
    const SOURCE_ARTICLE_ID = '6b6099c305f591cb39d4314e9a823fc1';

    /**
     * Generated test article id.
     * @var string
     */
    private $sTestArticleId = null;

    private $aOriginalLanguageArray = null;
    private $iOriginalBaseLanguageId = null;
    private $iLanguageMain = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->aOriginalLanguageArray = $this->getLanguageMain()->_getLanguages();
        $this->iOriginalBaseLanguageId = oxRegistry::getLang()->getBaseLanguage();
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        oxRegistry::getLang()->setBaseLanguage($this->iOriginalBaseLanguageId);
        $this->storeLanguageConfiguration($this->aOriginalLanguageArray);
        $this->updateViews();

        parent::tearDown();
    }

    /**
     * Assert that we get the expected multilanguage data for some language id >= 8.
     * Testing the case that no data is available for the article title in new default language.
     */
    public function testMultilanguageViewsAddLanguagesAfterAddingArticle()
    {
        //insert article first
        $this->insertArticle();

        //add more languages and activate latest added language in frontend
        $iLanguageId = $this->prepare();
        oxRegistry::getLang()->setBaseLanguage($iLanguageId);

        //load article to have a look at e.g. it's title in the current language
        $oArticle = oxnew('oxArticle');
        $oArticle->disableLazyLoading();
        $oArticle->load($this->sTestArticleId);

        //As we have no data for this language added in table oxarticle_set1, so article title is null.
        $this->assertNull($oArticle->oxarticles__oxtitle->value);

        //Make sure we have the expected value for the base language.
        //Effect of #6216 was that base language data was wrongly used for language id >= 8 with no way to change this.
        oxRegistry::getLang()->setBaseLanguage($this->iOriginalBaseLanguageId);
        $oArticle = oxnew('oxArticle');
        $oArticle->disableLazyLoading();
        $oArticle->load($this->sTestArticleId);
        $this->assertSame('TEST_MULTI_LANGUAGE', $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * Assert that we get the expected multilanguage data for some language id >= 8.
     * Testing the case that we add the article when base language has some language id >= 8.
     */
    public function testMultilanguageViewsAddArticleInDifferentDefaultLanguage()
    {
        //add more languages and activate latest added language in frontend
        $iLanguageId = $this->prepare();
        oxRegistry::getLang()->setBaseLanguage($iLanguageId);

        //insert article after switching base language
        $this->insertArticle();

        //load article to have a look at e.g. it's title in the current language
        $oArticle = oxnew('oxArticle');
        $oArticle->disableLazyLoading();
        $oArticle->load($this->sTestArticleId);

        //We stored article in switched default language
        $this->assertSame('TEST_MULTI_LANGUAGE', $oArticle->oxarticles__oxtitle->value);

        //As article was stored in switched base language, related original base language field is empty.
        oxRegistry::getLang()->setBaseLanguage($this->iOriginalBaseLanguageId);
        $oArticle = oxnew('oxArticle');
        $oArticle->disableLazyLoading();
        $oArticle->load($this->sTestArticleId);
        $this->assertSame('', $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * Make a copy of article and variant for testing.
     */
    private function insertArticle()
    {
         $this->sTestArticleId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );

        //copy from original article
        $oArticle = oxNew('oxarticle');
        $oArticle->disableLazyLoading();
        $oArticle->load(self::SOURCE_ARTICLE_ID);
        $oArticle->setId($this->sTestArticleId);
        $oArticle->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $oArticle->oxarticles__oxtitle  = new oxField('TEST_MULTI_LANGUAGE', oxField::T_RAW);
        $oArticle->save();
    }

    /**
     * Test helper for test preparation.
     * Add given count of new languages.
     *
     * @param $count
     *
     * @return int
     */
    private function prepare($count = 9)
    {
        for ($i=0;$i<$count;$i++) {
            $sLanguageName = chr(97+$i) . chr(97+$i);
            $iLanguageId = $this->insertLanguage($sLanguageName);
        }
        //we need a fresh instance of language object in registry,
        //otherwise stale data is used for language abbreviations.
        oxRegistry::set('oxLang', null);

        $this->updateViews();

        return $iLanguageId;
    }

    /**
     * Test helper to insert a new language with given id.
     *
     * @param $iLanguageId
     *
     * @return integer
     */
    private function insertLanguage($iLanguageId)
    {
        $aLanguages = $this->getLanguageMain()->_getLanguages();
        $iBaseId = $this->getLanguageMain()->_getAvailableLangBaseId();
        $iSort = $iBaseId*100;

        $aLanguages['params'][$iLanguageId] = array('baseId' => $iBaseId,
                                                    'active' => 1,
                                                    'sort'   => $iSort);

        $aLanguages['lang'][$iLanguageId] = $iLanguageId;
        $aLanguages['urls'][$iBaseId]     = '';
        $aLanguages['sslUrls'][$iBaseId]  = '';
        $this->getLanguageMain()->setLanguageData($aLanguages);

        $this->storeLanguageConfiguration($aLanguages);

        if (!$this->getLanguageMain()->_checkMultilangFieldsExistsInDb($iLanguageId)) {
            $this->getLanguageMain()->_addNewMultilangFieldsToDb();
        }

        return $iBaseId;
    }

    /**
     * Test helper for saving language configuration.
     *
     * @param $aLanguages
     */
    private function storeLanguageConfiguration($aLanguages)
    {
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $aLanguages['params']);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $aLanguages['lang']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $aLanguages['urls']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $aLanguages['sslUrls']);
    }

    /**
     * Test helder to trigger view update.
     */
    private function updateViews()
    {
        $oMeta = oxNew('oxDbMetaDataHandler');
        $oMeta->updateViews();
    }

    /**
     * Getter for Language_Main_Helper proxy class.
     *
     * @return object
     */
    private function getLanguageMain()
    {
        if (is_null($this->iLanguageMain)) {
            $this->iLanguageMain = $this->getProxyClass('Language_Main_Helper');
            $this->iLanguageMain->render();
        }
        return $this->iLanguageMain;
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
