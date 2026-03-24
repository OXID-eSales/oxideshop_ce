<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use Symfony\Contracts\EventDispatcher\Event;

class AfterProductSearchEvent extends Event
{
    public function __construct(
        private readonly ProductSearchCriteria $searchCriteria,
        private readonly array $context,
        private ProductSearchResult $searchResult,
    ) {
    }

    public function getSearchCriteria(): ProductSearchCriteria
    {
        return $this->searchCriteria;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getSearchResult(): ProductSearchResult
    {
        return $this->searchResult;
    }

    public function setSearchResult(ProductSearchResult $searchResult): void
    {
        $this->searchResult = $searchResult;
    }
}
