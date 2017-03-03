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
namespace OxidEsales\EshopCommunity\Tests\Integration\Multilanguage;

use oxField;
use oxRegistry;

/**
 * Class ViewTest
 *
 * @group slow-tests
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Multilanguage
 */
class ViewTest extends MultilanguageTestCase
{
    /**
     * Make a copy of Stewart+Brown Shirt Kisser Fish for testing
     */
    const SOURCE_ARTICLE_ID = '6b6099c305f591cb39d4314e9a823fc1';

    /** @var string Generated test article id. */
    private $testArticleId = null;

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
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);

        //As we have no data for this language added in table oxarticle_set1, so article title is null.
        $this->assertNull($article->oxarticles__oxtitle->value);

        //Make sure we have the expected value for the base language.
        //Effect of #6216 was that base language data was wrongly used for language id >= 8 with no way to change this.
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $article = oxNew('oxArticle');
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
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);

        //We stored article in switched default language
        $this->assertSame('TEST_MULTI_LANGUAGE', $article->oxarticles__oxtitle->value);

        //As article was stored in switched base language, related original base language field is empty.
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->load($this->testArticleId);
        $this->assertSame('', $article->oxarticles__oxtitle->value);
    }

    /**
     * Make a copy of article and variant for testing.
     */
    private function insertArticle()
    {
        $this->testArticleId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);

        //copy from original article
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($this->testArticleId);
        $article->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $article->oxarticles__oxtitle  = new oxField('TEST_MULTI_LANGUAGE', oxField::T_RAW);
        $article->save();
    }
}
