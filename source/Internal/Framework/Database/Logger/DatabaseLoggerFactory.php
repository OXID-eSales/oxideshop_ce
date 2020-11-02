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
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var QueryLogger
     */
    private $queryLogger;

    /**
     * @var NullLogger
     */
    private $nullLogger;

    /**
     * DatabaseLoggerFactory constructor.
     */
    public function __construct(
        ContextInterface $context,
        QueryLogger $queryLogger,
        NullLogger $nullLogger
    ) {
        $this->context = $context;
        $this->queryLogger = $queryLogger;
        $this->nullLogger = $nullLogger;
    }

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
