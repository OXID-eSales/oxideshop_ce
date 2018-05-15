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
use OxidEsales\EshopCommunity\Internal\Logger\Mapper\LogLevelMapperInterface;
use Psr\Log\LoggerInterface;

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
     * @var LogLevelMapperInterface
     */
    private $monologLogLevelMapper;

    /**
     * MonologLoggerServiceFactory constructor.
     *
     * @param MonologConfigurationInterface $configuration
     * @param LogLevelMapperInterface       $monologLogLevelMapper
     */
    public function __construct(
        MonologConfigurationInterface $configuration,
        LogLevelMapperInterface $monologLogLevelMapper
    ) {
        $this->configuration = $configuration;
        $this->monologLogLevelMapper = $monologLogLevelMapper;
    }


    /**
     * @return LoggerInterface
     */
    public function create()
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
        $monologLogLevel = $this
            ->monologLogLevelMapper
            ->getLoggerLogLevel($this->configuration);

        $handler = new StreamHandler(
            $this->configuration->getLogFilePath(),
            $monologLogLevel
        );

        $formatter = $this->getFormatter();
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
}
