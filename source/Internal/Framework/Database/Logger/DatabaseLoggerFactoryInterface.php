<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @deprecated will be removed in next major version, will be replaced by new dbal middleware
 *
 * @internal
 */
interface DatabaseLoggerFactoryInterface
{
    public function getDatabaseLogger(): SQLLogger;
}
