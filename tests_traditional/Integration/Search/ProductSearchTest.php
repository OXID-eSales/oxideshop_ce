<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Checkout;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\Search;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ProductSearchTest extends UnitTestCase
{
    private const CONFIG_KEY_SEARCH_COLUMNS = 'aSearchCols';
    private const SEARCH_STRING_WITH_HITS = 'abc-123';
    private const SEARCH_STRING_WITHOUT_HITS = 'XYZ-987';
    private const ID_PRODUCT_WITH_TITLE_HIT = '1';
    private const ID_PRODUCT_WITH_SEARCH_KEYS_HIT = '2';
    private const ID_PRODUCT_WITHOUT_HITS = '3';

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareProducts();
    }

    public function testGetSearchArticlesWithoutHitsWillReturnEmptyList(): void
    {
        $searchColumns = ['oxtitle'];
        Registry::getConfig()->setConfigParam(self::CONFIG_KEY_SEARCH_COLUMNS, $searchColumns);

        $productList = oxNew(Search::class)->getSearchArticles(self::SEARCH_STRING_WITHOUT_HITS);

        $this->assertCount(0, $productList);
    }

    public function testGetSearchArticlesWithHitInOneColumnWillReturnExpected(): void
    {
        $searchColumns = ['oxtitle'];
        Registry::getConfig()->setConfigParam(self::CONFIG_KEY_SEARCH_COLUMNS, $searchColumns);

        $productList = oxNew(Search::class)->getSearchArticles(self::SEARCH_STRING_WITH_HITS);

        $this->assertCount(1, $productList);
        $productListArray = $productList->getArray();
        $this->assertSame(self::ID_PRODUCT_WITH_TITLE_HIT, reset($productListArray)->getProductId());
    }

    public function testGetSearchArticlesWithHitsInTwoColumnsWillReturnExpectedListSize(): void
    {
        $searchColumns = ['oxtitle', 'oxsearchkeys'];
        Registry::getConfig()->setConfigParam(self::CONFIG_KEY_SEARCH_COLUMNS, $searchColumns);

        $productList = oxNew(Search::class)->getSearchArticles(self::SEARCH_STRING_WITH_HITS);

        $this->assertCount(2, $productList);
    }

    private function prepareProducts(): void
    {
        $productWithTitleColumnHit = oxNew(Article::class);
        $productWithTitleColumnHit->setId(self::ID_PRODUCT_WITH_TITLE_HIT);
        $productWithTitleColumnHit->oxarticles__oxtitle = new Field($this->getStringWithHit());
        $productWithTitleColumnHit->oxarticles__oxsearchkeys = new Field($this->getStringWithoutHits());
        $productWithTitleColumnHit->save();

        $productWithSearchKeysColumnHit = oxNew(Article::class);
        $productWithSearchKeysColumnHit->setId(self::ID_PRODUCT_WITH_SEARCH_KEYS_HIT);
        $productWithSearchKeysColumnHit->oxarticles__oxtitle = new Field($this->getStringWithoutHits());
        $productWithSearchKeysColumnHit->oxarticles__oxsearchkeys = new Field($this->getStringWithHit());
        $productWithSearchKeysColumnHit->save();

        $productWithoutHits = oxNew(Article::class);
        $productWithoutHits->setId(self::ID_PRODUCT_WITHOUT_HITS);
        $productWithoutHits->oxarticles__oxtitle = new Field($this->getStringWithoutHits());
        $productWithoutHits->oxarticles__oxsearchkeys = new Field($this->getStringWithoutHits());
        $productWithoutHits->save();
    }

    private function getStringWithHit(): string
    {
        return self::SEARCH_STRING_WITH_HITS;
    }

    private function getStringWithoutHits(): string
    {
        return uniqid('', true);
    }
}
