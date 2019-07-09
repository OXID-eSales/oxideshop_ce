<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\Configuration;

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
