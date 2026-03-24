<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Search;

use OxidEsales\EshopCommunity\Internal\Framework\Search\FilterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Sorting;

readonly class ProductSearchCriteria
{
    /** @var list<FilterInterface> */
    private array $filters;

    /** @var list<Sorting> */
    private array $sorting;

    /**
     * @param list<FilterInterface> $filters
     * @param list<Sorting> $sorting
     */
    public function __construct(
        private Pagination $pagination,
        private SearchTerm $term,
        array $filters = [],
        array $sorting = [],
    ) {
        $this->filters = array_values($filters);
        $this->sorting = array_values($sorting);
    }

    public function getTerm(): SearchTerm
    {
        return $this->term;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /** @return list<FilterInterface> */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /** @return list<Sorting> */
    public function getSorting(): array
    {
        return $this->sorting;
    }
}
