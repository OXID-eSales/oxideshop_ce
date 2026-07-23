<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Exception;

class RedirectException extends \Error implements RequestTerminationInterface
{
    public function __construct(
        private readonly string $url,
        private readonly int $statusCode = 302
    ) {
        parent::__construct();
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatusCode(): int
    {
        return in_array($this->statusCode, [301, 302, 303, 307, 308], true) ? $this->statusCode : 302;
    }
}
