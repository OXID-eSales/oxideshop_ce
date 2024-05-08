<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use oxField;
use OxidEsales\EshopCommunity\Core\Registry;
use oxRegistry;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

final class ViewTest extends MultilanguageTestCase
{
    /**
     * Make a copy of Stewart+Brown Shirt Kisser Fish for testing
     */
    public const SOURCE_ARTICLE_ID = '6b6099c305f591cb39d4314e9a823fc1';

    /**
     * @var string Generated test article id.
     */
    private string|array|null $testArticleId = null;

    #[RunInSeparateProcess]
    public function testMultilanguageViewsAddLanguagesAfterAddingArticle(): void
    {
        //insert article first
        $this->insertArticle();

        //add more languages and activate latest added language in frontend
        $languageId = $this->prepare();
        Registry::getLang()->setBaseLanguage($languageId);

        //load article to have a look at e.g. it's title in the current language
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->setLanguage($languageId);
        $article->load($this->testArticleId);

        //As we have no data for this language added in table oxarticle_set1, so article title is null.
        $this->assertNull($article->oxarticles__oxtitle->value);

        //Make sure we have the expected value for the base language.
        //Effect of #6216 was that base language data was wrongly used for language id >= 8 with no way to change this.
        Registry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->setLanguage($this->originalBaseLanguageId);
        $article->load($this->testArticleId);
        $this->assertSame('TEST_MULTI_LANGUAGE', $article->oxarticles__oxtitle->value);
    }

    #[RunInSeparateProcess]
    public function testMultilanguageViewsAddArticleInDifferentDefaultLanguage(): void
    {
        //add more languages and activate latest added language in frontend
        $languageId = $this->prepare();
        oxRegistry::getLang()->setBaseLanguage($languageId);

        //insert article after switching base language
        $this->insertArticle();

        //load article to have a look at e.g. it's title in the current language
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->setLanguage($languageId);
        $article->load($this->testArticleId);

        //We stored article in switched default language
        $this->assertSame('TEST_MULTI_LANGUAGE', $article->oxarticles__oxtitle->value);

        //As article was stored in switched base language, related original base language field is empty.
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->setLanguage($this->originalBaseLanguageId);
        $article->load($this->testArticleId);
        $this->assertSame('', $article->oxarticles__oxtitle->value);
    }

    /**
     * Make a copy of article and variant for testing.
     */
    private function insertArticle(): void
    {
        $this->testArticleId = substr_replace((string) oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);

        //copy from original article
        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($this->testArticleId);
        $article->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $article->oxarticles__oxtitle = new oxField('TEST_MULTI_LANGUAGE', oxField::T_RAW);
        $article->save();
    }
}
