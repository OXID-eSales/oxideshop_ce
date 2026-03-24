<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeProductSearchEvent extends Event
{
    public function __construct(
        private ProductSearchCriteria $searchCriteria,
        private array $context = [],
    ) {
    }

    public function getSearchCriteria(): ProductSearchCriteria
    {
        return $this->searchCriteria;
    }

    public function setSearchCriteria(ProductSearchCriteria $searchCriteria): void
    {
        $this->searchCriteria = $searchCriteria;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}
