<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration;

class MonologConfiguration implements MonologConfigurationInterface
{
    /**
     * @param string $loggerName
     * @param string $logFilePath
     * @param string $logLevel
     */
    public function __construct(
        private $loggerName,
        private $logFilePath,
        private $logLevel
    ) {
    }


    /**
     * @return string
     */
    public function getLoggerName(): string
    {
        return $this->loggerName;
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        return $this->logFilePath;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }
}
