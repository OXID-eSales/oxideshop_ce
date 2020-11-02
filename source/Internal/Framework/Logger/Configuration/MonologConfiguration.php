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
     * @var string
     */
    private $loggerName;

    /**
     * @var string
     */
    private $logFilePath;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * MonologConfiguration constructor.
     *
     * @param string $loggerName
     * @param string $logFilePath
     * @param string $logLevel
     */
    public function __construct($loggerName, $logFilePath, $logLevel)
    {
        $this->loggerName = $loggerName;
        $this->logFilePath = $logFilePath;
        $this->logLevel = $logLevel;
    }

    public function getLoggerName(): string
    {
        return $this->loggerName;
    }

    public function getLogFilePath(): string
    {
        return $this->logFilePath;
    }

    public function getLogLevel(): string
    {
        return $this->logLevel;
    }
}
