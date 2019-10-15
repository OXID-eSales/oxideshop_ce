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
interface DatabaseLoggerFactoryInterface
{
    /**
     * @return SQLLogger
     */
    public function getDatabaseLogger(): SQLLogger;
}
