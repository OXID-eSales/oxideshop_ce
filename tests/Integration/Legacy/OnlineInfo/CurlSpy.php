<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\OnlineInfo;

use OxidEsales\Eshop\Core\Curl;

final class CurlSpy extends Curl
{
    public function __construct(
        private readonly string $logPath
    ) {
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    public function execute(): bool
    {
        return true;
    }

    public function setQuery($query): void
    {
        $xmlContent = urldecode(substr($query, strlen('xmlRequest=')));

        file_put_contents($this->logPath, $xmlContent);
        parent::setQuery($query);
    }
}
