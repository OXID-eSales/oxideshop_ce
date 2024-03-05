<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('triggers-implicit-transaction-commit')]
final class ViewTest extends TestCase
{
    use DatabaseTrait;
    use MultilanguageTrait;

    private string $productId;
    private int $originalBaseLanguageId;

    public function setUp(): void
    {
        parent::setUp();

        $this->originalBaseLanguageId = Registry::getLang()->getBaseLanguage();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->setupShopDatabase();
    }

    public function testMultilanguageViewsAddLanguagesAfterAddingProduct(): void
    {
        $this->createProduct();
        $languageId = $this->createLanguages();
        Registry::getLang()->setBaseLanguage($languageId);

        $product = oxNew(Article::class);
        $product->setLanguage($languageId);
        $product->load($this->productId);

        //As we have no data for this language added in table oxarticle_set1, so article title is null.
        $this->assertNull($product->getFieldData('oxtitle'));

        //Make sure we have the expected value for the base language.
        //Effect of #6216 was that base language data was wrongly used for language id >= 8 with no way to change this.
        Registry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $product = oxNew(Article::class);
        $product->setLanguage($this->originalBaseLanguageId);
        $product->load($this->productId);
        $this->assertEquals('TEST_MULTI_LANGUAGE', $product->getFieldData('oxtitle'));
    }

    public function testMultilanguageViewsAddProductInDifferentDefaultLanguage(): void
    {
        $languageId = $this->createLanguages();
        Registry::getLang()->setBaseLanguage($languageId);
        $this->createProduct();

        $product = oxNew(Article::class);
        $product->setLanguage($languageId);
        $product->load($this->productId);

        //We stored article in switched default language
        $this->assertEquals('TEST_MULTI_LANGUAGE', $product->getFieldData('oxtitle'));

        //As article was stored in switched base language, related original base language field is empty.
        Registry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $product = oxNew(Article::class);
        $product->setLanguage($this->originalBaseLanguageId);
        $product->load($this->productId);
        $this->assertEquals('', $product->getFieldData('oxtitle'));
    }

    private function createProduct(): void
    {
        $this->productId = substr_replace((string)Registry::getUtilsObject()->generateUId(), '_', 0, 1);

        $product = oxNew(Article::class);
        $product->setId($this->productId);
        $product->oxarticles__oxartnum = new Field('123');
        $product->oxarticles__oxtitle = new Field('TEST_MULTI_LANGUAGE');
        $product->save();
    }
}
