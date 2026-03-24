<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Search\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\AfterProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use PHPUnit\Framework\TestCase;

final class AfterProductSearchEventTest extends TestCase
{
    public function testGetSearchResult(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $result = new ProductSearchResult([], 0);
        $event = new AfterProductSearchEvent($searchCriteria, [], $result);

        $this->assertSame($result, $event->getSearchResult());
    }

    public function testGetSearchCriteria(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $result = new ProductSearchResult([], 0);
        $event = new AfterProductSearchEvent($searchCriteria, [], $result);

        $this->assertSame($searchCriteria, $event->getSearchCriteria());
    }

    public function testGetContext(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $result = new ProductSearchResult([], 0);
        $context = ['locale' => 'de_DE'];
        $event = new AfterProductSearchEvent($searchCriteria, $context, $result);

        $this->assertSame($context, $event->getContext());
    }

    public function testGetSearchResultWithContext(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $result = new ProductSearchResult([], 0);
        $event = new AfterProductSearchEvent($searchCriteria, [], $result);

        $this->assertSame($result, $event->getSearchResult());
    }

    public function testSetSearchResultReplacesResult(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $result = new ProductSearchResult([], 0);
        $newResult = new ProductSearchResult([], 5);
        $event = new AfterProductSearchEvent($searchCriteria, [], $result);

        $event->setSearchResult($newResult);

        $this->assertSame($newResult, $event->getSearchResult());
    }
}
