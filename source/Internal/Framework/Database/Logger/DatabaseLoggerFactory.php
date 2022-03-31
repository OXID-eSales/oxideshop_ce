<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @internal
 */
class DatabaseLoggerFactory implements DatabaseLoggerFactoryInterface
{
    public function __construct(
        private ContextInterface $context,
        private QueryLogger $queryLogger,
        private NullLogger $nullLogger
    ) {
    }

    /**
     * @return SQLLogger
     */
    public function getDatabaseLogger(): SQLLogger
    {
        if ($this->context->isAdmin() && $this->context->isEnabledAdminQueryLog()) {
            $logger = $this->queryLogger;
        } else {
            $logger = $this->nullLogger;
        }

        return $logger;
    }
}
