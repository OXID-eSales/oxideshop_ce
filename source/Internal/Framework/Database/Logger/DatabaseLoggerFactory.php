<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

/**
 * @internal
 */
class DatabaseLoggerFactory implements DatabaseLoggerFactoryInterface
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly SQLLogger $queryLogger,
        private readonly SQLLogger $nullLogger
    ) {
    }

    public function getDatabaseLogger(): SQLLogger
    {
        return $this->context->isAdmin() && $this->context->isEnabledAdminQueryLog()
            ? $this->queryLogger
            : $this->nullLogger;
    }
}
