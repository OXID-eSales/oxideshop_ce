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

    public function setParameters($parameters): void
    {
        file_put_contents($this->logPath, $parameters['xmlRequest']);
        parent::setParameters($parameters);
    }
}
