<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\LineFormatter;

/**
 * @internal
 */
class MonologLoggerServiceFactory implements LoggerServiceFactoryInterface
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
     * @var array Valid Monolog log levels
     */
    private $validLogLevels = [
        \Psr\Log\LogLevel::DEBUG,
        \Psr\Log\LogLevel::INFO,
        \Psr\Log\LogLevel::NOTICE,
        \Psr\Log\LogLevel::WARNING,
        \Psr\Log\LogLevel::ERROR,
        \Psr\Log\LogLevel::CRITICAL,
        \Psr\Log\LogLevel::ALERT,
        \Psr\Log\LogLevel::EMERGENCY,
    ];

    /**
     * @var array Map Monolog log levels to \Psr\Log\LogLevel
     */
    private $psrLogLevelMap = [
        \Psr\Log\LogLevel::DEBUG     => \Monolog\Logger::DEBUG,
        \Psr\Log\LogLevel::INFO      => \Monolog\Logger::INFO,
        \Psr\Log\LogLevel::NOTICE    => \Monolog\Logger::NOTICE,
        \Psr\Log\LogLevel::WARNING   => \Monolog\Logger::WARNING,
        \Psr\Log\LogLevel::ERROR     => \Monolog\Logger::ERROR,
        \Psr\Log\LogLevel::CRITICAL  => \Monolog\Logger::CRITICAL,
        \Psr\Log\LogLevel::ALERT     => \Monolog\Logger::ALERT,
        \Psr\Log\LogLevel::EMERGENCY => \Monolog\Logger::EMERGENCY,
    ];

    /**
     * MonologLoggerFactory constructor.
     *
     * @param string $loggerName  Name of the logger as shown in the log file
     * @param string $logFilePath Path to the log file
     * @param string $logLevel    A log level as defined in \Psr\Log\LogLevel
     */
    public function __construct($loggerName, $logFilePath, $logLevel)
    {
        if (!in_array($logLevel, $this->validLogLevels)) {
            throw new \InvalidArgumentException('Log level ' . var_export($logLevel, true) . ' is not permitted');
        }
        
        $this->loggerName = $loggerName;
        $this->logFilePath = $logFilePath;
        $this->logLevel = $this->psrLogLevelMap[$logLevel];
    }

    /**
     * @return LoggerInterface
     */
    public function create()
    {
        $lineFormatter = new LineFormatter();
        $lineFormatter->includeStacktraces(true);

        $streamHandler = new StreamHandler(
            $this->logFilePath,
            $this->logLevel
        );
        $streamHandler->setFormatter($lineFormatter);

        $logger = new Logger($this->loggerName);
        $logger->pushHandler($streamHandler);

        return $logger;
    }
}
