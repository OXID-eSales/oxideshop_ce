<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\PsrLoggerConfigurationInterface;
use Psr\Log\LogLevel;

class PsrLoggerConfigurationValidator implements LoggerConfigurationValidatorInterface
{
    /**
     * @var array
     */
    private $validLogLevels = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY,
    ];

    public function validate(PsrLoggerConfigurationInterface $configuration): void
    {
        $this->validateLogLevel($configuration);
    }

    /**
     * @throws \InvalidArgumentException if log level is not valid
     */
    private function validateLogLevel(PsrLoggerConfigurationInterface $configuration): void
    {
        $logLevel = $configuration->getLogLevel();

        if (!\in_array($logLevel, $this->validLogLevels, true)) {
            throw new \InvalidArgumentException('Log level "' . var_export($logLevel, true) . '" is not a PSR-3 compliant log level');
        }
    }
}
