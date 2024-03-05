<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator\PsrLoggerConfigurationValidator;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Wrapper\LoggerWrapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Log\LoggerInterface;

class LoggerServiceFactory
{
    public function __construct(private readonly ContextInterface $context)
    {
    }

    public function getLogger(): LoggerInterface
    {
        return new LoggerWrapper(
            (new MonologLoggerFactory(
                new MonologConfiguration(
                    'OXID Logger',
                    $this->context->getLogFilePath(),
                    $this->context->getLogLevel()
                ),
                new PsrLoggerConfigurationValidator()
            ))->create()
        );
    }

}
