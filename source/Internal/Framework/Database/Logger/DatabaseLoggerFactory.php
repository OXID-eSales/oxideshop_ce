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
readonly class DatabaseLoggerFactory implements DatabaseLoggerFactoryInterface
{
    public function __construct(
        private ContextInterface $context,
        private SQLLogger $queryLogger,
        private SQLLogger $nullLogger,
        private bool $logQueriesInAdmin
    ) {
    }

    public function getDatabaseLogger(): SQLLogger
    {
        return $this->logQueriesInAdmin && $this->context->isAdmin()
            ? $this->queryLogger
            : $this->nullLogger;
    }
}
