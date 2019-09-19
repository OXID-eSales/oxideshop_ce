<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Wrapper\LoggerWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator\PsrLoggerConfigurationValidator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Log\LoggerInterface;

class LoggerServiceFactory
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * LoggerServiceFactory constructor.
     *
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return new LoggerWrapper(
            $this->getMonologLoggerFactory()->create()
        );
    }

    /**
     * @return MonologLoggerFactory
     */
    private function getMonologLoggerFactory()
    {
        return new MonologLoggerFactory(
            $this->getMonologConfiguration(),
            $this->getLoggerConfigurationValidator()
        );
    }

    /**
     * @return MonologConfigurationInterface
     */
    private function getMonologConfiguration()
    {
        return new MonologConfiguration(
            'OXID Logger',
            $this->context->getLogFilePath(),
            $this->context->getLogLevel()
        );
    }

    /**
     * @return PsrLoggerConfigurationValidator
     */
    private function getLoggerConfigurationValidator()
    {
        return new PsrLoggerConfigurationValidator();
    }
}
