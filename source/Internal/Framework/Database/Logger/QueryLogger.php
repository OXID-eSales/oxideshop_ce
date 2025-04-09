<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Monolog\Logger;

class QueryLogger extends Logger
{
    public function __construct(
        string $loggerName,
        private readonly QueryLogFilterInterface $queryLogFilter,
        private readonly QueryLogContextExtenderInterface $queryLogContextExtender,
    ) {
        parent::__construct($loggerName);
    }

    public function addRecord($level, $message, array $context = array()): bool
    {
        return isset($context['sql']) &&
            $this->queryLogFilter->shouldLogQuery($context['sql'])
            && parent::addRecord(
                $level,
                $message,
                $this->queryLogContextExtender->extend($context)
            );
    }
}
