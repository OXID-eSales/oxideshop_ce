<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\MonologLoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Logger\ServiceWrapper\LoggerServiceWrapper;
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
        return new LoggerServiceWrapper(
            $this->getMonologLoggerFactory()->create()
        );
    }

    /**
     * @return MonologLoggerServiceFactory
     */
    private function getMonologLoggerFactory()
    {
        return new MonologLoggerServiceFactory(
            'OXID Logger',
            $this->context->getLogFilePath(),
            $this->context->getLogLevel()
        );
    }
}
