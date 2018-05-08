<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Logger\DataObject\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\LoggerConfigurationValidatorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @internal
 */
class MonologLoggerServiceFactory implements LoggerServiceFactoryInterface
{
    /**
     * @var MonologConfigurationInterface $configuration
     */
    private $configuration;


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
     * MonologLoggerFactory constructor.
     *
     * @param MonologConfigurationInterface         $configuration
     * @param LoggerConfigurationValidatorInterface $configurationValidator
     */
    public function __construct(MonologConfigurationInterface $configuration, LoggerConfigurationValidatorInterface $configurationValidator)
    {
        $configurationValidator->validate($configuration);
        $this->configuration = $configuration;
    }

    /**
     * @return LoggerInterface
     */
    public function create()
    {
        $logger = $this->getLogger();

        return $logger;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        $handler = $this->getHandler();

        $logger = new Logger($this->configuration->getLoggerName());
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @return HandlerInterface
     */
    private function getHandler()
    {
        $formatter = $this->getFormatter();
        $handler = new StreamHandler(
            $this->configuration->getLogFilePath(),
            $this->getMappedLogLevel()
        );
        $handler->setFormatter($formatter);

        return $handler;
    }

    /**
     * @return FormatterInterface
     */
    private function getFormatter()
    {
        $formatter = new LineFormatter();
        $formatter->includeStacktraces(true);

        return $formatter;
    }

    /**
     * @return string
     */
    private function getMappedLogLevel()
    {
        return $this->psrLogLevelMap[$this->configuration->getLogLevel()];
    }
}
