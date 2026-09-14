<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class RateLimitExceededEvent extends Event
{
    public function __construct(
        private readonly string $ruleId,
        private readonly string $key,
        private readonly int $retryAfter,
    ) {
    }

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
