<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\Configuration;

use Psr\Log\LogLevel;
use OxidEsales\EshopCommunity\Internal\Utility\Context;

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
     * @var string
     */
    private $defaultLogLevel = LogLevel::ERROR;

    /**
     * MonologConfiguration constructor.
     *
     * @param string  $loggerName
     * @param Context $context
     */
    public function __construct($loggerName, $context)
    {
        $this->loggerName = $loggerName;
        $this->logFilePath = $context->getLogFilePath();
        $this->logLevel = $context->getLogLevel();
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
        return $this->logLevel !== null
            ? $this->logLevel
            : $this->defaultLogLevel;
    }
}
