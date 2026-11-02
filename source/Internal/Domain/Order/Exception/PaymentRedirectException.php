<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Order\Exception;

use Exception;

class PaymentRedirectException extends Exception
{
    public function __construct(private readonly string $url)
    {
        parent::__construct();
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
