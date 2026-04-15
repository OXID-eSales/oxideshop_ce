<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Exception;

class RedirectException extends \RuntimeException
{
    public function __construct(
        private readonly string $url,
        private readonly int $statusCode = 302
    ) {
        parent::__construct(sprintf('Redirect to %s', $url));
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
