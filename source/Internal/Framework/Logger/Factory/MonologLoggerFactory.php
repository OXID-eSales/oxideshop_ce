<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator\LoggerConfigurationValidatorInterface;
use Psr\Log\LoggerInterface;

class MonologLoggerFactory implements LoggerFactoryInterface
{
    /**
     * @var MonologConfigurationInterface $configuration
     */
    private $configuration;

    /**
     * MonologLoggerFactory constructor.
     *
     * @param MonologConfigurationInterface         $configuration
     * @param LoggerConfigurationValidatorInterface $configurationValidator
     */
    public function __construct(
        MonologConfigurationInterface $configuration,
        LoggerConfigurationValidatorInterface $configurationValidator
    ) {
        $configurationValidator->validate($configuration);

        $this->configuration = $configuration;
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
        $handler = new StreamHandler(
            $this->configuration->getLogFilePath(),
            $this->configuration->getLogLevel()
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
