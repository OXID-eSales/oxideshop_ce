<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Search\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\BeforeProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use PHPUnit\Framework\TestCase;

final class BeforeProductSearchEventTest extends TestCase
{
    public function testGetSearchCriteria(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $event = new BeforeProductSearchEvent($searchCriteria);

        $this->assertSame($searchCriteria, $event->getSearchCriteria());
    }

    public function testSetSearchCriteriaReplacesCriteria(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $newSearchCriteria = new ProductSearchCriteria(new Pagination(5, 0), new SearchTerm('other'));
        $event = new BeforeProductSearchEvent($searchCriteria);

        $event->setSearchCriteria($newSearchCriteria);

        $this->assertSame($newSearchCriteria, $event->getSearchCriteria());
    }

    public function testGetContextReturnsEmptyByDefault(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $event = new BeforeProductSearchEvent($searchCriteria);

        $this->assertSame([], $event->getContext());
    }

    public function testSetContextReplacesContext(): void
    {
        $searchCriteria = new ProductSearchCriteria(new Pagination(10, 0), new SearchTerm('test'));
        $event = new BeforeProductSearchEvent($searchCriteria);

        $event->setContext(['shop' => 1]);

        $this->assertSame(['shop' => 1], $event->getContext());
    }
}
