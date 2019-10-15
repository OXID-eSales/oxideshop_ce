<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Doctrine\DBAL\Logging\SQLLogger;

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
     *
     * @param ContextInterface $context
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
