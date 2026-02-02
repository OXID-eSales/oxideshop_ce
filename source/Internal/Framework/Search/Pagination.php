<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Search;

use InvalidArgumentException;

readonly class Pagination
{
    private int $limit;
    private int $offset;

    public function __construct(int $limit, int $offset)
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Pagination limit must be >= 1');
        }
        if ($offset < 0) {
            throw new InvalidArgumentException('Pagination offset must be >= 0');
        }

        $this->limit = $limit;
        $this->offset = $offset;
    }

    public static function fromPage(int $page, int $limit): self
    {
        if ($page < 1 || $limit < 1) {
            throw new InvalidArgumentException('Pagination page and limit must be >= 1');
        }

        return new self($limit, ($page - 1) * $limit);
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
