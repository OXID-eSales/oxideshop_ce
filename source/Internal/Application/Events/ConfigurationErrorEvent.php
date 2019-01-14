<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * @internal
 */
class ConfigurationErrorEvent extends Event
{
    const ERROR_LEVEL_DEBUG = 0;
    const ERROR_LEVEL_INFO = 1;
    const ERROR_LEVEL_WARN = 2;
    const ERROR_LEVEL_ERROR = 3;

    /**
     * @var int $errorLevel
     */
    private $errorLevel;

    /**
     * @var string $errorMessage
     */
    private $errorMessage;

    /**
     * @var string $configurationFilePath
     */
    private $configurationFilePath;

    /**
     * @param int    $errorLevel
     * @param string $errorMessage
     * @param string $configurationFilePath
     */
    public function __construct(int $errorLevel, string $errorMessage, string $configurationFilePath)
    {
        $this->errorLevel = $errorLevel;
        $this->errorMessage = $errorMessage;
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * Returns the file that is misconfigured
     *
     * @return string
     */
    public function getConfigurationFilePath()
    {
        return $this->configurationFilePath;
    }

    /**
     * @return int
     */
    public function getErrorLevel(): int
    {
        return $this->errorLevel;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
