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
    private $testArticleId = null;

    private $originalLanguageArray = null;
    private $originalBaseLanguageId = null;
    private $languageMain = null;

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
     * Assert that we get the expected multilanguage data for some language id >= 8.
     * Testing the case that no data is available for the article title in new default language.
     */
    public function testMultilanguageViewsAddLanguagesAfterAddingArticle()
    {
        //insert article first
        $this->insertArticle();

        //add more languages and activate latest added language in frontend
        $languageId = $this->prepare();
        oxRegistry::getLang()->setBaseLanguage($languageId);

        //load article to have a look at e.g. it's title in the current language
        $article = oxnew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);

        //As we have no data for this language added in table oxarticle_set1, so article title is null.
        $this->assertNull($article->oxarticles__oxtitle->value);

        //Make sure we have the expected value for the base language.
        //Effect of #6216 was that base language data was wrongly used for language id >= 8 with no way to change this.
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $article = oxnew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);
        $this->assertSame('TEST_MULTI_LANGUAGE', $article->oxarticles__oxtitle->value);
    }

    /**
     * Assert that we get the expected multilanguage data for some language id >= 8.
     * Testing the case that we add the article when base language has some language id >= 8.
     */
    public function testMultilanguageViewsAddArticleInDifferentDefaultLanguage()
    {
        //add more languages and activate latest added language in frontend
        $languageId = $this->prepare();
        oxRegistry::getLang()->setBaseLanguage($languageId);

        //insert article after switching base language
        $this->insertArticle();

        //load article to have a look at e.g. it's title in the current language
        $article = oxnew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);

        //We stored article in switched default language
        $this->assertSame('TEST_MULTI_LANGUAGE', $article->oxarticles__oxtitle->value);

        //As article was stored in switched base language, related original base language field is empty.
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $article = oxnew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);
        $this->assertSame('', $article->oxarticles__oxtitle->value);
    }

    /**
     * Make a copy of article and variant for testing.
     */
    private function insertArticle()
    {
        $this->testArticleId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );

        //copy from original article
        $article = oxNew('oxarticle');
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($this->testArticleId);
        $article->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $article->oxarticles__oxtitle  = new oxField('TEST_MULTI_LANGUAGE', oxField::T_RAW);
        $article->save();
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
     * @param $languageId
     *
     * @return integer
     */
    private function insertLanguage($languageId)
    {
        $languages = $this->getLanguageMain()->_getLanguages();
        $baseId = $this->getLanguageMain()->_getAvailableLangBaseId();
        $sort = $baseId*100;

        $languages['params'][$languageId] = array( 'baseId' => $baseId,
                                                   'active' => 1,
                                                   'sort'   => $sort );

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
    private function storeLanguageConfiguration($languages)
    {
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $languages['params']);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $languages['lang']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $languages['urls']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $languages['sslUrls']);
    }

    /**
     * Test helder to trigger view update.
     */
    private function updateViews()
    {
        $meta = oxNew('oxDbMetaDataHandler');
        $meta->updateViews();
    }

    /**
     * Getter for Language_Main_Helper proxy class.
     *
     * @return object
     */
    private function getLanguageMain()
    {
        if (is_null($this->languageMain)) {
            $this->languageMain = $this->getProxyClass('Language_Main_Helper');
            $this->languageMain->render();
        }
        return $this->languageMain;
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
