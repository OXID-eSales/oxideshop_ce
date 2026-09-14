<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use Symfony\Component\HttpFoundation\Response;

readonly class RateLimitResponder implements RateLimitResponderInterface
{
    public function respond(TooManyRequestsException $exception): void
    {
        (new Response(
            'Too many requests. Please try again later.',
            Response::HTTP_TOO_MANY_REQUESTS,
            [
                'Retry-After' => $exception->getRetryAfter(),
                'X-RateLimit-Limit' => $exception->getLimit(),
                'X-RateLimit-Remaining' => $exception->getRemainingTokens(),
                'X-RateLimit-Reset' => $exception->getReset(),
            ]
        ))->send();
    }
}
