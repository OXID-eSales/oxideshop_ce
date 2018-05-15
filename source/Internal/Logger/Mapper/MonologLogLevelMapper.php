<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Logger\Mapper;

use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Logger\DataObject\PsrLoggerConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\LoggerConfigurationValidatorInterface;
use Psr\Log\LogLevel;

/**
 * @internal
 */
class MonologLogLevelMapper implements LogLevelMapperInterface
{
    /**
     * @var LoggerConfigurationValidatorInterface
     */
    private $configurationValidator;

    /**
     * @var array Map Monolog log levels to \Psr\Log\LogLevel
     */
    private $psrLogLevelMap = [
        LogLevel::DEBUG     => Logger::DEBUG,
        LogLevel::INFO      => Logger::INFO,
        LogLevel::NOTICE    => Logger::NOTICE,
        LogLevel::WARNING   => Logger::WARNING,
        LogLevel::ERROR     => Logger::ERROR,
        LogLevel::CRITICAL  => Logger::CRITICAL,
        LogLevel::ALERT     => Logger::ALERT,
        LogLevel::EMERGENCY => Logger::EMERGENCY,
    ];

    /**
     * MonologLogLevelMapper constructor.
     * @param LoggerConfigurationValidatorInterface $configurationValidator
     */
    public function __construct(LoggerConfigurationValidatorInterface $configurationValidator)
    {
        $this->configurationValidator = $configurationValidator;
    }

    /**
     * @param PsrLoggerConfigurationInterface $configuration
     *
     * @return string
     */
    public function getLoggerLogLevel(PsrLoggerConfigurationInterface $configuration)
    {
        $this->configurationValidator->validate($configuration);

        $psrLogLevel = $configuration->getLogLevel();

        return $this->psrLogLevelMap[$psrLogLevel];
    }
}
