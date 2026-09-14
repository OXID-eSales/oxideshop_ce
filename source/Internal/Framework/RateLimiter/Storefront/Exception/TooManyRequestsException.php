<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception;

class TooManyRequestsException extends \RuntimeException
{
    public function __construct(
        private readonly int $retryAfter,
        private readonly int $limit,
        private readonly int $remainingTokens,
        private readonly int $reset,
    ) {
        parent::__construct('Too many requests.');
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getRemainingTokens(): int
    {
        return $this->remainingTokens;
    }

    public function getReset(): int
    {
        return $this->reset;
    }
}
