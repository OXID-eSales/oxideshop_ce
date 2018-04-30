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
     * MonologLoggerFactory constructor.
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
