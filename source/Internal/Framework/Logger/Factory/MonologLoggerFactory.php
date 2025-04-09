<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

readonly class MonologLoggerFactory implements LoggerFactoryInterface
{
    public function __construct(
        private string $loggerName,
        private string $logFilePath,
        private string $logLevel,
    ) {
    }

    public function create(): LoggerInterface
    {
        $handler = $this->getHandler();

        $logger = new Logger($this->loggerName);
        $logger->pushHandler($handler);

        return $logger;
    }

    private function getHandler(): StreamHandler
    {
        $handler = new StreamHandler(
            $this->logFilePath,
            $this->logLevel
        );

        $formatter = new LineFormatter();
        $formatter->includeStacktraces();
        $handler->setFormatter($formatter);

        return $handler;
    }
}
