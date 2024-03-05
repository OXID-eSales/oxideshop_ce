<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Attribute;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ArticleListTest extends IntegrationTestCase
{
    private int $language = 0;
    private ArticleList $productList;

    private string $productId = 'ksdjflkdsjflsdkfkfj';
    private string $categoryId = 'ioweruweoiruweiovxj';
    private string $attributeId = 'ioweruweoiruweiovxi';


    public function setUp(): void
    {
        parent::setUp();
        Registry::getLang()->setBaseLanguage($this->language);

        $this->productList = oxNew(ArticleList::class);
    }

    public function testLoadCategoryArticlesWithSameValue(): void
    {
        $value = 'value';
        $this->prepareCategories($value);
        $sessionFilter = [$this->categoryId => [$this->language => [$this->attributeId => $value]]];

        $productCount = $this->productList->loadCategoryArticles($this->categoryId, $sessionFilter);

        $this->assertEquals(1, $productCount);
    }

    public function testLoadCategoryArticlesWithZero(): void
    {
        $value = 0;
        $this->prepareCategories($value);
        $sessionFilter = [$this->categoryId => [$this->language => [$this->attributeId => $value]]];

        $productCount = $this->productList->loadCategoryArticles($this->categoryId, $sessionFilter);

        $this->assertEquals(1, $productCount);
    }

    public function testLoadCategoryArticlesWithNoResults(): void
    {
        $this->prepareCategories('value');
        $sessionFilter = [$this->categoryId => [$this->language => [$this->attributeId => 'something-different']]];

        $productCount = $this->productList->loadCategoryArticles($this->categoryId, $sessionFilter);

        $this->assertEquals(0, $productCount);
    }

    private function prepareCategories(string|int $value): void
    {
        $this->createCategory();
        $this->createProduct();
        $this->createAttribute();
        $this->linkProductToCategory();
        $this->linkAttributeToCategory();
        $this->linkProductToAttribute($value);
    }

    private function createCategory(): void
    {
        $category = oxNew(Category::class);
        $category->setId($this->categoryId);
        $category->oxcategories__oxactive = oxNew(Field::class, 1, Field::T_RAW);
        $category->oxcategories__oxparentid = oxNew(Field::class, 'oxrootid');
        $category->save();
    }

    private function createProduct(): void
    {
        $product = oxNew(Article::class);
        $product->setId($this->productId);
        $product->oxarticles__oxactive = oxNew(Field::class, true);
        $product->oxarticles__oxunitquantity = oxNew(Field::class, 50);
        $product->oxarticles__oxstock = oxNew(Field::class, 50);
        $product->save();
    }

    private function createAttribute(): void
    {
        $attribute = oxNew(Attribute::class);
        $attribute->setId($this->attributeId);
        $attribute->save();
    }

    private function linkProductToCategory(): void
    {
        $link = oxNew(BaseModel::class);
        $link->init("oxobject2category");
        $link->oxobject2category__oxobjectid = oxNew(Field::class, $this->productId);
        $link->oxobject2category__oxcatnid = oxNew(Field::class, $this->categoryId);
        $link->save();
    }

    private function linkAttributeToCategory(): void
    {
        $link = oxNew(BaseModel::class);
        $link->init("oxcategory2attribute");
        $link->oxcategory2attribute__oxobjectid = oxNew(Field::class, $this->categoryId);
        $link->oxcategory2attribute__oxattrid = oxNew(Field::class, $this->attributeId);
        $link->save();
    }

    private function linkProductToAttribute(string|int $value): void
    {
        $link = oxNew(BaseModel::class);
        $link->init("oxobject2attribute");
        $link->oxobject2attribute__oxobjectid = oxNew(Field::class, $this->productId);
        $link->oxobject2attribute__oxattrid = oxNew(Field::class, $this->attributeId);
        $link->oxobject2attribute__oxvalue = oxNew(Field::class, $value);
        $link->save();
    }
}
