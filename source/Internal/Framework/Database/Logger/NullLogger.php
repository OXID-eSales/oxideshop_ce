<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @internal
 */
class NullLogger implements SQLLogger
{
    public function startQuery($query, ?array $params = null, ?array $types = null): void
    {
    }

    public function stopQuery(): void
    {
    }
}
