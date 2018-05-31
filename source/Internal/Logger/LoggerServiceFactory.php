<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger;

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Logger\Wrapper\LoggerWrapper;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\PsrLoggerConfigurationValidator;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
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
            $this->context
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
