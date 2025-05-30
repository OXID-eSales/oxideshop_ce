<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @deprecated will be removed in next major version
 *
 * @internal
 */
class NullLogger implements SQLLogger
{
    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
    }

    public function stopQuery(): void
    {
    }
}
