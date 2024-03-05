<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Utils;

class UtilsSpy extends Utils
{
    private array $headers = [];

    public function setHeader($header): void
    {
        $this->headers[$header] = null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
