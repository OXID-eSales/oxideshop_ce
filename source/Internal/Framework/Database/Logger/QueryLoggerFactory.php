<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Monolog\Handler\StreamHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory\LoggerFactoryInterface;
use Psr\Log\LoggerInterface;

readonly class QueryLoggerFactory implements LoggerFactoryInterface
{
    public function __construct(
        private QueryLogFilterInterface $queryLogFilter,
        private QueryLogContextExtenderInterface $queryLogContextExtender,
        private StreamHandler $streamHandler,
        private string $loggerName,
    ) {
    }

    public function create(): LoggerInterface
    {
        return (new QueryLogger(
            $this->loggerName,
            $this->queryLogFilter,
            $this->queryLogContextExtender,
        ))
            ->pushHandler($this->streamHandler);
    }
}
