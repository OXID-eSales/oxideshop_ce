<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\DataObject;

/**
 * @internal
 */
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


    /**
     * @return string
     */
    public function getLoggerName()
    {
        return $this->loggerName;
    }

    /**
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }
}
